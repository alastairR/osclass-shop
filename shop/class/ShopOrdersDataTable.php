<?php if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

    class ShopOrdersDataTable extends DataTable
    {
        private $currencies = array();
        
        public function __construct()
        {
            osc_add_filter('datatable_orders_status_class', array(&$this, 'row_class'));
            $this->currencies = ShopCurrency::newInstance()->getAll();
        }

        public function table($params)
        {

            $this->addTableHeader();

            $start = ((int)$params['iPage']-1) * $params['iDisplayLength'];

            $this->start = intval( $start );
            $this->limit = intval( $params['iDisplayLength'] );

            $invoices = ShopOrders::newInstance()->orders(array(
                'start'     => $this->start,
                'limit'     => $this->limit,
                'status'  => Params::getParam('status'),
                'source' => Params::getParam('source'),
                'userid' => Params::getParam('userid'),
                'email' => Params::getParam('email')
            ));
            $this->processData($invoices);

            $this->total = ShopOrders::newInstance()->ordersTotal();
            $this->total_filtered = $this->total;

            return $this->getData();
        }

        private function addTableHeader()
        {

            $this->addColumn('bulkactions', __('Actions', 'payment_pro'));
            $this->addColumn('status', __('Status', 'payment_pro'));
            $this->addColumn('date', __('Date', 'payment_pro'));
            $this->addColumn('items', __('Items', 'payment_pro'));
            $this->addColumn('subtotal', __('Subtotal', 'payment_pro'));
            $this->addColumn('tax', __('Taxes', 'payment_pro'));
            $this->addColumn('total', __('Total', 'payment_pro'));
            $this->addColumn('user', __('User', 'payment_pro'));
            $this->addColumn('email', __('Email', 'payment_pro'));
            $this->addColumn('code', __('Tx ID', 'payment_pro'));
            $this->addColumn('source', __('source', 'payment_pro'));

            $dummy = &$this;
            osc_run_hook("admin_shop_orders_table", $dummy);
        }

        private function processData($invoices)
        {
            if(!empty($invoices)) {

                foreach($invoices as $aRow) {
                    $row     = array();

                    // Options of each row
                    $options_more = array();
                    if ($aRow['i_status'] == SHOP_ORDER_DOWNLOADED || $aRow['i_status'] == SHOP_ORDER_PAID) {
                        $options[] = '<a href="' . osc_route_admin_url('shop-admin-orders',array('function'=>'downloads','id'=>$aRow['fk_i_invoice_id'])).'">' . __('Reset download limit')
                            . '</a>';
                    } 
                    if ($aRow['i_status'] == SHOP_ORDER_PAID) {
                        $options[] = '<a href="' . osc_route_admin_url('shop-admin-orders',array('function'=>'waiting','id'=>$aRow['fk_i_invoice_id'])).'">'
                            . __('Mark as waiting on stock') . '</a>';
                        $options[] = '<a href="' . osc_route_admin_url('shop-admin-orders',array('function'=>'despatched','id'=>$aRow['fk_i_invoice_id'])).'">'
                            . __('Mark as despatched') . '</a>';
                    } else {
                        $options[] = '<a href="' . osc_route_admin_url('shop-admin-orders',array('function'=>'paid','id'=>$aRow['fk_i_invoice_id'])).'">'
                            . __('Mark as paid') . '</a>';
                    }

                    $options_more = osc_apply_filter('more_actions_manage_orders', $options_more, $aRow);
                    // more actions
                    $moreOptions =
                        '<li class="show-more">' . PHP_EOL . '<a href="#" class="show-more-trigger">' . __('Show more')
                        . '...</a>' . PHP_EOL . '<ul>' . PHP_EOL;
                    foreach ($options_more as $actual) {
                        $moreOptions .= '<li>' . $actual . '</li>' . PHP_EOL;
                    }
                    $moreOptions .= '</ul>' . PHP_EOL . '</li>' . PHP_EOL;

                    $options = osc_apply_filter('actions_manage_orders', $options, $aRow);
                    // create list of actions
                    $auxOptions = '<ul>' . PHP_EOL;
                    foreach ($options as $actual) {
                        $auxOptions .= '<li>' . $actual . '</li>' . PHP_EOL;
                    }
                    if (!empty($options_more)) {
                        $auxOptions .= $moreOptions;
                    }
                    $auxOptions .= '</ul>' . PHP_EOL;

                    $actions = '<div class="actions">' . $auxOptions . '</div>' . PHP_EOL;

                    $row['status'] = ShopOrders::newInstance()->status($aRow['i_status']);
                    $row['date'] = $aRow['dt_order'];

                    $row['items'] = $this->_invoiceRows($aRow['rows'], $aRow['s_currency_code']) . $actions;
                    $symbol = $this->currencies[$aRow['s_currency_code']]['s_symbol'];

                    if($aRow['s_currency_code']=="BTC") {
                        // FORGET FORMAT IF BTC
                        $row['subtotal'] = ($aRow['i_amount']/1000000) . " " . $aRow['s_currency_code'];
                        $row['tax'] = ($aRow['i_amount_tax']/1000000) . " " . $aRow['s_currency_code'];
                        $row['total'] = ($aRow['i_amount_total']/1000000) . " " . $aRow['s_currency_code'];
                    } else {
                        $row['subtotal'] = ShopCurrency::newInstance()->format_price($aRow['i_amount'], $symbol);
                        $row['tax'] = ShopCurrency::newInstance()->format_price($aRow['i_amount_tax'], $symbol);
                        $row['total'] = ShopCurrency::newInstance()->format_price($aRow['i_amount_total'], $symbol);
                    }
                    $row['email'] = $aRow['s_email'];
                    $row['user'] = $aRow['fk_i_user_id'];
                    $row['code'] = payment_pro_tx_link($aRow['s_code'], $aRow['s_source']);
                    $row['source'] = $aRow['s_source'];

                    $row = osc_apply_filter('admin_shop_orders_processing_row', $row, $aRow);

                    $this->addRow($row);
                    $this->rawRows[] = $aRow;
                }

            }
        }

        private function _invoiceRows($items, $currency) {
            $rows = array();
            foreach($items as $item) {
                $symbol = $this->currencies[$currency]['s_symbol'];
                $rows[] = ShopCurrency::newInstance()->format_price($item['i_amount'], $symbol) . ' - ' . $item['i_product_type'] . ' - ' . $item['s_concept'];
            }

            return implode('<br />', $rows);
        }

        public function row_class($status)
        {
            return $this->get_row_status_class($status);
        }

        private function get_row_status_class($status) {
            switch($status) {
                case SHOP_ORDER_PENDING:
                    return 'status-spam';
                    break;
                case SHOP_ORDER_PAID:
                    return 'status-active';
                    break;
                case SHOP_ORDER_DESPATCHED:
                    return 'status-inactive';
                    break;
                case SHOP_ORDER_AWAITING_STOCK:
                    return 'status-expired';
                    break;
                default:
                    return 'status-spam';
                    break;
            }
        }
    }
