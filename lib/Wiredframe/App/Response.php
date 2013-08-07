<?php
namespace Wiredframe\App;

class Response {
  private $controller;
  private $params;

  public function __construct( $controller, $params = array()) {
    $this->controller = $controller;
    $this->params = $params;
  }

  /**
   * Instantiate and return controller for this response
   * @param  [type] $app [description]
   * @return [type]      [description]
   */
  public function controller_factory(App $app = null) {
    $obj = new $this->controller();
    $obj->init( $app, $this->params );
    return $obj;
  }

}