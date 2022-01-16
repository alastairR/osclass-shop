<?php

class ShopAccountDataTable extends DataTable {

    private $invoices;
    private $total_filtered;

    /**
     * @param $params
     *
     * @return array
     */
    public function table($params) {

        $this->addTableHeader();

        $start = ((int) $params['iPage'] - 1) * $params['iDisplayLength'];

        $this->start = (int) $start;
        $this->limit = (int) $params['iDisplayLength'];

        $invoices = ModelPaymentPro::newInstance()->invoices(array(
            'start' => $this->start,
            'limit' => $this->limit,
            'status' => $params['status'],
            'source' => $params['source'],
            'userid' => osc_logged_user_id()
        ));

        $orders = ShopOrders::newInstance()->orders(array(
            'invoiceids' => array_column($invoices,'pk_i_id')
        ));
        foreach ($invoices as $key=>$inv) {
            foreach($orders as $ord) {
                if ($inv['pk_i_id'] == $ord['fk_i_invoice_id']) {
                    $invoices[$key]['order'] = $ord;
                }
            }
        }

        $this->processData($invoices);

        $this->total = ModelPaymentPro::newInstance()->invoicesTotal(array(
            'start' => 0,
            'limit' => 0,
            'status' => $params['status'],
            'source' => $params['source'],
            'userid' => osc_logged_user_id()
        ));
        $this->total_filtered = $this->total;

        return $this->getData();
    }

    private function addTableHeader() {

        $this->addColumn('status', __('Status'));
        $this->addColumn('date', __('Date'));
        $this->addColumn('items', __('Items'));
        $this->addColumn('subtotal', __('Subtotal'));
        $this->addColumn('tax', __('Tax'));
        $this->addColumn('total', __('Total'));
        $this->addColumn('action', __('Action'));

        $dummy = &$this;
        osc_run_hook('shop_account_table', $dummy);
    }

    /**
     * @param $invoices
     *
     */
    private function processData($invoices) {
        $currencies = ShopCurrency::newInstance()->getAll();
        if (!empty($invoices)) {
            foreach ($invoices as $aRow) {
                $row = array();

                // -- options --
                $options = array();
                View::newInstance()->_exportVariableToView('page', $aRow);
                $status = ModelPaymentPro::newInstance()->status($aRow['i_status']);
                $symbol = $currencies[$aRow['s_currency_code']]['s_symbol'];
                $subtotal = ShopCurrency::newInstance()->format_price($aRow['i_amount'], $symbol);
                $tax = ShopCurrency::newInstance()->format_price($aRow['i_amount_tax'], $symbol);
                $total = ShopCurrency::newInstance()->format_price($aRow['i_amount_total'], $symbol);
                $items = array_column($aRow['rows'], 's_concept');
                $items = implode('<br />', $items);
                $row['status'] = ModelPaymentPro::newInstance()->status($aRow['i_status']);
                $row['date'] = $aRow['dt_date'];
                $row['items'] = $items;
                $row['subtotal'] = $subtotal;
                $row['tax'] = $tax;
                $row['total'] = $total;
                if ($aRow['i_status'] == PAYMENT_PRO_COMPLETED) {
                    $row['action'] = $this->_printLink($row, $aRow);
                }

                $row = osc_apply_filter('shop_account_processing_row', $row, $aRow);

                $this->addRow($row);
                $this->rawRows[] = $aRow;
            }
        }
    }

    private function _printLink($row, $aRow) {
        $params = array();
        $params['company']['name'] = getPreference('companyName', 'shop');
        $params['company']['address1'] = getPreference('companyAddress1', 'shop');
        $params['company']['address2'] = getPreference('companyAddress2', 'shop');
        $params['company']['address3'] = getPreference('companyAddress3', 'shop');
        $params['company']['address4'] = getPreference('companyAddress4', 'shop');
        $params['company']['address5'] = getPreference('companyAddress5', 'shop');
        $params['company']['taxno'] = getPreference('companyTaxNo', 'shop');

        if (isset($aRow['order'])) {
            $params['customer']['name'] = $aRow['order']['s_name'];
            $params['customer']['address'] = $aRow['order']['s_address'];
        }
            
        $params['invoice']['items'] = $row['items'];
        $params['invoice']['number'] = $aRow['s_code'];
        $params['invoice']['date'] = $aRow['dt_date'];
        $taxes = preg_replace('/[^0-9]/', '', $row['tax']);

        if (osc_get_preference('show_taxes', 'payment_pro') == 1 || $taxes > 0) {
            $params['invoice']['subtotal'] = $row['subtotal'];
            $params['invoice']['taxes'] = $row['tax'];
        }
        $params['invoice']['currency'] = $aRow['s_currency_code'];
        $params['invoice']['total'] = $row['total'];

        $json = json_encode($params);
        return '<button class="btn btn-success" onclick="javascript:printInvoice(\'' . urlencode($json) . '\');">' . __('Print', 'shop') . '</button>';
    }

}
