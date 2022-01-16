<?php

    $function = Params::getParam('function');
    switch ($function) {
        case ('address'):
            $action = Params::getParam('process');
            $id = Params::getParam('aid');
            $userId = Params::getParam('id');
            $code = Params::getParam('code');

            $user = User::newInstance()->findByIdSecret($userId, $code);
            if ($user == false) {
                $json['success'] = false;
                $json['msg'] = __('Invalid parameters', "shop");
                echo json_encode($json);
                return false;
            }
            switch ($action) {
                case 'u':
                    $type = Params::getParam('addrType');
                    $name = Params::getParam('name');
                    $company = Params::getParam('company');
                    $country = Params::getParam('country');
                    $address = Params::getParam('address');
                    $suburb = Params::getParam('suburb');
                    $city = Params::getParam('city');
                    $state = Params::getParam('state');
                    $postcode = Params::getParam('postcode');
                    $addr = array('userId'=> $userId, 'type'=>$type, 'name'=>$name, 'company'=>$company,
                                    'country'=>$country, 'address'=>$address, 'suburb'=>$suburb, 
                                    'city'=>$city, 'state'=>$state, 'postcode'=>$postcode);
                    ShopAddresses::newInstance()->updateAddress($id, $addr);
                    $data = array();
                    $data['success'] = true;
                    echo json_encode($data);
                    break;
                case 'd':
                    ShopAddresses::newInstance()->deleteAddress($id);
                    $data = array();
                    $data['success'] = true;
                    echo json_encode($data);
                    break;
            }
            break;
        case 'cart':
            $incr = Params::getParam('incr');
            $productId = Params::getParam('id');
            $desc = Params::getParam('desc');
            $price = Params::getParam('price');
            if (!empty($productId)) {
                $count = 0;
                $qty = 1;
                $products = payment_pro_cart_get();

                $tmp = explode("-", $productId);
                $itemId = $tmp[count($tmp) - 1];
                $key = array_search($productId, array_column($products,'id'));
                if (!empty($incr)) {
                    if ($products[$productId]['quantity'] == 1 && $incr == '-') {
                        payment_pro_cart_drop($key);
                    } else {
                        // adds to qty of existing item
                        payment_pro_cart_add($productId, $desc, $price, (($incr == '+') ? 1 : -1)); 
                    }
                } else {
                    if ($key !== false) {
                        // remove from items to insert those already in user table
                        payment_pro_cart_drop($productId);
                    } else {
                        // add saved user table items to cookie
                        payment_pro_cart_add($productId, $desc, $price);
                    }
                }
                $products = payment_pro_cart_get();

                if ( osc_is_web_user_logged_in() ) {
                    //check if the item is not already in the shoppingcart
                    $detail = ShopCart::newInstance()->getUserItem($itemId, osc_logged_user_id());

                    $url = osc_route_url('shop-user', array('iPage' => null));

                    //If nothing returned then we can process
                    $qty = $products[$productId]['quantity'];
                    if (!empty($incr) && isset($detail['fk_i_item_id']) && $qty > 0) {
                        ShopCart::newInstance()->updateUserItem($itemId, osc_logged_user_id(),$qty);
                    } else {
                        if (!isset($detail['fk_i_item_id'])) {
                            ShopCart::newInstance()->insertUserItem($itemId, $productId, $desc, osc_logged_user_id(),$qty, $price);
                        } else {
                            ShopCart::newInstance()->deleteUserItem($itemId, osc_logged_user_id());
                        }
                    }
                }
                $item = Item::newInstance()->findByPrimaryKey($itemId);
                $data = array();
                $cartCount = 0;
                foreach($products as $product) {
                    $cartCount += $product['quantity'];
                }
                $data['count'] = $cartCount;
                $data['qty'] = $qty;
                $data['button'] = ShopCart::newInstance()->link($item);
                $data['shops'] = ShopCart::newInstance()->totals_html();
                $data['cart'] = ShopCart::newInstance()->html();
                $data['success'] = true;
                echo json_encode($data);
                break;
            }
            $json['success'] = false;
            $json['msg'] = __("Empty id", "shop");
            echo json_encode($json);
            return false;
        case ('checkout'):
            $select = Params::getParam('select');
            $option = Params::getParam('option');
            if (!empty($select) && !empty($option)) {
                Session::newInstance()->_set('checkout-'.$select, $option);
                $data = array();
                $data['success'] = true;
                echo json_encode($data);
            }
            break;
        case ('currency'):
            $curr = Params::getParam('currency');
            if (!empty($curr)) {
                ShopCurrency::newInstance()->setDefault($curr);
                $data = array();
                $data['success'] = true;
                echo json_encode($data);
            }
            break;
        case ('convert'):
            $from = Params::getParam('from');
            $amount = Params::getParam('amount');
            
            if (!empty($from) && !empty($amount)) {
                $currencies = ShopCurrency::newInstance()->getAll();
                $curr = array();
                foreach($currencies as $currency) {
                    $curr[$currency['pk_c_code']]['amount'] = ShopCurrency::newInstance()->convert($from, $amount, $currency['pk_c_code']) * 1000000;
                    $curr[$currency['pk_c_code']]['formatted'] = ShopCurrency::newInstance()->format_price($curr[$currency['pk_c_code']]['amount'], $currencies[$currency['pk_c_code']]['s_description']);
                }
                $data = array();
                $data['from'] = $from;
                $data['amount'] = $amount;
                $data['currencies'] = $curr;
                $data['success'] = true;
                echo json_encode($data);
            } else {
                $json['success'] = false;
                $json['msg'] = __('Missing parameters', "shop");
                echo json_encode($json);
                return false;
            }
            break;
        case 'downloads':
            $id     = Params::getParam('id') ;
            $item   = Params::getParam('item_id') ;
            $code   = Params::getParam('code') ;
            $secret = Params::getParam('secret') ;
            $json = array();

            if( Session::newInstance()->_get('userId') != '' ){
                $userId = Session::newInstance()->_get('userId');
                $user = User::newInstance()->findByPrimaryKey($userId);
            }else{
                $userId = null;
                $user = null;
            }

            // Check for required fields
            if ( !( is_numeric($id) && is_numeric($item) ) ) {
                $json['success'] = false;
                $json['msg'] = __("The selected file couldn't be deleted, the url doesn't exist", "shop");
                echo json_encode($json);
                return false;
            }

            $aItem = Item::newInstance()->findByPrimaryKey($item);

            // Check if the item exists
            if(count($aItem) == 0) {
                $json['success'] = false;
                $json['msg'] = __('The item doesn\'t exist', "shop");
                echo json_encode($json);
                return false;
            }

            // Check if the item belong to the user
            if($userId != null && $userId != $aItem['fk_i_user_id']) {
                $json['success'] = false;
                $json['msg'] = __('The item doesn\'t belong to you', "shop");
                echo json_encode($json);
                return false;
            }

            // Check if the secret passphrase match with the item
            if($userId == null && $aItem['fk_i_user_id']==null && $secret != $aItem['s_secret']) {
                $json['success'] = false;
                $json['msg'] = __('The item doesn\'t belong to you', "shop");
                echo json_encode($json);
                return false;
            }

            $result = ShopDownloads::newInstance()->getFileByItemCode($item, $code);

            if (isset($result['pk_i_id'])) {

                ShopDownloads::newInstance()->deleteFile($id, $code);

                $json['msg'] =  __('The selected file has been successfully deleted', "shop") ;
                $json['success'] = 'true';
            } else {
                $json['msg'] = __("The selected file couldn't be deleted", "shop") ;
                $json['success'] = 'false';
            }

            echo json_encode($json);
            break;
    }
    