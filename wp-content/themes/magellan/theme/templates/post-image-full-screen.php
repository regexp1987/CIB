<?php
	global $post;
	$thumb = magellan_get_thumbnail('magellan_post_single_full_screen', true, false);
	
	if($thumb) : ?>

	<div class="container-fluid page-title post-page-title post-block">
		<div class="container-fluid">
			<div class="featured-post-content post-featured <?php  MagellanInstance()->get_image_gradient_class(); ?>" style="background-image: url(<?php echo esc_url($thumb); ?>)">
				<div class="overlay-wrapper">
					
					<?php get_template_part('theme/templates/post-editors-choice'); ?>
					
					<div class="title">
						
						<?php get_template_part('theme/templates/post-categories'); ?>
						
						<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						
						<?php get_template_part('theme/templates/title-legend'); ?>
						
						<div class="intro">
							<p><?php echo magellan_excerpt(); ?></p>
						</div>
					</div>
					<div class="overlay <?php  MagellanInstance()->get_image_gradient_class(); ?>" style="background-image: url(<?php echo esc_url($thumb); ?>)"></div>
				</div>
			</div>
		</div>
	</div>

	<?php endif; ?>