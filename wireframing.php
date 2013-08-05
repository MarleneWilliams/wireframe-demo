<?php
/**
 * Any request of form /xyz returns static demo page, ie. /demo/xzy.html
 * Any request of form /abc/xyz returns dynamic/dev content defined in
 *  ./source/xyz.php.  Note that the first path arg ("abc") is
 *  arbitrary: /abc/xyz == /rst/xyz.
 */

define('WF_BASEDIR', __DIR__);

/**
 * Simple data class, basically just namespacing app global vars.
 *
 */
class Wireframing {
  /**
   *  Install subdir, relative to domain root, eg 'subdir/'
   */
  public $url_install_dir;
  public $legend_overlay_config;
  public $q;
  public $css_files;
  /**
   * True if exporting to static/demo file
   */
  public $exporting;
  public $dev;

  public function __construct() {
    $args = explode('?', $_SERVER['REQUEST_URI']);
    $args = array_shift($args);
    $args = explode('/', $args);
    // Dump the first (null) and second (install dir) args
    // TODO: dynamically handle install subdirs.
    array_shift($args); array_shift($args);

    $this->dev = false;
    if(count($args) == 0) {
      $this->invalid_request();

    // Static page request
    } elseif( count($args) == 1 ) {
      $q = array_shift($args);

    // Dev request
    } else {
      $q = array_shift($args);
      $q = array_shift($args);
      $this->dev = true;
    }
    $this->q = $q;
  }

  public function handle_request() {
    if($this->dev) {
      $this->dev_page();
    } else {
      $this->demo_page();
    }
  }

  private function dev_page() {

    $q = $this->q;

    if( ! file_exists('source/pages/' . $q . '.php')) {
      invalid_request();
    }


    // Copy css to demo/stylesheets, and set demo page css references to them (so
    // we don't end up with changes in dev css breaking a demo page.)
    if(@$_GET['export']) {
      $this->exporting = true;
      $css_prefix = uniqid() . '_';
      $css_files = $this->css_files;
      $this->css_files = array();
      foreach($css_files as $subdir => $filename) {
        $this->css_files['demo/stylesheets'] = $css_prefix . $filename;
        $css_contents = file_get_contents(WF_BASEDIR . '/' . $subdir . '/' . $filename);
        print WF_BASEDIR . '/' . $subdir . '/' . $filename;
        file_put_contents('demo/stylesheets/' . $css_prefix . $filename, $css_contents);
      }
    }

    ob_start();
    $this->legend_overlay_config = file_get_contents(WF_BASEDIR . '/source/overlay/' . $q . '.php');
    $this->file_include('components/htmltop.php');
    $this->file_include('pages/' . $q . '.php');
    $this->file_include('components/htmlbtm.php');
    $op = ob_get_clean();
    if(@$_GET['export']) {
      $fn = 'demo/' . $_GET['export'] . '.html';  
      file_put_contents($fn, $op);
      print 'Page exported to ' . $fn;
    } else { 
      print $op;
    }
  }

  private function demo_page() {

    $q = $this->q;
    if( ! file_exists('demo/' .$q . '.html') ) {
      $this->invalid_request();
    } else {
      include __DIR__ . '/demo/' . $q . '.html';
    }
  }


  function invalid_request() {
    die('Not a valid requestx');
  }

  /**
   * 
   * @param $vars array Pass arbitrary data to included file
   *
   */
  function file_include($path, $vars = array()) {
    // find file, allowing for inferred extension
    $base = __DIR__ . '/source/';
    if(file_exists($base . $path)) {
      $path = $base . $path;
    } elseif (file_exists($base . $path . '.php')) {
      $path = $base . $path . '.php';
    } elseif (file_exists($base . $path . '.html')) {
      $path = $base . $path . '.html';
    }

    // make controller available to included file
    $app = $this;
    include $path; 
  }

  function print_css() {

    if($this->exporting) {

      $path = '/' . $this->url_install_dir;
    } else {
      $path = '/' . $this->url_install_dir;
    }


    foreach($this->css_files as $dir_path => $filename) {
      print '<link rel="stylesheet" href="' . $path . $dir_path . '/' . $filename . '"/>' . "\n";
    }
  }

}

$wireframing = new Wireframing();
$wireframing->css_files['stylesheets'] = 'app.css';
$wireframing->url_install_dir = 'wireframing/';
$wireframing->handle_request();




$args = explode('?', $_SERVER['REQUEST_URI']);
$args = array_shift($args);
$args = explode('/', $args);


?>
