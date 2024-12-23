<div class="box chart-box chart-vertical">
	<p class="chart-title has-large-font-size">Title of the chart</p>
	<p class="vertical-text">Percentage of respondents</p>
	<div class="data-column">
		<span>125%</span>
		<span>100%</span>
		<span>75%</span>
		<span>50%</span>
		<span>25%</span>
		<span>0%</span>
	</div>
	<canvas id="chart<?php echo $pid; ?>" height="<?php if(wp_is_mobile()) echo '400'; else echo '600'; ?>"></canvas>
	<div class="chart-value">
		<?php foreach($datas as $da) { ?>
			<p><span style="background: <?php echo $da['area_color']; ?>"></span><?php echo $da['label']; ?></p>
		<?php } ?>
	</div>
	<div class="chart-info">
		<p class="source"><b>Source:</b> Kaiser Family Foundation; CNN © Statista 2023</p>
		<div class="design">Source: <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-chart.svg" alt=""></div>
	</div>
</div>
<script>
	jQuery(function($) {
	const myChart1 = document.getElementById("chart<?php echo $pid; ?>").getContext("2d");
	new Chart(myChart1, {
      plugins: [ChartDataLabels],
	  type: 'bar',
	  data: {
	    labels: [<?php echo $xlabel; ?>],
	    datasets: [
	    <?php foreach($datas as $da) { ?>
	    {
	      label: "<?php echo $da['label']; ?>",
	      backgroundColor: "<?php echo $da['area_color']; ?>",
	      data: [<?php echo $da['value']; ?>],
	    },
		<?php } ?>
	    ],
	  },
	  options: {
	    plugins: {
	      legend: {
	        position: 'bottom',
	        display: false,
	        labels: {
	          boxWidth: 12,
	          fontColor: '#7a7a7a'
	        },
	      },
	      title: {
	        display: true,
	        text: "Percentage of respondents",
	        position: 'bottom',
	        color: '#7a7a7a',
	        font:{
	        	size: window.innerWidth < 767 ? 7 : 16,
	        	family: 'DM Sans',
	        	weight: 400,
	        }
	      },
	      datalabels: {
	        color: '#FFFFFF',
	        font: {
	          size: window.innerWidth < 767 ? 7 : 18,
	          family: 'DM Sans',
	          weight: 600,
	        },
			className: 'has-medium-font-size',
	        anchor: 'center',
	        formatter: function (value, context) {
	          return value + '%';
	        }
	      },
	    },
	    tooltips: {
	      displayColors: true,
	    },
	    scales: {
	      x: {
	        stacked: true,
	        ticks: {
	          color: '#7A7A7A',
	          font: {
	            size: window.innerWidth < 767 ? 7 : 16,
	            family: 'DM Sans',
	            weight: 400,
				   size: window.innerWidth < 767 ? 7 : 16,
	          },
	        },
	        grid: {
	          display: false,
	        }
	      },
	      y: {
	        stacked: true,
	        max: <?php echo $max_value; ?>,
	        border: {
	          display: false,
	        },
	        ticks: {
	        	display: false,
	          color: '#101C3D80',
	          font: {
	            size: window.innerWidth < 767 ? 7 : 16,
	            family: 'DM Sans',
	            weight: 400,
				   size: window.innerWidth < 767 ? 7 : 14,
	          },
	          beginAtZero: true,
	          labelOffset: -10,
	          stepSize: 25,
	          callback: function (value, index, values) {
	            return value + '%';
	          },
	        },
	        type: 'linear',
	      }
	    },
	    responsive: true,
	    maintainAspectRatio: false,
	    barPercentage: 0.8,
	    categoryPercentage:  0.86,
	    layout: {
	      padding: {
	        top: 70,
	      }
	    },
	  },
	});
	})
</script>