<?php
	
	$cats = wp_get_post_categories(get_the_ID());
	
    if(!empty($cats) || MagellanInstance()->post_is_featured())
    {
        echo '<div class="tags"><div>';
		
		if(MagellanInstance()->post_is_featured())
		{
			?><div class="trending"><a href="#"><?php esc_html_e('Featured', 'magellan'); ?></a></div><?php
		}
		
		if(!empty($cats))
		{
			$cat = array_pop($cats);
			$category = get_category($cat);
			$link = get_category_link($category);
			echo '<div><a href="' . esc_url( $link ) . '" title="' . esc_attr($category->name) . '" class="tag-default">' . $category->name . '</a></div>';
		}
			
		echo '</div>';
		
		MagellanInstance()->get_rating_stars('div', 'rating-stars');
		
		echo '</div>';
    }
?>