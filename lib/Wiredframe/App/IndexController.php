<?php
namespace Wiredframe\App;

class IndexController implements ResponseControllerInterface {

  private $app;
  private $wireframe_model;
  private $wireframes_view;

  public function init(App $app, $data = null) {
    $this->app = $app;
    $this->wireframe_model = $app->wireframe_model();
    $this->wireframes_view = new WireframesView($app);
  }
  public function render() {
    $wf_model = $this->app->wireframe_model();
    $wfs_view = $this->wireframes_view;
    print $wfs_view->index_page();



  }

}
