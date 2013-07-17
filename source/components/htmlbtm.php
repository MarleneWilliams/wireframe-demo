<script src="/unc-wireframing/javascripts/vendor/jquery.js"></script>

<script src="/unc-wireframing/javascripts/foundation/foundation.js"></script>
<script src="/unc-wireframing/javascripts/foundation/foundation.orbit.js"></script>
<script src="/unc-wireframing/javascripts/foundation/foundation.topbar.js"></script>
<script>
  $(document).foundation();
</script>



<!-- Load legend overlay plugin -->
<script src="/unc-wireframing/javascripts/vendor/serverlogic.wf-legend.js"></script>
<script>
  $(function() {
    var config = {
      <?php print $app->legend_overlay_config; ?>
    };
    
    $().legendOverlay.init(config); });
</script>

</body>
</html>
