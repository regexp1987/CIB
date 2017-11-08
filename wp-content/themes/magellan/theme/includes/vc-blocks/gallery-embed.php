<?php

if(class_exists('Magellan_VC_Block_Base') && function_exists('vc_map'))
{
	class Magellan_Gallery_Embed extends Magellan_VC_Block_Base {

		public $shortcode = 'gallery_embed';
		public $classname = 'Magellan_Gallery_Embed';	//for 5.2 compatibility.
		
		/*
		 * Return parameters
		 */
		public function getParams() {
			
			//galleries
			$galleries_data = magellan_get_post_collection(array(), 999, 1, 'date', 'DESC', 'gallery');
			$galleries = array();

			foreach($galleries_data as $gallery)
			{
				$galleries[$gallery->post_title] = $gallery->ID;
			}
			
			return array(
				"name" => esc_html__("Gallery embed", 'magellan'),
				"base" => "gallery_embed",
				"class" => "",
				"category" => esc_html__('Magellan Post Blocks', 'magellan'),
				"params" => array(
					array(
						"type" => "dropdown",
						"holder" => "div",
						"class" => "",
						"heading" => esc_html__("Gallery", 'magellan'),
						"param_name" => "gallery",
						"value" => $galleries,
						"description" => esc_html__("Select which gallery to embed in the post", 'magellan')
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
			$original_post_object = $post;  //store post object

			extract( shortcode_atts( array(
				'gallery' => '',
			), $atts ) );
			?>
			<?php

			$post = get_post($gallery);
			if(!empty($post))
			{
				setup_postdata($post);

				if(class_exists('Attachments'))
				{
					$attachments = new Attachments( 'magellan_galleries' );
					if( $attachments->exist() )
					{
						$attachment = $attachments->get_single(0);
						$attachment = $attachments->get();	//move the pointer
						$large_obj = wp_get_attachment_image_src($attachment->id, 'magellan_gallery_embed_item');

						?>
						<div class="widget-default widget-gallery">
							<div class="row post-block">
								<div class="col-md-6 col-sm-6">
									<div class="post-featured" <?php if(!empty($large_obj)) { ?>style="background-image: url(<?php echo esc_url($large_obj[0]); ?>);" <?php } ?>>
										<div class="overlay-wrapper">
											<div class="title">
												<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
												<?php get_template_part('theme/templates/title-legend'); ?>
												<a href="<?php the_permalink(); ?>" class="btn btn-default btn-dark"><?php esc_html_e('View all photos', 'magellan') ?></a>
											</div>
											<div class="overlay" <?php if(!empty($large_obj)) { ?>style="background-image: url(<?php echo esc_url($large_obj[0]); ?>);" <?php } ?>></div>
										</div>
									</div>
								</div>
								<div class="col-md-6 col-sm-6">
									
									<?php for($j = 0; $j < 2; $j++) : ?>
									
										<div class="row">
											<?php
											for($i = 0; $i < 2; $i++) 
											{ 
												$attachment = $attachments->get();
												if($attachment)
												{
													?>
													<div class="col-md-6 col-sm-6">
														<a href="<?php the_permalink(); ?>"><?php echo magellan_kses_widget_html_field($attachments->image('magellan_gallery_embed_item')); ?></a>
													</div>
													<?php
												}
											}
											?>
										</div>
									
									<?php endfor; ?>
									
								</div>
							</div>
						</div>
						<?php
					}
				}
			}

			$post = $original_post_object; //restore the original post

			$return = ob_get_contents();
			ob_end_clean();
			wp_reset_postdata();
			return $return;
			
		}

	}

	if(class_exists('Magellan_Gallery_Embed'))
	{
		//Create instance of VC block
		new Magellan_Gallery_Embed();
	}	
}