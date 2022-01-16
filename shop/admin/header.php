<?php if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

$route = Params::getParam('route');
?>

    <div class="header-title-market">
    </div>
    <ul class="nav nav-tabs">
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php if($route == 'shop-admin-options'){ echo 'active';} ?>" id="general" 
               href="<?php echo osc_route_admin_url('shop-admin-options'); ?>"><?php _e('General','shop'); ?></a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php if($route == 'shop-admin-company'){ echo 'active';} ?>" id="company" 
               href="<?php echo osc_route_admin_url('shop-admin-company'); ?>"><?php _e('Company','shop'); ?></a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php if($route == 'shop-admin-downloads'){ echo 'active';} ?>" id="downloads" 
               href="<?php echo osc_route_admin_url('shop-admin-downloads'); ?>"><?php _e('Downloads','shop'); ?></a>
        </li>
    </ul>
<style>
    .form-controls input[type=text] {
        display:inline-block !important;
    }
</style>
<?php
