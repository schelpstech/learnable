$(document).ready(function(){
    $.ajax({
      url: "./chart.php",
      method: "GET",
      success: function(data) {
        console.log(data);
        var subject = [];
        var score = [];
       
        for(var i in data) {
          subject.push(data[i].sbjname);
          score.push(data[i].totalscore);
         
        }
  
        var chartdata = {
          labels: subject,
          datasets : [
            {
              label: 'Subject Performance',
              backgroundColor: 'grey',
              borderColor: 'grey',
              hoverBackgroundColor: 'rgba(200, 200, 200, 1)',
              hoverBorderColor: 'rgba(200, 200, 200, 1)',
              data: score
            }
          ]
       
    };
      
  
        var ctx = $("#mycanvas");
  
        var barGraph = new Chart(ctx, {
          type: 'bar',
          data: chartdata,
          options: {
            responsive: true,
               
                hover: {
                    mode: 'label'
                },
            scales: {
                
                yAxes: [{
                        display: true,
                        ticks: {
                            beginAtZero: true,
                            steps: 10,
                            stepValue: 5,
                            max: 100
                        }
                    }]
            }
           
        }
        
        });
      },
      error: function(data) {
        console.log(data);
      }
    });
  });