<?php if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

require_once __DIR__ . '/../class/ShopOrdersDataTable.php';

if(Params::getParam('status')==='*') {
    Params::setParam('status', '');
}

switch (Params::getParam('function')) {
    case 'downloads':
        $invoiceId = Params::getParam('id');
        ShopDownloads::newInstance()->resetLimit($invoiceId);
        break;
    case 'despatched':
        $invoiceId = Params::getParam('id');
        ShopOrders::newInstance()->setOrderStatus($invoiceId, SHOP_ORDER_DESPATCHED);
        break;
    case 'waiting':
        $invoiceId = Params::getParam('id');
        ShopOrders::newInstance()->setOrderStatus($invoiceId, SHOP_ORDER_AWAITING_STOCK);
        break;
    case 'paid':
        $invoiceId = Params::getParam('id');
        ShopOrders::newInstance()->setOrderStatus($invoiceId, SHOP_ORDER_PAID);
        break;
}

if( Params::getParam('iDisplayLength') != '' ) {
    Cookie::newInstance()->push('listing_iDisplayLength', Params::getParam('iDisplayLength'));
    Cookie::newInstance()->set();
} else {
    // set a default value if it's set in the cookie
    $listing_iDisplayLength = (int) Cookie::newInstance()->get_value('listing_iDisplayLength');
    if ($listing_iDisplayLength == 0) $listing_iDisplayLength = 10;
    Params::setParam('iDisplayLength', $listing_iDisplayLength );
}

$page  = (int)Params::getParam('iPage');
if($page==0) { $page = 1; };
Params::setParam('iPage', $page);

$params = Params::getParamsAsArray();

$ordersDataTable = new ShopOrdersDataTable();
$ordersDataTable->table($params);
$aData = $ordersDataTable->getData();
View::newInstance()->_exportVariableToView('aData', $aData);

if(count($aData['aRows']) == 0 && $page!=1) {
    $total = (int)$aData['iTotalDisplayRecords'];
    $maxPage = ceil( $total / (int)$aData['iDisplayLength'] );

    $url = osc_admin_base_url(true).'?'.$_SERVER['QUERY_STRING'];

    if($maxPage==0) {
        $url = preg_replace('/&iPage=(\d)+/', '&iPage=1', $url);
        ob_get_clean();
        osc_redirect_to($url);
    }

    if($page > $maxPage) {
        $url = preg_replace('/&iPage=(\d)+/', '&iPage='.$maxPage, $url);
        ob_get_clean();
        osc_redirect_to($url);
    }
}

$columns    = $aData['aColumns'];
$rows       = $aData['aRows'];

?>
<style>
     /* overlay */
    .overlay {
        position:absolute;
        top:0;
        left:0;
        right:0;
        bottom:0;
        background-color:rgba(255, 255, 255, 0.55);
        background: url(data:;base64,iVBORw0KGgoAAAANSUhEUgAAAAIAAAACCAYAAABytg0kAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAgY0hSTQAAeiYAAICEAAD6AAAAgOgAAHUwAADqYAAAOpgAABdwnLpRPAAAABl0RVh0U29mdHdhcmUAUGFpbnQuTkVUIHYzLjUuNUmK/OAAAAATSURBVBhXY2RgYNgHxGAAYuwDAA78AjwwRoQYAAAAAElFTkSuQmCC) repeat scroll transparent\9;
        z-index:9999;
        color:black;
    }
    .overlay {
        text-align: center;
        display: block;
    }

    .overlay:before {
        content: '';
        display: inline-block;
        height: 100%;
        vertical-align: middle;
        margin-right: -0.25em;
    }
    .col-subtotal,
    .col-tax,
    .col-total {
        text-align: right;
    }
    .col-bulkactions {
        display:none;
    }
</style>
<div class="relative">
    <p>
        <select id="filter-status" class="filter-orders" name="status">
            <option <?php echo (Params::getParam('status')==='') ? 'selected' : '';?> value="*"><?php _e('View all status', 'shop'); ?></option>
            <option <?php echo (Params::getParam('status')===(string)SHOP_ORDER_PENDING) ? 'selected' : '';?> value="<?php echo SHOP_ORDER_PENDING; ?>"><?php echo ShopOrders::newInstance()->status(SHOP_ORDER_PENDING); ?></option>
            <option <?php echo (Params::getParam('status')===(string)SHOP_ORDER_PAID) ? 'selected' : '';?> value="<?php echo SHOP_ORDER_PAID; ?>"><?php echo ShopOrders::newInstance()->status(SHOP_ORDER_PAID); ?></option>
            <option <?php echo (Params::getParam('status')===(string)SHOP_ORDER_DESPATCHED) ? 'selected' : '';?> value="<?php echo SHOP_ORDER_DESPATCHED; ?>"><?php echo ShopOrders::newInstance()->status(SHOP_ORDER_DESPATCHED); ?></option>
            <option <?php echo (Params::getParam('status')===(string)SHOP_ORDER_AWAITING_STOCK) ? 'selected' : '';?> value="<?php echo SHOP_ORDER_AWAITING_STOCK; ?>"><?php echo ShopOrders::newInstance()->status(SHOP_ORDER_AWAITING_STOCK); ?></option>
            <option <?php echo (Params::getParam('status')===(string)SHOP_ORDER_DOWNLOADED) ? 'selected' : '';?> value="<?php echo SHOP_ORDER_DOWNLOADED; ?>"><?php echo ShopOrders::newInstance()->status(SHOP_ORDER_DOWNLOADED); ?></option>
        </select>
    </p>
    <form class="table" id="datatablesForm" action="<?php echo osc_admin_base_url(true); ?>" method="post">
        <div class="table-contains-actions shadow-sm">
            <table class="table" cellpadding="0" cellspacing="0">
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
                        $row['status'] = osc_apply_filter('datatable_orders_status_text', $row['status']);
                         ?>
                        <tr class="<?php echo osc_apply_filter('datatable_orders_status_class',  $status); ?>">
                            <td class="col-status-border"></td>
                            <?php foreach($row as $k => $v) { ?>
                                <td class="col-<?php echo $k; ?>" data-col-name="<?php echo ucfirst($k); ?>"><?php echo $v; ?></td>
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
</div>
<div id="bulkActionsModal" class="modal fade static" tabindex="-1" aria-labelledby="bulkActionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkActionsModalLabel"><?php _e('Bulk actions'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal"><?php _e('Cancel'); ?></button>
                <button id="bulkActionsSubmit" onclick="bulkActionsSubmit()" class="btn btn-sm btn-red"><?php echo osc_esc_html(__('Delete')); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    $('.filter-orders').change( function create_url_log() {
        var new_url = '<?php echo osc_route_admin_url('shop-admin-orders'); ?>' ;
        var status  = $('#filter-status').val();
        var url_changed = false;
        if( status.length > 0 ) {
            new_url = new_url.concat("&status=" + status );
            url_changed = true;
        }
        if(url_changed) {
            $('#content-page').append('<div class="overlay"></div>');
            window.location.href = new_url;
        }
    });
</script>
<?php
function showingResults(){
    $aData = __get('aData');
    echo '<ul class="showing-results"><li><span>'.osc_pagination_showing((Params::getParam('iPage')-1)*$aData['iDisplayLength']+1, ((Params::getParam('iPage')-1)*$aData['iDisplayLength'])+count($aData['aRows']), $aData['iTotalDisplayRecords'], $aData['iTotalRecords']).'</span></li></ul>';
}
osc_add_hook('before_show_pagination_admin','showingResults');
osc_show_pagination_admin($aData);