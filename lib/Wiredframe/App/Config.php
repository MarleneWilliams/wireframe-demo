<?php
namespace Wiredframe\App;

/**
 * Container class for holding config data
 *
 * @todo : This is next to worthless; use a 3rd party (Symfony?) config library
 *
 */
class Config {

  /**
   * Known keys:
   *     dev_key      
   *       string; url root of "admin section" of wireframing app 
   *       ('dev' by default)
   *       
   *     lib_path     
   *       path to app/plugin code
   *       Default: 'lib'
   *       
   *     source_paths array
   *       root # eg., 'source'       
   *       pages # ie., for source/pages, 'pages'
   *       components # ie., for source/components, 'components'
   *       blocks # ie., for source/blocks, 'blocks'
   *       
   *     static_files_path  e.g., 'static'
   *     
   *     sass_path    string
   *     
   *     scripts_path string
   *     
   *     css[]        array:
   *             path     string
   *             files[]  array of file names
   *             
   *     basedir      string; filesystem root path (/c/.../{webroot}/app/install/dir)
   *     ini_set[]    array of key/val pairs of valid PHP settings, 
   *                  eg: array('display_errors' => true)
   * 
   */
  private $settings;

  public function __construct($settings = array()) {
    $this->settings = $settings;
  }

  public function get($key) {
    return $this->settings[ $key ];
  }

  /**
   * Allow clients to directly modify config settings
   * @return [type] [description]
   */
  public function &settings_by_ref() {
    return $this->settings;
  }

  public function add_items(array $items) {
    $this->settings = array_replace_recursive($this->settings, $items);
  }

  public function replace_items(array $items) {
    $settings = &$this->settings;
    foreach($items as $key => $val) {
      if(array_key_exists($key, $settings)) {
        unset($settings[$key]);
      }
      $settings[$key] = $val;
    }
  }


}