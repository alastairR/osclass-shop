<?php
$products = payment_pro_cart_get();
$empty_shoppingcart = (count($products) == 0);
$extra = osc_apply_filter('payment_pro_checkout_extra', array('user' => osc_logged_user_id(), 'email' => osc_logged_user_email()));

Session::newInstance()->_set('shopcart-active', true);
?>
<?php if ($empty_shoppingcart) { ?>
<div class="user-cart">
    <h2>
        <?php _e('Your cart', 'shopcart'); ?>
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
        <?php _e('Your cart', 'shopcart'); ?>
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
    .payments-ul {
        list-style-type:none;
    }
    .payments-ul li
    {
        display: inline-block;
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
</style>
<div class="user-cart">
    <h2>
        <?php _e('Order Summary', 'shopcart'); ?>
    </h2>
    <div>
        <div class="wrapper box">
            <div class="cart-details ">
                <h3><?php _e('Order Details', 'structechnique'); ?></h3>
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
            <div class="col-md-6">
                <div class="cart-payment ">
                    <h3><?php _e('Addess', 'structechnique'); ?></h3>
                </div>
                <div class="row">
                    <div class="col-md-6">
                    </div>
                    <div class="col-md-6">
                    </div>
                </div>
            </div>
        </div>
        <div class="wrapper box">
            <div class="col-md-6">
                <div class="cart-payment ">
                    <h3><?php _e('Payment Method', 'structechnique'); ?></h3>
                </div>
                <div class="control-group payments-box-wrapper">
                    <p style="font-style: italic"><?php _e('Continue and pay with:', 'payment_pro'); ?></p>
                    <ul class="payments-ul">
                        <?php payment_pro_buttons($products, $extra); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
    <?php } // logged in & cart not empty ?>

<?php } // end logged in ?>
