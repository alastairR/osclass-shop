<?php
$aUser      = User::newInstance()->findByPrimaryKey(osc_logged_user_id());
$aCountries = Country::newInstance()->listAll();
$aRegions   = array();
if ($aUser['fk_c_country_code'] != '') {
    $aRegions = Region::newInstance()->findByCountry($aUser['fk_c_country_code']);
} elseif (count($aCountries) > 0) {
    $aRegions = Region::newInstance()->findByCountry($aCountries[0]['pk_c_code']);
}
$aCities = array();
if ($aUser['fk_i_region_id'] != '') {
    $aCities = City::newInstance()->findByRegion($aUser['fk_i_region_id']);
} elseif (count($aRegions) > 0) {
    $aCities = City::newInstance()->findByRegion($aRegions[0]['pk_i_id']);
}

//calling the view...
View::newInstance()->_exportVariableToView('user', $aUser);
View::newInstance()->_exportVariableToView('countries', $aCountries);
View::newInstance()->_exportVariableToView('regions', $aRegions);
View::newInstance()->_exportVariableToView('cities', $aCities);
View::newInstance()->_exportVariableToView('locales', OSCLocale::newInstance()->listAllEnabled());

$addresses = ShopAddresses::newInstance()->getUserAddresses(osc_logged_user_id());
$no_address = count($addresses) == 0 && empty($aUser['s_address']) ? true : false;
?>
<div class="user-addresses">
    <a rel="nofollow" href="javascript:;" data-toggle="modal" data-target="#modalAddress" data-code="<?php echo $aUser['s_secret']; ?>" data-id="<?php echo $aUser['pk_i_id']; ?>" class="address_update float-right"><i class="fa fa-plus" ></i> Add address</a>
    <h2>
        <?php _e('Address Book', 'shop'); ?>
    </h2>
        <div class="address-empty" style="<?php echo ($no_address ? "" : "display: none;"); ?>">
            <h4><?php _e('You have no alternate addresses to display.', 'shop'); ?>
        </div>
        <div class="address-list" style="<?php echo ($no_address ? "display: none;" : ""); ?>">
            <div class="row">
                <?php if (isset($aUser['s_address'])) { ?>
                    <div class="col-md-3">
                        <?php _e('Default (profile)'); ?>
                    </div>
                    <div class="col-md-7">
                        <?php echo ShopAddresses::newInstance()->format($aUser); ?>
                    </div>
                    <div class="col-md-1">
                    </div>
                </div>
                <?php } ?>
                <?php foreach ($addresses as $addr) { 
                    $address = array('typ'=>$addr['s_type'], 
                        'nam'=>$addr['s_name'], 'com'=>@$addr['s_company'],
                        'cou'=>$addr['s_country'], 'add'=>$addr['s_address'], 'sub'=>$addr['s_city_area'], 
                        'cit'=>$addr['s_city'], 'sta'=>$addr['s_region'], 'pos'=>$addr['s_zip']);

                    $json = json_encode($address);
                    
                    ?>
                    <div class="row address_row">
                        <div class="col-md-3">
                            <?php echo $addr['s_type']; ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo ShopAddresses::newInstance()->format($addr); ?>                    
                        </div>
                        <div class="col-md-2">
                            <a rel="nofollow" href="javascript:;" class="address_update" data-toggle="modal" data-target="#modalAddress"
                               data-aid="<?php echo $addr['pk_i_id']; ?>" data-id="<?php echo $aUser['pk_i_id']; ?>" data-code="<?php echo $aUser['s_secret']; ?>" data-addr="<?php echo htmlspecialchars($json, ENT_QUOTES, 'UTF-8'); ?>" title="<?php _e('Edit address'); ?>">
                                <i class="fa fa-pencil-square-o"></i></a>                    
                            <a href="javascript://" class="address_delete" data-aid="<?php echo $addr['pk_i_id']; ?>" data-id="<?php echo $aUser['pk_i_id']; ?>" data-code="<?php echo $aUser['s_secret']; ?>" title="<?php _e('Delete address'); ?>">
                                <i class="fa fa-times-circle-o"></i></a>                    
                        </div>
                    </div>
                <?php } ?>
        </div>
    </div>
</div>
