<?php
	$thumb = magellan_get_thumbnail('magellan_post_list_item_medium', true, false);
    $class = '';
?>

<div class="post">
	<div class="title">
		<h3><a href="<?php the_permalink(); ?>"><span><?php the_title(); ?></span></a></h3>
	</div>
	<div class="overlay-wrapper <?php  MagellanInstance()->get_image_gradient_class(); ?>">
		<div class="content">
			<div>
				<a href="<?php the_permalink(); ?>" class="link"></a>
			</div>
		</div>
		<?php
		if($thumb)
		{
			?><div class="overlay" style="background-image: url(<?php echo esc_url($thumb); ?>);"></div><?php
		}
		?>
	</div>
	<?php
	if($thumb)
    {
		?><div class="image <?php  MagellanInstance()->get_image_gradient_class(); ?>" style="background-image: url(<?php echo esc_url($thumb); ?>);"></div><?php
	}
	?>
	
</div>