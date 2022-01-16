<?php if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

require_once PAYMENT_PRO_PATH . 'CheckoutInvoicesDataTable.php';
require_once PAYMENT_PRO_EXT_PATH . "InvoicesDataTable.php";

Params::setParam('userid', osc_logged_user_id());

if( Params::getParam('iDisplayLength') != '' ) {
    Cookie::newInstance()->push('listing_iDisplayLength', Params::getParam('iDisplayLength'));
    Cookie::newInstance()->set();
} else {
    // set a default value if it's set in the cookie
    $listing_iDisplayLength = (int) Cookie::newInstance()->get_value('listing_iDisplayLength');
    if ($listing_iDisplayLength == 0) $listing_iDisplayLength = 5;
    Params::setParam('iDisplayLength', $listing_iDisplayLength );
}

$page  = (int)Params::getParam('iPage');
if($page==0) { $page = 1; };
Params::setParam('iPage', $page);

$params = Params::getParamsAsArray();
$wallet = ModelPaymentPro::newInstance()->getWallet(osc_logged_user_id());

$invoicesDataTable = new InvoicesDataTable();
$invoicesDataTable->table($params);
$aData = $invoicesDataTable->getData();
View::newInstance()->_exportVariableToView('aData', $aData);
$itemsPerPage = (Params::getParam('itemsPerPage')!='')?Params::getParam('itemsPerPage'):5;
$page         = (Params::getParam('iPage') > 0) ? Params::getParam('iPage') -1 : 0;
$total_items  = count($aData['aRows']);
$total_pages  = ceil($total_items/$itemsPerPage);
View::newInstance()->_exportVariableToView('search_total_pages', $total_pages);
View::newInstance()->_exportVariableToView('list_total_items', $total_items);
View::newInstance()->_exportVariableToView('items_per_page', $itemsPerPage);
View::newInstance()->_exportVariableToView('list_page', $page);

if(count($aData['aRows']) == 0 && $page!=1) {
    $total = (int)$aData['iTotalDisplayRecords'];
    $maxPage = ceil( $total / (int)$aData['iDisplayLength'] );

    $url = osc_route_url('payment-pro-user-invoices').'?'.$_SERVER['QUERY_STRING'].'&iPage=(\d)+/';

    if($page > $maxPage) {
        $url = preg_replace('/&iPage=(\d)+/', '&iPage='.$maxPage, $url);
        ob_get_clean();
        osc_redirect_to($url);
    }
}

$columns    = $aData['aColumns'];
$rows       = $aData['aRows'];

?>
<div class="relative">
    <?php if (osc_count_items() == 0) { ?>
        <h3><?php _e('You don\'t have any invoices yet', 'shop'); ?></h3>
        <?php } else { ?>

    <div class="user-balance">
        <?php echo __('Current credit total: ') . osc_format_price($wallet['i_amount'],'$'); ?>
    </div>
    <p>
        <select id="filter-status" class="filter-log" name="status">
            <option <?php echo (Params::getParam('status')==='') ? 'selected' : '';?> value=""><?php _e('View all status', 'shop'); ?></option>
            <option <?php echo (Params::getParam('status')===(string)PAYMENT_PRO_FAILED) ? 'selected' : '';?> value="<?php echo PAYMENT_PRO_FAILED; ?>"><?php echo $invoicesDataTable->_status(PAYMENT_PRO_FAILED); ?></option>
            <option <?php echo (Params::getParam('status')===(string)PAYMENT_PRO_COMPLETED) ? 'selected' : '';?> value="<?php echo PAYMENT_PRO_COMPLETED; ?>"><?php echo $invoicesDataTable->_status(PAYMENT_PRO_COMPLETED); ?></option>
            <option <?php echo (Params::getParam('status')===(string)PAYMENT_PRO_PENDING) ? 'selected' : '';?> value="<?php echo PAYMENT_PRO_PENDING; ?>"><?php echo $invoicesDataTable->_status(PAYMENT_PRO_PENDING); ?></option>
            <option <?php echo (Params::getParam('status')===(string)PAYMENT_PRO_ALREADY_PAID) ? 'selected' : '';?> value="<?php echo PAYMENT_PRO_ALREADY_PAID; ?>"><?php echo $invoicesDataTable->_status(PAYMENT_PRO_ALREADY_PAID); ?></option>
            <option <?php echo (Params::getParam('status')===(string)PAYMENT_PRO_WRONG_AMOUNT_TOTAL) ? 'selected' : '';?> value="<?php echo PAYMENT_PRO_WRONG_AMOUNT_TOTAL; ?>"><?php echo $invoicesDataTable->_status(PAYMENT_PRO_WRONG_AMOUNT_TOTAL); ?></option>
            <option <?php echo (Params::getParam('status')===(string)PAYMENT_PRO_WRONG_AMOUNT_ITEM) ? 'selected' : '';?> value="<?php echo PAYMENT_PRO_WRONG_AMOUNT_ITEM; ?>"><?php echo $invoicesDataTable->_status(PAYMENT_PRO_WRONG_AMOUNT_ITEM); ?></option>
        </select>
    </p>
    <form id="datatablesForm" action="<?php echo osc_base_url(true); ?>" method="post">
        <div class="table-contains-actions">
            <table id="invoices" class="table" cellpadding="0" cellspacing="0">
                <thead>
                <tr>
                    <th class="col-status-border"></th>
                    <?php foreach($columns as $k => $v) {
                        echo '<th class="col-'.$k.' ">'.$v.'</th>';
                    }; ?>
                </tr>
                </thead>
                <tbody>
                <?php if( count($rows) > 0 ) { ?>
                    <?php foreach($rows as $key => $row) {
                        $status = $row['status'];
                        $row['status'] = osc_apply_filter('datatable_payment_log_status_text', $row['status']);
                         ?>
                        <tr class="<?php echo osc_apply_filter('datatable_payment_log_status_class',  $status); ?>">
                            <td class="col-status-border"></td>
                            <?php foreach($row as $k => $v) { ?>
                                <td class="col-<?php echo $k; ?>"><?php echo $v; ?></td>
                            <?php }; ?>
                        </tr>
                    <?php }; ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="<?php echo count($columns)+1; ?>" class="text-center">
                            <p><?php _e('No data available in table'); ?></p>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <div id="table-row-actions"></div> <!-- used for table actions -->
        </div>
    </form>
    <form method="POST" id="invoice" target="_blank" action="<?php echo osc_plugin_url(__FILE__); ?>invoice_print.php">
        <input type="hidden" name = "params" id="invoiceParams" value="" />
    </form>
<?php } ?>
</div>

<script>
    function printInvoice(data) {
        $('#invoiceParams').val(data);
        $('form#invoice').submit();
    }
    $('.filter-log').change( function create_url_log() {
        var new_url = '<?php echo osc_route_url('payment-pro-user-invoices'); ?>' ;
        var status  = $('#filter-status').val();
        new_url = new_url.concat("?status=" + status );
        $('#content-page').append('<div class="overlay"></div>');
        window.location.href = new_url;
    });
</script>
<?php
$params = array(
    'total' => $total_pages,
    'selected' => $page,
    'list_class' => 'paginate',
    'url' => osc_route_url('payment-pro-user-invoices', array('iPage' => '{PAGE}'))
);
echo osc_pagination($params);