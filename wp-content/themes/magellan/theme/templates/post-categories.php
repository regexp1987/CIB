<?php
    $cats = wp_get_post_categories(get_the_ID());

    if(!empty($cats))
    {
        echo '<div class="tags"><div>';
		
		foreach($cats as $cat )
        {
            $category = get_category($cat);
            $link = get_category_link($category);
            echo '<div><a href="' . esc_url( $link ) . '" title="' . esc_attr($category->name) . '" class="tag-default">' . $category->name . '</a></div>';
        }
		
		echo '</div>';
		
		
		MagellanInstance()->get_rating_stars('div');
		
		echo '</div>';
    }
?>