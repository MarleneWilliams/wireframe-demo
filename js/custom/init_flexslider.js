$(window).load(function(){
  $('#carousel').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: true,
    slideshow: false,
    directionNav: false,
    itemWidth: 133,
    itemMargin: 30,
    keyboard: true,  
    asNavFor: '#slider',
    controlsContainer: $('#slider-controls')
  });

  $('#slider').flexslider({
    animation: "slide",
    pausePlay: true,
    pauseText: 'Pause', 
    controlNav: false,
    pauseOnAction: true,
    pauseOnHover: true,
    animationLoop: true,
    slideshow: true,
    touch: true,
    keyboard: true,  
    sync: "#carousel",
    controlsContainer: $('#slider-controls')
  });
});