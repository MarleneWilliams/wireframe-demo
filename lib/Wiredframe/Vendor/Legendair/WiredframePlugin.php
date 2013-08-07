<?php
namespace Wiredframe\Vendor\Legendair;


class WiredframePlugin implements \Wiredframe\App\PluginInterface {
  private $wf_app;
  private $config;

  public function init(\Wiredframe\App\App $wf_app, array $config) {
    $this->wf_app = $wf_app;
    $this->config = $config;
  }


  public function get_token($token, $vars = array()) {
    $fxn = $token . '_token';
    if(method_exists($this, $fxn)) {
      return $this->$fxn($vars);
    }
  }

  private function css_token($vars = array()) {
    $config = $this->config;
    $op = '<link rel="stylesheet" href="' . $config['css_path']  . '"/>';
    return $op;
  }

  private function js_token($vars) {

    $config = $this->config;
    $wf_app = $this->wf_app;
    $fs_helper = $wf_app->fs_helper();

    $jspath = $this->scripts_path();
    $app_base_path = $fs_helper->app_base_path();
    $src_path = $wf_app->config->get('source_paths');
    $src_path = $app_base_path . '/' . $src_path['root'];
    $src_path .= '/' . $config['config_dir'] . '/';
    $src_path .= $vars['key'] . '.php';

    if(!file_exists($src_path)) return '';

    // app configuration
    $ui_config = $config['ui_config'];

    // load page elements configuration
    $page_config = file_get_contents($src_path);

    $js_config = $ui_config . ",\n" . $page_config;

    return '
<script src="' . $jspath . '"></script>
<script>
  $(function() {
    var config = {
      ' . $js_config . '
    };
    
    $().legendOverlay.init(config); });
</script>';
  }

  private function scripts_path() {
    $cfg = $this->wf_app->config;
    $wf = $this->wf_app->current_wireframe();
    return $this->config['js_path'];
  }
}