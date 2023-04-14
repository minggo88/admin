$(function() {

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
            var totals = ['USD'];
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
