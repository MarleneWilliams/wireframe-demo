/*

*/




$(document).ready(function() {
  // start up foundation
  $(document).foundation();

  // Add plugin to jQuery
  $.fn.wiredframe = {
    init: function() {

    // load admin menu and add to
    $.ajax({
      url: 'app/templates/wf_mini_menu.html',
      cache: true,
      async: false
    }).done(function( html ) {


      $("body").prepend(html);



    });
      // // Add admin menu to dom
      // menu_html = 
      // '<div class="wf-admin-menu">' +
      // '  <ul>' +
      // '    <li><a href=".">Index</a></li>' +
      // '  </ul>' +
      // '</div>';

      // $('body').prepend(menu_html);



    }
  };

  // start up wiredframe plugin
  $().wiredframe.init(); 

});
