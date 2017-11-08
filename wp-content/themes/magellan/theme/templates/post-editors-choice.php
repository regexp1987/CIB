<?php


$post_image_width = magellan_get_post_image_width($post->ID);
if($post_image_width != 'full_screen' || !has_post_thumbnail()) {
	$class = 'big';
}
else {
	$class = '';
}

if(MagellanInstance()->is_editors_choice()) : ?>
	<a href="<?php the_permalink(); ?>" class="btn-editors-choice-2 <?php echo esc_attr($class); ?>">
		<div class="circle">
			<span class="crown"><i class="fa fa-ra"></i></span>
			<span class="ribbon"><?php esc_html_e('Editors choice', 'magellan'); ?></span>
			<span class="stars">
				<i class="fa fa-star"></i>
				<i class="fa fa-star"></i>
				<i class="fa fa-star"></i>
			</span>
		</div>
	</a>
<?php endif; ?>