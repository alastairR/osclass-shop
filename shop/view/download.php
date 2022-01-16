<?php

require_once __DIR__ . '/../../../../oc-load.php';
$id = Params::getParam('id');
$secret = Params::getParam('secret');
$code = Params::getParam('code');
$download = ShopDownloads::newInstance()->getDownloadByItemSecret($id, $secret, $code);
$file = osc_get_preference('download_path', 'shop') . $download['s_name'];
$maxDownloads = osc_get_preference('maxDownloads', 'shop');
$downloadDays = osc_get_preference('downloadDays', 'shop');
$oldestDate = strtotime('- '.$downloadDays.' days');

if ((isset($download['pk_i_id']) && file_exists($file) 
        && $download['i_download_count'] < $maxDownloads
        && strtotime($download['dt_purchase']) >= $oldestDate)
        || empty($download['i_price'])) {
    $testing = osc_get_preference('testing', 'shop');
    if ($testing) {
        $file = osc_get_preference('download_path', 'shop') . 'dummy.xlsm';
    } 
    ShopDownloads::newInstance()->updateDownloads($download['fileId'], $download['i_downloads'] + 1);
    if ($download['fk_i_invoice_id']) {
        ShopDownloads::newInstance()->updateUserDownloads($download['pk_i_id'], array('i_download_count'=>$download['i_download_count'] + 1));
        ShopOrders::newInstance()->logOrder($download['fk_i_invoice_id'],__('Download','shop'),$download['s_name']);
        ShopOrders::newInstance()->setOrderStatus($download['fk_i_invoice_id'], SHOP_ORDER_DOWNLOADED);
    }
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    @ob_clean();
    flush();
    readfile($file);
    exit;
}
?>