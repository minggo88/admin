
var myeditor_kr = new cheditor("contents_kr");
var myeditor_en = new cheditor("contents_en");
var myeditor_cn = new cheditor("contents_cn");

jQuery(function($) {

    $('#jsform').submit(function () {
        
        const pg_mode = getURLParameter('pg_mode');

        myeditor_kr.outputBodyHTML();
        myeditor_en.outputBodyHTML();
        myeditor_cn.outputBodyHTML();
        
		/*
		var chk_option = [
			{ 'target':'name', 'name':'', 'type':'num', 'msg':'' }
		];
		if(!jsForm(this,chk_option)) {
			return false;
		}
		*/

		if(!confirm('저장하시겠습니까?')) {
			return false;
		}
		$(this).ajaxSubmit({
			success: function (data, statusText) {
                if (data['bool']) {
                    if (pg_mode == 'form_new') {
                        alert('저장되었습니다.!');
                        location.replace('?pg_mode=list');
                    } else {
                        if(confirm('계속 수정하시겠습니까?')) {
                        } else {
                            location.href = '?pg_mode=list';
                        }
                    }
				}
				else {
					if(data['msg'] == 'err_access') {
						alert('비정상적인 접근입니다.');
					}
					else if(data['msg'] == 'err_sess') {
                        // location.replace('/member/memberAuth.php?ret_url=<!--{=base64_encode(_SERVER.REQUEST_URI)}-->');
                        alert('다시 로그인해주세요');
					}
					else {
						alert('재시도 해주세요.!');
					}
				}
			},
			dataType:'json',
			resetForm: false
		});
		return false;
	});
});


myeditor_kr.config.editorHeight = '200px';
myeditor_kr.config.editorWidth = '100%';
myeditor_kr.inputForm = 'contents_kr';
myeditor_kr.config.imgMaxWidth = 800;
myeditor_kr.config.imgSetAttrWidth = true;
myeditor_kr.config.useSource = true;
myeditor_kr.config.usePreview = true;
myeditor_kr.run();

myeditor_en.config.editorHeight = '200px';
myeditor_en.config.editorWidth = '100%';
myeditor_en.inputForm = 'contents_en';
myeditor_en.config.imgMaxWidth = 800;
myeditor_en.config.imgSetAttrWidth = true;
myeditor_en.config.useSource = true;
myeditor_en.config.usePreview = true;
myeditor_en.run();

myeditor_cn.config.editorHeight = '200px';
myeditor_cn.config.editorWidth = '100%';
myeditor_cn.inputForm = 'contents_cn';
myeditor_cn.config.imgMaxWidth = 800;
myeditor_cn.config.imgSetAttrWidth = true;
myeditor_cn.config.useSource = true;
myeditor_cn.config.usePreview = true;
myeditor_cn.run();