<?php
	global $post;
	$thumb = magellan_get_thumbnail('magellan_post_single_small', true, false);
	
	if($thumb) : ?>
	<div class="row">
		<div class="col-md-12 post-block post-page-title-small">
			<?php echo '<img src="' . $thumb . '" alt="' . get_the_title() . '">'; ?>
		</div>
	</div>
	<?php endif; ?>