<?php

    $tags = get_the_tags(get_the_ID());
    if(!empty($tags))
    {
        ?> 
		<div class="widget-tags">
			<div class="tags">
			<?php
			foreach($tags as $tag)
			{
				$tag = get_tag($tag);
				$link = get_tag_link($tag);
				echo ' <a href="' . esc_url( $link ) . '" title="' . esc_attr($tag->name) . '">' . $tag->name . '<span>' . $tag->count . '</span></a>';
			}
			?>
			</div>
		</div> 
	<?php
    }
?>