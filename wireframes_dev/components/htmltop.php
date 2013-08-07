<!DOCTYPE html>
<!--[if IE 8]> 				 <html class="no-js lt-ie9" lang="en" > <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en" > <!--<![endif]-->

<head>
	<meta charset="utf-8" />
  <meta name="viewport" content="width=device-width" />

  <title><?php print $title; ?></title>
  
  <?php print $css_links; ?>

  <?php print $app->get_token('wfctrl::jsrefs::/vendor/custom.modernizr.js,/vendor/jquery.js'); ?>


</head>

<body class="<?php print $app->q; ?>">
