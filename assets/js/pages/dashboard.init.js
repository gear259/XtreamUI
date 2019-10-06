$(document).ready(function() {
    $("#datatable").DataTable({
        searching: !1,
        lengthChange: !1,
        paging: false,
        bInfo: false
    });
    var e = {
        chart: {
            height: 380,
            type: "line",
            padding: {
                right: 0,
                left: 0
            },
            stacked: !1,
            toolbar: {
                show: !1
            }
        },
        stroke: {
            width: [0, 4, 4],
            curve: "smooth"
        },
        plotOptions: {
            bar: {
                columnWidth: "50%"
            }
        },
        colors: ["#23b397", "#f8cc6b", "#f0643b"],
        series: [{
            name: "Capacity",
            type: "column",
            data: [23, 11, 22, 27, 13, 22, 37, 21, 44, 22, 30]
        }, {
            name: "Input Stream",
            type: "line",
            data: [44, 55, 41, 67, 22, 43, 21, 41, 56, 27, 43]
        }, {
            name: "Output Stream",
            type: "line",
            data: [30, 25, 36, 30, 45, 35, 64, 52, 59, 36, 39]
        }],
        fill: {
            opacity: [.85, .5, .5],
            gradient: {
                inverseColors: !1,
                shade: "light",
                type: "vertical",
                opacityFrom: .85,
                opacityTo: .55,
                stops: [0, 100, 100, 100]
            }
        },
        labels: ["01/01/2018", "02/01/2018", "03/01/2018", "04/01/2018", "05/01/2018", "06/01/2018", "07/01/2018", "08/01/2018", "09/01/2018", "10/01/2018", "11/01/2018"],
        markers: {
            size: 0
        },
        legend: {
            offsetY: -10
        },
        xaxis: {
            type: "datetime"
        },
        yaxis: {
            labels: {
                show: !0
            },
            title: {
                text: "Percentage %",
                offsetX: -10,
                offsetY: 0
            }
        },
        tooltip: {
            shared: !0,
            intersect: !1,
            y: {
                formatter: function(e) {
                    return void 0 !== e ? e.toFixed(0) + "%" : e
                }
            }
        },
        grid: {
            borderColor: "#f1f3fa"
        }
    };
    new ApexCharts(document.querySelector("#network-statistics"), e).render();
});