<?php

if (!defined('ABS_PATH'))
    exit('ABS_PATH is not loaded. Direct access is not allowed.');

class Shop extends DAO {

    public function __construct() {
        parent::__construct();
        $page = Params::getParam('page');
        $route = Params::getParam('route');
        $action = Params::getParam('action');

        osc_add_hook('user_menu', array(&$this, 'user_menu_route'));

        osc_add_route('shop-admin-conf', 'shop/admin/conf', 'shop/admin/conf', osc_plugin_folder(__DIR__) . 'index.php');
        osc_add_route('shop-admin-options', 'shop/admin/options', 'shop/admin/options', osc_plugin_folder(__DIR__) . 'admin/options.php');
        osc_add_route('shop-admin-company', 'shop/admin/company', 'shop/admin/company', osc_plugin_folder(__DIR__) . 'admin/company.php');
        osc_add_route('shop-admin-downloads', 'shop/admin/downloads', 'shop/admin/downloads', osc_plugin_folder(__DIR__) . 'admin/downloads.php');
        osc_add_route('shop-admin-stats', 'shop/admin/stats', 'shop/admin/stats', osc_plugin_folder(__DIR__) . 'admin/stats.php');
        osc_add_route('shop-admin-orders', 'shop/admin/orders/([0-9]+)/([a-zA-Z0-9]+)/([0-9]+)', 'shop/admin/orders/{iPage}/{function}/{id}', osc_plugin_folder(__DIR__) . 'admin/orders.php');
        osc_add_route('shop-admin-currency', 'shop/admin/currency', 'shop/admin/currency', osc_plugin_folder(__DIR__) . 'admin/currency.php');
        osc_add_route('shop-ajax', 'shop/ajax', 'shop/ajax', osc_plugin_folder(__DIR__) . 'view/ajax_shop.php');
        osc_add_route('shop-download', 'download/([([0-9]+)/([([a-zA-Z0-9]+)/([([a-zA-Z0-9]+)', 'download/{id}/{secret}/{code}', osc_plugin_folder(__DIR__) . 'view/download.php');
        osc_add_route('shop-delete', 'user/shop-delete/([0-9]+)', 'user/shop-delete/{delete}', osc_plugin_folder(__DIR__) . 'view/cart_route.php', true, 'custom', 'custom', __('Your cart', 'shop'));

        osc_add_route('shop-checkout', 'shop/checkout', 'shop/checkout', osc_plugin_folder(__DIR__) . 'view/checkout.php', false, 'custom', 'custom', __('Checkout', 'shop'));
        osc_add_route('user-account', 'user/account/?([0-9]+)?', 'user/account/{iPage}', osc_plugin_folder(__DIR__) . 'user/account.php', true, 'custom', 'custom', __('Account', 'shop'));
        osc_add_route('user-downloads', 'user/downloads/?([0-9]+)?', 'user/downloads/{iPage}', osc_plugin_folder(__DIR__) . 'user/downloads.php', true, 'custom', 'custom', __('Downloads', 'shop'));
        osc_add_route('user-addresses', 'user/addresses/?([0-9]+)?', 'user/addresses/{iPage}', osc_plugin_folder(__DIR__) . 'user/addresses.php', true, 'custom', 'custom', __('Address Book', 'shop'));
        osc_add_route('user-invoice-print', 'user/invoice', 'user/invoice', osc_plugin_folder(__DIR__) . 'user/invoice_print.php', true, 'custom', 'custom', __('Print Invoice', 'shop'));

        // custom header
        if ($page == 'plugins') {
            switch ($route) {
                case "shop-admin-options":
                    osc_add_hook('admin_header', array(&$this, 'remove_title_header'));
                    osc_add_hook('admin_page_header', array(&$this, 'customPageHeader_options'));
                    break;
                case "shop-admin-currency":
                    osc_add_hook('admin_header', array(&$this, 'remove_title_header'));
                    osc_add_hook('admin_page_header', array(&$this, 'customPageHeader_currency'));
                    break;
                case "shop-admin-orders":
                    osc_add_hook('admin_header', array(&$this, 'remove_title_header'));
                    osc_add_hook('admin_page_header', array(&$this, 'customPageHeader_orders'));
                    break;
            }
            switch ($action) {
                case "configure":
                    osc_add_hook('admin_header', array(&$this, 'remove_title_header'));
                    osc_add_hook('admin_page_header', array(&$this, 'customPageHeader_conf'));
                    break;
            }
        }

        osc_add_filter('admin_title', array(&$this, 'plugin_title'));
        //osc_add_hook('scripts_loaded', array(&$this, 'scripts_loaded'));
        //osc_add_hook('header_scripts_loaded', array(&$this, 'scripts_loaded'));
        //osc_add_hook('admin_header_scripts_loaded', array(&$this, 'scripts_loaded'));
        //osc_add_hook('admin_scripts_loaded', array(&$this, 'scripts_loaded'));
        osc_add_hook('admin_header', array(&$this, 'scripts_loaded'));
        osc_add_hook('header', array(&$this, 'scripts_loaded'));
        // add javascript
        osc_add_hook('init', function() {
            $page = Params::getParam('page');
            $route = Params::getParam('route');
            $action = Params::getParam('action');
            osc_register_script('shop-js', osc_plugin_url('shop/js/shop.js') . 'shop.js', 'jquery');
            if ($route == 'shop-checkout') {
                osc_enqueue_script('jquery-ui');
                osc_enqueue_style('jquery-ui', osc_assets_url('jquery-ui/jquery-ui.min.css'));
            }
            if ($page != 'items') {
                osc_enqueue_script('shop-js');
            }

            osc_add_route('payment-pro-done', 'paymentpro/done/(.*)', 'paymentpro/done/{tx}', osc_plugin_folder(__DIR__) . 'user/done.php', false, 'custom', 'custom', __('Checkout Complete', 'shop'));
        });

        osc_add_hook('before_validating_login', array(&$this, 'before_validating_login'));
        osc_add_hook('after_login', array(&$this, 'after_login'));
        osc_add_hook('init_main', array(&$this, 'start_page'));
        osc_add_hook('init_page', array(&$this, 'start_page'));
        osc_add_hook('init_custom', array(&$this, 'start_page'));
        osc_add_hook('init_contact', array(&$this, 'start_page'));
        osc_add_hook('before_search', array(&$this, 'start_page'));

        osc_add_hook('add_admin_toolbar_menus', array(&$this, 'add_menu_toolbar'));
        osc_add_hook('admin_header', array(&$this, 'admin_page_header'));
        osc_add_hook('admin_items_table', array(&$this, 'admin_items_table')); 
        osc_add_hook('items_processing_row', array(&$this, 'items_processing_row')); 
        osc_add_hook('admin_users_table', array(&$this, 'admin_users_table')); 
        osc_add_hook('users_processing_row', array(&$this, 'users_processing_row')); 

        osc_add_hook('item_extend', array(&$this, 'item_extend_merge'), 2); // add product attributes to Search results pre osclass 5.0
        osc_add_hook('item_extend_query', array(&$this, 'item_extend_query'),2);
        osc_add_hook('item_extend_merge', array(&$this, 'item_extend_merge'),2);
        osc_add_hook('item_detail', array(&$this, 'item_detail'));
        osc_add_hook('item_form', array(&$this, 'item_edit_currency'),2);
        osc_add_hook('item_form', array(&$this, 'item_edit_downloads'),8);
        osc_add_hook('item_edit', array(&$this, 'item_edit_currency'),2);
        osc_add_hook('item_edit', array(&$this, 'item_edit_downloads'),8);
        osc_add_hook('delete_item', array(&$this, 'item_delete'));

        osc_add_hook('pre_item_post', array(&$this, 'pre_item_post'));
        osc_add_hook('item_add_prepare_data', array(&$this, 'item_add_prepare_data'));
        osc_add_hook('edited_item', array(&$this, 'item_post'));
        osc_add_hook('posted_item', array(&$this, 'item_post'));
        osc_add_hook('payment_pro_item_paid', array(&$this, 'item_paid'));

        osc_add_hook('delete_user', array(&$this, 'user_delete'));

        osc_add_hook('admin_menu_init', array(&$this, 'admin_menu'));

        osc_add_hook('gdpr_dump_user_data', array(&$this, 'gdpr_dump_user_data'));
        osc_add_hook('payment_pro_invoices_processing_row', array(&$this, 'payment_pro_invoices_processing_row'));

}

