<?php
namespace Wiredframe\App;


class FileSystemHelper {

  private $config;
  /**
   *  Install subdir, relative to domain root, eg 
   *  + app installed at domain.com/subdir/ : $app_install_path = 'subdir'
   *  + app installed at domain.com/ : $app_install_path = ''
   */
  private $app_install_path;
  private $webroot;


  public function __construct( Config $config ) { 
   $this->config = $config;

   // get webroot
    $this->webroot = trim(str_replace('\\', '/', strtolower(trim($_SERVER['DOCUMENT_ROOT']))));
    // normalize basedir
    $basedir = $this->config->get('basedir');
    $basedir = trim(str_replace('\\', '/', strtolower(trim($basedir))));

    $this->config->add_items(array(
      'basedir' => $basedir,
    ));

  }


  public function resolve_install_path() {

    $basedir = $this->config->get('basedir');
    $webroot = $this->config->get('webroot');
    $req = $this->config->get('request_uri');

    // strip off docroot and any wayward path separators
    $basedir = trim(str_replace($webroot , '', $basedir), '/');
    $this->app_install_path = $basedir;

    // strip off install path
    if($this->app_install_path) {
      $req = preg_replace( '/^' . $this->app_install_path . '(\/|)/', '', $req);
    }    

    $this->config->add_items(array(
      'install_path' => $this->app_install_path,
    )); 

  }

  /**
   * 
   * @param $vars array Pass arbitrary data to included file
   *
   */
  function file_include($inc_file_path, App $app, $vars = array(), $globals = array()) {

    static $global_vars = array();
    $global_vars += $globals;

    // find file, allowing for implicit extension
    $cfg = $this->config;
    $base = $cfg->get('basedir');
    $source_root = $cfg->get('source_paths');
    $source_root = $source_root['root'];
    $base .= '/' . $source_root . '/' . $inc_file_path;
    if(file_exists($base)) {
      $inc_file_path = $base;
    } elseif (file_exists($base . '.php')) {
      $inc_file_path = $base . '.php';
    } elseif (file_exists($base . '.html')) {
      $inc_file_path = $base . '.html';
    }



    // @TODO: refactor to use WF obj, not App, in templates

    // Clean up namespace
    unset($cfg, $base, $source_root);

    // Note app controller available to included file as $app
    extract($vars);
    extract($global_vars);
    // process html ("twiggish") template
    if(substr($inc_file_path, -5, 5) == '.html') {
      $tpl = file_get_contents($inc_file_path);
      preg_match_all('/{{(.*)}}/u', $tpl, $matches);
      $replacements = array();
      foreach($matches[0] as $mkey => $val) {
        $token = trim($matches[1][$mkey]);
        $repl = null;


        // token
        if(strstr($token, '::')) {
          // prcess embedded vars
          preg_match_all('/\$\$([a-zA-Z].*)\$\$/u', $token, $varmatches);
          foreach($varmatches[0] as $vmkey => $vmitem) {
            $embvar = trim($varmatches[1][$vmkey]);
            $embvarfind = $varmatches[0][$vmkey];
            $token = str_replace($embvarfind, $$embvar, $token);
    // dbgp($token, 'replaced token');
          }
          $repl = $app->get_token($token);


        // include file
        } elseif( strstr($token, '/')) {

          ob_start();
          $this->file_include($token, $app, $vars);
          $repl = ob_get_clean();

        // variable
        } else {
          $repl = $$token;

       }
        $replacements[] = array(
          'token' => $token,
          'find' => $val,
          'replace' => $repl,
        );
      }
      foreach($replacements as $repl) {
        $tpl = str_replace($repl['find'], $repl['replace'], $tpl); 
      }
// dbgp($inc_file_path,'inc_file_path');
// if($inc_file_path == 'c:/htdocs/tools/wireframing/wireframes_dev/components/htmlbtm.html') {
//   dbgp($matches,'matches');
//   foreach($replacements as &$repla) {$repla['replace'] = htmlspecialchars($repla['replace']);}
//   dbgp($replacements,'replacements');
// }
      print $tpl;
    // process php template
    } else {
      include $inc_file_path; 
    }
  }



  function get_static_directory($dir) {
    $cfg = $this->config;
    $basepath = $cfg->get('basedir');
    $subdir = $cfg->get('static_files_path'); 
    $dir = $basepath . '/' . $subdir . '/' . $dir;
    if( ! file_exists($dir ) ) {
      mkdir( $dir );
    }
    return $dir;
  }

  /**
   * Map relative path to app path 
   * @param  [type] $path [description]
   * @return [type]       [description]
   *
   * EG:
   * If app is installed in domain.com/foo, calling app_file_url_path('bar/baz.html') will
   * return /foo/bar/baz.html.
   */
  public function app_file_url_path($path) {
    return '/' . ($this->app_install_path ? $this->app_install_path . '/' : '') . $path;
  }

  /**
   * Map relative path to app path 
   * @param  [type] $path [description]
   * @return [type]       [description]
   *
   * EG:
   * If app is installed in domain.com/foo, calling app_file_fs_path('bar/baz.html') will
   * return /path/to/webroot/foo/bar/baz.html.
   */
  public function app_file_fs_path($filepath) {
    $path = $this->app_base_path;
    $app_path = $this->app_install_path;
    if($app_path) {
      $path .= '/' . $app_path;
    }
    $path .= '/' . $filepath;
dbgp($path, 'app_file_fs_path');
    return $path;
  }

  /**
   *
   * EG:
   * + If app is installed in domain.com/foo/bar, will
   * return /path/to/webroot.
   * + If app is installed in domain.com, will
   * return /path/to/webroot.
   */
  public function webroot() {
    return $this->webroot;
  }

  /**
   * ie., install subdir relative to webroot
   *
   * EG:
   * + If app is installed in domain.com/foo/bar, will
   * return foo/bar.
   * + If app is installed in domain.com, will
   * return ''.
   */
  public function app_install_path() {
    return $this->app_install_path;
  }
  public function app_install_path2() {
    $path = $this->app_install_path;
    if($path) $path .= '/';
    return $path;
  }

  /**
   * EG:
   * if webroot is 'webroot' and intall_dir is foo, and dev root source/pages, will return
   * '../..'
   */
  public function dev_wf_path_to_app_root() {
    $source_paths = $this->config->get('source_paths');
    $wf_dir = $source_paths['root'] . '/' . $source_paths['pages'];
    // count the path elements
    $pathbits = count(explode('/', $wf_dir));
    return substr(str_repeat('../', $pathbits), 0, -1);

  }

  /**
   *
   * EG:
   * If app is installed in domain.com/foo,  will
   * return /path/to/webroot/foo.
   */
  public function app_base_path() {
    return $this->config->get('basedir');
  }


  // get all files or subdirs of a directory
  public function get_dir_items($path, $type = 'file') {
    $objects = array();
    $items = scandir($path);
    foreach($items as $item) {
      if($item == '.' || $item == '..') continue;
      $itempath = $path . '/' . $item;
      if($type == 'directory') {
        if(!is_dir($itempath)) continue;
      } else {
        if(is_dir($itempath)) continue;
      }
      $objects[] = array(
        'name' => $item,
        'dir' => $path,
        'path' => $itempath,
      );
    }
    return $objects;
  }

}