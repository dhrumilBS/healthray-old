<style>
	.sitemap-menu {margin: 30px 0;}
	.sitemap-menu .col {display: flex;flex-direction: column;margin-top: 20px;}

	.sitemap-menu .menu-item {background-color: #d8eeff;margin-top: 24px;box-shadow: 0 0 5px 0 #00000010;border: 1px solid var(--hr-primary-color);}
	.sitemap-menu .menu-item h2 { font-size: 24px; margin: 0; font-weight: 800; padding: 10px; color: #fff; background-color: var(--hr-primary-color);  }

	<?php if(1 == 25){ ?>
	div.sitemap-menu .menu-item { margin-top: 24px; }
	div.sitemap-menu .menu-item h2 { font-size: 24px; margin:0 8px 8px; font-weight: 800; padding-bottom: 4px; border-bottom: 1px solid #00000010; }
	<?php } ?>

	.sitemap-menu .menu-item ul { --column: 1; padding: 8px; margin: 0; display: flex; flex-wrap: wrap; }
	.sitemap-menu .menu-item ul li { width: calc(100% / var(--column)); padding: 0 10px;position: relative;transition: all .2s; list-style: none;}
	.sitemap-menu .menu-item ul li::before {display: block; content: ""; width: 5px; height: 5px; position: absolute;transition: all .2s; background: var(--hr-primary-color); left: 0; top: 11.5px; margin: auto 0;rotate: 45deg}
	.sitemap-menu .menu-item ul li::before  { background: var(--hr-secondary-color); rotate: 0deg}
	.sitemap-menu .menu-item ul li a { padding: 4px 8px; color: #1d1c39; display: inline-block; border-radius: 8px; }
	.sitemap-menu .menu-item ul li a:hover { background-color: #1d1c39; color: #FFF; } 





	@media screen and (min-width: 575px) {
		.sitemap-menu .menu-item ul { --column: 2; }
	}
	@media screen and (min-width: 991px) {
		.sitemap-menu .menu-item ul { --column: 3; }
	}
</style>

<?php
global $wpdb;

function get_hms_international_pages() {

    $cache_key = 'hms_international_pages';
    $state = get_transient($cache_key);

    if ($state !== false) {
        return [
            'success' => true,
            'msg' => 'List Successful (Cached)',
            'data' => $state
        ];
    }

    // Main pages
    $main_pages = get_posts([
        'post_type'      => 'page',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'meta_key'       => '_wp_page_template',
        'meta_value'     => 'templates/template-hms-international.php',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'fields'         => 'ids'
    ]);

    $state = [];

    foreach ($main_pages as $main_id) { 
	$sub_pages = get_posts([
            'post_type'      => 'page',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'fields'         => 'ids',
            'meta_query' => [
				[
					'key' => 'state_name_link',
					'value' => $main_id,
					'compare' => '='
				]
			]
        ]);

        $children = [];

        foreach ($sub_pages as $sub_id) {
            $children[] = [
                'id'          => $sub_id,
                'post_name'   => get_post_field('post_name', $sub_id),
                'post_status' => get_post_status($sub_id),
                'post_title'  => get_the_title($sub_id),
                'menu_order'  => get_post_field('menu_order', $sub_id),
            ];
        }

        $state[] = [
            'id'          => $main_id,
            'post_name'   => get_post_field('post_name', $main_id),
            'post_status' => get_post_status($main_id),
            'post_title'  => get_the_title($main_id),
            'menu_order'  => get_post_field('menu_order', $main_id),
            'textdata'    => $children
        ];
    }

    set_transient($cache_key, $state, HOUR_IN_SECONDS);

    return !empty($state)
        ? ['success'=>true,'msg'=>'List Successful','data'=>$state]
        : ['success'=>false,'msg'=>'No data found'];
}

$result = get_hms_international_pages();


function healthray_menu_pages($template, $extra_links = []) {

    $cache_key = 'healthray_' . md5($template);
    $pages = get_transient($cache_key);
	
    if ($pages === false) {
        $pages = get_posts([
            'post_type'      => 'page',
            'meta_key'       => '_wp_page_template',
            'meta_value'     => $template,
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'post_status'    => 'publish',
            'fields'         => 'ids' // Faster - only fetch IDs
        ]);

        set_transient($cache_key, $pages, '', 3600); // Cache 1 hour
    }

    // Static links
    foreach ($extra_links as $link => $title) {
        echo '<li class="menu-link">
                <a href="' . esc_url($link) . '" target="_blank">' . esc_html($title) . '</a>
              </li>';
    }

    // Dynamic pages
    if (!empty($pages)) {
        foreach ($pages as $page_id) {
            echo '<li class="menu-link">
                    <a href="' . esc_url(get_permalink($page_id)) . '" target="_blank">'
                        . esc_html(get_the_title($page_id)) .
                    '</a>
                  </li>';
        }
    }
}

$menus = [
    [
        'title' => 'Best HIMS Software For India',
        'template' => 'templates/template-city-page.php'
    ],
    [
        'title' => 'Best Hospital Management Software In India',
        'template' => 'templates/template-state-city.php',
        'links' => [
            'https://healthray.com/best-hospital-management-software-india/' =>
            'Best Hospital Management Software In India'
        ]
    ],
    [
        'title' => 'Best Lab Software In India',
        'template' => 'templates/template-lab-state.php',
        'links' => [
            'https://healthray.com/best-lab-software-india/' =>
            'Best Lab Software In India'
        ]
    ],
    [
        'title' => 'Best EMR Software In India',
        'template' => 'templates/template-emr-state.php',
        'links' => [
            'https://healthray.com/best-emr-software-india/' =>
            'Best EMR Software In India'
        ]
    ],
    [
        'title' => 'Best EHR Software In India',
        'template' => 'templates/template-ehr-state.php',
        'links' => [
            'https://healthray.com/best-ehr-software-india/' =>
            'Best EHR Software In India'
        ]
    ],
    [
        'title' => 'Best Pharmacy Management Software In India',
        'template' => 'templates/template-pms-state.php',
        'links' => [
            'https://healthray.com/best-pharmacy-management-software-india/' =>
            'Best Pharmacy Management Software In India'
        ]
    ]
];

?>


<section class="blog-hero hero-section">
	<div class="container">
		<div class="heading">
			<h1 class='entry-title header-default'> Healthray Sitemap</h1>
		</div>
	</div>
</section>

<div class="sitemap-menu">
	<!-- Company -->
	<div class="container">
		<div class="menu-item sub-menu-item">
			<div>
				<h2>Company</h2>
				<ul class="sub-menu">
					<li class="menu-link"><a href="https://healthray.com/" target="_blank"> Home </a></li>
					<li class="menu-link"><a href="https://healthray.com/blogs/" target="_blank"> Blogs </a></li>
					<li class="menu-link"><a href="https://healthray.com/why-healthray/" target="_blank"> Why Healthray </a></li>
					<li class="menu-link"><a href="https://healthray.com/careers/" target="_blank"> Careers </a></li>
					<li class="menu-link"><a href="https://healthray.com/jobs/" target="_blank"> Jobs </a></li>
					<li class="menu-link"><a href="https://healthray.com/contact/" target="_blank"> Contact </a></li>
				</ul>
			</div>

		</div>
	</div>

	<!-- HEalthray Products -->
	<div class="container">
		<div class="menu-item sub-menu-item">
			<div>
				<h2>Healthray Products</h2>
				<ul class="sub-menu">
					<li class="menu-link"><a href="https://healthray.com/hospital-information-management-system/" target="_blank"> Hospital Information Management System </a></li>
					<li class="menu-link"><a href="https://healthray.com/emr-software/" target="_blank"> EMR Software </a></li>
					<li class="menu-link"><a href="https://healthray.com/ehr-software/" target="_blank"> EHR Software </a></li>
					<li class="menu-link"><a href="https://healthray.com/pharmacy-management-system/" target="_blank"> Pharmacy Management System </a></li>
					<li class="menu-link"><a href="https://healthray.com/laboratory-information-management-system/" target="_blank"> Laboratory Information Management System </a></li>
					<li class="menu-link"><a href="https://healthray.com/emr-ehr-software/" target="_blank"> EMR/EHR Software </a></li>
					<li class="menu-link"><a href="https://healthray.com/hospital-management-software/" target="_blank"> Hospital Management Software </a></li>
				</ul>
			</div>
		</div>
	</div>

	<?php foreach ($menus as $menu): ?>
<div class="container">
    <div class="menu-item sub-menu-item">
        <div>
            <h2><?php echo esc_html($menu['title']); ?></h2>
            <ul class="sub-menu">
                <?php
                healthray_menu_pages(
                    $menu['template'],
                    $menu['links'] ?? []
                );
                ?>
            </ul>
        </div>
    </div>
</div>
<?php endforeach; ?>

	<!-- Alternative -->
	<div class="container">
		<div class="menu-item sub-menu-item">
			<div>
				<h2> HIMS Compare With </h2>
				<ul class="sub-menu">
					<li class="menu-link"><a href="https://healthray.com/advancedmd-alternative/" target="_blank"> AdvancedMD Alternative </a></li>
					<li class="menu-link"><a href="https://healthray.com/docon-alternative/" target="_blank"> Docon Alternative </a></li>
					<li class="menu-link"><a href="https://healthray.com/docpulse-alternative/" target="_blank"> Docpulse Alternative </a></li>
					<li class="menu-link"><a href="https://healthray.com/healthplix-alternative/" target="_blank"> Healthplix Alternative </a></li>
					<li class="menu-link"><a href="https://healthray.com/karexpert-alternative/" target="_blank"> Karexpert Alternative </a></li>
					<li class="menu-link"><a href="https://healthray.com/mocdoc-alternative/" target="_blank"> MocDoc Alternative </a></li>
					<li class="menu-link"><a href="https://healthray.com/practo-alternative/" target="_blank"> Practo Alternative </a></li>
					<li class="menu-link"><a href="https://healthray.com/akhil-system-alternative/" target="_blank"> Akhil System Alternative </a></li>
					<li class="menu-link"><a href="https://healthray.com/ezovion-alternative/" target="_blank"> Ezovion Alternative </a></li>
					<li class="menu-link"><a href="https://healthray.com/epic-emr-alternative/" target="_blank"> Epic EMR Alternative </a></li>
				</ul>
			</div>
		</div>
	</div>
	<!-- Lab Alternative -->
	<div class="container">
		<div class="menu-item sub-menu-item">
			<div>
				<h2> LIMS Compare With </h2>
				<ul class="sub-menu">
					<li class="menu-link"><a href="https://healthray.com/crelio-laboratory-alternative/" target="_blank"> Crelio Alternative </a></li>
					<li class="menu-link"><a href="https://healthray.com/labsmart-alternative/" target="_blank"> LabSmart Alternative </a></li>
					<li class="menu-link"><a href="https://healthray.com/labguru-alternative/" target="_blank"> Labguru Alternative </a></li>
					<li class="menu-link"><a href="https://healthray.com/elabassist-alternative/" target="_blank"> eLabAssist Alternative </a></li>
					<li class="menu-link"><a href="https://healthray.com/flabs-alternative/" target="_blank"> Flabs Alternative </a></li>
				</ul>
			</div>
		</div>
	</div>
	<!-- Pharmacy Alternative -->
	<div class="container">
		<div class="menu-item sub-menu-item">
			<div>
				<h2> Pharmacy Management Compare With </h2>
				<ul class="sub-menu">
					<li class="menu-link"><a href="https://healthray.com/marg-erp-9-alternative/" target="_blank"> Marg ERP 9 Alternative </a></li>
					<li class="menu-link"><a href="https://healthray.com/gofrugal-alternative/" target="_blank"> Gofrugal Alternative </a></li>
					<li class="menu-link"><a href="https://healthray.com/wondersoft-alternative/" target="_blank"> Wondersoft Alternative </a></li> 
				</ul>
			</div>
		</div>
	</div>

	<?php
	foreach ($result['data'] as $key => $value) {
		if ($value['textdata'] != '' && !empty($value['textdata']) && is_array($value['textdata'])) { ?>
			<!-- <?= $value['post_title']; ?> -->
			<div class="container">
				<div class="menu-item sub-menu-item">
					<div>
						<h2> <?= $value['post_title']; ?> </h2>

						<ul class="sub-menu">
							<li class="menu-link"><a href="https://healthray.com/<?= $value['post_name']; ?>/"><?= $value['post_title']; ?> </a></li>
							<?php foreach ($value['textdata'] as $key => $val) { ?>
								<li class="menu-link"><a href="https://healthray.com/<?= $val['post_name']; ?>/"><?= $val['post_title']; ?> </a></li>
							<?php } ?>
						</ul>
					</div>
				</div>
			</div>

		<?php }
	} 
	?>


	<!-- Extra Links -->
	<div class="container">
		<div class="menu-item sub-menu-item">
			<div>
				<h2>Extra Links</h2>
				<ul class="sub-menu">
					<li class="menu-link"><a href="https://healthray.com/medical-billing-services/" target="_blank"> Medical Billing Services </a></li>
					<li class="menu-link"><a href="https://healthray.com/telehealth-services/" target="_blank"> Telehealth Services </a></li>
					<li class="menu-link"><a href="https://healthray.com/patient-mobile-app/" target="_blank"> Patient Mobile App </a></li>
					<li class="menu-link"><a href="https://healthray.com/doctor-mobile-app/" target="_blank"> Doctor Mobile App </a></li>
					<li class="menu-link"><a href="https://healthray.com/medical-billing-software/" target="_blank"> Medical Billing Software </a></li>
					<li class="menu-link"><a href="https://healthray.com/practice-management/" target="_blank"> Practice Management </a></li>
					<li class="menu-link"><a href="https://healthray.com/patient-engagement-software/" target="_blank"> Patient Engagement Software </a></li>
					<li class="menu-link"><a href="https://healthray.com/medical-iot-solution/" target="_blank"> Medical IoT Solution </a></li>
					<li class="menu-link"><a href="https://healthray.com/patient-flow/" target="_blank"> Patient Flow </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-pathology-lab-software/" target="_blank"> Best Pathology Lab Software </a></li>
					<li class="menu-link"><a href="https://healthray.com/free-pathology-lab-software/" target="_blank"> Free Pathology Lab Software </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-pharmacy-software/" target="_blank"> Best Pharmacy Software </a></li>
					<li class="menu-link"><a href="https://healthray.com/free-pharmacy-software/" target="_blank"> Free Pharmacy Software </a></li> 
					<li class="menu-link"><a href="https://healthray.com/inventory-management-software/" target="_blank"> Inventory Management Software </a></li> 
					<li class="menu-link"><a href="https://healthray.com/clinic-management-software/" target="_blank"> Clinic Management Software </a></li> 
					<li class="menu-link"><a href="https://healthray.com/electronic-patient-records/" target="_blank"> Electronic Patient Records </a></li> 
					<li class="menu-link"><a href="https://healthray.com/medical-college-software/" target="_blank"> Medical College Software </a></li> 
				</ul>
			</div>

		</div>
	</div>

	<!-- Legal Pages -->
	<div class="container">
		<div class="menu-item sub-menu-item">
			<div>
				<h2>Legal Pages</h2>
				<ul class="sub-menu">
					<li class="menu-link"><a href="https://healthray.com/privacy-policy/" target="_blank">Privacy
							Policy</a></li>
					<li class="menu-link"><a href="https://healthray.com/terms-condition/" target="_blank">Terms &amp;
							condition</a></li>
					<li class="menu-link"><a href="https://healthray.com/refund-cancellation-policy/"
							target="_blank">Refund &amp; Cancellation Policy</a></li>
					<li class="menu-link"><a href="https://healthray.com/dpa/" target="_blank">DPA</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>