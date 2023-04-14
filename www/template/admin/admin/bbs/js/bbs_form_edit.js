
var myeditor_kr = new cheditor("contents_kr");
var myeditor_en = new cheditor("contents_en");
var myeditor_cn = new cheditor("contents_cn");

jQuery(function () {
    $('#bbsform').submit(function () {
        myeditor_kr.outputBodyHTML();
        myeditor_en.outputBodyHTML();
        myeditor_cn.outputBodyHTML();
        var chk_option = [
            { 'target': 'author', 'name': '이름', 'type': 'blank', 'msg': '작성자 이름을 입력하세요.!' },
        ];
        if ($('#bbsform').find('[name=category]:visible').length) {
            chk_option.push({ 'target': 'category', 'name': '카테고리', 'type': 'blank', 'msg': '카테고리를 입력하세요.!' });
        }
        if ($(this).find('[name=bool_secret]:checked').val() == '1') {
            chk_option.push({ 'target': 'passwd', 'name': '비밀번호', 'type': 'blank', 'msg': '비밀번호를 입력하세요.!' });
        }
        chk_option.push({ 'target': 'subject_kr', 'name': '제목', 'type': 'blank', 'msg': '한국어 글 제목을 입력하세요.!' });
        // chk_option.push({ 'target':'subject_en', 'name':'제목', 'type':'blank', 'msg':'영어 글 제목을 입력하세요.!' });
        // chk_option.push({ 'target':'subject_cn', 'name':'제목', 'type':'blank', 'msg':'중국어 글 제목을 입력하세요.!' });

        if (!jsForm(this, chk_option)) {
            return false;
        }

        if (!confirm('저장하시겠습니까?')) {
            return false;
        }

        $(this).ajaxSubmit({
            success: function (data, statusText) {
                if (data['bool']) {
                    alert('저장되었습니다.!');
                    location.href = '?pg_mode=list&bbscode='+getURLParameter('bbscode');
                }
                else {
                    if (data['msg'] == 'err_access') {
                        alert('비정상적인 접근입니다.');
                    }
                    else if (data['msg'] == 'err_sess') {
                        alert('다시 로그인 해주세요.');
                        // location.replace('/admin/auth.php?ret_url=<!--{=base64_encode(_SERVER.REQUEST_URI)}-->');
                    }
                    else {
                        alert('재시도 해주세요.!');
                    }
                }
            },
            dataType: 'json',
            resetForm: false
        });
        return false;
    });
});

myeditor_kr.config.editorHeight = '300px';
myeditor_kr.config.editorWidth = '100%';
myeditor_kr.inputForm = 'contents_kr';
myeditor_kr.config.imgMaxWidth = 800;
myeditor_kr.config.imgSetAttrWidth = true;
myeditor_kr.config.useSource = true;
myeditor_kr.config.usePreview = true;
myeditor_kr.run();

myeditor_en.config.editorHeight = '300px';
myeditor_en.config.editorWidth = '100%';
myeditor_en.inputForm = 'contents_en';
myeditor_en.config.imgMaxWidth = 800;
myeditor_en.config.imgSetAttrWidth = true;
myeditor_en.config.useSource = true;
myeditor_en.config.usePreview = true;
myeditor_en.run();

myeditor_cn.config.editorHeight = '300px';
myeditor_cn.config.editorWidth = '100%';
myeditor_cn.inputForm = 'contents_cn';
myeditor_cn.config.imgMaxWidth = 800;
myeditor_cn.config.imgSetAttrWidth = true;
myeditor_cn.config.useSource = true;
myeditor_cn.config.usePreview = true;
myeditor_cn.run();
