YAMMON.Widgets.Form = new Class({
    Extends: YAMMON.Widget ,
    options: {
    },
    initialize: function( node , options ){

        this.parent( node , options );
        this.locked = 0;
        this.highlighting_attached = false;
        this.change_attached       = false;
        this.eventManager          = {};
        this.refresh();
    },
    refresh: function(){
        this.setupHighlighting();
        this.setupDependencies();
        this.setupNested();
        this.setupLocks();
        this.setupPlaceHolders();
    },
    lock: function( bool ){

        if( bool == undefined )
            bool = true;

        if( bool )
            this.locked++;
        else
            this.locked--;
    },
    unlock: function(){
        this.lock( false );
    },
    setupHighlighting: function(){

        if( this.highlighting_attached )
            return false;

        var node = this.node;
        if( node.addEventListener ){
            node.addEventListener( 'focus' , this.onFocus.bindWithEvent( this ) , true );
            node.addEventListener( 'blur'  , this.onBlur.bindWithEvent(this)    , true );
        }else{
           node.addEvent( 'focusin'   , this.onFocus.bindWithEvent( this ) );
           node.addEvent( 'focusout'  , this.onBlur.bindWithEvent(this)    );
        }

        this.highlighting_attached = true;

        return true;
    },
    setupDependencies: function(){


        //Get dependant elements
        var has_dependencies  = false;
        var node              = this.node;
        var attribute         = 'ym-form-dependencies';
        var dependants_map    = this.dependants_map = {};
        var dependency_map    = this.dependency_map = {};
        var dependants        = node.getElements('*['+ attribute +']');

        this.cleanEvents('Dependencies');

        //Create dependecy maps
        for( var i = 0 ; i < dependants.length ; i++ ){

            var dependant      = dependants[i];
            var property       = dependant.getProperty(attribute).split(":");
            var dependant_name = property[ 0 ];
            var depends_on     = property[1].split(",");

            //Create Dependants Map
            dependants_map[ dependant_name ] = dependant.get('id');

            //Create dependency map
            for( var j = 0 ; j < depends_on.length ; j++ ){
                var on = depends_on[j];

                if( !dependency_map[ on ] )
                    dependency_map[ on ] = [];

                dependency_map[ on ].push( dependant_name );
                has_dependencies = true;
            }

        }

        //Attach event handlers
		for( var id in dependency_map ){
			if( !dependency_map.hasOwnProperty( id ) ) continue;

            var el = $(id);
            if( !el ) continue;

            if( el.attachEvent ){
                //Internet Explorer Attach Events Directly
                var elements = el.getElements("input , select, textarea ");
                elements.push( el );
            }else{
                //Other Browsers use event delegation
                var elements = [ el ];
            }

            //Attach events
			var len = elements.length;
			for( var j = 0 ; j < len ; j++ ){
                if( elements[j].match("input[type='radio']") || elements[j].match("input[type='checkbox']") ){
                    this.registerEvent( 'Dependencies', elements[j], 'click', this.onDependsChange.bindWithEvent( this ,  [elements[j]] ), false );
                }else{
                    this.registerEvent( 'Dependencies', elements[j], 'change', this.onDependsChange.bindWithEvent( this ,  [elements[j]] ), false );
                }
			}

		}

    },
    setupNested: function(){

        //Get dependant elements
        var has_dependencies  = false;
        var node              = this.node;
        var attribute         = 'ym-form-nested';
        var dependants_map    = this.nested_dependants_map = {};
        var dependency_map    = this.nested_dependency_map = {};
        var dependants        = node.getElements('*['+ attribute +']');

		this.cleanEvents('Nested');

        //Create dependecy maps
        for( var i = 0 ; i < dependants.length ; i++ ){

            var dependant      = dependants[i];
            var property       = dependant.getProperty(attribute).split(":");
            var dependant_name = property[ 0 ];
            var depends_on     = property[1].split(",");

            //Create Dependants Map
            dependants_map[ dependant_name ] = dependant.get('id');

            //Create dependency map
            for( var j = 0 ; j < depends_on.length ; j++ ){
                var on = depends_on[j];

                if( !dependency_map[ on ] )
                    dependency_map[ on ] = [];

                dependency_map[ on ].push( dependant_name );
                has_dependencies = true;
            }

        }

        //Attach event handlers
		for( var id in dependency_map ){
			if( !dependency_map.hasOwnProperty( id ) ) continue;

            var el = $(id);
            if( !el ) continue;

            if( el.attachEvent ){
                //Internet Explorer Attach Events Directly
                var elements = el.getElements("input , select, textarea ");
                elements.push( el );
            }else{
                //Other Browsers use event delegation
                var elements = [ el ];
            }

            //Attach events
			var len = elements.length;
			for( var j = 0 ; j < len ; j++ ){
                if( elements[j].match("input[type='radio']") || elements[j].match("input[type='checkbox']") ){
                    this.registerEvent( 'Nested', elements[j], 'click' , this.onNestedChange.bindWithEvent( this  , [elements[j]] ), false );
                }else{
                    this.registerEvent( 'Nested', elements[j], 'change', this.onNestedChange.bindWithEvent( this , [elements[j]] ), false );
                }
			}

		}

    },
    setupLocks: function(){

        var node    = this.node;
        var self    = this;
        node.addEvent('submit' , function(e){
            if( self.locked > 0 ){
                if( confirm('There are pending processes.\n Do you want to wait until they are finished?') ){
                    e.stop();
                }
            }
        });

    },
    setupPlaceHolders: function(){

        //Get the elements to placeholder
        var placeholders = this.node.getElements('input[placeholder] , textarea[placeholder]');
        var len          = placeholders.length;
        for( var i = 0 ; i < len ; i++ ){

            var el = placeholders[i];

            //Check for placeholder support
            if( 'placeholder' in el )
                continue;

            //Bind Event
            el.addEvent( 'blur'  , this.onPlaceHolderBlur );
            el.addEvent( 'focus' , this.onPlaceHolderFocus );

            //Simulate a blur
            this.onPlaceHolderBlur.call( el );

        }

        //Remove Placeholders before submit
        this.node.addEvent('submit' , function(){

            var len = placeholders.length;
            for( var i = 0 ; i < len ; i++ ){
                var el = placeholders[i];
                if( el.retrieve('placeholdered') )
                    this.value = '';
            }
        });


    },
    onPlaceHolderBlur: function(){
        if( this.value == '' ){
            this.value = this.getProperty('placeholder');
            this.setStyle('color' , '#999' );
            this.store('placeholdered' , true );
        }
    },
    onPlaceHolderFocus: function(){
        if( this.retrieve('placeholdered') ){
            this.value = '';
            this.setStyle('color' , '' );
            this.store('placeholdered' , false );
        }
    },
    onFocus: function( e ){

        //Get Target
        var target = $( e.target );

        //Get Highlight parent
		//Dont know why $().getParent wasn't working in ie
		var parent = target;
		do{
			parent = $(parent.parentNode);
			if( !parent ) return;
			if( parent.hasClass && parent.hasClass('ym-form-box') ) break;
		}while( true );

        //Check if highlighting is disabled
        if( parent.hasClass('ym-form-box-no-highlight') )
            return false;

        //Check if we need to highlight a container
        var container = parent.getParent('.ym-form-box-highlight');
        if( container )
            parent = container;

        //Highlight
        this.activeElement = parent;
        parent.addClass( "ym-form-box-active" );

    },
    onBlur: function( e ){

        //Remove Class
        if( this.activeElement )
            this.activeElement.removeClass('ym-form-box-active');

    },
    onDependsChange: function( e , target ){

        var self   = this;
        var id     = target.id;
        var form   = this.node;

        //Check if a depency matches
        var dependecy = false;
        var map       = this.dependency_map;
        if( map.hasOwnProperty( id ) )
            dependecy = map[ id ];
        else{

            //This is a hack for radios/checkboxes
            var parent = target;
            while( parent = parent.getParent() ){
                if( map.hasOwnProperty( parent.id ) ){
                    dependecy = map[parent.id];
                    break;
                }
            }

        }

        if( dependecy ){

            //Make request
            var method = form.get('method');
            var data   = form.toQueryString();
            var headers = {
                'X-YAMMON-REQUEST'         : 'HELPER_FORM-DEPENDS',
                'X-YAMMON-REQUEST-DEPENDS' : dependecy.join(','),
                'Content-Type'             : 'application/gzip'
            };

            //Do Request
            var req = new Request({
                'method':     method ,
                'url':        window.location.href,
                'urlEncoded': false,
                'data':       btoa(pako.gzip(data, {to:'string'})),
                'headers':    headers ,
                'onComplete': this.onDependsComplete.bind( this )
            }).send();

        }
    },
    onNestedChange: function( e , target ){

        var id     = target.id;
        var form   = this.node;

        //Make request
        var method = form.get('method');
        var data   = form.toQueryString();
        var headers = {
            'X-YAMMON-REQUEST'         : 'HELPER_FORM-NESTED' ,
            'X-YAMMON-REQUEST-NESTED'  : this.nested_dependency_map[ id ].join(','),
            'Content-Type'             : 'application/gzip'
        };

        //Do Request
        var req = new Request({
            'method':     method ,
            'url':        window.location.href,
            'urlEncoded': false,
            'data':       btoa(pako.gzip(data, {to:'string'})),
            'headers':    headers ,
            'onComplete': this.onNestedComplete.bind( this )
        }).send();

    },
    onDependsComplete: function( response ){

        //Get response
        response = JSON.decode( response );

        //Show hide elements
        for( var i in response ){
            if( !response.hasOwnProperty( i ) ) continue;

            //Get the element/value
            var el    = $( this.dependants_map[i] );
            var value = response[i];
            if( !el ) continue;

            //Show Hide/Element
            if( value ) {
                el.setStyle('display' , '' );
                el.fireEvent( 'dependshow', [!!value] );
            }
            else {
                el.setStyle('display' , 'none' );
                el.fireEvent( 'dependhide', [!!value] );
            }

            this.node.fireEvent( 'form:depends' ,  [!!value, el] );
            window.fireEvent( 'form:depends'    ,  [!!value, el]  );

            this.setupDependencies();
        }
    },
    onNestedComplete: function( response ){

        //Get response
        response = JSON.decode( response );

        //Show hide elements
        for( var i in response ){
            if( !response.hasOwnProperty( i ) ) continue;

            //Get the element/value
            var el   = $( this.nested_dependants_map[i] );
            var id   = el.id;
            var html = response[i];

            if( !el ) continue;

            //Replace the element
            var previousValue = el.get('value');
            el.set('html' , html  );

            if( el.tagName == 'SELECT' ){
                var value = '';
                for( var j = 0 ; j < el.options.length ; j++ ){
                    if( previousValue == el.options[j].value ){
                        value = previousValue;
                        break;
                    }
                }
                el.set( 'value', value );
            }

            //Fire Event
            el.fireEvent('nestedcomplete');

        }

        this.node.fireEvent( 'nestedcomplete' );
        this.setupNested();

    },
    registerEvent: function( subWidget, element, type, handler, isCapture ){

    	if( !this.eventManager.hasOwnProperty( subWidget ) )
    		this.eventManager[ subWidget ] = [];

		var event = {
			'element'   : element ,
			'type'      : type ,
			'handler'   : handler ,
			'isCapture' : isCapture
		};

		if ( isCapture )
			element.addEventListener( type , handler , true );
		else
			element.addEvent( type , handler );

		this.eventManager[ subWidget ].push( event );
    },
    cleanEvents: function( subWidget ){

		var objEvent = null;

    	if( !this.eventManager.hasOwnProperty( subWidget ) )
    		this.eventManager[ subWidget ] = [];

        //Remove previous events
        for( i in this.eventManager[ subWidget ] ){
            if( !this.eventManager[ subWidget ].hasOwnProperty( i ) ) continue;

            objEvent = this.eventManager[subWidget][i];


			if ( objEvent.isCapture )
				objEvent.element.removeEventListener( objEvent.type , objEvent.handler, objEvent.isCapture );
			else
				objEvent.element.removeEvent( objEvent.type , objEvent.handler );
        }

        this.eventManager[ subWidget ] = [];
    },

    formToJSON: function(form) {
        var j = {};

        Array.each(form.toQueryString().split('&'),function(a){
            var kv = a.split('=');
            j[kv[0]] = kv[1]||'';
        });

        return JSON.encode(j);
    }
});

