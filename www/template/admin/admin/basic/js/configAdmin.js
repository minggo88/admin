function adminAjaxSubmit(obj,tabs_idx) {
	obj.ajaxSubmit({
		success: function (data, statusText) {
			if(data['bool']) {
				alert('저장되었습니다.!');
				location.replace('?tabs_idx='+tabs_idx);
			}
			else {
				if(data['msg'] == 'err_access') {
					alert('비정상적인 접근입니다.');
				}
				else if(data['msg'] == 'err_old_pw') {
					alert('기존비밀번호를 입력하여 주세요.!');
				}
				else if(data['msg'] == 'err_new_pw') {
					alert('신규비밀번호를 입력하여 주세요.!');
				}
				else if(data['msg'] == 'err_sess') {
					location.replace('/admin/auth.php?ret_url='+base64_encode(window.location.href));
				}
				else {
					alert('재시도 해주세요.!');
				}
			}
		},
		dataType:'json',
		resetForm: false
	});
}

$(document).ready(function(){

	$('#jsform1').submit(function() {
		if(this.bool_passwd.checked) {
			var chk_option = [
				{ 'target':'admin_mobile', 'name':'관리자 휴대전화', 'type':'blank', 'msg':'관리자 휴대전화를 입력하여 주세요.!' },
				{ 'target':'old_passwd', 'name':'기존비밀번호', 'type':'blank', 'msg':'기존비밀번호를 입력하여 주세요.!' },
				{ 'target':'new_passwd', 'name':'신규비밀번호', 'type':'blank', 'msg':'신규비밀번호를 입력하여 주세요.!' },
				{ 'target':'renew_passwd', 'name':'신규비밀번호', 'type':'blank', 'msg':'신규비밀번호를 다시 한번 입력하여 주세요.!' },
				{ 'target':'new_passwd', 'target2':'renew_passwd', 'name':'비밀번호', 'type':'eq_check', 'msg':'비밀번호를 동일하게 입력하여 주세요.!' }
			];
		}
		else {
			var chk_option = [
				{ 'target':'admin_mobile', 'name':'관리자 휴대전화', 'type':'blank', 'msg':'관리자 휴대전화를 입력하여 주세요.!' }
			];
		}
		if(!jsForm(this,chk_option)) {
			return false;
		}
		if(!confirm('저장하시겠습니까?')) {
			return false;
		}
		adminAjaxSubmit($(this),this.tabs_idx.value);
		return false;
	});

	$('#jsform2').submit(function() {
		var chk_option = [
			{ 'target':'admin_name', 'name':'관리자명', 'type':'blank', 'msg':'관리자명을 입력하여 주세요.!' },
			{ 'target':'adminid', 'name':'아이디', 'type':'blank', 'msg':'아이디를 입력하여 주세요.!' }
		];
		if(!jsForm(this,chk_option)) {
			return false;
		}
		if(!confirm('저장하시겠습니까?')) {
			return false;
		}
		adminAjaxSubmit($(this),this.tabs_idx.value);
		return false;
	});
});

function setEditForm(adminid) {
	$.get('?pg_mode=get_value&adminid=' + adminid, function (data) {
		if(data['bool']!=0) {
			var arr_checkbox_radio = ['right_basic','right_member','right_goods','right_order','right_wallet','right_community','right_cs','right_auction','right_statistics','right_point','right_video','right_shareholder'];
			$.each(data, function(i, v) {
				if($.inArray(i,arr_checkbox_radio) > -1) {
					$('input[name=' + i+']').val([v]);
				}
				else {
					$('input[name=' + i+']').val(v);
				}
			});
			$('#pg_mode').val('edit');
		}
		else {
			if(data['msg'] == 'err_access') {
				alert('비정상적인 접근입니다.');
			}
			else if(data['msg'] == 'err_sess') {
				location.replace('/admin/auth.php?ret_url='+base64_encode(window.location.href));
			}
			else {
				alert('재시도 해주세요.!');
			}
		}
	},'json');
}

function adminDel(adminid) {
	if(!confirm('선택하신 관리자를 삭제하시겠습니까?')) {
		return false;
	}
	$.get('?pg_mode=del&adminid='+adminid,function(data) {
		if(data['bool']) {
			alert('삭제되었습니다.');
			location.replace('?tabs_idx=1');
		}
		else {
			if(data['msg'] == 'err_access') {
				alert('비정상적인 접근입니다.');
			}
			else if(data['msg'] == 'err_sess') {
				location.replace('/admin/auth.php?ret_url='+base64_encode(window.location.href));
			}
			else {
				alert('재시도 해주세요.!');
			}
		}
	},'json');
}

$(document).ready(function(){
	$('ul.tab_menu>li').click(function(event){
		$('ul.tab_menu>li').removeClass("selected");
		$(this).addClass("selected");
		$('div.tab_container>div.tab_content').hide();
		$($(this).find(">a").attr("href")).show();
		return false;
	});
});


var elem = document.querySelector('.js-switch');
var init = new Switchery(elem);

