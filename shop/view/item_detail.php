<div class="download-files">
<?php
if($item['download']!=null && is_array($item['download']) && count($item['download'])>0) {
    foreach($item['download'] as $_r) { ?>
        <div id="<?php echo $_r['pk_i_id'];?>" fkid="<?php echo $_r['fk_i_item_id'];?>" name="<?php echo $_r['s_name'];?>">
            <?php if ($item['i_price'] == 0) { ?>
            <button class="btn btn-success download"><a target="_blank" href="<?php echo osc_route_url('shop-download', array('id'=>$_r['fk_i_item_id'],'secret'=>'free','code'=>$_r['s_code']));?>" ><?php _e('Download', 'shop'); ?></a></button>
            <?php } else { ?>
                <div class="item-add">
                    <?php echo ShopCart::newInstance()->link($item); ?>
                </div>
            <?php } ?>
            <label><?php echo $_r['s_name']; ?></label>
        </div>
    <?php }
}; ?>
</div>
