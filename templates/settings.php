<?php
/**
 * @package Device detector
 * @author Iurii Makukh
 * @copyright Copyright (c) 2017, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */
?>
<form method="post" class="form-horizontal">
  <input type="hidden" name="token" value="<?php echo $this->prop("token"); ?>">
  <div class="panel panel-default">
    <div class="panel-body">
      <div class="form-group">
        <div class="col-md-12">
          <table class="table table-condensed">
            <thead>
              <tr class="active">
                <th><?php echo $this->text('Store'); ?></th>
                <th><?php echo $this->text('Mobile theme'); ?></th>
                <th><?php echo $this->text('Tablet theme'); ?></th>
              </tr>
            </thead>
            <?php foreach ($stores as $store_id => $store_name) { ?>
            <tr>
              <td class="middle"><?php echo $this->escape($store_name); ?></td>
              <td>
                <select class="form-control" name="settings[theme][<?php echo $this->escape($store_id); ?>][mobile]">
                  <option value=""><?php echo $this->text('- do not switch automatically -'); ?></option>
                  <?php foreach ($themes as $theme_id => $theme) { ?>
                  <option value="<?php echo $this->escape($theme_id); ?>"<?php echo isset($settings['theme'][$store_id]['mobile']) && $settings['theme'][$store_id]['mobile'] == $theme_id ? ' selected' : ''; ?>>
                    <?php echo $this->escape($theme['name']); ?>
                  </option>
                  <?php } ?>
                </select>
              </td>
              <td>
                <select class="form-control" name="settings[theme][<?php echo $this->escape($store_id); ?>][tablet]">
                  <option value=""><?php echo $this->text('- do not switch automatically -'); ?></option>
                  <?php foreach ($themes as $theme_id => $theme) { ?>
                  <option value="<?php echo $this->escape($theme_id); ?>"<?php echo isset($settings['theme'][$store_id]['tablet']) && $settings['theme'][$store_id]['tablet'] == $theme_id ? ' selected' : ''; ?>>
                    <?php echo $this->escape($theme['name']); ?>
                  </option>
                  <?php } ?>
                </select>
              </td>
            </tr>
            <?php } ?>
          </table>
        </div>
      </div>
      <div class="form-group">
        <div class="col-md-4">
          <div class="btn-toolbar">
            <a href="<?php echo $this->url("admin/module/list"); ?>" class="btn btn-default"><?php echo $this->text("Cancel"); ?></a>
            <button class="btn btn-default save" name="save" value="1"><?php echo $this->text("Save"); ?></button>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>