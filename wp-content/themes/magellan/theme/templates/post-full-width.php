<?php
//setup post in case its not there
if(!in_the_loop())
{
    if ( have_posts() ) 
    {
        the_post();
    }
}
?>
<?php
	
	$post_image_width = magellan_get_post_image_width($post->ID);
?>

<?php
	if($post_image_width == 'container_width')
	{
		get_template_part( 'theme/templates/post-image-medium');
	}
	elseif($post_image_width == 'full_screen')
	{
		get_template_part( 'theme/templates/post-image-full-screen');
	}
	elseif($post_image_width == 'video_autoplay' || $post_image_width == 'video')
	{
		get_template_part( 'theme/templates/post-image-video');
	}
?>

<?php
if($post_image_width == 'full_screen')
{
	$affix_wrapper_open = '<div class="container-fluid parallax-wrapper" data-spy="affix">';
	$affix_wrapper_close = '</div>';
	$outer_wrapper_class = 'container main-content-wrapper hentry post-main-wrapper featured sidebar-disabled';
	$inner_wrapper_class = 'main-content hentry';
}
else
{
	$outer_wrapper_class = 'container main-content-wrapper post-main-wrapper sidebar-disabled';
	$inner_wrapper_class = 'main-content hentry';
	$affix_wrapper_open = '';
	$affix_wrapper_close = '';
}
?>

<?php echo magellan_kses_wrapper_html($affix_wrapper_open); ?>

<div class="<?php echo esc_attr($outer_wrapper_class); ?>">
    
    <div <?php post_class($inner_wrapper_class); ?>>

		<?php
			if($post_image_width == 'text_width')
			{
				get_template_part( 'theme/templates/post-image-small');
			}
		?>
		
		<div class="row">
			<div class="col-md-12 post-block <?php if(MagellanInstance()->is_editors_choice()) { echo 'editors-choice'; } ?>">
				
				<?php if($post_image_width != 'full_screen' || !has_post_thumbnail()) : ?>
				
					<div class="post-title">

						<?php get_template_part('theme/templates/post-categories'); ?>

						<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

						<?php get_template_part('theme/templates/title-legend'); ?>

						<?php get_template_part('theme/templates/post-editors-choice'); ?>

					</div>
				
				<?php endif; ?>
				
				
				<?php get_template_part('theme/templates/post-controls'); ?>

				<div class="the-content-container"><?php the_content(); ?></div>
				
				<?php get_template_part( 'theme/templates/link-pages'); ?>
				
				<?php get_template_part('theme/templates/post-tags'); ?>
				
			</div>
		</div>

		<?php get_template_part('theme/templates/post-previous-next'); ?>

		<?php get_template_part('theme/templates/post-author'); ?>

		<?php comments_template( '', true ); ?>
		
    </div>
    
</div>

<?php echo magellan_kses_wrapper_html($affix_wrapper_close); ?>

<?php echo $banner = magellan_get_banner_by_location('post_ad', 'banner-footer'); ?>