    function hook_after_install() {
        $path = osc_plugin_resource('shop/install/struct.sql');
        $sql = file_get_contents($path);

        if (!$this->dao->importSQL($sql)) {
            throw new Exception($this->dao->getErrorLevel() . ' - ' . $this->dao->getErrorDesc());
        }

        @mkdir(osc_content_path() . 'uploads/shop/');
        osc_set_preference('download_path', osc_content_path() . 'uploads/shop/', 'shop', 'STRING');
        osc_set_preference('max_files', '1', 'shop', 'INTEGER');
        osc_set_preference('allowed_ext', 'zip,rar,tgz', 'shop', 'INTEGER');
    }

    function hook_after_uninstall() {
        $this->dao->query(sprintf('DROP TABLE %st_shop_cart', DB_TABLE_PREFIX));
        $this->dao->query(sprintf('DROP TABLE %st_shop_currencies', DB_TABLE_PREFIX));
        $this->dao->query(sprintf('DROP TABLE %st_shop_downloads', DB_TABLE_PREFIX));
        $this->dao->query(sprintf('DROP TABLE %st_shop_order_history', DB_TABLE_PREFIX));
        $this->dao->query(sprintf('DROP TABLE %st_shop_order_items', DB_TABLE_PREFIX));
        $this->dao->query(sprintf('DROP TABLE %st_item_files', DB_TABLE_PREFIX));
        $this->dao->query(sprintf('DROP TABLE %st_item_prices', DB_TABLE_PREFIX));
        $this->dao->query(sprintf('DROP TABLE %st_user_addresses', DB_TABLE_PREFIX));
        osc_delete_preference('max_files', 'shop');
        osc_delete_preference('download_path', 'shop');
        osc_delete_preference('allowed_ext', 'shop');
    }

