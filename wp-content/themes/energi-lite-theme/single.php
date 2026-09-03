<?php get_header(); ?>
<article <?php post_class('card'); ?>>
  <h1 style="margin-top:0"><?php the_title(); ?></h1>
  <div class="entry">
    <?php while(have_posts()): the_post(); the_content(); endwhile; ?>
  </div>
</article>
<?php get_footer(); ?>