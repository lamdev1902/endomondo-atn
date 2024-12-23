<?php 
    if($datas) {
        foreach ($datas as $key => $da) {
            $ho_label .= "'".$da['label']."',";
            $ho_color .= "'".$da['area_color']."',";
            $ho_val .= $da['value'].',';
        }
    }
?>
<div class="box chart-box chart-horizontal">
  <p class="chart-title has-large-font-size">Title of the chart</p>
  <canvas id="chart<?php echo $pid; ?>"></canvas>
  <p class="title-x">Percentage of respondents</p>
  <div class="chart-info">
    <p class="source"><b>Source:</b> Kaiser Family Foundation; CNN © Statista 2023</p>
    <div class="design">Source: <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-chart.svg" alt=""></div>
  </div>
</div>
<style>
  .chart-box .chartjs-size-monitor-expand,
 .chart-box .chartjs-size-monitor-shrink {
    width: 100px !important;
 }
</style>
<script>
jQuery(function($) {
    const myChart<?php echo $pid; ?> = document.getElementById('chart<?php echo $pid; ?>');
    new Chart(myChart<?php echo $pid; ?>, {
      plugins: [ChartDataLabels],
      type: 'bar',
      data: {
        labels: [<?php echo $ho_label; ?>],
        datasets: [{
          data: [<?php echo $ho_val; ?>],
          borderWidth: 1,
          fill: false,
          backgroundColor: [<?php echo $ho_color; ?>],
          datalabels: {
            color: '#151515',
            font: {
              size: window.innerWidth < 991 ? 7 : 16,
              family: 'Arimo',
              weight: 700,
            },
            anchor: 'end',
            align: 'right',
            offset: 16,
            formatter: function (value, context) {
              return value + '%';
            }
          }
        }],

      },
      options: {
        responsive: true,
        indexAxis: 'y',
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              display: false
            },
            ticks: {
              color: '#7A7A7A',
              font: {
                size: window.innerWidth < 991 ? 7 : 16,
                family: 'DM Sans',
                weight: 400,
				  font: 14,
              },
            },
          },
          x: {
            border: {
              display: false,
            },
            max: <?php echo $max_value; ?>,
            ticks: {
              color: '#7A7A7A',
              font: {
                size: window.innerWidth < 991 ? 7 : 16,
                family: 'DM Sans',
                weight: 400,
			  font: 14,
              },
              stepSize: 5,
              callback: function (value, index, values) {
                return value + '%';
              },
            }

          }
        },
        barPercentage: 0.8,
        categoryPercentage: 0.9,
        plugins: {
          legend: {
            position: 'top',
            display: false,
          },
        },
      }
    });
})    
</script>