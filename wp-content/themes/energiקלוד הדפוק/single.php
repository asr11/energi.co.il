<?php
get_header(); ?>
<div id="contentarea">
	<div id="maincontent">

<h1 class="biz-title"> <?php the_title(); ?> </h1>
<?php the_content() ?>

<h2>נושאים קשורים:</h2>
<?php
$related = get_posts( array( 'category__in' => wp_get_post_categories($post->ID), 'numberposts' => 4, 'post__not_in' => array($post->ID) ) );
if( $related ) foreach( $related as $post ) {
setup_postdata($post); ?>
 <ul> 
        <li>
        <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a>
            <?php the_content('Read the rest of this entry &raquo;'); ?>
        </li>
    </ul>   
<?php }
wp_reset_postdata(); ?>
	</div>
<?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>