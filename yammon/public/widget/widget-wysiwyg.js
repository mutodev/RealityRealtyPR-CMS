YAMMON.Widgets.Wysiwyg = new Class({
    Extends: YAMMON.Widget ,
    options: {
        values: []
    },
    initialize: function( node , options ){
        this.parent( node , options );

        this.Wysiwyg = tinyMCE.init( options );
    }
});