    function plugin_title($title) {
        switch (Params::getParam('action')) {
            case "configure":
                osc_remove_filter('admin_title', 'customPageTitle');
                $title = __('Shop Configure', 'shop');
                break;
        }
        switch (Params::getParam('route')) {
            case 'shop-admin-options':
            case 'shop-admin-downloads':
            case 'shop-admin-company':
                osc_remove_filter('admin_title', 'customPageTitle');
                $title = __('Shop Options', 'shop');
                break;
            case 'shop-admin-currency':
                osc_remove_filter('admin_title', 'customPageTitle');
                $title = __('Exchange rates', 'shop');
                break;
            case "shop-admin-orders":
                osc_remove_filter('admin_title', 'customPageTitle');
                $title = __('Orders', 'shop');
                break;
        }
        return $title;
    }

    function remove_title_header() {
        osc_remove_hook('admin_page_header', 'customPageHeader');
    }

    function customPageHeader_conf() {
        ?>
        <h1><?php _e('Shop Configuration', 'shop'); ?></h1>
        <?php
    }

    function customPageHeader_orders() {
        ?>
        <h1><?php _e('Orders', 'shop'); ?></h1>
        <?php
    }


    function admin_menu() {
        osc_add_admin_submenu_divider('plugins', __('Shop', 'shop'), 'shop', 'administrator');
        osc_add_admin_submenu_page('plugins', __('&raquo; Configure categories', 'shop'), osc_admin_configure_plugin_url("shop/index.php"), 'shop_cats', 'administrator');
        osc_admin_menu_plugins(__('&raquo; Shop options'), osc_route_admin_url('shop-admin-options'), 'shop_options');
        osc_admin_menu_plugins(__('&raquo; Exchange rates'), osc_route_admin_url('shop-admin-currency'), 'shop_curr');
    }

    function admin_page_header() {

        switch (Params::getParam('route')) {
            case 'shop-admin-options':
            case 'shop-admin-downloads':
            case 'shop-admin-company':
                osc_remove_hook('admin_page_header', 'customPageHeader');
                osc_add_hook('admin_page_header',  function() { echo '<h1>' . __('Shop options', 'shop') . '</h1>'; @include(__DIR__ . '/../admin/header.php'); } );
                break;
            case "shop-admin-currency":
                osc_remove_hook('admin_page_header', 'customPageHeader');
                osc_add_hook('admin_page_header',  function() { echo '<h1>' . __('Exchange rates', 'shop') . '</h1>'; } );
                break;
            default:
                break;
        }
    }

