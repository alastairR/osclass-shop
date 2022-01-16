<label><?php _e('Downloads','shop'); ?></label>
<div class="" style="background-color: #f8f9fa;
border: solid 1px #e9ecef;
padding: 10px;
border-radius: 4px;">
<p><?php printf(__('Allowed extensions are %s. Any other file will not be uploaded', 'shop'), osc_get_preference('allowed_ext', 'shop')) ; ?></p>
<?php
$max_files = osc_get_preference('max_files', 'shop');
if($files != null && is_array($files) && count($files) > 0) {
    foreach($files as $_r) { ?>
        <div id="<?php echo $_r['pk_i_id'] ; ?>" fkid="<?php echo $_r['fk_i_item_id'];?>" name="<?php echo $_r['s_name'];?>">
            <p><?php echo $_r['s_name'] ; ?> <a href="javascript:delete_download_file(<?php echo $_r['pk_i_id'] . ", " . $_r['fk_i_item_id'] . ", '" . $_r['s_code'] . "', '" . $secret . "'" ;?>);"  class="delete"><?php _e('Delete', 'shop') ; ?></a></p>
        </div>
    <?php }
} ?>
<div id="shop_files">
    <?php 
    $hidden = false;
    if($max_files != 0 && count($files) >= $max_files) { 
        $hidden = "display: none;";
    } ?>
    <div class="row file-browse" style="<?php echo $hidden; ?>">
        <input type="file" name="shop_files[]" />
    </div>
</div>
<a href="#" onclick="addNewDownload(); return false;" title="<?php _e('Add new file', 'shop') ; ?>">
    <i class="h4 text-success bi bi-plus-circle-fill"></i>
</a>
</div>
<script type="text/javascript">
var dgIndex = 0;
function gebi(id) { return document.getElementById(id); }
function ce(name) { return document.createElement(name); }
function re(id) {
    var e = gebi(id);
    e.parentNode.removeChild(e);
}
function addNewDownload() {
    var max = <?php echo osc_get_preference('max_files', 'shop'); ?>;
    var num_img = $('input[name="shop_files[]"]').length + $("a.delete").length;
    if((max!=0 && num_img<max) || max==0) {
        var id = 'p-' + dgIndex++;

        var i = ce('input');
        i.setAttribute('type', 'file');
        i.setAttribute('name', 'shop_files[]');

        var a = ce('a');
        a.style.fontSize = 'x-small';
        a.style.paddingLeft = '10px';
        a.setAttribute('href', '#');
        a.setAttribute('divid', id);
        a.onclick = function() { re(this.getAttribute('divid')); return false; }
        a.appendChild(document.createTextNode('<?php _e('Remove'); ?>'));

        var d = ce('div');
        d.setAttribute('id', id);
        d.setAttribute('style','padding: 4px 0;')

        d.appendChild(i);
        d.appendChild(a);

        gebi('shop_files').appendChild(d);

        $("#"+id+" input:file").uniform();
    } else {
        alert('<?php _e('Sorry, you have reached the maximum number of files per ad'); ?>');
    }
}

setInterval("add_file_field()", 250);

function add_file_field() {
    var count = 0;
    $('input[name="shop_files[]"]').each(function(index) {
        if ( $(this).val() == '' ) {
            count++;
        }
    });
    var max = <?php echo osc_get_preference('max_files', 'shop'); ?>;
    var num_img = $('input[name="shop_files[]"]').length + $("a.delete").length;
    if (count == 0 && (max==0 || (max!=0 && num_img<max))) {
        addNewDownload();
    }
}

function delete_download_file(id, item_id, code, secret) {
    var result = confirm('<?php echo __('This action can\\\'t be undone. Are you sure you want to continue?', 'shop'); ?>');
    if(result) {
        var dataString = {'function' : 'downloads', 'id' : id,'item_id' : item_id, 'code' : code, 'secret' : secret};
        $.ajax({
            type: "POST",
            url: shop_url,
            data: dataString,
            dataType: 'json',
            success: function(data){
                var class_type = "error";
                if(data.success) {
                    $("div[id="+id+"]").remove();
                    $(".file-browse").css('display','');
                    class_type = "ok";
                }
                var flash = $("#flash_js");
                var message = $('<div>').addClass('pubMessages').addClass(class_type).attr('id', 'FlashMessage').html(data.msg);
                flash.html(message);
                $("#FlashMessage").slideDown('slow').delay(3000).slideUp('slow');
            }
        });
    }
}
</script>