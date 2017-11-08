<?php


$args = array();
$category_link = '#';
	
$source = magellan_gs('trending_source');
if($source == 'popular')
{
	$pop_items = MagellanInstance()->get_popular_posts('weekly');
	
	$ids = array();
	if(!empty($pop_items))
	{
		foreach($pop_items as $item)
		{
			$ids[] = $item->postid;
		}
	}
	
	$args['post__in'] = $ids;
}
else
{
	$category = magellan_gs('trending_category');
	
	if($category)
	{
		$args['cat'] = $category;
		$category_link = get_category_link($category);
	}
}

$items = magellan_get_post_collection($args, 10);

if(!empty($items)) 
{
?>
	<div class="trending-posts">
		<div class="tags">
			<div class="trending"><a href="<?php echo esc_url($category_link); ?>"><?php echo esc_html(magellan_gs('trending_title')); ?></a></div>
		</div>
		<div id="trending-posts" class="carousel slide" data-ride="carousel" data-interval="false">
			<div class="controls">
				<a href="#trending-posts" data-slide="next"><i class="fa fa-caret-left"></i></a>
				<a href="#trending-posts" data-slide="prev"><i class="fa fa-caret-right"></i></a>
			</div>
			<div class="carousel-inner">
				<?php
					$first = true;
					foreach($items as $post_item)
					{
						?>
						<div class="item<?php if($first) { echo ' active'; $first = false; } ?>">
							<div class="post-item">
								<div class="title">
									<h3><a href="<?php echo get_permalink($post_item->ID); ?>"><?php echo get_the_title($post_item->ID); ?></a></h3>
									<div class="legend">
										<a href="<?php echo get_permalink($post_item->ID); ?>" class="time"><?php echo get_the_date('', $post_item->ID); ?></a>
										<a href="<?php echo get_comments_link($post_item->ID); ?>" class="comments"><?php echo esc_attr($post_item->comment_count); ?></a>
									</div>
								</div>
							</div>
						</div>
						<?php
					}
				?>
			</div>
		</div>
	</div>
<?php
}
wp_reset_postdata();
?>