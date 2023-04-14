$(function() {
    /*
    var d1 = [[1262304000000, 6], [1264982400000, 3057], [1267401600000, 20434], [1270080000000, 31982], [1272672000000, 26602], [1275350400000, 27826], [1277942400000, 24302], [1280620800000, 24237], [1283299200000, 21004], [1285891200000, 12144], [1288569600000, 10577], [1291161600000, 10295]];
    var d2 = [[1262304000000, 5], [1264982400000, 200], [1267401600000, 1605], [1270080000000, 6129], [1272672000000, 11643], [1275350400000, 19055], [1277942400000, 30062], [1280620800000, 39197], [1283299200000, 37000], [1285891200000, 27000], [1288569600000, 21000], [1291161600000, 17000]];

    var data1 = [
        { label: "Data 1", data: d1, color: '#17a084'},
        { label: "Data 2", data: d2, color: '#fff8dc' }
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

    // "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
    var lineData = {
        labels: ["April", "May", "June", "July", "August", "September", "October"],
        datasets: [
            {
                label: "배정 과금액",
                backgroundColor: "rgba(247,228,101,0.5)",
                borderColor: "rgba(240,193,7,0.8)",
                pointBackgroundColor: "rgba(240,193,7,1)",
                pointBorderColor: "#fff",
                data: [65, 59, 40, 110, 360, 450, 600]
            },
            {
                label: "질의 과금액",
                backgroundColor: "rgba(101,211,189,0.5)",
                borderColor: "rgba(23,160,132,0.8)",
                pointBackgroundColor: "rgba(23,160,132,1)",
                pointBorderColor: "#fff",
                data: [48, 48, 60, 390, 560, 670, 850]
            },
            {
                label: "총 과금액",
                backgroundColor: "rgba(249,220,212,0.5)",
                borderColor: "rgba(250,72,25,0.8)",
                pointBackgroundColor: "rgba(250,72,25,1)",
                pointBorderColor: "#fff",
                data: [113, 107, 100, 500, 920, 1120, 1450]
            }
        ]
    };

    var lineOptions = {
        responsive: true
    };


    var ctx = document.getElementById("lineChart").getContext("2d");
    new Chart(ctx, {type: 'line', data: lineData, options:lineOptions});

});
