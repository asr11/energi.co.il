<?php
/*
Template Name: Blog & Articles Index (2026)
*/
get_header(); ?>

<div id="contentarea" class="hub-blog-page">
  <main id="maincontent" class="blog-full-width">
    
    <div class="blog-page-header" style="margin-bottom: 30px; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px;">
      <h1 style="color: #0d3b66; font-size: 2.2rem; font-family: 'Rubik', sans-serif; margin-bottom: 8px;">📝 בלוג ומדריכי אנרגיה 2026</h1>
      <p style="color: #64748b; font-size: 1.1rem; margin: 0;">כל המאמרים, המדריכים והחידושים בתחומי האנרגיה הסולארית, הרכבים החשמליים והחיסכון בחשמל.</p>
    </div>

    <div class="hub-articles-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 25px;">
      <?php
      $args = array(
          'post_type'      => 'post',
          'post_status'    => 'publish',
          'posts_per_page' => -1,
          'orderby'        => 'date',
          'order'          => 'DESC'
      );
      $blog_query = new WP_Query($args);

      if ($blog_query->have_posts()) :
          while ($blog_query->have_posts()) : $blog_query->the_post();
      ?>
        <article class="article-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 22px; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.04); transition: transform 0.2s ease, box-shadow 0.2s ease;">
          <div>
            <?php if (has_post_thumbnail()) : ?>
              <div class="article-thumb" style="border-radius: 12px; overflow: hidden; margin-bottom: 15px; height: 180px;">
                <?php the_post_thumbnail('medium', array('style' => 'width: 100%; height: 100%; object-fit: cover;')); ?>
              </div>
            <?php endif; ?>

            <div class="article-meta" style="font-size: 0.85rem; color: #10b981; font-weight: bold; margin-bottom: 8px;">
              <span>📅 <?php echo get_the_date('d/m/Y'); ?></span>
              <span style="margin: 0 5px;">•</span>
              <span>📂 <?php the_category(', '); ?></span>
            </div>

            <h2 style="font-size: 1.25rem; line-height: 1.4; margin-top: 0; margin-bottom: 12px;">
              <a href="<?php the_permalink(); ?>" style="color: #0d3b66; text-decoration: none; font-family: 'Rubik', sans-serif; font-weight: 700;">
                <?php the_title(); ?>
              </a>
            </h2>

            <div class="article-excerpt" style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin-bottom: 15px;">
              <?php echo wp_trim_words(get_the_excerpt(), 22, '...'); ?>
            </div>
          </div>

          <div class="article-footer" style="padding-top: 12px; border-top: 1px solid #f1f5f9;">
            <a href="<?php the_permalink(); ?>" style="color: #10b981; font-weight: 700; font-size: 0.95rem; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
              קרא את המאמר המלא ⟵
            </a>
          </div>
        </article>
      <?php
          endwhile;
          wp_reset_postdata();
      else :
      ?>
        <p>לא נמצאו מאמרים להצגה.</p>
      <?php endif; ?>
    </div>

  </main>
</div>

<?php get_footer(); ?>
