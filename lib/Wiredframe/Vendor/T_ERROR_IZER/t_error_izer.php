<?php

ini_set('display_errors', true);
ini_set('display_startup_errors', true);

$t_error_old_err_hander = set_error_handler('t_error_handler', E_ALL);


function t_error_handler ( $errno, $errstr, $errfile, $errline, $errcontext) {
  $data = array(
    'errno' => $errno, 
    'errstr' => $errstr, 
    'errfile' => $errfile, 
    'errline' => $errline, 
    'errcontext' => $errcontext,
  );
  dbgp($data, 'ERROR');
  return true;
}

// function t_exception_hander( $ex ) {
//   // dbgp($ex);
// }
// t_exception_hander('asdf');
// // set_exception_hander('t_exception_hander');
// set_exception_handler(create_function('$e', 'exit("An unknown error occurred");'));


function dbgp($data, $label = '', $print_trace = false ) {
  $is_error = (is_array($data) && array_key_exists('errno', $data));
  
  static $css_injected = false;
  if(!$css_injected) {
    $css = file_get_contents(__DIR__ . '/t_error_izer.css');
    print $css;

    $css_injected = true;
  }


  $trace = debug_backtrace();
  $trace_op = '';
  if($print_trace) {
    $trace_op = '<div class="debug-op-trace">' .
            print_r(debug_backtrace(), true) . '</div>';
  }

  if($data === '') {
    $data = '{null string}';
  } elseif ($data === true) {
    $data = '{TRUE}';
  } elseif ($data === false) {
    $data = '{FALSE}';
  }

  $file = @$trace[0]['file'];
  $line = @$trace[0]['line'];
  $class = '';
  if(array_key_exists(1, $trace) && array_key_exists('class', $trace[1])) {
    $class = @$trace[1]['class'];
  }
  $class = ($class ? $class . '::' : '');

  $function = '';
  if(array_key_exists(1, $trace) && array_key_exists('function', $trace[1])) {
    $function = @$trace[1]['function'];
  }

  $info = '';
  $type_class = array();
  $err_op = '';

  if($is_error) {
    $label = 'Error: ' . $data['errstr'] . '<br/>' . 
      $data['errfile'] . ', Line ' . $data['errline'] ;
    $type_class[] = "error";
    $err_trace = $err_op = '';
    $err_trace = debug_backtrace();
    array_shift($err_trace);
    foreach($err_trace as &$frame) {
      $file = @$frame['file'] ? $frame['file'] : "File??";
      $line = @$frame['line'] ? $frame['line'] : "Line??";
      $err_op .= $file . ': Line ' . $line . '<br/>';
    }
    $err_op = '<div class="debug-op-err-trace"><strong>Call Stack</strong><br/>' . $err_op . '</div>';
  } else {
    if($label) {
      $info = $class . $function . '()<br/>' .
            $file . ', Line ' . $line;
    } else {
      $label = $class . $function . '()';
      $info =  $file . ', Line ' . $line . '<br/>' ;
    }
  }
  $type_class = implode(' ', $type_class);
  if($type_class) $type_class = ' ' . $type_class;

  $label = '<h3>' . $label . '</h3>';
  $info = '<div class="debug-op-info">' . $info . '</div>';
  $data = '<div class="debug-op-data">' . print_r($data, true) . '</div>';

  

  print '<div class="debug-op' . $type_class . '">';
  print $label .  
        $info . 
        $trace_op .
        $data . 
        $err_op . 
        '</div>';
}
