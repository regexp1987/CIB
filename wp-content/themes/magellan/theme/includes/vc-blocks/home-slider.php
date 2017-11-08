<?php

if(class_exists('Magellan_VC_Block_Base') && function_exists('vc_map'))
{
	class Magellan_Home_Slider extends Magellan_VC_Block_Base {

		public $shortcode = 'home_slider';
		public $classname = 'Magellan_Home_Slider';	//for 5.2 compatibility.
		
		/*
		 * Return parameters
		 */
		public function getParams() {
			
			return array(
				'name'				=> esc_html__('Home slider', 'magellan'),
				'description'		=> esc_html__('Display large home slider for posts with optional sidebar', 'magellan'),
				'base'				=> 'home_slider',
				"as_parent"			=> array('only' => 'home_slider_item'),
				"content_element"	=> true,
				"show_settings_on_create" => false,
				"js_view"			=> 'VcColumnView',
				'class'				=> '',
				'category'			=> esc_html__('Magellan', 'magellan'),
				'params'			=> array(
					array(
						 "type" => "checkbox",
						 "holder" => "div",
						 "class" => "",
						 "heading" => esc_html__("Show post switcher sidebar", 'magellan'),
						 "param_name" => "show_sidebar",
						 "value" => 1,
						 "description" => esc_html__("Display tabbed switcher with popular, latest and recently commented posts", 'magellan')
					),
					array(
						"type" => "textfield",
						"holder" => "div",
						"class" => "",
						"heading" => esc_html__("Interval", 'magellan'),
						"param_name" => "interval",
						"value" => 0,
						"description" => esc_html__("The amount of miliseconds to delay between advancing the slider. Entering 0 will disable auto advance.", 'magellan')
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
				'show_sidebar' => '',
				'interval' => 0
			), $atts ) );

			if($interval == 0 || !is_numeric($interval)) { $interval = 'false'; }

			$slide_count = substr_count($content, 'home_slider_item');
			$unique_id = uniqid();
			
			$content = preg_replace('/home_slider_item/', 'home_slider_item first="1" ', $content, 1);
			
			if($slide_count > 0) : ?>
			
			<div class="container-fluid magellan-slider-wrapper">
				<div class="magellan-slider">

                    <div id="magellan-slider-<?php echo esc_attr($unique_id); ?>" class="carousel slide carousel-fade" data-ride="carousel" data-interval="<?php echo esc_attr($interval); ?>">

						<?php if($slide_count > 1) : ?>
						<div class="controls left">
							<a href="#magellan-slider-<?php echo esc_attr($unique_id); ?>" data-slide="prev" class="btn btn-default"><i class="fa fa-caret-left"></i></a>
							<a href="#magellan-slider-<?php echo esc_attr($unique_id); ?>" data-slide="next" class="btn btn-default"><i class="fa fa-caret-right"></i></a>
							<ol class="carousel-indicators">
								<?php for($i = 0; $i < $slide_count; $i++) { ?>
								<li data-target="#magellan-slider-<?php echo esc_attr($unique_id); ?>" data-slide-to="<?php echo esc_attr($i); ?>" <?php if($i == 0) { echo 'class="active"'; } ?>></li>
								<?php } ?>
							</ol>
						</div>
						<?php endif; ?>

						<div class="carousel-inner">
							<?php  echo do_shortcode($content); ?>
						</div>

					</div>

					<?php if($show_sidebar) : ?>
					<div class="container sidebar">
						<?php 
							the_widget('MagellanPostTabs',  array('count' => 8));
						?>
					</div>
					<?php endif; ?>

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
	new Magellan_Home_Slider();
	
	if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
		class WPBakeryShortCode_Home_Slider extends WPBakeryShortCodesContainer { }
	}
}