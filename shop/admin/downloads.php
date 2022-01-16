<?php if (!defined('OC_ADMIN') || OC_ADMIN !== true) exit('Access is not allowed.');

if (Params::getParam('plugin_action') == 'done') {
    osc_set_preference('testing', Params::getParam('testing'), 'shop');
    osc_set_preference('max_files', Params::getParam('max_files'), 'shop', 'INTEGER');
    osc_set_preference('allowed_ext', Params::getParam('allowed_ext'), 'shop', 'STRING');
    $path = rtrim(Params::getParam('download_path'), '/') . '/';
    osc_set_preference('download_path', $path, 'shop', 'STRING');
    osc_set_preference('maxDownloads', Params::getParam('maxDownloads'), 'shop');
    osc_set_preference('downloadDays', Params::getParam('downloadDays'), 'shop');
    osc_add_flash_warning_message(__('Settings saved!', 'shop'), 'shop');

    osc_reset_preferences();
}
$max_files = osc_get_preference('max_files', 'shop');
$allowed_ext = osc_get_preference('allowed_ext', 'shop');
$download_path = osc_get_preference('download_path', 'shop');
$testing = osc_get_preference('testing', 'shop');
$maxDownloads = osc_get_preference('maxDownloads', 'shop');
$downloadDays = osc_get_preference('downloadDays', 'shop');

osc_show_flash_message('shop');
?>
<div id="settings_form" style="border: 1px solid #ccc; background: #eee; ">
    <div style="padding: 20px;">
    <h4><?php _e('Download options', 'shop'); ?></h4>
        <div style="float: left; width: 100%;">
            <form name="shop_form" id="shop_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
                <input type="hidden" name="page" value="plugins" />
                <input type="hidden" name="action" value="renderplugin" />
                <input type="hidden" name="route" value="shop-admin-downloads" />
                <input type="hidden" name="plugin_action" value="done" />
                <fieldset>
                    <div class="form-horizontal">
                        <div class="form-row">
                            <div class="form-label">
                                <?php _e('Test mode', 'shop'); ?>
                            </div>
                            <div class="form-controls">
                                <input type="checkbox" name="testing" value="1" <?php
                                if ($testing == '1') {
                                    echo 'checked="checked"';
                                }
                                ?>> <?php _e('Only download dummy files', 'shop'); ?>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-label">
                                <?php _e('Downloads folder', 'shop'); ?>
                            </div>
                            <div class="form-controls">
                                <input type="text" name="download_path" id="download_path"  class="input-medium" value="<?php echo $download_path; ?>" >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-label">
                                <?php _e('Maximum files per ad', 'shop'); ?>
                            </div>
                            <div class="form-controls">
                                <input type="text" name="max_files" id="max_files"  class="input-medium" value="<?php echo $max_files; ?>"/>
                                <span class="help-box"><?php echo _e('(0 for unlimited)', 'shop'); ?></span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-label">
                                <?php _e('Allowed filetypes', 'shop'); ?>
                            </div>
                            <div class="form-controls">
                                <input type="text" name="allowed_ext" id="allowed_ext"  class="input-medium" value="<?php echo $allowed_ext; ?>"/>
                                <span class="help-box"><?php echo _e('(separated by comma)', 'shop'); ?></span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-label">
<?php _e('Maximum downloads per order item', 'shop'); ?>
                            </div>
                            <div class="form-controls">
                                <input type="text" name="maxDownloads"  class="input-medium" value="<?php echo $maxDownloads; ?>" >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-label">
<?php _e('Number of days download available', 'shop'); ?>
                            </div>
                            <div class="form-controls">
                                <input type="text" name="downloadDays"  class="input-medium" value="<?php echo $downloadDays; ?>" >
                            </div>
                        </div>
                        <div class="form-row">
                             <div class="form-controls">
                                 <button type="submit" class="btn btn-submit"><?php _e('Update', 'shop'); ?></button>
                             </div>
                         </div>
                    </div>
                </fieldset>
            </form>
        </div>
        <div style="clear: both;"></div>										
    </div>
</div>
