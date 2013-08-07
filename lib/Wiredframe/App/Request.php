<?php
namespace Wiredframe\App;


class Request {

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

    // get request uri
    $req = $_SERVER['REQUEST_URI'];
    // dump request parameters
    $req = preg_replace('/\?.*$/', '', $req);
    // normalize
    $req = strtolower(trim(trim($req), '/'));

    // parse path into parts
    $args = array();
    if($req) $args = explode('/', $req);

    $config->add_items(array(
      'webroot' => $this->webroot,
      'request_uri' => $req,
      'request_params' => $_GET,
      'request_args' => $args,
    ));


    // $this->get_normalized_install_path();
  }

  public function resolve_install_path() {

    // dump install path from args
    $install_path = $this->config->get('install_path');
    $args = $this->config->get('request_args');
    $install_path = explode('/', $install_path);
    $args = array_diff_assoc($args, $install_path);
    $this->config->replace_items(array(
      'request_args' => $args,
    ));  
  }

  public function param_exists($key) {
    return array_key_exists($key, $this->config->get('request_params'));
  }
  public function param_value($key) {
    $params = $this->config->get('request_params');
    if( array_key_exists($key, $params)) {
      return $params[$key];
    } else {
      trigger_error('Param key ' . $key . ' not set; check using param_exists() before calling param_value()', E_USER_WARNING);
    }
  }

}