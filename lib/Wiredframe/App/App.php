<?php
namespace Wiredframe\App;
use Wiredframe\App\App as WFApp;

/**
 * 
 *
 */
class App {

  public $q;

  public $plugins;

  public $token_providers;

  public $config;

  private $current_wireframe;



  private $request;
  private $wireframe_model;
  private $fs_helper;


  const  WF_MODE_STATIC = 'static';
  const  WF_MODE_DEV    = 'dev';


  /**
   * True if exporting to static/demo file
   */
  public $exporting;
  public $dev;

  public function __construct( Config $config ) {

    $this->config = $config;
    $this->request = $request = new Request($config);
    $this->fs_helper = new FileSystemHelper($config);

    $this->wireframe_model = new WireframeModel($this);

    $this->fs_helper->resolve_install_path();
    $this->request->resolve_install_path();



    // apply php ini overrides
    foreach($config->get('ini_set') as $key => $val) ini_set($key, $val);

    // initialize  plugins
    $this->plugins = array();
    $plugins = $this->config->get('plugins');
    foreach($plugins as $pkey => $plugin) {
      $this->plugins[$pkey] = $plugin_obj = new $plugin['plugin']();
      $plugin_obj->init($this, $plugin);
    }

  }

  private function initialize() {

    $wf_model = $this->wireframe_model;



  }

  public function handle_request() {

    $resp = $this->router();

    $controller = $resp->controller_factory($this);

    $controller->render();

  }

  private function router() {

    $fs_helper = $this->fs_helper;
    $args = $this->config->get('request_args');

    // "Index" - list of available resources
    if(count($args) == 0) {
      // Return configured Response object
      $resp = new Response( 'Wiredframe\App\IndexController', array());
      return $resp;
      // $t = new IndexController();
      die('need to implement index controller');
      return;
    }
    $arg1 = array_shift($args);

    // routing keys passed in url params
    $export_routing = $this->request->param_exists($this->config->get('exp_key'));
    $export_text = '';
    if($export_routing) {
      $export_text = $this->request->param_value($this->config->get('exp_key'));
    }
    $dev_routing = $export_routing || $this->request->param_exists($this->config->get('dev_key'));

    // Dev request
    if($dev_routing) {

      $this->q = $q = $arg1;

      // check for matching wireframe
      if( ! $wf_file = $this->wireframe_model->wireframe_exists($q, WFApp::WF_MODE_DEV) ) {
        $this->invalid_request('Requested dev wireframe does not exist');
      } else {
        // Return configured Response object
        $export = false;
        if( $this->request->param_exists($this->config->get('exp_key'))) {
          $export = true; #if param has no value (eg ?export), use TRUE
          $export_value = $this->request->param_value($this->config->get('exp_key'));
          if($export_value) $export = $export_value;
        }

        $resp = new Response( 'Wiredframe\App\WireframeController', array(
          'name' => $q,
          'mode' => App::WF_MODE_DEV,
          'wf_key' => $wf_file,
          'export' => $export_routing,
          'export_text' => $export_text,
          'wireframe_model' => $this->wireframe_model,
        ));
        return $resp;
      }

    // Static page request?
    } else {

      $this->q = $q = $arg1;

      // Note: can't assume that because wf doesn't exist that the
      //  request is invalid -- need to allow for future extensions
      $wf_model = $this->wireframe_model;
      if( $wf_model->wireframe_exists($q, WFApp::WF_MODE_STATIC) ) {

        // get revision from request param
        $rev_key = $this->config->get('rev_key');
        $ver = -1;
        if($this->request->param_exists($rev_key)) {
          $ver = $this->request_params[$rev_key];
        } 
        $this->ver = $ver;

        // Return configured Response object
        $resp = new Response( 'Wiredframe\App\WireframeController', array(
          'name' => $q,
          'version' => $ver,
          'mode' => WFApp::WF_MODE_STATIC,
          'wireframe_model' => $this->wireframe_model,
        ));
        return $resp;
      }
    }


    // Other controllers ... ?
    

    $this->invalid_request( 'No controller found for your request' );
  }



  public function invalid_request($msg = '') {
    if($msg) $msg = ': ' . $msg;
    die('Not a valid request' . $msg);
  }

  public function get_token($token) {

    $token = explode('::', $token);
    $plugin_key = array_shift($token);
    $token_key = array_shift($token);
    $vars = array();
    if(count($token) > 0 ) {
      $data = array_shift($token);
      if( ! $vars = json_decode($data, true) ) {
        $vars = array($data);
      }
    }
    $plugins = $this->plugins;
    if(array_key_exists($plugin_key, $plugins)) {
      $plugin = $plugins[$plugin_key];
      return $plugin->get_token($token_key, $vars);
    } elseif(array_key_exists($plugin_key, $this->token_providers)) {
      $plugin = $this->token_providers[$plugin_key];
      return $plugin->get_token($token_key, $vars);

    }

  }


  /**
   * 
   * @param $vars array Pass arbitrary data to included file
   *
   */
  public function file_include($path, $vars = array(), $globals = array()) {
    $this->fs_helper->file_include($path, $this, $vars, $globals);
  }

  public function relative_url_path($origin, $target) {

  }




  /**
   * Provides access to wireframe->print_css()
   * @return [type] [description]
   *
   * @todo : refactor to have source files use $wireframe directly
   */
  public function print_css() {
    $this->current_wireframe->print_css();
  }



  public function set_current_wireframe( WireframeController $wf ) {
    $this->current_wireframe = $wf;
  }
  public function current_wireframe() {
    return $this->current_wireframe;
  }

  public function wireframe_model() {
    return $this->wireframe_model;
  }

  public function fs_helper() {
    return $this->fs_helper;
  }

  public function register_token_provider ($key, $obj) {
    $this->token_providers[$key] = $obj;
  }

}




