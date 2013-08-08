<?php

// See config array documentation in config.class.php
$wiredframe_config = array(
  'dev_key' => 'dev',
  'rev_key' => 'rev',
  'exp_key' => 'export',
  'lib_path' => 'app',
  'source_paths' => array(
    'root' => 'wireframes_dev',
    'pages' => 'pages', # ie., source/pages
    'components' => 'components', # ie., source/components
    // 'blocks' => 'blocks', # ie., source/blocks
  ),
  'static_files_path' => 'wireframes_static',
  'sass_path' => 'scss',
  'scripts_path' => 'js',
  'css' => array(
    'path' => 'css',
    'files' => array(
      // filename
      // @TODO: consider allowing per-file path
      'style.css',
    ),
  ),
  'basedir' => __DIR__,
  // key/val pairs of valid PHP settings, eg: array('display_errors' => true)
  // @TODO:  allow environment-based settings (eg., 'dev', 'default')
  'ini_set' => array(
    'display_errors' => true,
    'display_startup_errors' => true,
  ), 
  'plugins' => array(
    'legendair' => array(
      'plugin' => 'Wiredframe\Vendor\Legendair\WiredframePlugin',
      'lib' => 'Legendair',
      'config_dir' => 'legend', #rel to 'source_paths.root'
      'css_path' => 'css/vendor/legendair.css',
      'js_path' => 'js/vendor/jquery.wf-legend.js',
      'ui_config' => 
        'ui: {
           button_insert_selector: \'.wf-admin-menu\',
           button_insert_method: \'append\',
           button_class: \'no-float\',
        }',
      
    ),
  ),
);
