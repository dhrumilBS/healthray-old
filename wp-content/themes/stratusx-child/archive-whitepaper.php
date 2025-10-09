<section class="sec-padded hero-section blog-hero">
	<div class="container">
		<div class="heading text-center">
			<h1> <span>Whitepaper Library</span> </h1>
			<p style="margin-bottom: 0;">Explore our collection of in-depth industry research and insights</p>
		</div>
	</div>
</section>

<?php $class = (!have_posts()) ? "no-results-section" : ''; ?>
<section class="whitepapers-section sec-padded <?= $class; ?>">
	<?php if (! have_posts()) { ?>
		<div class="text-center">
			<?php get_template_part('template-parts/section', '404'); ?>
		</div>
	<?php } else { ?>
		<div class="container">
			<div style="max-width: 500px; margin: 0 auto 24px;">
				<?php get_search_form(); ?>
			</div>

			<div class="whitepapers-grid">
				<?php
				while (have_posts()) {
					the_post();
					$post_id = get_the_ID();
					$title   = get_the_title($post_id);
					$excerpt = get_the_excerpt($post_id);

					if (empty($excerpt)) {
						$excerpt = wp_trim_words(get_the_content(null, false, $post_id), 20);
					}

					$term_name = '';
					$tax_list = ['whitepaper_topic', 'whitepaper_category' ];

					foreach ($tax_list as $tax) {
						$terms = get_the_terms($post_id, $tax);
						if (! empty($terms) && ! is_wp_error($terms)) {
							$term_name = esc_html($terms[0]->name);
							break;
						}
					}

					$thumb = get_the_post_thumbnail($post_id, 'medium');
					$href = get_permalink($post_id);

					$term_out = '';
					if ($term_name) {
						$term_out = '<span class="whitepaper-tag">' . $term_name . '</span>';
					}
				?>
					<div class="whitepaper-card">
						<div class="whitepaper-image" aria-hidden="true">
							<?= $thumb; ?>
							<?= $term_out; ?>
						</div>
						<a href="<?= $href; ?>">
							<h3 class="whitepaper-title"><?= $title; ?></h3>
						</a>
						<p class="whitepaper-desc"><?= $excerpt; ?></p>
						<a href="<?= $href; ?>" class="btn btn-primary" > View Whitepaper </a>
					</div>
				<?php } ?>
				<?php wp_reset_postdata(); ?>
			</div>
		</div>
	<?php } ?>
</section>