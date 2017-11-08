<?php

if(class_exists('Magellan_VC_Block_Base') && function_exists('vc_map'))
{
	class Magellan_News_Ticker extends Magellan_VC_Block_Base {

		public $shortcode = 'news_ticker';
		public $classname = 'Magellan_News_Ticker';	//for 5.2 compatibility.
		
		/*
		 * Return parameters
		 */
		public function getParams() {
					
			return array(
				'name'				=> esc_html__('Trending news ticker', 'magellan'),
				'description'		=> esc_html__('Popular post title slider', 'magellan'),
				'base'				=> 'news_ticker',
				"content_element"	=> true,
				'class'				=> '',
				'category'			=> esc_html__('Magellan', 'magellan'),
				'params'			=> array(
					array(
						"type" => "textfield",
						"holder" => "div",
						"class" => "",
						"heading" => esc_html__("Title", 'magellan'),
						"param_name" => "title",
						"value" => esc_html__("Trending", 'magellan'),
						"description" => esc_html__("The title for post block", 'magellan')
				   ),
				   array(
						"type" => "textfield",
						"holder" => "div",
						"class" => "",
						"heading" => esc_html__("Count", 'magellan'),
						"param_name" => "count",
						"value" => 5,
						"description" => esc_html__("How many posts should be shown", 'magellan')
				   ),
				),
			);	
		}
		
		/*
		 * Shortcode content
		 */
		public static function shortcode($atts = array(), $content = '') {
			
			ob_start();
			global $post;

			extract( shortcode_atts( array(
				'title' => esc_html__('Trending', 'magellan'),
				'count' => 5
			), $atts ) );
			
			$args = array();
			$unique_id = uniqid();
			
			$pop_items = MagellanInstance()->get_popular_posts('weekly', $count);
	
			$ids = array();
			if(!empty($pop_items))
			{
				foreach($pop_items as $item)
				{
					$ids[] = $item->postid;
				}
			}
			
			$args['post__in'] = $ids;
			
			$items = magellan_get_post_collection($args, 10);
			
			if(!empty($items)) 
			{
			?>
			<div class="post-block trending-posts trending-posts-main-content">
				<div class="row">
					<div class="col-md-12">
						<div id="trending-posts-<?php echo esc_attr($unique_id); ?>" class="carousel slide" data-ride="carousel" data-interval="false">
							<div class="title-default">
								<span><?php echo esc_html($title); ?></span>
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
							<div class="controls right">
								<a href="#trending-posts-<?php echo esc_attr($unique_id); ?>" data-slide="prev" class="btn btn-default"><i class="fa fa-caret-left"></i></a>
								<a href="#trending-posts-<?php echo esc_attr($unique_id); ?>" data-slide="next" class="btn btn-default"><i class="fa fa-caret-right"></i></a>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<?php
			}

			$return = ob_get_contents();
			ob_end_clean();
			wp_reset_postdata();
			return $return;
		
		}

	}
	
	//Create instance of VC block
	new Magellan_News_Ticker();
	
}