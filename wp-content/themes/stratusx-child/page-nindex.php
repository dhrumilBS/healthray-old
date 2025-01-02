<link href="<?= site_url(); ?>/wp-content/uploads/assets/glightbox/css/glightbox.min.css" rel="stylesheet">
<link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.4.2/uicons-regular-rounded/css/uicons-regular-rounded.css'>
<style>
	.flex{ display: flex; flex-wrap:wrap; }
	.flex .col { width:calc(100% / 5);  }
	.flex .flex-image { border: 1px solid #dcdcdc; width:120px; text-align: center;}
	.flex .flex-image img{ object-fit: contain; height: 100px}


	.container { padding: 50px 0; max-width: 1440px !important; }

	.state-city{  margin-bottom: 50px }
	.state-city span{font-weight: 600;  }

	.list-style{ list-style-type: none; padding:0;}
</style>
 
<?php if(1 == 1){  ?>
<div class="flex">
	<?php
	$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
	$as = array(
		'post_type'      => 'attachment',
		'posts_per_page'         => -1,
		'post_mime_type' => 'image', 
		'paged' => $paged
	);
	$attachments = get_posts( $as ); 
	$os = array("image/webp", "image/svg+xml");

	if ( $attachments )
	{
		foreach ( $attachments as $attachment ) 
		{ 
			if (in_array($attachment->post_mime_type, $os)) {
				$png ='#456aad';
			}else{
				$png ='green';
			}
			$size = filesize( get_attached_file( $attachment->ID ) );
			$size = number_format($size / 1024, 2);

	?>
	<div href="<?= get_permalink($attachment->ID); ?>" class="flex-image" style='margin:2px;border:1px solid <?= $png ?>'>		
		<?= wp_get_attachment_image( $attachment->ID, array('100', 'auto'), "", array( "class" => "img-responsive" ) );  ?>
		<?= "<p> ".get_post_meta($attachment->ID, '_wp_attachment_image_alt', true) . "</p>"; ?>
	</div> 

	<?php

		}
	} else {
		echo 'No image attachments found.';
	}
	?>
</div>

<?php } ?> 
<script src="https://cdn.jsdelivr.net/npm/animejs@3.1.0/lib/anime.min.js"></script>
<script src="<?= site_url(); ?>/wp-content/uploads/assets/glightbox/js/glightbox.min.js"></script>
<script>
	const portfolioLightbox = GLightbox({
		selector: '.image',
		openEffect: 'zoom',
		closeEffect: 'fade',
		loop: true, 
		touchNavigation: true
	});</script>