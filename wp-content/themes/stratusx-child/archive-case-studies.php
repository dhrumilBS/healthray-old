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
			<?php while (have_posts()) {
				the_post(); ?>
				<article <?php post_class('case-study-card border border-opacity-25 border-primary bg-white position-relative'); ?>>
					<div class="row g-0">
						<div class="col-md-6">
							<div class="case-study-image overflow-hidden">
								<?php
								if (has_post_thumbnail()) {
									echo get_the_post_thumbnail(get_the_ID(), 'large', [
										'class' => 'img-fluid w-100 h-100 object-fit-cover',
										'alt'   => get_the_title(),
									]);
								} else {
									// Fallback image (ID = 62392)
									echo wp_get_attachment_image(62392, 'large', false, [
										'class' => 'img-fluid w-100 h-100 object-fit-cover',
										'alt'   => 'Default case study image',
									]);
								}
								?>

							</div>
						</div>

						<!-- Content Section -->
						<div class="col-md-6">
							<div class="case-study-content p-3 p-md-4">
								<div class="case-study-category d-inline-block rounded-pill text-sm mb-3 bg-primary bg-opacity-10 px-3 py-1">Orthopedics</div>
								<h2 class="case-study-title fw-bold mb-3"> <?= get_the_title(); ?> </h2>
								<div class="case-study-description text-lg mb-3 lead"> <?= get_the_content(); ?> </div>

								<ul class="case-study-metrics list-unstyled d-flex flex-column gap-2 ps-3">
									<?php while (have_rows('metrics', get_the_ID())) {
										the_row(); ?>
										<li class="metric-item fw-medium"><?= get_sub_field('title'); ?> <?= get_sub_field('text'); ?> </li>
									<?php } ?>
								</ul>


								<div class="case-study-link mt-4">
									<a href="<?= get_permalink(); ?>" class="read-more text-decoration-none fw-semibold d-inline-flex align-items-center">
										Read case study
										<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ms-2">
											<path d="M5 12h14"></path>
											<path d="m12 5 7 7-7 7"></path>
										</svg>
									</a>
								</div>
							</div>
						</div>
					</div>
				</article>

			<?php } ?>
			<?= pagination_bar() ?>
			<?php wp_reset_postdata(); ?>
		</div>
	<?php } ?>
</section>