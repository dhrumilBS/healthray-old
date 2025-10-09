<?php global $post; ?>
<section class="blog-hero hero-section">
	<div class="container">
		<div class="heading">
			<?php
			if (is_author()) {
				$author = get_queried_object();
			?>
				<div class="profile-picture"> <?= get_avatar(get_the_author_meta('ID'), '250'); ?> </div>
				<h1><?= $author->display_name . '\'s Blogs'; ?></h1>
				<p class="description"><?= get_the_author_description(); ?></p>
			<?php } else { ?>
				<h1 class='entry-title header-default azsfdc'><?= wp_kses_post(roots_title()); ?> </h1>
			<?php } ?>
		</div>
		<?php
		$categories = get_categories();
		if ($categories) {
		?>
			<div class="main-sidebar">
				<?php get_template_part('templates/category-list'); ?>
			</div>
		<?php } ?>
	</div>
</section>

<?php $class = (!have_posts()) ? "no-results-section" : ''; ?>

<section class="blog-container sec-padded <?= $class; ?>">
	<?php if (! have_posts()) { ?>
		<div class="text-center">
			<?php get_template_part('template-parts/section', '404'); ?>
		</div>
	<?php } else { ?>
		<div class="container">
			<div style="max-width: 500px; margin: 0 auto 24px;">
				<?php get_search_form(); ?>
			</div>
			<?php
			$class = is_archive() ? "th-masonry-blog hr-archives"
				: (is_search() ? "th-masonry-blog hr-search"
					: "th-masonry-blog");
			?>

			<div class="<?= $class; ?>">
				<div class="mas-blog row">
					<?php while (have_posts()) {
						the_post();  ?>
						<div <?= post_class('mas-blog-post'); ?>>
							<div class="mas-blog-post-inner">
								<?php get_template_part('templates/content'); ?>
							</div>
						</div>
					<?php } ?>
				</div>
				<?php if ($wp_query->max_num_pages > 1) : ?>
					<nav class="post-nav">
						<?= pagination_bar($wp_query) ?>
					</nav>
				<?php endif; ?>
			</div>
		</div>
	<?php } ?>
</section>