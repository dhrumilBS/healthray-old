<div class="categories-list">
	<?php 
	if(is_author()){
		while (have_posts()) { the_post(); foreach ( get_the_category($post->ID) as $key => $value) { $cats[] = $value->term_id; $cats = array_unique($cats); } } ?>
	<ul>
		<?php foreach($cats as $category){$term=get_term($category,'category'); echo '<li class="cat-item"> <a href="'.get_term_link($category).'">'.$term->name.'</a></li>';} ?>
	</ul>

	<?php } else { ?>

	<ul>
		<?php 
		$blog_cat_id = 34;
$cat = [];
$show_cat = 7;
$current_term = get_queried_object();
$current_term_tax_id = is_object($current_term) && isset($current_term->term_taxonomy_id) ? $current_term->term_taxonomy_id : null;

foreach (get_categories(['orderby' => 'count', 'order' => 'DESC']) as $i => $category) {
	$class  = ' catagory-' . $category->term_taxonomy_id;
	$class .= ($current_term_tax_id === $category->term_taxonomy_id) ? ' active' : '';

	if ($category->term_id != $blog_cat_id) {
		if ($i < $show_cat) {
			echo '<li class="cat-item' . $class . '"><a class="cat-link" href="' . get_category_link($category) . '">' . $category->name . '</a></li>';
		} else {
			$cat[] = '<a class="more-cat-item cat-item cat-link' . $class . '" href="' . get_category_link($category) . '">' . $category->name . '</a>';
		}
	}
}

		?>
		<?php if($i > $show_cat ){ ?>
		<li class="more-category cat-item">
			<div class="cat-link blog-category-more-link"><span>More </span>
				<svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" style="width: 14px;"><path fill="currentColor" d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"></path></svg>
			</div>
			<div class="blog-category-more-list" style="display: none; ">
				<ul><?php foreach ($cat as $c) {echo '<li>'. $c.'</li>';} ?></ul>
			</div>
		</li>
		<?php } ?>
	</ul>
	<?php } ?> 
</div>