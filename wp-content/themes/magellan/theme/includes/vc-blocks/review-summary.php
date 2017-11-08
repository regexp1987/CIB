<?php

if(class_exists('Magellan_VC_Block_Base') && function_exists('vc_map'))
{
	class Magellan_Review_Summary extends Magellan_VC_Block_Base {

		public $shortcode = 'review_summary';
		public $classname = 'Magellan_Review_Summary';	//for 5.2 compatibility.
		
		/*
		 * Return parameters
		 */
		public function getParams() {
			
			return array(
				"name" => esc_html__("Review Summary", 'magellan'),
				"base" => "review_summary",
				"class" => "",
				"category" => esc_html__('Magellan Post Blocks', 'magellan'),
				"params" => array(
					array(
						 "type" => "textarea",
						 "holder" => "div",
						 "class" => "",
						 "heading" => esc_html__("Rating", 'magellan'),
						 "param_name" => "content",
						 "value" => "",
						 "description" => esc_html__('List of Ratings. Enter them in format like this: [rating title="Design" value="4"] This will result in "Design" being rated with 4 out of 5', 'magellan')
					),
					array(
						 "type" => "checkbox",
						 "holder" => "div",
						 "class" => "",
						 "heading" => esc_html__("Enable reader ratings?", 'magellan'),
						 "param_name" => "reader_ratings",
						 "value" => 1,
						 "description" => esc_html__("Allow your visitors to rate the product", 'magellan')
					),
					array(
						"type" => "textfield",
						"holder" => "div",
						"class" => "",
						"heading" => esc_html__("Heading for positive feature list", 'magellan'),
						"param_name" => "positive_heading",
						"value" => esc_html__("What's good", 'magellan'),
				   ),
					array(
						 "type" => "textarea",
						 "holder" => "div",
						 "class" => "",
						 "heading" => esc_html__("Positives", 'magellan'),
						 "param_name" => "positives",
						 "value" => "",
						 "description" => esc_html__("List of positives. Enter each item in new row.", 'magellan')
					),
					array(
						"type" => "textfield",
						"holder" => "div",
						"class" => "",
						"heading" => esc_html__("Negative for positive feature list", 'magellan'),
						"param_name" => "negative_heading",
						"value" => esc_html__("What's bad", 'magellan'),
				   ),
					array(
						 "type" => "textarea",
						 "holder" => "div",
						 "class" => "",
						 "heading" => esc_html__("Negatives", 'magellan'),
						 "param_name" => "negatives",
						 "value" => "",
						 "description" => esc_html__("List of negatives. Enter each item in new row.", 'magellan')
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
				'positive_heading' => esc_html__("What's good", 'magellan'),
				'negative_heading' => esc_html__("What's bad", 'magellan'),
				'reader_ratings'	=> false,
				'positives' => '',
				'negatives' => '',
				'tags' => '',
			), $atts ) );
			
			$src_parts = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'magellan_post_list_item_large' );
			if(empty($src_parts[0]))
			{
				$thumb = '';
			}
			else
			{
				$thumb = $src_parts[0];
			}
			?>

			<div class="overview review-summary">
				<?php 
					$content = strip_tags($content);
					echo do_shortcode($content); 
				?>
				
				<?php if($reader_ratings) : ?>
					<div class="row user reader-reviews" data-post_id="<?php echo esc_attr(get_the_ID()); ?>" data-nonce="<?php echo wp_create_nonce('post_reader_rate' . get_the_ID()); ?>" data-msg_your="<?php esc_html_e('You rated:', 'magellan'); ?>" data-msg_saved="<?php esc_html_e('Rating saved', 'magellan'); ?>">
						<?php
							$reader_rating_avg = get_post_meta($post->ID, 'reader_rating_avg', true);
							
							if(empty($reader_rating_avg))
							{
								$reader_rating_avg = 0;
								$stars = 0;
							}
							else
							{
								$stars = ($reader_rating_avg/10);
								$stars = round(($stars+5/2)/5)*5; //round to nearest 5
								$reader_rating_avg = round(($reader_rating_avg+5/2)/5)*5; //round to nearest 5
							}	
						?>
						<div class="overview-title"><h3><?php esc_html_e('Reader rating', 'magellan'); ?></h3><i class="stars rating s-<?php echo esc_attr(str_replace('.', '-' , $stars)); ?>"></i></div>
						<span class="bar">
							<s data-value="<?php echo esc_attr($reader_rating_avg * 2); ?>">
								<span class="grip">
									<span class="tooltip"><?php esc_html_e('Drag to rate', 'magellan'); ?></span>
								</span>
							</s>
						</span>
						<div class="bg" style="background-image: url(<?php echo esc_url($thumb); ?>);"></div>
					</div>
				<?php endif; ?>
				
				<div class="row good-bad">
					<div class="good col-md-6 col-sm-6">
						<div class="overview-title"><h3><?php echo esc_html($positive_heading); ?></h3></div>
						
						<?php
						$positives = explode("\n", $positives);
						if(!empty($positives) && strlen(trim($positives[0])) > 0) : ?>

							<?php
								echo '<ul>';
								foreach($positives as $item)
								{
									echo '<li>' . $item . '</li>';
								}
								echo '</ul>';
							?>

						<?php endif; ?>
						
					</div>
					
					<div class="bad col-md-6 col-sm-6">
						<div class="overview-title"><h3><?php echo esc_html($negative_heading); ?></h3></div>
						
						<?php
						$negatives = explode("\n", $negatives);
						if(!empty($negatives) && strlen(trim($negatives[0])) > 0) : ?>
						
							<?php
								echo '<ul>';
								foreach($negatives as $item)
								{
									echo '<li>' . $item . '</li>';
								}
								echo '</ul>';
							?>
						
						<?php endif; ?>
						
					</div>
				</div>
			</div>

			<?php
			$return = ob_get_contents();
			ob_end_clean();
			wp_reset_postdata();
			return $return;
		}

	}

	//Create instance of VC block
	new Magellan_Review_Summary();
		
}