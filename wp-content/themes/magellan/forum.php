<?php get_header(); ?>
<?php wp_reset_postdata(); ?>

<div class="container-fluid page-title">
	<div class="container">
		<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
	</div>
</div>

<div class="container main-content-wrapper post-main-wrapper sidebar-<?php echo magellan_get_sidebar_position(); ?>">
    
    <div <?php post_class('main-content hentry'); ?>>
		
		<?php the_content(); ?>
		
	</div>
	
	<?php get_sidebar('forum'); ?>
	
</div>

<?php get_footer(); ?>