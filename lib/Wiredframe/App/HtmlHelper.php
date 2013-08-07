<?php
namespace Wiredframe\App;

class HtmlHelper {

  static function theme_links_list($items) {
    $op = '<ul>';
    foreach($items as $item) {
      $op .= '<li><a href="' . $item['url'] . '">' . $item['label'] . '</a></li>';
    }
    $op .= '</ul>';
    return $op;

  }

  static function theme_admin_page($content) {
    $op = '
    <html>
    <head>
    </head>
    <body>' .
      $content . '
    </body>
    </html>
    ';
    return $op;

  }
}