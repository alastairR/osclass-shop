<?php
$tx_id = Params::getParam('tx');
$invoice = ModelPaymentPro::newInstance()->invoices(array('tx'=>$tx_id));
$itemIds = array_column($invoice[0]['rows'],'fk_i_item_id');
$digital = ShopDownloads::newInstance()->checkDigital($itemIds, $all = false);

Session::newInstance()->_set('shopcart-active', false);
$downloads = ShopDownloads::newInstance()->getAvailableDownloads(osc_logged_user_id());
?>
<div class="payments-done" style='text-align: center;width: 100%; '>
    <?php if ($digital) { ?>
    <div>
        <p><?php _e('There can be a delay before your download is available whilst the payment processor completes the transaction.', 'shop'); ?></p>
        <p><?php _e('If your purchase is not available, check the Downloads page in "My Account" when the payment transaction is complete.', 'shop'); ?></p>
    </div>

        <?php if (count($downloads)) { ?>
    <div style="margin:2em;font-size: 2em;line-height: 1.2em;">
        <p><?php _e('You have the following downloads available:', 'shop'); ?></p>
    </div>
            <?php foreach($downloads as $download) { ?>
    <div>
        <p><a class="download-link" href="<?php echo osc_route_url('shop-download', array('id'=>$download['fk_i_item_id'],'secret'=>$download['s_secret'],'code'=>'code')); ?>"><?php echo $download['s_name']; ?></a>
    </div>
            <?php } ?>
        <?php } ?>
    <?php } else { ?>
    <div style="margin:2em;font-size: 2em;line-height: 1.2em;">
        <p><?php _e('Payment processed', 'shop'); ?></p>
    </div>
    <?php } ?>
    <div style="margin:4em;line-height: 1.2em;">
        <h4><a class="ui-button ui-button-blacktext" href="<?php echo osc_base_url(); ?>"><?php _e('Continue browsing', 'shop'); ?></a></h4>
    </div>
</div>
<?php osc_run_hook('payment_pro_done_page', Params::getParam('tx'));