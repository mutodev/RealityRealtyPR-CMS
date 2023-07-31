YAMMON.JS = {
    isString: function( obj ){
       return _toString.call(object) == "[object String]";
    },
    isNumber: function( obj ){
      return _toString.call(object) == "[object Number]";
    },
    isArray: function( obj ){
        return obj.toString.call(obj) === "[object Array]";
    },
    isFunction: function( obj ){
        return typeof object === "function";    
    },
    contains: function( string , substr , separator ){
        return (separator) ? (separator + string + separator).indexOf(separator + substr + separator) > -1 : string.indexOf(substr) > -1;
    },
    clean: function( string ){
        return YAMMON.JS.trim( string.replace(/\s+/g, ' ') );
    },
    trim: function( string ){
        return string.replace(/^\s+|\s+$/g,"");
    },
    camel: function( string ){
		return string.replace(/-\D/g, function(match){
			return match.charAt(1).toUpperCase();
		});    
    },
    without: function( arr , item ){
    
        var result = [];
        for (var i = 0 , c = arr.length ; i < c ; i++ )
            if( arr[ i ] !== item )
                result.push( arr[ i ] );

        return result;
    }        
};