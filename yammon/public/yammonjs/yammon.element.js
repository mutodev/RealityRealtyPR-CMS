/***
    YAMMON.Element
    
    Dom Wrapper
***/    
YAMMON.Element = YAMMON.Class.extend({
    init: function( el , properties ){
    
        //If a string was passed
        //covert to an element
        if( typeof el == 'string' ){
            el = document.createElement( el );
        }else if( typeof el == YAMMON.Element ){
            return el;
        }
                
        //Save the dom reference
        this.__dom = el;
                    
        //Set the properties
        this.setAttribute( properties );
                    
    },
    dom: function(){
        return this.__dom;
    },
    /* == Attributes ===================================== */
    id: function(){
        var id = this.get('id');

        if( YAMMON.Element.__id === undefined )
            YAMMON.Element.__id = 0;
            
        //Create an id for the element                
        if( id === null ){

            do{                
                id = "element_" + ( ++YAMMON.Element.__id );
            }while( document.getElementById( id ) );                    

            this.setAttribute('id' ,  id );
        }
        
        return id;
        
    },    
    setAttribute: function( attribute , value ){
    
        if( attribute === undefined )
            return;
            
        if( typeof attribute == "object" )
            for( var i in attribute )
                if( attribute.hasOwnProperty( i ) )
                    this.setAttribute( i , attribute[i] );

        if( value === undefined )
            return;
            
        key   = YAMMON.Element.Attributes.Map[ attribute ];
        value = YAMMON.Element.Attributes.Bools[key] ? !!value : value;
        
        if( key )
            this.__dom[ key ] = value;
        else
            this.__dom.setAttribute( attribute , '' + value );
        
        return this;

    },
    getAttribute: function( attribute ){
    
        var key   = YAMMON.Element.Attributes.Map[ attribute ];
        var value = (key) ? this.__dom[key] : this.__dom.getAttribute(attribute, 2);

        if( YAMMON.Element.Attributes.Bools[key] )	    	
            value = !!value;
        else if( !key )
            value = value || null;

        return value;
        
    },
    remove: function( attribute ){
        
        var key   = YAMMON.Element.Attributes.Map[ attribute ];
        
        if( key && YAMMON.Element.Attributes.Bools[key] )
           this.__dom[ key ] = false;
        else
           this.__dom.removeAttribute( attribute );    		
        
        return this;            

    },
    /* == Storage ===================================== */    
    store: function( key , value ){
        
        //Initialize Storage
        if( YAMMON.Element.__storage === undefined ){
            YAMMON.Element.__storage   = {};         
            YAMMON.Element.__storageId = 0;                         
        }        
        if( !this.__storageId )
            this.__storageId = ( ++YAMMON.Element.__storageId );
                
        var storage = YAMMON.Element.__storage;
        var id      = this.__storageId;
                    
        if( storage[ id ] === undefined )
             storage[ id ] = {};
        
        if( value === undefined )
            delete storage[ id ][ key ];
        else
            storage[ id ][ key ] = value;
                    
        return this;
        
    },
    retrieve: function( key ){

        //Initialize Storage
        if( YAMMON.Element.__storage === undefined ){
            YAMMON.Element.__storage   = {};         
            YAMMON.Element.__storageId = 0;                         
        }        
        if( !this.__storageId )
            this.__storageId = ( ++YAMMON.Element.__storageId );

        var storage = YAMMON.Element.__storage;
        var id      = this.__storageId;
        var value   = undefined;
                  
        if( storage[ id ] === undefined )
             storage[ id ] = {};
       
        if( storage[ id ].hasOwnProperty( key ) )
            return storage[ id ][ key ];
        else
            return null;

    },
    /* == Classes ===================================== */
    addClass: function( classname ){
        if (!this.hasClass(classname))
            this.__dom.className = YAMMON.JS.clean( this.__dom.className + ' ' + classname);
        
        return this;        
    },
    removeClass: function( classname ){
        this.__dom.className = this.__dom.className.replace(new RegExp('(^|\\s)' + classname + '(?:\\s|$)'), '$1');
        return this;        
    },
    hasClass: function( classname ){      
        return YAMMON.JS.contains( this.__dom.className , classname , '' );
    },
    toggleClass: function( classname ){
        this.hasClass( classname ) ? this.removeClass( classname ) : this.addClass( classname );
        return this;
    },
    /* == Style ===================================== */
    setStyle: function( key , value ){
    
    },
    getStyle: function( key , value ){
    
    },
    getComputedStyle: function( key , number ){
        var el  = this.__dom;
        var key = YAMMON.JS.camel( key );
        var val = null;
        if (el.currentStyle)
            val = el.currentStyle[key];
        else if (window.getComputedStyle)
            val = document.defaultView.getComputedStyle(el,null).getPropertyValue(key);
            
        if( number )
            val = parseInt( val , 10 ) || 0;

        return val;
    },
    hide: function(){
        this.__dom.style.display = 'none';
    },
    show: function(){
        this.__dom.style.display = '';    
    },
    visible: function(){
        return this.__dom.style.display !== 'none';
    },
    /* == Transversal ===================================== */
    matches: function( selector ){
        return YAMMON.matches( el , selector );
    },
    getPrevious: function( selector ){
        return YAMMON.Element.walk( this.__dom , 'previousSibling', null, selector, false );
    },
    getAllPrevious: function( selector ){
        return YAMMON.Element.walk( this.__dom , 'previousSibling', null, selector, true );
    },
    getNext: function( selector ){        
        return YAMMON.Element.walk( this.__dom , 'nextSibling', null, selector, false );
    },
    getAllNext: function( selector ){
        return YAMMON.Element.walk( this.__dom , 'nextSibling', null, selector, true );
    },
    getFirst: function( selector ){
        return YAMMON.Element.walk( this.__dom , 'nextSibling', 'firstChild' , selector, false  );
    },
    getLast: function( selector ){
        return YAMMON.Element.walk( this.__dom , 'previousSibling', 'lastChild' , selector, false  );
    },
    getParent: function( selector ){
        return YAMMON.Element.walk( this.__dom , 'parentNode', null, selector, false  );
    },
    getParents: function( selector ){
        return YAMMON.Element.walk( this.__dom , 'parentNode', null, selector, true  );
    },
    getSiblings: function( selector ){

        var parent = this.getParent();
        if( !parent ) return [];
                    
        var children = parent.getChildren( selector );
        return YAMMON.JS.without( children , this.__dom );
        
    },
    getChildren: function( selector ){
        return YAMMON.Element.walk( this.__dom , 'nextSibling' , 'firstChild' , selector , true  );
    },
    getElement: function( selector ){
        return this._getElement( selector , this.__dom );
    },
    _getElement: function( selector , el ){
        
        for( var i = 0 , c = el.childNodes.length ; i < c ; i++ ){
            var el2  = el.childNodes[i];
            
            if( el2.nodeType != 1 ) 
                continue;                        
                
            if( YAMMON.matches( el2, selector ) )
                return el2;
                
            var next = YAMMON.Element.walk( el2 , 'nextSibling', null, selector, false );
            if( next ) 
                return next;

            var nextchild = this._getElement( selector , el2 );                
            if( nextchild )
                return nextchild;

        }
    
        return null;    
    
    },
    getElements: function( selector ){
        return YAMMON.query( selector , this.__dom );    
    },
    /* == Manipulation ===================================== */    
    clone: function( deep ){
        return this.__dom.cloneNode( deep );
    },
    append: function( element , where ){
    
        var context = this.__dom;
        switch( where ){
            case 'before': //Before
                           if (context.parentNode) 
                              context.parentNode.insertBefore(element, context);
                           break;
                           
            case 'after':  //After
                           if (!context.parentNode) return;
		                   var next = context.nextSibling;
		                   if( next )
		                      context.parentNode.insertBefore(element, next);
		                   else
		                      context.parentNode.appendChild(element);            
                           break;

            case 'top':    //Top
                           var first = context.firstChild;
                           if( first )
                              context.insertBefore(element, first);
                           else
                              context.appendChild(element);            
                           break;
                           
            case 'bottom': //Bottom
            default:       context.appendChild(element);
                           break;
            
        }
    },
    dispose: function(){
        var el = this.__dom;
        var p  = el.parentNode;
        if( p ) p.removeChild( el );
        return this;
    },
    empty: function(){
        var el = this.__dom;
        for( var i = 0 , c = el.childNodes.length ; i < c ; i++ )
            el.removeChild( el.childNodes[i] );
		return this;    
    },
    replaceWith: function( replacement ){
    
    },
    /* == Dimmensions ============================== */
    getSize: function(){
        var el = this.__dom;
		return {width: el.offsetWidth, height: el.offsetHeight};    
    },
    getScrollSize: function(){
        var el = this.__dom;    
		return {width: el.scrollWidth, height: el.scrollHeight};    
    },
    getScroll: function(){
        var el = this.__dom;       
		return {left: el.scrollLeft, top: el.scrollTop};    
    },
    getPosition: function( relative ){

        var el   = this.__dom;
    	var left = obj.offsetLeft;
    	var top  = obj.offsetTop;
    	    	
    	if( relative )    	
        	while( el = el.getOffsetParent() ){
                top  += el.__dom.offsetTop;
                left += el.__dom.offsetLeft;            
        	}
 
        return {
            top:  top  ,
            left: left
        };
 
    },
    setPosition: function( top , left ){
        var style = {
            top:  top  ,        
            left: left 
        };     
        this.setStyle( style );
    },
    getCoordinates: function(){
		var position = this.getPosition();
		var size     = this.getSize();
		var obj      = {
		   left:    position.left , 
		   top:     position.top , 
		   width:   size.width , 
		   height:  size.height ,
		   right:   position.left + size.width ,
		   bottom:  position.top  + size.height
		};
		return obj;    
    },
    getOffsetParent: function( ){
            
        if( !YAMMON.Browser.trident() ){
            if( this.__dom.offsetParent) 
                return new YAMMON.Element( this.__dom.offsetParent );
            else
                return null;
        }
    
        var el = this;
		while ((el = el.getParent() ) && el.tagName != 'BODY' ){
		    el = new YAMMON.Element( el );
			if( el.getComputedStyle( 'position' ) != 'static' ) 
			    return el;
		}

		return null;
		
    },
    /* == Events =================================== */
    on: function( event , callback , capture ){
    
        var el = this.__dom;
        var fn = function( e ){
            e = e || window.event;
            return callback( e );
        };
        
    	if ( el.addEventListener )
		    el.addEventListener(event, callback, fn );
        else
            elm.attachEvent('on' + event , fn );

        return this;

    },
    /* == Form ===================================== */            
    serialize: function( selector ){
    
		var queryString = [];
		Hash.each(this, function(value, key){
			if (base) key = base + '[' + key + ']';
			var result;
			switch ($type(value)){
				case 'object': result = Hash.toQueryString(value, key); break;
				case 'array':
					var qs = {};
					value.each(function(val, i){
						qs[i] = val;
					});
					result = Hash.toQueryString(qs, key);
				break;
				default: result = key + '=' + encodeURIComponent(value);
			}
			if (value != undefined) queryString.push(result);
		});

		return queryString.join('&');    
    
    }
});

