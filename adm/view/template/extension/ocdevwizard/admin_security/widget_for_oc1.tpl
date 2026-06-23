<?php
##====================================================##
## @author    : OCdevWizard                           ##
## @contact   : ocdevwizard@gmail.com                 ##
## @support   : http://help.ocdevwizard.com           ##
## @copyright : (c) OCdevWizard. Admin Security, 2018 ##
##====================================================##
?>
<div class="overview ocdw-form-builder-widget" style="width:100%">
  <div class="dashboard-heading"><a href="<?php echo $link; ?>" style="color:#fff"><?php echo $heading_title; ?></a> (<?php echo $total_0; ?> / <?php echo $total_1; ?>)</div>
  <div class="dashboard-content" style="min-height:auto">
    <?php if ($results) { ?>
      <table>
        <?php foreach ($results as $result) { ?>
          <tr>
            <td><?php echo $result['name']; ?></td>
            <td><?php echo $result['total_0']; ?></td>
          </tr>
        <?php } ?>
      </table>
    <?php } ?>
  </div>
</div>