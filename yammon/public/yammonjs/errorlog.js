(function( window , logUrl ){

    /* ====================================== */
    var detectPlugin = function (substrs) {

        if (navigator.plugins) {
            for (var i = 0; i < navigator.plugins.length; i++) {
                var plugin = navigator.plugins[i];
                var haystack = plugin.name + plugin.description;
                var found = 0;

                for (var j = 0; j < substrs.length; j++) {
                    if (haystack.indexOf(substrs[j]) != -1) {
                        found++;
                    }
                }

                if (found == substrs.length) {
                    return true;
                }
            }
        }

        return false;
    };
    /* ====================================== */
    var detectObject = function(progIds, fns) {
        for (var i = 0; i < progIds.length; i++) {
            try {
                var obj = new ActiveXObject(progIds[i]);

                if (obj) {
                    return fns && fns[i]
                        ? fns[i].call(obj)
                        : true;
                }
            } catch (e) {
                // Ignore
            }
        }

        return false;
    };

    /* ====================================== */
    var _cache = null;
    var detectPlugins = function () {

        if( _cache ){
            return _cache;
        }

        var ret    = [];
        var plugins = {
            java: {
                substrs: [ "Java" ],
                progIds: [ "JavaWebStart.isInstalled" ]
            },
            acrobat: {
                substrs: [ "Adobe", "Acrobat" ],
                progIds: [ "AcroPDF.PDF", "PDF.PDFCtrl.5" ]
            },
            flash: {
                substrs: [ "Shockwave", "Flash" ],
                progIds: [ "ShockwaveFlash.ShockwaveFlash" ]
            },
            director: {
                substrs: [ "Shockwave", "Director" ],
                progIds: [ "SWCtl.SWCtl" ]
            },
            quicktime: {
                substrs: [ "QuickTime" ],
                progIds: [ "QuickTimeCheckObject.QuickTimeCheck" ],
                fns: [ function () { return this.IsQuickTimeAvailable(0); } ]
            },
            real: {
                substrs: [ "RealPlayer" ],
                progIds: [
                    "rmocx.RealPlayer G2 Control",
                    "RealPlayer.RealPlayer(tm) ActiveX Control (32-bit)",
                    "RealVideo.RealVideo(tm) ActiveX Control (32-bit)"
                ]
            },
            mediaplayer: {
                substrs: [ "Windows Media" ],
                progIds: [ "MediaPlayer.MediaPlayer" ]
            },
            silverlight: {
                substrs: [ "Silverlight" ],
                progIds: [ "AgControl.AgControl" ]
            }
        };

        //Detect Plugins
        for( var alias in plugins ){
            var plugin = plugins[alias];
            if (detectPlugin(plugin.substrs) || detectObject(plugin.progIds, plugin.fns)) {
                ret.push( alias );
            }
        }

        //Detect Gears
        if( window.google && google.gears )
            ret.push("Google Gears");

        //Detect Browser Plus
        if( window.BrowserPlus )
            ret.push("Browser Plus");

        return _cache = ret.join(",");

    };

    /* ====================================== */
    var handler = function( msg , source, line ){

        //Create Url
        var url = logUrl
                  + "?msg="        + escape(msg)
                  + "&source="     + escape(source)
                  + "&line="       + escape(line)
                  + "&url="        + escape(document.location.href)
                  + "&ua="         + escape(navigator.userAgent)
                  + '&plugins='    + escape(detectPlugins())
                  + '&rand='       + (Math.random() * 1000);

        //Request
        new Image().src = url;

        //Call Default Error Handler
        return false;
    };

    /* ====================================== */
    var prev_handler = window['onerror'];
    window['onerror'] = function( msg , url, line ){
        var ret = prev_handler ? prev_handler( msg , url, line ) : false;
        ret = ret || handler( msg , url, line );
        return ret;
    };

})( window , '/error/jslog' );
