<?php
/**
 * Any request of form /xyz returns static demo page, ie. /demo/xzy.html
 * Any request of form /abc/xyz returns dynamic/dev content defined in
 *  ./source/xyz.php.  Note that the first path arg ("abc") is
 *  arbitrary: /abc/xyz == /rst/xyz.
 */



// error handing and debugging utilities
include_once 'lib/Wiredframe/Vendor/T_ERROR_IZER/t_error_izer.php';


// Bootstrap and config Symfony autoloader
require_once 'lib/Symfony/Component/ClassLoader/UniversalClassLoader.php';
use Symfony\Component\ClassLoader\UniversalClassLoader;
$loader = new UniversalClassLoader();
// register namespaces
$loader->registerNamespaces(array(
  'Wiredframe' => __DIR__ . '/lib',
  #'Monolog' => __DIR__.'/../vendor/monolog/monolog/src',
));
$loader->register();

// Bootstrap app
//   Base configuration (needs to live in app root)
include_once 'app_config.php';

//   Instantiate config object from data in app_config.php
$wiredframe_config = new Wiredframe\App\Config( $wiredframe_config );

//   Instantiate app object, passing in config
$wireframing = new Wiredframe\App\App($wiredframe_config);

//   Clean up global namespace
unset($wiredframe_config);



//  Handle request
$wireframing->handle_request();










?>
