<?php

if(class_exists('Magellan_VC_Block_Base') && function_exists('vc_map'))
{
	class Magellan_Post_List_With_Heading extends Magellan_VC_Block_Base {

		public $shortcode = 'post_list_with_heading';
		public $classname = 'Magellan_Post_List_With_Heading';	//for 5.2 compatibility.
		
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
				'name'				=> esc_html__('Post list with Heading', 'magellan'),
				'description'		=> esc_html__('Large featured post followed by list of smaller posts', 'magellan'),
				'base'				=> 'post_list_with_heading',
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
						"value" => 8,
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
			
			/* Featured Post Query */
			$params = array(
				'category_name' => $category,
				'meta_key' => 'is_featured',
				'meta_value' => 'on'
			);

			$skip_id = array();
			$featured = magellan_get_post_collection($params, 1, 1);
			if(!empty($featured))   //if featured post found, reduce the overal count
			{
				$featured = $featured[0];
				$count--;
				$skip_id[] = $featured->ID;
			}

			/* Post List Query */
			$params = array(
				'category_name' => $category,
				'post__not_in' => $skip_id
			);			
			$items = magellan_get_post_collection($params, $count, 1);

			//get link
			if(!empty($category))
			{
				$cat = get_category_by_slug($category);
				if(!empty($cat))
				{
					$view_all = get_category_link($cat->cat_ID);
				}
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
			
			//if featured not found, take the first from items
			if(empty($featured) && !empty($items))
			{
				$featured = array_shift($items);
			}
			
			if(!empty($featured) || !empty($items)) :
			?>	
				<div class="post-block post-list-with-heading">
					
					<div class="title-default">
						<span><?php echo esc_html($title); ?></span>
						<a href="<?php echo esc_url($view_all); ?>" class="more"><?php esc_html_e('View all', 'magellan'); ?></a>
					</div>

					<?php
					if(!empty($featured))
					{
						$post = $featured;
						setup_postdata($post);
						echo '<div class="post-block heading-block">';
						get_template_part('theme/templates/featured-medium-post');
						echo '</div>';
					}
					?>

					<?php if(!empty($items)) : ?>

							<?php foreach($items as $post) : ?>

								<div class="row">
									<div class="col-md-12">
										<?php 
										setup_postdata($post);
										get_template_part('theme/templates/post-list-item-medium-with-overlay');
										?>
									</div>
								</div>

							<?php endforeach; ?>

					<?php endif; ?>

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
	new Magellan_Post_List_With_Heading();
	
}