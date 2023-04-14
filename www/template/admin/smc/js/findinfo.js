$(document).ready(function(){
    var addevent = function(){
        var $f = $('#email_resend');
        if($f.length<1) {
            setTimeout(function(){addevent();}, 200);
            return false;
        }
        $f.submit(function() {
            var chk_option = [
                { 'target':'email', 'name':'email', 'type':'blank', 'msg':change_lang('member12') }
            ];
            if(!jsForm(this,chk_option)) {
                return false;
            }		
            $(this).ajaxSubmit({
                success: function (data, statusText) {
                    if(data['bool']) {
                        fn_show_toast("success", __('member37'));
                    }
                    else {
                        if(data['msg'] == 'err_access') {
                            fn_show_toast("error", __('member38'));
                        }
                        else if(data['msg'] == 'err_notemail') {
                            fn_show_toast("error", __('member39'));
                        }
                        else if(data['msg'] == 'err_unexist') {
                            fn_show_toast("error", __('member40'));
                        }
                        else if(data['msg'] == 'err_mail') {
                            fn_show_toast("error", __('member41'));
                        }
                        else if(data['msg'] == 'err_sms') {
                            fn_show_toast("error", __('member42'));
                        }
                        else {
                            fn_show_toast("error", __('member43'));
                        }
                    }
                },
                dataType:'json',
                resetForm: true
            });
            return false;
        });
    };
    addevent();
});

if(getCookie('lang') == 'kr'){
    deneme('find_info_kr.html');
}else{
    deneme('find_info.html');
}