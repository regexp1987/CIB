<?php

if(class_exists('Magellan_VC_Block_Base') && function_exists('vc_map'))
{
	class Magellan_Post_Slider_Large extends Magellan_VC_Block_Base {

		public $shortcode = 'post_slider_large';
		public $classname = 'Magellan_Post_Slider_Large';	//for 5.2 compatibility.
		
		/*
		 * Return parameters
		 */
		public function getParams() {
					
			//get categories
			$post_categories = get_terms('category');
			$post_cats = array('' => '');
			foreach($post_categories as $pc)
			{
				$post_cats[$pc->name] = $pc->slug;
			}
			
			return array(
				'name'				=> esc_html__('Large post slider', 'magellan'),
				'description'		=> esc_html__('Large slider with 2 columns', 'magellan'),
				'base'				=> 'post_slider_large',
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
						"value" => esc_html__("Latest news", 'magellan'),
						"description" => esc_html__("The title for post block", 'magellan')
				   ),
				   array(
						"type" => "textfield",
						"holder" => "div",
						"class" => "",
						"heading" => esc_html__("Count", 'magellan'),
						"param_name" => "count",
						"value" => 6,
						"description" => esc_html__("How many posts should be shown", 'magellan')
				   ),
				   array(
						"type" => "dropdown",
						"holder" => "div",
						"class" => "",
						"heading" => esc_html__("Post category", 'magellan'),
						"param_name" => "category",
						"value" => $post_cats,
						"description" => esc_html__("List posts from specific category", 'magellan')
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
				'title' => esc_html__('Latest news', 'magellan'),
				'count' => 6,
				'category' => NULL,
			), $atts ) );
			
			$unique_id = uniqid();
			
			/* Post List Query */
			$params = array(
				'category_name' => $category,
			);			
			$items = magellan_get_post_collection($params, $count, 1);
			$chunks = array_chunk($items, 2);
			
			//get link
			if(!empty($category))
			{
				$cat = get_category_by_slug($category);
				$view_all = get_category_link($cat->cat_ID);
			}
			else
			{
				if(get_option('show_on_front') == 'page')
				{
					$view_all = get_permalink( get_option( 'page_for_posts' ) );
				}
				else
				{
					$view_all = get_home_url();
				}
			}
			
			
			if(!empty($items)) : ?>	
				

			<div class="post-block editors-choice-slider">
				<div class="row">
					<div class="col-md-12">
						<div class="title-default">
							<span><?php echo esc_html($title); ?></span>
						</div>
						<div id="slider-<?php echo esc_attr($unique_id); ?>" class="carousel slide" data-ride="carousel" data-interval="false">
														
							<?php if(!empty($chunks) && count($chunks) > 1) : ?>
								<div class="controls right">
									<ol class="carousel-indicators">
										<?php for($i = 0; $i < count($chunks); $i++) { ?>

											<li data-target="#slider-<?php echo esc_attr($unique_id); ?>" data-slide-to="<?php echo esc_attr($i); ?>" <?php if($i == 0) { echo 'class="active"'; } ?>></li>

										<?php } ?>
									</ol>

									<a href="#slider-<?php echo esc_attr($unique_id); ?>" data-slide="prev" class="btn btn-default"><i class="fa fa-caret-left"></i></a>
									<a href="#slider-<?php echo esc_attr($unique_id); ?>" data-slide="next" class="btn btn-default"><i class="fa fa-caret-right"></i></a>
								</div>
							<?php endif; ?>
							
							<div class="carousel-inner">
								<?php
								if(!empty($items))
								{ 
								
									foreach($chunks as $key => $chunk)
									{

										if(!empty($chunk))
										{
											?>
												<div class="slide item <?php if($key == 0) { echo ' active'; } ?>">
													<div class="row">
													
														<?php
														foreach($chunk as $post)
														{
															?><div class="col-md-6 col-sm-6"><?php

																setup_postdata($post);
																get_template_part('theme/templates/post-slider-item-large');

															?></div><?php
														}
														?>
														
													</div>
												</div>
											<?php
										}

									}
								}
								?>								
							</div>
							
						</div>
					</div>
				</div>
			</div>


			<?php endif; ?>
			
			<?php
			$return = ob_get_contents();
			ob_end_clean();
			wp_reset_postdata();
			return $return;
		
		}

	}
	
	//Create instance of VC block
	new Magellan_Post_Slider_Large();
	
}