    function user_menu_route() {
        $class = '';
        if (Params::getParam('route') == 'user-account') {
            $class = 'active';
        }
        echo '<li class="opt_account ' . $class . '" ><a href="' . osc_route_url('user-account', array('iPage' => null)) . '" >' . __('Account history', 'shop') . '</a></li>';

        $class = '';
        if (Params::getParam('route') == 'user-downloads') {
            $class = 'active';
        }
        echo '<li class="opt_downloads ' . $class . '" ><a href="' . osc_route_url('user-downloads', array('iPage' => null)) . '" >' . __('Downloads', 'shop') . '</a></li>';

        $class = '';
        if (Params::getParam('route') == 'user-addresses') {
            $class = 'active';
        }
        echo '<li class="opt_addresses ' . $class . '" ><a href="' . osc_route_url('user-addresses', array('iPage' => null)) . '" >' . __('Address book', 'shop') . '</a></li>';
    }

    function shop_help() {
        osc_admin_render_plugin(osc_plugin_path(dirname(__FILE__)) . '/admin/help.php');
    }
    
    function add_menu_toolbar() {
        $title = '<i class="bi bi-shop"></i> ' . __('Orders', 'admin_process_alert');
        AdminToolbar::newInstance()->add_menu(
                array('id' => 'orders',
                    'title' => $title,
                    'href' => osc_route_admin_url('shop-admin-orders'),
                    'meta' => array('class' => 'action-btn action-btn-black')
                )
        );
    }

    function admin_items_table($table) {
        $table->removeColumn('user');
        $table->removeColumn('location');
        $table->removeColumn('date');
        $table->removeColumn('expiration');
        $table->addColumn('price', __('Price'));        
        $table->addColumn('stats', __('Statistics'));
        $table->addColumn('file', __('File'));
    }

    function admin_users_table($table) {
        $table->removeColumn('items');
        $table->addColumn('downloads', __('Downloads'));        
        $table->addColumn('sales', __('Sales'));
    }

    function items_processing_row($row, $aRow) {
        $currencies = ShopCurrency::newInstance()->getAll();
        $price = '';
        if (isset($aRow['fk_c_currency_code'])) {
            $price = osc_format_price($aRow['i_price'], $currencies[$aRow['fk_c_currency_code']]['s_symbol']);
            foreach ($currencies as $key=> $currency) {
                if ($key != $aRow['fk_c_currency_code']) {
                    $altPrice = '';
                    foreach ($aRow['currency'] as $curr => $curPrice) {
                        if ($curr == $key) {
                            $altPrice = '<br />' . osc_format_price($curPrice, $currency['s_symbol']);
                        }
                    }
                    if (empty($altPrice)) {
                        $converted = ShopCurrency::newInstance()->convert($aRow['fk_c_currency_code'],$aRow['i_price'], $key);
                        $altPrice = osc_format_price($converted, $currencies[$key]['s_symbol']);                   
                    }
                    $price .= ', ' .$altPrice;
                }
            }
        }
        $row['price']   = $price;
        $file = '';
        if (isset($aRow['download'][0])) {
            $file       = $aRow['download'][0]['s_name'];
        }
        $row['file']    = $file;
        $row['stats']   = 'Views:'.$aRow['i_num_views'];
        return $row;
    }
    
    function users_processing_row($row, $aRow) {
        $downloadTotals = ShopDownloads::newInstance()->getDownloadStats($aRow['pk_i_id']);
        $downloads = $downloadTotals['total'] . ', ' . __(' downloaded ','shop') 
                        . $downloadTotals['times'] . __(' times','shop');
        $currencies = ShopCurrency::newInstance()->getAll();
        $salesTotals = ShopOrders::newInstance()->getSalesTotal($aRow['pk_i_id']);
        $totals = array();
        foreach ($salesTotals as $saleTotal) {
            $status = ModelPaymentPro::newInstance()->status($saleTotal['i_status']) . ':';
            $status .= ShopCurrency::newInstance()->format_price($saleTotal['total'], $currencies[$saleTotal['s_currency_code']]['s_symbol']);
            $totals[] = $status;
        }
        $sales = implode(', ', $totals);

        $row['downloads']   = ($downloadTotals['total'] ? $downloads : '');
        $row['sales']   = '<a href="'.osc_route_admin_url('payment-pro-admin-log',array('userid'=> $aRow['pk_i_id'])).'">'.$sales.'</a>';
        return $row;
    }
    
    function user_extend($user) {
        return $user;
    }
    
