$(function() {

    $('[name="owner_user_change"]').off().on('click', function(e) {
        $.post( '//'+window.location.host.replace('admin.', 'api.')+'/v1.0/putAuction/owner_user_change.php', {
            'goods_idx':$('[name="goods_idx"]').val(),
            'owner_no':$('[name="owner_userno"]').val(),
            'type':'ownerChange'
        }, function(r){
            if(r && r.success) {
                alert('소유자가 변경 되었습니다.');
            } else {
                alert(msg);
            }
        }, 'json');
    });
	/* 230131 mk 스크립트 오류 */
    /*$('[name=owner_username]').typeahead({
        source: function (query, result) {
            $.ajax({
                url:  '//'+window.location.host.replace('admin.', 'api.')+'/v1.0/putAuction/owner_user_change.php',
                data: {'goods_idx':$('[name="goods_idx"]').val(),
                    'owner_name':$('[name="owner_username"]').val(),
                    'type':'ownerChange',
                    'search_name': query,
                    'type': 'memberList'},
                dataType: "json",
                type: "POST",
                success: function (r) {
                    if (r && r.success) {
                        if (r.payload.length < 1) {
                            // alert('등록가능한 상품을 찾지 못했습니다.');
                        } else {
                            result($.map(r.payload, function (i) {
                                // console.log(i, i);
                                return { 'id':i.userno, 'name':i.name+'|'+i.userid+'|'+i.mobile };
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

    }).on('change', function() {
        var c = $(this).typeahead("getActive");
        // console.log(c);
        // userno set
        $('[name="owner_userno"]').val(c.id);
    });
	*/

    if ($('[name="goods_upload"]').attr('disabled') == true )  $(this).attr('disabled', false);
    if ($('[name="goods_add_uplaod"]').attr('disabled') == true )  $(this).attr('disabled', false);

    $('[name="goods_upload"]').on('click', function() {
        let goods_file_data = $('[name="goods_file_data"]').val()
        if (!goods_file_data) {
            alert('파일을 선택하세요.');
            return false;
        }
        fileName = goods_file_data.slice(goods_file_data.indexOf(".") + 1).toLowerCase();
        if(fileName != "xlsx"){
            alert("엑셀 파일은 (xlsx) 형식만 등록 가능합니다.");
            return false;
        }
        $('[name="upload_type"]').val("goods_upload")
        $(this).attr('disabled', true);
        $('[name=excel_form]').submit();
    })
    $('[name="goods_add_uplaod"]').on('click', function() {
        let add_goods_file_data = $('[name="add_goods_file_data"]').val()
        if (!add_goods_file_data) {
            alert('파일을 선택하세요.');
        }
        fileName = add_goods_file_data.slice(add_goods_file_data.indexOf(".") + 1).toLowerCase();
        if(fileName != "xlsx"){
            alert("엑셀 파일은 (xlsx) 형식만 등록 가능합니다.");
            return false;
        }
        $('[name="upload_type"]').val("goods_add_uplaod")
        $(this).attr('disabled', true);
        $('[name=excel_form]').submit();
    })

    // 날짜 설정
    var start_get_date = '';
    var end_get_date = '';

    start_get_date = $('input[name=start_date]').val();
    end_get_date = $('input[name=end_date]').val();

    if(start_get_date) {
        $('#reportrange span').html(start_get_date + ' - ' + end_get_date);
    } else {
        start_get_date = moment().format('YYYY-MM-DD');
        $('#reportrange span').html(moment().subtract(30, 'days').format('YYYY-MM-DD') + ' - ' + moment().format('YYYY-MM-DD'));
    };

    $('#reportrange').daterangepicker({
        format: 'YYYY-MM-DD',
        startDate: start_get_date,
        endDate: moment(),
        // minDate: '2018-04-01',
        // maxDate: '2020-12-31',
        // dateLimit: { days: 60 },
        showDropdowns: true,
        showWeekNumbers: true,
        timePicker: false,
        timePickerIncrement: 1,
        timePicker12Hour: true,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(7, 'days'), moment().subtract(1, 'days')],
            'Last 30 Days': [moment().subtract(30, 'days'), moment().subtract(1, 'days')],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        opens: 'right',
        drops: 'down',
        buttonClasses: ['btn', 'btn-sm'],
        applyClass: 'btn-primary',
        cancelClass: 'btn-default',
        separator: ' to ',
        locale: {
            applyLabel: 'Submit',
            cancelLabel: 'Cancel',
            fromLabel: 'From',
            toLabel: 'To',
            customRangeLabel: 'Custom',
            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
            monthNames: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            firstDay: 1
        }
    }, function(start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);

        var start_date = '';
        var end_date = '';
        start_date = start.format('YYYY-MM-DD');
        end_date = end.format('YYYY-MM-DD');

        $('input[name=start_date]').val(start_date);
        $('input[name=end_date]').val(end_date);

        $('#reportrange span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));

    });

    // 새 데이터 쓰기
    $('#editform').on('submit', function(){
        var fm = this, $fm = $(fm);
        
        // 이름 확인
        if($.trim($fm.find('[name=title]').val())=='') {
            alert('이름을 입력해주세요.'); return false;
        }
        // base_price 확인
        if($.trim($fm.find('[name=base_price]').val())=='') {
            alert('기본 가격을 입력해주세요.'); return false;
        }
        // 차소개 확인
        if($.trim($fm.find('[name=content]').val())=='') {
            alert('차 소개글을 입력해주세요.'); return false;
        }
        // goods_type(카테고리) 확인
        if($.trim($fm.find('[name=goods_type]').val())=='') {
            alert('카테고리를 선택해주세요.'); return false;
        }
        // minting_quantity(발행수량) 확인
        if($.trim($fm.find('[name=minting_quantity]').val())=='') {
            alert('발행수량을 입력해주세요.'); return false;
        }
        // 스캔 파일 처리
        if($.trim($fm.find('[name=minting_quantity]').val())=='') {
            alert('발행수량을 입력해주세요.'); return false;
        }

        // 저장.
        if (confirm('저장하시겠습니까?')) {
            let royalty = $.trim($fm.find('[name=royalty]').val());
            royalty = royalty > 0 ? royalty + '%' : '';
            $fm.find('[name=royalty]').val(royalty);
            
            $.post( '//'+window.location.host.replace('admin.', 'api.')+'/v1.0/putAuction/goods.php', $fm.serialize(), function(r){
                if(r && r.success) {
                    alert('저장되었습니다.');
                    window.location.href='?'; // 목록으로 이동
                } else {
                    // if (r.msg == 'err_sess') {
                    //     alert('로그인 해주세요.'); 
                    //     window.location.reload();
                    //     return false;
                    // }
                    var msg = r.error.message ? r.error.message : '저장하지 못했습니다.';
                    alert(msg);
                }
            }, 'json');
        }
        return false;
    });


    // 스캔파일 URL 등록
    $('[name="animation_url"]').on('change keyup', function () {
        let file_url = $(this).val();
        if (!file_url) { return false; }
        // youtube url
        const re = file_url.match(/https:\/\/www.youtube.com\/watch\?v=(.*)/);
        if (re && re[1]) {
            file_url = 'https://www.youtube.com/embed/' + re[1];
        }
        const $box_animation = $('#box_animation');
        // 현재값 초기화
        $('[name="animation_file"]').val('');
        // 이전 업로드 파일있으면 삭제
        const prev_file_url = $box_animation.find('video').data('src');
        if (prev_file_url) {
            $.post('?', { 'pg_mode': 'delete_file', 'file_url': prev_file_url, 'idx': '' }, function (r) {
                if (!r || !r.bool) {
                    alert('임시 파일을 삭제하지 못했습니다.'); // 중요하지 않아 안내만 하고 패스
                }
            });
        }
        // 업로드된 파일 표시
        $('[name="animation"]').val(file_url);
        $box_animation.empty().append($('<div style="position: relative;display: inline-block;max-height:200px"><i name="btn-delete-image" data-idx="" data-url="' + file_url + '" class="fa fa-times" aria-hidden="true" style="position: absolute;right: 0;color: red;font-size: 2rem;margin: 0.5rem;z-index:10"></i><iframe src="' + file_url + '" height="200px" style="border:0"></iframe></div>'));
    });
    // 스캔 파일 업로드
    $('[name="animation_file"]').on('change', function () { 
        if (!$(this).val()) { return false; }
        upload($(this), 'google_drive', function (file_url, msg) { 
            const $box_animation = $('#box_animation');
            if (file_url) {
                // 현재값 초기화
                $('[name="animation_file"]').val('');
                // 이전 업로드 파일있으면 삭제
                const prev_file_url = $box_animation.find('video').data('src');
                if (prev_file_url) {
                    $.post('?', { 'pg_mode': 'delete_file', 'file_url': prev_file_url, 'idx': '' }, function (r) {
                        if (!r || !r.bool) {
                            alert('임시 파일을 삭제하지 못했습니다.'); // 중요하지 않아 안내만 하고 패스
                        }
                    });
                }
                // 업로드된 파일 표시
                $('[name="animation"]').val(file_url);
                $box_animation.empty().append($('<div style="position: relative;display: inline-block;max-height:200px"><i name="btn-delete-image" data-idx="" data-url="' + file_url + '" class="fa fa-times" aria-hidden="true" style="position: absolute;right: 0;color: red;font-size: 2rem;margin: 0.5rem;z-index:10"></i><video controls="" controlslist="nodownload" autoplay="" loop="" preload="auto" src="' + file_url + '" height="200px"></video></div>'));
            } else {
                if (r.msg == 'err_sess') {
                    alert('로그인 해주세요.'); 
                    window.location.reload();
                    return false;
                }
                alert('이미지를 서버에 등록하지 못했습니다. '+(msg||''));
            }
        })
        
    })
    $('#box_animation').on('click', '[name="btn-delete-image"]', function () { 
        const idx = $(this).attr('data-idx');
        const image_url = $(this).attr('data-url');
        // console.log(idx, image_url);
        const confirmed = idx ? confirm('삭제하시겠습니까?') : 1;
        const $parent = $(this).parent();
        if (confirmed && image_url) {
            $.post('?', { 'pg_mode': 'delete_file', 'file_url': image_url, 'idx': idx }, function (r) { 
                if (r && r.bool) {
                    if (image_url == $('[name="animation"]').val()) {
                        $('[name="animation"]').val('');
                    }
                    $parent.remove();
                } else {
                    if (r.msg == 'err_sess') {
                        alert('로그인 해주세요.'); 
                        // window.location.reload();
                        return false;
                    }
                    alert('삭제하지 못했습니다. '+(r.msg||''));
                }
            }, 'json')
        }
    })


    // 아이콘 업로드
    $('[name="icon_file"]').on('change', function () { 
        const $box_image_url = $('#box_image_url');
        const cnt = $box_image_url.children().length;
        if (cnt >= 10) {
            alert('최대 10개까지 이미지를 등록하실 수 있습니다.');
            return false;
        }
        upload($(this), 'aws_s3', function (file_url, msg) { 
            const $box_image_url = $('#box_image_url');
            if (file_url) {
                $('[name="icon_file"]').val('');
                const no = cnt + 1;
                $box_image_url.append($('<div style="position: relative;display: inline-block;margin: 0 5px 0 0;"><i name="btn-delete-image" data-idx="" class="fa fa-times" aria-hidden="true" style="position: absolute;right: 0;color: red;font-size: 2rem;margin: 0.5rem;z-index:10"></i><img src="' + file_url + '" class="icon_image" style="height:150px"><input type="hidden" name="sub'+no+'_pic" value="' + file_url + '"><div>'));
            } else {
                if (r.msg == 'err_sess') {
                    alert('로그인 해주세요.'); 
                    window.location.reload();
                    return false;
                }
                alert('이미지를 서버에 등록하지 못했습니다. '+(msg||''));
            }
        })
    })
    $('#box_image_url').on('click', '[name="btn-delete-image"]', function () { 
        const idx = $(this).attr('data-idx');
        const image_url = $(this).siblings('img').attr('src');
        const confirmed = idx ? confirm('삭제하시겠습니까?') : 1;
        const $parent = $(this).parent();
        if (confirmed && image_url) {
            $.post('?', { 'pg_mode': 'delete_file', 'file_url': image_url, 'idx': idx }, function (r) { 
                if (r && r.bool) {
                    $parent.remove();
                } else {
                    if (r.msg == 'err_sess') {
                        alert('로그인 해주세요.'); 
                        // window.location.reload();
                        return false;
                    }
                    alert('삭제하지 못했습니다. '+(r.msg||''));
                }
            }, 'json')
        }
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

});