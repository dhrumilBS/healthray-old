<?php
/**
 * single.php — Healthray Single Blog Post Template
 * Dynamic TOC + Related Posts + Author Bio + Share Buttons
 */
// Helper: Generate TOC from post content

// Related Posts Logic
function healthray_get_related_posts($post_id, $limit = 3)
{
    $categories = wp_get_post_categories($post_id);
    $tags = wp_get_post_tags($post_id, ['fields' => 'ids']);

    $args = [
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => $limit,
        'post__not_in' => [$post_id],
        'ignore_sticky_posts' => 1,
        'orderby' => 'relevance',
    ];

    if (!empty($categories)) {
        $args['category__in'] = $categories;
    }
    if (!empty($tags)) {
        $args['tag__in'] = $tags;
    }

    $related = new WP_Query($args);

    // Fallback: if not enough posts, fill with recent from same category
    if ($related->post_count < $limit && !empty($categories)) {
        $args2 = [
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            'post__not_in' => array_merge([$post_id], wp_list_pluck($related->posts, 'ID')),
            'category__in' => $categories,
            'ignore_sticky_posts' => 1,
        ];
        unset($args2['tag__in']);
        $fallback = new WP_Query($args2);
        $related->posts = array_slice(array_merge($related->posts, $fallback->posts), 0, $limit);
        $related->post_count = count($related->posts);
        wp_reset_postdata();
    }

    return $related;
}

// Reading Time
function healthray_reading_time($content)
{
    $word_count = str_word_count(wp_strip_all_tags($content));
    $reading_time = max(1, ceil($word_count / 200));
    return $reading_time . ' Min Read';
}
?>

