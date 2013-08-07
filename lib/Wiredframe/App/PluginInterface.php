<?php
namespace Wiredframe\App;

/**
 * 
 *
 */
interface PluginInterface {
  public function init(App $app, array $config);
  public function get_token($token, $vars = array());
}