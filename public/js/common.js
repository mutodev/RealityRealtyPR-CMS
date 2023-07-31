jQuery(function () {

    //Slideshow
    if(jQuery.anythingSlider) {
        var startStopped = (jQuery(".anythingSlider .sticky:first").length == 0) ? false : true;

        jQuery('.anythingSlider').anythingSlider({
            easing: "linear",           // Anything other than "linear" or "swing" requires the easing plugin
            autoPlay: true,             // This turns off the entire FUNCTIONALY, not just if it starts running or not.
            delay: 4000,                // How long between slide transitions in AutoPlay mode
            startStopped: false, // If autoPlay is on, this can force it to start stopped
            animationTime: 600,         // How long the slide transition takes
            hashTags: true,             // Should links change the hashtag in the URL?
            buildNavigation: false,     // If true, builds and list of anchor links to link to each slide
            pauseOnHover: true,         // If true, and autoPlay is enabled, the show will pause on hover
            startText: "Go",            // Start text
            stopText: "Stop"            // Stop text
        });

        //Slideshow custom pager
        //jQuery("#top-content ul.menu a").click(function(){
        //    var page = parseInt( jQuery(this).attr('rel') );
        //    jQuery('.anythingSlider').anythingSlider( page );
        //
        //    return false;
        //});

    }

    //Default fields values
    //jQuery(".useDefault").addDefaultText();


    jQuery('#mainContactForm').bind('submit.contactForm', function() {
        sendContactForm('footer' , jQuery(this) );
        return false;
    });


    //Sidebar Sub Menus
    jQuery("#sidebar-menu .subtitle > a").click(function(){
        jQuery(this).toggleClass("expanded");
        jQuery(this).next('ul').toggle('slow');
            return false;
    });

    //Holyday Flash
    //jQuery("#holiday-flash").flashembed( jQuery("#holiday-flash").html() );


    //Show emails
    protectEmails();

});


function sendContactForm( from , formElement ) {
    var data = {};

    // Prevent Spam
    if ( formElement.find("[name='email']").val() != '' )
        return;

    jQuery.each( formElement.serializeArray(), function(index,value) {
        data[value.name] = value.value;
    });

    data['from']  = from;

    jQuery.ajax({
       type:       "POST",
       url:        "/contactenos",
       data:       data,
       beforeSend: function(xhr) { xhr.setRequestHeader("X-AJAX-REQUEST", "CONTACT_FORM") },
       success:    function(response) {

           if ( response == 'done' ) {
               alert( 'Su mensaje ha sido enviado' );
               formElement.find(".contactField").val('');
               return;
           }

           var errors = '';
           var resp   = jQuery.evalJSON(response);
           for( var i = 0; i < resp.length; i++ ) {
               errors += resp[i] + "<br />";
           }

       }
     });

}

function protectEmails( ) {
   jQuery(".emailJS").each(function() {

       var result = '';
       var email  = jQuery(this).html();
       var length = email.length;

       for( var i = 0; i < length; i++ ) {
          var letter      = String.fromCharCode( email.charCodeAt(i) - 1 );
          var falseLetter = String.fromCharCode(97 + Math.round(Math.random() * 25));
           result += "<span>" + letter + "</span>\n<span class='do'>" + falseLetter + "</span>\n";
       }

       jQuery(this).html(result);

   });
}

/* Copyright (c) 2009 Michael Manning (actingthemaggot.com) Dual licensed under the MIT (MIT-LICENSE.txt) and GPL (GPL-LICENSE.txt) licenses.*/
(function(A){A.fn.extend({currency:function(B){var C={s:",",d:".",c:2};C=A.extend({},C,B);return this.each(function(){var D=(C.n||A(this).text());D=(typeof D==="number")?D:((/\./.test(D))?parseFloat(D):parseInt(D)),s=D<0?"-":"",i=parseInt(D=Math.abs(+D||0).toFixed(C.c))+"",j=(j=i.length)>3?j%3:0;A(this).text(s+(j?i.substr(0,j)+C.s:"")+i.substr(j).replace(/(\d{3})(?=\d)/g,"$1"+C.s)+(C.c?C.d+Math.abs(D-i).toFixed(C.c).slice(2):""));return this})}})})(jQuery);jQuery.currency=function(){var A=jQuery("<span>").text(arguments[0]).currency(arguments[1]);return A.text()};
