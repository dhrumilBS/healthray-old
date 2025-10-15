<?php
// Post data
$author_id = get_the_author_meta('ID');
$author_name = get_the_author();
$author_avatar = get_avatar($author_id, 40);
$author_link = get_author_posts_url($author_id);
$post_date = get_the_date('F j, Y');
$categories = get_the_category();
$category_list = '';
if (! empty($categories)) {
	foreach ($categories as $category) {
		$category_list .= '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>, ';
	}
	$category_list = rtrim($category_list, ', ');
}
$words = str_word_count(strip_tags(get_the_content()));
$reading_time = ceil($words / 200);
$featured_image = get_the_post_thumbnail_url(get_the_ID(), 'medium');
?>

<section class="post-hero sec-padded">
	<div class="container">
		<div class="row align-items-center">
			<div class="hero-text col-12 col-md-8">
				<?php if ($categories)  ?>
				<div class="post-categories"><?= $category_list; ?></div>
				<h1 class="post-title"><?php the_title(); ?></h1>
				<div class="post-meta">
					<a href="<?= esc_url($author_link); ?>" class="author-info">
						<?= $author_avatar; ?>
						<span class="author-name"><?= esc_html($author_name); ?></span>
					</a>
					<span class="post-date"><?= esc_html($post_date); ?></span>
					<span class="separator">|</span>
					<span class="reading-time"><?= $reading_time; ?> min read</span>
				</div>
			</div>
			<?php if ($featured_image): ?>
				<div class="hero-image col-12 col-md-4">
					<img src="<?= esc_url($featured_image); ?>" alt="<?php the_title(); ?>">
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<style>
	.post-categories a { color: #1b2374; font-weight: 600; font-size: 13px; margin-right: 6px; }
	.post-categories a:hover { color: #152ce1; }
	.post-title { font-size: 32px; font-weight: 700; margin: 10px 0 15px; line-height: 1.2; color: #1b2374; }
	.post-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }
	.post-meta .author-info { display: flex; align-items: center; color: #1b2374; transition: color 0.3s; }
	.post-meta .author-info img { border-radius: 50%; margin-right: 6px; border: 2px solid #152ce1; }
	.post-meta .author-info:hover { color: #152ce1; }
	.post-meta .author-name { font-weight: bold; }
	.post-meta .separator { margin: 0 5px; color: #aaa; }
	.post-meta .reading-time { font-style: italic; }
	.hero-image { border-radius: 8px; overflow: hidden; }
	.hero-image img { width: 100%; height: auto; display: block; border-radius: 8px; }
	@media(max-width:768px) {
		.hero-wrapper { flex-direction: column; gap: 15px; }
		.hero-image { width: 100%; flex: auto; }
		.post-title { font-size: 26px; }
	}
</style>

<?php
$author_id = get_the_author_meta('ID');
$author_name = get_the_author();
$author_avatar = get_avatar($author_id, 64);
$author_link = get_author_posts_url($author_id);
$post_date = get_the_date('F j, Y');
$categories = get_the_category();
$category_list = '';
if (! empty($categories)) {
	foreach ($categories as $category) {
		$category_list .= '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>, ';
	}
	$category_list = rtrim($category_list, ', ');
}

$words = str_word_count(strip_tags(get_the_content()));
$reading_time = ceil($words / 200); // assuming 200 wpm average


?>
<div class="post-content-wrap">
	<div class="container">
		<div class="row">
			<div class="col-12 col-md-3">
				<?= do_shortcode('[toc]'); ?>
			</div>
			<div class="col-12 col-md-9">
				<?= the_content(); ?>
			</div>

		</div>
	</div>
</div>