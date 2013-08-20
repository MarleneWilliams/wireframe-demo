$(window).load(function(){

  $('#slider').flexslider({
    animation: "slide",

    animationLoop: true,
    slideshow: true,
    touch: true,
    slideshowSpeed: 10000,
    animationSpeed: 600, 

    pauseOnAction: true,
    pauseOnHover: true,
    controlNav: true,
    directionNav: true,
    prevText: "Previous",
    nextText: "Next",
    pausePlay: true,
    pauseText: 'Pause',
    playText: 'Play', 
  });
});