    function item_extend_query($item) {
//        $item->dao->select('ip.fk_c_code as s_price_curr, ip.i_price as i_price_curr');
//        $item->dao->join(ShopCurrency::newInstance()->getTableNamePrices() . ' as ip', 
//            'i.pk_i_id = pa.fk_i_item_id'
//        );
    }
    
    function item_extend_merge($items) {
        $currencies = ShopCurrency::newInstance()->getAll();
        $default_currency = ShopCurrency::newInstance()->getDefault();
        $itemIds = array_column($items, 'pk_i_id');
        $prices = ShopCurrency::newInstance()->findPrices($itemIds);
        $files = ShopDownloads::newInstance()->findFiles($itemIds);
        foreach ($items as $itemKey=>$item) {
            $extra = array();
            foreach ($prices as $price) {
                if ($price['fk_i_item_id'] === $item['pk_i_id']) {
                    $extra[$price['fk_c_code']] = $price['i_price'];
                }
            }
            $items[$itemKey]['currency'] = $extra;

            $extra = array();
            foreach ($files as $file) {
                if ($file['fk_i_item_id'] === $item['pk_i_id']) {
                    $extra[] = array('s_name'=>$file['s_name'], 's_code'=>$file['s_code'],
                        'pk_i_id'=>$file['pk_i_id'],
                        'fk_i_item_id'=>$file['fk_i_item_id']);
                }
            }
            $items[$itemKey]['download'] = $extra;
        }
        
        return $items;
    }

    function item_add_prepare_data($data) {
        if (empty($data['contactEmail'])) {
            $data['contactEmail'] = (osc_is_admin_user_logged_in() ? osc_logged_admin_email() : osc_logged_user_email());
        }
        return $data;
    }
    
    function item_detail($item) {
        if (osc_is_this_category('shop', osc_item_category_id())) {
            require_once __DIR__ . '/../view/item_detail.php';
        }
    }

    function item_edit_currency($catId = null, $item_id = null) {
        if (osc_is_this_category('shop', $catId)) {
            $item = Item::newInstance()->findByPrimaryKey($item_id);
            
            require_once __DIR__ . '/../view/item_edit_currency.php';
        }
    }

    function item_edit_downloads($catId = null, $item_id = null) {
        if (osc_is_this_category('shop', $catId)) {
            $files = ShopDownloads::newInstance()->getFilesFromItem($item_id);
            require_once __DIR__ . '/../view/item_edit_downloads.php';
        }
    }

    function pre_item_post() {
        $currencies = ShopCurrency::newInstance()->getAll();
        $default_currency = ShopCurrency::newInstance()->getDefault();
        foreach($currencies as $currency) {
            Session::newInstance()->_setForm('pre_'.$currency['pk_c_code'].'price', Params::getParam('pre_'.$currency['pk_c_code'].'price'));
        }
        Session::newInstance()->_setForm('pre_shop_files', Params::getParam('pre_shop_files'));

        // keep values on session
        Session::newInstance()->_keepForm('pre_'.$currency['pk_c_code'].'price');
        Session::newInstance()->_keepForm('pre_shop_files');
    }

    function item_post($item) {
        if ($item['fk_i_category_id'] != null) {
            if (osc_is_this_category('shop', $item['fk_i_category_id'])) {
                $currencies = ShopCurrency::newInstance()->getAll();
                $prices = array();
                foreach($currencies as $currency) {
                    if ($currency['pk_c_code'] != $item['fk_c_currency_code']) {
                        $price = array();
                        $price['code'] = $currency['pk_c_code'];
                        $price['price'] = Params::getParam($currency['pk_c_code'].'-price')*1000000;
                        $prices[] = $price;
                    }
                }
                if (isset($item['fk_c_currency_code'])) {
                    $prices[] = array('code'=>$item['fk_c_currency_code'],'price'=>0);
                }
                ShopCurrency::newInstance()->updateItemPrices($item, $prices);
    
                $this->upload_files($item);
            }
        }
    }
    
    function downloadProducts() {
        $allowed_ext = explode(',', osc_get_preference('allowed_ext', 'shop'));
        $products = array();
        foreach($allowed_ext as $ext) {
            $products[] = strtoupper(substr($ext,0,3));
        }
        return $products;
    }
    
