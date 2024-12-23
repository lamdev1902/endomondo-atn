<?php 
    if($datas) {
        foreach ($datas as $key => $da) {
            $ho_label .= "'".$da['label']."',";
            $ho_color .= "'".$da['area_color']."',";
            $ho_val .= $da['value'].',';
        }
    }
?>
<div class="box chart-box chart-pie">
	<p class="chart-title has-large-font-size">Title of the chart</p>
	<canvas id="chart<?php echo $pid; ?>"></canvas>
	<div class="chart-info">
		<p class="source"><b>Source:</b> Kaiser Family Foundation; CNN © Statista 2023</p>
		<div class="design">Source: <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-chart.svg" alt=""></div>
	</div>
</div>
<script>
	jQuery(function($) {
		new Chart("chart<?php echo $pid; ?>", {
		  plugins: [ChartDataLabels],
		  type: "pie",
		  data: {
		    labels: [<?php echo $ho_label; ?>],
		    datasets: [{
		      backgroundColor: [<?php echo $ho_color; ?>],
		      data: [<?php echo $ho_val; ?>],
		      datalabels: {
		        color: '#151515',
		        font: {
		          size: window.innerWidth < 991 ? 7 : 16,
		          family: 'Inter',
		          weight: 400,
		        },
		        display: true,
		        align: 'end',
		        offset: window.innerWidth < 991 ? 40 : 100,
		        formatter: function (value, context) {
					return value.toFixed(0) + '%\n' + context.chart.data.labels[context.dataIndex];
		        },
		         parseHTML: true,
		      },
		      radius: window.innerWidth < 991 ? 57 : 130,
		    }]
		  },
		  options: {
		    maintainAspectRatio: false,
		    plugins: {
		      legend: {
		        display: false,
		      },
		    },
		    layout: {
		      padding: {
		        left: 70,
		        right: 70,
		        top: 70,
		        bottom: 70
		      }
		    },

		  }
		});
	})
</script>