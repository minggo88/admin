
jQuery(function ($) {
    var $f = $('#reset_pw');
    $f.submit(function () {
        var chk_option = [
            { 'target': 'userpw', 'name': __('bbs12'), 'type': 'passwd', 'msg': __('member13') }
        ];
        if (!jsForm(this, chk_option)) {
            return false;
        }
        if (this.userpw.value.length < 8) {
            fn_show_toast("warning", __('member17'));
            $('#userpw').focus();
            return false;
        }
        $(this).ajaxSubmit({
            success: function (data, statusText) {
                if (data['bool']) {
                    fn_show_toast("success", __('member66') + ' ' + __('member67'));
                    setTimeout(function () { window.location.replace('/login'); }, 2000);
                } else {
                    var msg = data['msg'] != '' ? data['msg'] : __('member68');
                    fn_show_toast("error", msg);
                }
            },
            dataType: 'json',
            resetForm: true
        });
        return false;
    });
})