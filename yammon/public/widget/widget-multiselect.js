YAMMON.Widgets.MultiSelect = new Class({
    Extends: YAMMON.Widget ,
    options: {
        values: []
    },
    initialize: function( node , options ){
        this.parent( node , options );
                              
        this.MultiSelect = new MultiSelect( '.yammon-form-checkboxes-select' );
    }
    
});

