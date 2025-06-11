<link href="<?= site_url(); ?>/wp-content/uploads/assets/glightbox/css/glightbox.min.css" rel="stylesheet">
<link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.4.2/uicons-regular-rounded/css/uicons-regular-rounded.css'>
<style>
	.flex { display: flex; flex-wrap: wrap; }
	.flex .col { width: calc(100% / 5); }
	.flex .flex-image { border: 1px solid #dcdcdc; width: 120px; text-align: center; margin: 2px; }
	.flex .flex-image img { object-fit: contain; height: 100px }
	.container { padding: 50px 0; max-width: 1440px !important; }
	.state-city { margin-bottom: 50px }
	.state-city span { font-weight: 600; }
	.list-style { list-style-type: none; padding: 0; } </style>

<div class="flex">
	<?php
	$paged = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;
	$as = array(
		'post_type'      => 'attachment',
		'posts_per_page' => 75,
		'post_mime_type' => 'image',
		'paged' => $paged
	);
	$attachments = get_posts($as);
	$os = ["image/webp", "image/svg+xml"];
	foreach ($attachments as $attachment) {
		$png = in_array($attachment->post_mime_type, $os) ? '#456aad' : 'green';
	?>
		<div href="<?= get_permalink($attachment->ID); ?>" class="flex-image <?= $attachment->ID; ?> " style='border:1px solid <?= $png ?>'>
			<?= wp_get_attachment_image(
				$attachment->ID,
				['100', 'auto'],
				"",
				["class" => "img-responsive wp-image-$attachment->ID"]
			);  ?>
			<?php // "<p> ".get_post_meta($attachment->ID, '_wp_attachment_image_alt', true) . "</p>"; 
			?>
			<?= $attachment->ID; ?>
		</div>
	<?php }	?>
</div>



<?php
$paged = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;
$args = array(
	'post_type'      => 'attachment',
	'posts_per_page' => 2,
	'post_mime_type' => 'image',
	'post_status'    => 'inherit',
	'paged'          => $paged,
);
$attachments = get_posts($args);
?>

<div class="container">
	<h1> Update Image ALT Text </h1>
	<div style='display: flex; flex-wrap: wrap; gap: 10px'>
		<?php foreach ($attachments as $attachment) {
			if ($attachment->ID == 59213)
				print_r($attachment);

			$alt_text = get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);
			if (empty($alt_text)) {
				if (strpos($attachment->post_mime_type, 'image/') !== false) {
					echo "<div>";
					echo wp_get_attachment_image($attachment->ID, [150, 150]);
					echo '<p><em>';
					echo  $alt_text = ucwords(str_replace(['-', '_'], ' ', $attachment->post_title));
					echo '</em></p>';
					echo "</div>";
					// update_post_meta($attachment->ID, '_wp_attachment_image_alt', $alt_text);
				}
			}
		} 
		$total_pages = $attachments->max_num_pages;

    if ($total_pages > 1){

        $current_page = max(1, get_query_var('paged'));
	echo "<div>";
        echo paginate_links(array(
            'base' => get_pagenum_link(1) . '%_%',
            'format' => '/page/%#%',
            'current' => $current_page,
            'total' => $total_pages,
            'prev_text'    => __('« prev'),
            'next_text'    => __('next »'),
        ));	echo "</div>";
    }
    
    ?>

	</div>
</div>


<script src="https://cdn.jsdelivr.net/npm/animejs@3.1.0/lib/anime.min.js"></script>
<script src="https://healthray.com/wp-content/uploads/assets/glightbox/js/glightbox.min.js"></script>
<script>
	const portfolioLightbox = GLightbox({
		selector: '.image',
		openEffect: 'zoom',
		closeEffect: 'fade',
		loop: true,
		touchNavigation: true
	});
</script>