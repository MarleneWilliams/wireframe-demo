<?php
namespace Wiredframe\App;

/**
 * Wireframe (ie., the actual *wireframe*) controller
 *
 */
interface ResponseControllerInterface {
  public function init(App $app, $data);
  public function render();
}