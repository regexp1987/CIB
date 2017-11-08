<?php

if(class_exists('Magellan_VC_Block_Base') && function_exists('vc_map'))
{
	class Magellan_Exclusive_Post extends Magellan_VC_Block_Base {

		public $shortcode = 'exclusive_post';
		public $classname = 'Magellan_Exclusive_Post';	//for 5.2 compatibility.
		
		/*
		 * Return parameters
		 */
		public function getParams() {
			
			return array(
				'name'				=> esc_html__('Exclusive post', 'magellan'),
				'description'		=> esc_html__('Embed a specially styled singe post block', 'magellan'),
				'base'				=> 'exclusive_post',
				"content_element"	=> true,
				'class'				=> '',
				'category'			=> esc_html__('Magellan', 'magellan'),
				'params'			=> array(
					
					array(
						"type" => "textfield",
						"holder" => "div",
						"class" => "",
						"heading" => esc_html__("Post ID", 'magellan'),
						"param_name" => "post_id",
						"value" => '',
						"description" => esc_html__("The ID of the post you wish to display", 'magellan')
				    ),
					array(
						"type" => "dropdown",
						"holder" => "div",
						"class" => "",
						"heading" => esc_html__("Image position", 'magellan'),
						"param_name" => "image_position",
						"value" => array('Left' => 'left', 'Right' => 'right'),
						"description" => esc_html__("Which side should the image be on", 'magellan')
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
				'post_id' => 1,
				'image'	=> '',
				'first' => 0,	//set automatically
				'image_position' => 'left'
			), $atts ) );
			
			$post = get_post($post_id);
			setup_postdata($post);
			
			if($post) : 
				
				$thumb = magellan_get_thumbnail('magellan_post_list_item_medium', true, false);
				?>

				<div class="post-block post-exclusive image-<?php echo esc_attr($image_position); ?>">
					<div class="col-md-12">
						<div class="post">
							
							<?php if($thumb) : ?>
								<div class="image <?php  MagellanInstance()->get_image_gradient_class(); ?>" style="background-image: url(<?php echo esc_url($thumb); ?>);"></div>
							<?php endif; ?>
							
							<div class="text">
								<div class="title">
									
									<?php get_template_part('theme/templates/post-categories-compact-dropdown'); ?>
									
									<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?> <?php if( MagellanInstance()->is_post_hot(get_the_ID())) { ?><span class="hot"><?php esc_html_e('Hot', 'magellan'); ?></span><?php } ?></a></h3>
									<?php get_template_part('theme/templates/title-legend'); ?>
								
									<a href="<?php the_permalink(); ?>" class="btn btn-default btn-dark"><?php esc_html_e('Read more', 'magellan'); ?></a>
								</div>
							</div>
							
							<div class="overlay <?php  MagellanInstance()->get_image_gradient_class(); ?>" style="background-image: url(<?php echo esc_url($thumb); ?>);"></div>
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
	new Magellan_Exclusive_Post();
		
}