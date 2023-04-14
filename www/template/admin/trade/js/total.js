$(function () {
    let search_profit = function(){
        let $target = $('[name=table_profit]');
        let $loading = $('[name=loading]', $target);
        let $empty = $('[name=empty]', $target);
        $loading.show();
        $empty.hide();
        $target.find('[name=data]').remove();
        let name = urldecode(getURLParameter('name'));
        $.post('//api.'+(window.location.host.replace('www.',''))+'/v1.0/getCurrency/', {'name':name, 'cal_base_price':'Y'}, function(r){
            if(r && r.success) {
                // set total value
                // set coin value
                if(r.payload) {
                    let $tpl = $('<div></div>').append($('[name=tpl]', $target).clone().attr('name','data').show());
                    let c = r.payload;
                    if(c) {
                        let html = [];
                        for(s in c) {
                            let _tpl = $tpl.html();
                            let t = c[s];
                            if(t.tradable==='Y') {
                                let logo = t.icon_url ? t.icon_url : "/img/favicon.png";
                                let cv = t.cv ? [t.cv] : [5,-10,-30,-20,-13,-4,-7,20,30,15,16,27,38,60];

                                html.push( _tpl
                                    .replace(/\{coin.symbol\}/g,t.symbol.toLowerCase())
                                    .replace(/\{coin.icon_url\}/g,logo)
                                    .replace(/\{coin.SYMBOL\}/g,t.symbol)
                                    .replace(/\{coin.name\}/g,t.name)
                                    .replace(/\{coin.trade_max_volume\}/g,real_number_format(t.trade_max_volume))
                                    .replace(/\{coin.market_cap\}/g,real_number_format(t.market_cap))
                                    .replace(/\{coin.circulating_supply\}/g,real_number_format(t.circulating_supply))
                                    .replace(/\{coin.base_price\}/g, t.base_price>0 ? real_number_format(t.base_price) : real_number_format(t.price) )
                                    .replace(/\{coin.out_min_volume\}/g,real_number_format(t.out_min_volume))
                                    .replace(/\{coin.chart_value\}/g,cv)
                                );
                            }
                        }
                        $target.append(html.join(''));
                    } else {
                        $empty.show();
                    }
                    $loading.hide();
                }
            }
        },'json');
    };
    search_profit();

    // $('#srchform').submit(function() {
    //     if($('*').is('select[name=loop_scale]')) {
    //         var loop_scale = $('select[name=loop_scale]').val();
    //         $('input[name=loop_scale]').val(loop_scale);
    //     }
    // });
    $('[name="btn-search"]').on('click', function(){
        search_profit();
    });

    $(".bar").peity("bar", {
        fill: ["#628fc2", "#ef9595"],
        width: 70,
        height: 40
    })


    let line_chart = function() {

        $.ajax({
            type: "post",
            dataType: "json",  //xml,html,jeon,jsonp,script,text
            url: '//api.'+(window.location.host.replace('www.',''))+'/v1.0/getChartData/',
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
                    console.log(close_data);
                    $("#sparkline_"+symbol).sparkline(close_data, {
                        type: 'line',
                        lineWidth: 2,
                        width: '100%',
                        height: '200px',
                        lineColor: '#6288e3',
                        fillColor: '#c3d3f7'
                    });

                }
            } 
        });
    }
    // line_chart();

    // var updatingChart = $(".updating-chart").peity("line", { fill: '#f0f0f0',stroke:'#0000ff', width: 64 })

    // setInterval(function() {

    //     updatingChart
    //         .text(values.join(","))
    //         .change()
    // }, 1000);
    
});
