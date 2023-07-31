YAMMON.Widgets.Autocomplete = new Class({
    Extends: YAMMON.Widget ,
    options: {
        values: []
    },
    autocomplete: null,
    refresh: function() {
        this.autocomplete.refreshCache();
    },
    initialize: function( node , options ){
        this.parent( node , options );

        var id = node.get('id')
        var hidden = $(id + "_id");

        if( options.hasOwnProperty( 'values' ) ){

            //Get the values
            var data   = [];
            var values = options.values;
            for( var i in options.values )
                if( values.hasOwnProperty( i ) )
                    data[ data.length ] = {'id' : i , 'value': values[i] };


            //Create Local Autocomplete
            this.autocomplete = new Meio.Autocomplete.Select( node , data, {
                valueField: hidden ,
                filter: {
                    type: 'contains',
                    path: 'value'
                }
            });

        }else if( hidden ){

            //Create Remote Autocomplete
            this.autocomplete = new Meio.Autocomplete.Select( node , window.location.href , {


                valueField: hidden ,
                filter: {
                    type: 'contains',
                    path: 'value'
                },
                requestOptions: {
                    method: 'POST' ,
                    headers: {
                        'X-YAMMON-REQUEST'    : 'HELPER_FORM_ELEMENT_AUTOCOMPLETE' ,
                        'X-YAMMON-REQUEST-ID' :  id
                    },
                    data: node.getParent('form')
                },
                urlOptions: {
                    'queryVarName': '__autocomplete'
                },
            });


        }else{


            //Create Remote Autocomplete
            this.autocomplete = new Meio.Autocomplete( node , window.location.href , {

                filter: {
                    type: 'contains',
                    path: 'value'
                },
                requestOptions: {
                    method: 'POST' ,
                    headers: {
                        'X-YAMMON-REQUEST'    : 'HELPER_FORM_ELEMENT_AUTOCOMPLETE' ,
                        'X-YAMMON-REQUEST-ID' :  id
                    },
                    data: node.getParent('form')
                },
                urlOptions: {
                    'queryVarName': '__autocomplete'
                }
            });

        }

    }

});