    function item_paid($item, $data, $invoiceId) {
        $invoice = ModelPaymentPro::newInstance()->getPayment($invoiceId);
        $products = $this->downloadProducts();
        if (in_array(substr($item['id'],0,3), $products)) {
            ShopDownloads::newInstance()->logSale($invoice['fk_i_user_id'], $item['item_id'], $invoiceId);
        }
        $invoiceExtra = ModelPaymentPro::newInstance()->invoiceExtra($invoiceId);
        $user = User::newInstance()->findByPrimaryKey($invoice['fk_i_user_id']);
        $delivery = array(); $billing = array();
        if ($invoiceExtra) {
            $extra = json_decode($invoiceExtra['s_extra'],true);
            $delivery = ShopAddresses::newInstance()->findByPrimaryKey($extra['delivery']);
            $billing  = ShopAddresses::newInstance()->findByPrimaryKey($extra['billing']);
            ShopOrders::newInstance()->insertOrder($user, $invoice, $extra, $delivery, $billing);
        }
        ShopCart::newInstance()->deleteCart($invoice['fk_i_user_id']);
        unset($invoice);
        unset($products);
        unset($invoiceExtra);
        unset($user);
        unset($delivery);
        unset($billing);
    }
    
    function item_delete($itemId) {
        ShopCart::newInstance()->deleteItem($itemId);
        ShopCurrency::newInstance()->deleteItem($itemId);
        ShopDownloads::newInstance()->deleteItem($itemId);
    }

    function user_delete($userId) {
        ShopOrders::newInstance()->deleteUser($userId);
        ShopAddresses::newInstance()->deleteUser($userId);
    }

    function upload_files($item) {
        $old_files = $item['download'];
        $files = Params::getFiles('shop_files');
        if (count($files) > 0) {
            require LIB_PATH . 'osclass/mimes.php';
            $aMimesAllowed = array();
            $aExt = explode(',', osc_get_preference('allowed_ext', 'shop'));
            foreach ($aExt as $ext) {
                $mime = $mimes[$ext];
                if (is_array($mime)) {
                    foreach ($mime as $aux) {
                        if (!in_array($aux, $aMimesAllowed)) {
                            array_push($aMimesAllowed, $aux);
                        }
                    }
                } else {
                    if (!in_array($mime, $aMimesAllowed)) {
                        array_push($aMimesAllowed, $mime);
                    }
                }
            }
            $failed = false;
            $maxSize = osc_max_size_kb() * 1024;
            foreach ($files['error'] as $key => $error) {
                $file_name = $files['name'][$key];
                $bool_img = false;
                if  (!empty($file_name)) {
                    if ($error == UPLOAD_ERR_OK) {
                        $size = $files['size'][$key];
                        if ($size <= $maxSize) {
                            $fileMime = $files['type'][$key];

                            if (in_array($fileMime, $aMimesAllowed)) {
                                $date = date('YmdHis');
                                $file_name = $files['name'][$key];
                                $path = osc_get_preference('download_path', 'shop') . $file_name;
                                if (move_uploaded_file($files['tmp_name'][$key], $path)) {
                                    ShopDownloads::newInstance()->insertFile($item['pk_i_id'], $files['name'][$key], $date);
                                } else {
                                    $error = 'move';
                                    $failed = true;
                                }
                            } else {
                                $error = 'invalid';
                                $failed = true;
                            }
                        } else {
                            $error = 'size';
                            $failed = true;
                        }
                    } else {
                        $error = 'upload - ' . $error;
                        $failed = true;
                    }
                }
            }
            if ($failed) {
                osc_add_flash_error_message(__('Some of the files were not uploaded because they have incorrect extension or were too large - ' . $error, 'shop'), 'admin');
            }
        }
    }

    function delete_item($item) {
        ShopDownloads::newInstance()->removeItem($item);
    }
    
    function add_address_modal() {
        require_once __DIR__ . '/../user/address_modal.php';
    }
    
    function add_payment_modal() {
        osc_run_hook('payment_pro_checkout_footer');        
    }

