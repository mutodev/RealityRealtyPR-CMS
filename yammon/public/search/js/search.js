function do_simple_search( e ){
    advanced_search_disable("advanced_search" , true );
}

function do_advanced_search( e ){
    advanced_search_disable("simple_search"   , true );
}

function show_simple_search( e ){

    compile_advanced_search();
    advanced_search_disable("simple_search"   , false );
    advanced_search_disable("advanced_search" , true );

    if( Fx && Fx.Reveal ){
        new Fx.Reveal('simple_search').reveal();
        new Fx.Reveal('advanced_search').dissolve();
    }else{
        $('simple_search').show();        
        $('advanced_search').hide();
    }
    
}

function show_advanced_search( e ){
    
    compile_simple_search();
    advanced_search_disable("simple_search"   , true );
    advanced_search_disable("advanced_search" , false );
            
    if( Fx && Fx.Reveal ){
        new Fx.Reveal('simple_search').dissolve();
        new Fx.Reveal('advanced_search').reveal();
    }else{
        $('simple_search').hide();        
        $('advanced_search').show();
    }    
}

function advanced_search_disable( parent , b ){
    $(parent).getElements("select").each( function( el ){
        el.disabled = b;
    });
    $(parent).getElements("input").each( function( el ){
        el.disabled = b;
    });
    $(parent).getElements("button").each( function( el ){
        el.disabled = b;
    });
}

function advanced_search_field_change( e ){

    var field     = e.target ? $( e.target ) : e;
    var operators = $( field.options[ field.selectedIndex ] ).get('operators').split(',');
    var operator  = field.getParent('tr').getElement('.advanced_search_operation');
    var value     = operator.get('value');
                    
    operator.empty();
    
    var i , c = operators.length;
    for( i = 0 ; i < c ; i++ ){
        var values  = operators[ i ].split(";");
        var option  = new Element( 'option' , { value:  i == 0 ? "" : values[0] } ).set('html' , values[1] ).inject( operator );
    }
    
    operator.set('value' , value );
    
}

function advanced_search_more( e ){

    e = new Event( e );
    e.stop();
    
    var target = $(e.target);
    var tr     = target.getParent("tr");
    var clone  = $(tr.clone( true ));
    clone.inject( tr , 'after' );
    clone.getElement(".advanced_search_value").set('value' , '' );
    
}

function advanced_search_less( e ){
    
    e = new Event( e );
    e.stop();

    var target = $(e.target);
    var tr     = target.getParent("tr");
    var table  = tr.getParent("table");
    var count  = table.rows.length;
    
    if( count >= 2 )
        tr.dispose();

}

function compile_simple_search(  ){

    var search  = $('search_query').get('value');
    var repeats = $$('#advanced_search tr');
    var len     = repeats.length;
    var query   = [];

    if( search.clean() == "" )
        return;

    $('advanced_search_bool_and').checked = true;
 
    //Clear the values of everything   
    for( i = 0 ; i < len ; i++ ){
        var field  = repeats[ i ].getElement(".advanced_search_field").selectedIndex = i;
        var value  = repeats[ i ].getElement(".advanced_search_value").set('value' , '');
    }

    //Set the default value
    var repeat   = repeats[0];
    var field    = repeat.getElement(".advanced_search_field").set('value' , '' );        
    advanced_search_field_change( field );

    var operator = repeat.getElement(".advanced_search_operation").set('value' , '' );
    var value    = repeat.getElement(".advanced_search_value").set('value' , search );
    
}

function compile_advanced_search(  ){

    var search  = $('search_query');
    var repeats = $$('#advanced_search tr');
    var bool    = $('advanced_search_bool_and').checked ? "AND" : "OR";
    var len     = repeats.length;
    var query   = [];

    for( i = 0 ; i < len ; i++ ){

        var repeat   = $(repeats[ i ]);
        var field    = repeat.getElement(".advanced_search_field").get('value').clean();
        var operator = repeat.getElement(".advanced_search_operation").get('value').clean();
        var value    = repeat.getElement(".advanced_search_value").get('value').clean();
        var subquery = "";

        if( value == '' )
            continue;

        if( field ) 
            subquery += field + ":" + " ";
        
        if( operator )
            subquery += operator + " ";

        if( value.contains(' ') && field != "" )
	        subquery += "\"" + value + "\"";
        else
            subquery += value;

        query[ query.length ] = subquery;

    }

    query = query.join( " " + bool + " ");
    search.set( 'value' , query );
}

