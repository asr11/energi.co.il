<!DOCTYPE html>
<html lang="he">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="<?php bloginfo( 'charset' ); ?>">
<title><?php // bloginfo('name'); ?><?php wp_title( '|', true, 'left' ); ?></title>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>">
<?php wp_head();  ?>	

</head>
<body>
<div align="center">
<div id="inner">
	
<header>
<!-- <a href="<?php bloginfo('url'); ?>"><img src="images/logo.png" alt="<?php bloginfo('name'); ?>" /></a> -->
<a href="<?php echo esc_url(home_url('/')); ?>"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/logo.png" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" /></a>

<?php get_search_form(); ?>

</header>

<?php
/*
if(is_category()) {
	$breakpoint = 0;
	$thiscat = get_term( get_query_var('cat') , 'category' );
	$subcategories = get_terms( 'category' , 'parent='.get_query_var('cat') );
	if(empty($subcategories) && $thiscat->parent != 0) {
		$subcategories = get_terms( 'category' , 'parent='.$thiscat->parent.'' );
	}
	$items='';
	if(!empty($subcategories)) {
		foreach($subcategories as $subcat) {
			if($thiscat->term_id == $subcat->term_id) $current = 'current-cat'; else $current = '';
			$items .= '
			<li class="sub-cat-item-'.$subcat->term_id.$current.'">
				<a href="'.get_category_link( $subcat->term_id ).'" title="'.$subcat->description.'">'.$subcat->name.'</a>
			</li>';
		}
		echo "<hr><div class='navigation'><ul>$items</ul></div>";
	}
	unset($subcategories,$subcat,$thiscat,$items);
}
else{} 
*/
?>