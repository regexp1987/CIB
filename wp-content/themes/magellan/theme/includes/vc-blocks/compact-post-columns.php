<?php

if(class_exists('Magellan_VC_Block_Base') && function_exists('vc_map'))
{
	class Magellan_Compact_Post_Columns extends Magellan_VC_Block_Base {

		public $shortcode = 'compact_post_columns';
		public $classname = 'Magellan_Compact_Post_Columns';	//for 5.2 compatibility.
		
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
				'name'				=> esc_html__('Compact post columns', 'magellan'),
				'description'		=> esc_html__('Columns with post titles and small thumbnails ', 'magellan'),
				'base'				=> 'compact_post_columns',
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
						"value" => 9,
						"description" => esc_html__("How many posts should be shown", 'magellan')
					),
					array(
						"type" => "dropdown",
						"holder" => "div",
						"class" => "",
						"heading" => esc_html__("Columns", 'magellan'),
						"param_name" => "columns",
						"value" => array(3 => 3, 4 => 4),
						"description" => esc_html__("How many columns per row", 'magellan')
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
				'columns' => 3,
				'category' => NULL,
			), $atts ) );
			
			$unique_id = uniqid();
			

			/* Post List Query */
			$params = array(
				'category_name' => $category,
			);			
			$items = magellan_get_post_collection($params, $count, 1);
			
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
			
			//set bootstrap column size n/12
			$bs_collumn = 4;
			if($columns == 4)
			{
				$bs_collumn = 3;
			}
			
			
			if(!empty($items)) :
			?>	
				
				<div class="post-block post-columns post-columns-small">
					<div class="row">
						<div class="col-md-12">

							<div class="title-default">
								<span><?php echo esc_html($title); ?></span>
								<a href="<?php echo esc_url($view_all); ?>" class="more"><?php esc_html_e('View all', 'magellan'); ?></a>
							</div>
														
							<div class="post-block post-image-60">
								<?php
									$chunks = array_chunk($items, $columns);
									if(!empty($chunks))
									{	
									
										foreach($chunks as $chunk) 
										{
										?>
										
										<div class="row">
											<?php
											foreach($chunk as $post) 
											{	
												setup_postdata($post);
												?>
												<div class="col-md-<?php echo esc_attr($bs_collumn); ?> col-sm-<?php echo esc_attr($bs_collumn); ?> col-xs-12">
													<?php get_template_part('theme/templates/post-list-item-small'); ?>
												</div>
												<?php
											} ?>
				
										</div>

										<?php }
									
									}
								?>
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
	new Magellan_Compact_Post_Columns();
	
}