<?php

/*
  Plugin Name: Shop
  Plugin URI: https://asrosoft.com
  Description: This plugin adds an ecommerce shop
  Version: 1.1.0
  Author: Asrosoft
  Author URI: https://asrosoft.com/
  Author Email: osclass@asrosoft.com
  Short Name: Shop
  Plugin update URI: shop
 */

define('SHOP_VERSION', '110');

require_once('class/ShopCart.php');
require_once('class/ShopCurrency.php');
require_once('class/ShopDownloads.php');
require_once('class/ShopOrders.php');
require_once('class/ShopAddresses.php');
require_once('class/Shop.php');

$shop = new Shop();

// This is needed in order to be able to activate the plugin
osc_register_plugin(osc_plugin_path(__FILE__), array(&$shop, 'hook_after_install'));
// This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', array(&$shop, 'hook_after_uninstall'));
// This is a hack to show a Configure link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(osc_plugin_path(__FILE__) . '_configure', array(&$shop, 'shop_help'));


define('SHOP_ORDER_PENDING', 0);
define('SHOP_ORDER_PAID', 1);
define('SHOP_ORDER_DESPATCHED', 2);
define('SHOP_ORDER_AWAITING_STOCK', 3);
define('SHOP_ORDER_DOWNLOADED', 4);
