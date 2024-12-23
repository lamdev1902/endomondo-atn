<div class="form-customer-feedback">
<a href="#" class="customer-ftoggle"><?php echo __('Feedback','hc_theme'); ?></a>
    <div class="customer-feedback">
        <div class="close-btn">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/close-gray.svg" alt="">
        </div>
        <div class="star-rating mr-bottom-20">
            <p class="has-large-font-size"><?php echo __('Help us rate this article','hc_theme'); ?></p>
            <input type="hidden" class="rating-feedback" data-empty="fa-regular fa-star" data-filled="fas fa-star" data-fractions="1" />
            <input class="link-post-feedback" value="<?php echo get_permalink(); ?>" type="hidden">
            <input class="ip-address" value="<?php
                $ip = $_SERVER['REMOTE_ADDR'];
                echo $ip;
                ?>" type="hidden">
        </div>
        <div class="form-feedback">
            <p class="sec-color-4"><?php echo __('Give us feedback in details','hc_theme'); ?></p>
            <?php echo do_shortcode('[contact-form-7 id="9b83ddb" title="Customer review"]'); ?>
        </div>
        <div class="mailsent">
            <p class="has-large-font-size sec-color-4"><?php echo __('Thank you for your feedback','hc_theme'); ?></p>
            <p class="sec-color-4"><?php echo __('Keep in touch to see our improvement','hc_theme'); ?></p>
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/success-icon.svg" alt="">
        </div>
    </div>
</div>