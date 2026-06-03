<style>
	body {	background: linear-gradient(135deg, #f8fafc, #eef2f7);	font-family: 'Inter', sans-serif; }

	.page-header { text-align: center; margin-bottom: 40px; }
	.page-header h1 { font-weight: 700; font-size: 32px; color: #1e293b; }
	.page-header p { color: #64748b; margin-top: 8px; }

	.search-box { max-width: 400px; margin: 20px auto; position: relative; }
	.search-box input { width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid #e2e8f0; outline: none; }
	.search-box input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }

	.pages-grid { display: grid;  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));  gap: 24px; }
	.glass-card { border-radius: 18px; background: rgba(255, 255, 255, 0.65); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.8); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06); transition: all 0.35s ease; height: 100%; display: flex; flex-direction: column; overflow: hidden; }
	.glass-card:hover { transform: scale(1.02); box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1); }
	.glass-card-img img { width: 100%; height: 160px; object-fit: cover; }
	.glass-card .glass-body { padding: 18px; }
	.glass-card .badge-index { background: #dcfce7; color: #16a34a; }
	.glass-card .badge-noindex { background: #fee2e2; color: #dc2626; }
	.glass-card .meta-info { font-size: 13px; color: #64748b; display: flex; flex-direction: column; gap: 4px; margin-bottom: 10px; }
	.glass-card .meta-info code { padding: 2px 4px; display: inline-block; background: #fcfcfc; border: 1px solid #c5d0dd; border-radius: 8px; font-family: monospace; }
	.glass-card h4 { font-size: 18px; margin-bottom: 8px; }
	.glass-card a { text-decoration: none; color: #2563eb; font-weight: 600; }
	.glass-card a:hover { color: #1e40af; }
	.card-excerpt { font-size: 14px; color: #64748b; }
	.badge { display: inline-block; background: #e0f2fe; color: #0284c7; padding: 4px 10px; border-radius: 999px; font-size: 12px; margin-bottom: 8px; }
	.no-results { text-align: center; color: #64748b; }
</style>

<?php
$args = array(
	'post_type'      => 'page',
	'posts_per_page' => -1,
);
$query = new WP_Query($args);
$total = $query->found_posts;
?>

<div class="py-5">
	<div class="container">
		<div class="page-header">
			<h1>All Pages</h1>
			<p><?php echo $total; ?> pages available</p>
		</div>

		<div class="search-box">
			<input type="text" id="searchInput" placeholder="Search pages...">
		</div>

		<?php if ($query->have_posts()) : ?>
		<div class="pages-grid" id="pagesGrid">
			<?php while ($query->have_posts()) : $query->the_post();
			$post_id = get_the_ID();
			
			$noindex = (get_post_meta($post_id, '_yoast_wpseo_meta-robots-noindex', true) == '1') ? true : false; 
			$index_status = $noindex ? 'Noindex' : 'Index';
			$slug = $post->post_name;
			$date = get_the_date('d M Y');
			?>
			
			<div class="page-card glass-card" data-title="<?php echo strtolower(get_the_title()); ?>">
				<div class="glass-card-img">
					<?php
					if (has_post_thumbnail()) the_post_thumbnail('medium', ['loading' => 'lazy']);
					else  echo 'default <img src="' . get_template_directory_uri() . '/assets/default.jpg" loading="lazy">';
					?>
				</div>
				<div class="glass-body">
					<span class="badge <?php echo $noindex ? 'badge-noindex' : 'badge-index'; ?>"> <?php echo $index_status; ?> 	</span>
					<h4> <a href="<?php the_permalink(); ?>"> <?php the_title(); ?> </a> </h4>
						<!-- Meta Info -->
					<div class="meta-info ">
						<code><?php echo esc_html($slug); ?></code>
						<span><?php echo esc_html($date); ?></span>
					</div>
					
					<div class="card-excerpt"> <?php echo wp_trim_words(get_the_excerpt(), 12); ?> </div>
				</div>
			</div>
			<?php endwhile; ?>
		</div>
		<?php else : ?>
		<p class="no-results">No pages found.</p>
		<?php endif; ?>
	</div>
</div>

<script>
	const searchInput = document.getElementById('searchInput');
	const cards = document.querySelectorAll('.page-card');

	searchInput.addEventListener('keyup', function() {
		const value = this.value.toLowerCase();
		cards.forEach(card => {
			const title = card.getAttribute('data-title');
			if (title.includes(value)) {
				card.style.display = "flex";
			} else {
				card.style.display = "none";
			}
		});
	});
</script>

<?php wp_reset_postdata(); ?>