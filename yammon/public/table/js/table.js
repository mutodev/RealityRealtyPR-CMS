window.addEvent( "domready" , function(){
    onTableInit();
});

function onTableInit(){

    var tables      = $$(".yammon-table-container");
    var c           = tables.length;



    for( var i = 0 ; i < c ; i++ ){

        if( tables[i].retrieve( 'table-loaded' ) )
            continue;

        tables[i].store('table-loaded' , true );
        onTableInitDropDown( tables[i]  );
        onTableInitExpanders( tables[i] );
        onTableInitTree( tables[i] );
    }

}

function onTableInitDropDown( container ){

    var table       = container.getElement('table');
    var headers     = container.getElements('thead th');
    var isGroupable = container.hasClass('yammon-table-groupable');
    var isHideable  = container.hasClass('yammon-table-hideable');
    var isSortable  = container.hasClass('yammon-table-sortable');
    var isGrouped   = container.hasClass('yammon-table-grouped');

    //Get the Columns on the table
    var columns = [];
    $each( headers , function( el ){
        var columnName      = el.get('column');
        var columnLabel     = el.get('text');
        var columnHideable  = el.hasClass('yammon-table-header-hideable');

        if( columnHideable ){
            columns.push( { name: columnName , label: columnLabel , checkbox: 1 } );
        }

    });

    //Create Context Menu
    var menuData = [];

    if( isSortable ){
        menuData.push( { name: 'asc'        , label: 'Sort Ascending'      , icon: 'sort-asc.png'  } );
        menuData.push( { name: 'desc'       , label: 'Sort Descending'     , icon: 'sort-desc.png' } );
    }

    if( isHideable ){
        menuData.push( { name: 'columns'    , label: 'Columns'             , icon: 'columns.png' ,  items: columns } );
    }

    if( isGroupable ){
        menuData.push( { name: 'group'      , label: 'Group by this field' , icon: 'group.png'     } );
        menuData.push( { name: 'showgroups' , label: 'Show in groups'      , checkbox: isGrouped   } );
    }

    var menu = new ContextMenu({
        items:    menuData ,
        onSelect: onTableContextMenuSelect
    });

    container.getElements('.yammon-table-dropdown').addEvent( 'click' , onDropDownClick.bindWithEvent( window , [menu] ) );

}

function onDropDownClick( e , menu ){

    if( $( e.target ).hasClass( 'yammon-table-dropdown' ) ){
        e.stop();
        menu.show( e );
    }
}

function TableURL( table , data ){

    var uri      = new URI( window.location.href );
    var key      = table.id.toLowerCase();

    var key_value  = (uri.getData( key ) || '').split("|");
    var values     = {};
    for( var i = 0 ; i < key_value.length ; i = i+2 ){
        if( i + 1 >= key_value.length ) continue;
        values[ key_value[i] ] = key_value[i+1];
    }

    for( var i in data ){
        if( !data.hasOwnProperty( i ) ) continue;
        values[ i ] = data[i];
    }

    var parameter = [];
    for( var i in values ){
        if( !values.hasOwnProperty( i ) ) continue;
        parameter.push( i );
        parameter.push( values[i] );
    }

    var obj = {};
    obj[ key ] = parameter.join("|");
    uri.setData( obj , true );

    return uri.toString();


}

function onTableContextMenuSelect( target , item ){

    var table  = $( target ).getParent('div.yammon-table');
    var th     = $( target ).getParent('th');
    var column = th.get('column');

    if( item.name == 'asc' ){
        window.location.href = TableURL( table , { 'sort': column , 'dir': 'ASC' } );
        return;
    }

    if( item.name == 'desc' ){
        window.location.href = TableURL( table , { 'sort': column , 'dir': 'DESC' } );
        return;
    }

    if( item.name == 'group' ){
        window.location.href = TableURL( table , { 'group': column } );
        return;
    }

    if( item.name == 'showgroups' ){

        if( !item.checkbox )
            window.location.href = TableURL( table , { 'group': null } );
        else
            window.location.href = TableURL( table ,{ 'group': column } );

        return;
    }

    onTableToggleColumn( table , item.name , item.checkbox )

}

