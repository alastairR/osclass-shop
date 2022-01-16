<?php if (!defined('OC_ADMIN')) {
    exit('Direct access is not allowed.');
}

require_once __DIR__ . '/../class/ShopAccountDataTable.php';

// set default iDisplayLength
if (Params::getParam('iDisplayLength') != '') {
    Cookie::newInstance()->push('listing_iDisplayLength', Params::getParam('iDisplayLength'));
    Cookie::newInstance()->set();
} else {
    // set a default value if it's set in the cookie
    $listing_iDisplayLength = (int)Cookie::newInstance()->get_value('listing_iDisplayLength');
    if ($listing_iDisplayLength == 0) {
        $listing_iDisplayLength = 10;
    }
    Params::setParam('iDisplayLength', $listing_iDisplayLength);
}
View::newInstance()->_exportVariableToView('iDisplayLength', Params::getParam('iDisplayLength'));

// Table header order by related
if (Params::getParam('sort') == '') {
    Params::setParam('sort', 'date');
}
if (Params::getParam('direction') == '') {
    Params::setParam('direction', 'desc');
}
if (Params::getParam('status') == '') {
    Params::setParam('status', '');
}
if (Params::getParam('source') == '') {
    Params::setParam('source', '');
}

$page = (int)Params::getParam('iPage');
if ($page == 0) {
    $page = 1;
}
Params::setParam('iPage', $page);

$params = Params::getParamsAsArray();

$shopAccountDataTable = new ShopAccountDataTable();
$shopAccountDataTable->table($params);
$aData = $shopAccountDataTable->getData();

if (count($aData['aRows']) == 0 && $page != 1) {
    $total   = (int)$aData['iTotalDisplayRecords'];
    $maxPage = ceil($total / (int)$aData['iDisplayLength']);

    $url = osc_admin_base_url(true) . '?' . Params::getServerParam('QUERY_STRING', false, false);

    if ($maxPage == 0) {
        $url = preg_replace('/&iPage=(\d)+/', '&iPage=1', $url);
        Utils::redirectTo($url);
    }

    if ($page > 1) {
        $url = preg_replace('/&iPage=(\d)+/', '&iPage=' . $maxPage, $url);
        Utils::redirectTo($url);
    }
}


View::newInstance()->_exportVariableToView('aData', $aData);
View::newInstance()->_exportVariableToView('aRawRows', $shopAccountDataTable->rawRows());


$aData     = __get('aData');
$aRawRows  = __get('aRawRows');
$sort      = Params::getParam('sort');
$direction = Params::getParam('direction');

$columns = $aData['aColumns'];
$rows    = $aData['aRows'];

?>
<style>
    .col-subtotal,
    .col-tax,
    .col-total {
        text-align: right;
    }
</style>
    <h2 class="render-title"><?php echo __('Account'); ?></h2>
    <div class="relative">
        <div id="pages-toolbar" class="table-toolbar">
        </div>
        <form class="" id="datatablesForm" action="<?php echo osc_route_url('user-account',array('iPage'=>$page)); ?>" method="post">
            <input type="hidden" name="page" value="pages"/>
            <div class="table-contains-actions shadow-sm">
                <table class="table" cellpadding="0" cellspacing="0">
                    <thead>
                    <tr class="table-secondary">
                        <?php foreach ($columns as $k => $v) {
                            if ($direction === 'desc') {
                                echo '<th class="col-' . $k . ' ' . ($sort === $k ? ('sorting_desc') : '') . '">' . $v . '</th>';
                            } else {
                                echo '<th class="col-' . $k . ' ' . ($sort === $k ? ('sorting_asc') : '') . '">' . $v . '</th>';
                            }
                        } ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (count($rows) > 0) { ?>
                        <?php foreach ($rows as $key => $row) { ?>
                            <tr>
                                <?php foreach ($row as $k => $v) { ?>
                                    <td class="col-<?php echo $k; ?>" data-col-name="<?php echo ucfirst($k); ?>"><?php echo $v; ?></td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="4" class="text-center">
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
    <form method="POST" id="invoice" target="_blank" action="<?php echo osc_route_url('user-invoice-print'); ?>">
        <input type="hidden" name = "params" id="invoiceParams" value="" />
    </form>

<script>
    function printInvoice(data) {
        $('#invoiceParams').val(data);
        $('form#invoice').submit();
    }
</script>
<?php
function showingResults()
{
    $aData = __get('aData');
    echo '<ul class="showing-results"><li><span>' 
        . osc_pagination_showing((Params::getParam('iPage') - 1)
            * $aData['iDisplayLength'] + 1,
            ((Params::getParam('iPage') - 1) * $aData['iDisplayLength'])
            + count($aData['aRows']),
            $aData['iTotalDisplayRecords'], $aData['iTotalRecords'])
         . '</span></li></ul>';
}
