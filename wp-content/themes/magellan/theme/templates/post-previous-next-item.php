<?php $image = magellan_get_thumbnail('magellan_post_list_item_small', true, false); ?>

<div class="post">
	
	<?php if($image) : ?>
		<div class="overlay-wrapper text-overlay" data-click-url="<?php the_permalink(); ?>">
			<div class="content">
				<div>
					<div class="tags">
					<?php
						//retrieve post category from transient cache
						$post_cats = get_transient('magellan_post_legend_cat_cache');
						if(empty($post_cats) || empty($post_cats[get_the_ID()]))
						{
							$cats = wp_get_post_categories(get_the_ID());   //get all current post cats
							if(!empty($cats))
							{
								$cat = $cats[0];
								$category = get_category($cat);
								$link = get_category_link($category);
								$current_post_cat = array('name' => $category->name, 'link' => $link);

								//init as empty array
								if(empty($post_cats))
								{
									$post_cats = array();
								}

								$post_cats[get_the_ID()] = $current_post_cat;        
								set_transient('magellan_post_legend_cat_cache', $post_cats, 60*60); //1 hour cache
							}
						}

						if(!empty($post_cats[get_the_ID()]))
						{
							$post_cat = $post_cats[get_the_ID()];
							echo '<div><a href="' . esc_url( $post_cat['link'] ) . '" title="' . esc_attr($post_cat['name']) . '">' . $post_cat['name'] . '</a></div>';
						}
					?>
					</div>

					<a href="<?php the_permalink(); ?>" class="btn btn-default btn-dark"><?php esc_html_e('Read more', 'magellan'); ?></a>	

				</div>
			</div>
			<?php
			if($image)
			{
				?><div class="overlay" style="background-image: url(<?php echo esc_url($image); ?>);"></div><?php
			}
			?>
		</div>
	<?php endif; ?>
	
	<?php if($image) : ?>
		<div class="image">
			<a href="<?php the_permalink(); ?>"><img src="<?php echo esc_url($image); ?>" alt="<?php the_title(); ?>"></a>
		</div>
	<?php endif; ?>
	
	<div class="title">
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
	</div>
</div>