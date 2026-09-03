<?php get_header(); ?>
<section class="grid" style="gap:clamp(16px,2vw,24px)">
  <?php if( is_home() && ! is_front_page() ): ?>
    <h1><?php single_post_title(); ?></h1>
  <?php endif; ?>

  <?php if( have_posts() ): while( have_posts() ): the_post(); ?>
    <article <?php post_class('card'); ?>>
      <header>
        <h2 style="margin-top:0"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <small style="color:#6b7280"><?php the_time(get_option('date_format')); ?></small>
      </header>
      <div class="entry">
        <?php the_excerpt(); ?>
      </div>
    </article>
  <?php endwhile; the_posts_pagination(); else: ?>
    <p><?php _e('לא נמצאו תכנים.', 'energi-lite'); ?></p>
  <?php endif; ?>
</section>
<?php get_footer(); ?>