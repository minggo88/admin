
deneme('join_clause.html');

function checkAgree() {
	var bool_agreement = $('#ok_agreement')[0].checked;
	var bool_private = $('#ok_private')[0].checked;
	if(!bool_agreement || !bool_private) {
        fn_show_toast("warning", change_lang("member25"));
		return false;
	}
	else {
		location.href ='/formjoin';
	}
}