<?php if (!defined('OC_ADMIN') || OC_ADMIN !== true) exit('Access is not allowed.');

if (Params::getParam('plugin_action') == 'done') {
    osc_set_preference('maxAddresses', Params::getParam('maxAddresses'), 'shop');
    osc_set_preference('admincopy', Params::getParam('admincopy'), 'shop');
    osc_set_preference('logging',Params::getParam('logging'),'shop');
    $path = rtrim(Params::getParam('logFolder'), '/') . '/';
    osc_set_preference('logFolder',$path,'shop');
    osc_add_flash_warning_message(__('Settings saved!', 'shop'), 'shop');

    osc_reset_preferences();
}
$maxAddresses = osc_get_preference('maxAddresses', 'shop');
$admincopy = osc_get_preference('admincopy', 'shop');
$logging = osc_get_preference('logging','shop');
$logFolder = osc_get_preference('logFolder','shop');

osc_show_flash_message('shop');
?>
<div id="settings_form" style="border: 1px solid #ccc; background: #eee; ">
    <div style="padding: 20px;">
    <h4><?php _e('General options', 'shop'); ?></h4>
        <div style="float: left; width: 100%;">
        <form name="shop_form" id="shop_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
            <input type="hidden" name="page" value="plugins" />
            <input type="hidden" name="action" value="renderplugin" />
            <input type="hidden" name="route" value="shop-admin-options" />
            <input type="hidden" name="plugin_action" value="done" />
            <fieldset>
            <div class="form-horizontal">
                <div class="tab-pane fade show active" id="general-tab" role="tabpanel" aria-labelledby="general">
                    <div class="form-row">
                        <div class="form-label">
<?php _e('Maximum addresses per customer', 'shop'); ?>
                        </div>
                        <div class="form-controls">
                            <input type="text" name="maxAddresses" value="<?php echo $maxAddresses; ?>" >
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-label">
                            <?php _e('Copy emails to admin', 'shop'); ?>
                        </div>
                        <div class="form-controls">
                            <div class="form-label-checkbox">
                                <input type="checkbox" name="admincopy" value="1" <?php
                                if ($admincopy == '1') {
                                    echo 'checked="checked"';
                                }
                                ?>> <?php _e('BCC to admin', 'shop'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-label">
                            <?php _e('Log folder','shop'); ?>
                        </div>
                        <div class="form-controls">
                            <input type="input" name="logFolder" value="<?php echo $logFolder; ?>" >
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-label">
                            <?php _e('Log process','shop'); ?>
                        </div>
                        <div class="form-controls">
                            <input type="radio" name="logging" value="none" <?php if($logging == 'none' || $logging == ''){ echo 'checked="checked"'; } ?>> <?php _e('None','shop'); ?>
                            <input type="radio" name="logging" value="info" <?php if($logging == 'info'){ echo 'checked="checked"'; } ?>> <?php _e('Info','shop'); ?>
                            <input type="radio" name="logging" value="debug" <?php if($logging == 'debug'){ echo 'checked="checked"'; } ?>> <?php _e('Debug','shop'); ?>
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
