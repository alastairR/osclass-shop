<?php
$products = payment_pro_cart_get();
$currencies = ShopCurrency::newInstance()->getAll();
$curr = ShopCurrency::newInstance()->getDefault();
$pay_items = array();
foreach($products as $item) {
    $item['amount'] = $item['amount'] / 1000000;
    $item['symbol'] = $currencies[$curr]['s_description'];
    $item['currency'] = $curr;
    $pay_items[$item['id']] = $item;
}
$addresses = ShopAddresses::newInstance()->getUserAddresses(osc_logged_user_id());
$user = User::newInstance()->findByPrimaryKey(osc_logged_user_id());
$empty_shoppingcart = (count($products) == 0);
$digital_files = ShopCart::newInstance()->allDigital();
$delivery = Session::newInstance()->_get('checkout-delivery');
$billing = Session::newInstance()->_get('checkout-billing');

$extra = osc_apply_filter('payment_pro_checkout_extra', 
                        array('user' => osc_logged_user_id(), 'email' => osc_logged_user_email(),
                            'details'=>array('delivery'=>$delivery, 'billing'=>$billing,
                                            'shipping_method'=>'', 'shipping_amount'=>0)));

Session::newInstance()->_set('shopcart-active', true);
?>
<?php if ($empty_shoppingcart) { ?>
<div class="user-cart">
    <h2>
        <?php _e('Your cart', 'shop'); ?>
    </h2>
    <div>
        <div class="wrapper box">
        <div class="row">
            <div class="cart-empty " <?php echo ($empty_shoppingcart ? "" : 'style="display:none;"'); ?>>
                <h4><?php _e('Your cart is empty. Please use Buy buttons to add items to your cart.', 'structechnique'); ?></h4>
            </div>
        </div>
        </div>
    </div>
</div>
<?php } else { ?>
    <?php if (!osc_is_web_user_logged_in()) { 
// not logged in and cart not empty, so prompt to register/login 
?>
<div class="user-cart">
    <h2>
        <?php _e('Your cart', 'shop'); ?>
    </h2>
    <div class="wrapper box">
        <div class="row">
            <div class="col-md-4">
                <div class="cart_register_block">
                    <h3>New customer</h3>
                    <div class="block">
                        <section>
                    <p>By creating an account, you will be able to shop faster, be up to date on order status, 
                        and keep track of orders you have previously made.</p>
                        </section>
                    </div>
                    <div class="cart_register" style=""><a href="javascript://" class="" >
                            <button class="btn btn-primary btn-block btn-lg"><?php _e('Register', 'shop'); ?></button>
                        </a>
                    </div>
                    <hr>
                </div>
            </div>
            <div class="col-md-4">
                    <div class="">
                        <h3>Returning Customer</h3>
                        <?php osc_current_web_theme_path('user-login-block.php'); ?>
                    </div>
            </div>
            <div class="col-md-4">
                <div class="shop-list" <?php echo ($empty_shoppingcart ? 'style="display:none;"' : ''); ?> id="grid-items" id="items-layout">
                    <div class="shop-item">
                        <div class="wrapper cart-list">
                            <?php echo ShopCart::newInstance()->html(true); ?>
                        </div><!-- structechnique end wrapper -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php } else { 
// logged in and cart not empty, so prompt to complete order and pay
?>
<style type="text/css">
    .checkout-address .form-controls {
        margin-bottom:1rem;
    }
    .payments-ul {
        list-style-type:none;
    }
    .payments-ul li
    {
        display: inline-block;
        margin-right: 2rem;
        margin-bottom: 1rem;
    }
    .payments-preview {
        float:left;
        width: 40%;
    }
    .payments-options {
        float:left;
        width: 60%;
    }
    #bank_info {
        cursor: pointer;
        min-height: 20px;
        margin-bottom: 20px;
        background-color: #f5f5f5;
        border: 1px solid #e3e3e3;
        -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.05);
        box-shadow: inset 0 1px 1px rgba(0,0,0,.05);
        margin: 1em;
        padding: 24px;
        border-radius: 6px;
    }
    .ui-widget-header {
        background: #0b6194;
        color: #fff;
    }
    .ui-widget-overlay.modal-opened{
        background: rgb(0, 0, 0);
        opacity: 0.5;
        filter: Alpha(Opacity=50);         
    }
    .payment .btn {
        font-size: 1.2em;
    }
</style>
<div class="user-cart">
    <h2>
        <?php _e('Order Summary', 'shop'); ?>
    </h2>
    <div>
        <div class="wrapper box">
            <div class="checkout-details ">
                <h3><?php _e('Order Details', 'shop'); ?></h3>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?php echo ShopCart::newInstance()->html(true); ?>                    
                </div>
                <div class="col-md-6">
                </div>
            </div>
        </div>
        <div class="wrapper box">
            <div class="checkout-address ">
                <h3><?php _e('Addess', 'shop'); ?></h3>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-label">
                            <?php _e('E-mail', 'shop'); ?>
                        </div>
                        <div class="form-controls">
                            <?php echo osc_logged_user_email();?>
                        </div>
                        <?php if (!empty($user['s_address'])) { ?>
                        <div class="form-label">
                            <?php _e('Primary', 'shop'); ?>
                        </div>
                        <div class="form-controls">
                            <?php echo ShopAddresses::newInstance()->format($user);?>
                        </div>
                        <?php } ?>
                    </div>
                    <?php if (!$digital_files) { ?>
                    <div class="col-md-4">
                        <div class="form-label">
                            <?php _e('Delivery', 'shop'); ?>
                        </div>
                        <div class="form-controls">
                            <select id="delivery" name="delivery">
                                <option value="PRIMARY" <?php echo $delivery=='PRIMARY' ? 'selected="selected"' : ''; ?>><?php _e('Same as primary','shop');?></option>
                            <?php foreach ($addresses as $address) { ?>
                                <?php if ($address['s_type'] == 'DELIVERY') { ?>
                                <option value="<?php echo $address['pk_i_id'];?>" <?php echo $delivery == $address['pk_i_id'] ? 'selected="selected"' : ''; ?>><?php echo ShopAddresses::newInstance()->format($address);?></option>
                                <?php } ?>
                            <?php } ?>
                            </select>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="col-md-4">
                        <div class="form-label">
                            <?php _e('Billing', 'shop'); ?>
                        </div>
                        <div class="form-controls">
                            <select id="billing" name="billing">
                                <option value="PRIMARY" <?php echo $billing=='PRIMARY' ? 'selected="selected"' : ''; ?>><?php _e('Same as primary','shop');?></option>
                            <?php foreach ($addresses as $address) { ?>
                                <?php if ($address['s_type'] == 'BILLING') { ?>
                                <option value="<?php echo $address['pk_i_id'];?>" <?php echo $billing == $address['fk_i_id'] ? 'selected="selected"' : ''; ?>><?php echo ShopAddresses::newInstance()->format($address);?></option>
                                <?php } ?>
                            <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="wrapper box">
            <div class="col-md-12">
                <div class="checkout-payment ">
                    <h3><?php _e('Payment Method', 'shop'); ?></h3>
                </div>
                <div class="control-group payments-box-wrapper">
                    <p style="font-style: italic"><?php _e('Continue and pay with:', 'shop'); ?></p>
                    <ul class="payments-ul">
                        <?php payment_pro_buttons($pay_items, $extra); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
    <?php } // logged in & cart not empty ?>

<?php } // end logged in ?>
