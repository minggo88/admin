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
        minDate: '2017-07-01',
        maxDate: '2020-12-31',
        dateLimit: { days: 60 },
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
   


    $.ajax({
        type: "post",
        dataType: "json",  //xml,html,jeon,jsonp,script,text
        url: "/laboratory/admin/labAdmin.php",
        data: {
            'pg_mode':'sellable_sections',
            'start_published_at' : start_get_published_at,
            'end_published_at' : end_get_published_at
        },          
        cache: false,
        success: function(data){
            
            var names = [];
            var counts = [];
            $(data.data.loop_insurers).each(function(){
                names.push(this.name);
                counts.push(this.count);
                
            });

            
            // barChart
            var barData = {
                labels: names,
                datasets: [
                    {
                        label: "섹션별 판매가능 플래너 수",
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
        
        
            var ctx2 = document.getElementById("sellable_sections").getContext("2d");
            new Chart(ctx2, {type: 'bar', data: barData, options:barOptions});


        }
    });  

    
    $.ajax({
        type: "post",
        dataType: "json",  //xml,html,jeon,jsonp,script,text
        url: "/laboratory/admin/labAdmin.php",
        data: {
            'pg_mode':'inquired_sections',
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
            
            
        }
    }); 

});