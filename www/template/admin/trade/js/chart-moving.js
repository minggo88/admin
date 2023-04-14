$(function() {

    // const data = [34, 43, 43, 35, 44, 32, 15, 22, 46, 33, 86, 54, 73, 53, 12, 53, 23, 65, 23, 63, 53, 42, 34, 56, 76, 15, 54, 23, 44,34, 43, 43, 35, 44, 32, 15, 22, 46, 33, 86, 54, 73, 53, 12, 53, 23, 65, 23, 63, 53, 42, 34, 56, 76, 15, 54, 23, 44,34, 43, 43, 35, 44, 32, 15, 22, 46, 33, 86, 54, 73, 53, 12, 53, 23, 65, 23, 63, 53, 42, 34, 56, 76, 15, 54, 23, 44,34, 43, 43, 35, 44, 32, 15, 22, 46, 33, 86, 54, 73, 53, 12, 53, 23, 65, 23, 63, 53, 42, 34, 56, 76, 15, 54, 23, 44];
    
    let symbol = $('input[name=symbol]').val();
    
    let line_chart = function() {
        $.ajax({
            type: "post",
            dataType: "json",  //xml,html,jeon,jsonp,script,text
            url: API_URL+'/getChartData/',
            data: {'token':getCookie('token'),'symbol':symbol,'exchange':'KRW','period':'1h','return_type':'JSON','cnt':100},
            cache: false,
            success: function (r) {
           
                if (r && r.payload) {
                    
                    let close_data = [];
                    let chart_data = r.payload.split("\n");
                    for (i in chart_data) {
                        if (i>0) {
                            const row = chart_data[i].split("\t"); // date open high low close volume symbol
                            close_data.push(row[4]*1);
                        }
                    }
                    // console.log(close_data);
                    $("#sparkline").sparkline(close_data, {
                        type: 'line',
                        lineWidth: 2,
                        width: '100%',
                        height: '200px',
                        lineColor: '#6288e3',
                        fillColor: '#c3d3f7'
                    }).find('canvas').css('width','100%');

                }
            } 
        });
    }
    line_chart();
    window.stock_line_chart = line_chart;
    $(window).on('resize', function () {
        if (! $("#sparkline").is(':visibled')) return;
        line_chart();
    });

});