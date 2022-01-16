<?php
$maxDownloads = osc_get_preference('maxDownloads', 'shop');
$downloadDays = osc_get_preference('downloadDays', 'shop');
$oldestDate = strtotime('- '.$downloadDays.' days');
$downloads = ShopDownloads::newInstance()->getAvailableDownloads(osc_logged_user_id(),true);
?>
<div class="user-downloads">
    <h2>
        <?php _e('Downloads', 'shop'); ?>
    </h2>
    <div>
        <div class="cart-empty " <?php echo (!count($downloads) ? "" : 'style="display:none;"'); ?>>
            <h4><?php _e('You have no downloads available.','structechnique'); ?>
        </div>
        <div class="shop-list grid compact grid-xl-3-items grid-lg-2-items grid-md-1-items " <?php echo (!count($downloads) ? 'style="display:none;"' : ''); ?> id="grid-items" id="items-layout">
            <div class="shop-item">
                <div class="wrapper cart-list box">
                    <div>
                        <h4><?php _e('You have the following downloads available:', 'shop'); ?></h4>
                    </div>
                        <?php foreach($downloads as $download) { ?>
                            <?php $available = ''; ?>
                    <div style="margin-top: 1.5rem;">
                            <?php if ($download['i_download_count'] >= $maxDownloads ) { ?>
                                <?php $available = __('Already downloaded maximum number of times'); ?>
                            <?php } ?>
                            <?php if (strtotime($download['dt_purchase']) < $oldestDate ) { ?>
                                <?php $available = __('Expired, purchased too long ago'); ?>
                            <?php } ?>
                            <?php if (!empty($available)) { ?>
                        <p><?php echo $download['s_name'] . ' (' . $available . ')'; ?>
                            <?php } else { ?>
                        <p><a class="download-link" href="<?php echo osc_route_url('shop-download', array('id'=>$download['fk_i_item_id'],'secret'=>$download['s_secret'],'code'=>'paid')); ?>"><?php echo $download['s_name']; ?></a>
                            <?php } ?>
                    </div>
                        <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>