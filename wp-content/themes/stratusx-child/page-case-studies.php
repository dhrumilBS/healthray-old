<section class="sec-padded hero-section">
    <div class="container">
        <div class="heading text-center">
            <h1>Case Studies</h1>
            <p>Real stories of healthcare providers transforming their practice with innovative technology solutions</p>
        </div>
    </div>
</section>

<section class="whitepapers-section">
    <div class="container">
        <?php
        $args = [
            'post_type'      => 'whitepaper',
            'posts_per_page' => 12,
            'order'          => 'desc',
        ];

        $q = new WP_Query($args);
        if (! $q->have_posts()) { ?>
            <p class="whitepaper-none">No whitepapers found.</p>
        <?php } else { ?>
            <div class="whitepapers-grid">
                <?php
                while ($q->have_posts()) {
                    $q->the_post();
                    $post_id = get_the_ID();

                    // Title and excerpt
                    $title   = get_the_title($post_id);
                    $excerpt = get_the_excerpt($post_id);

                    if (empty($excerpt)) {
                        $excerpt = wp_trim_words(get_the_content(null, false, $post_id), 20);
                    }

                    $term_name = '';
                    $tax_list = ['whitepaper_topic', 'whitepaper_category', 'post_tag'];

                    foreach ($tax_list as $tax) {
                        $terms = get_the_terms($post_id, $tax);
                        if (! empty($terms) && ! is_wp_error($terms)) {
                            $term_name = esc_html($terms[0]->name);
                            break;
                        }
                    }

                    $thumb = get_the_post_thumbnail($post_id, 'medium');

                    $pdf_url = '';
                    $meta_pdf = get_post_meta($post_id, 'whitepaper_pdf', true);
                    if (! empty($meta_pdf)) {
                        $pdf_url = esc_url($meta_pdf);
                    } else {
                        $attachments = get_children(['post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'application/pdf', 'numberposts' => 1,]);
                        if ($attachments) {
                            $first = reset($attachments);
                            $pdf_url = esc_url(wp_get_attachment_url($first->ID));
                        }
                    }

                    $download_href = $pdf_url ? $pdf_url : get_permalink($post_id);

                    $term_out = '';
                    if ($term_name) {
                        $term_out = '<span class="whitepaper-tag">' . $term_name . '</span>';
                    }
                    $pdf_out = '';
                    if ($pdf_url) {
                        $pdf_out .= ' target="_blank" rel="noopener noreferrer"';
                    }
                ?>
                    <div class="whitepaper-card">
                        <div class="whitepaper-image" aria-hidden="true">
                            <?= $thumb; ?>
                            <?= $term_out; ?>
                        </div>
                        <h3 class="whitepaper-title"><?= esc_html($title); ?></h3>
                        <p class="whitepaper-desc"><?= esc_html($excerpt); ?></p>
                        <a href="<?= $download_href; ?>" class="btn-download" <?= $pdf_out; ?>> Download Whitepaper </a>
                    </div>
                <?php } ?>
                <?php wp_reset_postdata(); ?>
            </div>
        <?php } ?>
    </div>
</section>