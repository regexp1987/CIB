<?php


if(MagellanInstance()->is_editors_choice()) : ?>
<a href="<?php the_permalink(); ?>" class="btn-editors-choice-2">
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