<?php

class ShopCart extends DAO {

    /**
     * It references to self object: ShopCart.
     * It is used as a singleton
     * 
     * @access private
     * @since 3.0
     * @var Currency
     */
    private static $instance;

    public static function newInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Construct
     */
    function __construct() {
        parent::__construct();
        $this->setTableName('t_user_cart');
        $this->setPrimaryKey('pk_i_id');
        $array_fields = array(
            'pk_i_id',
            'fk_i_item_id',
            's_source',
            'i_quantity',
            'i_price',
            'fk_i_user_id');
        $this->setFields($array_fields);
    }

    public function findByUserId($userId) {
        $this->dao->select();
        $this->dao->from($this->getTableName());
        $this->dao->where('fk_i_user_id', $userId);
        $result = $this->dao->get();
        if ($result == false) {
            return array();
        }

        return $result->resultArray();
    }
    
    public function getUserItem($itemId, $userId) {
        $result = $this->dao->query(sprintf("SELECT * FROM %st_user_cart WHERE fk_i_item_id = %d and fk_i_user_id = %d", DB_TABLE_PREFIX, $itemId, $userId));
        if ($result !== false && $result->numRows() > 0) {
            return $result->resultArray()[0];
        }
        return array();
    }

    public function updateUserItem($itemId, $userId, $qty) {
        return $this->dao->query(sprintf("UPDATE %st_user_cart SET i_quantity = %d WHERE fk_i_item_id = %d AND fk_i_user_id = %d", DB_TABLE_PREFIX, $qty, $itemId, $userId));
    }
    
    public function insertUserItem($itemId, $productId, $desc, $userId, $qty, $price) {
        return $this->dao->query(sprintf("INSERT INTO %st_user_cart (fk_i_item_id, s_product_type, s_description, i_quantity, i_price, fk_i_user_id) VALUES (%d,'%s','%s',%d,%d,%d)", DB_TABLE_PREFIX, $itemId, $productId, $desc, $qty, $price, $userId));
    }
    
    public function deleteUserItem($itemId, $userId) {
        return $this->dao->query(sprintf("DELETE FROM %st_user_cart WHERE fk_i_item_id = %d AND fk_i_user_id = %d", DB_TABLE_PREFIX, $itemId, $userId));
    }
    
    public function deleteItem($itemId) {
        $this->dao->query(sprintf("DELETE FROM %st_user_cart WHERE fk_i_item_id = '$itemId'", DB_TABLE_PREFIX));
    }

    public function deleteCart($userId) {
        $this->dao->query(sprintf("DELETE FROM %st_user_cart WHERE fk_i_user_id = '$userId'", DB_TABLE_PREFIX));
    }

    public function items() {
        $cookie_list = unserialize(Session::newInstance()->_get('shopcart'));
        if (!isset($cookie_list) || !is_array($cookie_list)) {
            return array();
        }
        return $cookie_list;
    }

    public function totals() {
        $cart = payment_pro_cart_get();
        $totals = array();
        $total = 0;
        foreach ($cart as $item) {
            $total += ($item['quantity'] * ($item['amount'] / 1000000));
        }
        if ($total > 0) {
            $totals[] = array('total' => $total);
        }
        return $totals;
    }

    public function totals_html() {
        $html = '';
        $symbol = ShopCurrency::newInstance()->defaultSymbol();
        $cart = payment_pro_cart_get();
        $grandtotal = 0;
        $total = 0;
        foreach ($cart as $item) {
            $total += ($item['quantity'] * $item['amount']);
        }
        $html .= '<span class="shop-price right">' . osc_format_price($total, $symbol) . '</span>';
        $html .= '<br />';
        $grandtotal += $total;
        if (!empty($html)) {
            $html .= 'Total<span class="shops-total right">' . osc_format_price($grandtotal, $symbol) . '</span>';
        }
        return $html;
    }

    public function sort() {
        $items = payment_pro_cart_get();
        $sorted = array();
        foreach($items as $item) {
            $sorted[] = array('description'=>trim($item['description']),
                'id'=>$item['id'], 
                'quantity'=>$item['quantity'], 'amount'=>$item['amount']);
        }
        sort($sorted);
        return $sorted;
    }

