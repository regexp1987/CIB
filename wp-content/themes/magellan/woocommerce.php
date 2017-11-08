<?php get_header(); ?>

<div class="container-fluid page-title">
	<div class="container">
		<h1><a href="<?php the_permalink(); ?>"><?php woocommerce_page_title(); ?></a></h1>
	</div>
</div>	

	
<?php
    $class = 'full-width';
    $has_sidebar = false;
    if(
        ((is_shop() || is_product_category()) && magellan_gs('show_shop_sidebar') == 'on')
        ||
        (is_product() && magellan_gs('show_product_sidebar') == 'on')
    )
    {
        $class = '';
        $has_sidebar = true;
    }
?>

<div class="container main-content-wrapper post-main-wrapper <?php if($has_sidebar) { echo 'sidebar-' . magellan_get_sidebar_position(); } else { echo 'sidebar-disabled'; } ?>">

	<div <?php post_class('main-content hentry'); ?>>
	
		<?php woocommerce_content(); ?>
		
	</div>
	
	<?php
    if($has_sidebar)
    {
        get_sidebar();
    }
    ?> 
	
</div>
  
<?php get_footer(); ?>