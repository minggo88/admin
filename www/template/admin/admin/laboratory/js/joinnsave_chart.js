$(function () {

    var start_get_created_at = '';
    var end_get_created_at = '';

    start_get_created_at = $('input[name=start_created_at]').val();
    end_get_created_at = $('input[name=end_created_at]').val();
    
    if(start_get_created_at) {
        $('#reportrange span').html(start_get_created_at + ' - ' + end_get_created_at);
    } else {
        $('#reportrange span').html(moment().subtract(30, 'days').format('YYYY-MM-DD') + ' - ' + moment().format('YYYY-MM-DD'));
    };

    $('#reportrange').daterangepicker({
        format: 'YYYY-MM-DD',
        startDate: moment().subtract(29, 'days'),
        endDate: moment(),
        minDate: '2018-04-01',
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

        var start_created_at = '';
        var end_created_at = '';
        start_created_at = start.format('YYYY-MM-DD');
        end_created_at = end.format('YYYY-MM-DD');
        
        $('input[name=start_created_at]').val(start_created_at);
        $('input[name=end_created_at]').val(end_created_at);

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
            'pg_mode':'joinnsave',
            'start_created_at' : start_get_created_at,
            'end_created_at' : end_get_created_at
        },          
        cache: false,
        success: function(data){
            
            var dates = [];
            var customers = [];
            var chargings = [];
            $(data.data).each(function(){
                dates.push(this.date);
                customers.push(this.member_cnt);
                chargings.push(this.charging);
            });
            
            // barChart
            var barData = {
                labels: dates,
                datasets: [
                    {
                        label: "일반회원",
                        backgroundColor: 'rgba(26,179,148,0.5)',
                        borderColor: "rgba(26,179,148,0.7)",
                        pointBackgroundColor: "rgba(26,179,148,1)",
                        pointBorderColor: "#fff",
                        data: customers
                    }
                ]
            };
        
            var barOptions = {
                responsive: true
            };
        
        
            var ctx2 = document.getElementById("barChart_joinnsave").getContext("2d");
            new Chart(ctx2, {type: 'bar', data: barData, options:barOptions});
            
        }
    });  

});