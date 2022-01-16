<?php
$currencies = ShopCurrency::newInstance()->getAll();
$default_currency = ShopCurrency::newInstance()->getDefault();
?>
<?php foreach ($currencies as $currency) { ?>
    <?php
    if (Session::newInstance()->_getForm('pre_'.$currency['pk_c_code'].'price') != '') {
        $item['currency'][$currency['pk_c_code']] = Session::newInstance()->_getForm('pre_'.$currency['pk_c_code'].'price');
    }
    $converted = 0;
    if (count($item) != 0) {
        $itemCurr = $item['fk_c_currency_code'];
        if (empty($itemCurr)) {
            $itemCurr = $default_currency;
        }
        if ($currency['pk_c_code'] == $itemCurr) {
            $price = '';
            $disabled = 'disabled';
        } else {
            $price = 0;
            if (isset($item['currency'][$currency['pk_c_code']])) {
                $price = osc_prepare_price($item['currency'][$currency['pk_c_code']]);
            }
            $disabled = '';
        }
        $converted = ShopCurrency::newInstance()->convert($item['fk_c_currency_code'],$item['i_price'], $currency['pk_c_code']);
    }
    $prompt = ShopCurrency::newInstance()->format_price($converted, $currencies[$currency['pk_c_code']]['s_description']);
    ?>
    <label id="<?php echo $currency['pk_c_code']; ?>-prompt" for="<?php echo $currency['pk_c_code']; ?>-price"><?php echo $prompt; ?></label>
    <div class="price input-group input-group-sm">
        <input type="text" name="<?php echo $currency['pk_c_code']; ?>-price" class="form-control form-control-sm"
               id="<?php echo $currency['pk_c_code']; ?>-price" 
               value="<?php echo $price; ?>"
               <?php echo $disabled; ?> />
    </div>
<?php } ?>
<style>
    #plugin-hook {
        padding: 0px;
        background: inherit;
        border: none;
    }
</style>
<script type="text/javascript">
function currency_change () {
    var from = $("#currency option").filter(':selected').val();
    var amount = $("#price").val();
    
    var dataString = {'function' : 'convert', 'from' : from, 'amount': amount};
    $.ajax({
        method: "POST",
        url: shop_url,
        data: dataString,
        dataType: 'JSON',
        cache: false,

        success: function (data) {
            $.each(data.currencies, function(key, value) {
                var id = '#' + key + '-prompt';
                $('#' + key + '-prompt').text(value.formatted);
                $('#' + key + '-price').prop("disabled",false);
            });
            $('#' + data.from + '-price').prop("disabled",true);
        },
        error: function (request, status, error) {
            alert(status + ":" + error);
        }
    });
}

jQuery(document).ready(function ($) {
    $("#price").change( function() { currency_change(); });
    $("#currency").change( function() { currency_change(); });
});
</script>