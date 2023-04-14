var isloaded = false;

$(function() {
	if (isloaded) return; //중복 호출방지

    var searchval = getURLParameter('searchval'), boolServerSide = searchval ? false : true, length = searchval ? "200" : "10";
    var symbol = getURLParameter('symbol');
    var datatable = $('[name=db_txn_list]').addClass( 'nowrap' ).DataTable({
        "search": {
            "search": getURLParameter('search')||'',
        },
        "language": {
            "emptyTable": "데이터가 없음.",
            "lengthMenu": "페이지당 _MENU_ 개씩 보기",
            "info": "현재 _START_ - _END_ / _TOTAL_건",
            "infoEmpty": "",
            "infoFiltered": "( _MAX_건의 데이터에서 필터링됨 )",
            "search": "검색: ",
            "zeroRecords": "일치하는 데이터가 없습니다.",
            "loadingRecords": "로딩중...",
            "processing": '<img src="/template/admin/script/plug_in/loading/loading.gif"> 잠시만 기다려 주세요.',
            "paginate": {
                "next": "다음",
                "previous": "이전"
            }
				}
				, 'destroy': true
        , "processing": true
        , "serverSide": true
        , 'pageLength': 10 , "lengthMenu": [ [10, 25, 50, 75, 100], [10, 25, 50, 75, 100] ]
        , "responsive": true
        , "ajax": {
            type: "post",
            url: "?",
            data:  function ( d ) {
                d.pg_mode = 'transaction_list';
                d.symbol = symbol;
                d.searchval = searchval;
                d.filter_out_txn = 'Y';
                d.inout = getURLParameter('inout')||'out';
            }
        }
        ,"order":[[0, 'desc']]
        ,"columns": [
					// 날짜
					{ "data": "regdate", "orderable": true, "className":"dt-body-center", "orderSequence": ['desc','asc']  },
					// 거래 상태 - 완료된것만 사용
					// { "data": "status", "className":"dt-body-center", "orderable": false,
                    //     render: function(data) {
                    //         var str ="";
                    //         if(data =='P'){ str="처리중";}
                    //         else if(data =='O'){ str="준비중";}
                    //         else if(data =='D'){ str="종료";}
                    //         else if(data =='C'){ str="취소";}
                    //         return str;
                    //     }
					// },
					// 거래종류
					// { "data": "txn_type", "orderable": true, "className":"dt-body-center",
					// 	render: function(data, type, row) {
					// 		var str =""; // 트렌젝션 종류. R:(외부)입금, W:(외부)출금, D:배당, E:교환, A:출석체크, I:초대하기, S:보내기, P:결제(pay), BO:보너스, R:환불(refund), DO: HTP 받음, EQ:ArATube Event QRCode
					// 		switch(data) {
					// 			case 'R': str = '외부입금'; break;
					// 			case 'W': str = '출금'; break;
					// 			case 'D': str = '배당'; break;
					// 			case 'E': str = '교환'; break;
					// 			case 'A': str = '출석체크'; break;
					// 			case 'I': str = '추천'; break;
					// 			case 'IE': str = '피추천인'; break;
					// 			case 'IR': str = '추천인'; break;
					// 			case 'S': str = '보내기'; break;
					// 			case 'P': str = '결제'; break;
					// 			case 'BO': str = '보너스'; break;
					// 			case 'R': str = '환불'; break;
					// 			case 'DO': str = '아라튜브 후원'; break;
					// 			case 'EQ': str = '아라튜브 이벤트 참여'; break;
					// 			case 'Au': str = '경매 입찰'; break;
					// 			case 'AS': str = '경매 낙찰'; break;
					// 			case 'AR': str = '경매 유찰'; break;
					// 		}
					// 		// if(data =='I'){ str="입금";}
					// 		// else if(data =='O'){ str="출금";}
                    //         str = row.txn_outlink_url ? '<a href="'+row.txn_outlink_url+'" target="_'+row.key_relative+'">'+str+'</a>' : str;
					// 		return str;
					// 	},
					// },
					// // 거래 아이디
					// { "data": "txnid", "orderable": true },

					// 외부거래소(주소)
					{ "data": "receiver_address", "orderable": false, render:function(data, type, row){
                        return getURLParameter('inout')=='in' ? '캐셔레스트'+'('+(row.sender_address||'')+')' : '캐셔레스트'+'('+(row.receiver_address||'')+')';
                    }},
					// 보낸회원(이름)
					{ "data": "receiver_userid", "orderable": false, render:function(data, type, row){
						    return getURLParameter('inout')=='in' ? (row.receiver_userid||'')+(row.receiver_userid ? ', ' : '')+(row.receiver_username||'') : (row.sender_userid||'')+(row.sender_userid ? ', ' : '')+(row.sender_username||'');
                        }
                    },
					// 수량
					{ "data": "amount", "orderable": false, "orderSequence": ['desc','asc']
						, render:function(data, type, row){return real_number_format(data);}
					},
					// 수수료
					{ "data": "fee", "orderable": false, "orderSequence": ['desc','asc']
						, render:function(data, type, row){return real_number_format(data);}
					},
					// 송금비용
					{ "data": "fee_relative", "orderable": false, "orderSequence": ['desc','asc']
						, render:function(data, type, row){return real_number_format(data, 4) + ' ETH';}
					}
        ]
        , "dom": '<html5buttons>Bfrtip'
        , "buttons": [
            {extend: 'copy'},
            {extend: 'csv', title: '거래소지갑_거래내역_'+symbol+'_'+date('YmdHis')},
            {extend: 'excel', title: '거래소지갑_거래내역_'+symbol+'_'+date('YmdHis')},

            {extend: 'pdfHtml5', title: '거래소지갑_거래내역_'+symbol+'_'+date('YmdHis'), fontSize: '10px', orientation: 'landscape', pageSize: 'A4', pageMargins: [ 40, 60, 40, 60 ] },

            {extend: 'print',
                customize: function (win){
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');

                    $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                }
            }
        ]
        , "error": function (xhr) {
            console.log(xhr.responseText);
        }
    });
    datatable.on('click', '[name=btn-unlock]', function(){
        var url = url = $(this).attr('href'), url = url.split('?'), param = url[1], url = url[0]+'?';
        if(confirm('지갑을 잠금해제하시겠습니까? 자동잠금도 같이 해제됩니다. ')) {
            setTimeout(function(){
                $.post(url, param, function(r){
                    if(r && r.bool){
                        datatable.ajax.reload(null, false);
                    } else {
                        var msg = r && r.msg && r.msg!='' ? r.msg : '잠금해제하지 못했습니다.';
                        alert(msg);
                    }
                }, 'json');
            }, 1);
        }
        return false;
    }).on('click', '[name=btn-lock]', function(){
        var url = url = $(this).attr('href'), url = url.split('?'), param = url[1], url = url[0]+'?';
        if(confirm('지갑을 잠그시겠습니까? 지갑이 잠기면 출금을 할 수 없습니다.')) {
            setTimeout(function(){
                $.post(url, param, function(r){
                    if(r && r.bool){
                        datatable.ajax.reload(null, false);
                    } else {
                        var msg = r && r.msg && r.msg!='' ? r.msg : '지갑을 잠그지 못했습니다.';
                        alert(msg);
                    }
                }, 'json');
            }, 1);
        }
        return false;
    });
    $('#filter_out_txn').on('click', function(){
        datatable.ajax.reload(null, false);
    });
    setTimeout(function(){$('#box_filter').show();}, 1000);

    isloaded = true;

});
