<?php

class ShopOrders extends DAO {

    /**
     * It references to self object: ModelCart.
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
        $this->setTableName('t_shop_orders');
        $this->setPrimaryKey('pk_i_id');
        $array_fields = array(
            'pk_i_id',
            'dt_order',
            'i_status',
            's_email',
            's_name',
            's_company',
            's_country',
            's_address',
            's_suburb',
            's_city',
            's_postcode',
            's_state',
            's_delivery_country',
            's_delivery_address',
            's_delivery_suburb',
            's_delivery_city',
            's_delivery_postcode',
            's_delivery_state',
            's_billing_country',
            's_billing_address',
            's_billing_suburb',
            's_billing_city',
            's_billing_postcode',
            's_billing_state',
            's_currency',
            'i_currency_value');
        $this->setFields($array_fields);
    }

    public function getTableOrderHistory() {
        return sprintf('%st_shop_order_history', DB_TABLE_PREFIX);
    }
    
    function deleteUser($userId) {
        $this->dao->query(sprintf("DELETE FROM %st_shop_addresses WHERE fk_i_user_id = '$userId'", DB_TABLE_PREFIX));
    }

    function getSalesTotal($userId) {
        $this->dao->select('sum(i_amount) as total, s_currency_code, i_status');
        $this->dao->from(ModelPaymentPro::newInstance()->getTable_invoice());
        $this->dao->where('fk_i_user_id', $userId);
        $this->dao->groupBy('s_currency_code, i_status');
        $this->dao->having('total <> 0');

        $result = $this->dao->get();
        if (!$result) {
            return array();
        }

        return $result->resultArray();
    }
    
    function logOrder($invoiceId, $status, $comment) {
        $set = array('fk_i_invoice_id'=>$invoiceId, 's_status'=> $status, 's_comment'=>$comment);
        $this->dao->insert($this->getTableOrderHistory(), $set);
        
    }

    function setOrderStatus($invoiceId, $status) {
        $this->dao->update($this->getTableName(), array('i_status'=>$status), array('fk_i_invoice_id'=>$invoiceId));
        $this->logOrder($invoiceId, $this->status($status), __('New status','shop'));
    }
    
    function insertOrder($user, $invoice, $invoiceExtra, $delivery, $billing) {
        $set = array('fk_i_invoice_id'=>$invoice['pk_i_id'], 'i_status'=>SHOP_ORDER_PAID, 
            's_email'=> $user['s_email'], 's_name'=>$user['s_name'],
            's_country'=>$user['s_country'], 's_address'=>$user['s_address'], 's_city_area'=>$user['s_city_area'],
            's_city'=>$user['s_city'], 's_zip'=>$user['s_zip'], 's_region'=>$user['s_region'],
            's_currency_code'=>$invoice['s_currency_code'], 'i_amount'=>$invoice['i_amount'],
            'i_amount_tax'=>$invoice['i_amount_tax'], 'i_amount_total'=>$invoice['i_amount_total'],
            's_shipping_method'=>$invoiceExtra['shipping_method'], 'i_shipping'=>$invoiceExtra['shipping_amount']);
        if ($delivery) {
            $set = array_merge($set, array('s_delivery_country'=>$delivery['s_country'], 's_delivery_address'=>$delivery['s_address'],
                's_delivery_city_area'=>$delivery['s_city_area'], 's_delivery_city'=>$delivery['s_city'],
                's_deleivery_zip'=>$delivery['s_zip'], 's_delivery_region'=>$delivery['s_region']));
        }
        if ($billing) {
            $set = array_merge($set, array('s_billing_country'=>$billing['s_country'], 's_billing_address'=>$billing['s_address'],
                's_billing_city_area'=>$billing['s_city_area'], 's_dbilling_city'=>$billing['s_city'],
                's_billing_zip'=>$billing['s_zip'], 's_billing_region'=>$billing['s_region']));
        }
        $this->dao->insert($this->getTableName(), $set);
    }
    
    function status($status) {
        switch ($status) {
            case SHOP_ORDER_PENDING:
                return __('Pending','shop');
                break;
            case SHOP_ORDER_PAID:
                return __('Paid','shop');
                break;
            case SHOP_ORDER_DESPATCHED:
                return __('Despatched','shop');
                break;
            case SHOP_ORDER_AWAITING_STOCK:
                return __('Waiting on stock','shop');
                break;
            case SHOP_ORDER_DOWNLOADED:
                return __('Downloaded','shop');
                break;
        }
    }

    public function orders($params) {
        $start    = (isset($params['start']) && $params['start']!='' )     ? $params['start']: 0;
        $limit    = (isset($params['limit']) && $params['limit']!='' )      ? $params['limit']: 10;
        $status  = (isset($params['status']) && $params['status']!='')  ? $params['status'] : '';
        $userid = (isset($params['userid']) && $params['userid']!='') ? $params['userid'] : '';
        $email = (isset($params['email']) && $params['email']!='') ? $params['email'] : '';
        $invoiceids = (isset($params['invoiceids']) && $params['invoiceids']!='') ? $params['invoiceids'] : '';
        $this->dao->select('so.*, ppi.dt_date, ppi.s_source, ppi.s_code, ppi.fk_i_user_id, ppi.s_currency_code') ;
        $this->dao->from($this->getTableName().' so');
        $this->dao->join(ModelPaymentPro::newInstance()->getTable_invoice().' ppi', 'ppi.pk_i_id = so.fk_i_invoice_id');
        $this->dao->orderBy('dt_order', 'DESC');
        if($status!='') {
            $this->dao->where('so.i_status', $status);
        }
        if($userid!='') {
            $this->dao->where('fk_i_user_id', $userid);
        }
        if($invoiceids!='') {
            $this->dao->whereIn('fk_i_invoice_id', $invoiceids);
        }
        if($email!='') {
            $this->dao->like('s_email', $email);
        }
        $this->dao->limit($limit, $start);
        $result = $this->dao->get();
        if(!$result) {
            return array();
        }
        $orders = $result->resultArray();
        $invIds = array_column($orders, 'fk_i_invoice_id');

        $this->dao->select('*') ;
        $this->dao->from(ModelPaymentPro::newInstance()->getTable_invoice_row());
        $this->dao->whereIn('fk_i_invoice_id', $invIds);
        $result = $this->dao->get();
        if($result) {
            foreach ($orders as $k => $inv) {
                $orders[$k]['rows'] = array();
                foreach($result->resultArray() as $row) {
                    if ($row['fk_i_invoice_id'] == $inv['fk_i_invoice_id']) {
                        $orders[$k]['rows'][] = $row;
                    }
                }
            }
        }
        return $orders;
    }

    public function ordersTotal($params = null) {
        $status  = (isset($params['status']) && $params['status']!='')  ? $params['status'] : '';
        $this->dao->select('COUNT(*) as total') ;
        $this->dao->from($this->getTableName());
        if($status!='') {
            $this->dao->where('i_status', $status);
        }
        $result = $this->dao->get();
        if($result) {
            $row = $result->row();
            if(isset($row['total'])) {
                return $row['total'];
            }
        }
        return 0;
    }

}
