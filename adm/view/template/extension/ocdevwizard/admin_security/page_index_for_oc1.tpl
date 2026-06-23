<?php
##====================================================##
## @author    : OCdevWizard                           ##
## @contact   : ocdevwizard@gmail.com                 ##
## @support   : http://help.ocdevwizard.com           ##
## @copyright : (c) OCdevWizard. Admin Security, 2018 ##
##====================================================##
?>
<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8" />
<meta name="robots" content="noindex, nofollow">
<title><?php echo $title; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
<script src="https://code.jquery.com/jquery-2.1.1.min.js" integrity="sha256-h0cGsrExGgcZtSZ/fRz4AwV+Nn6Urh/3v3jFRQ0w9dQ=" crossorigin="anonymous"></script>
<?php foreach ($styles as $style) { ?>
<link type="text/css" href="<?php echo $style['href']; ?>" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>
<?php foreach ($scripts as $script) { ?>
<script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php } ?>
</head>
<body>
<div id="main">
  <div class="inner">
    <h1><?php echo $heading_title; ?></h1>
    <?php if ($access_type == 1) { ?>
    <?php if ($error_pattern_code) { ?>
    <div class="error-text"><?php echo $error_pattern_code; ?></div>
    <?php } ?>
    <?php if ($error_pattern_attempts) { ?>
    <div class="error-text"><?php echo $error_pattern_attempts; ?></div>
    <?php } ?>
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      <input type="hidden" name="pattern" value="<?php echo $pattern; ?>"/>
      <?php if ($redirect) { ?>
      <input type="hidden" name="redirect" value="<?php echo $redirect; ?>"/>
      <?php } ?>
    </form>
    <div id="pattern-block"></div>
    <?php if ($description && $show_description) { ?>
    <div class="additional-information"><?php echo $description; ?></div>
    <?php } ?>
    <?php } ?>
  </div>
</div>
<?php if ($access_type == 1) { ?>
<script>
  $(function() {
    var lock = new PatternLock("#pattern-block", {
      matrix: [<?php echo $pattern_size; ?>,<?php echo $pattern_size; ?>],
      margin: 10,
      radius: 20,
      delimiter: ',',
      onDraw:function(pattern) {
        $("input[name='pattern']").val(pattern);
        $("#form").submit();
      }
    });
  });
</script>
<?php } ?>
<style>
body {<?php if ($page_background_type == 1) { ?>background: url("<?php echo $config_store_url; ?>image/catalog/ocdevwizard/<?php echo $_name; ?>/background/<?php echo $style_background; ?>")<?php } else if ($page_background_type == 2) { ?>background: <?php echo $style_color; ?><?php } ?>}
#main .inner{background:<?php echo $panel_style_color; ?> }
#main .inner .error-text{color:<?php echo $panel_style_error_text_color; ?> }
</style>
</body>
</html>