<?php
global $post;
$author = get_queried_object();   
$author_id = get_the_author_meta('ID');
?>
<style>
	.author-template .container-md{ max-width: 991px !important; }
	.author-hero{ min-height: 250px; background-color: #d8eeff; display: flex; align-items: center; justify-content: center; padding: 50px 0; margin-bottom: 150px; }

	.author-hero .author-section {display: flex; justify-content: center; align-items: center; margin-bottom: -155px; }
	.author-hero .author-section .author{ display: flex; max-width: 100%; padding: 12px; background: #fff; border-radius: 8px; box-shadow: rgba(17, 12, 46, 0.15) 0px 48px 100px 0px; flex-wrap: wrap; gap: 16px; width: 100%;}
	.author-hero .author-section .author .author-profile { flex-shrink: 0;}
	.author-hero .author-section .author .author-profile img { border-radius:10px; width : 100%; height: auto}

	.author-hero .author-section .author .about-author { display: flex; flex-direction: column; } 
	.author-hero .author-section .author .about-author .author-follow { flex-grow: 1; align-content: flex-end} 
	.author-hero .author-follow .social-media-list { display: flex; list-style: none; padding:0; margin:12px 0 0; gap:16px}
	.author-hero .author-follow .social-media-list li a { display: flex; justify-content:center; align-items:center; padding: 12px; border-radius: 8px; background-color: #f0f8ff;  }
	.author-hero .author-follow .social-media-list li a:hover { color: #fff; background: var(--hr-primary-color);}
	.author-hero .author-follow .social-media-list li a svg{ width:24px; height: 24px; }

	@media screen and (min-width:575px){
		.author-hero .author-section .author{ flex-wrap: nowrap; padding: 20px;border-radius: 20px;   }
		.author-hero .author-section .author .author-profile img { width:200px; height:auto; }
	}

	@media screen and (min-width:991px){
		.author-hero .author-section .author{ max-width: 65%; }
	}

	@media screen and (min-width:1200px){
		.author-hero .author-section .author{ max-width: 90%; }
	}

	.author-content { align-content:center; font-size:17px;}
	.author-content .box-container{ padding-bottom: 30px}
	.author-content .title{ margin-bottom: 0.45em}
	.author-content p { margin-bottom: .5em; font-size:17px; text-align: justify;}
	.author-content a{ font-weight: 600; color:var(--hr-secondary-color);}
	.author-content .theme-btn { text-transform: uppercase; color: #fff; background-color: #5669ec; border-radius: 8px; padding: 16px 20px; line-height: 1; display: inline-block; }
	.author-content .theme-btn:hover{ background-color: #465efb; box-shadow: 0 0 5px 0 #5168fb80; }
	
	.blog-container .elementor-heading-title { margin-bottom: 24px;}
	.blog-container .th-masonry-blog h2 { margin-bottom: 12px;}
	.blog-container .th-masonry-blog .mas-blog .mas-blog-post .post-inner { margin: 0;}
</style>
 

<div class="author-template">
	<section class="author-hero hero">
		<div class="container-md">
			<div class="author-section">
				<div class="author">
					<div class="author-profile">
						<?= "<img src='".get_avatar_url($author_id, ['size' => 250 ])."' alt='".$author->display_name."' height='250' width='250' />"; ?> 
					</div>
					<div class="about-author">
						<h1 class="author-name"><?= $author->display_name; ?></h1>
						<p class="author-position"><?= get_the_author_meta('user_position'); ?></p>
						<div class="author-follow"> 
							<ul class="social-media-list">
								<li>
									<a href="<?= get_the_author_meta('linkedin'); ?>" target="_blank" rel="nofollow noopener" title="Linkedin">										
										<svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M6.93994 5.00002C6.93968 5.53046 6.72871 6.03906 6.35345 6.41394C5.97819 6.78883 5.46937 6.99929 4.93894 6.99902C4.40851 6.99876 3.89991 6.78779 3.52502 6.41253C3.15014 6.03727 2.93968 5.52846 2.93994 4.99802C2.94021 4.46759 3.15117 3.95899 3.52644 3.5841C3.9017 3.20922 4.41051 2.99876 4.94094 2.99902C5.47137 2.99929 5.97998 3.21026 6.35486 3.58552C6.72975 3.96078 6.94021 4.46959 6.93994 5.00002ZM6.99994 8.48002H2.99994V21H6.99994V8.48002ZM13.3199 8.48002H9.33994V21H13.2799V14.43C13.2799 10.77 18.0499 10.43 18.0499 14.43V21H21.9999V13.07C21.9999 6.90002 14.9399 7.13002 13.2799 10.16L13.3199 8.48002Z" fill="currentcolor"></path></svg>
									</a>
								</li>
								<li> 
									<a href="<?= get_the_author_meta('youtube'); ?>" target="_blank" rel="nofollow noopener" title="You Tube">
										<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50"  width="24" height="24" fill="currentcolor"><path d="M 44.898438 14.5 C 44.5 12.300781 42.601563 10.699219 40.398438 10.199219 C 37.101563 9.5 31 9 24.398438 9 C 17.800781 9 11.601563 9.5 8.300781 10.199219 C 6.101563 10.699219 4.199219 12.199219 3.800781 14.5 C 3.398438 17 3 20.5 3 25 C 3 29.5 3.398438 33 3.898438 35.5 C 4.300781 37.699219 6.199219 39.300781 8.398438 39.800781 C 11.898438 40.5 17.898438 41 24.5 41 C 31.101563 41 37.101563 40.5 40.601563 39.800781 C 42.800781 39.300781 44.699219 37.800781 45.101563 35.5 C 45.5 33 46 29.398438 46.101563 25 C 45.898438 20.5 45.398438 17 44.898438 14.5 Z M 19 32 L 19 18 L 31.199219 25 Z"/></svg>
									</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>


	<section class="author-content ">
		<div class="container-md">
			<div class="content">
				<?php 
				if(get_user_meta( $author_id )['user_description']) {
					echo  get_user_meta( $author_id )['user_description'][0];	
				}
				?>
			</div>
		</div>
	</section>


	<section class="inner-container blog-container">
		<div class="container">

			<?php
			$args = ['posts_per_page' => '3','author' => get_the_author_meta('ID')];
			$query = new WP_Query($args);
			?>
			<?php if ($query->have_posts()) {  ?>
			<section class="th-masonry-blog hr-archives">		
				<div class="heading sec-heading centered">
					<div class="headline-color">
						<h2 class="elementor-heading-title elementor-size-default"> <?= $author->display_name; ?>'s Recent Articles </h2>
					</div>
				</div>
				<div class="mas-blog row" style="--gap: 1px">
					<?php while ($query->have_posts()) {$query->the_post(); ?>
					<div <?= post_class('mas-blog-post col-sm-6 col-md-4'); ?>>
					<div class="mas-blog-post-inner">
						<?php get_template_part('templates/content'); ?>
					</div>
					</div>
					<?php } ?>
				</div> 
			</section>
			<?php } ?>
		</div><!-- /.main -->
	</section><!-- /.inner-container -->


</div>