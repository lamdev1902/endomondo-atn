<?php
function hc_custom_count_posts_be($postType) {
	$start_date = cformat_date($_POST['date_from']);
	$end_date = cformat_date($_POST['date_to']);
	$args = array(
		'posts_per_page' => -1,
		'post_type' => $postType,
		'suppress_filters' => false
	);
	if ( $start_date &&  $end_date ) {
	    $args['date_query'] =  array(
            array (
            	 'column' => 'post_modified',
            	 'after' => $start_date . ' 00:00:00',
            	 'before' => $end_date . ' 23:59:59'
            )
		    );
	}
 	return count(get_posts($args));
}
function count_of_month_be($postType,$date) {
	$date = explode('-',$date);
	$args = array(
		'posts_per_page' => -1,
		'post_type' => $postType,
		'suppress_filters' => false,
		'date_query' => array(
			array(
				'column' => 'post_modified',
				'year' => $date[0],
				'month' => $date[1]
			)
			
		)
	);
	return count(get_posts($args));
}
function hc_register_update_modifie_date() {
	add_submenu_page(
		'tools.php',
		__( 'Update Date Modifield', 'hc_theme' ),
		__( 'Update Date Modifield', 'hc_theme' ),
		'manage_options',
		'hc-modifield-update',
		'leea_modifield_date_ref_page_callback'
	);
}
function leea_modifield_date_ref_page_callback() {
$user_current = wp_get_current_user();
$userid = $user_current->ID;
if(isset($_POST['cpt'])) $cpt = $_POST['cpt'];
global $wpdb,$post;
function cformat_date($date) {
	if($date && $date != '') {
		$date = explode('-',$date);
		return $date[2].'-'.$date[1].'-'.$date[0];
	}
}
if(isset($_POST['date_from'])) $start_date = cformat_date($_POST['date_from']);
if(isset($_POST['date_to'])) $end_date = cformat_date($_POST['date_to']);
if(isset($_POST['cpt']) && $_POST['cpt'] != '') $cpt = $_POST['cpt'];
/* Update modifield date */
if(isset($_POST['update_action']) && $_POST['update_action'] == $userid) {
	if(isset($_POST['post_tagget']) && $_POST['post_tagget'] != '') { 
		$post_tagget = $_POST['post_tagget'];
		foreach($post_tagget as $pt) {
			$date_rand = date('Y-m-d ', strtotime( '-'.mt_rand(0,7).' days')) . rand(00,23) . ':' . rand(00,59). ':' . rand(00,59);
			$query = "UPDATE $wpdb->posts
			   				SET post_modified = '$date_rand'
			         WHERE ID = $pt";
			$wpdb->query( $query );
		}
	}
}
?>
<div id="updateModifiedDate">
	<div class="cpt-statics">
		<h2>Statistics by post type</h2>
		<table class="table">
			<tr>
				<th></th>
				<?php for ($i = 0; $i <= 12; $i++) {
			    $month = date("m/Y", strtotime( date( 'Y-m-01' )." -$i months")); ?>
				<th class="text-center"><?php echo $month; ?></th>
				<?php } ?>
			</tr>
			<tr>
				<td>Post</td>
				<?php for ($i = 0; $i <= 12; $i++) {
			    $date = date("Y-m", strtotime( date( 'Y-m-01' )." -$i months")); ?>
			    <td class="text-center"><?php echo count_of_month_be('post',$date); ?></td>
			    <?php } ?>	
			</tr>
			<tr>
				<td>Single reviews</td>
				<?php for ($i = 0; $i <= 12; $i++) {
			    $date = date("Y-m", strtotime( date( 'Y-m-01' )." -$i months")); ?>
			    <td class="text-center"><?php echo count_of_month_be('single_reviews',$date); ?></td>
			    <?php } ?>	
			</tr>
			<tr>
				<td>Round up</td>
				<?php for ($i = 0; $i <= 12; $i++) {
			    $date = date("Y-m", strtotime( date( 'Y-m-01' )." -$i months")); ?>
			    <td class="text-center"><?php echo count_of_month_be('round_up',$date); ?></td>
			    <?php } ?>	
			</tr>
			<tr>
				<td>Information post</td>
				<?php for ($i = 0; $i <= 12; $i++) {
			    $date = date("Y-m", strtotime( date( 'Y-m-01' )." -$i months")); ?>
			    <td class="text-center"><?php echo count_of_month_be('informational_posts',$date); ?></td>
			    <?php } ?>	
			</tr>
			<tr>
				<td>Counpon post</td>
				<?php for ($i = 0; $i <= 12; $i++) {
			    $date = date("Y-m", strtotime( date( 'Y-m-01' )." -$i months")); ?>
			    <td class="text-center"><?php echo count_of_month_be('coupon',$date); ?></td>
			    <?php } ?>	
			</tr>
		</table>
	</div>
	<h2>Update date modifield</h2>
	<form action="" method="POST" id="actionfilterForm">
		<div id="filterForm">
			<div class="form-cpt">
				<div class="list-flex checkbox-list">
					<label><input type="checkbox" name="cpt[]" value="post" <?php if(in_array('post',$cpt)) echo 'checked'; ?>> Post (<?php echo hc_custom_count_posts_be('post'); ?>) </label>
					<label><input type="checkbox" name="cpt[]" value="single_reviews" <?php if(in_array('single_reviews',$cpt)) echo 'checked'; ?>> Single reviews (<?php echo hc_custom_count_posts_be('single_reviews'); ?>)</label>
					<label><input type="checkbox" name="cpt[]" value="round_up" <?php if(in_array('round_up',$cpt)) echo 'checked'; ?>> Round up (<?php echo hc_custom_count_posts_be('round_up'); ?>)</label>
					<label><input type="checkbox" name="cpt[]" value="informational_posts" <?php if(in_array('informational_posts',$cpt)) echo 'checked'; ?>> Information posts (<?php echo hc_custom_count_posts_be('informational_posts'); ?>)</label>
					<label><input type="checkbox" name="cpt[]" value="coupon" <?php if(in_array('coupon',$cpt)) echo 'checked'; ?>> Coupon post (<?php echo hc_custom_count_posts_be('coupon'); ?>)</label>
				</div>
			</div>
			<div class="form-field">
				<input type="text" value="<?php echo $start_date; ?>" name="date_from" id="dateFrom" class="form-input form-date" placeholder="From date" autocomplete="off" required>
				<input type="text" value="<?php echo $end_date; ?>" name="date_to" id="dateTo" class="form-input form-date"  placeholder="to date" autocomplete="off" required>
				<input type="hidden" id="fillAction" name="update_action" value="no">
				<button tye="submit">Filter</button>
			</div>
		</div>
		<?php if ( $start_date &&  $end_date ) { ?>
		<div class="table-static">
			<table class="table table-striped">
				<tr>
					<td><input type="checkbox" id="checkboxAll"></td>
					<th>Title</th>
					<th class="text-right">Date modifile</th>
				</tr>
				<?php
					$page_number = 1;
					if(isset($_POST['page_number'])) $page_number = $_POST['page_number'];
					$args = array(
						'post_type' => $cpt,
						'posts_per_page' => 50,
						'paged' => $page_number
					);
					if ( $start_date &&  $end_date ) {
					    $args['date_query'] =  array(
			            array (
			            	 'column' => 'post_modified',
			            	 'after' => $start_date . ' 00:00:00',
			            	 'before' => $end_date . ' 23:59:59'
			            )
					    );
					}
				 	$the_query = new WP_Query( $args );
					while ($the_query->have_posts() ) : $the_query->the_post();
				?>
				<tr>
					<td><input class="checkbox-child" type="checkbox" name="post_tagget[]" value="<?php echo $post->ID; ?>"></td>
					<td><a href="<?php the_permalink(); ?>"><?php the_permalink(); ?></a></td>
					<td class="text-right"><?php echo get_the_modified_date('d/m/Y H:i:s',$post->ID); ?></td>
				</tr>
				<?php
					endwhile;
					wp_reset_query();
				?>
			</table>
		</div>
		<input type="hidden" name="page_number" value="1" id="fillPaged">
		<?php
			$big = 999999999;
			$mcs_paginate_links = paginate_links( array(
				'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'format' => '?paged=%#%',
				'current' => max( 1, $page_number ),
				'total' => $the_query->max_num_pages,
				'prev_text'    => false,
				'next_text'    => false
			  ) );
			 if($mcs_paginate_links) : 
		 ?>
	    <div class="pagi-custom">
			  Page: <?php echo $mcs_paginate_links ?>
			</div>
		<?php endif; ?>
		<div class="text-center"><a href="" class="update-action" data-toggle="modal" data-target="#confirmModal">Update</a></div>
		<?php } ?>
	</form>
</div>
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-body">
        	Are you sure you want to update?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">NO</button>
        <button type="button" class="btn btn-success update-action-true">YES</button>
      </div>
    </div>
  </div>
</div>	
<link rel='stylesheet' href='<?php echo get_template_directory_uri(); ?>/hcfunction/bootstrap/css/bootstrap.min.css?ver=6.0.2' type='text/css' media='all' />
<link rel='stylesheet' href='<?php echo get_template_directory_uri(); ?>/hcfunction/assets/ui/jquery-ui.css?ver=1.0.4' type='text/css' media='all' />
<link rel='stylesheet' href='<?php echo get_template_directory_uri(); ?>/hcfunction/assets/update-modifile.css?ver=1.2.3' type='text/css' media='all' />
<script src="<?php echo get_template_directory_uri(); ?>/hcfunction/assets/jquery-3.5.0.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/hcfunction/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/hcfunction/assets/ui/jquery-ui.js"></script>
<script>
jQuery(function($) {
	$( "#dateFrom" ).datepicker({
      changeMonth: true,
      changeYear: true,
      dateFormat: 'dd-mm-yy'
    });
    $( "#dateTo" ).datepicker({
      changeMonth: true,
      changeYear: true,
      dateFormat: 'dd-mm-yy'
    });
    $('.form-cpt a').on('click',function() {
    	var val = $(this).attr('attr-cpt');
    	$('#fillCPT').val(val);
    	$('#actionfilterForm').submit();
    	return false;
    })
    $('.update-action-true').on('click',function() {
    	$('#fillAction').val('<?php echo $userid; ?>');
    	$('#actionfilterForm').submit();
    	return false;
    })
    $('#checkboxAll').on('change',function() {
    		if($(this).is( ":checked") == true) {
    			$('.checkbox-child').attr('checked','checked');
    		} else {
    			$('.checkbox-child').attr('checked',false);
    		}
    });
    $('.page-numbers').on('click',function() {
    	var vl = $(this).text();
    	$('#fillPaged').attr('value',vl).val(vl);
    	$('#actionfilterForm').submit();
    	return false;
    });
});
</script>
<?php if(isset($_POST['update_action']) && $_POST['update_action'] == $userid) { ?>
<script defer>
	alert('Update completed');
	//location.reload();
</script>
<?php }
}
add_action( 'admin_menu', 'hc_register_update_modifie_date' );
?>