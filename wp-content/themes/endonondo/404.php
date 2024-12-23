<?php get_header(); ?>
<main id="content">
	<div class="page-top-white">
		<div class="container">
			<div class="breacrump">
				<a href="<?php echo home_url(); ?>">Home</a>
				<span class="line">|</span>
				<span>404</span>
			</div>
		</div>
	</div>
	<div class="error-main">
		<div class="container">
			<div class="error-box list-flex flex-middle">
				<div class="info text-center">
					<h1 class="text-uppercase">Oops! That page can’t be found.</h1>
					<p>The page requested couldn’t be found. This could be a spelling error in the URL or a removed page</p>
					<a class="ed-btn on-pc" href="<?php echo home_url(); ?>">Home</a>
					<a class="ed-btn on-sp" href="<?php echo home_url(); ?>">Back</a>
				</div>
				<div class="featured">
					<img src="<?php echo get_template_directory_uri(); ?>/assets/images/error.png" alt="">
				</div>
			</div>
		</div>
	</div>
</main>
<?php get_footer(); ?>