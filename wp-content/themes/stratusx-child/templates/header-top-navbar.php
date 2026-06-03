<style>
    /* HEADER / NAV */
    .header-inner {
        position: sticky;
        top: 0;
        z-index: 200;
        background: #fff;
        border-bottom: 1px solid #e8ecf4;
        box-shadow: 0 2px 20px rgba(25, 49, 106, 0.08);
        height: 90px;
        display: flex;
    }

    .header-inner .container {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .logo img {
        height: 50px;
        width: auto;
        display: block;
    }

    .menu-btn-contact {
        background: var(--cta-orange) !important;
        color: var(--white) !important;
        border-radius: 80px !important;
        padding: 12px 28px !important;
        font-weight: 600 !important;
        transition: background 0.2s, transform 0.15s !important;
        white-space: nowrap;
    }

    .menu-btn-contact:hover {
        background: var(--cta-orange-hover) !important;
        transform: translateY(-1px);
    }

 
</style>
<header class="header-inner">
    <div class="container">
        <div class="logo">
            <?php the_custom_logo(); ?>
        </div>
        <div class="header-nav">
            <button class="mobile-menu-toggle">
                <i class="icon-menu"></i>
            </button>
            <?php
            if (has_nav_menu('primary_navigation')):

                wp_nav_menu([
                    'theme_location' => 'primary_navigation',
                    'container' => false,
                    'menu_class' => 'main-menu',
                ]);

            endif;
            ?>
        </div>
    </div>
</header>
