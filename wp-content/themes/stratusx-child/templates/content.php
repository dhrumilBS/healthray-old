<?php
$str = '';
foreach (wp_get_post_terms(get_the_ID(), 'category') as $term) {
	$temp['title'] =  $term->name;
	$temp['url'] = get_term_link($term);
	$termsList[] = $temp;
}
if ($termsList > 0) {
	foreach ($termsList as $index => $term) {
		$str .= '<a href="' . $term['url'] . '">' . $term['title'] . (isset($termsList[$index + 1]) ? ', ' : '') . '</a>';
	}
}
$article = get_post_field('post_content', $post->ID);
$wordcount = str_word_count(strip_tags($article));
$time = ceil($wordcount / 250);

if ($time == 1) {
	$label = " Min";
} else {
	$label = " Mins";
}

$totalString = $time . $label;
?>
<div class="post-feature-img">
	<span class="show-category"><?= $str ?> </span>
	<a href="<?php esc_url(the_permalink()); ?>" class="img-wrap">
		<?php the_post_thumbnail('full', ['class' => "img-responsive"]); ?>
	</a>
</div>

<div class="post-inner">
	<a href="<?php esc_url(the_permalink()); ?>">
		<h2 class="post-title"><?= wp_kses_post(get_the_title('', '', false)); ?></h2>
	</a>

	<div class="post-meta">
		<span class="show-author"> Written by <?php the_author_posts_link(); ?></span>
		<span class="date"> | <?= get_the_date(); ?></span>
		<span class="time"> | <?= $totalString; ?></span>
	</div>

	<?php
	$excerpt = apply_filters('the_excerpt', get_the_excerpt());
	$excerpt = str_replace(']]>', ']]&gt;', $excerpt);
	if ($excerpt != "") { ?>
		<div class="entry-content post-excerpt">
			<?php echo wp_kses_post($excerpt); ?>
		</div>
	<?php } ?>
</div>