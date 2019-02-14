<script type="text/javascript">
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
  ga('create', 'UA-36122219-1', 'www.team.de');
  // ga('create', 'UA-115539165-1', 'auto');
  ga('require', 'ec');

  <?php if ($objPage->GACommands): ?>
  <?php echo implode('', $objPage->GACommands); ?>
  <?php endif; ?>

  ga('set', 'anonymizeIp', true);
</script>

<script>
    <?php if ($objPage->customPageViewUrl): ?>
    ga('send', {
      hitType: 'pageview',
      page: '<?php echo $objPage->customPageViewUrl; ?>'
    });
    <?php else: ?>
    ga('send', 'pageview');
    <?php endif; ?>
</script>