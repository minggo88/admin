
  $(document).ready(function() {

    /*
    var d1 = [[1262304000000, 6], [1264982400000, 3057], [1267401600000, 20434], [1270080000000, 31982], [1272672000000, 26602], [1275350400000, 27826], [1277942400000, 24302], [1280620800000, 24237], [1283299200000, 21004], [1285891200000, 12144], [1288569600000, 10577], [1291161600000, 10295]];
    var d2 = [[1262304000000, 5], [1264982400000, 200], [1267401600000, 1605], [1270080000000, 6129], [1272672000000, 11643], [1275350400000, 19055], [1277942400000, 30062], [1280620800000, 39197], [1283299200000, 37000], [1285891200000, 27000], [1288569600000, 21000], [1291161600000, 17000]];

    var data1 = [
        { label: "Data 1", data: d1, color: '#17a084'},
        { label: "Data 2", data: d2, color: '#127e68' }
    ];
    $.plot($("#flot-chart1"), data1, {
        xaxis: {
            tickDecimals: 0
        },
        series: {
            lines: {
                show: true,
                fill: true,
                fillColor: {
                    colors: [{
                        opacity: 1
                    }, {
                        opacity: 1
                    }]
                },
            },
            points: {
                width: 0.1,
                show: false
            },
        },
        grid: {
            show: false,
            borderWidth: 0
        },
        legend: {
            show: false,
        }
    });
    */
   /*
    var lineData = {
        labels: ["January", "February", "March", "April", "May", "June", "July"],
        datasets: [
            {
                label: "Example dataset",
                backgroundColor: "rgba(26,179,148,0.5)",
                borderColor: "rgba(26,179,148,0.7)",
                pointBackgroundColor: "rgba(26,179,148,1)",
                pointBorderColor: "#fff",
                data: [48, 48, 60, 39, 56, 37, 30]
            },
            {
                label: "Example dataset",
                backgroundColor: "rgba(220,220,220,0.5)",
                borderColor: "rgba(220,220,220,1)",
                pointBackgroundColor: "rgba(220,220,220,1)",
                pointBorderColor: "#fff",
                data: [65, 59, 40, 51, 36, 25, 40]
            }
        ]
    };

    var lineOptions = {
        responsive: true
    };


    var ctx = document.getElementById("lineChart").getContext("2d");
    new Chart(ctx, {type: 'line', data: lineData, options:lineOptions});
*/

});


