function setCurrency(curr) {
    var dataString = {'function' : 'currency', 'currency' : curr};
    $.ajax({
        method: "POST",
        url: shop_url,
        data: dataString,
        dataType: 'JSON',
        cache: false,

        success: function (data) {
            var curr = document.getElementsByClassName('currency');
            var cart = document.getElementsByClassName('cart');
            if (curr.length > 0 ||cart.length > 0) {
                window.location.reload();
            }
        },
        error: function (request, status, error) {
            alert(status + ":" + error.message);
        }
    });
}

function cart_update (elem, incr = null) {
    var id = $(elem).attr("data-id");
    var desc = $(elem).attr("data-desc");
    var price = $(elem).attr("data-price");
    var shopList = $('.shop-list'); // element cart total
    var cart = $('.cart-list'); // element containing all cart items
    var cartEmpty = $('.cart-empty'); // element containing all cart items
    var cartCount = $('.cart-count'); // count on cart button
    var sibling = $(elem).parent().siblings('.cart_qty');
    if (!incr) {
        sibling = $(elem).siblings('.cart_upd');
        $(elem).fadeOut(300);
    } else {
        if (incr == '-' && sibling.text() == '1') {
            $(elem).parent().parent().fadeOut(300);
            incr = null;
            sibling = $(elem).parent().parent().siblings('.cart_add');
        }
    }
    var dataString = {'function' : 'cart', 'incr' : incr, 'id': id, 'price': price, 'desc': desc};
    $.ajax({
        method: "POST",
        url: shop_url,
        data: dataString,
        dataType: 'JSON',
        cache: false,

        success: function (data) {
            window.location.reload();
        },
        error: function (request, status, error) {
            alert(status + ":" + error);
        }
    });
}

function checkout_address (elem, select = null) {
    var opt = elem.value;
    var dataString = {'function' : 'checkout', 'select' : select, 'option': opt};
    $.ajax({
        method: "POST",
        url: shop_url,
        data: dataString,
        dataType: 'JSON',
        cache: false,

        success: function (data) {
            window.location.reload();
        },
        error: function (request, status, error) {
            alert(status + ":" + error);
        }
    });
}

jQuery(document).ready(function ($) {
    $(".cart_add").click( function() { cart_update(this); });
    $(".cart_del").click( function() { cart_update(this, '0'); });
    $(".cart_item").click( function() { cart_update(this); });
    $(".cart_incr").click( function() { cart_update(this,'+'); });
    $(".cart_decr").click( function() { cart_update(this,'-'); });
    $("#delivery").on('change', function() { checkout_address(this, 'delivery'); });
    $("#billing").on('change', function() { checkout_address(this, 'billing'); });
    $(".shop-currency .dropdown-menu li a").click(function(){
      var desc = $(this).text();
      var curr = $(this).data('value');
      $(this).parents(".dropdown").find(".default-currency").html('<i class="fa fa-money fa-fw" aria-hidden="true"></i> ' + desc + ' <span class="caret"></span>');
      $(this).parents(".dropdown").find(".default-currency").val(curr);
      setCurrency(curr);
    });
});
