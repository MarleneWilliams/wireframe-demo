<?php
namespace Wiredframe\App;

/**
 * Wireframe (ie., the actual *wireframe*) controller
 *
 */
class WireframeController implements ResponseControllerInterface {

	private $name;
  private $app;
  private $wireframe_model;
  private $mode;
  private $export; 
  private $export_text;

  private $version;
  private $ver_num;



  public function init(App $app, $data) {

    $this->app = $app;
    $this->app->set_current_wireframe($this);
    $this->app->register_token_provider('wfctrl', $this);
    $this->wireframe_model = $data['wireframe_model'];
    $this->name = $data['name'];
    $this->mode = $data['mode'];
    switch ($this->mode) {
      case App::WF_MODE_STATIC:
        $this->version = $data['version'];
        break;
      case App::WF_MODE_DEV:
        $this->file = $data['wf_key'];
        $this->export = $data['export'];
        $this->export_text = $data['export_text'];
        break;
    }

    // // get wireframe-specific config
    // $this->config = arrayarray_intersect_key($this->app->config, array('css'));
;
  } 

  public function render() {
    $wf_model = $this->wireframe_model;
    $app = $this->app;
    switch ($this->mode) {
      case App::WF_MODE_STATIC:
        $wfs = $wf_model->get_all_static_wireframes($this->name);
        $req_ver = $this->version == -1 ? $wfs['cur_ver_num'] : $this->version;
        if(!array_key_exists($req_ver, $wfs['version_map'])) {
          $app->invalid_request('Bad version ID');
        }
        $ver_key = $wfs['version_map'][$req_ver];
        $version_def = $wfs['versions'][$ver_key];
        include $version_def['path'];
        break;
      case App::WF_MODE_DEV:
        if($this->export) {
          $this->export_wireframe();
        } else {
          $this->render_wireframe();
        }
        break;
    }
  }


  /**
   * Renders dynamic wireframe, returning html as string
   * @param  boolean $return set True to return as HTML; leave
   *                         False to output to browser
   * @return [type] [description]
   */
  private function render_wireframe($return = false) {
    $app = $this->app;

    // prepare content vars
    $page_title = ucwords($app->q); 
    if($this->export) {
      $page_title .= ' (v' . $this->ver_num . ' "' . $this->export_text . '")';
    } else {
      $page_title .= ' --DEV--';
    }
    $page_title .= ' Wireframe';
    $vars = array(
      'js_path' => $this->scripts_path(),
      'title' => $page_title,
      'css_links' => $this->print_css(),
    );
    $global_vars = array(
      'q' => $app->q,
    );

    ob_start(); 
    // $app->file_include('components/htmltop.php', $vars, array('test'=>'xx'));
    $app->file_include('components/htmltop2.html', $vars, $global_vars);
    $app->file_include($this->file, $vars);
    $app->file_include('components/htmlbtm.html', $vars);
    $op = ob_get_clean();
    if($return) return $op;
    print $op;
    return;
  }


  private function export_wireframe() {
    $app = $this->app;
    $cfg = $this->app->config;
    $fs_helper = $app->fs_helper();
    $wf_model = $this->wireframe_model;
    $q = $this->name;
    $webroot = $fs_helper->webroot();

    // Organize static wf's in directories by wf base name
    $export_dir = $fs_helper->get_static_directory($q);

    // Get next file version number
    $this->ver_num = $ver_num = $wf_model->get_next_static_wf_version($q);

    $ver_label = 'v' . str_pad($ver_num, 3, '0', STR_PAD_LEFT);

    // "name_x" user-supplied name suffix/annotation, if there is one
    $name_x = $this->export_text;
    if($name_x) $name_x = '_' . $name_x;

    $export_wf_name = $ver_label . $name_x . '.html';
    $export_wf_path = $export_dir . '/' . $export_wf_name;


    //
    // Copy css to demo/stylesheets, and set demo page css references to them (so
    // we don't end up with changes in dev css breaking a demo page.)
    // 

    // Generate a unique prefix for 'static' css files
    $css_export_prefix = $q . '_' . $ver_label . '_' . uniqid() . '_';

    // Get pathrefs for import and export css
    $css_files_map = $wf_model->css_files($this->mode, $css_export_prefix);

    // Copy/export the source css files
    foreach($css_files_map as &$item) {
      $src_filepath = $webroot . $item['source_file'];
      $exp_filepath = $webroot . $item['export_file'];
      $src_filepath = $item['source_file'];
      $exp_filepath = $item['export_file'];
      $item['contents'] = file_get_contents($src_filepath);
      file_put_contents($exp_filepath, $item['contents']);
    }

    // Render the wireframe using the 'static' css references
    //   Overwrite the css config with the exported filenames
    $settings = &$cfg->settings_by_ref();
    $settings['css']['files'] = array();
    foreach($css_files_map as $item) { 
      $settings['css']['files'][] = $item['export_file_filename'];
    }

    $op = $this->render_wireframe(true);

    $fn = 'demo/' . $_GET['export'] . '.html';  
    file_put_contents($export_wf_path, $op);

    $link_path = $this->static_wireframe_path($q, $ver_num);

    $op = 'Page exported to ' . $export_wf_path . '.';
    $op .= '<br/><a href="' . $link_path . '">View</a>';
    print $op;
    ;

  }
  
  public function scripts_path() {
    $cfg = $this->app->config;
    return $cfg->get('scripts_path');  
  }

  public function css_path() {
    $cfg = $this->app->config->get('css');
    return $cfg['path'];  
  }

  /**
   * Outputs HTML css link elements.
   * @return  string [description]
   */
  public function print_css() {
    $path = $this->css_path();
    $app = $this->app;
    $cfg = $app->config->get('css');
    $op = '';
    foreach($cfg['files'] as $filename) {
      $filepath = $path . '/' . $filename;
      $op .= '<link rel="stylesheet" href="' . $filepath  . '"/>' . "\n";
    }
    return $op;
  }

  public function script_refs($files) {
    $scripts = explode(',', $files);
    $op = '';
    foreach($scripts as $script) {
      $script = trim($script);
      $op .= '<script src="';
      $op .= $this->scripts_path();
      $op .= $script;
      $op .= '"></script>' . "\n";
    }
    return $op;
  }
  public function css_refs($files) {
    $scripts = explode(',', $files);
    $op = '';
    foreach($scripts as $script) {
      $script = trim($script);
      $op .= '<link rel="stylesheet" href="';
      $op .= $this->css_path();
      $op .= $script;
      $op .= '"/>' . "\n";
    }
    return $op;
  }

  public function mode() {
    return $this->mode;
  }

  public function static_wireframe_path($wf_key, $version) {
    return $wf_key . '?ver=' . $version;
  }

  public function get_token($key, $data) {
    switch($key) {
      case 'cssrefs':
        return $this->css_refs( $data[0] ) . "\n";
        break;
      case 'jsrefs':
        return $this->script_refs( $data[0] ) . "\n";
        break;
      case 'js_bottom':
      return '';
        $op = 
        '<script>
          $(function() {
            $().wiredframe.init(); });
        </script>';
        return $op;
        break;

    }

  }
}