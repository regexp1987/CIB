<?php
	$image = magellan_get_thumbnail('magellan_post_list_item_medium', true, false);
?>


<div class="row">
	<div class="col-md-12">
		<div class="post-featured <?php  MagellanInstance()->get_image_gradient_class(); ?>" <?php if($image) { ?>style="background-image: url(<?php echo esc_url($image); ?>);" <?php } ?>>
			<div class="overlay-wrapper">
				<div class="title">

					<?php get_template_part('theme/templates/post-categories-compact'); ?>

					<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

					<?php get_template_part('theme/templates/title-legend'); ?>

					<a href="<?php the_permalink(); ?>" class="btn btn-default btn-dark"><?php esc_html_e('Read more', 'magellan'); ?></a>

				</div>
				<div class="overlay <?php  MagellanInstance()->get_image_gradient_class(); ?>" <?php if($image) { ?>style="background-image: url(<?php echo esc_url($image); ?>);"<?php } ?>></div>
			</div>
		</div>
	</div>
</div>
