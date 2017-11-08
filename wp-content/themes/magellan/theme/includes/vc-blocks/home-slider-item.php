<?php

if(class_exists('Magellan_VC_Block_Base') && function_exists('vc_map'))
{
	class Magellan_Home_Slider_Item extends Magellan_VC_Block_Base {

		public $shortcode = 'home_slider_item';
		public $classname = 'Magellan_Home_Slider_Item';	//for 5.2 compatibility.
		
		/*
		 * Return parameters
		 */
		public function getParams() {
			
			return array(
				'name'				=> esc_html__('Home slider item', 'magellan'),
				'description'		=> esc_html__('Item for home slider', 'magellan'),
				'base'				=> 'home_slider_item',
				"as_child"			=> array('only' => 'home_slider'),
				"content_element"	=> true,
				'class'				=> '',
				'category'			=> esc_html__('Magellan', 'magellan'),
				'params'			=> array(
					
					array(
						"type" => "textfield",
						"holder" => "div",
						"class" => "",
						"heading" => esc_html__("Post ID", 'magellan'),
						"param_name" => "slider_post_id",
						"value" => '',
						"description" => esc_html__("The ID of the post you wish to display in this slide", 'magellan')
				    ),
					array(
						"type" => "attach_image",
						"holder" => "div",
						"class" => "",
						"heading" => esc_html__("Slider image", 'magellan'),
						"param_name" => "image",
						"value" => '',
						"description" => esc_html__("Recommended size - 1980x500px", 'magellan')
					),
					
					/*
					array(
						'type' => 'autocomplete',
						'heading' => esc_html__( 'Post to display in slide', 'js_composer' ),
						'param_name' => 'slide_post',
						'description' => esc_html__( 'Find post by title.', 'js_composer' ),
						'settings' => array(
							'multiple' => false,
							'sortable' => true,
							'groups' => true,
						),
						'dependency' => array(
							'element' => 'post_type',
							'value' => array( 'ids' ),
						),
					),
					 * 
					 */
					
					
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
				'slider_post_id' => 1,
				'image'	=> '',
				'first' => 0,	//set automatically
			), $atts ) );
			
			if(is_numeric($image))
			{
				$src_parts = wp_get_attachment_image_src($image, 'magellan_post_single_full_screen');
				if(!empty($src_parts))
				{
					$image = $src_parts[0];
				}
				else 
				{
					$image = '';
				}
			}
			
			$post = get_post($slider_post_id);
			setup_postdata($post);
			
			if($post) : ?>

			<div class="slide item <?php  MagellanInstance()->get_image_gradient_class(); ?> <?php if($first == 1) { echo ' active'; } ?>" style="background-image: url(<?php echo esc_url($image); ?>);">
				<div class="container">
					<div class="overlay-wrapper">
						<div class="title-wrapper">
							<div class="title">
								
								<?php get_template_part('theme/templates/post-categories-compact-dropdown'); ?>
								
								<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
								
								<?php get_template_part('theme/templates/title-legend'); ?>
								
								<div class="intro">
									<?php echo wpautop(magellan_excerpt(100)); ?>
								</div>
								
								<a href="<?php the_permalink(); ?>" class="btn btn-default"><?php esc_html_e('Read more', 'magellan'); ?></a>
								
							</div>
						</div>
						<div class="overlay <?php  MagellanInstance()->get_image_gradient_class(); ?>" style="background-image: url(<?php echo esc_url($image); ?>);"></div>
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
	new Magellan_Home_Slider_Item();
		
	if ( class_exists( 'WPBakeryShortCode' ) ) {
		class WPBakeryShortCode_Home_Slider_Item extends WPBakeryShortCode { }
	}
}