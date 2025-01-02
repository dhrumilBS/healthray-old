<section class="blog-hero hero">
	<div class="container">
		<div class="page-title">
			<h1> <span>Healthray Blog</span> </h1>		 
		</div>
		<?php
		$categories = get_categories();
		if ($categories) {
		?>
		<div class="main-sidebar">
			<?php get_template_part('templates/category-list'); ?>
		</div>
		<?php } ?>
		<div class="sidebar-form">
			<?php echo get_search_form(); ?>
		</div>

	</div>
</section>
<section class="inner-container blog-container ">
	<div class="container">
		<?php
		$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;

		$args = array(
			'post_type' => array('post'),
			'post_status' => array('publish'),
			'orderby' => 'date',
			'order' => 'DESC',
			'paged' => $paged
		);
		$widget_wp_query = new \WP_Query($args); ?>
		<section class="th-masonry-blog">
			<div class="mas-blog row">
				<?php 
				while ($widget_wp_query->have_posts()) {
					$widget_wp_query->the_post();
				?>
				<div <?= post_class('mas-blog-post'); ?>>
					<div class="mas-blog-post-inner">
						<?php get_template_part('templates/content'); ?>
					</div>
				</div>
				<?php } ?>
			</div>

			<nav class="post-nav">
				<?= pagination_bar($widget_wp_query)?>
			</nav>
			<?php wp_reset_postdata(); ?>
		</section>

	</div><!-- /.container -->
</section><!-- /.inner-container -->