/* pako 1.0.1 nodeca/pako - pako_deflate.min.js */
!function(t){if("object"==typeof exports&&"undefined"!=typeof module)module.exports=t();else if("function"==typeof define&&define.amd)define([],t);else{var e;e="undefined"!=typeof window?window:"undefined"!=typeof global?global:"undefined"!=typeof self?self:this,e.pako=t()}}(function(){return function t(e,a,n){function r(s,h){if(!a[s]){if(!e[s]){var l="function"==typeof require&&require;if(!h&&l)return l(s,!0);if(i)return i(s,!0);var o=new Error("Cannot find module '"+s+"'");throw o.code="MODULE_NOT_FOUND",o}var _=a[s]={exports:{}};e[s][0].call(_.exports,function(t){var a=e[s][1][t];return r(a?a:t)},_,_.exports,t,e,a,n)}return a[s].exports}for(var i="function"==typeof require&&require,s=0;s<n.length;s++)r(n[s]);return r}({1:[function(t,e,a){"use strict";var n="undefined"!=typeof Uint8Array&&"undefined"!=typeof Uint16Array&&"undefined"!=typeof Int32Array;a.assign=function(t){for(var e=Array.prototype.slice.call(arguments,1);e.length;){var a=e.shift();if(a){if("object"!=typeof a)throw new TypeError(a+"must be non-object");for(var n in a)a.hasOwnProperty(n)&&(t[n]=a[n])}}return t},a.shrinkBuf=function(t,e){return t.length===e?t:t.subarray?t.subarray(0,e):(t.length=e,t)};var r={arraySet:function(t,e,a,n,r){if(e.subarray&&t.subarray)return void t.set(e.subarray(a,a+n),r);for(var i=0;n>i;i++)t[r+i]=e[a+i]},flattenChunks:function(t){var e,a,n,r,i,s;for(n=0,e=0,a=t.length;a>e;e++)n+=t[e].length;for(s=new Uint8Array(n),r=0,e=0,a=t.length;a>e;e++)i=t[e],s.set(i,r),r+=i.length;return s}},i={arraySet:function(t,e,a,n,r){for(var i=0;n>i;i++)t[r+i]=e[a+i]},flattenChunks:function(t){return[].concat.apply([],t)}};a.setTyped=function(t){t?(a.Buf8=Uint8Array,a.Buf16=Uint16Array,a.Buf32=Int32Array,a.assign(a,r)):(a.Buf8=Array,a.Buf16=Array,a.Buf32=Array,a.assign(a,i))},a.setTyped(n)},{}],2:[function(t,e,a){"use strict";function n(t,e){if(65537>e&&(t.subarray&&s||!t.subarray&&i))return String.fromCharCode.apply(null,r.shrinkBuf(t,e));for(var a="",n=0;e>n;n++)a+=String.fromCharCode(t[n]);return a}var r=t("./common"),i=!0,s=!0;try{String.fromCharCode.apply(null,[0])}catch(h){i=!1}try{String.fromCharCode.apply(null,new Uint8Array(1))}catch(h){s=!1}for(var l=new r.Buf8(256),o=0;256>o;o++)l[o]=o>=252?6:o>=248?5:o>=240?4:o>=224?3:o>=192?2:1;l[254]=l[254]=1,a.string2buf=function(t){var e,a,n,i,s,h=t.length,l=0;for(i=0;h>i;i++)a=t.charCodeAt(i),55296===(64512&a)&&h>i+1&&(n=t.charCodeAt(i+1),56320===(64512&n)&&(a=65536+(a-55296<<10)+(n-56320),i++)),l+=128>a?1:2048>a?2:65536>a?3:4;for(e=new r.Buf8(l),s=0,i=0;l>s;i++)a=t.charCodeAt(i),55296===(64512&a)&&h>i+1&&(n=t.charCodeAt(i+1),56320===(64512&n)&&(a=65536+(a-55296<<10)+(n-56320),i++)),128>a?e[s++]=a:2048>a?(e[s++]=192|a>>>6,e[s++]=128|63&a):65536>a?(e[s++]=224|a>>>12,e[s++]=128|a>>>6&63,e[s++]=128|63&a):(e[s++]=240|a>>>18,e[s++]=128|a>>>12&63,e[s++]=128|a>>>6&63,e[s++]=128|63&a);return e},a.buf2binstring=function(t){return n(t,t.length)},a.binstring2buf=function(t){for(var e=new r.Buf8(t.length),a=0,n=e.length;n>a;a++)e[a]=t.charCodeAt(a);return e},a.buf2string=function(t,e){var a,r,i,s,h=e||t.length,o=new Array(2*h);for(r=0,a=0;h>a;)if(i=t[a++],128>i)o[r++]=i;else if(s=l[i],s>4)o[r++]=65533,a+=s-1;else{for(i&=2===s?31:3===s?15:7;s>1&&h>a;)i=i<<6|63&t[a++],s--;s>1?o[r++]=65533:65536>i?o[r++]=i:(i-=65536,o[r++]=55296|i>>10&1023,o[r++]=56320|1023&i)}return n(o,r)},a.utf8border=function(t,e){var a;for(e=e||t.length,e>t.length&&(e=t.length),a=e-1;a>=0&&128===(192&t[a]);)a--;return 0>a?e:0===a?e:a+l[t[a]]>e?a:e}},{"./common":1}],3:[function(t,e,a){"use strict";function n(t,e,a,n){for(var r=65535&t|0,i=t>>>16&65535|0,s=0;0!==a;){s=a>2e3?2e3:a,a-=s;do r=r+e[n++]|0,i=i+r|0;while(--s);r%=65521,i%=65521}return r|i<<16|0}e.exports=n},{}],4:[function(t,e,a){"use strict";function n(){for(var t,e=[],a=0;256>a;a++){t=a;for(var n=0;8>n;n++)t=1&t?3988292384^t>>>1:t>>>1;e[a]=t}return e}function r(t,e,a,n){var r=i,s=n+a;t^=-1;for(var h=n;s>h;h++)t=t>>>8^r[255&(t^e[h])];return-1^t}var i=n();e.exports=r},{}],5:[function(t,e,a){"use strict";function n(t,e){return t.msg=O[e],e}function r(t){return(t<<1)-(t>4?9:0)}function i(t){for(var e=t.length;--e>=0;)t[e]=0}function s(t){var e=t.state,a=e.pending;a>t.avail_out&&(a=t.avail_out),0!==a&&(j.arraySet(t.output,e.pending_buf,e.pending_out,a,t.next_out),t.next_out+=a,e.pending_out+=a,t.total_out+=a,t.avail_out-=a,e.pending-=a,0===e.pending&&(e.pending_out=0))}function h(t,e){U._tr_flush_block(t,t.block_start>=0?t.block_start:-1,t.strstart-t.block_start,e),t.block_start=t.strstart,s(t.strm)}function l(t,e){t.pending_buf[t.pending++]=e}function o(t,e){t.pending_buf[t.pending++]=e>>>8&255,t.pending_buf[t.pending++]=255&e}function _(t,e,a,n){var r=t.avail_in;return r>n&&(r=n),0===r?0:(t.avail_in-=r,j.arraySet(e,t.input,t.next_in,r,a),1===t.state.wrap?t.adler=D(t.adler,e,r,a):2===t.state.wrap&&(t.adler=I(t.adler,e,r,a)),t.next_in+=r,t.total_in+=r,r)}function d(t,e){var a,n,r=t.max_chain_length,i=t.strstart,s=t.prev_length,h=t.nice_match,l=t.strstart>t.w_size-dt?t.strstart-(t.w_size-dt):0,o=t.window,_=t.w_mask,d=t.prev,u=t.strstart+_t,f=o[i+s-1],c=o[i+s];t.prev_length>=t.good_match&&(r>>=2),h>t.lookahead&&(h=t.lookahead);do if(a=e,o[a+s]===c&&o[a+s-1]===f&&o[a]===o[i]&&o[++a]===o[i+1]){i+=2,a++;do;while(o[++i]===o[++a]&&o[++i]===o[++a]&&o[++i]===o[++a]&&o[++i]===o[++a]&&o[++i]===o[++a]&&o[++i]===o[++a]&&o[++i]===o[++a]&&o[++i]===o[++a]&&u>i);if(n=_t-(u-i),i=u-_t,n>s){if(t.match_start=e,s=n,n>=h)break;f=o[i+s-1],c=o[i+s]}}while((e=d[e&_])>l&&0!==--r);return s<=t.lookahead?s:t.lookahead}function u(t){var e,a,n,r,i,s=t.w_size;do{if(r=t.window_size-t.lookahead-t.strstart,t.strstart>=s+(s-dt)){j.arraySet(t.window,t.window,s,s,0),t.match_start-=s,t.strstart-=s,t.block_start-=s,a=t.hash_size,e=a;do n=t.head[--e],t.head[e]=n>=s?n-s:0;while(--a);a=s,e=a;do n=t.prev[--e],t.prev[e]=n>=s?n-s:0;while(--a);r+=s}if(0===t.strm.avail_in)break;if(a=_(t.strm,t.window,t.strstart+t.lookahead,r),t.lookahead+=a,t.lookahead+t.insert>=ot)for(i=t.strstart-t.insert,t.ins_h=t.window[i],t.ins_h=(t.ins_h<<t.hash_shift^t.window[i+1])&t.hash_mask;t.insert&&(t.ins_h=(t.ins_h<<t.hash_shift^t.window[i+ot-1])&t.hash_mask,t.prev[i&t.w_mask]=t.head[t.ins_h],t.head[t.ins_h]=i,i++,t.insert--,!(t.lookahead+t.insert<ot)););}while(t.lookahead<dt&&0!==t.strm.avail_in)}function f(t,e){var a=65535;for(a>t.pending_buf_size-5&&(a=t.pending_buf_size-5);;){if(t.lookahead<=1){if(u(t),0===t.lookahead&&e===q)return vt;if(0===t.lookahead)break}t.strstart+=t.lookahead,t.lookahead=0;var n=t.block_start+a;if((0===t.strstart||t.strstart>=n)&&(t.lookahead=t.strstart-n,t.strstart=n,h(t,!1),0===t.strm.avail_out))return vt;if(t.strstart-t.block_start>=t.w_size-dt&&(h(t,!1),0===t.strm.avail_out))return vt}return t.insert=0,e===N?(h(t,!0),0===t.strm.avail_out?kt:zt):t.strstart>t.block_start&&(h(t,!1),0===t.strm.avail_out)?vt:vt}function c(t,e){for(var a,n;;){if(t.lookahead<dt){if(u(t),t.lookahead<dt&&e===q)return vt;if(0===t.lookahead)break}if(a=0,t.lookahead>=ot&&(t.ins_h=(t.ins_h<<t.hash_shift^t.window[t.strstart+ot-1])&t.hash_mask,a=t.prev[t.strstart&t.w_mask]=t.head[t.ins_h],t.head[t.ins_h]=t.strstart),0!==a&&t.strstart-a<=t.w_size-dt&&(t.match_length=d(t,a)),t.match_length>=ot)if(n=U._tr_tally(t,t.strstart-t.match_start,t.match_length-ot),t.lookahead-=t.match_length,t.match_length<=t.max_lazy_match&&t.lookahead>=ot){t.match_length--;do t.strstart++,t.ins_h=(t.ins_h<<t.hash_shift^t.window[t.strstart+ot-1])&t.hash_mask,a=t.prev[t.strstart&t.w_mask]=t.head[t.ins_h],t.head[t.ins_h]=t.strstart;while(0!==--t.match_length);t.strstart++}else t.strstart+=t.match_length,t.match_length=0,t.ins_h=t.window[t.strstart],t.ins_h=(t.ins_h<<t.hash_shift^t.window[t.strstart+1])&t.hash_mask;else n=U._tr_tally(t,0,t.window[t.strstart]),t.lookahead--,t.strstart++;if(n&&(h(t,!1),0===t.strm.avail_out))return vt}return t.insert=t.strstart<ot-1?t.strstart:ot-1,e===N?(h(t,!0),0===t.strm.avail_out?kt:zt):t.last_lit&&(h(t,!1),0===t.strm.avail_out)?vt:yt}function p(t,e){for(var a,n,r;;){if(t.lookahead<dt){if(u(t),t.lookahead<dt&&e===q)return vt;if(0===t.lookahead)break}if(a=0,t.lookahead>=ot&&(t.ins_h=(t.ins_h<<t.hash_shift^t.window[t.strstart+ot-1])&t.hash_mask,a=t.prev[t.strstart&t.w_mask]=t.head[t.ins_h],t.head[t.ins_h]=t.strstart),t.prev_length=t.match_length,t.prev_match=t.match_start,t.match_length=ot-1,0!==a&&t.prev_length<t.max_lazy_match&&t.strstart-a<=t.w_size-dt&&(t.match_length=d(t,a),t.match_length<=5&&(t.strategy===J||t.match_length===ot&&t.strstart-t.match_start>4096)&&(t.match_length=ot-1)),t.prev_length>=ot&&t.match_length<=t.prev_length){r=t.strstart+t.lookahead-ot,n=U._tr_tally(t,t.strstart-1-t.prev_match,t.prev_length-ot),t.lookahead-=t.prev_length-1,t.prev_length-=2;do++t.strstart<=r&&(t.ins_h=(t.ins_h<<t.hash_shift^t.window[t.strstart+ot-1])&t.hash_mask,a=t.prev[t.strstart&t.w_mask]=t.head[t.ins_h],t.head[t.ins_h]=t.strstart);while(0!==--t.prev_length);if(t.match_available=0,t.match_length=ot-1,t.strstart++,n&&(h(t,!1),0===t.strm.avail_out))return vt}else if(t.match_available){if(n=U._tr_tally(t,0,t.window[t.strstart-1]),n&&h(t,!1),t.strstart++,t.lookahead--,0===t.strm.avail_out)return vt}else t.match_available=1,t.strstart++,t.lookahead--}return t.match_available&&(n=U._tr_tally(t,0,t.window[t.strstart-1]),t.match_available=0),t.insert=t.strstart<ot-1?t.strstart:ot-1,e===N?(h(t,!0),0===t.strm.avail_out?kt:zt):t.last_lit&&(h(t,!1),0===t.strm.avail_out)?vt:yt}function g(t,e){for(var a,n,r,i,s=t.window;;){if(t.lookahead<=_t){if(u(t),t.lookahead<=_t&&e===q)return vt;if(0===t.lookahead)break}if(t.match_length=0,t.lookahead>=ot&&t.strstart>0&&(r=t.strstart-1,n=s[r],n===s[++r]&&n===s[++r]&&n===s[++r])){i=t.strstart+_t;do;while(n===s[++r]&&n===s[++r]&&n===s[++r]&&n===s[++r]&&n===s[++r]&&n===s[++r]&&n===s[++r]&&n===s[++r]&&i>r);t.match_length=_t-(i-r),t.match_length>t.lookahead&&(t.match_length=t.lookahead)}if(t.match_length>=ot?(a=U._tr_tally(t,1,t.match_length-ot),t.lookahead-=t.match_length,t.strstart+=t.match_length,t.match_length=0):(a=U._tr_tally(t,0,t.window[t.strstart]),t.lookahead--,t.strstart++),a&&(h(t,!1),0===t.strm.avail_out))return vt}return t.insert=0,e===N?(h(t,!0),0===t.strm.avail_out?kt:zt):t.last_lit&&(h(t,!1),0===t.strm.avail_out)?vt:yt}function m(t,e){for(var a;;){if(0===t.lookahead&&(u(t),0===t.lookahead)){if(e===q)return vt;break}if(t.match_length=0,a=U._tr_tally(t,0,t.window[t.strstart]),t.lookahead--,t.strstart++,a&&(h(t,!1),0===t.strm.avail_out))return vt}return t.insert=0,e===N?(h(t,!0),0===t.strm.avail_out?kt:zt):t.last_lit&&(h(t,!1),0===t.strm.avail_out)?vt:yt}function b(t,e,a,n,r){this.good_length=t,this.max_lazy=e,this.nice_length=a,this.max_chain=n,this.func=r}function w(t){t.window_size=2*t.w_size,i(t.head),t.max_lazy_match=E[t.level].max_lazy,t.good_match=E[t.level].good_length,t.nice_match=E[t.level].nice_length,t.max_chain_length=E[t.level].max_chain,t.strstart=0,t.block_start=0,t.lookahead=0,t.insert=0,t.match_length=t.prev_length=ot-1,t.match_available=0,t.ins_h=0}function v(){this.strm=null,this.status=0,this.pending_buf=null,this.pending_buf_size=0,this.pending_out=0,this.pending=0,this.wrap=0,this.gzhead=null,this.gzindex=0,this.method=Z,this.last_flush=-1,this.w_size=0,this.w_bits=0,this.w_mask=0,this.window=null,this.window_size=0,this.prev=null,this.head=null,this.ins_h=0,this.hash_size=0,this.hash_bits=0,this.hash_mask=0,this.hash_shift=0,this.block_start=0,this.match_length=0,this.prev_match=0,this.match_available=0,this.strstart=0,this.match_start=0,this.lookahead=0,this.prev_length=0,this.max_chain_length=0,this.max_lazy_match=0,this.level=0,this.strategy=0,this.good_match=0,this.nice_match=0,this.dyn_ltree=new j.Buf16(2*ht),this.dyn_dtree=new j.Buf16(2*(2*it+1)),this.bl_tree=new j.Buf16(2*(2*st+1)),i(this.dyn_ltree),i(this.dyn_dtree),i(this.bl_tree),this.l_desc=null,this.d_desc=null,this.bl_desc=null,this.bl_count=new j.Buf16(lt+1),this.heap=new j.Buf16(2*rt+1),i(this.heap),this.heap_len=0,this.heap_max=0,this.depth=new j.Buf16(2*rt+1),i(this.depth),this.l_buf=0,this.lit_bufsize=0,this.last_lit=0,this.d_buf=0,this.opt_len=0,this.static_len=0,this.matches=0,this.insert=0,this.bi_buf=0,this.bi_valid=0}function y(t){var e;return t&&t.state?(t.total_in=t.total_out=0,t.data_type=Y,e=t.state,e.pending=0,e.pending_out=0,e.wrap<0&&(e.wrap=-e.wrap),e.status=e.wrap?ft:bt,t.adler=2===e.wrap?0:1,e.last_flush=q,U._tr_init(e),H):n(t,K)}function k(t){var e=y(t);return e===H&&w(t.state),e}function z(t,e){return t&&t.state?2!==t.state.wrap?K:(t.state.gzhead=e,H):K}function x(t,e,a,r,i,s){if(!t)return K;var h=1;if(e===G&&(e=6),0>r?(h=0,r=-r):r>15&&(h=2,r-=16),1>i||i>$||a!==Z||8>r||r>15||0>e||e>9||0>s||s>W)return n(t,K);8===r&&(r=9);var l=new v;return t.state=l,l.strm=t,l.wrap=h,l.gzhead=null,l.w_bits=r,l.w_size=1<<l.w_bits,l.w_mask=l.w_size-1,l.hash_bits=i+7,l.hash_size=1<<l.hash_bits,l.hash_mask=l.hash_size-1,l.hash_shift=~~((l.hash_bits+ot-1)/ot),l.window=new j.Buf8(2*l.w_size),l.head=new j.Buf16(l.hash_size),l.prev=new j.Buf16(l.w_size),l.lit_bufsize=1<<i+6,l.pending_buf_size=4*l.lit_bufsize,l.pending_buf=new j.Buf8(l.pending_buf_size),l.d_buf=l.lit_bufsize>>1,l.l_buf=3*l.lit_bufsize,l.level=e,l.strategy=s,l.method=a,k(t)}function B(t,e){return x(t,e,Z,tt,et,X)}function A(t,e){var a,h,_,d;if(!t||!t.state||e>R||0>e)return t?n(t,K):K;if(h=t.state,!t.output||!t.input&&0!==t.avail_in||h.status===wt&&e!==N)return n(t,0===t.avail_out?P:K);if(h.strm=t,a=h.last_flush,h.last_flush=e,h.status===ft)if(2===h.wrap)t.adler=0,l(h,31),l(h,139),l(h,8),h.gzhead?(l(h,(h.gzhead.text?1:0)+(h.gzhead.hcrc?2:0)+(h.gzhead.extra?4:0)+(h.gzhead.name?8:0)+(h.gzhead.comment?16:0)),l(h,255&h.gzhead.time),l(h,h.gzhead.time>>8&255),l(h,h.gzhead.time>>16&255),l(h,h.gzhead.time>>24&255),l(h,9===h.level?2:h.strategy>=Q||h.level<2?4:0),l(h,255&h.gzhead.os),h.gzhead.extra&&h.gzhead.extra.length&&(l(h,255&h.gzhead.extra.length),l(h,h.gzhead.extra.length>>8&255)),h.gzhead.hcrc&&(t.adler=I(t.adler,h.pending_buf,h.pending,0)),h.gzindex=0,h.status=ct):(l(h,0),l(h,0),l(h,0),l(h,0),l(h,0),l(h,9===h.level?2:h.strategy>=Q||h.level<2?4:0),l(h,xt),h.status=bt);else{var u=Z+(h.w_bits-8<<4)<<8,f=-1;f=h.strategy>=Q||h.level<2?0:h.level<6?1:6===h.level?2:3,u|=f<<6,0!==h.strstart&&(u|=ut),u+=31-u%31,h.status=bt,o(h,u),0!==h.strstart&&(o(h,t.adler>>>16),o(h,65535&t.adler)),t.adler=1}if(h.status===ct)if(h.gzhead.extra){for(_=h.pending;h.gzindex<(65535&h.gzhead.extra.length)&&(h.pending!==h.pending_buf_size||(h.gzhead.hcrc&&h.pending>_&&(t.adler=I(t.adler,h.pending_buf,h.pending-_,_)),s(t),_=h.pending,h.pending!==h.pending_buf_size));)l(h,255&h.gzhead.extra[h.gzindex]),h.gzindex++;h.gzhead.hcrc&&h.pending>_&&(t.adler=I(t.adler,h.pending_buf,h.pending-_,_)),h.gzindex===h.gzhead.extra.length&&(h.gzindex=0,h.status=pt)}else h.status=pt;if(h.status===pt)if(h.gzhead.name){_=h.pending;do{if(h.pending===h.pending_buf_size&&(h.gzhead.hcrc&&h.pending>_&&(t.adler=I(t.adler,h.pending_buf,h.pending-_,_)),s(t),_=h.pending,h.pending===h.pending_buf_size)){d=1;break}d=h.gzindex<h.gzhead.name.length?255&h.gzhead.name.charCodeAt(h.gzindex++):0,l(h,d)}while(0!==d);h.gzhead.hcrc&&h.pending>_&&(t.adler=I(t.adler,h.pending_buf,h.pending-_,_)),0===d&&(h.gzindex=0,h.status=gt)}else h.status=gt;if(h.status===gt)if(h.gzhead.comment){_=h.pending;do{if(h.pending===h.pending_buf_size&&(h.gzhead.hcrc&&h.pending>_&&(t.adler=I(t.adler,h.pending_buf,h.pending-_,_)),s(t),_=h.pending,h.pending===h.pending_buf_size)){d=1;break}d=h.gzindex<h.gzhead.comment.length?255&h.gzhead.comment.charCodeAt(h.gzindex++):0,l(h,d)}while(0!==d);h.gzhead.hcrc&&h.pending>_&&(t.adler=I(t.adler,h.pending_buf,h.pending-_,_)),0===d&&(h.status=mt)}else h.status=mt;if(h.status===mt&&(h.gzhead.hcrc?(h.pending+2>h.pending_buf_size&&s(t),h.pending+2<=h.pending_buf_size&&(l(h,255&t.adler),l(h,t.adler>>8&255),t.adler=0,h.status=bt)):h.status=bt),0!==h.pending){if(s(t),0===t.avail_out)return h.last_flush=-1,H}else if(0===t.avail_in&&r(e)<=r(a)&&e!==N)return n(t,P);if(h.status===wt&&0!==t.avail_in)return n(t,P);if(0!==t.avail_in||0!==h.lookahead||e!==q&&h.status!==wt){var c=h.strategy===Q?m(h,e):h.strategy===V?g(h,e):E[h.level].func(h,e);if(c!==kt&&c!==zt||(h.status=wt),c===vt||c===kt)return 0===t.avail_out&&(h.last_flush=-1),H;if(c===yt&&(e===T?U._tr_align(h):e!==R&&(U._tr_stored_block(h,0,0,!1),e===L&&(i(h.head),0===h.lookahead&&(h.strstart=0,h.block_start=0,h.insert=0))),s(t),0===t.avail_out))return h.last_flush=-1,H}return e!==N?H:h.wrap<=0?F:(2===h.wrap?(l(h,255&t.adler),l(h,t.adler>>8&255),l(h,t.adler>>16&255),l(h,t.adler>>24&255),l(h,255&t.total_in),l(h,t.total_in>>8&255),l(h,t.total_in>>16&255),l(h,t.total_in>>24&255)):(o(h,t.adler>>>16),o(h,65535&t.adler)),s(t),h.wrap>0&&(h.wrap=-h.wrap),0!==h.pending?H:F)}function C(t){var e;return t&&t.state?(e=t.state.status,e!==ft&&e!==ct&&e!==pt&&e!==gt&&e!==mt&&e!==bt&&e!==wt?n(t,K):(t.state=null,e===bt?n(t,M):H)):K}function S(t,e){var a,n,r,s,h,l,o,_,d=e.length;if(!t||!t.state)return K;if(a=t.state,s=a.wrap,2===s||1===s&&a.status!==ft||a.lookahead)return K;for(1===s&&(t.adler=D(t.adler,e,d,0)),a.wrap=0,d>=a.w_size&&(0===s&&(i(a.head),a.strstart=0,a.block_start=0,a.insert=0),_=new j.Buf8(a.w_size),j.arraySet(_,e,d-a.w_size,a.w_size,0),e=_,d=a.w_size),h=t.avail_in,l=t.next_in,o=t.input,t.avail_in=d,t.next_in=0,t.input=e,u(a);a.lookahead>=ot;){n=a.strstart,r=a.lookahead-(ot-1);do a.ins_h=(a.ins_h<<a.hash_shift^a.window[n+ot-1])&a.hash_mask,a.prev[n&a.w_mask]=a.head[a.ins_h],a.head[a.ins_h]=n,n++;while(--r);a.strstart=n,a.lookahead=ot-1,u(a)}return a.strstart+=a.lookahead,a.block_start=a.strstart,a.insert=a.lookahead,a.lookahead=0,a.match_length=a.prev_length=ot-1,a.match_available=0,t.next_in=l,t.input=o,t.avail_in=h,a.wrap=s,H}var E,j=t("../utils/common"),U=t("./trees"),D=t("./adler32"),I=t("./crc32"),O=t("./messages"),q=0,T=1,L=3,N=4,R=5,H=0,F=1,K=-2,M=-3,P=-5,G=-1,J=1,Q=2,V=3,W=4,X=0,Y=2,Z=8,$=9,tt=15,et=8,at=29,nt=256,rt=nt+1+at,it=30,st=19,ht=2*rt+1,lt=15,ot=3,_t=258,dt=_t+ot+1,ut=32,ft=42,ct=69,pt=73,gt=91,mt=103,bt=113,wt=666,vt=1,yt=2,kt=3,zt=4,xt=3;E=[new b(0,0,0,0,f),new b(4,4,8,4,c),new b(4,5,16,8,c),new b(4,6,32,32,c),new b(4,4,16,16,p),new b(8,16,32,32,p),new b(8,16,128,128,p),new b(8,32,128,256,p),new b(32,128,258,1024,p),new b(32,258,258,4096,p)],a.deflateInit=B,a.deflateInit2=x,a.deflateReset=k,a.deflateResetKeep=y,a.deflateSetHeader=z,a.deflate=A,a.deflateEnd=C,a.deflateSetDictionary=S,a.deflateInfo="pako deflate (from Nodeca project)"},{"../utils/common":1,"./adler32":3,"./crc32":4,"./messages":6,"./trees":7}],6:[function(t,e,a){"use strict";e.exports={2:"need dictionary",1:"stream end",0:"","-1":"file error","-2":"stream error","-3":"data error","-4":"insufficient memory","-5":"buffer error","-6":"incompatible version"}},{}],7:[function(t,e,a){"use strict";function n(t){for(var e=t.length;--e>=0;)t[e]=0}function r(t,e,a,n,r){this.static_tree=t,this.extra_bits=e,this.extra_base=a,this.elems=n,this.max_length=r,this.has_stree=t&&t.length}function i(t,e){this.dyn_tree=t,this.max_code=0,this.stat_desc=e}function s(t){return 256>t?lt[t]:lt[256+(t>>>7)]}function h(t,e){t.pending_buf[t.pending++]=255&e,t.pending_buf[t.pending++]=e>>>8&255}function l(t,e,a){t.bi_valid>W-a?(t.bi_buf|=e<<t.bi_valid&65535,h(t,t.bi_buf),t.bi_buf=e>>W-t.bi_valid,t.bi_valid+=a-W):(t.bi_buf|=e<<t.bi_valid&65535,t.bi_valid+=a)}function o(t,e,a){l(t,a[2*e],a[2*e+1])}function _(t,e){var a=0;do a|=1&t,t>>>=1,a<<=1;while(--e>0);return a>>>1}function d(t){16===t.bi_valid?(h(t,t.bi_buf),t.bi_buf=0,t.bi_valid=0):t.bi_valid>=8&&(t.pending_buf[t.pending++]=255&t.bi_buf,t.bi_buf>>=8,t.bi_valid-=8)}function u(t,e){var a,n,r,i,s,h,l=e.dyn_tree,o=e.max_code,_=e.stat_desc.static_tree,d=e.stat_desc.has_stree,u=e.stat_desc.extra_bits,f=e.stat_desc.extra_base,c=e.stat_desc.max_length,p=0;for(i=0;V>=i;i++)t.bl_count[i]=0;for(l[2*t.heap[t.heap_max]+1]=0,a=t.heap_max+1;Q>a;a++)n=t.heap[a],i=l[2*l[2*n+1]+1]+1,i>c&&(i=c,p++),l[2*n+1]=i,n>o||(t.bl_count[i]++,s=0,n>=f&&(s=u[n-f]),h=l[2*n],t.opt_len+=h*(i+s),d&&(t.static_len+=h*(_[2*n+1]+s)));if(0!==p){do{for(i=c-1;0===t.bl_count[i];)i--;t.bl_count[i]--,t.bl_count[i+1]+=2,t.bl_count[c]--,p-=2}while(p>0);for(i=c;0!==i;i--)for(n=t.bl_count[i];0!==n;)r=t.heap[--a],r>o||(l[2*r+1]!==i&&(t.opt_len+=(i-l[2*r+1])*l[2*r],l[2*r+1]=i),n--)}}function f(t,e,a){var n,r,i=new Array(V+1),s=0;for(n=1;V>=n;n++)i[n]=s=s+a[n-1]<<1;for(r=0;e>=r;r++){var h=t[2*r+1];0!==h&&(t[2*r]=_(i[h]++,h))}}function c(){var t,e,a,n,i,s=new Array(V+1);for(a=0,n=0;K-1>n;n++)for(_t[n]=a,t=0;t<1<<et[n];t++)ot[a++]=n;for(ot[a-1]=n,i=0,n=0;16>n;n++)for(dt[n]=i,t=0;t<1<<at[n];t++)lt[i++]=n;for(i>>=7;G>n;n++)for(dt[n]=i<<7,t=0;t<1<<at[n]-7;t++)lt[256+i++]=n;for(e=0;V>=e;e++)s[e]=0;for(t=0;143>=t;)st[2*t+1]=8,t++,s[8]++;for(;255>=t;)st[2*t+1]=9,t++,s[9]++;for(;279>=t;)st[2*t+1]=7,t++,s[7]++;for(;287>=t;)st[2*t+1]=8,t++,s[8]++;for(f(st,P+1,s),t=0;G>t;t++)ht[2*t+1]=5,ht[2*t]=_(t,5);ut=new r(st,et,M+1,P,V),ft=new r(ht,at,0,G,V),ct=new r(new Array(0),nt,0,J,X)}function p(t){var e;for(e=0;P>e;e++)t.dyn_ltree[2*e]=0;for(e=0;G>e;e++)t.dyn_dtree[2*e]=0;for(e=0;J>e;e++)t.bl_tree[2*e]=0;t.dyn_ltree[2*Y]=1,t.opt_len=t.static_len=0,t.last_lit=t.matches=0}function g(t){t.bi_valid>8?h(t,t.bi_buf):t.bi_valid>0&&(t.pending_buf[t.pending++]=t.bi_buf),t.bi_buf=0,t.bi_valid=0}function m(t,e,a,n){g(t),n&&(h(t,a),h(t,~a)),D.arraySet(t.pending_buf,t.window,e,a,t.pending),t.pending+=a}function b(t,e,a,n){var r=2*e,i=2*a;return t[r]<t[i]||t[r]===t[i]&&n[e]<=n[a]}function w(t,e,a){for(var n=t.heap[a],r=a<<1;r<=t.heap_len&&(r<t.heap_len&&b(e,t.heap[r+1],t.heap[r],t.depth)&&r++,!b(e,n,t.heap[r],t.depth));)t.heap[a]=t.heap[r],a=r,r<<=1;t.heap[a]=n}function v(t,e,a){var n,r,i,h,_=0;if(0!==t.last_lit)do n=t.pending_buf[t.d_buf+2*_]<<8|t.pending_buf[t.d_buf+2*_+1],r=t.pending_buf[t.l_buf+_],_++,0===n?o(t,r,e):(i=ot[r],o(t,i+M+1,e),h=et[i],0!==h&&(r-=_t[i],l(t,r,h)),n--,i=s(n),o(t,i,a),h=at[i],0!==h&&(n-=dt[i],l(t,n,h)));while(_<t.last_lit);o(t,Y,e)}function y(t,e){var a,n,r,i=e.dyn_tree,s=e.stat_desc.static_tree,h=e.stat_desc.has_stree,l=e.stat_desc.elems,o=-1;for(t.heap_len=0,t.heap_max=Q,a=0;l>a;a++)0!==i[2*a]?(t.heap[++t.heap_len]=o=a,t.depth[a]=0):i[2*a+1]=0;for(;t.heap_len<2;)r=t.heap[++t.heap_len]=2>o?++o:0,i[2*r]=1,t.depth[r]=0,t.opt_len--,h&&(t.static_len-=s[2*r+1]);for(e.max_code=o,a=t.heap_len>>1;a>=1;a--)w(t,i,a);r=l;do a=t.heap[1],t.heap[1]=t.heap[t.heap_len--],w(t,i,1),n=t.heap[1],t.heap[--t.heap_max]=a,t.heap[--t.heap_max]=n,i[2*r]=i[2*a]+i[2*n],t.depth[r]=(t.depth[a]>=t.depth[n]?t.depth[a]:t.depth[n])+1,i[2*a+1]=i[2*n+1]=r,t.heap[1]=r++,w(t,i,1);while(t.heap_len>=2);t.heap[--t.heap_max]=t.heap[1],u(t,e),f(i,o,t.bl_count)}function k(t,e,a){var n,r,i=-1,s=e[1],h=0,l=7,o=4;for(0===s&&(l=138,o=3),e[2*(a+1)+1]=65535,n=0;a>=n;n++)r=s,s=e[2*(n+1)+1],++h<l&&r===s||(o>h?t.bl_tree[2*r]+=h:0!==r?(r!==i&&t.bl_tree[2*r]++,t.bl_tree[2*Z]++):10>=h?t.bl_tree[2*$]++:t.bl_tree[2*tt]++,h=0,i=r,0===s?(l=138,o=3):r===s?(l=6,o=3):(l=7,o=4))}function z(t,e,a){var n,r,i=-1,s=e[1],h=0,_=7,d=4;for(0===s&&(_=138,d=3),n=0;a>=n;n++)if(r=s,s=e[2*(n+1)+1],!(++h<_&&r===s)){if(d>h){do o(t,r,t.bl_tree);while(0!==--h)}else 0!==r?(r!==i&&(o(t,r,t.bl_tree),h--),o(t,Z,t.bl_tree),l(t,h-3,2)):10>=h?(o(t,$,t.bl_tree),l(t,h-3,3)):(o(t,tt,t.bl_tree),l(t,h-11,7));h=0,i=r,0===s?(_=138,d=3):r===s?(_=6,d=3):(_=7,d=4)}}function x(t){var e;for(k(t,t.dyn_ltree,t.l_desc.max_code),k(t,t.dyn_dtree,t.d_desc.max_code),y(t,t.bl_desc),e=J-1;e>=3&&0===t.bl_tree[2*rt[e]+1];e--);return t.opt_len+=3*(e+1)+5+5+4,e}function B(t,e,a,n){var r;for(l(t,e-257,5),l(t,a-1,5),l(t,n-4,4),r=0;n>r;r++)l(t,t.bl_tree[2*rt[r]+1],3);z(t,t.dyn_ltree,e-1),z(t,t.dyn_dtree,a-1)}function A(t){var e,a=4093624447;for(e=0;31>=e;e++,a>>>=1)if(1&a&&0!==t.dyn_ltree[2*e])return O;if(0!==t.dyn_ltree[18]||0!==t.dyn_ltree[20]||0!==t.dyn_ltree[26])return q;for(e=32;M>e;e++)if(0!==t.dyn_ltree[2*e])return q;return O}function C(t){pt||(c(),pt=!0),t.l_desc=new i(t.dyn_ltree,ut),t.d_desc=new i(t.dyn_dtree,ft),t.bl_desc=new i(t.bl_tree,ct),t.bi_buf=0,t.bi_valid=0,p(t)}function S(t,e,a,n){l(t,(L<<1)+(n?1:0),3),m(t,e,a,!0)}function E(t){l(t,N<<1,3),o(t,Y,st),d(t)}function j(t,e,a,n){var r,i,s=0;t.level>0?(t.strm.data_type===T&&(t.strm.data_type=A(t)),y(t,t.l_desc),y(t,t.d_desc),s=x(t),r=t.opt_len+3+7>>>3,i=t.static_len+3+7>>>3,r>=i&&(r=i)):r=i=a+5,r>=a+4&&-1!==e?S(t,e,a,n):t.strategy===I||i===r?(l(t,(N<<1)+(n?1:0),3),v(t,st,ht)):(l(t,(R<<1)+(n?1:0),3),B(t,t.l_desc.max_code+1,t.d_desc.max_code+1,s+1),v(t,t.dyn_ltree,t.dyn_dtree)),p(t),n&&g(t)}function U(t,e,a){return t.pending_buf[t.d_buf+2*t.last_lit]=e>>>8&255,t.pending_buf[t.d_buf+2*t.last_lit+1]=255&e,t.pending_buf[t.l_buf+t.last_lit]=255&a,t.last_lit++,0===e?t.dyn_ltree[2*a]++:(t.matches++,e--,t.dyn_ltree[2*(ot[a]+M+1)]++,t.dyn_dtree[2*s(e)]++),t.last_lit===t.lit_bufsize-1}var D=t("../utils/common"),I=4,O=0,q=1,T=2,L=0,N=1,R=2,H=3,F=258,K=29,M=256,P=M+1+K,G=30,J=19,Q=2*P+1,V=15,W=16,X=7,Y=256,Z=16,$=17,tt=18,et=[0,0,0,0,0,0,0,0,1,1,1,1,2,2,2,2,3,3,3,3,4,4,4,4,5,5,5,5,0],at=[0,0,0,0,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,11,11,12,12,13,13],nt=[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,2,3,7],rt=[16,17,18,0,8,7,9,6,10,5,11,4,12,3,13,2,14,1,15],it=512,st=new Array(2*(P+2));n(st);var ht=new Array(2*G);n(ht);var lt=new Array(it);n(lt);var ot=new Array(F-H+1);n(ot);var _t=new Array(K);n(_t);var dt=new Array(G);n(dt);var ut,ft,ct,pt=!1;a._tr_init=C,a._tr_stored_block=S,a._tr_flush_block=j,a._tr_tally=U,a._tr_align=E},{"../utils/common":1}],8:[function(t,e,a){"use strict";function n(){this.input=null,this.next_in=0,this.avail_in=0,this.total_in=0,this.output=null,this.next_out=0,this.avail_out=0,this.total_out=0,this.msg="",this.state=null,this.data_type=2,this.adler=0}e.exports=n},{}],"/lib/deflate.js":[function(t,e,a){"use strict";function n(t){if(!(this instanceof n))return new n(t);this.options=l.assign({level:b,method:v,chunkSize:16384,windowBits:15,memLevel:8,strategy:w,to:""},t||{});var e=this.options;e.raw&&e.windowBits>0?e.windowBits=-e.windowBits:e.gzip&&e.windowBits>0&&e.windowBits<16&&(e.windowBits+=16),this.err=0,this.msg="",this.ended=!1,this.chunks=[],this.strm=new d,this.strm.avail_out=0;var a=h.deflateInit2(this.strm,e.level,e.method,e.windowBits,e.memLevel,e.strategy);if(a!==p)throw new Error(_[a]);if(e.header&&h.deflateSetHeader(this.strm,e.header),e.dictionary){var r;if(r="string"==typeof e.dictionary?o.string2buf(e.dictionary):"[object ArrayBuffer]"===u.call(e.dictionary)?new Uint8Array(e.dictionary):e.dictionary,a=h.deflateSetDictionary(this.strm,r),a!==p)throw new Error(_[a]);this._dict_set=!0}}function r(t,e){var a=new n(e);if(a.push(t,!0),a.err)throw a.msg;return a.result}function i(t,e){return e=e||{},e.raw=!0,r(t,e)}function s(t,e){return e=e||{},e.gzip=!0,r(t,e)}var h=t("./zlib/deflate"),l=t("./utils/common"),o=t("./utils/strings"),_=t("./zlib/messages"),d=t("./zlib/zstream"),u=Object.prototype.toString,f=0,c=4,p=0,g=1,m=2,b=-1,w=0,v=8;n.prototype.push=function(t,e){var a,n,r=this.strm,i=this.options.chunkSize;if(this.ended)return!1;n=e===~~e?e:e===!0?c:f,"string"==typeof t?r.input=o.string2buf(t):"[object ArrayBuffer]"===u.call(t)?r.input=new Uint8Array(t):r.input=t,r.next_in=0,r.avail_in=r.input.length;do{if(0===r.avail_out&&(r.output=new l.Buf8(i),r.next_out=0,r.avail_out=i),a=h.deflate(r,n),a!==g&&a!==p)return this.onEnd(a),this.ended=!0,!1;0!==r.avail_out&&(0!==r.avail_in||n!==c&&n!==m)||("string"===this.options.to?this.onData(o.buf2binstring(l.shrinkBuf(r.output,r.next_out))):this.onData(l.shrinkBuf(r.output,r.next_out)))}while((r.avail_in>0||0===r.avail_out)&&a!==g);return n===c?(a=h.deflateEnd(this.strm),this.onEnd(a),this.ended=!0,a===p):n===m?(this.onEnd(p),r.avail_out=0,!0):!0},n.prototype.onData=function(t){this.chunks.push(t)},n.prototype.onEnd=function(t){t===p&&("string"===this.options.to?this.result=this.chunks.join(""):this.result=l.flattenChunks(this.chunks)),this.chunks=[],this.err=t,this.msg=this.strm.msg},a.Deflate=n,a.deflate=r,a.deflateRaw=i,a.gzip=s},{"./utils/common":1,"./utils/strings":2,"./zlib/deflate":5,"./zlib/messages":6,"./zlib/zstream":8}]},{},[])("/lib/deflate.js")});
