/*
TODO: 
Extend init/config to allow automatic numbering and nested elements
*/
$(document).ready(function() {

  // Add plugin to jQuery
  $.fn.legendOverlay = {
    active:false,
    toggle: function() {
      this.active = ! this.active;
      elms = $('.meta-overlay, .legend-overlay-notes');
      elms.each(function(index,elm) { $(this).addClass('meta-overlay-hide'); });
      elms.each(function(index,elm) { $(this).removeClass('meta-overlay-show'); });
      //elms.each(function(index,elm) { $(this).hide(); });
      if(this.active) {
        elms.each(function(index,elm) { $(this).removeClass('meta-overlay-hide'); });
        elms.each(function(index,elm) { $(this).addClass('meta-overlay-show'); });
        //elms.each(function(index,elm) { $(this).show(); });
      }
    },
    
    init: function(config) {

      if(config.constructor == Array) {
        var elms = config;
      } else if(config.constructor == Object) {
        if(!config.elms) return;
        var elms = config.elms;
      } else {
        return;
      }
      
      // Add overlay toggle button
      $('body').prepend('<div class="meta-overlay-toggle">LEGEND</div>');
      $('.meta-overlay-toggle').click(function() {
          $().legendOverlay.toggle();
        });
      
      // Add legend notes area 
      $('body').append('<div class="row legend-overlay-notes"><div class="small-12 columns">NOTES<dl class="legend"></dl></div></div>');
      
      // Add legend tags to DOM and notes to notes area
      $.each( elms, function(key, item) {
        var targetElm = $(item.sel);
        if(item.targetParent) {
          var targetElm = targetElm.parent();
        }
        var idx = item.idx;
        var overlayElm = $('<span class="meta-overlay meta-overlay-' + idx + '">' + idx + '</span>');
        targetElm.prepend(overlayElm);
        if(targetElm.css('position') == 'static') {
          targetElm.css('position', 'relative');
        }
        if(item.parentStyles) {
          $.each(item.parentStyles, function(key, item) {
            targetElm.css(key, item);
          });
        }
        // add 'absolute' to element to avoid selector specificity issues
        $.extend(true, item, {styles:{position:'absolute'}});
        $.each(item.styles, function(key, item) {
          $(overlayElm).css(key, item);
        });
        //$(overlayElm).css('display', 'inline-block');

        // Notes
        $('.legend-overlay-notes dl.legend').append('<dt>' + idx + '</dt><dd>' + item.notes + '</dd></dl>');
      
      });
      
      $().legendOverlay.toggle();
      $().legendOverlay.toggle();

    }

  };

});



/*  ----  Breakpoint event handling  ----
$(document).ready(function() {
  $.fn.legendOverlay = {
    dateObj: new Date(),
    lastTime: 0,
    throttle: function() {
      var ms = self.dateObj.getMilliseconds();
      if(ms > self.lastTime) {
        self.lastTime = ms;
        return true;
      }
      return false;
    } 
  };


  $(window).resize(function(event) {
    console.log('dddddd');
    //if(legendOverlay
    //now = 
  });

});
*/




