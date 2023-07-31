YAMMON.Widgets.Repeat = new Class({
    Extends: YAMMON.Widget ,
    options: {
        min:      1,
        max:      null,
        start:    null,
        sortable: false
    },
    initialize: function( node , options ){


        this.parent( node , options );

        //Reindex on submit
        var form = node.getParent("form");
        if( form )
            form.addEvent( "submit" , this.reindex.bind( this ) );

        //Reindex
        this.reindex( true );

        //Add Handlers
        node.addEvent( 'click' , this._onClick.bindWithEvent( this ) );

        //Activate mootools Sortables
        if ( this.options.sortable) {
            var self = this;
            this.sortables = new Sortables( this.getNode() ,{
              'clone': true ,
              'onComplete': function( element ){
                self.reindex();
              }
            });
        }

        //Add remove buttons
        var buttons = $$('.ym-form-repeat-more');
        var c       = buttons.length;
        for( var i = 0 ; i < c ; i++ ){
            if( this.node.contains( buttons[i] ) )
                continue;

            if( $(buttons[i].get('repeat-element')) != this.node )
                continue;

            buttons[i].addEvent( 'click' , this.onInsert.bindWithEvent( this ) );
        }

    },
    _onClick: function( e ){

        var target = $(e.target );

        if( target.hasClass( 'ym-form-repeat-more' ) || target.getParent('.ym-form-repeat-more') || target.hasClass( 'ym-form-repeat-more-after' ) || target.getParent('.ym-form-repeat-more-after') ){
            this.onAdd( e, 'after' );
        }

        if( target.hasClass( 'ym-form-repeat-more-before' ) || target.getParent('.ym-form-repeat-more-before') ){
            this.onAdd( e, 'before' );
        }

        if( target.hasClass( 'ym-form-repeat-less' ) || target.getParent('.ym-form-repeat-less') ){
            this.onRemove( e );
        }

        if( target.hasClass( 'ym-form-repeat-up' ) || target.getParent('.ym-form-repeat-up') ){
            this.onMoveUp( e );
        }

        if( target.hasClass( 'ym-form-repeat-down' ) || target.getParent('.ym-form-repeat-down') ){
            this.onMoveDown( e );
        }

    },
    getTemplate: function(){
        return this.getNode().getFirst(".ym-form-repeat-template");
    },
    getItems: function(){
        return this.getNode().getChildren(".ym-form-repeat-item");
    },
    getNewEntry: function(){

        //Get Template
        var template = this.getTemplate();

        //Clone
        var clone    = template.clone( true , true );
        clone.removeClass( 'ym-form-repeat-template' );
        clone.addClass( 'ym-form-repeat-item' );
        clone.removeProperty( 'widgetoff' );

        //Return
        return clone;

    },
    getItemsCount: function(){
        return this.getItems().length;
    },

    reindex: function( partial ){

        //Get entries
        var entries  = this.getItems();
        var id       = this.node.get('id');
        var odd      = false;

        for (var i = 0; i < entries.length; i++){

            var entry = entries[i];

            //Set Classes
            if( odd ){
                entry.addClass('ym-form-repeat-item-odd');
                entry.removeClass('ym-form-repeat-item-even');
            }else{
                entry.removeClass('ym-form-repeat-item-odd');
                entry.addClass('ym-form-repeat-item-even');
            }

            //Set Labels
            var labels = entry.getElements('.ym-form-repeat-index');
            labels.set('html' , i + 1 );
        }

        //Reindex HTML
        if( !partial ) {
            this.reindexHTML();

            YAMMON.WidgetManager.reload();
        }

        this.node.fireEvent('repeat:reindex');
        window.fireEvent('repeat:reindex' , this.node );
    },

    reindexHTML: function(){

        var tempHtml     = '';
        var values       = {};
        var id           = this.node.get('id');
        var entries      = this.getItems();
        var entriesTotal = entries.length;

        //Get form fields name prefix
        var input = /name=["|'][^\s]+__template__\]/gi.exec(this.getTemplate().innerHTML)[0];
        input     = input.substring(6, input.indexOf('[__template__]'));

        //Name
        var name = input.replace( /\[/g , '.').replace( /\]/g , '');

        //Regex
        var idRegex = new RegExp(id + '___([^_]+)__', "gi");
        var inputRegex = new RegExp(input.replace( /\[/g , '\\[').replace( /\]/g , '\\]') + '\\[__[^_]+__\\]', "gi");
        var nameRegex = new RegExp(name.replace( '.' , '\\.') + '\\.__[^_]+__', "gi");

        //Check differences and mode
        var diff = [];
        var mode = 'MOVE';
        for (var i = 0; i < entriesTotal; i++){

            var dataIndex = entries[i].get('data-index');


            if (i != dataIndex) {

                //ADD
                if (dataIndex === null) {
                    mode = 'ADD';
                }
                //REMOVE
                else if (entries[dataIndex] === undefined) {
                    mode = 'REMOVE';
                }

                diff.push(i);
            }
        }

        //Change order for adding entry
        if (mode == 'ADD') {
            diff.reverse();
        }

        //Temp entry elements
        else if (mode == 'MOVE') {
            for (var o = 0, len = diff.length; o < len; o++) {

                var i = diff[o];

                //Get Values
                values[i] = entries[i].toQueryString().parseQueryString(); //Get previous values

                //Change innerHTML
                tempHtml = entries[i].innerHTML;
                tempHtml = tempHtml.replace( idRegex   , id    + '___temp'+i+'__'  );
                tempHtml = tempHtml.replace( inputRegex, input + '[__temp'+i+'__]' );
                tempHtml = tempHtml.replace( nameRegex , name  + '.__temp'+i+'__'  );
                entries[i].innerHTML = tempHtml;
            }
        }

        //Execute differences changes
        for (var o = 0, len = diff.length; o < len; o++) {

            var i = diff[o];

            //Get Values
            values[i] = values[i] || entries[i].toQueryString().parseQueryString(); //Get previous values

            //Change innerHTML
            tempHtml = entries[i].innerHTML;
            tempHtml = tempHtml.replace( idRegex   , id    + '___'+i+'__'  );
            tempHtml = tempHtml.replace( inputRegex, input + '[__'+i+'__]' );
            tempHtml = tempHtml.replace( nameRegex , name  + '.__'+i+'__'  );
            entries[i].innerHTML = tempHtml;

            //Set Index
            entries[i].set('data-index', i);

            //Add the values again
            for( var key in values[i] ){
                if( !values[i].hasOwnProperty( key ) )
                    continue;

                var nkey = key.replace( inputRegex, input+'[__'+i+'__]' );

                nkey = nkey.replace( /\[/g , '\\[');
                nkey = nkey.replace( /\]/g , '\\]');
                var el = entries[i].getElement('*[name=' + nkey + ']');

                if( el ) {

                    if ( el.get('type') == 'radio' ) {
                        el = entries[i].getElement('*[name=' + nkey + '][value=' + values[i][key] + ']');
                        el.set('checked', true);
                    }
                    else if ( el.get('type') == 'hidden' ) {

                        //Fix Checkbox
                        var checkboxEl = entries[i].getElement('input[type=checkbox][name=' + nkey + ']');

                        if (checkboxEl) {

                            var checkboxValue = values[i][key][1] == undefined ? values[i][key][0] : values[i][key][1];

                            checkboxEl.set('checked', values[i][key][0] != checkboxValue);
                            values[i][key] = values[i][key][0];
                        }

                        el.value = values[i][key];
                    }
                    else {
                        el.value = values[i][key];
                    }
                }

            }
        }
    },

    addEntry: function( entry, location ) {

        //Make sure we are withing limits
        var count = this.getItemsCount();
        if( this.options.max && count > this.options.max){
            return false;
        }

        //Create new section
        var newentry = this.getNewEntry();

        this.node.fireEvent('repeat:add:pre', [newentry]);
        window.fireEvent('repeat:add:pre' , [newentry]);

        newentry.inject( entry , location || 'after' );

        //Reveal
        if( Fx && Fx.Reveal ){
            new Fx.Reveal( newentry ).reveal();
        }else{
            newentry.show();
        }

        if (this.sortables)
            this.sortables.addItems( newentry );

        //Reindex
        this.reindex();

        this.node.fireEvent('repeat:add:post', [newentry]);
        window.fireEvent('repeat:add:post' , [newentry]);

        return true;
    },

    onInsert: function( e ){

        //Stop Event
        e.stop();

        //Get the players
        var entry = this.getNode().getLast('.ym-form-repeat-item');
        if( this.addEntry( entry ) ){
            this.node.fireEvent('repeat:changed');
            window.fireEvent('repeat:changed' , this.node );
        }

    },

    onAdd: function( e, location ){

        //Stop Event
        e.stop();

        //Get the players
        var button    = $( e.target );
        var entry     = button.getParent(".ym-form-repeat-item");
        if( this.addEntry( entry, location ) ){
            this.node.fireEvent('repeat:changed');
            window.fireEvent('repeat:changed' , this.node );
        }
    },
    onRemove: function( e ){

        //Stop Event
        e.stop();

        //Get the players
        var button    = $( e.target );
        var entry     = button.getParent(".ym-form-repeat-item");

        if( !confirm('Are you sure you want to remove this item?') ){
            return;
        }

        this.node.fireEvent('repeat:remove:pre', [entry]);
        window.fireEvent('repeat:remove:pre' , [entry]);

        //Make sure we are withing limits
        var count = this.getItemsCount();
        if( this.options.min && count <= this.options.min ){
            if( this.addEntry( entry ) ){
                this.node.fireEvent('repeat:changed');
                window.fireEvent('repeat:changed' , this.node );
            }
        }

        //Remove Element
        if( Fx && Fx.Reveal ){
            var self = this;
            new Fx.Reveal( entry , {
                onComplete: function(){
                    entry.dispose();
                    self.reindex();
                    self.node.fireEvent('repeat:remove:post', [entry]);
                    window.fireEvent('repeat:remove:post' , [entry]);
                    self.node.fireEvent('repeat:changed');
                    window.fireEvent('repeat:changed' , this.node );
                }
            }).dissolve();
        }else{
            entry.dispose();
            this.reindex();
            this.node.fireEvent('repeat:remove:post', [entry]);
            window.fireEvent('repeat:remove:post' , [entry]);
            this.node.fireEvent('repeat:changed');
            window.fireEvent('repeat:changed' , this.node );
        }

    },
    onMoveUp: function( e ){

        //Stop the event
        e.stop();

        //Get the players
        var button  = $( e.target );
        var entry   = button.getParent(".ym-form-repeat-item");
        var swap    = entry.getPrevious(".ym-form-repeat-item");

        if( swap )
            entry.inject( swap , 'before' );

        //Reindex
        this.reindex();
        this.node.fireEvent('repeat:changed');
        window.fireEvent('repeat:changed' , this.node );

    },
    onMoveDown: function( e ){

        //Stop the event
        e.stop();

        //Get the players
        var button  = $( e.target );
        var entry   = button.getParent(".ym-form-repeat-item");
        var swap    = entry.getNext(".ym-form-repeat-item");

        if( swap )
            entry.inject( swap , 'after' );

        //Reindex
        this.reindex();
        this.node.fireEvent('repeat:changed');
        window.fireEvent('repeat:changed' , this.node );

    }

});