    public function html($cartPage = false) {
        $total = 0;
        $html = ''; 
        $symbol = ShopCurrency::newInstance()->defaultSymbol();
        $items = $this->sort();
        foreach ($items as $item) {
            $tmp = explode("-", $item['id']);
            $itemId = $tmp[count($tmp) - 1];
            $url = osc_item_url_ns($itemId);
            if ($item['quantity'] > 0) {
                if (empty($html)) {
                    $html = '<div class="cart">';
                }
                $qty = $item['quantity'];
                $html .= '<div><a href="' . $url . '" class="cart_title"  title="'. osc_esc_html($item['description']).'" style="max-width:180px;">';
                $html .= osc_highlight($item['description'], 40);
                $html .= '</a></div>';
                $html .= '<span class="cart_qty">' . $qty . ' x</span>';
                $html .= '<span class="cart_price right">' . osc_format_price($item['amount'], $symbol);
                $total += ($qty * $item['amount']);
                $html .= '<a href="javascript://" class="cart_del" data-id="' . $item['id'] . '" data-price="' . $item['amount'] . '">';
                $html .= '<i class="fa fa-times-circle-o"></i></a></span></a>';
            }
        }

        if (!empty($html)) {
            if ($cartPage) {
                $hide = "display: none;";
                $text = '';
            } else {
                $text = "Checkout";
                $hide = "";
            }
            $html .= '<div class="cart_total">Total<span class="shop-total right">' . osc_format_price($total, $symbol) . '</span></div>';
            $html .= '<div class="cart_checkout" style="'.$hide.'"><a href="' . osc_route_url('shop-checkout') . '" class="cart_checkout_button" >';
            $html .= '<button class="btn btn-success btn-block  btn-lg">' .$text.'</button>';
            $html .= '</a></div></div>';
        }
        return $html;
    }

    public function qty($id) {
        $cart = payment_pro_cart_get();
        $qty = 0;
        if (count($list)) {
            $qty = $cart[array_search($id, array_column($cart, 'id'))]['quantity'];
        }
        return $qty;
    }
    
    public function allDigital() {
        $cart = payment_pro_cart_get();
        $itemIds = array();
        foreach($cart as $key=>$product) {
            $tmp = explode("-", $key);
            $itemIds[] = $tmp[count($tmp) - 1];           
        }
        return ShopDownloads::newInstance()->checkDigital($itemIds, $all = true);
    }

    public function getCartItems() {
        $cart = payment_pro_cart_get();
        $items = implode(',', array_column($cart, 'item'));
        $key = md5(osc_base_url() . (string) osc_logged_user_id() . (string) $items);
        $found = null;
        $shopItems = osc_cache_get($key, $found);
        if ($shopItems === false && count($cart) > 0) {
            $search = Search::newInstance();
            $search->reset();
            $search->page(0,1000);
            $search->addConditions(sprintf("%st_item.pk_i_id IN (%s) ", DB_TABLE_PREFIX, $items));
            $return = $search->doSearch();
            osc_cache_set($key, $return, OSC_CACHE_TTL);
            $search->reset();
            return $return;
        } else {
            if ($shopItems === false) {
                $shopItems = array();
            }
            return $shopItems;
        }
    }

    public function count_items() {
        View::newInstance()->_exportVariableToView('shopItems', $this->getCartItems());
        $cart = payment_pro_cart_get();
        if (!isset($cart) || !is_array($cart)) {
            return 0;
        }
        $cartCount = 0;
        foreach($cart as $item) {
            $cartCount += $item['quantity'];
        }
        return $cartCount;
    }
    
    public function button($id, $price, $desc, $class = '') {
        $html = '<a href="javascript://" class="cart_item" data-id="' . $id . '" data-price="' . $price . '" data-desc="' . htmlspecialchars($desc) . '">';
        $html .= '<img src="' . $this->image($id) . '" alt="' . __('Add to cart', 'shop') . '" >';
        $html .= $this->tooltip($id);
        $html .= '</a>';
        return $html;
    }

    public function cart_class($id, $price, $class = '') {
        echo '<a href="javascript://" class="cart_item"  data-id="' . $id . '" data-price="' . $price . '">';
        echo '<img src="' . $this->image($id) . '" alt="' . __('Add to cart', 'shop') . '" >';
        $this->tooltip($id);
        echo '</a>';
    }

    public function cart_url() {
        return osc_route_url('shop-checkout');
    }

    public function cart_button() { 
        $count = $this->count_items();
        $badge = '';
        if ($count) {
            $badge = '<span class="badge badge-light pill-badge"><span  style="color:#000;">'.$count.'</span></span>';
        }
        ?>
        <a href="javascript:;" onclick="location.href = '<?php echo $this->cart_url(); ?>';">
            <i class="fa fa-shopping-cart" aria-hidden="true"></i> <?php _e('Cart', 'structechnique'); ?> <?php echo $badge; ?></a>
        <?php 
    }

