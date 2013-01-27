<div class="post-content"><?PHP echo $post_content; ?></div>
</div><!-- .entry-content -->
</div><!-- .site-main -->
</div><!-- .page -->

<script src="<?PHP echo theme_url('js/jquery.details.min.js'); ?>" defer></script>
<script src="<?PHP echo theme_url('js/bootstrap.min.js'); ?>" defer></script>
<script src="<?PHP echo theme_url('js/jquery.dotdotdot-1.5.3.min.js'); ?>" defer></script>
<script src="<?PHP echo theme_url('js/jquery.fittext.min.js'); ?>" defer></script>
<script src="<?PHP echo theme_url('js/jquery.infinitescroll.min.js'); ?>" defer></script>
<script src="<?PHP echo theme_url('js/main.js'); ?>" defer></script>

<!-- BEGIN Google Analytics Tracking Code -->	
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '<?PHP echo $this->config->item('google_analytics_id'); ?>']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<!-- END Google Analytics Tracking Code -->
</body>
</html>