<!DOCTYPE html> 
<html class="no-js" <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="alternate" type="application/rss+xml" title="<?php echo sanitize_text_field(get_bloginfo('name')); ?> Feed" href="<?php echo esc_url(home_url('/')); ?>/feed/">
		<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
		
		<?php 
		if (has_post_thumbnail()) {
			$thumb_id = get_post_thumbnail_id( get_the_ID() );
			$featured_img_src = wp_get_attachment_image_src( $thumb_id, 'large' )[0];
			$featured_img_srcset = wp_get_attachment_image_srcset( $thumb_id );
			printf('<link rel="preload" as="image" importance="high" href="%s" srcset="%s" />', $featured_img_src, $featured_img_srcset );
		} 
		?>
		<link rel="preload" href="<?= site_url(); ?>/wp-content/themes/stratusx-child/fonts/nunito/XRXV3I6Li01BKofINeaB.woff2" as="font" type="font/woff2" crossorigin>
		<!-- Google tag (gtag.js) -->
		<script defer src="https://www.googletagmanager.com/gtag/js?id=GT-P8VLNQ"></script>
		<script defer src="https://www.googletagmanager.com/gtag/js?id=AW-10935864100"></script>
		<script> window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments); } gtag('js', new Date()); gtag('config', 'GT-P8VLNQ'); gtag('config', 'AW-10935864100'); gtag('event', 'conversion', {'send_to': 'AW-10935864100/Yc8NCInZxdYDEKSW0N4o'}); </script>
		<!-- Review Schema -->
		<script type="application/ld+json">
		{
			"@context": "https://schema.org/",
			"@type": "CreativeWorkSeries",
			"name": "A Smart Healthcare Solution, For A Smart Digital Clinics.",
			"aggregateRating": {
				"@type": "AggregateRating",
				"ratingValue": "4.7",
				"bestRating": "5",
				"ratingCount": "23"
			}}
		</script>
		<?php wp_head(); ?>
	</head>