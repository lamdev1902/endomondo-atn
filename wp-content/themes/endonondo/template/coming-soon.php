<?php
/* Template Name: Coming Soon */
$pageid = get_the_ID();
get_header();
the_post();
?>
<main id="content" class="coming">
	<section class="coming-main" style="
	background-image: url(<?= get_template_directory_uri() . '/assets/images/coming/coming-bg-hero.svg' ?>)">
		<div class="container">
			<div class="content flex">
				<div class="left">
					<h1 class="title">WE ARE LAUNCHING SOON</h1>
					<p class="">We've have helped 1.542,335 people get in shape</p>
					<a href="#klaviyo-form" class="">Get me the Lifetime Deal</a>
				</div>
				<div class="right">
					<div class="coming-img">
						<img src="<?= get_template_directory_uri() . '/assets/images/coming/coming-mb.svg' ?>" alt="">
					</div>
				</div>
			</div>
			<div class="social-box">
				<div class="social-coming">
					<a target="_blank" href="https://www.youtube.com/@endomondodotcom"><img
							src="https://wordpress-1308981-4772530.cloudwaysapps.com/wp-content/uploads/2024/01/youtube.svg"></a>
					<a target="_blank" href="https://www.pinterest.com/endomondo/"><img
							src="https://wordpress-1308981-4772530.cloudwaysapps.com/wp-content/uploads/2024/01/pinterest-1.svg"></a>
					<a target="_blank" href="https://www.instagram.com/workoutendomondo/"><img
							src="https://wordpress-1308981-4772530.cloudwaysapps.com/wp-content/uploads/2024/01/instagram-2-1.svg"></a>
					<a target="_blank" href="https://tiktok.com/@workoutendomondo"><img
							src="https://wordpress-1308981-4772530.cloudwaysapps.com/wp-content/uploads/2024/04/Asset-1.svg"></a>
				</div>
			</div>
		</div>
	</section>
	<section class="coming-feature">
		<div class="container">
			<div class="title">
				<h3>Feature</h3>
			</div>
			<div class="content flex">
				<div class="left">
					<div class="feature-item flex">
						<div class="feature-img">
							<img src="<?= get_template_directory_uri() . '/assets/images/coming/coming-icon-1.svg' ?>"
								alt="">
						</div>
						<div class="feature-description">
							<p class="description">1000+ Exercises</p>
							<span>Explore a diverse range of exercises from strength training to yoga, tailored to all
								fitness levels.</span>
						</div>
					</div>

					<div class="feature-item flex">
						<div class="feature-img">
							<img src="<?= get_template_directory_uri() . '/assets/images/coming/coming-icon-2.svg' ?>"
								alt="">
						</div>
						<div class="feature-description">
							<p class="description">1,000,000+ Members Community</p>
							<span>Join a dynamic community for support, motivation, and shared progress.</span>
						</div>
					</div>

					<div class="feature-item flex">
						<div class="feature-img">
							<img src="<?= get_template_directory_uri() . '/assets/images/coming/coming-icon-3.svg' ?>"
								alt="">
						</div>
						<div class="feature-description">
							<p class="description">Exclusive Training Plan</p>
							<span>Access expert-designed plans for personalized fitness goals like weight loss or muscle
								gain.</span>
						</div>
					</div>

					<div class="feature-item flex">
						<div class="feature-img">
							<img src="<?= get_template_directory_uri() . '/assets/images/coming/coming-icon-4.svg' ?>"
								alt="">
						</div>
						<div class="feature-description">
							<p class="description">Realtime Progress Tracking</p>
							<span>Monitor your workouts, calorie burn, and achievements with detailed, real-time
								insights.</span>
						</div>
					</div>
				</div>
				<div class="right">
					<div class="feature-img">
						<img src="<?= get_template_directory_uri() . '/assets/images/coming/coming-ft-img.svg' ?>"
							alt="">
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="coming-countdown">
		<div class="container">
			<?php
			$days = 30;
			$hours = 00;
			$min = 00;
			$sec = 00;
			?>
			<div class="content">
				<div class="top">
					<div class="title">
						<h3>Be the First to Know When the App Launches</h3>
					</div>
					<div class="time flex">
						<div class="time-item">
							<p class="days"><?= $days ?></p>
							<span>Days</span>
						</div>
						<div class="time-item">
							<p class="hours"><?= $hours ?></p>
							<span>Hours</span>
						</div>
						<div class="time-item">
							<p class="min"><?= $min ?></p>
							<span>Minutes</span>
						</div>
						<div class="time-item">
							<p class="sec"><?= $sec ?></p>
							<span>Seconds</span>
						</div>
					</div>
					<div class="keep-button">
						<a href="#klaviyo-form" class="keep">KEEP ME IN THE LOOP</a>
					</div>
				</div>
				<div class="bottom">
					<div class="countdown-list flex">
						<div class="countdown-img">
							<img src="<?= get_template_directory_uri() . '/assets/images/coming/coming-cd-1.svg' ?>"
								alt="">
						</div>
						<div class="countdown-img">
							<img src="<?= get_template_directory_uri() . '/assets/images/coming/coming-cd-2.svg' ?>"
								alt="">
						</div>
						<div class="countdown-img">
							<img src="<?= get_template_directory_uri() . '/assets/images/coming/coming-cd-3.svg' ?>"
								alt="">
						</div>
						<div class="countdown-img">
							<img src="<?= get_template_directory_uri() . '/assets/images/coming/coming-cd-4.svg' ?>"
								alt="">
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="coming-workout">
		<div class="container"><div class="workout-bg"></div>
			<div class="workout-container">
				<div class="title">
					<h3>Unleash Your Potential with Personalized Workouts!</h3>
				</div>
				<div class="workout-list flex">
					<div class="workout-item">
						<div class="workout-img">
							<img src="<?= get_template_directory_uri() . '/assets/images/coming/coming-wo-1.svg' ?>"
								alt="">
						</div>
					</div>
					<div class="workout-item">
						<div class="workout-img">
							<img src="<?= get_template_directory_uri() . '/assets/images/coming/coming-wo-2.svg' ?>"
								alt="">
						</div>
					</div>
					<div class="workout-item">
						<div class="workout-img">
							<img src="<?= get_template_directory_uri() . '/assets/images/coming/coming-wo-3.svg' ?>"
								alt="">
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</main>
<div id="klaviyo-form" class="klaviyo-form-email">
	<div class="container">
		<div class="klaviyo-form-Wms4wM"></div>
	</div>
</div>
<?php get_footer(); ?>

<script>
    $(document).ready(function() {
		function formatNumber(number) {
			return number < 10 ? '0' + number : number;
		}

        var $days = $('.days');
        var $hours = $('.hours');
        var $minutes = $('.min');
        var $seconds = $('.sec');

        var days = <?= $days ?>;
        var hours = <?= $hours ?>;
        var minutes = <?= $min ?>;
        var seconds = <?= $sec ?>;

        var totalSeconds = days * 24 * 60 * 60 + hours * 60 * 60 + minutes * 60 + seconds;

        function updateTime() {
            var d = Math.floor(totalSeconds / (24 * 60 * 60));
            var h = Math.floor((totalSeconds % (24 * 60 * 60)) / (60 * 60));
            var m = Math.floor((totalSeconds % (60 * 60)) / 60);
            var s = totalSeconds % 60;
			
			d = formatNumber(d);
			h = formatNumber(h);
			m = formatNumber(m);
			s = formatNumber(s);
			
            $days.text(d);
            $hours.text(h);
            $minutes.text(m);
            $seconds.text(s);

            if (totalSeconds > 0) {
                totalSeconds--;
            }
        }

        updateTime();

        setInterval(updateTime, 1000);
    });
</script>