<section class="inner-container blog-container">
	<div class="container">
		<?php
		if (is_archive()) $class = "th-masonry-blog hr-archives";
		elseif (is_search()) $class = "th-masonry-blog hr-search";
		else $class = "th-masonry-blog"; 
		?>
		<div class="row">
			<div class="main col-sm-12" role="main">
				<section class="<?= $class; ?>">				 
					<div class="mas-blog row">
						<?php while (have_posts()) { the_post(); ?>
						<div <?= post_class('mas-blog-post'); ?>>
							<div class="mas-blog-post-inner">
								<?php get_template_part('templates/content'); ?>
							</div>
						</div>
						<?php } ?>
					</div>
					<?php if ($wp_query->max_num_pages > 1) : ?>
					<nav class="post-nav">
						<?= pagination_bar($wp_query)?>
					</nav>
					<?php endif; ?>
				</section>
			</div><!-- /.main -->
		</div><!-- /.row -->
	</div><!-- /.container -->
</section><!-- /.inner-container -->