    public function link($item) {
        $cart = payment_pro_cart_get();
        if (isset($item['download'][0]['s_name'])) {
            $file = $item['download'][0]['s_name'];
            $type = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
        } else {
            $type = 'PRD';
        }
        $id = substr($type,0,3).$item['fk_i_category_id'].'-'.$item['pk_i_id'];
        $qty = (!isset($cart[$id]) ? 1 : $cart[$id]['quantity']);
        $desc = $item['s_title'];
        $price = ShopCurrency::newInstance()->item_price($item);
        $html = '<span class="cart_upd" style="'.(!isset($cart[$id]['quantity']) ? 'display:none;':'').'">';
        $html .= '<a href="javascript://" class="cart_decr" data-id="' . $id . '" data-price="' . $price . '">';
        $html .= '<button class="btn btn-secondary" >' .__('-', 'shop').'</button>';
        $html .= '</a><span class="cart_qty">';
        $html .= $qty;
        $html .= '</span><a href="javascript://" class="cart_incr" data-id="' . $id . '" data-price="' . $price . '" data-desc="' . htmlspecialchars($desc) . '">';
        $html .= '<button class="btn btn-secondary" >' .__('+', 'shop').'</button>';
        $html .= '</a></span>';
        $html .= '<a href="javascript://" class="cart_add" style="'.(isset($cart[$id]['quantity']) ? 'display:none;':'').'" data-id="' . $id . '" data-price="' . $price . '" data-desc="' . htmlspecialchars($desc) . '">';
        $html .= '<button class="btn btn-success btn-lg">' .__('Buy now', 'shop').'</button>';
        $html .= '</a>';
        return $html;
    }

    public function cart_a($id, $price, $desc = '') {
        return '<a href="javascript://" class="cart_item"  data-id="' . $id . '" data-price="' . $price . '" data-desc="' . htmlspecialchars($desc) . '">';
    }

    public function cart_a_end() {
        return '</a>';
    }

    public function shop_alert_form() {
        osc_current_web_theme_path('shop-alert-form.php');
    }

    public function image($id) {
        if ($this->cart_check($id)) {
            $file = osc_get_preference('addedIcon', 'cart_attributes');
        } else {
            $file = osc_get_preference('addIcon', 'cart_attributes');
        }
        return osc_base_url() . "oc-content/plugins/cart/images/" . $file;
    }

    public function tooltip($id) {
        if ($this->check($id)) {
            $action = __("Remove from cart", "cart");
        } else {
            $action = __("Add to cart", "cart");
        }
        return '<span class= "cart_tip">' . $action . '</span>';
    }

    public function check($id) {
        $cart = payment_pro_cart_get();
        if ($cart == false || empty($cart)) {
            return false;
        }
        if (!in_array($id, $cart)) {
            return false;
        }
        return true;
    }
    
    public function convertCart($oldCurrency, $newCurrency) {
        $cart = payment_pro_cart_get();
        if (count($cart) == 0) {
            return;
        }
        $items = array();

        foreach($cart as $product) {
            $tmp = explode("-", $product['id']);
            $itemIds[] = $tmp[count($tmp) - 1];
        }
        $itemIds = implode(',',$itemIds);
        $sql = sprintf('select * from %s where pk_i_id in (%s)',Item::newInstance()->getTableName(),$itemIds);
        $items = Item::newInstance()->dao->query($sql);
        $items = Item::newInstance()->extendData($items->resultArray());

        payment_pro_cart_drop();

        foreach($cart as $key=>$product) {
            $tmp = explode("-", $product['id']);
            $itemId = $tmp[count($tmp) - 1];
            foreach ($items as $item) {
                if ($itemId == $item['pk_i_id']) {
                    if (!empty($item['currency'][$newCurrency])) {
                        $product['amount'] = $item['currency'][$newCurrency];
                    } elseif ($item['fk_c_currency_code'] == $newCurrency) {
                        $product['amount'] = $item['i_price'];
                    } else {
                        $product['amount'] = ShopCurrency::newInstance()->convert($oldCurrency, $product['amount'], $newCurrency);
                    }
                    payment_pro_cart_add($key, $product['description'], $product['amount'], $product['quantity']);
                }
            }
        }
    }

}
