<?php echo $header; ?>
<?php
##====================================================##
## @author    : OCdevWizard                           ##
## @contact   : ocdevwizard@gmail.com                 ##
## @support   : http://help.ocdevwizard.com           ##
## @copyright : (c) OCdevWizard. Admin Security, 2018 ##
##====================================================##
?>
<?php echo $column_left; ?>
<?php echo $column_right; ?>
<div id="content" class="<?php echo $class; ?> <?php echo $_code; ?>-content">
  <?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php $b_i = 1; ?>
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
      <?php echo $breadcrumb['separator']; ?><?php if ($b_i < (count($breadcrumbs))) { ?><a href="<?php echo $breadcrumb['href']; ?>"><span><?php echo $breadcrumb['text']; ?></span></a><?php } else { ?><span><?php echo $breadcrumb['text']; ?></span><?php } ?>
    <?php $b_i++; ?>
    <?php } ?>
  </div>
  <h1><?php echo $heading_title; ?></h1>
	<p><?php echo $text_result_message; ?></p>
  <?php echo $content_bottom; ?>
</div>
<?php echo $footer; ?>