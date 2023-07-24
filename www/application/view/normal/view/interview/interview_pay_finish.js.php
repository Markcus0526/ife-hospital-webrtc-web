<script type="text/javascript">
  <?php if ($mInterview->pay_id) { ?>
  $(function() {
    var g_count = 5;
    setInterval(function() {
      g_count --;
      $('#seconds').html(g_count);
      if (g_count == 0) {
        window.close();
        //document.location.href = '<?php echo $redirect_url;?>';
      }
    }, 1000);
  });
  <?php } ?>
</script>