jQuery(function ($) {
	// select tab
	if(window.location.hash) {
		$('.nav.nav-tabs li a[href="'+window.location.hash+'"]').click()
	}
	$('.nav.nav-tabs li').on('click', function(){
		// window.history.pushState(null, null, window.location.href.replace(/\#.*/,$(this).find('a').attr('href'))); // 페이지 이동없이 주소줄만 변경
		window.location=$(this).find('a').attr('href');
	})



	// tab-3 otp 저장
	$('#jsform3').on('submit',function(){
		let otpuse = $('[name=otpuse]').is(':checked') ? 1 : 0;
		$.post('?', {'pg_mode':'save_otp', 'otpuse':otpuse}, function(r){
			console.log(r);
			if(r.bool) {
				alert('저장했습니다.');
			} else {
				alert('저장하지 못했습니다.');
			}
		},'json');
		return false;
	});
	$('[name="btn-save-otp"]').on('click',function(){
		// $('#jsform3').trigger('submt');
		// return false;
	});

	// tab-4 로그인 기록 조회
	let tpl='<tr><td class="text-center">{regdate}</td><td class="text-center">{adminid}</td><td class="text-center">{ip}</td></tr>';
	let page = 1;
	let get_access_log = function(p, c) {
		p = p*1>0 ? p*1 : 1;
		c = c*1>0 ? c*1 : 10;
		let reg_date = $('[name=box_log] [name=reg_date]').val();
		let adminid = $('[name=box_log] [name=adminid]').val();
		let html=['<tr><td colspan="3" class="text-center">기록이 없습니다.</td></tr>']; // 여기서 선언해줌.
		$.post('?', {'pg_mode':'get_access_log', 'page':p, 'cnt':c, 'regdate':reg_date, 'adminid':adminid}, function(r){
			page = p;
			if(r && r.data) {
				let data = r.data, cnt = data.length;
				for(i=0; i<cnt; i++) {
					let row = data[i];
					html[i] = tpl
					.replace('{regdate}', row.regdate)
					.replace('{adminid}', row.adminid)
					.replace('{ip}', row.ip)
					;
				}
			}
			$('#box_log_list').html(html.join(''));
			$('[name=box_log_paging]').html(gen_paging_html(p, r.total_cnt, c));
		}, 'json');
	}
	get_access_log(page);
	$('[name=box_log] [name=search]').on('click', function(){
		get_access_log(1);
		return false;
	})
	$('[name=box_log] [name="form-search"]').on('submit', function(){
		get_access_log(1);
		return false;
	})
	$('[name=box_log_paging]').on('click', 'button', function(){
		let p = $(this).attr('data-page');
		if(page != p) {
			get_access_log(p);
		}
		return false;
	});

	/**
	 * 페이징 표시할 HTML 생성하는 함수
	 * INSPINIA 태마(관리자용)에서 작동합니다. http://192.168.0.200/1.inspinia_v2.7.1/Static_Full_Version/widgets.html
	 * 클릭 이벤트는 직접 추가해주세요.
	 * 출력예: <button type="button" class="btn btn-white hide"><i class="fa fa-chevron-left" data-page="1"></i></button><button class="btn btn-white active" data-page="1">1</button><button type="button" class="btn btn-white hide"><i class="fa fa-chevron-right" data-page="1"></i></button>
	 * @param {Number} current_page 현재 페이지 번호. 1페이지부터 시작
	 * @param {Number} total_rows 전체 row 수
	 * @param {Number} page_rows 한페이지에 표시하는 row수
	 */
	let gen_paging_html = function(current_page, total_rows, page_rows) {
		let tpl_body = '<button type="button" class="btn btn-white {hide_leftarrow_class}" data-page="{prev_page}"><i class="fa fa-chevron-left"></i></button>{btn-pages}<button type="button" class="btn btn-white {hide_rightarrow_class}" data-page="{next_page}"><i class="fa fa-chevron-right"></i></button>';
		let tpl_page = '<button class="btn btn-white {active_class}" data-page="{page}">{page}</button>';

		// current_page=1, total_rows=0, page_rows=10
		current_page = current_page*1>=1 ? current_page*1 : 1;
		total_rows = total_rows*1>=1 ? total_rows*1 : 0;
		page_rows = page_rows*1>=1 ? page_rows*1 : 10;
		total_page = Math.ceil(total_rows/page_rows);
		page_buttons = 10;
		start_page = page_buttons * Math.floor(current_page/page_buttons) + 1; // 10 * Math.floor(1/10) + 1
		end_page = page_buttons * Math.floor(current_page/page_buttons) + page_buttons; // 10 * Math.floor(1/10) + 10
		end_page = end_page > total_page ? total_page : end_page;
		end_page = end_page < 1 ? 1 : end_page; // 값이 없어도 1페이지는 보여야 할때 사용
		prev_page = current_page - 1 < 1 ? 1 : current_page - 1;
		next_page = current_page + 1 > total_page ? total_page : current_page + 1;

		let html_body = tpl_body
		.replace(/\{hide_leftarrow_class\}/g, total_page>1 ? '' : 'hide')
		.replace(/\{hide_rightarrow_class\}/g, total_page>1 ? '' : 'hide')
		.replace(/\{prev_page\}/g, prev_page)
		.replace(/\{next_page\}/g, next_page)
		.replace(/\{page_end\}/g, total_page)
		.replace(/\{page_start\}/g, 1);
		let html_button = [];
		for(i=0; i<page_buttons; i++) {
			let page = start_page + i;
			let active_class = current_page == page ? 'active' : '';
			if(page>end_page) {break;}
			html_button[i] = tpl_page
			.replace(/\{active_class\}/g, active_class)
			.replace(/\{page\}/g, page)
			;
		}
		return html_body.replace('{btn-pages}', html_button.join(''));
	}

})