$(function() {
/*
    $.ajax({
        type: "post",
        dataType: "json",  //xml,html,json,jsonp,script,text
        url: "/admin/",
        data: {
            'pg_mode':'revenue'
        },          
        cache: false,
        success: function(data){
            
            // BTC, ETH, LTC, USD
            var dates = [];
            var bitcoins = ['BTC'];
            var ethereums = ['ETH'];
            var lightcoins = ['LTC'];
            var dollars = ['USD x 1,000'];
            var totals = ['KRW'];
            $(data.data).each(function(){
                dates.push(this.date);
                bitcoins.push(this.bitcoin);
                ethereums.push(this.ethereum);
                lightcoins.push(this.lightcoin);
                dollars.push(this.dollar);
            });

            var chart = c3.generate({
                bindto: '#lineChart0',
                data: {
                    columns: [
                        bitcoins,
                        ethereums,
                        lightcoins,
                        dollars
                    ],
                    axes: {
                        Presales: 'y'
                    },
                },
                grid: {
                    x: {
                        show: true
                    },
                    y: {
                        show: true
                    }
                },
                axis: {
                    x: {
                        type: 'category',
                        categories: dates
                    },
                    y: {
                        label: {
                            text: 'Coin count, But USD x 1,000'
                        },
                        tick: {
                            format: d3.format("\,")
                            //format: function (d) { return "$" + d; }
                        }
                    },
                    y2: {
                        show: false,
                        label: {
                        text: 'Y2 Label',
                        position: 'outer-middle'
                        }
                    },
                    tooltip: {
                        format: {
                            title: function (d) { return d + ' Month'; },
                            //value: d3.format(',') // apply this format to both y and y2
                        }
                    }
                },
                color: {
                    pattern: [ '#f0c107', '#17a084', '#0961f0', '#750404', '#2ca02c', '#98df8a', '#d62728', '#ff9896', '#9467bd', '#c5b0d5', '#8c564b', '#c49c94', '#e377c2', '#f7b6d2', '#7f7f7f', '#c7c7c7', '#bcbd22', '#dbdb8d', '#17becf', '#9edae5']
                }
            });
        }
    });  

    $.ajax({
        type: "post",
        dataType: "json",  //xml,html,json,jsonp,script,text
        url: "/admin/",
        data: {
            'pg_mode':'coins'
        },          
        cache: false,
        success: function(data){
            
            var months = [];
            var total_chargings = ['충전액'];
            var total_assigns = ['과금액'];
            $(data.data).each(function(){
                months.push(this.month);
                total_chargings.push(this.coin_chargings);
                total_assigns.push(this.sub_total_coin);
            });

            var chart = c3.generate({
                bindto: '#lineChart2',
                data: {
                    columns: [
                        total_chargings,
                        total_assigns
                    ],
                    axes: {
                        충전액: 'y'
                    },
                    type: 'bar' // ADD
                    //types: {
                        //충전액: 'bar' // ADD
                    //}
                    },
                    grid: {
                        x: {
                            show: true
                        },
                        y: {
                            show: true
                        }
                    },
                    axis: {
                        x: {
                            type: 'category',
                            categories: months
                        },
                    y: {
                        label: {
                            text: '금액(원)'
                        },
                        tick: {
                            format: d3.format("\,")
                            //format: function (d) { return "$" + d; }
                        }
                    },
                    y2: {
                        show: false,
                        label: {
                        text: 'Y2 Label',
                        position: 'outer-middle'
                        }
                    },
                    tooltip: {
                        format: {
                            title: function (d) { return d + ' Month'; },
                            value: d3.format(',') // apply this format to both y and y2
                        }
                    }
                }
            });
        }
    });  
*/
    /*
    $.ajax({
        type: "post",
        dataType: "json",  //xml,html,json,jsonp,script,text
        url: "/admin/index.php",
        data: {
            'pg_mode':'halfyear'
        },          
        cache: false,
        success: function(data){
            
            var dates = [];
            var assigns = [];
            var inquiries = [];
            var totals = [];
            $(data.data).each(function(){
                dates.push(this.date);
                assigns.push(this.assign_fee);
                inquiries.push(this.inquiry_fee);
                totals.push(this.total_fee);
            });
                    
            // "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
            var lineData = {
                labels: dates,
                datasets: [
                    {
                        label: "플랜매칭 과금액",
                        backgroundColor: "rgba(247,228,101,0.5)",
                        borderColor: "rgba(240,193,7,0.8)",
                        pointBackgroundColor: "rgba(240,193,7,1)",
                        pointBorderColor: "#fff",
                        data: assigns
                    },
                    {
                        label: "플랜노출 과금액",
                        backgroundColor: "rgba(101,211,189,0.5)",
                        borderColor: "rgba(23,160,132,0.8)",
                        pointBackgroundColor: "rgba(23,160,132,1)",
                        pointBorderColor: "#fff",
                        data: inquiries
                    },
                    {
                        label: "총 과금액",
                        backgroundColor: "rgba(249,220,212,0.5)",
                        borderColor: "rgba(250,72,25,0.8)",
                        pointBackgroundColor: "rgba(250,72,25,1)",
                        pointBorderColor: "#fff",
                        data: totals
                    }
                ]
            };

            var lineOptions = {
                responsive: true
            };


            var ctx = document.getElementById("lineChart").getContext("2d");
            new Chart(ctx, {type: 'line', data: lineData, options:lineOptions});
        }
    });
    */

});
