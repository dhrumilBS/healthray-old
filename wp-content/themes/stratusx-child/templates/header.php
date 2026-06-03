<header class="banner container" role="banner">
    <div class="row">
        <div class="col-lg-12">
            <?php
            if (has_nav_menu('primary_navigation')):
                wp_nav_menu(array('theme_location' => 'primary_navigation', 'menu_class' => 'nav nav-pills'));
            endif;
            ?>
        </div>
    </div>
</header>