<?php
require_once('initialize.php');
?>
<style>
    #uni_modal .modal-content>.modal-footer,#uni_modal .modal-content>.modal-header{
        display:none;
    }
</style>
<div class="container-fluid">
    <form action="" id="verify-form">
        <div class="row">
            <h3 class="text-center">Email Verification
                <span class="float-right">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </span>
            </h3>
            <hr>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label for="" class="control-label">Enter OTP</label>
                    <input type="text" class="form-control form-control-sm form" name="otp" required>
                    <small class="text-muted">Please enter the OTP sent to your email address.</small>
                </div>
                <div class="form-group d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" id="resend-otp">Resend OTP</button>
                    <button class="btn btn-primary btn-flat">Verify</button>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    $(function(){
        $('#verify-form').submit(function(e){
            e.preventDefault();
            start_loader();
            
            $.ajax({
                url:_base_url_+"classes/Master.php?f=verify_email",
                method:"POST",
                data:$(this).serialize(),
                dataType:"json",
                error:err=>{
                    console.log(err);
                    var _err_el = $('<div>')
                        _err_el.addClass("alert alert-danger err-msg").text("An error occurred: " + err.responseText)
                    $('#verify-form').prepend(_err_el)
                    end_loader()
                },
                success:function(resp){
                    if(typeof resp == 'object' && resp.status == 'success'){
                        alert_toast("Email verified successfully",'success')
                        setTimeout(function(){
                            $(document).trigger('email_verified');
                            $('.modal').modal('hide');
                            uni_modal("Create New Account","registration.php");
                        },2000)
                    }else if(resp.status == 'failed' && !!resp.msg){
                        var _err_el = $('<div>')
                            _err_el.addClass("alert alert-danger err-msg").text(resp.msg)
                        $('#verify-form').prepend(_err_el)
                        end_loader()
                    }else{
                        var _err_el = $('<div>')
                            _err_el.addClass("alert alert-danger err-msg").text("An error occurred: " + JSON.stringify(resp))
                        $('#verify-form').prepend(_err_el)
                        end_loader()
                    }
                }
            })
        })

        $('#resend-otp').click(function(){
            start_loader();
            $.ajax({
                url:_base_url_+"classes/Master.php?f=resend_otp",
                method:"POST",
                dataType:"json",
                error:err=>{
                    console.log(err);
                    var _err_el = $('<div>')
                        _err_el.addClass("alert alert-danger err-msg").text("An error occurred: " + err.responseText)
                    $('#verify-form').prepend(_err_el)
                    end_loader()
                },
                success:function(resp){
                    if(typeof resp == 'object' && resp.status == 'success'){
                        alert_toast("OTP resent successfully",'success')
                    }else if(resp.status == 'failed' && !!resp.msg){
                        var _err_el = $('<div>')
                            _err_el.addClass("alert alert-danger err-msg").text(resp.msg)
                        $('#verify-form').prepend(_err_el)
                    }
                    end_loader()
                }
            })
        })
    })
</script> 