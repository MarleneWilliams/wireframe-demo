<script src="<?php print $js_path . '/vendor/jquery.js'; ?>"></script>

<script src="<?php print $js_path . '/foundation/foundation.js'; ?>"></script>
<script src="<?php print $js_path . '/foundation/foundation.orbit.js'; ?>"></script>
<script src="<?php print $js_path . '/foundation/foundation.topbar.js'; ?>"></script>
<script>
  $(document).foundation();
</script>



<!-- Load legend overlay plugin -->
<?php print $app->get_token('legendair::js::{"key":"' . $app->q . '"}'); ?>



</body>
</html>
