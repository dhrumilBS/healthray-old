<section class="blog-container blog-hero hero">
	<div class="container">
		<style>
			.d-flex {
				display: flex;
				flex-wrap: wrap;
				justify-content: space-between;
				align-items: center;
			}

			.d-flex .entry-title {
				font-size: 36px;
			}

			.breadcrumb {
				font-size: 16px;
			}

			.breadcrumb a {
				color: var(--hr-primary-color);
				padding: 2px 12px;
				font-size: 15px;
				border-radius: 20px;
				transition: all 200ms ease-in-out;
			}

			.breadcrumb a:hover {
				background-color: var(--hr-primary-color);
				color: #fff;

				text-decoration: underline;
			}

			.breadcrumb-item {
				display: inline-block;
				margin-right: 5px;
			}

			.breadcrumb-item.active {
				font-weight: bold;
				color: #333;
			}
		</style>

		<div class="d-flex">
			<div class="col">
				<h1 class='entry-title header-default azsfdc'><?= wp_kses_post(roots_title()); ?> </h1>
			</div>
			<div class="col">
				<nav class="breadcrumb">
					<a class="breadcrumb-item" href="<?php echo home_url(); ?>">Home</a><span> » </span>
					<?php
					$category = get_queried_object();
					if ($category->parent != 0) {
						$parent_categories = get_category_parents($category->parent, true, ' » ');
						echo '<span class="breadcrumb-item">' . $parent_categories . '</span>';
					}
					?>
					<span class="breadcrumb-item active" aria-current="page"><?php single_cat_title(); ?></span>
				</nav>
			</div>
		</div>

	</div>
</section>
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