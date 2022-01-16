<!-- structechnique MODAL -->
<div class="modal fade" id="modalAddress" tabindex="-1" role="dialog" aria-labelledby="modalLabelAddress">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel" align="center"><i class="fa fa-envelope" aria-hidden="true"></i> <?php _e('Edit address', 'structechnique'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="modalAddress-form">
                <input type="hidden" id="txId" value = "">
                <input type="hidden" id="txAid" value = "">
                <input type="hidden" id="txCode" value = "">
                    <div class="form-group">
                        <label for="seType" class="control-label"><?php _e('Type','structechnique'); ?></label>
                        <div class="form-controls">
                            <select id="seType">
                                <option value="PRIMARY"><?php _e("Primary", 'structechnique'); ?></option>
                                <option value="BILLING"><?php _e("Billing", 'structechnique'); ?></option>
                                <option value="DELIVERY"><?php _e("Delivery", 'structechnique'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txName" class="control-label"><?php _e('Contact name','structechnique'); ?></label>
                        <div class="form-controls">
                            <input type="text" id="txName">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txCompany" class="control-label"><?php _e('Company name','structechnique'); ?></label>
                        <div class="form-controls">
                            <input type="text" id="txCompany">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txCountry" class="control-label"><?php _e('Country','structechnique'); ?></label>
                        <div class="form-controls">
                            <input type="text" id="txCountry">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txAddress" class="control-label"><?php _e('Address','structechnique'); ?></label>
                        <div class="form-controls">
                            <input type="text" id="txAddress">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txSuburb" class="control-label"><?php _e('Suburb','structechnique'); ?></label>
                        <div class="form-controls">
                            <input type="text" id="txSuburb">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txCity" class="control-label"><?php _e('City','structechnique'); ?></label>
                        <div class="form-controls">
                            <input type="text" id="txCity">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txState" class="control-label"><?php _e('State','structechnique'); ?></label>
                        <div class="form-controls">
                            <input type="text" id="txState">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txPostcode" class="control-label"><?php _e('Postcode','structechnique'); ?></label>
                        <div class="form-controls">
                            <input type="text" id="txPostcode">
                        </div>
                    </div>
                    <input type="hidden" class="form-control" id="txUserId" value="<?php echo osc_logged_user_id(); ?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close"><?php _e('Close', 'structechnique'); ?></button>
                <button type="submit" id="btSubmit" class="btn btn-primary" data-dismiss="modal"><?php _e('Save', 'structechnique'); ?></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
function address_update (shop_url, dataString) {
    $.ajax({
        method: "POST",
        url: shop_url,
        data: dataString,
        dataType: 'JSON',
        cache: false,

        success: function (data) {
          if(data.success) { window.location.reload(); }
          else { alert(data.msg); };
        },
        error: function (request, status, error) {
            alert(status + ":" + error);
        }
    });
}
    
$(document).ready(function() {
    $(".address_delete").click( function() { 
        var aid = $(this).data('aid');
        var id = $(this).data('id');
        var addrCode = $(this).data('code');
        var dataString = {'function' : 'address',  'process': 'd', id: id, aid:aid, code:addrCode}
        address_update(shop_url, dataString);        
    });
    $("#modalAddress").on('show.bs.modal', function (e) {
        $(this).removeData('bs.modal');
        var aid = $(e.relatedTarget).data('aid');
        var id = $(e.relatedTarget).data('id');
        var addrCode = $(e.relatedTarget).data('code');
        $("#txAid").val(aid);
        $("#txId").val(id);
        $("#txCode").val(addrCode);
        var addr = $(e.relatedTarget).data('addr');
        if (typeof addr !== 'undefined') {
            var $select = $("#seType").selectize();
            var selectize = $select[0].selectize;
            selectize.setValue(addr.typ);
            $("#txName").val(addr.nam);
            $("#txCompany").val(addr.com);
            $("#txCountry").val(addr.cou);
            $("#txAddress").val(addr.add);
            $("#txSuburb").val(addr.sub);
            $("#txCity").val(addr.cit);
            $("#txState").val(addr.sta);
            $("#txPostcode").val(addr.pos);
        }
    });
    $(document).on('click', '#btSubmit', function() {
        var addrType = $("#seType").find(":selected").val();
        var id = $("#txId").val();
        var name = $("#txName").val();
        var company = $("#txCompany").val();
        var country = $("#txCountry").val();
        var address = $("#txAddress").val();
        var suburb = $("#txSuburb").val();
        var city = $("#txCity").val();
        var state = $("#txState").val();
        var postcode = $("#txPostcode").val();
        var aid = $("#txAid").val();
        var addrCode = $("#txCode").val();
        var dataString = {'function' : 'address', 'process': 'u', id: id, aid:aid, code:addrCode,
                            addrType: addrType, name: name, company: company, 
                            country: country, address: address, suburb: suburb, 
                            city: city, state: state, postcode: postcode };
        address_update(shop_url, dataString);
    });
});
</script>
