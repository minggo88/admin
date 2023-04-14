$(function () {

    var start_get_published_at = '';
    var end_get_published_at = '';

    start_get_published_at = $('input[name=start_published_at]').val();
    end_get_published_at = $('input[name=end_published_at]').val();
    
    if(start_get_published_at) {
        $('#reportrange span').html(start_get_published_at + ' - ' + end_get_published_at);
    } else {
        $('#reportrange span').html(moment().subtract(30, 'days').format('YYYY-MM-DD') + ' - 2017-12-06');
    };

    $('#reportrange').daterangepicker({
        format: 'YYYY-MM-DD',
        startDate: moment().subtract(29, 'days'),
        endDate: moment(),
        minDate: '2017-07-01',
        maxDate: '2017-12-06',
        dateLimit: { days: 180 },
        showDropdowns: true,
        showWeekNumbers: true,
        timePicker: false,
        timePickerIncrement: 1,
        timePicker12Hour: true,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
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

        var start_published_at = '';
        var end_published_at = '';
        start_published_at = start.format('YYYY-MM-DD');
        end_published_at = end.format('YYYY-MM-DD');
        
        $('input[name=start_published_at]').val(start_published_at);
        $('input[name=end_published_at]').val(end_published_at);

        $('#reportrange span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
        
    });
   
    $('#srchform').submit(function() {
		if($('*').is('select[name=loop_scale]')) {
			var loop_scale = $('select[name=loop_scale]').val();
            $('input[name=loop_scale]').val(loop_scale);
		}
    });

    $.ajax({
        type: "post",
        dataType: "json",  //xml,html,jeon,jsonp,script,text
        url: "/laboratory/admin/labAdmin.php",
        data: {
            'pg_mode':'houravg',
            'start_published_at' : start_get_published_at,
            'end_published_at' : end_get_published_at
        },          
        cache: false,
        success: function(data){
            
            var hours = [];
            var counts = [];
            $(data.data).each(function(){
                hours.push(this.hour+'H');
                counts.push(this.count);
            });
            
            // barChart
            var barData = {
                labels: hours,
                datasets: [
                    {
                        label: "플랜요청 시간대별 Published",
                        backgroundColor: 'rgba(26,179,148,0.5)',
                        borderColor: "rgba(26,179,148,0.7)",
                        pointBackgroundColor: "rgba(26,179,148,1)",
                        pointBorderColor: "#fff",
                        data: counts
                    }
                ]
            };
        
            var barOptions = {
                responsive: true,
                scales: {
                    xAxes: [{
                        stacked: true
                    }],
                    yAxes: [{
                        stacked: true
                    }]
                }
            };
        
            var ctx2 = document.getElementById("barChart_hour").getContext("2d");
            new Chart(ctx2, {type: 'bar', data: barData, options:barOptions});
            
        }
    });  

    
    $.ajax({
        type: "post",
        dataType: "json",  //xml,html,jeon,jsonp,script,text
        url: "/laboratory/admin/labAdmin.php",
        data: {
            'pg_mode':'weekavg',
            'start_published_at' : start_get_published_at,
            'end_published_at' : end_get_published_at
        },          
        cache: false,
        success: function(data){
            
            var days = [];
            var counts = [];
            $(data.data).each(function(){
                days.push(this.day);
                counts.push(this.count);
            });
            
            // barChart
            var barData = {
                labels: days,
                datasets: [
                    {
                        label: "플랜요청",
                        backgroundColor: 'rgba(26,179,148,0.5)',
                        borderColor: "rgba(26,179,148,0.7)",
                        borderWidth: 1,
                        pointBackgroundColor: "rgba(26,179,148,1)",
                        pointBorderColor: "#fff",
                        data: counts
                    }
                ]
            };
        
            var barOptions = {
                responsive: true,
                scales: {
                    xAxes: [{
                        stacked: true
                    }],
                    yAxes: [{
                        stacked: true
                    }]
                }
            };
    
            var ctx2 = document.getElementById("barChart_week").getContext("2d");
            new Chart(ctx2, {type: 'bar', data: barData, options:barOptions});
            
        }
    });  

    /*
    var polarData = {
        datasets: [{
            data: [
                300,140,200
            ],
            backgroundColor: [
                "#a3e1d4", "#dedede", "#b5b8cf"
            ],
            label: [
                "My Radar chart"
            ]
        }],
        labels: [
            "App","Software","Laptop"
        ]
    };

    var polarOptions = {
        segmentStrokeWidth: 2,
        responsive: true

    };

    var ctx3 = document.getElementById("polarChart").getContext("2d");
    new Chart(ctx3, {type: 'polarArea', data: polarData, options:polarOptions});

    var doughnutData = {
        labels: ["App","Software","Laptop" ],
        datasets: [{
            data: [300,50,100],
            backgroundColor: ["#a3e1d4","#dedede","#b5b8cf"]
        }]
    } ;


    var doughnutOptions = {
        responsive: true
    };


    var ctx4 = document.getElementById("doughnutChart").getContext("2d");
    new Chart(ctx4, {type: 'doughnut', data: doughnutData, options:doughnutOptions});


    var radarData = {
        labels: ["Eating", "Drinking", "Sleeping", "Designing", "Coding", "Cycling", "Running"],
        datasets: [
            {
                label: "My First dataset",
                backgroundColor: "rgba(220,220,220,0.2)",
                borderColor: "rgba(220,220,220,1)",
                data: [65, 59, 90, 81, 56, 55, 40]
            },
            {
                label: "My Second dataset",
                backgroundColor: "rgba(26,179,148,0.2)",
                borderColor: "rgba(26,179,148,1)",
                data: [28, 48, 40, 19, 96, 27, 100]
            }
        ]
    };

    var radarOptions = {
        responsive: true
    };

    var ctx5 = document.getElementById("radarChart").getContext("2d");
    new Chart(ctx5, {type: 'radar', data: radarData, options:radarOptions});
    */

});