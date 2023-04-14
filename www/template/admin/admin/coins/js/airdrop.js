$(function() {

    var grid = canvasDatagrid({
        // parentNode: $('#target_list').get(0),
        // editCellFontSize:'9px', // 안먹는다
        borderDragBehavior: 'move',
        allowMovingSelection: true,
        allowColumnReordering: false,
        columnHeaderClickBehavior: 'sort',
        allowFreezingRows: true,
        allowFreezingColumns: true,
        allowRowReordering: true,
        allowSorting: true,
        autoResizeColumns: true,
        tree: false,
        debug: false,
        showPerformance: false,
        showNewRow: true
    });
    var showExcelFile = function(data) {
        console.log('data:', data);
        // 해더 넣기
        for( r in data) {
            var row = data[r];
            if(row.length) {
                data[r] = {"종목코드":row[0],"회원아이디":row[1],"주당금액":row[2],"주식수량":row[3],"합계금액":row[4],"보유기간(일)":row[5],"보유날짜":row[6]};
            }
        }
        $('#target_list').get(0).appendChild(grid);
        grid.style.height = '100%';
        grid.style.width = '100%';
        // grid.style.font = '9px'; // 안먹는다
        grid.data = data;
    };
    var grid2 = canvasDatagrid({
        // parentNode: $('#target_list').get(0),
        // editCellFontSize:'11px', // 안먹는다
        borderDragBehavior: 'move',
        allowMovingSelection: true,
        allowColumnReordering: false,
        columnHeaderClickBehavior: 'sort',
        allowFreezingRows: true,
        allowFreezingColumns: true,
        allowRowReordering: true,
        allowSorting: true,
        autoResizeColumns: true,
        tree: false,
        debug: false,
        showPerformance: false,
        showNewRow: true
    });
    var showResult = function(data) {
        console.log('data:', data);
        // 해더 넣기
        // for( r in data) {
        //     var row = data[r];
        //     if(row.length) {
        //         data[r] = {"회원아이디":row[0],"금액":row[1],"결과":row[1]};
        //     }
        // }
        $('#result_list').get(0).appendChild(grid2);
        grid2.style.height = '100%';
        grid2.style.width = '100%';
        grid2.data = data;
    };
    // showExcelFile([ {"회원아이디":'홍길동',"금액":'1000000'}, {"회원아이디":'홍상직',"금액":'50000000'} ]);
    // showExcelFile([ ['hongsangzig@kmcse.com','50000000'],['honggildong@kmcse.com','1000000'] ]);
    // "회원아이디","주당금액","주식수량","합계금액","보유기간(일)","보유날짜"
    showExcelFile([ ['APC','sample_user@paran.com','100','2','50','10','2022-07-16' ] ]);

    var readExcelFile = function(fObj, callback) {
        // console.log(fObj.value);
        if(!fObj || fObj.value === '') {
            alert('액셀파일을 선택해주세요.');
        } else {
            const selectedFile = fObj.files[0];
            var reader = new FileReader();
            reader.onload = function(evt) {
                if(evt.target.readyState == FileReader.DONE) {
                    var data = evt.target.result, r = [];
                    data = new Uint8Array(data);
                    console.log('data:',data); 
                    // call 'xlsx' to read the file
                    var workbook = XLSX.read(data, {type: 'array', raw:false});
                    if(workbook.Sheets) {
                        for( i in workbook.Sheets) {
                            // r.push(XLSX.utils.sheet_to_json(workbook.Sheets[i]));//, {header:1}
                            r = XLSX.utils.sheet_to_json(workbook.Sheets[i]);break; // 첫번째 시트만 작업하고 종료.
                        }
                    }
                    if(typeof callback != typeof undefined) {
                        callback(r);
                    }
                }
            };
            reader.readAsArrayBuffer(selectedFile);
        }
    }
    $('[name=readexcelfile]').on('change', function(){
        readExcelFile($('[name=readexcelfile]').get(0), showExcelFile);
    });
    $('[name=downloadexcelfile]').on('click', function(){
        var data = [];
        for ( r in grid.data ) {
            var row = grid.data[r];
            if(row['회원아이디']!='') {
                data.push(row);
            }
        }
        console.log(data);
        var ws = XLSX.utils.json_to_sheet(data, {header:["종목코드","회원아이디","주당금액","주식수량","합계금액","보유기간(일)","보유날짜"]}),
            html = XLSX.utils.sheet_to_html(ws);
        if($('#tmpbox').length<1) {
            $('body').append('<div style="display:none" id="tmpbox"></div>');
        }
        $('#tmpbox').append(html);
        var wb = XLSX.utils.table_to_book($('#tmpbox').find('table').get(0), {sheet:"Sheet JS"});
        XLSX.writeFile(wb, 'sample.xlsx');
    });
    $('[name=downloadresult]').on('click', function(){
        var data = [];
        for ( r in grid2.data ) {
            var row = grid2.data[r];
            if(row['회원아이디']!='') {
                data.push(row);
            }
        }
        console.log(data);
        var ws = XLSX.utils.json_to_sheet(data, {header:["결과","종목코드","회원아이디","주당금액","주식수량","합계금액","보유기간(일)","보유날짜","등록날짜"]}),
            html = XLSX.utils.sheet_to_html(ws);
        if($('#tmpbox').length<1) {
            $('body').append('<div style="display:none" id="tmpbox"></div>');
        }
        $('#tmpbox').append(html);
        var wb = XLSX.utils.table_to_book($('#tmpbox').find('table').get(0), {sheet:"Sheet JS"});
        XLSX.writeFile(wb, '스톡옵션결과_'+((new Date).toISOString())+'.xlsx');
    });
    $('[name=resetexcelfile]').on('click', function(){
        showExcelFile([ ['',''] ]);
        grid.deleteRow(0);
    });
    $('[name=unqueexcelfile]').on('click', function(){
        var userid = [];
        grid.data = grid.data.reduce(function(accumulator, value){
            if($.inArray(value['회원아이디'], userid)<0 ) {
                accumulator.push(value);
                userid.push(value['회원아이디']);
            }
            return accumulator;
        }, []);
    });
    $('[name=do_airdrop]').on('click', function(){
        var data = [], userid=[];
        for( r in grid.data ) {
            var row = grid.data[r];
            // "회원아이디","주당금액","주식수량","합계금액","보유기간(일)","보유날짜"
            if($.inArray(row['회원아이디'], userid)>=0) {
                alert(row['회원아이디']+'회원이 중복으로 작성 되었습니다. 금액을 확인하고 하나만 써주세요.');
                return false;
            } else {
                userid.push(row['회원아이디']);
            }
            if($.trim(row['회원아이디'])!='' && $.trim(row['주당금액'])=='') {
                alert(row['회원아이디']+'회원의 주당금액을 입력해주세요.');return false;
            }
            if($.trim(row['회원아이디'])!='' && $.trim(row['주식수량'])=='') {
                alert(row['회원아이디']+'회원의 주식수량을 입력해주세요.');return false;
            }
            if($.trim(row['회원아이디'])!='' && $.trim(row['합계금액'])=='') {
                alert(row['회원아이디']+'회원의 합계금액을 입력해주세요.');return false;
            }
            if($.trim(row['회원아이디'])!='' && $.trim(row['보유기간(일)'])=='') {
                alert(row['회원아이디']+'회원의 보유기간을 입력해주세요.');return false;
            }
            if($.trim(row['회원아이디'])!='' && $.trim(row['보유날짜'])=='') {
                alert(row['회원아이디']+'회원의 보유날짜를 입력해주세요.');return false;
            }
            data.push(row);
        }
        if(data.length<1) {
            alert('스톡옵션을 지급할 정보가 없습니다.');
        }
        if(confirm(data.length+'명에게 스톡옵션을 지급하시겠습니까?')) {
            $.post('?', {'pg_mode':'airdrop', 'data':data, 'symbol':getURLParameter('symbol')}, function(r){
                if(r && r.bool) {
                    showResult(r.msg);
                }
            }, 'json');
        }
    });

});