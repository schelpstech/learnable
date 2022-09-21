$(document).ready(function () {
    $.ajax({
        url: "../../app/chartfetch.php",
        method: "GET",
        success: function (data) {
            console.log(data);
            var subject = [];
            var score = [];

            for (var i in data) {
                subject.push(data[i].sbjname);
                score.push(data[i].totalscore);
            }

            var chartdata = {
                labels: subject,
                datasets: [
                    {
                        label: "Performance",
                        backgroundColor: "#B21F1F",
                        borderColor: "transparent",
                        borderWidth: 2,
                        categoryPercentage: 0.5,
                        hoverBackgroundColor: "#00d8c2",
                        hoverBorderColor: "transparent",
                        data: score
                    },
                ],
            };
            var ctx = $("#mycanvas");
            var barGraph = new Chart(ctx, {
                type: 'bar',
                data: chartdata,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: { display: !1, labels: { fontColor: "#50649c" } },
                    tooltips: {
                        enabled: !0,
                    },
                    scales: {
                        xAxes: [
                            {
                                barPercentage: 0.35,
                                categoryPercentage: 0.4,
                                display: !0,
                                gridLines: {
                                    color: "transparent",
                                    borderDash: [0],
                                    zeroLineColor: "transparent",
                                    zeroLineBorderDash: [2],
                                    zeroLineBorderDashOffset: [2],
                                },
                                ticks: { fontColor: "#a4abc5", beginAtZero: true, padding: 12 },
                            },
                        ],
                        yAxes: [
                            {
                              gridLines: {
                                color: "#8997bd29",
                                borderDash: [3],
                                drawBorder: !1,
                                drawTicks: !1,
                                zeroLineColor: "#8997bd29",
                                zeroLineBorderDash: [2],
                                zeroLineBorderDashOffset: [2],
                              },
                              ticks: {
                                fontColor: "#a4abc5",
                                beginAtZero: true,
                                padding: 12, steps: 10, stepValue: 5, max: 100
                              },
                            },
                          ],
                    },
                },
            });
        },
        error: function (data) {
            console.log(data);
        }
    });
});