<?php
namespace Wiredframe\App;

class WireframeModel {
  private $app;
  private $static_wireframes;
  public $css_files;

  public function __construct(App $app) {
    $this->app = $app;
  }


  function wireframe_exists($file, $mode = WFApp::WF_MODE_STATIC) {
    $cfg = $this->app->config;
    $basepath = $cfg->get('basedir');
    switch ($mode) {
      case App::WF_MODE_STATIC:
        $wfs = $this->get_all_static_wireframes($file);
        return $wfs;
        break;
      case App::WF_MODE_DEV:
        $src_paths = $cfg->get('source_paths');
        $subdir = $src_paths['root'] . '/' . $src_paths['pages'];
        $base = $subdir . '/' . $file;
        $path = false;
        if(file_exists($base)) {
          $path = $base;
        } elseif (file_exists($base . '.php')) {
          $path = $base . '.php';
        } elseif (file_exists($base . '.html')) {
          $path = $base . '.html';
        }  
        // strip the "sources root" path
        if($path) {
          $subdir = str_replace('/', '\\/', $src_paths['root'] . '/');
          $path = preg_replace('/^' . $subdir .  '/', '', $path);
        }
        return $path;                                     
        break;
    }
    return false;
  }


  public function get_dev_wireframes() {
    $app = $this->app;
    $cfg = $this->app->config;
    $fs_helper = $app->fs_helper();
    $pages_path = $cfg->get('source_paths');
    $pages_path = $pages_path['root'] . '/' . $pages_path['pages'];
    $pages_path = $fs_helper->app_base_path() . '/' . $pages_path;
    $wfs = $fs_helper->get_dir_items($pages_path);
    // get 'q'/wf key from filename
    foreach($wfs as &$item) {
      $item['wf_key'] = preg_replace('/\.(html|php)$/', '', $item['name']);
    }
    return $wfs;

  }

  public function get_static_wireframe_versions($q) {
    $wireframes = $this->get_all_static_wireframes();
    return $wireframes[$q]['versions'];
  }

  public function get_next_static_wf_version($q) {
    $wireframes = $this->get_all_static_wireframes();
    return $wireframes[$q]['next_ver_num'];    
  }

  public function get_all_static_wireframes($q = null) { 
    $wfs = $this->load_static_wireframes();
    if($q) {  
      if(array_key_exists($q, $wfs)) {
        return $this->static_wireframes[$q];
      } else {
        return null;
      }
    } else {
      return $this->static_wireframes;
    }

  }

  private function load_static_wireframes() {

    if($this->static_wireframes) {
      return $this->static_wireframes;
    }

    $items = array();
    $cfg = $this->app->config;
    $fs_helper = $this->app->fs_helper();

    $basepath = $fs_helper->app_base_path();
    $export_root_dir = $cfg->get('static_files_path'); 

    // Each subdirectory corresponds to a wireframe.  Each child
    // file is a version of that base wireframe.
    $wireframe_export_dirs = $fs_helper->get_dir_items($basepath . '/' . $export_root_dir, 'directory'); 

    // Compile exported versions for each base wireframe
    foreach($wireframe_export_dirs as $wireframe_dir) {
      $versions = $fs_helper->get_dir_items($wireframe_dir['path'], 'file');
      $max_ver = 0;
      $version_map = array(); # vernum => array idx

      $latest_version_key = null;

      // parse out names and version info
      foreach($versions as $ver_key => &$version) {

        $parseinfo = $this->parse_static_wireframe_name($version['name']);

        $version['base_name'] = $parseinfo['base_name'];
        $vernum = $parseinfo['version_num'];
        $version['version_num'] = $vernum;     
        $version['name_x'] = $parseinfo['name_x'];     
        
        $version_map[$vernum] = $ver_key;

        // identify current version
        if($version['version_num'] > $max_ver) {
          $max_ver = $version['version_num'];
          $latest_version_key = $ver_key;
        }
      }
      // Add versions & data to the parent wf array
      $items[$wireframe_dir['name']] = array(
        'cur_ver_num' => $max_ver,
        'cur_ver_key' => $latest_version_key,
        'next_ver_num' => $max_ver + 1,
        'version_map' => $version_map,
        'versions' => $versions,
      );
    } 

    return $this->static_wireframes = $items; 

  }


  private function parse_static_wireframe_name($name) {
    // remove file ext
    $refname = $basename = preg_replace('/\.html$/', '', $name);
    // extract version
    preg_match_all('/^v([0-9]*)(_(.*)|)$/', $basename, $matches); # (basename).v(###) 


    if($verstr = @$matches[0][0]) {
      $basename = $matches[0][0];
    } else {
      return null; # bad filename
    }

    if($verstr = @$matches[1][0]) {
      $vernum = (int) $matches[1][0];
    } else {
      return null; # bad filename
    }

    $name_x = @$matches[3][0];


    $item = array(
      'name' => $name, 
      'base_name' => $basename,
      'version_num' => $vernum,
      'name_x' => $name_x,
    );

    return $item;
  }


  /**
   * Generates list of css url-relative references.
   * If pass in a prefix, uses it to create derived paths and returns an array
   * mapping derived paths to original ones, eg:
   *   path/to/file.css => path/to/{prefix}file.css
   * @return [type] [description]
   * @Todo: refactor wf-specific config/functionality to Wireframe class
   */
  public function css_files($mode = \Wiredframe\App\App::WF_MODE_STATIC , $prefix = '') {
    $app = $this->app;
    $css_cfg = $app->config->get('css');
    $fs_helper = $app->fs_helper();

    $files = $css_cfg['files'];
    $dirpath = $this->css_path($mode) . '/';
    $dirpath_dev = $this->css_path(\Wiredframe\App\App::WF_MODE_DEV) . '/';
    $dirpath_static = $this->css_path(\Wiredframe\App\App::WF_MODE_STATIC) . '/';
    $files_op = array();
    foreach($files as $config_item_key => $filename) {
      $source_file = $fs_helper->app_file_url_path($dirpath . $filename);
      $source_file = $dirpath . $filename;
      if($prefix) {
        $export_file_filename = $prefix . $filename;
        $export_file = $fs_helper->app_file_url_path($dirpath . $export_file_filename);
        $export_file = $dirpath . $export_file_filename;
        // Use key from css files config array for easy mapping
        $files_op[$config_item_key] = array(
          'source_file' => $source_file,
          'source_file_filename' => $filename,
          'export_file' => $export_file,
          'export_file_filename' => $export_file_filename,
          'config_item_key' => $config_item_key,
          'config_file' => $filename
        );
      } else {
        $files_op[] = $source_file;
      }
    }
    return $files_op;
  }

  public function css_path($mode = \Wiredframe\App\App::WF_MODE_STATIC ) {
    $cfg = $this->app->config->get('css');
    switch($mode) {
      case App::WF_MODE_STATIC:
        return $cfg['path'];
      case App::WF_MODE_DEV: 
        return $cfg['path'];
    }   
  }
}