function onTableToggleColumn( table , column , state ){

    //Get the column index
    var c         = table.rows[0].cells.length;
    var col_index = -1;
    for( i = 0 ; i < c ; i++ ){
        if( table.rows[ 0 ].cells[ i ].get('column') == column ){
            col_index = i;
            break;
        }
    }

    //Hide all cells in that column
    if( col_index != -1 ){
        var c = table.rows.length;
        for( i = 0 ; i < c ; i++ ){
            if( table.rows[ i ].cells.length > 1 ){
                table.rows[ i ].cells[ col_index ].style.display = state ? '' : 'none';
            }
        }
    }

}

function onTableInitExpanders( container ){

    var expanders = container.getElements('.yammon-table-expander-all');
    expanders.addEvent( 'click' , onExpanderAllClick );

    var expanders = container.getElements('.yammon-table-expander');
    expanders.addEvent( 'click' , onExpanderClick );

}

function onExpanderAllClick( e ){
    var table = $( this ).getParent('table');
    onTableExpandAll( table );
}

function onExpanderClick( e ){
    var row = $( this ).getParent('tr');
    onTableExpand( row );
}

function onTableExpandAll( table , expand ){

    //Get the master expander
    var expander = table.getElement('.yammon-table-expander-all');
    var rows     = table.getElements('.yammon-table-row' );

    if( !expander )
       return;

    //Get if we need to expand
    if( expand === undefined ){
        expand = expander.hasClass('yammon-table-expander-all-collapsed');
    }

    if( expand ){
        expander.removeClass('yammon-table-expander-all-collapsed');
    }else{
        expander.addClass('yammon-table-expander-all-collapsed');
    }

    var c = rows.length;
    for( var i = 0 ; i < c ; i++ ){
        onTableExpand( rows[i] , expand );
    }

}

function onTableExpand( row , expand ){

    //Get expander for row
    var expander = row.getElement('.yammon-table-expander');

    if( !expander )
        return;

    //Get if we need to expand
    if( expand === undefined ){
        expand = expander.hasClass('yammon-table-expander-collapsed');
    }

    //Get the affected rows
    var next = row.getNext('.yammon-table-row-extra');

    //Expand/collapse
    if( expand ){
        expander.removeClass('yammon-table-expander-collapsed');
        next.setStyle('display' , '' );
    }else{
        expander.addClass('yammon-table-expander-collapsed');
        next.setStyle('display' , 'none' );
    }

}

function onTableInitTree( container ){

      container.addEvent('click:relay(.yammon-table-tree-icon)' , function( e ){
        onTableTreeClick( e.target );
      });

}

function onTableTreeClick( target ){

    var target      = $( target );
    var row         = target.getParent('tr');
    var table       = target.getParent('table');
    var parent_id   = target.get('yammon-tree-id');
    var loaded      = true;//target.get('yammon-tree-loaded');
    var descendants = target.get('yammon-tree-descendants');
    var level       = target.get('yammon-tree-level');
    var leaf        = target.hasClass('yammon-table-tree-icon-leaf');
    var expand      = target.hasClass('yammon-table-tree-icon-closed');

    //If its a lead do nothing
    if( leaf ){
        return;
    }

    //If its not loaded load
    if( !loaded ){
        onTableLoadChilds( parent_id , target , row );
        return;
    }

    //Set the icon to expanded
    if( expand ){
        target.addClass( 'yammon-table-tree-icon-open' );
        target.removeClass( 'yammon-table-tree-icon-closed' );
    }else{
        target.removeClass( 'yammon-table-tree-icon-open' );
        target.addClass( 'yammon-table-tree-icon-closed' );
    }

    //Get the affect rows
    var childRows = [];
    var childRow  = row;
    for( var i = 0 ; i < descendants ; i++ ){
       childRow = childRow.getNext('tr');
       if( !childRow ) break;
       childRows.push( childRow );
    }

    //Show/Hide Descendants
    var c = childRows.length;
    for( var i = 0 ; i < c ; i++ ){

        var childRow         = childRows[i];
        var childIcon        = childRow.getElement('.yammon-table-tree-icon');
        var childDescendants = childIcon.get('yammon-tree-descendants');
        var childLevel       = childIcon.get('yammon-tree-level');
        var childClosed      = childIcon.hasClass('yammon-table-tree-icon-closed');

        if( expand )
            childRow.setStyle( 'display' , ''     );
        else
            childRow.setStyle( 'display' , 'none' );

        if( childClosed ){
            i = i + childDescendants;
            continue;
        }

    }

    //Retripe table
    onTableRetripe( table );


}

