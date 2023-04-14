$(function () {

    var start_get_published_at = '';
    var end_get_published_at = '';

    start_get_published_at = $('input[name=start_published_at]').val();
    end_get_published_at = $('input[name=end_published_at]').val();
    
    if(start_get_published_at) {
        $('#reportrange span').html(start_get_published_at + ' - ' + end_get_published_at);
    } else {
        $('#reportrange span').html(moment().subtract(30, 'days').format('YYYY-MM-DD') + ' - ' + moment().format('YYYY-MM-DD'));
    };
    
    $('#reportrange').daterangepicker({
        format: 'YYYY-MM-DD',
        startDate: moment().subtract(29, 'days'),
        endDate: moment(),
        minDate: '2017-12-07',
        maxDate: '2020-12-31',
        dateLimit: { days: 365 },
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

    // 지역별
    $.ajax({
        type: "post",
        dataType: "json",  //xml,html,jeon,jsonp,script,text
        url: "/laboratory/admin/labAdmin.php",
        data: {
            'pg_mode':'newlocationavg',
            'start_published_at' : start_get_published_at,
            'end_published_at' : end_get_published_at
        },          
        cache: false,
        success: function(data){
            
            var locations = [];
            var counts = [];
            $(data.data).each(function(){
                locations.push(this.name);
                counts.push(this.count);
            });
            
            // barChart
            var barData = {
                labels: locations,
                datasets: [
                    {
                        label: "플랜요청 지역별 New",
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
        
        
            var ctx1 = document.getElementById("barChart_location").getContext("2d");
            new Chart(ctx1, {type: 'bar', data: barData, options:barOptions});
            
        }
    });  

    // 나이별
    $.ajax({
        type: "post",
        dataType: "json",  //xml,html,jeon,jsonp,script,text
        url: "/laboratory/admin/labAdmin.php",
        data: {
            'pg_mode':'newageavg',
            'start_published_at' : start_get_published_at,
            'end_published_at' : end_get_published_at
        },          
        cache: false,
        success: function(data){
            
            var ages = [];
            var counts = [];
            $(data.data).each(function(){
                if(this.age==0) age = '태아';
                else if(this.age==5) age = '어린이';
                else if(this.age==15) age = '10대';
                else if(this.age==25) age = '20대';
                else if(this.age==35) age = '30대';
                else if(this.age==45) age = '40대';
                else if(this.age==55) age = '50대';
                ages.push(age);
                counts.push(this.count);
            });
            
            // barChart
            var barData = {
                labels: ages,
                datasets: [
                    {
                        label: "플랜요청 나이대별 New",
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
    
            var ctx2 = document.getElementById("barChart_age").getContext("2d");
            new Chart(ctx2, {type: 'bar', data: barData, options:barOptions});
            
        }
    });  
    
    // 직업등급별
    $.ajax({
        type: "post",
        dataType: "json",  //xml,html,jeon,jsonp,script,text
        url: "/laboratory/admin/labAdmin.php",
        data: {
            'pg_mode':'newjobavg',
            'start_published_at' : start_get_published_at,
            'end_published_at' : end_get_published_at
        },          
        cache: false,
        success: function(data){
            
            var jobs = [];
            var counts = [];
            $(data.data).each(function(){
                if(this.job_grade==1)  job_grade = '1등급';
                else if(this.job_grade==2) job_grade = '2등급';
                else if(this.job_grade==3) job_grade = '3등급';
                jobs.push(job_grade);
                counts.push(this.count);
            });
            
            // barChart
            var barData = {
                labels: jobs,
                datasets: [
                    {
                        label: "플랜요청 직업등급별 New",
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
    
            var ctx3 = document.getElementById("barChart_job").getContext("2d");
            new Chart(ctx3, {type: 'bar', data: barData, options:barOptions});
            
        }
    });
    
    // 성별
    $.ajax({
        type: "post",
        dataType: "json",  //xml,html,jeon,jsonp,script,text
        url: "/laboratory/admin/labAdmin.php",
        data: {
            'pg_mode':'newsexavg',
            'start_published_at' : start_get_published_at,
            'end_published_at' : end_get_published_at
        },          
        cache: false,
        success: function(data){
            
            var sexs = [];
            var counts = [];
            $(data.data).each(function(){
                if(this.sex==0) sex = '태아';
                else if(this.sex==1) sex = '남자';
                else if(this.sex==2) sex = '여자';
                sexs.push(sex);
                counts.push(this.count);
            });
            
            // doughnutChart
            var doughnutData = {
                labels: sexs,
                datasets: [{
                    label: "플랜요청 성별 Published New",
                    data: counts,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(153, 102, 255, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255,99,132,1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            } ;
        


        
            var doughnutOptions = {
                responsive: true
            };
        
        
            var ctx4 = document.getElementById("doughnutChart_sex").getContext("2d");
            new Chart(ctx4, {type: 'doughnut', data: doughnutData, options:doughnutOptions});

            
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