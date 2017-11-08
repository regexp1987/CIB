<?php

if(class_exists('Magellan_VC_Block_Base') && function_exists('vc_map'))
{
	class Magellan_Title_block extends Magellan_VC_Block_Base {

		public $shortcode = 'title_block';
		public $classname = 'Magellan_Title_block';	//for 5.2 compatibility.
		
		/*
		 * Return parameters
		 */
		public function getParams() {
			
			return array(
				"name" => esc_html__("Title block", 'magellan'),
				"base" => "title_block",
				"class" => "",
				"category" => esc_html__('Magellan', 'magellan'),
				"params" => array(
					array(
						"type" => "textfield",
						"holder" => "div",
						"class" => "",
						"heading" => esc_html__("Title", 'magellan'),
						"param_name" => "title",
						"value" => esc_html__("Latest news", 'magellan'),
				   ),
				)   
			);
		}
		
		/*
		 * Shortcode content
		 */
		public static function shortcode($atts = array(), $content = '') {
			
			ob_start();
			global $post;

			extract( shortcode_atts( array(
				'title' => ''
			), $atts ) );
			
			?>
				<div class="title-default">
					<span><?php echo esc_html($title); ?></span>
				</div>
			<?php

			$return = ob_get_contents();
			ob_end_clean();
			wp_reset_postdata();
			return $return;
		}
		
		
	}

	//Create instance of VC block
	new Magellan_Title_block();
	
}