<?php get_header(); ?>
<header class="card">
  <h1 style="margin:0"><?php the_archive_title(); ?></h1>
  <?php the_archive_description('<p class="description">','</p>'); ?>
</header>
<section class="grid" style="gap:var(--spacing)">
<?php if(have_posts()): while(have_posts()): the_post(); ?>
  <article <?php post_class('card'); ?>>
    <h2 style="margin-top:0"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <?php the_excerpt(); ?>
  </article>
<?php endwhile; the_posts_pagination(); else: ?>
  <p><?php _e('אין תכנים להצגה.', 'energi-lite'); ?></p>
<?php endif; ?>
</section>
<?php get_footer(); ?>