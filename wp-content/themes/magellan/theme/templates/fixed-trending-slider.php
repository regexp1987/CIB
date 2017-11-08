<?php


$args = array();
$category_link = '#';
	
$source = magellan_gs('trending_slider_source');
if($source == 'popular')
{
	$pop_items = MagellanInstance()->get_popular_posts('weekly', 15);
	
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
	$category = magellan_gs('trending_slider_category');
	
	if($category)
	{
		$args['cat'] = $category;
		$category_link = get_category_link($category);
	}
}

$items = magellan_get_post_collection($args, 15);

if(!empty($items)) :
	
	$chunks = array_chunk($items, 5);
?>
<div class="container-fluid trending-slider">
	<div id="trending-slider" class="carousel slide" data-ride="carousel" data-interval="false">
		<div class="controls right">
			<a href="#" class="close-trending-slider"><i class="fa fa-times"></i></a>
			<div>
				<p><?php echo esc_html(magellan_gs('trending_slider_title')); ?></p>
				<div class="buttons">
					<a href="#trending-slider" data-slide="prev" class="btn btn-default"><i class="fa fa-caret-left"></i></a>
					<a href="#trending-slider" data-slide="next" class="btn btn-default"><i class="fa fa-caret-right"></i></a>
				</div>
			</div>
		</div>
		<div class="carousel-inner">
			
			<?php
			foreach($chunks as $key => $chunk)
			{
				if(!empty($chunk))
				{
					?>
			
					<div class="slide item <?php if($key == 0) { echo ' active'; } ?>">
						<div class="post-block post-video post-trending">
							<div class="row">
								<?php
									foreach($chunk as $post)
									{
										setup_postdata($post);
										get_template_part('theme/templates/fixed-trending-slider-item');
									}
								?>
							</div>
						</div>
					</div>
					
					<?php
				}
			}
			?>
			
		</div>	
	</div>
</div>
<?php endif; ?>