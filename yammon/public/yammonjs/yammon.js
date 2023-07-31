//TODO
//FX
//AJAX
//DOMREADY
//JSON
//COOKIE
//STORAGE
//HISTORY

var YAMMON = {
    get: function( id ){
        var dom = document.getElementById( id );
        return dom ? new YAMMON.Element( dom ) : null;
    },
    query: function( selector , context ){
        var els = Sizzle( selector , context );
        for( var i = 0 , c = els.length ; i < c ; i++ )
            els[i] = new YAMMON.Element( els[i] );

        return els;
    },
    matches: function( element , selector ){
    
        if( selector == undefined )
            return true;
    
        var a = [ element ];
        var b = Sizzle.matches( selector , a );
        return a.length == b.length;
    },
    browser: function( name ){
        switch( name ){
            case 'presto':  return (!window.opera) ? false : ((arguments.callee.caller) ? 960 : ((document.getElementsByClassName) ? 950 : 925));
                            break;
            case 'trident': return (!window.ActiveXObject) ? false : ((window.XMLHttpRequest) ? 5 : 4);
                            break;
            case 'webkit':  return (navigator.taintEnabled) ? false : ((Browser.Features.xpath) ? ((Browser.Features.query) ? 525 : 420) : 419);
                            break;
            case 'gecko':   return (document.getBoxObjectFor == undefined) ? false : ((document.getElementsByClassName) ? 19 : 18);
                            break;         
        }
        return undefined;
    }
};