<?php
if (!defined('OC_ADMIN') || OC_ADMIN !== true)
    exit('Access is not allowed.');

if (Params::getParam('plugin_action') != '') {
    if (Params::getParam('plugin_action') == "company") {
        osc_set_preference('taxRate', Params::getParam('taxRate'), 'shop');
        osc_set_preference('logging', Params::getParam('logging'), 'shop');
        $path = rtrim(Params::getParam('logFolder'), '/') . '/';
        osc_set_preference('logFolder', $path, 'shop');
        osc_set_preference('companyName', Params::getParam('companyName'), 'shop');
        osc_set_preference('companyTaxNo', Params::getParam('companyTaxNo'), 'shop');
        osc_set_preference('companyAddress1', Params::getParam('companyAddress1'), 'shop');
        osc_set_preference('companyAddress2', Params::getParam('companyAddress2'), 'shop');
        osc_set_preference('companyAddress3', Params::getParam('companyAddress3'), 'shop');
        osc_set_preference('companyAddress4', Params::getParam('companyAddress4'), 'shop');
        osc_set_preference('companyAddress5', Params::getParam('companyAddress5'), 'shop');
        osc_add_flash_warning_message(__('Settings saved!', 'shop'), 'shop');
    }
    osc_reset_preferences();
}
$taxRate = osc_get_preference('taxRate', 'shop');
$logging = osc_get_preference('logging', 'shop');
$logFolder = osc_get_preference('logFolder', 'shop');
$companyName = osc_get_preference('companyName', 'shop');
$companyTaxNo = osc_get_preference('companyTaxNo', 'shop');
$companyAddress1 = osc_get_preference('companyAddress1', 'shop');
$companyAddress2 = osc_get_preference('companyAddress2', 'shop');
$companyAddress3 = osc_get_preference('companyAddress3', 'shop');
$companyAddress4 = osc_get_preference('companyAddress4', 'shop');
$companyAddress5 = osc_get_preference('companyAddress5', 'shop');
?>
<?php
osc_show_flash_message('shop');
?>

<div id="settings_form" style="border: 1px solid #ccc; background: #eee; ">
    <div style="padding: 20px;">
    <h4><?php _e('Company details', 'shop'); ?></h4>
        <div style="float: left; width: 100%;">
            <form name="propertys_form"  action="<?php echo osc_admin_base_url(true); ?>" method="GET" enctype="multipart/form-data" >
                <input type="hidden" name="page" value="plugins" />
                <input type="hidden" name="action" value="renderplugin" />
                <input type="hidden" name="route"  value="shop-admin-company" />
                <input type="hidden" name="plugin_action" value="company" />
                <fieldset>
                    <div class="form-horizontal">
                        <div class="form-row">
                            <div class="form-label">
                                <?php _e('Tax Rate (inclusive)', 'shop'); ?>
                            </div>
                            <div class="form-controls">
                                <input type="input" name="taxRate" value="<?php echo $taxRate; ?>" >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-label">
                                <?php _e('Company name', 'shop'); ?>
                            </div>
                            <div class="form-controls">
                                <input type="input" name="companyName" value="<?php echo $companyName; ?>" >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-label">
                                <?php _e('Company Tax No', 'shop'); ?>
                            </div>
                            <div class="form-controls">
                                <input type="input" name="companyTaxNo" value="<?php echo $companyTaxNo; ?>" >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-label">
                                <?php _e('Company address', 'shop'); ?>
                            </div>
                            <div class="form-controls">
                                <input type="input" name="companyAddress1" value="<?php echo $companyAddress1; ?>" >
                                <input type="input" name="companyAddress2" value="<?php echo $companyAddress2; ?>" >
                                <input type="input" name="companyAddress3" value="<?php echo $companyAddress3; ?>" >
                                <input type="input" name="companyAddress4" value="<?php echo $companyAddress4; ?>" >
                                <input type="input" name="companyAddress5" value="<?php echo $companyAddress5; ?>" >
                            </div>
                        </div>
                        <div class="form-controls">
                            <button type="submit" class="btn btn-submit"><?php _e('Update', 'shop'); ?></button>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
        <div style="clear: both;"></div>										
    </div>
</div>
