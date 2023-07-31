YAMMON.Widgets.DropDownLinks = new Class({

    options: {
        timeout: 250 
    },

    initialize: function( el , options ){

        el         = $( el );
        this.links = el.getElements('a');
        var c      = this.links.length;

        if ( !c )
            return;

        this.div     = new Element('div');
        this.div.addClass('drop-down-links');

        this.main    = this.links[0].clone( true );
        this.main.addClass('drop-down-links-main');
        this.main.inject( this.div );

        this.list    = new Element('ul');
        this.list.addClass('drop-down-links-list');
        
        for( var i = 0 ; i < c ; i++ ){
            var li = new Element('li');
            li.inject( this.list );
            this.links[i].inject( li );
        }
        
        this.div.inject( this.list , 'after' );
        this.list.inject( this.div );
        
        this.div.inject( el , 'after' );
        el.destroy();
        
        this.timeout = false;

        if ( c < 2 )
            return;

        this.main.addEvent( 'mouseover' , this.onMouseOver.bind( this ) );
        this.main.addEvent( 'mouseout'  , this.onMouseOut.bind( this ));
        this.list.addEvent( 'mouseover' , this.onMouseOver.bind( this ) );
        this.list.addEvent( 'mouseout'  , this.onMouseOut.bind( this ));
    },

    onShow: function( list ){
        this.list.setStyle('display' , 'block' );    
    },
    
    onHide: function( list ){
        this.list.setStyle('display' , 'none' );        
    },

    onMouseOver: function( e ){
        clearTimeout( this.timeout );
        this.timeout = setTimeout( this.onShow.bind( this ) , this.options.timeout );
    },
    
    onMouseOut: function( e ){    
        clearTimeout( this.timeout );
        this.timeout = setTimeout( this.onHide.bind( this ) , this.options.timeout );
    }    

});
