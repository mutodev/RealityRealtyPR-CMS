YAMMON.Widgets.SelectCheckboxTags = new Class({
    Extends: YAMMON.Widget ,
    options: {
        values: []
    },
    initialize: function( node , options ){    
        this.parent( node , options );
        this.element = $(options.element);
        this.element.addEvent( 'click' , this.sync.bindWithEvent( this ) );
        this.sync();
    },
    sync: function(){
                
        var checkboxes = this.element.getElements('input[type=checkbox]');
        this.node.set('html' , '' );    
        var count = 0;
        for( var i in checkboxes ){

            if( !checkboxes.hasOwnProperty(i) ) continue;
            if( !checkboxes[i].checked ) continue;

            var label = this.element.getElement('label[for='+checkboxes[i].id+']');
            if( !label ) continue;
                        

            var tag = new Element('a' , {
                'href': 'javascript:void(0)' ,
                'html': label.get('html') ,
            }).inject( this.node );

            var close = new Element('span' , {
                'class': 'close'
            }).inject( tag , 'top');                        
                        
        
            var self = this;
            tag.store( 'checkbox' , checkboxes[i] );    
            tag.addEvent( 'click' , function(){
            
                var tag      = $(this);
                var checkbox = tag.retrieve('checkbox');
                var ms      = self.element.retrieve('widget.selectcheckbox').MultiSelect;
                var item    = checkbox.getParent('li');
                var monitor = self.element.getElement('.monitor');

                checkbox.checked = false;
                item.addClass('selected');
                ms.changeItemState(item, checkbox, monitor);
                self.sync();
                
            });
            
            count++;
            
        }
                
        if( count == 0 )
            this.node.set('html' , "<span class='tag-box-empty'>" + this.options.empty + "</span>" );
            
        this.node.fireEvent( 'selectcheckboxtags:sync' );
        window.fireEvent( 'selectcheckboxtags:sync' ,  this.node );
        
    }
    
    
});




