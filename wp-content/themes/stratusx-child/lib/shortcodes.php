<?php

add_shortcode('healthray_megamenu_notice', 'healthray_render_megamenu_notice');

function healthray_render_megamenu_notice($atts)
{
	// Default attributes
	$atts = shortcode_atts(array(
		'text'      => 'Have a look on our one-stop digital healthcare solution',
		'link'      => 'https://calendly.com/healthray/30min',
		'link_text' => 'Book A Demo',
	), $atts, 'healthray_megamenu_notice');

	ob_start(); ?>
	<div class="mega-menu-notice">
		<span><?php echo esc_html($atts['text']); ?></span>
		<a href="<?php echo esc_url($atts['link']); ?>">
			<?php echo esc_html($atts['link_text']); ?>
		</a>
		<a href="https://healthray.com/pdf/Healthray.pdf" target="_blank">
			Brochure
		</a>
	</div>

<?php
	return ob_get_clean();
}


// =----------------------------------------------------------------------------= //

function healthray_cta_shortcode($atts)
{
	// Default attributes
	$atts = shortcode_atts(array(
		'title'    => 'Boost Your Healthcare Practice',
		'text' => 'Discover how Healthray can streamline your workflow and improve patient care.',
		'button_text' => 'Book a Free Demo Today!',
	), $atts, 'healthray_cta');

	ob_start(); ?>

	<div class="cta-box">
		<h3 class="cta-title"><?= esc_html($atts['title']); ?> </h3>
		<p class="cta-subtext"><?= esc_html($atts['text']); ?> </p>
		<a href="https://calendly.com/healthray/30min" class="cta-btn">
			<?= esc_html($atts['button_text']); ?>
		</a>
	</div>

<?php
	return ob_get_clean();
}
add_shortcode('healthray_cta', 'healthray_cta_shortcode');