YAMMON.Widgets.TextList = new Class({
    Extends: YAMMON.Widget ,
    options: {
        values: []
    },
    initialize: function( node , options ){
        this.parent( node , options );
                                  
        var tl = new TextboxList( node , options );
        var values = options.values;
        if( values && values.constructor.toString().indexOf("Array") != -1 && values.length ){
            tl.plugins['autocomplete'].setValues( values );
        }      
        
        if( options.defaults ){        
            for( var i in options.defaults ){
                if( !options.defaults.hasOwnProperty( i ) ) continue;
                var key   = i;
                var value = options.defaults[i];
                tl.add( value , key , value );
            }
        }            

    }
    
});

