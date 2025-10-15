<?php get_template_part('template-parts/section', 'archive-hero'); ?>

<?php $class = (!have_posts()) ? "no-results-section" : ''; ?>
<section class="inner-container blog-container sec-padded <?= $class; ?>">
	<?php if (! have_posts()) { ?>
		<div class="text-center">
			<?php get_template_part('template-parts/section', '404'); ?>
		</div>
	<?php } else { ?>
		<div class="container">
			<div style="max-width: 500px; margin: 0 auto 24px;">
				<?php get_search_form(); ?>
			</div>

			<section class="th-masonry-blog">
				<div class="mas-blog row">
					<?php
					while (have_posts()) {
						the_post();
					?>
						<div <?= post_class('mas-blog-post'); ?>>
							<div class="mas-blog-post-inner">
								<?php get_template_part('templates/content'); ?>
							</div>
						</div>
					<?php } ?>
				</div>

				<?= pagination_bar() ?>
				<?php wp_reset_postdata(); ?>
			</section>
		</div>
	<?php } ?>
</section>