<?php get_header(); ?>
<h1><?php printf( __('תוצאות חיפוש: %s', 'energi-lite'), get_search_query() ); ?></h1>
<section class="grid" style="gap:var(--spacing)">
<?php if(have_posts()): while(have_posts()): the_post(); ?>
  <article <?php post_class('card'); ?>>
    <h2 style="margin-top:0"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <?php the_excerpt(); ?>
  </article>
<?php endwhile; the_posts_pagination(); else: ?>
  <p><?php _e('לא נמצאו תוצאות.', 'energi-lite'); ?></p>
<?php endif; ?>
</section>
<?php get_footer(); ?>