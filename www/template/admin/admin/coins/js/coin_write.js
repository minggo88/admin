
const API_URL = '//' + window.location.host.replace('admin.', 'api.') + '/v1.0';

$(function () {

    // 새 데이터 쓰기
    $('#editform').on('submit', function(){
        var fm = this, $fm = $(fm);
        setTimeout(function(){
            // 값 확인
            if($.trim($fm.find('[name=symbol]').val())=='') {
                alert('종목코드을 입력해주세요.'); return false;
            } else {
                $fm.find('[name=symbol]').val( $.trim($fm.find('[name=symbol]').val()).toUpperCase() );
            }
            const symbol = $fm.find('[name=symbol]').val();
            // symbol 영어만 가능
            if (symbol.match(/[^a-z0-9]/i)) {
                alert('종목코드는 영문자(a~z, A~Z)와 숫자(0-9)로만 입력해주세요.'); return false;
            }
            if (symbol.length>10) {
                alert('종목코드는 10글자까지만 입력해주세요.'); return false;
            }

            const exchange = $fm.find('[name=exchange]').val();

            if (!exchange) {
                alert('마켓을 입력해주세요.'); return false;
            }

            if (exchange != 'KRW') {
                alert('마켓은 KRW만 가능 합니다.'); return false;
            }

            // 이름 확인
            if($.trim($fm.find('[name=name]').val())=='') {
                alert('이름을 입력해주세요.'); return false;
            }
            // 저장.
            if (confirm('저장하시겠습니까?')) {
                $.post('?', $fm.serialize(), function(r){
                    if(r && r.bool) {
                        alert('저장되었습니다.');
                        // window.location.href='?pg_mode=edit&symbol='+symbol;
                        window.location.href='?'; // 목록으로 이동
                    } else {
                        if (r.msg == 'err_sess') {
                            alert('로그인 해주세요.'); 
                            window.location.reload();
                            return false;
                        }
                        var msg = r.msg ? r.msg : '저장하지 못했습니다.';
                        alert(msg);
                    }
                }, 'json');
            }
        }, 1);
        return false;
    });


    // 아이콘 업로드
    $('[name="icon_file"]').on('change', function () { 
        upload($(this), 'aws', function (file_url, msg) { 
            if (file_url) {
                $('#icon_image').attr('src',file_url);
                $('#icon_url').val(file_url);
            } else {
                alert('이미지를 서버에 등록하지 못했습니다.'+(msg||''));
            }
        })
    })

    // 기준가변화 업로드
    $('[name="info01_file"]').on('change', function () { 
        upload($(this), 'aws', function (file_url, msg) { 
            if (file_url) {
                $('#info01_image').attr('src',file_url);
                $('#info01').val(file_url);
            } else {
                alert('이미지를 서버에 등록하지 못했습니다.'+(msg||''));
            }
        })
    })
    // 기업정보 업로드
    $('[name="info02_file"]').on('change', function () { 
        // const $input_box = $(this).closest('.input_box');
        // let $new_box = $input_box.clone(true);
        // $new_box.find('[name="info02_file"]').val('');
        // $input_box.after($input_box); // 여러개 입력 할 수 있게 수정
        upload($(this), 'aws', function (file_url, msg) { 
            if (file_url) {
                $('#info02_image').attr('src',file_url);
                $('#info02').val(file_url);
                // $('#info02').val($('#info02').val()+';'+file_url);
            } else {
                alert('이미지를 서버에 등록하지 못했습니다.'+(msg||''));
            }
        })
    })
    // 투자정보 업로드
    $('[name="info03_file"]').on('change', function () { 
        upload($(this), 'aws', function (file_url, msg) { 
            if (file_url) {
                $('#info03_image').attr('src',file_url);
                $('#info03').val(file_url);
            } else {
                alert('이미지를 서버에 등록하지 못했습니다.'+(msg||''));
            }
        })
    })
    // 판매회원 계정 만들기
    $('[name=btn-create-manager]').on('click', function () { 
        let userid = $('[name="manager_userid"]').val();
        if (!userid) { alert('판매회원 아이디를 입력해주세요.'); return false;}
        let userpw = $('[name="manager_userpw"]').val();
        if (!userpw) { alert('판매회원 비밀번호를 입력해주세요.'); return false;}
        let nickname = $('[name="manager_nickname"]').val();
        if (!nickname) { alert('판매회원 닉네임을 입력해주세요.'); return false;}
        $.post('?', { 'pg_mode': 'create_manager', 'userid': userid, 'userpw': userpw , 'nickname': nickname}, function (r) {
            if (r && r.bool) {
                alert('판매회원 계정이 생성되었습니다.\n' + (r.msg ? r.msg : ''));
                $('[name="manager_userpw"]').val('');
            } else {
                if (r.msg == 'err_sess') {
                    alert('로그인 해주세요.');
                    window.location.reload();
                    return false;
                }
                var msg = '판매회원 계정을 생성하지 못했습니다. ' + (r.msg ? r.msg : '');
                alert(msg);
            }
        }, 'json');
    })
    // 판매회원 주식지급
    $('[name="btn-add-balance"]').on('click', function () { 
        let userid = $('[name="manager_userid"]').val();
        if (!userid) { alert('판매회원 아이디를 입력해주세요.'); return false;}
        let amount = $('[name="add_balance_to_manager"]').val();
        if (amount<=0) { alert('판매회원에게 지급할 수량을 입력해주세요.'); return false;}
        $.post('?', { 'pg_mode': 'add_krw_to_seller', 'userid': userid, 'amount': amount, 'symbol':getURLParameter('symbol') }, function (r) {
            if (r && r.bool) {
                alert('판매회원에게 지급했습니다.');
                window.location.reload();
            } else {
                if (r.msg == 'err_sess') {
                    alert('로그인 해주세요.');
                    window.location.reload();
                    return false;
                }
                var msg = '판매회원에게 지급하지 못했습니다. ' + (r.msg ? r.msg : '');
                alert(msg);
            }
        }, 'json');
    })

    $('.input_percent').on('keyup', function () { 
        const number = $(this).val().replace(/[^0-9.]/g, ''), percent = number_format(number*100, 2);
        $(this).siblings('.text_percent').text(percent);
    })

    // NFT 상품 검색
    $('[name=nft_goods_title]').typeahead({
        source: function (query, result) {
            $.ajax({
                url: API_URL+"/getAuction/auction_goods_list.php",
                data: { 'goods_name': query , 'added_trade_currency':'N'} ,
                dataType: "json",
                type: "POST",
                success: function (r) {
                    if (r && r.success) {
                        if (r.payload.length < 1) {
                            // alert('등록가능한 상품을 찾지 못했습니다.');
                        } else {
                            result($.map(r.payload, function (i) {
                                console.log(i, i.title);
                                return { 'name':i.title, 'idx':i.idx, 'main_pic':i.main_pic, 'price':i.price };
                            }));
                        }
                    } else {
                        if (r && r.error) {
                            if (r.error.code == '001') {
                                alert(r.error.message); // 로그인해주세요.
                            }
                        }
                    }
                }
            });
        }
    }).on('change', function () { 
        var c = $(this).typeahead("getActive");
        console.log(c);
        $('[name=nft_goods_idx]').val(c.idx);
        $('[name=price]').val(c.price);
        $('#icon_image').attr('src',c.main_pic);
        $('#icon_url').val(c.main_pic);
        $('[name=name]').val(c.name);
        $('[name=symbol]').val(c.idx);
    });
});