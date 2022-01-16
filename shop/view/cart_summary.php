<?php
$products = payment_pro_cart_get();
$extra = osc_apply_filter('payment_pro_checkout_extra', array('user' => osc_logged_user_id(), 'email' => osc_logged_user_email()));

Session::newInstance()->_set('shopcart-active', true);
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