<?php while (have_posts()):
    the_post();

    $post_id = get_the_ID();
    $post_content = get_the_content();
    $post_content = apply_filters('the_content', $post_content);

    [$post_content_with_ids, $toc_items] = healthray_generate_toc($post_content);

    // Post meta
    $author_id = get_the_author_meta('ID');
    $author_name = get_the_author_meta('display_name');
    $author_bio = get_the_author_meta('description');
    $author_url = get_author_posts_url($author_id);
    $author_avatar = get_avatar_url($author_id, ['size' => 100]);
    $author_initials = implode('', array_map(fn($w) => strtoupper($w[0]), explode(' ', $author_name)));

    $published_date = get_the_date('M j, Y');
    $modified_date = get_the_modified_date('M j, Y');
    $published_iso = get_the_date('c');
    $modified_iso = get_the_modified_date('c');
    $reading_time = healthray_reading_time($post_content);
    $post_url = get_permalink();
    $post_title = get_the_title();

    // Primary category
    $categories = get_the_category();
    $primary_cat = !empty($categories) ? $categories[0] : null;

    // Related posts
    $related_query = healthray_get_related_posts($post_id, 3);

    ?>

    <!-- HERO / ARTICLE HEADER -->
    <div class="hr-single-wrapper" id="hr-single-wrapper">
        <!-- HERO SECTION -->
        <header class="hr-hero-section" role="banner">
            <div class="container">
                <div class="hr-hero-inner">
                    <nav class="hr-breadcrumb" aria-label="Breadcrumb">
                        <a href="<?= esc_url(home_url('/')); ?>">Home</a>
                        <span class="hr-sep" aria-hidden="true">›</span>
                        <a href="<?= esc_url(get_permalink(get_option('page_for_posts'))); ?>">Blog</a>
                        <?php if ($primary_cat): ?>
                            <span class="hr-sep" aria-hidden="true">›</span>
                            <a href="<?= esc_url(get_category_link($primary_cat->term_id)); ?>">
                                <?= esc_html($primary_cat->name); ?>
                            </a>
                        <?php endif; ?>
                        <span class="hr-sep" aria-hidden="true">›</span>
                        <span class="hr-breadcrumb-current" aria-current="page">
                            <?= esc_html(wp_trim_words($post_title, 6, '...')); ?>
                        </span>
                    </nav>

                    <div class="hr-meta-pills">
                        <?php if ($primary_cat): ?>
                            <a href="<?= esc_url(get_category_link($primary_cat->term_id)); ?>" class="hr-tag-pill">
                                <?= esc_html($primary_cat->name); ?>
                            </a>
                        <?php endif; ?>
                        <div class="hr-date-reading">
                            <time datetime="<?= esc_attr($published_iso); ?>"><?= esc_html($published_date); ?></time>
                            <span aria-hidden="true">·</span>
                            <span><?= esc_html($reading_time); ?></span>
                            
                            <?php if (is_user_logged_in() && ($published_date !== $modified_date)): ?>
                                <span aria-hidden="true">·</span>
                                <span>Updated <?= esc_html($modified_date); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <h1 class="hr-blog-title"> <?php the_title(); ?> </h1>

                    <!-- Author + Share Row -->
                    <div class="hr-author-row">
                        <div class="hr-author-info-wrap">
                            <div class="hr-author-avatar" aria-hidden="true">
                                <?php if ($author_avatar): ?>
                                    <img src="<?= esc_url($author_avatar); ?>" alt="<?= esc_attr($author_name); ?>" width="40" height="40" loading="lazy">
                                <?php else: ?>
                                    <span class="hr-author-initials"><?= esc_html($author_initials); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="hr-author-details">
                                <a href="<?= esc_url($author_url); ?>" class="hr-author-name" rel="author"><span><?= esc_html($author_name); ?></span> </a>
                                <?php
                                $author_job = get_the_author_meta('job_title') ?: get_bloginfo('name');
                                ?>
                                <span class="hr-author-role"><?= esc_html($author_job); ?></span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </header>

        <div id="hr-content" tabindex="-1">
            <div class="container">
                <div class="hr-blog-row">

                    <main class="hr-content-area" id="hr-main" role="main">

                        <?php if (has_post_thumbnail()): ?>
                            <div class="hr-featured-image"> <?php the_post_thumbnail('full', ['class' => 'hr-hero-img','loading' => 'lazy']); ?></div>
                        <?php endif; ?>

                        <?php if (!empty($toc_items)): ?>
                            <div class="hr-toc-mobile">
                                <button class="hr-toc-toggle" type="button" aria-expanded="false" aria-controls="hr-toc-mobile-list">
                                    <span class="hr-toc-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18"
                                            height="18" aria-hidden="true">
                                            <line x1="3" y1="6" x2="21" y2="6" />
                                            <line x1="3" y1="12" x2="15" y2="12" />
                                            <line x1="3" y1="18" x2="18" y2="18" />
                                        </svg>
                                    </span>
                                    <span>Table of Contents</span>
                                    <span class="hr-toc-chevron" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14"
                                            height="14">
                                            <polyline points="6 9 12 15 18 9" />
                                        </svg>
                                    </span>
                                </button>
                                <nav id="hr-toc-mobile-list" class="hr-toc-nav hr-toc-collapsed" aria-label="Table of Contents">
                                    <ol class="hr-toc-list">
                                        <?php foreach ($toc_items as $i => $item): ?>
                                            <li class="hr-toc-item hr-toc-<?= esc_attr($item['tag']); ?>">
                                                <a href="#<?= esc_attr($item['anchor']); ?>" class="hr-toc-link">
                                                    <span class="hr-toc-num"><?= esc_html($i + 1); ?>.</span>
                                                    <?= esc_html($item['text']); ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ol>
                                </nav>
                            </div>
                        <?php endif; ?>

                        <div class="hr-post-content entry-content">
                            <?= $post_content_with_ids; // already filtered above ?>
                        </div>
                        
                        <div class="hr-post-cta"><?= do_shortcode('[blog_cta_end]'); ?></div>
                        
                        <?php if (have_rows('blog_faqs')): ?>
                            <div class="hr-post-faqs">
                                <h2 class="hr-faqs-heading">Frequently Asked Questions</h2>
                                <div class="elementor-toggle accordion-list">
                                    <?php while (have_rows('blog_faqs')):
                                        the_row(); ?>
                                        <div class="elementor-toggle-item">
                                            <div id="elementor-tab-title-<?= get_row_index(); ?>" class="elementor-tab-title"
                                                data-tab="<?= get_row_index(); ?>" role="button"
                                                aria-controls="elementor-tab-content-<?= get_row_index(); ?>" aria-expanded="false">

                                                <span class="elementor-toggle-icon elementor-toggle-icon-left" aria-hidden="true">
                                                    <span class="elementor-toggle-icon-closed">
                                                        <svg class="e-font-icon-svg e-fas-caret-down" viewBox="0 0 320 512" xmlns="http://www.w3.org/2000/svg" width="16" height="16">
                                                            <path d="M31.3 192h257.3c17.8 0 26.7 21.5 14.1 34.1L174.1 354.8c-7.8 7.8-20.5 7.8-28.3 0L17.2 226.1C4.6 213.5 13.5 192 31.3 192z" />
                                                        </svg>
                                                    </span>
                                                    <span class="elementor-toggle-icon-opened">
                                                        <svg class="elementor-toggle-icon-opened e-font-icon-svg e-fas-caret-up" viewBox="0 0 320 512" xmlns="http://www.w3.org/2000/svg" width="16" height="16">
                                                            <pathd="M288.662 352H31.338c-17.818 0-26.741-21.543-14.142-34.142l128.662-128.662c7.81-7.81 20.474-7.81 28.284 0l128.662 128.662c12.6 12.599 3.676 34.142-14.142 34.142z" />
                                                        </svg>
                                                    </span>
                                                </span>

                                                <a class="elementor-toggle-title" tabindex="0">
                                                    <?= get_sub_field('question'); ?>
                                                </a>
                                            </div>

                                            <div id="elementor-tab-content-<?= get_row_index(); ?>"
                                                class="elementor-tab-content elementor-clearfix answer"
                                                data-tab="<?= get_row_index(); ?>" role="region"
                                                aria-labelledby="elementor-tab-title-<?= get_row_index(); ?>">
                                                <div><?= get_sub_field('answer'); ?></div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>

                                    <?php
                                    // FAQ Schema — only output when ACF field has data
                                    $faq_rows = get_field('blog_faqs');
                                    if (!empty($faq_rows)):
                                        $faq_json = [
                                            '@context' => 'https://schema.org',
                                            '@type' => 'FAQPage',
                                            'mainEntity' => [],
                                        ];
                                        foreach ($faq_rows as $faq_item) {
                                            $faq_json['mainEntity'][] = [
                                                '@type' => 'Question',
                                                'name' => wp_strip_all_tags($faq_item['question']),
                                                'acceptedAnswer' => [
                                                    '@type' => 'Answer',
                                                    'text' => wp_strip_all_tags($faq_item['answer']),
                                                ],
                                            ];
                                        }
                                        ?>
                                        <script type="application/ld+json">
                                                    <?= wp_json_encode($faq_json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
                                                </script>
                                    <?php endif; ?>

                                </div><!-- /.elementor-toggle.accordion-list -->
                            </div>
                        <?php endif; ?>

                        <nav class="hr-post-navigation" aria-label="Post navigation">
                            <?php
                            $prev_post = get_previous_post();
                            $next_post = get_next_post();
                            ?>
                            <?php if ($prev_post): ?>
                                <a href="<?= esc_url(get_permalink($prev_post->ID)); ?>" class="hr-nav-prev">
                                    <span class="hr-nav-dir">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16"
                                            height="16">
                                            <polyline points="15 18 9 12 15 6" />
                                        </svg>
                                        Previous
                                    </span>
                                    <span class="hr-nav-title"><?= esc_html(get_the_title($prev_post->ID)); ?></span>
                                </a>
                            <?php endif; ?>
                            <?php if ($next_post): ?>
                                <a href="<?= esc_url(get_permalink($next_post->ID)); ?>" class="hr-nav-next">
                                    <span class="hr-nav-dir">
                                        Next
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16"
                                            height="16">
                                            <polyline points="9 18 15 12 9 6" />
                                        </svg>
                                    </span>
                                    <span class="hr-nav-title"><?= esc_html(get_the_title($next_post->ID)); ?></span>
                                </a>
                            <?php endif; ?>
                        </nav>

                        <div class="hr-author-bio-box" aria-label="About the author">
                            <div class="hr-bio-avatar">
                                <?php if ($author_avatar): ?>
                                    <img src="<?= esc_url($author_avatar); ?>" alt="<?= esc_attr($author_name); ?>" width="80"
                                        height="80" loading="lazy" class="hr-bio-img">
                                <?php else: ?>
                                    <div class="hr-bio-initials" aria-hidden="true"><?= esc_html($author_initials); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="hr-bio-info">
                                <p class="hr-bio-heading">About the Author</p>
                                <div class="hr-bio-name h3" itemprop="name"><?= esc_html($author_name); ?></div>
                                <?php if ($author_bio): ?>
                                    <div class="hr-bio-desc" itemprop="description">
                                        <?= wp_kses_post(wpautop($author_bio)); ?>
                                    </div>
                                <?php endif; ?>
                                <a href="<?= esc_url($author_url); ?>" class="hr-bio-link" itemprop="url">
                                    View all posts →
                                </a>
                            </div>
                        </div>
                    </main>

                    <aside class="hr-sidebar" id="hr-sidebar" aria-label="Article sidebar">

                        <!-- CTA Box -->
                        <div class="hr-sidebar-cta hr-sidebar-card">
                            <p class="hr-cta-title">Explore Healthray for Free</p>
                            <ul class="hr-cta-list">
                                <li>Manage patient records</li>
                                <li>Appointment scheduling</li>
                                <li>Prescription management</li>
                                <li>24/7 support</li>
                            </ul>
                            <button class="hr-cta-btn"> Try it Now! </button>
                        </div>
                        
                        <div class="hr-sidebar-card hr-sidebar-recent">
                            <p class="hr-sidebar-widget-title">Recent Posts</p>
                            <?php
                            $recent = new WP_Query([
                                'post_type' => 'post',
                                'post_status' => 'publish',
                                'posts_per_page' => 4,
                                'post__not_in' => [$post_id],
                                'ignore_sticky_posts' => 1,
                            ]);
                            if ($recent->have_posts()):
                                while ($recent->have_posts()):
                                    $recent->the_post();
                                    ?>
                                    <a href="<?php the_permalink(); ?>" class="hr-recent-post-item">
                                        <?php if (has_post_thumbnail()): ?>
                                            <div class="hr-recent-thumb">
                                                <?php the_post_thumbnail('thumbnail', ['loading' => 'lazy', 'alt' => get_the_title()]); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="hr-recent-info">
                                            <span class="hr-recent-title"><?php the_title(); ?></span>
                                            <time class="hr-recent-date" datetime="<?= esc_attr(get_the_date('c')); ?>">
                                                <?= esc_html(get_the_date('M j, Y')); ?>
                                            </time>
                                        </div>
                                    </a>
                                    <?php
                                endwhile;
                                wp_reset_postdata();
                            endif;
                            ?>
                        </div>


                        <?php if (!empty($toc_items)): ?>
                            <div class="hr-sidebar-toc hr-sidebar-card hr-toc-sticky" id="hr-toc-sidebar">
                                <p class="hr-toc-heading">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16"
                                        height="16" aria-hidden="true">
                                        <line x1="3" y1="6" x2="21" y2="6" />
                                        <line x1="3" y1="12" x2="15" y2="12" />
                                        <line x1="3" y1="18" x2="18" y2="18" />
                                    </svg>
                                    Table of Contents
                                </p>
                                <nav aria-label="Table of Contents (sidebar)">
                                    <ol class="hr-toc-list">
                                        <?php foreach ($toc_items as $i => $item): ?>
                                            <li class="hr-toc-item hr-toc-<?= esc_attr($item['tag']); ?>">
                                                <a href="#<?= esc_attr($item['anchor']); ?>" class="hr-toc-link"
                                                    data-anchor="<?= esc_attr($item['anchor']); ?>">
                                                    <span class="hr-toc-num"><?= esc_html($i + 1); ?>.</span>
                                                    <?= esc_html($item['text']); ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ol>
                                </nav>
                            </div>
                        <?php endif; ?>
                    </aside>
                </div>
            </div>
        </div>

        <!-- RELATED POSTS -->
        <?php if ($related_query->have_posts()): ?>
            <section class="hr-related-section" aria-label="Related articles">
                <div class="container">
                    <div class="hr-related-header">
                        <h2 class="hr-related-title">Related Articles</h2>
                        <p class="hr-related-sub">Continue exploring similar topics and insights.</p>
                    </div>
                    <div class="hr-related-grid">
                        <?php
                        foreach ($related_query->posts as $rel_post):
                            $rel_id = $rel_post->ID;
                            $rel_cats = get_the_category($rel_id);
                            $rel_cat = !empty($rel_cats) ? $rel_cats[0] : null;
                            $rel_date = get_the_date('M j, Y', $rel_id);
                            $rel_date_iso = get_the_date('c', $rel_id);
                            $rel_author = get_the_author_meta('display_name', $rel_post->post_author);
                            $rel_initials = implode('', array_map(fn($w) => strtoupper($w[0]), explode(' ', $rel_author)));
                            $rel_thumb = get_the_post_thumbnail_url($rel_id, 'medium_large');
                            $rel_excerpt = wp_trim_words(get_the_excerpt($rel_id), 18, '…');
                            ?>
                            <article class="hr-post-card">
                                <a href="<?= esc_url(get_permalink($rel_id)); ?>" class="hr-card-img-wrap" tabindex="-1"
                                    aria-hidden="true">
                                    <?php if ($rel_thumb): ?>
                                        <img src="<?= esc_url($rel_thumb); ?>" alt="<?= esc_attr(get_the_title($rel_id)); ?>"
                                            loading="lazy" width="400" height="210" class="hr-card-img">
                                    <?php else: ?>
                                        <div class="hr-card-img hr-card-img-placeholder" aria-hidden="true"></div>
                                    <?php endif; ?>
                                </a>
                                <div class="hr-card-body">
                                    <div class="hr-card-meta">
                                        <?php if ($rel_cat): ?>
                                            <a href="<?= esc_url(get_category_link($rel_cat->term_id)); ?>" class="hr-card-cat-pill">
                                                <?= esc_html($rel_cat->name); ?>
                                            </a>
                                        <?php endif; ?>
                                        <time class="hr-card-date" datetime="<?= esc_attr($rel_date_iso); ?>">
                                            <?= esc_html($rel_date); ?>
                                        </time>
                                    </div>
                                    <h3 class="hr-card-title">
                                        <a href="<?= esc_url(get_permalink($rel_id)); ?>">
                                            <?= esc_html(get_the_title($rel_id)); ?>
                                        </a>
                                    </h3>
                                    <?php if ($rel_excerpt): ?>
                                        <p class="hr-card-excerpt"><?= esc_html($rel_excerpt); ?></p>
                                    <?php endif; ?>
                                    <div class="hr-card-footer">
                                        <div class="hr-card-author">
                                            <div class="hr-card-author-avatar" aria-hidden="true">
                                                <?= esc_html($rel_initials); ?>
                                            </div>
                                            <span><?= esc_html($rel_author); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- View All Posts link -->
                    <div class="hr-related-view-all">
                        <a href="<?= esc_url(get_permalink(get_option('page_for_posts'))); ?>" class="hr-view-all-btn">
                            View All Articles
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"
                                aria-hidden="true">
                                <path d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </section>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>

    </div>

    <!-- INLINE JS — TOC Active State + Smooth Scroll + Mobile Toggle -->
    <script>
        (function () {
            'use strict';

            /* ── Mobile TOC Toggle ── */
            const mobileToggle = document.querySelector('.hr-toc-toggle');
            const mobileTocNav = document.getElementById('hr-toc-mobile-list');

            if (mobileToggle && mobileTocNav) {
                mobileToggle.addEventListener('click', function () {
                    const expanded = this.getAttribute('aria-expanded') === 'true';
                    this.setAttribute('aria-expanded', String(!expanded));
                    mobileTocNav.classList.toggle('hr-toc-collapsed');
                });
            }

            /* ── Smooth Scroll for TOC Links ── */
            document.querySelectorAll('.hr-toc-link').forEach(function (link) {
                link.addEventListener('click', function (e) {
                    const targetId = this.getAttribute('href').slice(1);
                    const target = document.getElementById(targetId);
                    if (target) {
                        e.preventDefault();
                        const offset = 80; // sticky header height
                        const top = target.getBoundingClientRect().top + window.pageYOffset - offset;
                        window.scrollTo({ top: top, behavior: 'smooth' });
                        // Close mobile toc
                        if (mobileTocNav) {
                            mobileTocNav.classList.add('hr-toc-collapsed');
                            if (mobileToggle) mobileToggle.setAttribute('aria-expanded', 'false');
                        }
                    }
                });
            });

            /* ── TOC Active Highlight on Scroll (sidebar) ── */
            const tocLinks = document.querySelectorAll('#hr-toc-sidebar .hr-toc-link[data-anchor]');

            if (tocLinks.length) {
                const headings = [];
                tocLinks.forEach(function (link) {
                    const id = link.getAttribute('data-anchor');
                    const el = document.getElementById(id);
                    if (el) headings.push({ el: el, link: link });
                });

                let ticking = false;
                function updateActiveToc() {
                    const scrollY = window.pageYOffset;
                    let activeIndex = 0;
                    headings.forEach(function (h, i) {
                        if (h.el.getBoundingClientRect().top + scrollY - 120 <= scrollY) {
                            activeIndex = i;
                        }
                    });
                    tocLinks.forEach(function (l) { l.classList.remove('hr-toc-active'); });
                    if (headings[activeIndex]) {
                        headings[activeIndex].link.classList.add('hr-toc-active');
                    }
                    ticking = false;
                }

                window.addEventListener('scroll', function () {
                    if (!ticking) {
                        requestAnimationFrame(updateActiveToc);
                        ticking = true;
                    }
                }, { passive: true });

                updateActiveToc();
            }

            /* ── Reading Progress Bar ── */
            const progressBar = document.getElementById('hr-progress-bar');
            if (progressBar) {
                window.addEventListener('scroll', function () {
                    const docEl = document.documentElement;
                    const scroll = docEl.scrollTop || document.body.scrollTop;
                    const height = docEl.scrollHeight - docEl.clientHeight;
                    progressBar.style.width = (height > 0 ? (scroll / height) * 100 : 0) + '%';
                }, { passive: true });
            }
        })();
    </script>

<?php endwhile; ?>