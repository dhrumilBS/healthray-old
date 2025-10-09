<?php
$pt = get_post_type();
if (is_post_type_archive()) {
	$pt = get_queried_object()->name;
}
?>
<form role="search" method="get" class="search-form form-inline" action="<?php echo esc_url(home_url('/')); ?>">
	<!--<input type="hidden" value="<?= $pt; ?>" name="post_type">-->
	<div class="input-group">

		<input type="search" value="<?php if (is_search()) {
										echo get_search_query();
									} ?>" name="s" class="search-field" placeholder="<?php esc_html_e('Search', 'stratus'); ?> <?php bloginfo('name'); ?>">
		<button class="btn-submit"> <i class="fas fa-search"></i> </button>
	</div>
</form>