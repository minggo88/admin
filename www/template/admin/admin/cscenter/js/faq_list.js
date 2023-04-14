
jQuery(function () {

    $('#searchform').submit(function () {
        var checked_size = $(':checkbox:checked', this).size();
        var s_val = $('input[name=s_val]').val();
        if (checked_size == 0) {
            alert('검색조건을 선택하여 주세요');
            return false;
        }
        if (s_val == '') {
            alert('검색어를 입력하여 주세요');
            return false;
        }
    });

    $('#all_check').click(function () {
        if (this.checked) {
            $("table.list_table tbody input:checkbox").attr('checked', 'checked');
        }
        else {
            $("table.list_table tbody input:checkbox").removeAttr('checked');
        }
    });

    $("#list_form>table>tbody>tr").hover(
        function () { $(this).css('background-color', '#FFF2F0'); },
        function () { $(this).css('background-color', '#FFF'); }
    );

    $('[name="language"]').on('change', function () { 
        const lang = $(this).val();
        window.location.href = setURLParameter('language', lang); 
    });

})

function checkDel() {
    var num_checked = $('#list_form :checkbox:checked').length;
    if (num_checked == 0) {
        alert('선택된 항목이 없습니다.!');
        return false;
    }
    $.get('<!--{_SERVER.SCRIPT_NAME}-->?pg_mode=del_multi&' + $('#list_form').serialize(), function (data) {
        if (data['bool']) {
            alert('삭제되었습니다.');
            location.replace('<!--{_SERVER.REQUEST_URI}-->');
        }
        else {
            if (data['msg'] == 'err_access') {
                alert('비정상적인 접근입니다.');
            }
            else if (data['msg'] == 'err_sess') {
                location.replace('/admin/auth.php?ret_url=<!--{=base64_encode(_SERVER.REQUEST_URI)}-->');
            }
            else {
                alert('재시도 해주세요.!');
            }
        }
    }, 'json');
}