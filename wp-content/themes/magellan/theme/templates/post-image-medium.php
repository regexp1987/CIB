<?php
	global $post;
	$thumb = magellan_get_thumbnail('magellan_post_single_medium', true, false);
	
	if($thumb) : ?>
	<div class="container page-title post-page-title-medium post-block">
		<div class="container">
			<?php echo '<img src="' . $thumb . '" alt="' . get_the_title() . '">'; ?>
		</div>
	</div>

	<?php endif; ?>