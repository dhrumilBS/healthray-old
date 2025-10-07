<?php
/**
 * Template for displaying Event archive
 * File: archive-event.php
 */

get_header(); ?>

<main id="primary" class="site-main container py-12">

    <header class="page-header mb-12 text-center">
        <h1 class="text-4xl font-bold">Events & News</h1>
        <p class="text-muted-foreground mt-2">Stay updated with our latest events, news, and announcements</p>
    </header>

    <?php if ( have_posts() ) : ?>
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <?php
            while ( have_posts() ) :
                the_post();

                $event_date     = get_post_meta( get_the_ID(), 'event_date', true );
                $event_location = get_post_meta( get_the_ID(), 'event_location', true );
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class("event-card border rounded-lg shadow-sm hover:shadow-md transition p-6"); ?>>
                    
                    <?php if ( has_post_thumbnail() ) : ?>
                        <a href="<?php the_permalink(); ?>" class="block mb-4">
                            <?php the_post_thumbnail('medium_large', ['class' => 'rounded-md w-full h-56 object-cover']); ?>
                        </a>
                    <?php endif; ?>

                    <h2 class="text-2xl font-semibold mb-2">
                        <a href="<?php the_permalink(); ?>" class="hover:text-primary">
                            <?php the_title(); ?>
                        </a>
                    </h2>

                    <?php if ( $event_date || $event_location ) : ?>
                        <p class="text-sm text-muted-foreground mb-3">
                            <?php if ( $event_date ) : ?>
                                <span class="mr-2"><strong>Date:</strong> <?php echo esc_html( $event_date ); ?></span>
                            <?php endif; ?>
                            <?php if ( $event_location ) : ?>
                                <span><strong>Location:</strong> <?php echo esc_html( $event_location ); ?></span>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>

                    <div class="text-base mb-4">
                        <?php echo wp_trim_words( get_the_excerpt(), 20, '...' ); ?>
                    </div>

                    <a href="<?php the_permalink(); ?>" class="inline-block text-primary font-medium hover:underline">
                        Read More →
                    </a>
                </article>
            <?php endwhile; ?>
        </div>

        <div class="mt-12">
            <?php the_posts_pagination([
                'mid_size'  => 2,
                'prev_text' => __('« Prev', 'stratus'),
                'next_text' => __('Next »', 'stratus'),
            ]); ?>
        </div>

    <?php else : ?>
        <p class="text-center text-lg">No events found.</p>
    <?php endif; ?>

</main>

<?php
get_footer();
