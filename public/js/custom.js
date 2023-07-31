equalheight = function(container){

var currentTallest = 0,
     currentRowStart = 0,
     rowDivs = new Array(),
     $el,
     topPosition = 0;
 $(container).each(function() {

   $el = $(this);
   $($el).height('auto')
   topPostion = $el.position().top;

   if (currentRowStart != topPostion) {
     for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
       rowDivs[currentDiv].height(currentTallest);
     }
     rowDivs.length = 0; // empty the array
     currentRowStart = topPostion;
     currentTallest = $el.height();
     rowDivs.push($el);
   } else {
     rowDivs.push($el);
     currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
  }
   for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
     rowDivs[currentDiv].height(currentTallest);
   }
 });
}

/*
$(window).load(function() {
  equalheight('.same-heigth');
});

$(window).resize(function(){
  equalheight('.same-heigth');
});
*/

jQuery(document).ready(function ($) {

  //$('#checkbox').change(function(){
    setInterval(function () {
        moveRight();
    }, 3000);
  //});

  $(".toggle").on("click", function(e){
    e.preventDefault();
    $($(this).data("target")).toggle();
    return false;
  });

  var slideCount = $('#slider ul li').length;
  var slideWidth = '100%';
  var slideHeight = 850;
  var sliderUlWidth = slideCount * slideWidth;

  $('#slider').css({ width: slideWidth, height: slideHeight });

  $('#slider ul').css({ width: sliderUlWidth, marginLeft: - slideWidth });

    $('#slider ul li:last-child').prependTo('#slider ul');

    function moveLeft() {
        $('#slider ul').animate({
            left: + slideWidth
        }, 'slow', function () {
            $('#slider ul li:last-child').prependTo('#slider ul');
            $('#slider ul').css('left', '');
        });
    };

    function moveRight() {
        $('#slider ul').animate({
            left: - slideWidth
        }, 'slow', function () {
            $('#slider ul li:first-child').appendTo('#slider ul');
            $('#slider ul').css('left', '');
        });
    };

    $('a.control_prev').click(function () {
        moveLeft();
    });

    $('a.control_next').click(function () {
        moveRight();
    });

    $('.property-save').click(function(e) {
      var self = this;

      $.ajax({
        type: "GET",
        url: "/properties/list/save",
        data: {
          property: $(this).data('property')
        },
        success: function() {
          $(self).toggleClass('saved');
          $(self).find('.glyphicon').toggleClass('glyphicon-star');
          $(self).find('.glyphicon').toggleClass('glyphicon-star-empty');
        }
      });

    });
});
