<?php if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

/*
 *      OSCLass – software for creating and publishing online classified
 *                           advertising platforms
 *
 *                        Copyright (C) 2010 OSCLASS
 *
 *       This program is free software: you can redistribute it and/or
 *     modify it under the terms of the GNU Affero General Public License
 *     as published by the Free Software Foundation, either version 3 of
 *            the License, or (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful, but
 *         WITHOUT ANY WARRANTY; without even the implied warranty of
 *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *             GNU Affero General Public License for more details.
 *
 *      You should have received a copy of the GNU Affero General Public
 * License along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
?>

<?php
if (Params::getParam('plugin_action') != '') {
    if (Params::getParam('plugin_action') == "currency_edit") {
        $rate = Params::getParam('currency');
        $symbol = Params::getParam('symbol');
        foreach ($rate as $k => $v) {
            ShopCurrency::newInstance()->updateRate($k, $v, $symbol[$k]);
        }
    }
}

$help = '<p>'
         . __("Add currency on Admin, settings, currencies settings first.")
         . '</p><p>'
         . __("Set default currency on Admin, settings, general settings.")
         . '</p>';

?>
<style>
    .form-controls input[type="text"] {
        display: inline-block !important;
    }
</style>

<div id="settings_form" style="border: 1px solid #ccc; background: #eee; ">
    <div class="row" style="padding: 20px;">
        <div class="col-md-8">
            <form name="rates_form" id="rates_form" action="<?php echo osc_admin_base_url(true); ?>" method="GET" enctype="multipart/form-data" >
                <input type="hidden" name="page" value="plugins" />
                <input type="hidden" name="action" value="renderplugin" />
                <input type="hidden" name="route" value="shop-admin-currency" />
                <input type="hidden" name="section" value="currency" />
                <input type="hidden" name="plugin_action" value="currency_edit" />
                <fieldset>
                    <div class="form-horizontal">
                        <?php
                        $data = ShopCurrency::newInstance()->getAll(true);
                        $base = osc_currency();
                        if (count($data) > 0) {
                            foreach ($data as $curr) {
                                if ($curr['pk_c_code'] == $base) {
                                    $rate = 1000000;
                                } else {
                                    $rate = $curr['i_rate'];
                                }
                                ?>
                                <div class="form-row">
                                    <div class="form-label">
                                        <?php echo $curr['pk_c_code'] == $base ? "Default rate " : ""; ?>
                                        <?php echo $curr['s_description']; ?>
                                    </div>
                                    <div class="form-controls">
                                        <input name="currency[<?php echo $curr['pk_c_code']; ?>]" id="currency[<?php echo $curr['pk_c_code']; ?>]" type="text" value="<?php echo ShopCurrency::newInstance()->formatRate($rate); ?>" <?php echo $curr['pk_c_code'] == $base ? "readonly" : ""; ?>/>
                                        <?php _e('Symbol','shop'); ?>
                                        <input name="symbol[<?php echo $curr['pk_c_code']; ?>]" id="symbol[<?php echo $curr['pk_c_code']; ?>]" type="text" value="<?php echo $curr['s_symbol']; ?>" />
                                    </div>
                                </div>
                            <?php
                            };
                        };
                        ?>
                        <div class="form-row">
                            <div class="form-controls">
                                <button type="submit" class="btn btn-submit"><?php _e('Edit', 'ue_attributes'); ?></button>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
        <div class="col-md-4">
            <span class="help-box"><?php echo $help ?></span>
        </div>
    </div>
    <div style="clear: both;"></div>										
</div>

