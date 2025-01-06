<!DOCTYPE html> 
<html class="no-js" <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="alternate" type="application/rss+xml" title="<?php echo sanitize_text_field(get_bloginfo('name')); ?> Feed" href="<?php echo esc_url(home_url('/')); ?>/feed/">
		<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
		<link rel="preload" href="<?= site_url(); ?>/wp-content/themes/stratusx-child/fonts/nunito/XRXV3I6Li01BKofINeaB.woff2" as="font" type="font/woff2" crossorigin>
		<?php wp_head(); ?>
	</head>