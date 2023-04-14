$(function() {

    // Configuration for jqGrid Example 2
    $("#table_list").jqGrid({
        url:'/admin/presaleAdmin.php?pg_mode=presale',
        datatype: "json",
        height: '100%',
        autowidth: true,
        shrinkToFit: true,
        rowNum: 20,
        rowList: [10, 20, 30, 40, 50],
        colNames:['No','Date','UserID','SCC','Method','Amount','From Address','To Address','IP','Process'],
        colModel:[
            {name:'idx',index:'idx', editable: false, width:50, align:"center", sorttype:"int",search:true},
            {name:'regdate', index:'regdate', editable: false, width:120, align:"center", sorttype:"date", formatter:"Y-m-d"},
            {name:'userid', index:'userid', editable: false, width:120, align:"left", sorttype:"int", formatter:"text"},
            {name:'scc', index:'scc', editable: false, width:70, align:"right", sorttype:"int", formatter:"integer"},
            {name:'method', index:'method', editable: false, width:70, align:"center", sorttype:"int", formatter:"text"},
            {name:'amount', index:'amount', editable: false, width:90, align:"right", sorttype:"int", formatter:"text"},
            {name:'from_address', index:'from_address', editable: false, width:230, align:"left", sorttype:"int", formatter:"text"},
            {name:'to_address', index:'to_address', editable: false, width:230, align:"left", sorttype:"int", formatter:"text"},
            {name:'ipaddr',index:'ipaddr', editable: false, width:80, align:"center",sorttype:"float", formatter:"text"},
            {name:'process',index:'process', editable: true, width:70, align:"center",sorttype:"float", formatter:"integer"}
        ],
        pager: "#pager_list",
        viewrecords: true,
        caption: "신청목록관리",
        editurl: "/admin/presaleAdmin.php",
        add: true,
        edit: true,
        addtext: 'Add',
        edittext: 'Edit',
        hidegrid: false,
        sortorder: "desc"
    });

    $("#table_list").jqGrid('setGroupHeaders', {
        useColSpanStyle: false
    });

    // Setup buttons
    $("#table_list").jqGrid('navGrid', '#pager_list',
            {edit: true, add: false, del: true, search: true},
            {height: 400, reloadAfterSubmit: true}
    );

    $("#table_list").jqGrid('inlineNav',"#pager_list");

    // Add responsive to jqGrid
    $(window).bind('resize', function () {
        var width = $('.jqGrid_wrapper').width();
        // $('#table_list').setGridWidth(width);
    });


    setTimeout(function(){
        $('.wrapper-content').removeClass('animated fadeInRight');
    },700);
    
});
