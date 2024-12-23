<div class="box chart-box chart-line">
	<p class="chart-title has-large-font-size">Title of the chart</p>
	<p class="vertical-text">Percentage of respondents</p>
	<div class="data-column">
		<span>50%</span>
		<span>40%</span>
		<span>30%</span>
		<span>20%</span>
		<span>10%</span>
		<span>0%</span>
	</div>
	<canvas id="chart<?php echo $pid; ?>"></canvas>
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
	var chart<?php echo $pid; ?> = document.getElementById("chart<?php echo $pid; ?>");
	var data1 = {
	  label: "Yes",
	  data: [45, 47, 44, 41, 44, 40, 42, 39, 50, 55,50,45],
	  borderWidth: 3,
	  pointRadius: 0,
	  lineTension: 0,
	  lineTension: 0,
	  fill: false,
	  borderColor: '#151515',
	  backgroundColor: '#151515',
	};

	var data2 = {
	  label: "Maybe",
	  data: [34, 35, 33, 35, 33, 34, 35, 37, 35, 37,50,45],
	  borderWidth: 3,
	  pointRadius: 0,
	  lineTension: 0,
	  lineTension: 0,
	  fill: false,
	  borderColor: '#A3A3A3',
	  backgroundColor: '#A3A3A3',
	};

	var data3 = {
	  label: "No",
	  data: [24, 18, 16, 16, 15, 14, 17, 15, 17, 13,50,45],
	  borderWidth: 3,
	  pointRadius: 0,
	  lineTension: 0,
	  lineTension: 0,
	  fill: false,
	  borderColor: '#6A6A6A',
	  backgroundColor: '#6A6A6A',
	};

	var data4 = {
	  label: "No comment",
	  data: [8, 9, 10, 9, 11, 13, 15, 17, 18, 19,50	,45],
	  borderWidth: 3,
	  pointRadius: 0,
	  lineTension: 0,
	  lineTension: 0,
	  fill: false,
	  borderColor: '#D9D9D9',
	  backgroundColor: '#D9D9D9',
	};
	var speedData = {
	  labels: ['',"2011", "2012", "2013", "2014", "2015", "2016", "2017", "2018", "2019", "2020", "2021", "2022"],
	  datasets: [
	  	<?php foreach($datas as $da) { ?>
	  	{
		  label: "<?php echo $da['label']; ?>",
		  data: [<?php echo $da['value']; ?>],
		  borderWidth: window.innerWidth < 767 ? 1 : 3,
		  pointRadius: 0,
		  lineTension: 0,
		  lineTension: 0,
		  fill: false,
		  borderColor: '<?php echo $da['area_color']; ?>',
		  backgroundColor: '<?php echo $da['area_color']; ?>',
		},
		<?php } ?>
	  ]
	};

	var chartOptions = {
		aspectRatio: window.innerWidth < 767 ? 1.5 : 2,
	  plugins: {
	    legend: {
	      position: 'bottom',
	      display: false,
	      labels: {
	        boxWidth: 12,
	        fontColor: '#101C3D'
	      },
	    },
	    title: {
	        display: true,
	        text: "Percentage of respondents",
	        position: 'bottom',
	        color: '#7A7A7A',
	        font:{
	        	size: window.innerWidth < 767 ? 7 : 16,
	        	family: 'DM Sans',
	        	weight: 400,
				font: 14,
	        }
	   	},
	  },
	  scales: {
	    x: {
	        stacked: true,
	        ticks: {
	          color:'#7A7A7A',
	          font: {
	            size: window.innerWidth < 767 ? 7 : 12,
	            family: 'DM Sans',
	            weight: 400,
				  font: 14,
	          },
	        },
	        grid: {
	          display: false,
	        }
	    },
	    y: {
	      border: {
	        display: false,
	      },
	      title: {
	      	display: false,
	      },
	        max: 50,
	        stepSize: 10,
	        min: 0,
	      ticks: {
	        display: true,
	        color:  '#fff',
	        font: {
	          size: window.innerWidth < 767 ? 10 : 16,
	          family: 'DM Sans',
	          weight: 400,
				font: 14,
	        },
	        beginAtZero: false,
	        callback: function (value, index, values) {
	          return value + '%';
	        },
	      },
	    },
	  },
	  layout: {
	    padding: {
	      top: window.innerWidth < 767 ? 30 : 70,
	    },
	  },
	};
	new Chart(chart<?php echo $pid; ?>, {
	  type: 'line',
	  data: speedData,
	  options: chartOptions,
	});
})
</script>