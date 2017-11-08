<?php

if(class_exists('Magellan_VC_Block_Base') && function_exists('vc_map'))
{
	class Magellan_Photo_Gallery extends Magellan_VC_Block_Base {

		public $shortcode = 'photo_galleries';
		public $classname = 'Magellan_Photo_Gallery';	//for 5.2 compatibility.
		
		/*
		 * Return parameters
		 */
		public function getParams() {
					
			return array(
				'name'				=> esc_html__('Latest photo galleries', 'magellan'),
				'description'		=> esc_html__('List of photo galleries', 'magellan'),
				'base'				=> 'photo_galleries',
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
						"description" => esc_html__("The title for gallery block", 'magellan')
				   ),
				   array(
						"type" => "textfield",
						"holder" => "div",
						"class" => "",
						"heading" => esc_html__("Count", 'magellan'),
						"param_name" => "count",
						"value" => 3,
						"description" => esc_html__("How many galleries should be shown", 'magellan')
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
				'title' => esc_html__('Latest Photo Galleries', 'magellan'),
				'count' => 3,
				'columns' => 3,
			), $atts ) );
			
			$unique_id = uniqid();
			
			//set bootstrap column size n/12
			$bs_collumn = 4;
			if($columns == 4)
			{
				$bs_collumn = 3;
			}
			
			$items = magellan_get_post_collection(array(), $count, 1, 'date', 'DESC', 'gallery');
			
			if(!empty($items))
			{ 
				?>
				<div class="post-block post-gallery">
					
						<div class="row">
							<div class="col-md-12">
								<div class="title-default">
									<span><?php echo esc_html($title); ?></span>
									<a href="<?php echo esc_url(get_post_type_archive_link('gallery')); ?>" class="more"><?php esc_html_e('View all', 'magellan'); ?></a>
								</div>

								<div class="galleries">
									
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
													 <?php get_template_part('theme/templates/loop-gallery-list-item'); ?>
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
				<?php
				
			}
			
			$return = ob_get_contents();
			ob_end_clean();
			wp_reset_postdata();
			return $return;
		
		}

	}
	
	if(class_exists('Magellan_Photo_Gallery'))
	{
		//Create instance of VC block
		new Magellan_Photo_Gallery();
	}
}