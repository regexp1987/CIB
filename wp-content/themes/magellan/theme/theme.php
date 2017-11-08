<?php

if(!class_exists('Magellan'))
{
	class Magellan {

        protected static $_instance = null;
        
        /* Return instance of Class */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }
        
		function __construct() {

			//set instance variable
			$this::$_instance = $this;

			/* Theme Specific Actions */
			add_action( 'after_setup_theme', array($this, 'setup' ));
			add_action( 'after_setup_theme', array($this, 'init_less' ));
			add_action( 'after_setup_theme', array($this, 'add_vc_blocks' ));
			add_action( 'wp_enqueue_scripts', array($this, 'add_stylesheets' ), 11 );
			add_action( 'wp_enqueue_scripts', array($this, 'add_scripts' ));
            add_action('wp_enqueue_scripts', array($this, 'restrict_loading_third_party_scripts'), 999);
			add_action( 'admin_enqueue_scripts', array($this, 'widget_upload_enqueue' ));
			add_action( 'wp_default_scripts', array($this, 'print_scripts_in_footer' ));
			add_action( 'parse_query', array($this, 'wpse_71157_parse_query' ));  //fix query parse bug
			add_action( 'widgets_init', array($this, 'add_widgets' ));
			add_action( 'add_meta_boxes', array($this, 'post_meta_boxes' ));
			add_action( 'save_post', array($this, 'post_save_postdata' ));
			
			add_action( 'wp_ajax_weather_widget', array($this, 'weather_widget' ), 10, 0);
			add_action( 'wp_ajax_nopriv_weather_widget', array($this, 'weather_widget' ), 10, 0);
			add_action( 'wp_ajax_post_like', array($this, 'post_like' ));
			add_action( 'wp_ajax_nopriv_post_like', array($this, 'post_like' ));
			add_action( 'wp_ajax_post_reader_rate', array($this, 'post_reader_rate' ));
			add_action( 'wp_ajax_nopriv_post_reader_rate', array($this, 'post_reader_rate' ));
			
			add_action( 'tgmpa_register', array($this, 'register_required_plugins' ));
			
			add_action( 'show_user_profile', array($this, 'extra_user_profile_fields'));
			add_action( 'edit_user_profile', array($this, 'extra_user_profile_fields'));
			add_action( 'personal_options_update', array($this, 'save_extra_user_profile_fields'));
			add_action( 'edit_user_profile_update', array($this, 'save_extra_user_profile_fields'));
						

			/* Framework Actions */
			add_action( 'customize_register', 'magellan_customize_register' );
			add_action( 'wp_head', 'magellan_output_theme_version' );
			add_filter( 'less_vars', 'magellan_map_visual_settings_to_less', 10, 2 );

			/* Filters */
			add_filter( 'wp_title', array($this, 'wp_title_for_home') , 10, 2 );
			add_filter( 'excerpt_length', array($this, 'custom_excerpt_length'), 999 );
			add_filter( 'excerpt_more', array($this, 'custom_excerpt_more'), 999 );
			add_filter( 'img_caption_shortcode', array($this, 'fix_image_margins'), 10, 3);
			add_filter( 'constellation_sidebar_args', array($this, 'setup_constellation_sidebar'));
			//add_filter( 'widget_title', array($this, 'widget_title_force'));
			add_filter( 'mega_menu_prepend_item', array($this, 'mega_menu_prepend_item'));
			add_filter( 'mega_menu_append_item', array($this, 'mega_menu_append_item'));
			add_filter( 'dynamic_sidebar_params',array($this, 'footer_custom_params'));
			add_filter( 'comment_form_fields', array($this, 'wpb_move_comment_field_to_bottom' ));
            
            /* Global - add body tag classes for thank you and add quoute pages */
			add_filter( 'body_class', array($this, 'theme_body_classes'));
			
			/* WooCommerce filters & actions */
			add_filter( 'woocommerce_output_related_products_args', array($this, 'woo_related_products_args'));
			add_filter( 'woocommerce_cross_sells_total', function() { return 4; });
			add_filter( 'woocommerce_show_page_title', function() { return false; });
			
			remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' ); //move cross sells lower
			add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display' );
			
            
			/* Post Category slider AJAX call */
			add_action( 'wp_ajax_load_post_category_slider_items', array($this, 'load_post_category_slider_items') );
			add_action( 'wp_ajax_nopriv_load_post_category_slider_items', array($this, 'load_post_category_slider_items') );
			
			if(function_exists('vc_map'))
			{
				add_filter( 'vc_autocomplete_home_slider_item_slide_post_callback', 'vc_include_field_search', 10, 1);
			}
		}

		
		/*
		 * Init main theme features, declare what's supported
		 */
		function setup() 
		{
			/* Make theme available for translation.
			 * Translations can be added to the /languages/ directory.
			 */
			load_theme_textdomain( 'magellan', get_template_directory() . '/languages' );
			if(is_child_theme())
			{
				load_child_theme_textdomain( 'magellan', get_stylesheet_directory() . '/languages' );
			}

			// This theme styles the visual editor with editor-style.css to match the theme style.
			add_theme_support( 'woocommerce' );
			add_theme_support( 'automatic-feed-links' );
			add_post_type_support( 'page', 'excerpt' );
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'title-tag' );
			add_theme_support( 'custom-background' );
			
			register_nav_menu( 'primary-menu', esc_html__( 'Primary Menu', 'magellan' ) );

			magellan_add_image_sizes();

			if(!magellan_is_woocommerce_active())
			{
				$this->remove_woocommerce_settings();
			}

			$sidebars = magellan_get_sidebars();
			if(!empty($sidebars))
			{
				foreach($sidebars as $sidebar)
				{
                    register_sidebar( $sidebar );
				}
			}
			
		}

		
		/*
		 *  Init LESS stylsheets
		 */
		function init_less() 
		{
			$upload_dir = wp_upload_dir();
			if(magellan_gs('enable_css_mode') == 'off' && (is_writable($upload_dir['basedir']) && class_exists( 'wp_less' ) && function_exists('file_get_contents') && function_exists('file_put_contents')))
			{
				add_action( 'init', array( 'wp_less', 'instance' ) );
				update_option('magellan_use_less', 1);
			}
			else
			{
				update_option('magellan_use_less', 0);
			}
		}

		/*
		 * Include stylsheets
		 */
		function add_stylesheets() 
		{
			wp_enqueue_style( 'magellan-bootstrap', MAGELLAN_CSS_URL . 'bootstrap.min.css' );
			wp_enqueue_style( 'magellan-vendor', MAGELLAN_CSS_URL . 'vendor.css' );

			if(get_option('magellan_use_less') == 1)
			{
				wp_enqueue_style( 'magellan-main-less', MAGELLAN_LESS_URL . 'magellan.less'); 
			}
			else
			{
				wp_enqueue_style( 'magellan-main-css', MAGELLAN_CSS_URL . 'magellan.css' );
			}

			wp_enqueue_style( 'magellan-style', get_bloginfo( 'stylesheet_url' ) );   
			wp_enqueue_style( 'magellan-google-fonts', magellan_google_fonts_url(), array(), null );

			//don't use Constellation stylesheet
			wp_dequeue_style('cm-frontend');
            
            
            /* Add inline styles */
            
            //customizer settings
            ob_start();
            include get_template_directory() . '/theme/includes/' . 'customizer-settings.php';
            $customizer = ob_get_contents();
			ob_end_clean();
            
            wp_add_inline_style('magellan-style', $customizer); //customizer

            wp_add_inline_style('magellan-style', stripslashes(magellan_gs('custom_css'))); //user css
		}

		/*
		 * Include scripts
		 */
		function add_scripts() 
		{
			wp_enqueue_script( 'magellan-modernizr', MAGELLAN_JS_URL . 'vendor/modernizr.min.js');
			wp_enqueue_script( 'magellan-bootstrap', MAGELLAN_JS_URL . 'vendor/bootstrap.min.js', array( 'jquery' ), false, true);
			
			if(is_single() || is_page())
			{
				wp_enqueue_script( 'jquery-ui-core' );
                wp_enqueue_script( 'jquery-ui-widget' );
				wp_enqueue_script( 'jquery-effects-slide');
				wp_enqueue_script( 'jquery-ui-draggable' );
                wp_enqueue_script( 'jquery-ui-mouse' );
                wp_enqueue_script( 'magellan-touch', MAGELLAN_JS_URL . 'vendor/jquery.ui.touch-punch.min.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget' ), false, true);
			}
            
            wp_enqueue_script( 'magellan-inview', MAGELLAN_JS_URL . 'vendor/jquery.inview.js', array( 'jquery' ), false, true);
			
			wp_enqueue_script( 'magellan-cycle2', MAGELLAN_JS_URL . 'vendor/jquery.cycle2.min.js', array( 'jquery' ), false, true);
			wp_enqueue_script( 'magellan-jquery-mobile', MAGELLAN_JS_URL . 'vendor/jquery.mobile.custom.min.js', array( 'jquery', 'magellan-bootstrap' ), false, true);
			wp_enqueue_script( 'magellan-social-button', MAGELLAN_JS_URL . 'vendor/share-button.min.js', array( 'jquery' ), false, true);
			wp_enqueue_script( 'magellan-particles', MAGELLAN_JS_URL . 'vendor/jquery.particleground.min.js', array( 'jquery' ), false, true);
			wp_enqueue_script( 'magellan-nicescroll', MAGELLAN_JS_URL . 'vendor/jquery.nicescroll.min.js', array( 'jquery' ), false, true);
			wp_enqueue_script( 'magellan-mega-menu', MAGELLAN_JS_URL . 'planetshine-mega-menu.js', array( 'jquery', 'magellan-jquery-mobile' ), false, true);
            wp_enqueue_script( 'magellan-theme', MAGELLAN_JS_URL . 'theme.js', array( 'jquery', 'magellan-social-button', 'magellan-inview', 'magellan-particles' ), false, true);

			$ajax_object = array();
			$ajax_object['ajaxurl'] = admin_url( 'admin-ajax.php' );
			$ajax_object['enable_sidebar_affix'] = magellan_gs('enable_sidebar_affix');

			if(function_exists('icl_get_languages'))
			{
				$ajax_object['lang'] = ICL_LANGUAGE_CODE;
			}

			$ajax_object['particle_color'] = get_theme_mod('particle_color', magellan_gs('particle_color'));

			wp_localize_script( 'magellan-theme', 'magellan_js_params', $ajax_object );
            
            //Add inline scripts
            wp_add_inline_script('magellan-theme', stripslashes(magellan_gs('custom_js')));
		}

        /*
         * Don't load woocommerce, bbpress and buddypress scripts on pages where they are not needed
         */
        function restrict_loading_third_party_scripts()
        {
                        
            //Only load CSS and JS on Woocommerce pages
            if(function_exists('is_woocommerce'))
            {
                if(! is_woocommerce() && ! is_cart() && ! is_checkout() ) 
                {
                    //Dequeue scripts
                    wp_dequeue_script('woocommerce'); 
                    wp_dequeue_script('wc-add-to-cart'); 
                    wp_dequeue_script('wc-cart-fragments');
                    wp_dequeue_script('wc-add-to-cart');
                }
            }
            

           	//bbpress scripts
            if ( class_exists('bbPress') ) 
            {
                if ( ! is_bbpress() ) 
                {
                    //Dequeue styles
                    wp_dequeue_style('bbp-default');
                    wp_dequeue_style( 'bbp_private_replies_style');
                    
                    //Dequeue scripts
                    wp_dequeue_script('bbpress-editor');
                }
            }
            
            
            //buddypress
            if(function_exists('is_buddypress'))
            {
                if(!is_buddypress())
                {
                    wp_dequeue_style('bp-admin-bar');
                    wp_dequeue_style('bp-legacy-css');
              
                    //here was code that removed buddypress code when its not needed
                }
            }
            
            
            //wordpress popular posts
            wp_dequeue_style('wordpress-popular-posts');
            
        }
        
		/*
		 * Initialize widgets
		 */
		function add_widgets()
		{
			require_once( get_template_directory() . '/theme/includes/widgets/' . 'magellan-post-tabs.php' );
			require_once( get_template_directory() . '/theme/includes/widgets/' . 'magellan-social-share.php' );
			require_once( get_template_directory() . '/theme/includes/widgets/' . 'magellan-post-list-with-heading.php' );
			require_once( get_template_directory() . '/theme/includes/widgets/' . 'magellan-newsletter-social.php' );
			require_once( get_template_directory() . '/theme/includes/widgets/' . 'magellan-recent-post-list.php' );
			require_once( get_template_directory() . '/theme/includes/widgets/' . 'magellan-post-carousel.php' );
			require_once( get_template_directory() . '/theme/includes/widgets/' . 'magellan-tag-cloud.php' );
			require_once( get_template_directory() . '/theme/includes/widgets/' . 'magellan-banner-large.php' );
			require_once( get_template_directory() . '/theme/includes/widgets/' . 'magellan-about.php' );
			require_once( get_template_directory() . '/theme/includes/widgets/' . 'magellan-category-scroller.php' );
			require_once( get_template_directory() . '/theme/includes/widgets/' . 'magellan-tweets-widget.php' );
			require_once( get_template_directory() . '/theme/includes/widgets/' . 'magellan-tweets-embed.php' );
			require_once( get_template_directory() . '/theme/includes/widgets/' . 'magellan-dropdown-featured-post-list.php' );
			require_once( get_template_directory() . '/theme/includes/widgets/' . 'magellan-dropdown-category-posts.php' );
			require_once( get_template_directory() . '/theme/includes/widgets/' . 'magellan-dropdown-post-list-with-heading.php' );
			require_once( get_template_directory() . '/theme/includes/widgets/' . 'magellan-dropdown-post-list-with-sidemenu.php' );
			require_once( get_template_directory() . '/theme/includes/widgets/' . 'magellan-dropdown-latest-videos.php' );
			
			register_widget( 'MagellanPostTabs' );
			register_widget( 'MagellanSocialShare' );
			register_widget( 'MagellanPostListWithHeading' );
			register_widget( 'MagellanRecentPostList' );
			register_widget( 'MagellanPostCarousel' );
			register_widget( 'MagellanSocialNewsletter' );
			register_widget( 'MagellanTagCloud' );
			register_widget( 'MagellanBannerLarge' );
			register_widget( 'MagellanAbout' );
			register_widget( 'MagellanCategoryScroller' );
			register_widget( 'MagellanTweets' );
			register_widget( 'MagellanTweetsEmbed' );
			register_widget( 'MagellanDropdownFeaturedPostList' );
			register_widget( 'MagellanDropdownCategoryPosts' );
			register_widget( 'MagellanDropdownPostListWithHeading' );
			register_widget( 'MagellanDropdownPostListWithSidemenu' );
			register_widget( 'MagellanDropdownLatestVideos' );

		}

		/*
		 * Initialize VC blocks
		 */
		function add_vc_blocks()
		{
			//if VC exists
			if(function_exists('vc_map'))
			{
				//register vc search post type				
				require_once( get_template_directory() . '/theme/includes/vc-blocks/' . 'home-slider.php');
				require_once( get_template_directory() . '/theme/includes/vc-blocks/' . 'home-slider-item.php');
				require_once( get_template_directory() . '/theme/includes/vc-blocks/' . 'post-category-slider.php');
				require_once( get_template_directory() . '/theme/includes/vc-blocks/' . 'news-block-with-scroll-list.php');
				require_once( get_template_directory() . '/theme/includes/vc-blocks/' . 'post-list-with-heading.php');
				require_once( get_template_directory() . '/theme/includes/vc-blocks/' . 'post-slider-large.php');
				require_once( get_template_directory() . '/theme/includes/vc-blocks/' . 'photo-galleries.php');
				require_once( get_template_directory() . '/theme/includes/vc-blocks/' . 'compact-post-columns.php');
				require_once( get_template_directory() . '/theme/includes/vc-blocks/' . 'post-list-large-items.php');
				require_once( get_template_directory() . '/theme/includes/vc-blocks/' . 'post-list-compact-items.php');
				require_once( get_template_directory() . '/theme/includes/vc-blocks/' . 'news-ticker.php');
				require_once( get_template_directory() . '/theme/includes/vc-blocks/' . 'exclusive-post.php');
				require_once( get_template_directory() . '/theme/includes/vc-blocks/' . 'title-block.php');
				require_once( get_template_directory() . '/theme/includes/vc-blocks/' . 'review-summary.php');
				require_once( get_template_directory() . '/theme/includes/vc-blocks/' . 'gallery-embed.php');
				
				require_once( get_template_directory() . '/theme/includes/vc-blocks/' . 'banner970.php');
				require_once( get_template_directory() . '/theme/includes/vc-blocks/' . 'banner728.php');
				require_once( get_template_directory() . '/theme/includes/vc-blocks/' . 'banner300.php');
			}
		}
		

		/*
		 * Add meta boxes
		 */
		function post_meta_boxes() 
		{
			add_meta_box(
				'post_settings',
				esc_html__('Post settings', 'magellan'),
				array($this, 'post_inner_meta_box'),
				'post'
			);

			global $post;
			// don't show page settings for shop
			if(magellan_is_woocommerce_active())
			{
				$shop_id = wc_get_page_id( 'shop' );
				if(wc_get_page_id( 'shop' ) == $post->ID) return;
			}

			//don't show page settings for blog & homepage
			if(get_option('show_on_front') == 'page')
			{
			//    if(get_option( 'page_on_front') == $post->ID) return;
				if(get_option( 'page_for_posts') == $post->ID) return;
			}

			add_meta_box(
				'page_settings',
				esc_html__('Page settings', 'magellan'),
				array($this, 'page_inner_meta_box'),
				'page'
			);

		}

		/*
		 * Handle meta box save
		 */
		function post_save_postdata( $post_id ) 
		{
			// Check if the current user is authorised to do this action. 
			if ( 'post' == magellan_get($_POST, 'post_type') || 'post' == magellan_get($_GET, 'post_type') || 'product' == magellan_get($_GET, 'post_type')) 
			{
				if ( ! current_user_can( 'edit_page', $post_id ) )
					  return;
			} 
			else
			{
				if ( ! current_user_can( 'edit_post', $post_id ) )
					  return;
			}

			//Nonce verfy
			if ( ! isset( $_POST['page_noncename'] ) || ! wp_verify_nonce( $_POST['page_noncename'], plugin_basename( __FILE__ ) ) )
				return;


			$post_ID = $_POST['post_ID'];
			$type = get_post_type($post_ID);
			if($type == 'post')
			{
				$post_style = trim(sanitize_text_field( $_POST['post_style'] ));
				$is_featured  = ( !empty($_POST['is_featured']) ? sanitize_text_field($_POST['is_featured']) : false);
				$image_size = trim(sanitize_text_field( $_POST['image_size'] ));
				$rating_stars = trim(sanitize_text_field( $_POST['rating_stars'] ));
				$is_editors_choice  = ( !empty($_POST['is_editors_choice']) ? sanitize_text_field($_POST['is_editors_choice']) : false);
				$video_url = trim(sanitize_text_field( $_POST['video_url'] ));

				update_post_meta($post_ID, 'post_style', $post_style);
				update_post_meta($post_ID, 'is_featured', $is_featured);
				update_post_meta($post_ID, 'image_size', $image_size);
				update_post_meta($post_ID, 'is_editors_choice', $is_editors_choice);
				update_post_meta($post_ID, 'rating_stars', $rating_stars);
				update_post_meta($post_ID, 'video_url', $video_url);
			}
			else if($type == 'page')
			{
				if(get_option( 'page_on_front') != $post_ID)
				{
					$show_share  = ( !empty($_POST['show_share']) ? sanitize_text_field($_POST['show_share']) : false);
					$custom_sidebar = trim(sanitize_text_field( $_POST['custom_sidebar'] ));

					update_post_meta($post_ID, 'show_share', $show_share);
					update_post_meta($post_ID, 'custom_sidebar', $custom_sidebar);
					
				}

				if(isset($_POST['page_rev_slider']))
				{
					$page_rev_slider = trim(sanitize_text_field( $_POST['page_rev_slider'] ));
					update_post_meta($post_ID, 'page_rev_slider', $page_rev_slider);
				}
				
			}

		}


		/*
		 * Post inner meta box
		 */
		function post_inner_meta_box( $post ) 
		{
			// Use nonce for verification
			wp_nonce_field( plugin_basename( __FILE__ ), 'page_noncename' );

			$is_featured = get_post_meta( $post->ID, $key = 'is_featured', $single = true );
			echo '<p>';
			echo '<input type="checkbox" id="is_featured" name="is_featured" ' . ($is_featured == true ? 'checked' : '')  . ' />';
			echo '<label for="is_featured">';
				 esc_html_e("This post is featured", 'magellan');
			echo '</label>';
			echo '</p>';
			
			$is_editors_choice = get_post_meta( $post->ID, $key = 'is_editors_choice', $single = true );
			echo '<p>';
			echo '<input type="checkbox" id="is_editors_choice" name="is_editors_choice" ' . ($is_editors_choice == true ? 'checked' : '')  . ' />';
			echo '<label for="is_editors_choice">';
				 esc_html_e("Show \"Editors Choice\" label", 'magellan');
			echo '</label>';
			echo '</p>';
						
			$post_style = get_post_meta( $post->ID, $key = 'post_style', $single = true );
			echo '<p>';
			echo '<label for="post_style">';
				 esc_html_e("Post style:", 'magellan');
			echo '</label><br/>';
			echo '<select id="post_style" name="post_style" value="' . esc_attr($post_style) . '"" style="min-width: 300px;">'
					. '<option value="global" ' . ($post_style == 'global' ? 'selected="selected"' : '')  . '>' . esc_html__('Global theme setting', 'magellan') . '</option>'
					. '<option value="sidebar"' . ($post_style == 'sidebar' ? 'selected="selected"' : '')  . '>' . esc_html__('With sidebar', 'magellan') . '</option>'
					. '<option value="no-sidebar"' . ($post_style == 'no-sidebar' ? 'selected="selected"' : '')  . '>' . esc_html__('Full width', 'magellan') . '</option>'
				. '</select>';
			echo '</p>';
			
			$image_size = get_post_meta( $post->ID, $key = 'image_size', $single = true );
			echo '<p>';
			echo '<label for="image_size">';
				 esc_html_e("Image mode:", 'magellan');
			echo '</label><br/>';
			echo '<select id="image_size" name="image_size" value="' . esc_attr($image_size) . '"" style="min-width: 300px;">'
					. '<option value="global" ' . ($image_size == 'global' ? 'selected="selected"' : '')  . '>' . esc_html__('Global theme setting', 'magellan') . '</option>'
					. '<option value="text_width"' . ($image_size == 'text_width' ? 'selected="selected"' : '')  . '>' . esc_html__('As wide as text', 'magellan') . '</option>'
					. '<option value="container_width"' . ($image_size == 'container_width' ? 'selected="selected"' : '')  . '>' . esc_html__('Site container width (requires sidebar)', 'magellan') . '</option>'
					. '<option value="full_screen"' . ($image_size == 'full_screen' ? 'selected="selected"' : '')  . '>' . esc_html__('Full screen (featured) ', 'magellan') . '</option>'
					. '<option value="no_image"' . ($image_size == 'no_image' ? 'selected="selected"' : '')  . '>' . esc_html__('No image', 'magellan') . '</option>'
					. '<option value="video"' . ($image_size == 'video' ? 'selected="selected"' : '')  . '>' . esc_html__('Video', 'magellan') . '</option>'
					. '<option value="video_autoplay"' . ($image_size == 'video_autoplay' ? 'selected="selected"' : '')  . '>' . esc_html__('Video with autoplay', 'magellan') . '</option>'
				. '</select>';
			echo '</p>';


			$video_url = get_post_meta( $post->ID, $key = 'video_url', $single = true );
			echo '<p>';
			echo '<label for="video_url">';
				esc_html_e("Video url (Optional. Used when image mode - video is set):", 'magellan');
			echo '</label><br/>';
			echo '<input type="text" id="video_url" name="video_url" value="' . $video_url . '" style="min-width: 300px;"/>';       
			echo '</p>';
			
			$rating_stars = get_post_meta( $post->ID, $key = 'rating_stars', $single = true );
			echo '<p>';
			echo '<label for="rating_stars">';
				 esc_html_e("Stars (for reviews):", 'magellan');
			echo '</label><br/>';
			echo '<select id="rating_stars" name="rating_stars" value="' . esc_attr($post_style) . '"" style="min-width: 300px;">'
					. '<option value="disabled" ' . ($rating_stars == 'disabled' ? 'selected="selected"' : '')  . '>Disabled</option>'
					. '<option value="0"' . ($rating_stars == '0' ? 'selected="selected"' : '')  . '>0</option>'
					. '<option value="5"' . ($rating_stars == '5' ? 'selected="selected"' : '')  . '>0.5</option>'
					. '<option value="10"' . ($rating_stars == '10' ? 'selected="selected"' : '')  . '>1</option>'
					. '<option value="15"' . ($rating_stars == '15' ? 'selected="selected"' : '')  . '>1.5</option>'
					. '<option value="20"' . ($rating_stars == '20' ? 'selected="selected"' : '')  . '>2</option>'
					. '<option value="25"' . ($rating_stars == '25' ? 'selected="selected"' : '')  . '>2.5</option>'
					. '<option value="30"' . ($rating_stars == '30' ? 'selected="selected"' : '')  . '>3</option>'
					. '<option value="35"' . ($rating_stars == '35' ? 'selected="selected"' : '')  . '>3.5</option>'
					. '<option value="40"' . ($rating_stars == '40' ? 'selected="selected"' : '')  . '>4</option>'
					. '<option value="45"' . ($rating_stars == '45' ? 'selected="selected"' : '')  . '>4.5</option>'
					. '<option value="50"' . ($rating_stars == '50' ? 'selected="selected"' : '')  . '>5</option>'
				. '</select>';
			echo '</p>';
			
			echo '<p>' . esc_html__('Post ID', 'magellan') . ': <strong>' . $post->ID . '</strong></p>';
		}

		/*
		 * Page inner meta box
		 */
		function page_inner_meta_box( $post ) 
		{
			// Use nonce for verification
			wp_nonce_field( plugin_basename( __FILE__ ), 'page_noncename' );

			if(get_option( 'page_on_front') != $post->ID)
			{
				$show_share = get_post_meta( $post->ID, $key = 'show_share', $single = true );
				echo '<p>';
				echo '<input type="checkbox" id="show_share" name="show_share" ' . ($show_share == true ? 'checked' : '')  . ' />';
				echo '<label for="show_share">';
					 esc_html_e("Show share icons", 'magellan');
				echo '</label>';
				echo '</p>';


				$page_sidebars =  magellan_get_sidebars();
				if(!empty($page_sidebars))
				{
					$custom_sidebar = get_post_meta( $post->ID, $key = 'custom_sidebar', $single = true );
					echo '<p>';
					echo '<label for="custom_sidebar">';
						 esc_html_e("Custom sidebar (for pages that have a sidebar):", 'magellan');
					echo '</label><br/>';
					echo '<select id="custom_sidebar" name="custom_sidebar" value="' . esc_attr($custom_sidebar) . '"" style="min-width: 300px;">';

					echo '<option value="global">' . esc_html__('Global theme setting', 'magellan') . '</option>';
					foreach($page_sidebars as $sidebar)
					{
						$selected = ($sidebar['id'] == $custom_sidebar ? 'selected="selected"' : '' );
						echo '<option value="' . $sidebar['id'] . '"' . $selected .'>' . $sidebar['name'] . '</option>';
					}

					echo '</select>';
					echo '</p>';
				}
			}

			if(function_exists('set_revslider_as_theme')) 
			{ 
				$page_rev_slider = get_post_meta( $post->ID, $key = 'page_rev_slider', $single = true );

				$revSlider = new RevSlider();
				$all_sliders = $revSlider->getAllSliderAliases();

				echo '<p>';
				echo '<label for="page_rev_slider">';
					esc_html_e("Full page Revolution Slider (Optional):", 'magellan');
				echo '</label><br/>';

				echo '<select id="page_rev_slider" name="page_rev_slider" value="' . esc_attr($page_rev_slider) . '"" style="min-width: 300px;">';

					echo '<option value="disabled">' . esc_html__('Disabled', 'magellan') . '</option>';
					if(!empty($all_sliders))
					{
						foreach($all_sliders as $slider)
						{
							$selected = ($slider == $page_rev_slider ? 'selected="selected"' : '' );
							echo '<option value="' . $slider . '"' . $selected .'>' . $slider . '</option>';
						}	
					}

				echo '</select>';

				echo '</p>';        
			}

		}

		/*
		 * Add extra fields to user profile in wp-admin. Mainly for socila profile urls
		 */		
		function extra_user_profile_fields($user)
		{
			?>
				<h3><?php esc_html_e('Additional user information', 'magellan'); ?></h3>

				<table class="form-table">

					<tr>
						<th><label for="position"><?php esc_html_e('Position', 'magellan'); ?></label></th>
						<td>
							<input type="text" name="position" id="position" value="<?php echo esc_attr( get_the_author_meta( 'position', $user->ID ) ); ?>" class="regular-text" /><br />
							<span class="description"><?php esc_html_e('Users position in this magazine. For example "Editor in chief" or "Food critic"', 'magellan'); ?></span>
						</td>
					</tr>

					<tr>
						<th><label for="twitter"><?php esc_html_e('Twitter account', 'magellan'); ?></label></th>
						<td>
							<input type="text" name="twitter" id="twitter" value="<?php echo esc_attr( get_the_author_meta( 'twitter', $user->ID ) ); ?>" class="regular-text" /><br />
							<span class="description"><?php esc_html_e('Twitter account URL', 'magellan'); ?></span>
						</td>
					</tr>

					<tr>
						<th><label for="facebook"><?php esc_html_e('Facebook account', 'magellan'); ?></label></th>
						<td>
							<input type="text" name="facebook" id="facebook" value="<?php echo esc_attr( get_the_author_meta( 'facebook', $user->ID ) ); ?>" class="regular-text" /><br />
							<span class="description"><?php esc_html_e('Facebook account URL', 'magellan'); ?></span>
						</td>
					</tr>

					<tr>
						<th><label for="youtube"><?php esc_html_e('Youtube account', 'magellan'); ?></label></th>
						<td>
							<input type="text" name="youtube" id="youtube" value="<?php echo esc_attr( get_the_author_meta( 'youtube', $user->ID ) ); ?>" class="regular-text" /><br />
							<span class="description"><?php esc_html_e('Youtube account URL', 'magellan'); ?></span>
						</td>
					</tr>

					<tr>
						<th><label for="gplus"><?php esc_html_e('Google+ account', 'magellan'); ?></label></th>
						<td>
							<input type="text" name="gplus" id="gplus" value="<?php echo esc_attr( get_the_author_meta( 'gplus', $user->ID ) ); ?>" class="regular-text" /><br />
							<span class="description"><?php esc_html_e('Google+ account URL', 'magellan'); ?></span>
						</td>
					</tr>

					<tr>
						<th><label for="pinterest"><?php esc_html_e('Pinterest account', 'magellan'); ?></label></th>
						<td>
							<input type="text" name="pinterest" id="pinterest" value="<?php echo esc_attr( get_the_author_meta( 'pinterest', $user->ID ) ); ?>" class="regular-text" /><br />
							<span class="description"><?php esc_html_e('Pinterest account URL', 'magellan'); ?></span>
						</td>
					</tr>
					
					<tr>
						<th><label for="instagram"><?php esc_html_e('Instagram account', 'magellan'); ?></label></th>
						<td>
							<input type="text" name="instagram" id="instagram" value="<?php echo esc_attr( get_the_author_meta( 'instagram', $user->ID ) ); ?>" class="regular-text" /><br />
							<span class="description"><?php esc_html_e('Instagram account URL', 'magellan'); ?></span>
						</td>
					</tr>

				</table>
			<?php
		}
		
		/*
		 * Save additional user field content
		 */
		function save_extra_user_profile_fields( $user_id ) {

			if ( !current_user_can( 'edit_user', $user_id ) )
			{
				return false;
			}

			update_user_meta( $user_id, 'position', $_POST['position'] );
			update_user_meta( $user_id, 'twitter', $_POST['twitter'] );
			update_user_meta( $user_id, 'facebook', $_POST['facebook'] );
			update_user_meta( $user_id, 'youtube', $_POST['youtube'] );
			update_user_meta( $user_id, 'gplus', $_POST['gplus'] );
			update_user_meta( $user_id, 'pinterest', $_POST['pinterest'] );
			update_user_meta( $user_id, 'instagram', $_POST['instagram'] );
		}
		

		/*
		 * Init Mega Menu sidebar 
		 */
		function setup_constellation_sidebar($args) {

			$args['before_widget'] = '<div id="%1$s" class="constellation-widget section %2$s">';
			$args['after_widget'] = '</div>';
			$args['before_title'] = '<h3><span>';
			$args['after_title'] = '</span></h3>';

			return $args;
		}

		/*
		 * Custom excerpt lenght
		 */
		function custom_excerpt_length( $length ) 
		{
			return 50;
		}

		/*
		 * Custom excerpt more
		 */
		function custom_excerpt_more( $more )
		{
			return '...';
		}


		/*
		* Description: removes the silly 10px margin from the new caption based images
		* Author: Justin Adie
		* Version: 0.1.0
		* Author URI: http://rathercurious.net
		*/
		function fix_image_margins($x=null, $attr, $content)
		{
			extract(shortcode_atts(array(
				'id'    => '',
				'align'    => 'alignnone',
				'width'    => '',
				'caption' => ''
			), $attr));

			if ( 1 > (int) $width || empty($caption) )
			{
				return $content;
			}

			if ( $id )
			{
				$id = 'id="' . $id . '" ';
			}

			return '<div ' . $id . 'class="wp-caption ' . $align . '" style="width: ' . ((int) $width + 0) . 'px">'
			. $content . '<p class="wp-caption-text">' . $caption . '</p></div>';
		}


		/*
		 * Adjust the title tag output in homepage
		 */
		function wp_title_for_home( $title, $sep ) {
			if ( is_feed() ) 
			{
				return $title;
			}

			global $page, $paged;

			// Add the blog name
			$title .= get_bloginfo( 'name', 'display' );

			// Add the blog description for the home/front page.
			$site_description = get_bloginfo( 'description', 'display' );
			if ( $site_description && ( is_home() || is_front_page() ) ) 
			{
				$title .= " $sep $site_description";
			}

			// Add a page number if necessary:
			if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) 
			{
				$title .= " $sep " . sprintf( esc_html__( 'Page %s', 'magellan' ), max( $paged, $page ) );
			}

			return $title;
		}

		/*
		 * Remove WooCommerce from theme settings
		 */
		function remove_woocommerce_settings()
		{
			global $_SETTINGS;
			if(!empty($_SETTINGS->admin_head['general']['children']['shop']))
			{
				unset($_SETTINGS->admin_head['general']['children']['shop']);
			}
			if(!empty($_SETTINGS->admin_body['general']['shop']))
			{
				unset($_SETTINGS->admin_body['general']['shop']);
			}
			if(!empty($_SETTINGS->active['page_types']['shop']))
			{
				unset($_SETTINGS->active['page_types']['shop']);
			}
			if(!empty($_SETTINGS->active['page_types']['product']))
			{
				unset($_SETTINGS->active['page_types']['product']);
			}
		}

		/*
		 * Include scripts required by wordpress media upload used in widget fro special offers
		 */
		function widget_upload_enqueue()
		{
			wp_enqueue_media();
			wp_enqueue_script('magellan-widget-upload', MAGELLAN_JS_URL. 'widget-upload.js', null, null, true);
		}

		/*
		 * Print footer scripts
		 */
		function print_scripts_in_footer( &$scripts) 
		{
			if ( ! is_admin() )
			{
				$scripts->add_data( 'comment-reply', 'group', 1 );
			}
		}

		/*
		 * Fix bug width wp_query parse
		 */
		function wpse_71157_parse_query( $wp_query )
		{
			if ( $wp_query->is_post_type_archive && $wp_query->is_tax )
			{
				$wp_query->is_post_type_archive = false;
			}
		}

		/*
		 * Force every widget to have non empty title
		 */
		function widget_title_force($title)
		{
			if(empty($title))
			{
				$title = ' ';
			}

			return $title;
		}
		
		/*
		 * Check if post is checked as editors choice
		 */
		function is_editors_choice($id = false) {
			global $post;
			
			if(!$id)
			{
				$id = get_the_ID();
			}
			
			$meta = get_post_meta(get_the_ID(), 'is_editors_choice');
			if(!empty($meta) & !empty($meta[0]) && $meta[0] == 'on')
			{
				return true;
			}

			return false;
		}
		

		/*
		 * Get a collection of popular posts
		 */
		function get_popular_posts($range='monthly', $count=5)
		{
			//if popular post plugin class is defined
			if(class_exists('WordpressPopularPosts'))
			{
				global $wpdb;

				if ( !$range || 'all' == $range ) 
				{
					$querydetails = $wpdb->prepare("SELECT
						pop.postid FROM {$wpdb->prefix}popularpostsdata as pop,
						{$wpdb->prefix}posts as p WHERE pop.postid = p.ID
						AND p.post_type = \"post\"
						ORDER BY pop.pageviews DESC LIMIT %d", $count);      
				} 
				else
				{
					$interval = "";

					switch( $range ){
						case "yesterday":
							$interval = "1 DAY";
						break;

						case "daily":
							$interval = "1 DAY";
						break;

						case "weekly":
							$interval = "1 WEEK";
						break;

						case "monthly":
							$interval = "1 MONTH";
						break;

						default:
							$interval = "1 DAY";
						break;
					}

					$now = current_time('mysql');
					$querydetails = $wpdb->prepare("SELECT pop.postid FROM {$wpdb->prefix}popularpostssummary as pop,
						{$wpdb->prefix}posts as p WHERE pop.postid = p.ID
						AND pop.last_viewed > DATE_SUB('%s', INTERVAL $interval )
						AND p.post_type = \"post\"
						GROUP BY pop.postid
						ORDER BY SUM(pop.pageviews) DESC LIMIT %d",
						$now,
						$count
						);
				}
				$result = $wpdb->get_results($querydetails);
				if (empty($result) )
				{
					return false;
				}
				
				$double_check = array();
				// WPML support, get original post/page ID
				if ( defined('ICL_LANGUAGE_CODE') && function_exists('icl_object_id') ) {
					global $sitepress;

					if ( isset( $sitepress )) { // avoids a fatal error with Polylang

						foreach($result as $key => &$item)
						{
							$new_id = icl_object_id( $item->postid, get_post_type( $item->postid ), false, ICL_LANGUAGE_CODE );
							if($new_id && !isset($double_check[$new_id]))
							{
								$double_check[$new_id] = true;
								$item->postid = $new_id;
							}
							else
							{
								unset($result[$key]);
							}
						}
					}

				}

				return $result;
			}
			
			return false;
		}

		/*
		 * Check if posts in the popular list this week
		 */
		function is_post_hot($post_id)
		{
			//if popular post plugin class is defined
			if(class_exists('WordpressPopularPosts'))
			{
				global $wpdb;

				$cached = get_option('magellan_cached_popular_posts', json_encode(array()));
				$cached = json_decode($cached, true);
				$result = array();

				if(empty($cached) || (!empty($cached) && $cached['timestamp'] < time()))  //if cache is older one hour
				{
					$table_name = $wpdb->prefix . "popularposts";
					$interval = "1 WEEK";                    
                    $now = current_time('mysql');
                    
					$querydetails = $wpdb->prepare("SELECT pop.postid FROM {$table_name}summary as pop, {$wpdb->prefix}posts as p"
                    . " WHERE pop.postid = p.ID AND pop.last_viewed >  DATE_SUB('%s', INTERVAL $interval )"
                    . " AND p.post_type = \"post\""
                    . " GROUP BY pop.postid ORDER BY SUM(pop.pageviews)"
                    . " DESC LIMIT 5",
						$now
                    );
                    
					$result = $wpdb->get_results($querydetails);
                    
					$data = array();
					if(!empty($result))
					{
						foreach($result as $item)
						{
							$data[] = $item->postid;
						}
					}
					$cached = array('data' => $data, 'timestamp' => time() + 60*60);
					update_option('magellan_cached_popular_posts', json_encode($cached));
				}

				if(empty($cached) || empty($cached['data']))
				{
					return false;
				}            

				if(in_array($post_id, $cached['data']))
				{
					return true;
				}
			}

			return false;
		}
		
		/*
		 * Get pageview count for a specific post
		 */
		function get_post_pageviews($post_id = false) {
			
			global $wpdb; 
			
			//if popular post plugin class is defined
			if(class_exists('WordpressPopularPosts') && $post_id)
			{
				$table_name = $wpdb->prefix . "popularposts";
				$query = "SELECT SUM(pop.pageviews) as pageviews FROM {$table_name}summary as pop WHERE pop.postid = %d";
				$querydetails = $wpdb->prepare($query, $post_id);
				
				$result = $wpdb->get_row($querydetails);
	
				if(!empty($result))
				{
					return $result->pageviews;
				}
				
				return 0;
			}
		}
		
		/*
		 * Check if gallery index/item is open
		 */
		function is_gallery()
		{
			if(is_post_type_archive('gallery') || is_singular('gallery'))
			{
			   return true; 
			}
			return false;
		}

		
		/*
		 * Add Home item to start of mega menu 
		 */
		function mega_menu_prepend_item() {
			
			$prepend = '';
			
			if(magellan_gs('show_small_logo_menu') == 'on')
			{
				$prepend .= '<div class="logo-2"><a href="' . home_url('/') . '">' . get_bloginfo('name') . '</a></div>';
			}
			
			if(magellan_gs('show_menu_home') == 'on')
			{
				$prepend .= '<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="' . esc_url( home_url( '/' ) ) . '">' . '<i class="fa fa-home"></i><span class="home-text-title">' . esc_html__('Homepage', 'magellan') . '</span>' . '</a></li>';
			}
			
			return $prepend;
		}

		/*
		 * Add Home item to end of mega menu 
		 */
		function mega_menu_append_item() {
			
			$append = '';
			
			if(magellan_gs('show_menu_videos') == 'on')
			{
				$append .= '<li class="menu-item menu-item-type-post_type menu-item-object-page full-width dropdown"><a href="#" class="parent">' . '<i class="fa fa-video-camera"></i>' . '</a>' . $this->get_video_dropdown_content() . '</li>';
			}
			
			if(magellan_gs('show_menu_search') == 'on')
			{
				$append .= '<li class="menu-item menu-item-type-post_type menu-item-object-page search-launcher"><a href="#">' . '<i class="fa fa-search"></i>' . '</a>' . '</li>';
			}
			
			return $append;
		}
		
		/*
		 * Get video dropdown content
		 */
		function get_video_dropdown_content() {
			
			ob_start();
   		
			the_widget('MagellanDropdownLatestVideos', array(), array('title' => esc_html__('Latest posts with videos', 'magellan'), 'before_widget' => '<ul><li><div class="magellan_dropdown_latest_videos">', 'after_widget' => '</div></li></ul>' ));
    
			$return = ob_get_contents();
			ob_end_clean();
			return $return;
		}
		
		/*
		 * Check if post is checked in as featured
		 */
		function post_is_featured($post_id = false) {
		
			global $post;
			if($post_id)
			{
				$post_item = get_post($post_id);
			}
			else 
			{
				$post_item = $post;
			}
			
			if($post_item)
			{
				$is_featured = get_post_meta( $post_item->ID, $key = 'is_featured', $single = true );
			
				if($is_featured == 'on')
				{
					return true;
				}
			}
			
			return false;
		}
		
		
		/*
		 * Modify the count of related products in WC
		 */
		function woo_related_products_args( $args )
		{
			$args['posts_per_page'] = 4; // 4 related products
			$args['columns'] = 4; // arranged in 2 columns
			return $args;
		}
		
		
		/*
		 * Callback for post category tab change
		 */
		function load_post_category_slider_items()
		{
			global $post;
			ob_start();

			$count = intval($_POST['count']);
			$cat_id = $_POST['category'];
			$unique_id = $_POST['unique_id'];
			$category = get_term_by('slug', $cat_id, 'category');
			$interval = $_POST['interval'];
						
			Magellan_Post_Category_Slider::single_slider($unique_id, $category, $count, $interval, true);

			$html = ob_get_contents();
			ob_end_clean();

			die($html);
		}
				
		/*
		 * Placeholder for weather widget
		 */
		function weather_widget_placeholder() {
			
			if(magellan_gs('openweathermap_api_key') != '')
			{	
				?>
				<div class="today">
					<div class="title-default">
						<span><?php esc_html_e('Today', 'magellan'); ?></span>
					</div>
					<p><i class="fa fa-clock-o"></i> <?php echo date('l, j M, Y'); ?></p>
					<p class="weather-ajax-container"> </p>
				</div>
				<?php
			}
		}
		
		/*
		 * weather widget api call
		 */
		static function weather_widget($die = 1) {
						
			ob_start();
			$location = self::get_user_location();
			$info = self::get_weather_info($location);
						
			if(!empty($info))
			{	
				echo '<i class="wi wi-owm-day-' . round($info['weather'][0]['id']) . '"></i> ' . ($info['main']['temp'] > 0 ? '+' : '') . round($info['main']['temp']) . ' ' . magellan_gs('openweathermap_unit') . ' ';
			}

			if(!empty($location))
			{
				if(!empty($location['city']))
				{
					echo $location['city'] . ',';
				}

				echo ' ' . $location['country_name'];
			}

			$data = ob_get_contents();
			ob_end_clean();
			
			if($die)
			{
				die($data);
			}
			
			echo $data;
		}
		
		/*
		 * Get location from IP
		 */
		static function get_user_location() {
			
			
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
						
			$url = 'http://freegeoip.net/json/' . $ip;
			$response = wp_remote_get($url, array('timeout' => 10));

			if(is_wp_error($response))
			{
				return false;
			} 
			$response = $response['body'];
			
			return json_decode($response, true);
		}
		
		/*
		 * Get weather info for location
		 */
		static function get_weather_info($loc = false) {
												
			//call api to get warther data
			if(!empty($loc['latitude']) && $loc['longitude'])
			{
				$unit = (magellan_gs('openweathermap_unit') == 'C') ? 'metric' : 'imperial';
				$api_key = magellan_gs('openweathermap_api_key');
				$url = 'http://api.openweathermap.org/data/2.5/weather?units=' . $unit . '&lat=' . $loc['latitude'] . '&lon=' . $loc['longitude'] . '&APPID=' . $api_key;
				$response = wp_remote_get($url);
				$response = $response['body'];
				
				return json_decode($response, true);
			}
			
			return array();
		}
		
		
		/*
		 * Callback for post reader ratings feature
		 */
		static function post_reader_rate() {
			
			$id = (!empty($_POST['id']) ? intval($_POST['id']) : false);
			$rating = (!empty($_POST['rating']) ? intval($_POST['rating']) : false);
			
			$response = array('status' => 'error');
			
			if($id !== false && $rating !== false)
			{
				check_ajax_referer( 'post_reader_rate' . $id );
				
				//make rating value safe
				if($rating > 50 || $rating < 0) die(-1);
				
				$post = get_post($id);
				
				if($post)
				{

					//get current post ratings
					$reader_rating_value = get_post_meta($id, 'reader_rating_value', true);
					$reader_rating_count = get_post_meta($id, 'reader_rating_count', true);

					if(empty($reader_rating_value)) { $reader_rating_value = 0; }
					if(empty($reader_rating_count)) { $reader_rating_count = 0; }


					//check if user has voted before
					if(
						!isset($_COOKIE['magellan_reader_rate_' . $id])
						&&
						self::post_rate_check_ip($id)
					)	
					{	
						//its the first vote
						$reader_rating_value += $rating;
						$reader_rating_count++;
						$reader_rating_avg = round($reader_rating_value/$reader_rating_count);

						update_post_meta($id, 'reader_rating_value', $reader_rating_value);
						update_post_meta($id, 'reader_rating_count', $reader_rating_count);
						update_post_meta($id, 'reader_rating_avg', $reader_rating_avg);

						//store ip to limit voting
						self::post_rate_store_ip($id);

						//set cookie to remember the rating
						setcookie("magellan_reader_rate_" . $id, $rating,  time() + (60 * 60 * 24 * 30), '/');

						$response = array('status' => 'ok', 'rating' => $reader_rating_avg);

					}	//edit the old vote
					elseif(
						isset($_COOKIE['magellan_reader_rate_' . $id])
						&&
						!self::post_rate_check_ip($id)
					)
					{
						$prev_rating = intval($_COOKIE['magellan_reader_rate_' . $id]);						
						$reader_rating_value = ($reader_rating_value - $prev_rating) + $rating;

						$reader_rating_avg = 0;
						if($reader_rating_value > 0)
						{
							$reader_rating_avg = round($reader_rating_value/$reader_rating_count);
						}
						else
						{
							$reader_rating_value = $rating;
						}

						update_post_meta($id, 'reader_rating_value', $reader_rating_value);
						update_post_meta($id, 'reader_rating_avg', $reader_rating_avg);

						//set cookie to remember the rating
						setcookie("magellan_reader_rate_" . $id, $rating,  time() + (60 * 60 * 24 * 30), '/');

						$response = array('status' => 'ok', 'rating' => $reader_rating_avg);
					}
					else	//has votes from this ip, but has no cookie to confirm what the vote was
					{
						$response = array('status' => 'error');
					}
					
				}
			}
			
			die(json_encode($response));
		}
		
		/*
		 * Save transiet in database to disalow repeat voting
		 */
		static function post_rate_store_ip($post_id) {
			
			$ip = $_SERVER['REMOTE_ADDR']?:($_SERVER['HTTP_X_FORWARDED_FOR']?:$_SERVER['HTTP_CLIENT_IP']);
			$ip = filter_var($ip, FILTER_VALIDATE_IP);
			
			if(!$ip === false)
			{
				set_transient('magellan_reader_rating_' . md5($ip . $post_id), true, 60 * 60 * 24 * 30);	//30 days
			}
		}
		
		/*
		 * Save transiet in database to disalow repeat voting
		 * Returns true if IP is valid and not used already. Otherwise false
		 */
		static function post_rate_check_ip($post_id) {
			
			$ip = $_SERVER['REMOTE_ADDR']?:($_SERVER['HTTP_X_FORWARDED_FOR']?:$_SERVER['HTTP_CLIENT_IP']);
			$ip = filter_var($ip, FILTER_VALIDATE_IP);
						
			//validate ip
			if(!$ip !== false)	{ return false; }
			
			$result = get_transient('magellan_reader_rating_' . md5($ip . $post_id));
			if($result)
			{
				return false;
			}
			
			return true;
		}
		
		/*
		 * Callback for post like/dislike feature
		 */
		static function post_like() {
			
			$data = -1;
			$type = (!empty($_POST['type']) ? intval($_POST['type']) : false);
			$id = (!empty($_POST['id']) ? intval($_POST['id']) : false);
			
			if($type && $id)
			{
				check_ajax_referer( 'post_like_' . $id );
								
				$post = get_post($id);
				
				if($post)
				{
					$meta = get_post_meta($id);
					$likes = (!empty($meta['post_likes']) ? intval($meta['post_likes'][0]) : 0);
					$dislikes = (!empty($meta['post_dislikes']) ? intval($meta['post_dislikes'][0]) : 0);
										
					if(empty($_COOKIE['magellan_post_like_' . $id]))	//check if user has voted before
					{
						if($type == 1) //like
						{
							update_post_meta($id, 'post_likes', ++$likes);
						}
						elseif($type == 2) //dislike
						{
							update_post_meta($id, 'post_dislikes', ++$dislikes);
						}
					}
					elseif(	//if this voted is different thant the original
						!empty($_COOKIE['magellan_post_like_' . $id])
						&&
						$_COOKIE['magellan_post_like_' . $id] != $type
					)
					{
						if($type == 1) //like
						{
							update_post_meta($id, 'post_likes', ++$likes);
							update_post_meta($id, 'post_dislikes', --$dislikes);
						}
						elseif($type == 2) //dislike
						{
							update_post_meta($id, 'post_dislikes', ++$dislikes);
							update_post_meta($id, 'post_likes', --$likes);
						}
					}
					
					$data = array('likes' => $likes, 'dislikes' => $dislikes);
				}
				
				//set cookie to remember the vote
				setcookie("magellan_post_like_" . $id, $type,  time() + (10 * 365 * 24 * 60 * 60), '/');

			}
			
			die(json_encode($data));
		}
				
		/*
		 * Filter footer widget width to squeeze all items in bs 12 columns layout
		 */
		function footer_custom_params($params) {

			$sidebar_id = $params[0]['id'];

			if ( $sidebar_id == 'footer_sidebar' ) {

				$total_widgets = wp_get_sidebars_widgets();
				$sidebar_widgets = count($total_widgets[$sidebar_id]);
				$params[0]['before_widget'] = str_replace('col-md-3', 'col-md-' . floor(12 / $sidebar_widgets), $params[0]['before_widget']);
			}

			return $params;
		}
		
        
        /*
         * Add body classes
         */
		function theme_body_classes($classes) {
            
            global $post;
            
            //only allow bg mode = boxed if image is set
            $bg_image = get_theme_mod('background_image', magellan_gs('background_image'));
            if($bg_image)
            {
                $classes[] = 'boxed';
            }
            else
            {
                 //background mode
                 $classes[] = get_theme_mod('background_mode', magellan_gs('background_mode'));
            }
            
            //is the a featured post
            if(is_single())
            {
                if('full_screen' == magellan_get_post_image_width($post->ID))
                {
                    $classes[] = 'featured-post';
                }
            }

            //what type of trending slider to sue
            
            if(MagellanInstance()->is_trending_slider_visible())
            {
                $pos = magellan_gs('trending_slider_position');
                if($pos == 'fixed')
                {
                    $classes[] = 'trending-slider-fixed';
                }
                else
                {
                    $classes[] = 'trending-slider-docked';
                }
            }

            //overlay pattern
            $overlay = get_theme_mod('enable-bg-overlay', magellan_gs('enable-bg-overlay'));
            if($overlay)
            {
                $classes[] = 'pattern';
            }
            
            //header offset
            $offset = get_theme_mod('enable-header-offset', magellan_gs('enable-header-offset'));
            if($offset)
            {
                $classes[] = 'header-offset';
            }
	
            return $classes;
        }
        
        /*
		 * Move comment textarea to be below other input fields
		 */
		function wpb_move_comment_field_to_bottom( $fields ) {
			$comment_field = $fields['comment'];
			unset( $fields['comment'] );
			$fields['comment'] = $comment_field;
			return $fields;
		}
		
		/*
		 * Determine if the trending slidet should be shown
		 */
		public function is_trending_slider_visible() {
			
			if(magellan_gs('show_trending_slider') == 'on')
			{
				$mode = magellan_gs('trending_slider_mode');
				if($mode == 'all')
				{
					return true;
				}
				elseif($mode == 'blog_and_post' && (is_single() || magellan_is_blog()))
				{
					return true;
				}
				elseif($mode == 'post' && is_single())
				{
					return true;
				}
			}
			
			return false;
		}
		
		/*
		 * Determine if image gradients are enabled
		 */
		public function get_image_gradient_class() {
			
			$gradient = get_theme_mod('use-image-gradients', magellan_gs('use-image-gradients'));
			if($gradient)
			{
				echo ' image-fx';
			}
		}
		
		/*
		 * Checks if current post is review
		 */
		public static function is_review()
		{
			$stars = get_post_meta(get_the_ID(), 'rating_stars', true );
			if($stars !== '' && $stars !== 'disabled')
			{
				return true;
			}

			return false;
		}
		
		/*
		 * Get rating star HTML
		 */
		public static function get_rating_stars($wrap = false, $wrap_class = '')
		{
			if(self::is_review())
			{
				if($wrap)
				{
					echo '<' . esc_attr($wrap) . ' class="' . $wrap_class . '">';
				}
				
				$stars = get_post_meta(get_the_ID(), 'rating_stars', true );
				$stars = $stars / 10;
				?>
				<i class="stars rating s-<?php echo esc_attr(str_replace('.', '-' , $stars)); ?>"></i>
				<?php if(is_single(get_the_ID())) : ?>
				<span itemprop="rating" class="item-rating-hidden"><?php echo $stars; ?></span>
				<?php endif; ?>

				<?php          
				if($wrap)
				{
					echo '</' . esc_attr($wrap) . '>';
				}
			}
		}

        
        /*
		 * Register plugins for TGMPA
		 */
		function register_required_plugins()
		{
			/**
			 * Array of plugin arrays. Required keys are name and slug.
			 * If the source is NOT from the .org repo, then source is also required.
			 */
			$plugins = $this->get_bunlded_plugins();

			// Change this to your theme text domain, used for internationalising strings

			/**
			 * Array of configuration settings. Amend each line as needed.
			 * If you want the default strings to be available under your own theme domain,
			 * leave the strings uncommented.
			 * Some of the strings are added into a sprintf, so see the comments at the
			 * end of each line for what each argument will be.
			 */
			$config = array(
				'domain'       		=> 'magellan',         	// Text domain - likely want to be the same as your theme.
				'default_path' 		=> '',                         	// Default absolute path to pre-packaged plugins
				'menu'         		=> 'install-required-plugins', 	// Menu slug
                'parent_slug'       => 'themes.php',
				'has_notices'      	=> true,                       	// Show admin notices or not
				'is_automatic'    	=> false,					   	// Automatically activate plugins after installation or not
				'message' 			=> '',							// Message to output right before the plugins table
				'strings'      		=> array(
					'page_title'                       			=> esc_html__( 'Install Required Plugins', 'magellan' ),
					'menu_title'                       			=> esc_html__( 'Install Plugins', 'magellan' ),
					'installing'                       			=> esc_html__( 'Installing Plugin: %s', 'magellan' ), // %1$s = plugin name
					'oops'                             			=> esc_html__( 'Something went wrong with the plugin API.', 'magellan' ),
					'notice_can_install_required'     			=> _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'magellan' ), // %1$s = plugin name(s)
					'notice_can_install_recommended'			=> _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'magellan' ), // %1$s = plugin name(s)
					'notice_cannot_install'  					=> _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'magellan' ), // %1$s = plugin name(s)
					'notice_can_activate_required'    			=> _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'magellan' ), // %1$s = plugin name(s)
					'notice_can_activate_recommended'			=> _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'magellan' ), // %1$s = plugin name(s)
					'notice_cannot_activate' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'magellan' ), // %1$s = plugin name(s)
					'notice_ask_to_update' 						=> _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'magellan' ), // %1$s = plugin name(s)
					'notice_cannot_update' 						=> _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'magellan' ), // %1$s = plugin name(s)
					'install_link' 					  			=> _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'magellan' ),
					'activate_link' 				  			=> _n_noop( 'Activate installed plugin', 'Activate installed plugins', 'magellan' ),
					'return'                           			=> esc_html__( 'Return to Required Plugins Installer', 'magellan' ),
					'plugin_activated'                 			=> esc_html__( 'Plugin activated successfully.', 'magellan' ),
					'complete' 									=> esc_html__( 'All plugins installed and activated successfully. %s', 'magellan' ), // %1$s = dashboard link
					'nag_type'									=> 'updated' // Determines admin notice type - can only be 'updated' or 'error'
				)
			);

			tgmpa( $plugins, $config );
		}

		/*
		 * Return list of bundled plugins
		 */
		public function get_bunlded_plugins()
		{
			return array(

				// This is an example of how to include a plugin pre-packaged with a theme
				array(
					'name'     				=> 'Visual Composer', // The plugin name
					'slug'     				=> 'js_composer', // The plugin slug (typically the folder name)
					'source'   				=> get_template_directory() . '/theme/plugins/' . 'js_composer.zip', // The plugin source
					'required' 				=> true, // If false, the plugin is only 'recommended' instead of required
					'version' 				=> magellan_get_bundled_plugin_version('js_composer'), // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
					'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
					'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
					'external_url' 			=> '', // If set, overrides default API URL and points to an external URL
				),
				array(
					'name'     				=> 'Revolution Slider', // The plugin name
					'slug'     				=> 'revslider', // The plugin slug (typically the folder name)
					'source'   				=> get_template_directory() . '/theme/plugins/' . 'revslider.zip', // The plugin source
					'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
					'version' 				=> magellan_get_bundled_plugin_version('revslider'), // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
					'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
					'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
					'external_url' 			=> '', // If set, overrides default API URL and points to an external URL
				),
				array(
					'name'     				=> 'Planetshine Magellan Theme Extension', // The plugin name
					'slug'     				=> 'planetshine-magellan', // The plugin slug (typically the folder name)
					'source'   				=> get_template_directory() . '/theme/plugins/' . 'planetshine-magellan.zip', // The plugin source
					'required' 				=> true, // If false, the plugin is only 'recommended' instead of required
					'version' 				=> '1.0.4', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
					'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
					'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
					'external_url' 			=> '', // If set, overrides default API URL and points to an external URL
				),
				array(
					'name'     				=> 'Regenerate Thumbnails', // The plugin name
					'slug'     				=> 'regenerate-thumbnails', // The plugin slug (typically the folder name)
					'required' 				=> true, // If false, the plugin is only 'recommended' instead of required
					'version' 				=> ''
				),
				array(
					'name'     				=> 'Attachments', // The plugin name
					'slug'     				=> 'attachments', // The plugin slug (typically the folder name)
					'required' 				=> true, // If false, the plugin is only 'recommended' instead of required
					'version' 				=> ''
				),
				array(
					'name'     				=> 'WordPress Popular Posts', // The plugin name
					'slug'     				=> 'wordpress-popular-posts', // The plugin slug (typically the folder name)
					'required' 				=> true, // If false, the plugin is only 'recommended' instead of required
				),
				array(
					'name'     				=> 'WooCommerce', // The plugin name
					'slug'     				=> 'woocommerce', // The plugin slug (typically the folder name)
					'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
					'version' 				=> ''
				),
				array(
					'name'     				=> 'bbPress', // The plugin name
					'slug'     				=> 'bbpress', // The plugin slug (typically the folder name)
					'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
					'version' 				=> ''
				),
				array(
					'name'     				=> 'buddyPress', // The plugin name
					'slug'     				=> 'buddypress', // The plugin slug (typically the folder name)
					'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
					'version' 				=> ''
				),
				array(
					'name'     				=> 'Contact Form 7', // The plugin name
					'slug'     				=> 'contact-form-7', // The plugin slug (typically the folder name)
					'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
					'version' 				=> ''
				),
			);
		}

        
	}
}

function MagellanInstance() {
	return Magellan::instance();
}
	
/**
 * Force Visual Composer & Revslider to initialize as "built into the theme".
 */
if(function_exists('vc_set_as_theme')) { vc_set_as_theme(true); }

if(function_exists('set_revslider_as_theme')) { set_revslider_as_theme(); }

?>