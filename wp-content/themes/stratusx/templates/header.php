<header class="banner container" role="banner">
	<div class="row">
		<div class="col-lg-12">
			<a class="brand" href="<?php echo esc_url(home_url('/')); ?>"><?php sanitize_text_field(bloginfo('name')); ?></a>
			<nav class="nav-main" role="navigation">
				<?php
				if (has_nav_menu('primary_navigation')) :
				wp_nav_menu(array('theme_location' => 'primary_navigation', 'menu_class' => 'nav nav-pills'));
				endif;
				?>
			</nav>
		</div>
	</div>
</header>