YAMMON.Element.walk = function( element, walk, start, selector , all , nowrap ){

    var el = element[start || walk];
    var elements = [];
    while (el){
        if (el.nodeType == 1 && YAMMON.matches(el, selector )){
            if( !nowrap ) el = new Element( el );
            if (!all) return el;
            elements.push(el);
        }
        el = el[walk];
    }
    
    return all ? elements : null;
    
};

YAMMON.Element.Attributes = {
    'Map':{
       'html':         'innerHTML',
       'class':        'className',
       'for':          'htmlFor' ,
       'value':        'value', 
       'type':         'type', 
       'defaultValue': 'defaultvalue' , 
       'accessKey' :   'accesskey', 
       'cellPadding':  'cellpadding', 
       'cellSpacing':  'cellspacing' ,
       'colSpan':      'colspan', 
       'frameBorder':  'frameborder', 
       'maxLength':    'maxlength', 
       'readOnly':     'readonly', 
       'rowSpan':      'rowspan', 
       'tabIndex':     'tabindex', 
       'useMap':       'usemap' ,
       'compact':      'compact' , 
       'nowrap':       'nowrap', 
       'ismap' :       'ismap' , 
       'declare':      'declare' , 
       'noshade':      'noshade' , 
       'checked':      'checked' , 
       'disabled':     'disabled' , 
       'readonly':     'readonly' , 
       'multiple':     'multiple' , 
       'selected':     'selected' , 
       'noresize':     'noresize' , 
       'defer':        'defer'
    },
    'Bools': {
      'compact':  1 , 
      'nowrap':   1 , 
      'ismap' :   1 , 
      'declare':  1 , 
      'noshade':  1 , 
      'checked':  1 , 
      'disabled': 1 , 
      'readonly': 1 , 
      'multiple': 1 , 
      'selected': 1 , 
      'noresize': 1 , 
      'defer':    1 
     }
};
