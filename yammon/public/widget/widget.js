var YAMMON;
if( YAMMON == undefined )
    YAMMON = {};

if( YAMMON.Widgets == undefined )
    YAMMON.Widgets = {};

YAMMON.Widget = new Class({
    Implements: Options,
    node:       null,
    options: {

    },
    initialize: function( node , options ){
        this.node = node;
        this.setOptions( options );
    },
    getNode: function(){
        return this.node;
    }

});

YAMMON.WidgetManager = {

    initialized: false ,

    initialize: function(){

        //Initialize
        if( YAMMON.WidgetManager.initialized == true )
            return;

        //Reload Widgets
        if( Browser.loaded )
            YAMMON.WidgetManager.reload();
        else
            window.addEvent("domready" , YAMMON.WidgetManager.reload );

    },

    find: function( widget , parent ){

        var ret = [];
        var widget = widget.toLowerCase();

        //Get the parent
        if( !parent )
            parent = document.body;

        //Find all widgets
        var nodes = $(parent).getElements("*[widget]");

        //Loop thru the nodes
        var len = nodes.length;
        var cls = false;
        for( var i = 0 ; i < len ; i++ ){
            if( cls = nodes[i].retrieve('widget.' + widget ) ){
                ret.push( cls );
            }
        }

        return ret;

    },

    create: function( widget , node ){

        //Make sure its a widget class
        if( !YAMMON.Widgets[ widget ] ){
            return false;
        }

        //Check if the widget has already been created
        var widget_name = widget.toLowerCase();
        var widget_str  = "widget-" + widget_name + "-created";

        //Create the object
        if( !node.retrieve( widget_str ) ){

            //Get the options
            var options = YAMMON.WidgetManager.options( widget , node );
            var cls     = new YAMMON.Widgets[ widget ]( node , options );

            //Store that we created the class
            $(node).store( widget_str , true );
            $(node).store( 'widget.' + widget_name , cls );

            return cls;
        }
        //Existing Widget
        else {
            var existingWidget = node.retrieve( 'widget.' + widget_name );

            //Refresh
            if(typeof existingWidget.refresh == 'function')
                existingWidget.refresh();

            return existingWidget;
        }

        return false;

    },

    options: function( widget , node ){

        var options      = {};
        var widget_name  = widget.toLowerCase();
        var options_name = "widget-" + widget_name;

        //Get the options attribute
        var options_att = node.getAttribute( options_name );

        if( options_att )
            options = JSON.decode( options_att );

        //Get specific options
        var len = node.attributes.length;
        var prefix      = options_name + "-";
        var prefix_len  = prefix.length;

        for( var i = 0 ; i < len ; i++ ){

            var att             = node.attributes[i];
            var att_name        = att.name.toLowerCase();
            var att_prefix      = att_name.substr( 0 , prefix_len );
            var att_suffix      = att_name.substr( prefix_len );
            var att_value       = att.value;

            if( att_prefix == prefix && att_value ){

                if( /^\s*(\{|\[)/.test( att_value ) )
                    att_value = JSON.decode( att_value );

                options[ att_suffix ] = att_value;

            }

        }

        return options;

    },

    reload: function( parent ){

        if( !parent )
            parent = document.body;

        //Find all widgets
        var nodes = $(parent).getElements("*[widget]");

        //Loop thru the nodes
        var len = nodes.length;
        for( var i = 0 ; i < len ; i++ ){

            var node     = nodes[i];
            var widgets  = node.get('widget');

            if( !widgets ){
                continue;
            }

            widgets  = widgets.split( "," );
            var wlen = widgets.length;
            for( var j = 0 ; j < wlen ; j++ ){
                YAMMON.WidgetManager.create( widgets[j] , node );
            }

        }

    }
};

//Initialize Widget Manager
YAMMON.WidgetManager.initialize();
