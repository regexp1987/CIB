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
			
			if(count($cats) > 2)	//more dropdown mode
			{
				$cat = array_pop($cats);
				$category = get_category($cat);
				$link = get_category_link($category);
				echo '<div><a href="' . esc_url( $link ) . '" title="' . esc_attr($category->name) . '" class="tag-default">' . $category->name . '</a></div>';
				
				?>
				<div>
					<a href="#" class="show-more"><i class="fa fa-plus-square"></i></a>
					<div class="more-dropdown" data-post_id="<?php echo esc_attr(get_the_ID()); ?>">
						<?php
						foreach($cats as $cat )
						{
							$category = get_category($cat);
							$link = get_category_link($category);
							echo '<div><a href="' . esc_url( $link ) . '" title="' . esc_attr($category->name) . '" class="tag-default">' . $category->name . '</a></div>';
						}
						?>						
					</div>
				</div>
				
				<?php
			}
			else	//regular mode
			{
				foreach($cats as $cat )
				{
					$category = get_category($cat);
					$link = get_category_link($category);
					echo '<div><a href="' . esc_url( $link ) . '" title="' . esc_attr($category->name) . '" class="tag-default">' . $category->name . '</a></div>';
				}
			}
			
		}
		
		echo '</div>';
		
		MagellanInstance()->get_rating_stars('div');
		
		echo '</div>';
    }
?>