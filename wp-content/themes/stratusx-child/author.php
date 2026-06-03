<?php
// Get the current author object
$author = get_queried_object();
$author_id = $author->ID;
$avatar = get_avatar_url($author_id, ['size' => 250]);

// Author description
$description = get_user_meta($author_id, 'user_description', true);

// Social links
$linkedin = get_the_author_meta('linkedin', $author_id);
$youtube  = get_the_author_meta('youtube', $author_id);
?>

<div class="author-template">
	<!-- Author Hero Section -->
	<section class="author-hero hero">
		<div class="container-md">
			<div class="author-section">
				<div class="author">

					<!-- Profile Image -->
					<div class="author-profile">
						<img src="<?= esc_url($avatar); ?>" width="250" height="250" alt="<?= esc_attr($author->display_name); ?>" decoding="async" fetchpriority="high" />
					</div>

					<!-- Author Info -->
					<div class="about-author">
						<h1 class="author-name"><?= esc_html($author->display_name); ?></h1>
						<p class="author-position"><?= esc_html(get_the_author_meta('user_position', $author_id)); ?></p>

						<!-- Social Media -->
						<?php if ($linkedin || $youtube): ?>
						<div class="author-follow">
							<ul class="social-media-list">
								<?php if ($linkedin): ?>
								<li>
									<a href="<?= esc_url($linkedin); ?>" target="_blank" rel="nofollow noopener" aria-label="LinkedIn profile of <?= esc_attr($author->display_name); ?>">
										<svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
											<path d="M6.93994 5.00002C6.93968 5.53046 6.72871 6.03906 6.35345 6.41394C5.97819 6.78883 5.46937 6.99929 4.93894 6.99902C4.40851 6.99876 3.89991 6.78779 3.52502 6.41253C3.15014 6.03727 2.93968 5.52846 2.93994 4.99802C2.94021 4.46759 3.15117 3.95899 3.52644 3.5841C3.9017 3.20922 4.41051 2.99876 4.94094 2.99902C5.47137 2.99929 5.97998 3.21026 6.35486 3.58552C6.72975 3.96078 6.94021 4.46959 6.93994 5.00002ZM6.99994 8.48002H2.99994V21H6.99994V8.48002ZM13.3199 8.48002H9.33994V21H13.2799V14.43C13.2799 10.77 18.0499 10.43 18.0499 14.43V21H21.9999V13.07C21.9999 6.90002 14.9399 7.13002 13.2799 10.16L13.3199 8.48002Z" fill="currentcolor"></path>
										</svg>
									</a>
								</li>
								<?php endif; ?>
								<?php if ($youtube): ?>
								<li>
									<a href="<?= esc_url($youtube); ?>" target="_blank" rel="nofollow noopener" aria-label="YouTube channel of <?= esc_attr($author->display_name); ?>">
										<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" width="24" height="24" fill="currentcolor">
											<path d="M 44.898438 14.5 C 44.5 12.300781 42.601563 10.699219 40.398438 10.199219 C 37.101563 9.5 31 9 24.398438 9 C 17.800781 9 11.601563 9.5 8.300781 10.199219 C 6.101563 10.699219 4.199219 12.199219 3.800781 14.5 C 3.398438 17 3 20.5 3 25 C 3 29.5 3.398438 33 3.898438 35.5 C 4.300781 37.699219 6.199219 39.300781 8.398438 39.800781 C 11.898438 40.5 17.898438 41 24.5 41 C 31.101563 41 37.101563 40.5 40.601563 39.800781 C 42.800781 39.300781 44.699219 37.800781 45.101563 35.5 C 45.5 33 46 29.398438 46.101563 25 C 45.898438 20.5 45.398438 17 44.898438 14.5 Z M 19 32 L 19 18 L 31.199219 25 Z" />
										</svg>
									</a>
								</li>
								<?php endif; ?>
							</ul>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Author Description -->
	<?php if ($description): ?>
	<section class="author-content">
		<div class="container-md">
			<div class="content">
				<?= wp_kses_post($description); ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<!-- Recent Posts -->
	<section class="inner-container blog-container">
		<div class="container">
			<?php
			$args = [
				'posts_per_page' => 3,
				'author' => $author_id,
			];
			$query = new WP_Query($args);
			?>
			<?php if ($query->have_posts()): ?>
			<section class="th-masonry-blog hr-archives">
				<div class="heading sec-heading centered">
					<h2 class="elementor-heading-title elementor-size-default">
						<?= esc_html($author->display_name); ?>'s Recent Articles
					</h2>
				</div>
				<div class="mas-blog row" style="--gap: 1px">
					<?php while ($query->have_posts()): $query->the_post(); ?>
					<div <?= post_class('mas-blog-post col-sm-6 col-md-4'); ?>>
						<div class="mas-blog-post-inner">
							<?php get_template_part('templates/content'); ?>
						</div>
					</div>
					<?php endwhile; ?>
				</div>
			</section>
			<?php endif;
			wp_reset_postdata(); ?>
		</div>
	</section>
</div>