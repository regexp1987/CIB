<?php
/*
Template Name: Page with sidebar
*/
?>
<?php get_header(); ?>

<div class="container-fluid page-title">
	<div class="container">
		<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
	</div>
</div>	


<div class="container main-content-wrapper post-main-wrapper sidebar-<?php echo magellan_get_sidebar_position(); ?>">
    
    <div <?php post_class('main-content hentry'); ?>>
		
		<?php get_template_part( 'theme/templates/post-image-small'); ?>

		<div class="row">
			<div class="col-md-12 post-block">
								
				<div class="the-content-container"><?php the_content(); ?></div>
				
				<?php get_template_part( 'theme/templates/link-pages'); ?>
				
			</div>
		</div>
		
		<?php
		if(magellan_not_woocommerce_special_content())
		{ 
			comments_template( '', true ); 	
		}
		?>

    </div>
	
	<?php get_sidebar(); ?>
    
</div>

<?php get_footer(); ?>