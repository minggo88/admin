
jQuery(function ($) {
	
    $('[name="language"]').on('change', function () { 
        const lang = $(this).val();
        window.location.href = setURLParameter('language', lang); 
    });

	$(".list_table>table>tbody>tr").hover(
		function () { $(this).css('background-color','#FFF2F0'); }, 
		function () { $(this).css('background-color','#FFF'); }
	);
});

function editForm(code) {
	location.href = '<!--{_SERVER.SCRIPT_NAME}-->?pg_mode=form_edit<!--{srch_url}-->&code='+code;
}

function listDel(code) {
	if(!confirm('삭제하시겠습니까?')) {
		return;
	}
	$.get('<!--{_SERVER.SCRIPT_NAME}-->?pg_mode=del&cts_code='+code,function(data) {
		if(data['bool']) {
			alert('삭제되었습니다.!');
			location.replace('<!--{_SERVER.REQUEST_URI}-->');
		}
		else {
			if(data['msg'] == 'err_access') {
				alert('비정상적인 접근입니다.');
			}
			else if(data['msg'] == 'err_sess') {
				//location.replace('/admin/auth.php?ret_url=<!--{=base64_encode(_SERVER.REQUEST_URI)}-->');
				location.replace('/member/memberAuth.php?ret_url=<!--{=base64_encode(_SERVER.REQUEST_URI)}-->');
			}
			else {
				alert('재시도 해주세요.!');
			}
		}
	},'json');
}


//테이블 드래그
jQuery(function($) {
	$("#list_table").tableDnD({
		onDragClass: "dndover "
	});
});

function saveRanking() {
	if(!confirm('이벤트 순서를 적용하시겠습니까?')) {
		return;
	}
	$.get('<!--{_SERVER.SCRIPT_NAME}-->?pg_mode=save_ranking<!--{srch_url}-->&'+$('#drag_table').tableDnDSerialize(),function(data) {
		if(data['bool']) {
			alert('순서가 저장되었습니다.!');
			location.replace('<!--{_SERVER.REQUEST_URI}-->');
		}
		else {
			if(data['msg'] == 'err_access') {
				alert('비정상적인 접근입니다.');
			}
			else if(data['msg'] == 'err_sess') {
				//location.replace('/admin/auth.php?ret_url=<!--{=base64_encode(_SERVER.REQUEST_URI)}-->');
				location.replace('/member/memberAuth.php?ret_url=<!--{=base64_encode(_SERVER.REQUEST_URI)}-->');
			}
			else {
				alert('재시도 해주세요.!');
			}
		}
	},'json');
}	

jQuery(function($) {
	$('#all_check').click(function() {
		var obj = $("#list_form>table>tbody input.check_code");
		if(this.checked) {
			obj.attr('checked','checked');
		}
		else {
			obj.removeAttr('checked');
		}
	});
});

function checkDel() {
	var cnt_checked = $('#list_form input.check_code:checked').length;
	if(cnt_checked == 0) {
		alert('선택하여 주세요.!');
		return false;
	}

	if(!confirm("삭제하시겠습니까?")) {
		return false;
	}
	var checked_val = $('#list_form').serialize();
	$.get('<!--{_SERVER.SCRIPT_NAME}-->?pg_mode=multi_del&'+checked_val, function(data) {
		if(data['bool']) {
			alert('삭제되었습니다.!');
			location.replace('<!--{_SERVER.REQUEST_URI}-->');
		}
		else {
			if(data['msg'] == 'err_access') {
				alert('비정상적인 접근입니다.');
			}
			else if(data['msg'] == 'err_sess') {
				//location.replace('/admin/auth.php?ret_url=<!--{=base64_encode(_SERVER.REQUEST_URI)}-->');
				location.replace('/member/memberAuth.php?ret_url=<!--{=base64_encode(_SERVER.REQUEST_URI)}-->');
			}
			else {
				alert('재시도 해주세요.!');
			}
		}
	},'json');
}