    function start_page() {
        $page = Params::getParam('page');
        $route = Params::getParam('route');
        
        if ($route == 'user-addresses' || $route == 'shop-checkout') {
            osc_add_hook('footer', array(&$this, 'add_address_modal'));
            if ($route == 'shop-checkout') {
                osc_add_hook('footer', array(&$this, 'add_payment_modal'));
            }
        }
        
        // block redirect to cart summary page if not logging in from cart page
        if (strpos($route,'shop-checkout') === false) {
            Session::newInstance()->_set('shopcart-active', false);
        }
    }
     function before_validating_login() {
        $email            = trim(Params::getParam('email'));
        if (osc_validate_email($email)) {
            $user = User::newInstance()->findByEmail($email);
        }
        if (empty($user)) {
            $user = User::newInstance()->findByUsername($email);
        }
        if (empty($user) || !empty($user['s_password'])) {
            return;
        }
        
        // user exists and password empty, so use recover process
        Params::setParam('s_email', $email);
        $userActions = new UserActions(false);
        $success     = $userActions->recover_password();

        osc_add_flash_ok_message(_m("Password change required."));
        osc_add_flash_ok_message(_m('We have sent you an email with the instructions to reset your password'));
        BaseModel::redirectTo(osc_user_login_url());
    }
  
    function after_login() {
        $cookie_list = ShopCart::newInstance()->items();
        $new_list = $cookie_list;
        $details = array();
        $result = $this->dao->query(sprintf("SELECT sh.s_source, sh.fk_i_item_id, sh.i_quantity, ipa.i_price "
                        . "FROM %st_shop_cart sh "
                        . "INNER JOIN %st_item_product_attr ipa ON ipa.fk_i_item_id = sh.fk_i_item_id "
                        . "WHERE sh.fk_i_user_id = %d", DB_TABLE_PREFIX, DB_TABLE_PREFIX, osc_logged_user_id()));
        if ($result !== false) {
            $details = $result->resultArray();
        }
        foreach ($details as $detail) {
            if (array_search($detail['fk_i_item_id'], array_column($new_list, 'item')) !== false) {
                // remove from items to insert those already in user table
                if (($key = array_search($detail['fk_i_item_id'], array_column($new_list, 'item'))) !== false) {
                    unset($new_list[$key]);
                    $new_list = array_values($new_list);
                }
            } else {
                // add saved user table items to cookie
                $cookie_list[] = array('site' => $detail['s_source'], 'item' => $detail['fk_i_item_id'],
                    'qty' => $detail['i_quantity'], 'price' => $detail['i_price']);
            }
        }
        // add to user table newly added items
        foreach ($new_list as $id) {
            $this->dao->query(sprintf("INSERT INTO %st_shop_cart (fk_i_item_id,i_quantity,i_price,fk_i_user_id) VALUES (%d,%d,%d,%d)", DB_TABLE_PREFIX, $id['item'], $id['qty'], $id['price'], osc_logged_user_id()));
        }
        Session::newInstance()->_set('shopcart', serialize(array_values($cookie_list)));
        
        if (Session::newInstance()->_get('shopcart-active')) {
            osc_redirect_to(osc_route_url('shop-checkout'));
        }

    }

    function scripts_loaded() {
        echo '<script type="text/javascript">';
        echo 'var shop_url = "' . osc_route_ajax_url('shop-ajax') . '";';
        echo '</script>';
    }
    
    function gdpr_dump_user_data($data) {
        $cart = ShopCart::newInstance()->findByUserId($data['user']['pk_i_id']);
        $address = ShopAddresses::newInstance()->findByUserId($data['user']['pk_i_id']);
        $params = array('userid'=>$data['user']['pk_i_id'],'limit'=>9999);
        $invoices = ShopOrders::newInstance()->orders($params);
        
        $data['address'] = $address;
        $data['cart'] = $cart;
        $data['invoice'] = $invoices;
        return $data;
    }
    
    function payment_pro_invoices_processing_row($row, $aRow) {        
        $currencies = ShopCurrency::newInstance()->getAll();
        $symbol = $currencies[$aRow['s_currency_code']]['s_symbol'];
        $row['items'] = $this->invoiceRows($aRow['rows'], $symbol);
        $row['subtotal'] = payment_pro_format_price($aRow['i_amount'], $symbol);
        $row['tax'] = payment_pro_format_price($aRow['i_amount_tax'], $symbol);
        $row['total'] = payment_pro_format_price($aRow['i_amount_total'], $symbol);
        
        return $row;
    }

    private function invoiceRows($items, $symbol) {
        $rows = array();
        foreach($items as $item) {
            $rows[] = ShopCurrency::newInstance()->format_price($item['i_amount'], $symbol) . ' - ' . $item['i_product_type'] . ' - ' . $item['s_concept'];
        }
        return implode('<br />', $rows);
    }
}
