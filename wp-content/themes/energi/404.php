<?php get_header(); ?>
<div class="container">
   <div class="right-main">
<?php get_sidebar(); ?>
   </div> <!---end right-main!--->
   <div class="left-main">
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<img src="<?php echo get_template_directory_uri(); ?>/images/404.png" alt="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_url( home_url( '/' ) ); ?>" align="center"/>
				<h3 class="page-title"><?php _e('שגיאה 404, הדף לא נמצא', 'themezee_lang'); ?></h3>
					<p><?php _e('הדף שאתה מנסה להגיע אינו קיים, או הועבר. נא להשתמש בתפריטים או בתיבת החיפוש כדי למצוא את מה שאתה מחפש.', 'themezee_lang'); ?></p>
					<?php wp_reset_query(); ?> 
			</div>
   </div><!---end left-main!--->
   <div style="clear:both;"></div>
</div><!---end container!--->
<?php get_footer(); ?>	