function onTableLoadChilds( parent_id , target , row ){

    //Send Request for child rows
    var req = new Request({
      method : 'POST' ,
      url    : window.location.href ,
      data   : { parent_id: parent_id } ,
      headers: { 'X-YAMMON-REQUEST' : 'HELPER_TABLE_TREE' } ,
      onComplete: function( html ){

           //Mark row as expanded
           target.set('yammon-tree-loaded' , 1 );

           //Add the the rows to the table
           var tmpTable = document.createElement('table');
           tmpTable.innerHTML = html;

           while( tmpTable.rows.length ){
               var orow = tmpTable.getElement('tr');
               row = orow.inject( row , 'after' );
           }

           //Simulate click on target
           onTableTreeClick( target );

      }

    });
    req.send();

}

function onTableRetripe( table ){
    var rows = table.getElements('tr');

    var c = rows.length;
    var j = 0;
    for( var i = 0 ; i < c ; i++ ){
        var row = rows[i];

        if( row.getStyle('display') == 'none' ){
            continue;
        }

        //Remove Classes
        row.removeClass('yammon-table-row-even');
        row.removeClass('yammon-table-row-odd');

        //Add Classes
        if( (j % 2) == 0 ){
            row.addClass('yammon-table-row-odd');
        }else{
            row.addClass('yammon-table-row-even');
        }

        j++;

    }

}


function onCheckboxAllClick( element ){

    var header_checkbox = $(element);
    var table           = header_checkbox.getParent('table.yammon-table');
    var checkboxes      = table.getElements('.yammon-table-column input[column='+header_checkbox.get('column')+']');

    for( var i = 0 ; i < checkboxes.length ; i++ ){
      checkboxes[i].checked = header_checkbox.checked;
    }
}

function ajaxify_table( name , filter  , url ){

    var timeout    = null;
    var container  = $(name);
    var filter     = $(filter);

    if( !url  )
        url = window.location.href;

    //Make sure the table exists
    if( !container ) return;

    //Ajax Filter
    if( filter && !filter.retrieve('table-filter') ){
        filter.store( 'table-filter' , 1 );
        filter.addEvent( 'keyup' , function( e ){
            clearTimeout( timeout );
            var url = new URI( url );
            var data = url.getData();
            data[ filter.name ] = filter.value;
            url.setData( data  );
            url = url.toString();
            timeout = setTimeout( reload_table.pass( [container.id,url] ) , 1000 );
        });
    }

    //Make sure we don't attach events twice
    if( container.retrieve('table-events') )
        return;
    container.store('table-events' , 1 );

    //Hijack clicks
    container.addEvent( 'click' , function( e ){

        var target = $(e.target);

        //Catch Search
        if( target.hasClass('yammon-table-empty-clear-search') ){
            e.stop();
            if( filter ) filter.value = '';
            reload_table( container.id  ,url );
            return;
        }

        //Catch Sorts
        if( target.hasClass('yammon-table-column-link') ){
            e.stop();
            reload_table( container.id , e.target.href );
            return;
        }

        //Catch Pagination
        if( target.hasClass('pagination-link') ){
            e.stop();
            reload_table( container.id , e.target.href );
            return;
        }

    });

    window.addEvent('ajax:success' , function(){
        ajaxify_table( name , filter );
    });

}


function reload_table( id , url ){

    if( !id ){
        el = document.getElement('.yammon-table');
        id = el.get('id');
    }

    if( !url )
        url = window.location.href;

    var req = new Request({
        'url': url ,
        'headers': {
            'X_YAMMON_REQUEST' : 'PARTS' ,
            'X_YAMMON_PARTS'   : 'helper.table.' + id.toLowerCase()
        },
        'onSuccess': function( html ){

            var el   = new Element("div");
            var org  = $(id);

            el.set('html' , html );
            var childs = el.getChildren();

            var len    = childs.length;
            var pel    = org;
            for( var i = 0 ; i < len ; i++ ){
                childs[ i ].inject( pel , 'after' );
                pel = childs[i];
            }
            org.destroy();
            onTableInit();
            $(window).fireEvent('ajax:success');

        }
    });
    req.get();

}
