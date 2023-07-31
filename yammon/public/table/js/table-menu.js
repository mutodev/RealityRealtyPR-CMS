var ContextMenu = new Class({

	//implements
	Implements: [ Options, Events ],

	//options
	options: {
	    targets:   [] ,
	    items:     [] ,
		fadeSpeed: 200 ,
		imgPath:   '/yammon/public/table/img/'
	},

	//initialization
	initialize: function( options) {

	     //Set Options
		 this.setOptions(options)

		 //Initialize
		 this.opened_sub_menus = [];

	     //Attach Events
         $( document ).addEvent( 'click' , this._onWindowClick.bindWithEvent( this ) );

	     //Contruct Dom
         this.menu = this.build( this.options.items );
         this.menu.setStyle('opacity' , 0 );
	     document.body.appendChild( this.menu );

	 	 this.fx = new Fx.Tween( this.menu, {
		    property: 'opacity',
		    duration: this.options.fadeSpeed
         });

	     return this;

	},

    setItems: function( items ){

        //Set new Items
        this.options.items = items;

        //Construct Dom
        this.menu.dispose();
        this.menu = this.build( this.options.items );
        this.menu.setStyle('opacity' , 0 );
	    document.body.appendChild( this.menu );

	 	this.fx = new Fx.Tween( this.menu, {
		    property: 'opacity',
		    duration: this.options.fadeSpeed
        });

    },

    //build
    build: function( items ){

        var self = this;

        var container = new Element("div" ,{
            'class': 'context-menu'
        });

        $A( items ).each( function( item ){

            var icon = item.icon;

            if( item.hasOwnProperty('checkbox') ){
                if( item.checkbox )
                    icon = "checked.png";
                else
                    icon = "unchecked.png";
            }

            icon = self.options.imgPath + icon;

            var item_container = new Element( "a" , {
                'class': 'context-menu-item'
            }).inject( container );

            var icon = new Element( "img" , {
                'class': 'context-menu-icon' ,
                'src'  : icon
            }).inject( item_container );

            var label = new Element( "span" , {
                'class': 'context-menu-label'
            }).set( 'html' , item.label )
            .inject( item_container );

            //Create an sub menu
            var sub_menu = false;
            if( item.items && item.items.length ){

                //Create Sub Menu
                sub_menu = self.build( item.items );
                sub_menu.addClass( 'context-submenu' );
                sub_menu.hide();
                sub_menu.inject( item_container );

                //Add Expendable Class
                item_container.addClass( 'context-menu-item-expandable' );

            }

            //Execute on Click
			item_container.addEvent( 'click'     , self._onItemClick.bindWithEvent(     self , [ item , item_container , icon , sub_menu] ) );
			item_container.addEvent( 'mouseover' , self._onItemMouseOver.bindWithEvent( self , [ item , item_container , icon , sub_menu] ) );
			item_container.addEvent( 'mouseout'  , self._onItemMouseOut.bindWithEvent(  self , [ item , item_container , icon , sub_menu] ) );

        });

        return container;

    },

	//show menu
	show: function( e ) {

	    this.target = e.target;
	    this.menu.setStyles({
	        'top':     e.page.y  + 'px' ,
	        'left':    e.page.x + 'px' ,
            'opacity':  0
	    });

		this.fx.start(1);
		this.fireEvent('show' , this.target );
		this.shown = true;
		return this;
	},

	//hide the menu
	hide: function() {
		if( this.shown ){

		   this.fx.start(0);
		   this.fireEvent('hide' , this.target );
		   this.shown  = false;
		   this.target = false;
		}
		return this;
	},

    disableItem: function( item ){
      this.menu.getElements('a[href$=' + item + ']').removeClass('disabled');
    },

    enableItem: function( item ){
      this.menu.getElements('a[href$=' + item + ']').removeClass('disabled');
    },

    _onWindowClick: function( e ){
       this.hide();
    },

    _onItemClick: function( e , opt , item , icon , sub_menu ){

        var target = this.target;

        if( opt.hasOwnProperty('checkbox') ){
            opt.checkbox = !opt.checkbox;
            icon.src = this.options.imgPath + ( opt.checkbox ? "checked.png" : "unchecked.png" );
        }else{
            this.hide();
        }

        this.fireEvent( 'select' , [ target , opt] );

        e.stop();
    },

    _onItemMouseOver: function( e , opt , item , icon , sub_menu ){

        //Hide all sub menus
        var collapse = false;
        for( i = this.opened_sub_menus.length -1 ; i >=  0 ; i-- ){
            var pi = this.opened_sub_menus[ i ].item;
            var sm = this.opened_sub_menus[ i ].menu;

            if( sm.hasChild( item ) )
                break;

            pi.removeClass('context-menu-item-active');
            sm.hide();
        }

        //Make this item active
        item.addClass('context-menu-item-active');


        //Show our submenu
        if( sub_menu ){
            sub_menu.show();
            this.opened_sub_menus.push( {
                item: item ,
                menu: sub_menu
            });
        }

        e.stop();

    },

    _onItemMouseOut: function( e , opt , item , icon , sub_menu ){

        if( !sub_menu ){
            item.removeClass('context-menu-item-active');
        }

    }

});
