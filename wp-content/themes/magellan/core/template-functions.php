<?php

/*************** Thumbnails ***************/

if(!function_exists('magellan_get_thumbnail'))
{
    function magellan_get_thumbnail( $size, $return_url = false, $placeholder = true ) {
        global $post;
        $image_sizes = magellan_gs('image_sizes');
        if(!empty($image_sizes[$size]))
        {
            
            if ( has_post_thumbnail($post) )
            {
                $src_parts = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $size);
                if($src_parts)
                {
                    if($return_url)
                    {
                        return $src_parts[0];
                    }
                    else
                    {
                        return '<img src="' . $src_parts[0] . '" alt=""/>';
                    }
                }
            }
           
            //this will only be reached if thumb exists
            if($placeholder)
            {
                if($return_url)
                {
                    return MAGELLAN_IMG_URL . 'no-image.png';
                }
                else
                {
                    return '<img src="'. MAGELLAN_IMG_URL .'no-image.jpg" alt="Placeholder" width="' . $image_sizes[$size][0] . '" height="' . $image_sizes[$size][1] . '" />';
                }
            }
            else
            {
                return false;
            }
            
        }
    }
}

if(!function_exists('magellan_add_image_sizes'))
{
    function magellan_add_image_sizes()
    {    
        $image_sizes = magellan_gs('image_sizes');
        if(!empty($image_sizes))
        {
            foreach($image_sizes as $key => $size)
            {
                add_image_size($key, $size[0], $size[1], $size[2]);
            }
        }
    }
}

/*************** PAGINATION ***************/

if(!function_exists('magellan_pagination_exists'))
{
    function magellan_pagination_exists()
    {
        global $wp_query;
        if ( $wp_query->max_num_pages > 1 )  return true;	
        return false;
    }
}

if(!function_exists('magellan_get_pagination'))
{
    function magellan_get_pagination()
    {
        $total = magellan_get_max_pages();

        if ( $total > 1 )  
        {
            $current_page = magellan_get_current_page_num();  		
            $append = '';
            $base = get_pagenum_link(1);
            $permalinks_set =  get_option('permalink_structure'); // structure of "format" depends on whether we're using pretty permalinks
            if(empty( $permalinks_set )) 
            {
                $format = '?paged=%#%';
            }
            else
            {
                $format =  'page/%#%/';

                if(strpos($base, '?') !== false)
                {
                    $pos = strpos($base, '?');
                    $append = substr($base, $pos);
                    $base = substr($base, 0, $pos);
                }
            }

            return paginate_links(array(
              //'base' => $base . '%_%' . $append,
              //'format' => $format,
				
			   'base' => @add_query_arg('paged','%#%'),
               'format'   => '?paged=%#%',
				
              'current' => $current_page,
              'total' => $total,
              'mid_size' => 2,
              'type' => 'array',
              'prev_next' => false,
            ));	
        }

        return array();
    }
}

if(!function_exists('magellan_get_max_pages'))
{
    function magellan_get_max_pages()
    {
        global $wp_query;
        return $wp_query->max_num_pages;
    }
}

if(!function_exists('magellan_get_current_page_num'))
{
    function magellan_get_current_page_num()
    {
        return max(1, get_query_var('paged'));
    }
}    

if(!function_exists('magellan_get_next_page_link'))
{
    function magellan_get_next_page_link()
    {
        $max = magellan_get_max_pages();
        $current = magellan_get_current_page_num();

        if($max > $current) { $page_num = $current + 1; }
        else { $page_num = $max; }

        return get_pagenum_link($page_num);
    }
}

if(!function_exists('magellan_get_prev_page_link'))
{
    function magellan_get_prev_page_link()
    {
        $current = magellan_get_current_page_num();

        if( $current > 1) { $page_num = $current - 1; }
        else { $page_num = $current; }

        return get_pagenum_link($page_num);
    }
}

/*************** Breadcrumbs ***************/

if(!function_exists('magellan_breadcrumbs'))
{
    function magellan_breadcrumbs() {  
        $delimiter = '';
        $home = 'Home'; // text for the 'Home' link
        $blog = 'Blog';
        $before = '<a href="#">'; // tag before the current crumb
        $after = '</a>'; // tag after the current crumb

        global $post;
        $homeLink = home_url('/');
        echo '<a href="' . esc_url($homeLink) . '">' . $home . '</a> ' . $delimiter . ' ';

        if ( get_post_type() == 'post' ) {
            if( get_option( 'show_on_front' ) == 'page' )
            { 
                $posts_page_url = get_permalink( get_option('page_for_posts' ) );
            }
            else
            {
                $posts_page_url = home_url('/');
            }
            echo '<a href="' . esc_url($posts_page_url) . '">' . $blog . '</a> ' . $delimiter . ' ';
        }

        if ( is_category() ) {
          global $wp_query;
          $cat_obj = $wp_query->get_queried_object();
          $thisCat = $cat_obj->term_id;
          $thisCat = get_category($thisCat);
          $parentCat = get_category($thisCat->parent);
          if ($thisCat->parent != 0) echo(get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' '));
          echo $before . 'Archive by category "' . single_cat_title('', false) . '"' . $after;

        } elseif ( is_day() ) {
          echo '<a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
          echo '<a href="' . esc_url(get_month_link(get_the_time('Y'),get_the_time('m'))) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
          echo $before . get_the_time('d') . $after;

        } elseif ( is_month() ) {
          echo '<a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
          echo $before . get_the_time('F') . $after;

        } elseif ( is_year() ) {
          echo $before . get_the_time('Y') . $after;

        } elseif ( is_single() && !is_attachment() ) {
          if ( get_post_type() != 'post' ) {
            $post_type = get_post_type_object(get_post_type());
            $slug = $post_type->rewrite;
            echo '<a href="' . esc_url($homeLink) . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a> ' . $delimiter . ' ';
          } else {
            $cat = get_the_category(); $cat = $cat[0];
            echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
          }

        } elseif ( !is_single() && !is_page() && get_post_type() != 'post' ) {
          $post_type = get_post_type_object(get_post_type());
          if(!empty($post_type))
          {
            echo $before . $post_type->labels->singular_name . $after;
          }
        } elseif ( is_attachment() ) {
          $parent = get_post($post->post_parent);
          $cat = get_the_category($parent->ID); $cat = $cat[0];
          echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
          echo '<a href="' . esc_url(get_permalink($parent)) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';
          echo $before . get_the_title() . $after;

        } elseif ( is_page() && !$post->post_parent ) {
          

        } elseif ( is_page() && $post->post_parent ) {
          $parent_id  = $post->post_parent;
          $breadcrumbs = array();
          while ($parent_id) {
            $page = get_page($parent_id);
            $breadcrumbs[] = '<a href="' . esc_url(get_permalink($page->ID)) . '">' . get_the_title($page->ID) . '</a>';
            $parent_id  = $page->post_parent;
          }
          $breadcrumbs = array_reverse($breadcrumbs);
          foreach ($breadcrumbs as $crumb) echo $crumb . ' ' . $delimiter . ' ';
          echo $before . get_the_title() . $after;

        } elseif ( is_search() ) {
          echo $before . 'Search results for "' . get_search_query() . '"' . $after;

        } elseif ( is_tag() ) {
          echo $before . 'Posts tagged "' . single_tag_title('', false) . '"' . $after;

        } elseif ( is_author() ) {
           global $author;
          $userdata = get_userdata($author);
          echo $before . 'Articles posted by ' . $userdata->display_name . $after;

        } elseif ( is_404() ) {
          echo $before . 'Error 404' . $after;
        }

        if ( get_query_var('paged') ) {
          if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
          echo esc_html__('Page', 'magellan') . ' ' . get_query_var('paged');
          if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
        }  
    }
}

if(!function_exists('magellan_output_taxonomy_hierarchy_option_tree'))
{
    function magellan_output_taxonomy_hierarchy_option_tree($data, $taxonomy, $level = 0)
    {
        global $wp_query;
        $category_slug = '';
        $cat_obj = $wp_query->get_queried_object();

        if(!empty($cat_obj) && !empty($cat_obj->slug))
        {
            $category_slug = $cat_obj->slug;
        }

        $padding = '';
        for($i = 0; $i < $level; $i++)
        {
            $padding .= '&emsp;';
        }

        foreach($data as $item)
        {
            echo '<option value="' . $item->slug . '"';
            if(magellan_get($_GET, $taxonomy) == $item->slug || $category_slug == $item->slug) echo 'selected="selected"';
            echo '>' . $padding . $item->name . '</option>';
            if(!empty($item->children))
            magellan_output_taxonomy_hierarchy_option_tree($item->children, $taxonomy, ++$level);
        }
    }
}

if(!function_exists('magellan_get_sidebar_page_type'))
{
    function magellan_get_sidebar_page_type() 
    {
        $page = NULL;

        if( is_home() ) {
            $page = 'blog';
        } elseif( is_single() && get_post_type() == 'post' ) {
            $page = 'single_post';
        } elseif( is_category() ) {
            $page = 'categories';
        } elseif( is_search() ) {
            $page = 'search';
        } elseif( is_archive() ) {
            $page = 'archives';
        } elseif( is_page() ) {
            $page = 'page';
        } elseif( get_post_type() == 'gallery') {
			$page = 'gallery';
		}
        
        if(magellan_is_woocommerce_active())
        {
            if(is_shop()) {
                $page = 'shop';
            } elseif(is_product()) {
                $page = 'product';
            }
        }

        if(function_exists('is_bbpress'))
        {
            if( is_bbpress() ) 
            {
                $page = 'forum';
            }
        }
		
		if(function_exists('is_buddypress'))
        {
			if( is_buddypress() ) 
            {
                $page = 'buddypress';
            }
		}
				
        return $page;
    }
}

if(!function_exists('magellan_comments'))
{
    function magellan_comments($comment, $args, $depth)
    {
        global $comment_iterator_count;
        if(empty($comment_iterator_count))
        {
            $comment_iterator_count = 1;
        }
        else
        {
            $comment_iterator_count++;
        }

        if($comment->comment_type == '') //normal comment
        {
			$user_id = get_current_user_id();
            ?>
            <li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
				<div class="comment-item">
					<div class="image">
						<?php echo get_avatar( $comment->user_id, $size='70' ); ?>
					</div>
					<div class="text">
						<h3><a href="<?php esc_url(comment_author_url()); ?>"><?php comment_author(); ?></a><span>#<?php echo esc_html($comment_iterator_count); ?></span></h3>
						<div class="legend">
							<span class="time"><?php comment_date(); ?></span>
						</div>
						<div class="comment">
							<?php comment_text(); ?>
							<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth,) ) ); ?>
						</div>
					</div>
				</div>
            <?php
        }   
    }
}

if(!function_exists('magellan_output_theme_version'))
{
    function magellan_output_theme_version()
    {
        $theme = 'Planetshine - ' . magellan_gs('theme_name') . ' - ' . magellan_gs('theme_version');
        echo '<meta name="generator" content="' . $theme . '">';
    }
}

if(!function_exists('magellan_customize_register'))
{
    function magellan_customize_register( $wp_customize ) 
    {   
        $settings = MAGELLAN_SETTINGS_INSTANCE()->get_visual_editor_settings();
        
        if(!empty($settings['head']))
        {
            foreach($settings['head'] as $section)
            {
                $wp_customize->add_section( $section['slug'] , array(
                    'title'      => $section['name'],
                    'priority'   => magellan_get($section, 'priority', 20),
                ) );

                if(!empty($settings['body'][$section['slug']]))
                {
                    $body = $settings['body'][$section['slug']];
                    $priority = 1;
                    
                    foreach($body as $item)
                    {
                        $wp_customize->add_setting(
                            $item['slug'] , array(
                                'default'     => $item['default'],
                                'transport'   => 'refresh',
                                'sanitize_callback' => 'magellan_setting_sanitize_callback'
                            )
                        );

                        $params = array(
                            'label'      => $item['title'],
                            'section'    => $section['slug'],
                            'settings'   => $item['slug'],
                            'priority'   => $priority
                        );

                        if($item['type'] == 'color')
                        {
                            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $item['slug'], $params ));
                        }
                        elseif($item['type'] == 'background')
                        {
                            $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $item['slug'], $params ));
                        }
                        else
                        {

                            if($item['type'] == 'checkbox')
                            {
                                $params['type'] = $item['type'];								
                            }
                            if($item['type'] == 'select' && !empty($item['data']))
                            {
                                $params['type'] = $item['type'];
                                $params['choices'] = $item['data'];
                            }
                            if($item['type'] == 'font_select' && !empty($item['data']))
                            {
                                $params['type'] = 'select';
                                $data = $item['data'];

                                foreach($data as $key => $value)
                                {
                                    $params['choices'][$key] = $value['name'];
                                }
                            }
                            
                            $control = new WP_Customize_Control( $wp_customize, $item['slug'], $params );
                            $wp_customize->add_control( $control );
                        }
                        
                        $priority++;
                    }
                }

            }

        }

    }
}

if(!function_exists('magellan_setting_sanitize_callback'))
{
    function magellan_setting_sanitize_callback($value)
    {
        return $value;
    }
}

if(!function_exists('magellan_google_fonts_url'))
{
    function magellan_google_fonts_url()
    {
        //add font stylesheets
        $fonts = magellan_get_all_google_fonts();
        $protocol = is_ssl() ? 'https' : 'http';
        $custom_fonts = magellan_gs('custom_fonts', false);
		$font_families = array();
        
        if(!empty($fonts) && !empty($custom_fonts))
        {
            foreach($custom_fonts as $cf)
            {
                $default = magellan_gs($cf, false);
                $font = get_theme_mod($cf, $default);
                
				if(empty($fonts[$font]))
				{
					$font = $default;
				}
				
                if(!empty($font) && !empty($fonts[$font]) && $fonts[$font]['status'] !== 'off')
                {
					$font_families[$fonts[$font]['url']] = $fonts[$font]['url'];
                }
            }
        }
		        
		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
        );
		
		$fonts_url = add_query_arg( $query_args, $protocol . '://fonts.googleapis.com/css' );

		return esc_url_raw( $fonts_url );
    }
}

if(!function_exists('magellan_get_all_google_fonts'))
{
    function magellan_get_all_google_fonts()
    {
		global $selected_google_fonts, $google_font_weights;
		
		$extra_fonts = get_option('magellan_extra_google_fonts', false);
		if(!empty($extra_fonts))
		{
			foreach($extra_fonts as $ef)
			{
				$slug = str_replace(' ', '+', $ef);
				$selected_google_fonts[$slug] = array(
					'slug'   => $slug,
					'name'   => $ef,
					'url'    => $ef . $google_font_weights,
					'status' => 'on'
				);
			}	
		}
		
		return $selected_google_fonts;
	}
}

if(!function_exists('magellan_archive_title'))
{
	function magellan_archive_title($title = 'News')
	{
		if(is_tax())
		{
			$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
			$title = $term->name;
		}
		if(is_category())
		{
			$title = single_cat_title('', false);
		}
		if(is_tag())
		{
			$title = single_tag_title('', false);
		}
		if(is_search())
		{
			$title = esc_html__('Search results for', 'magellan') . ' "' . get_search_query() . '"';
		}

	   return $title;
	}
}

if(!function_exists('magellan_search_excerpt_highlight'))
{
    function magellan_search_excerpt_highlight() {
        $excerpt = get_the_excerpt();
        $keys = implode('|', explode(' ', get_search_query()));
		if ($keys != '')
		{
			$excerpt = preg_replace('/(' . $keys .')/iu', '<s>\0</s>', $excerpt);
		}
        echo  $excerpt;
    }
}

if(!function_exists('magellan_search_title_highlight'))
{
    function magellan_search_title_highlight() {
        $title = get_the_title();
        $keys = implode('|', explode(' ', get_search_query()));
        $title = preg_replace('/(' . $keys .')/iu', '<s>\0</s>', $title);
        
        echo $title;
    }
}

if(!function_exists('magellan_navbar_active'))
{
    function magellan_navbar_active() {
        global $post;
        
        if(magellan_gs('post_navigation') == 'enabled')
        {
            return true;
        }
        elseif(magellan_gs('post_navigation') == 'selected')
        {
            $meta = get_post_meta(get_the_ID());
            if(!empty($meta['show_nav']) && $meta['show_nav'][0] == 'on')
            {
                return true;
            }
        }
        return false;
    }
}

if(!function_exists('magellan_language_selector_flags'))
{
    function magellan_language_selector_flags(){
        if(function_exists('icl_get_languages'))
        {
            $languages = icl_get_languages('skip_missing=0&orderby=code');
            if(!empty($languages)){
                echo '<li>';
                
                foreach($languages as $l){
                    if(!$l['active']) { echo '<a href="'. esc_url($l['url']) .'">'; }
                    echo '<img src="'.$l['country_flag_url'].'" height="12" alt="'.$l['language_code'].'" width="18" /> ';
                    if(!$l['active']) { echo '</a>'; }
                }
                
                echo '</li>';
            }
        }
    }
}

if(!function_exists('get_wpml_admin_text_string'))
{
    function get_wpml_admin_text_string($name)
    {		
		$text = false;
        if(function_exists('icl_get_languages') && function_exists('icl_t'))
        {
            $text = icl_t('admin_texts_theme_magellan', 'magellan' . '_' . $name, false);
        }

        if(!$text)
        {
            $text = magellan_gs($name);
        }

        return $text;
    }
}

if(!function_exists('magellan_get_rotated_banner'))
{
    function magellan_get_rotated_banner($location)
    {
        if(!empty($location) && !empty($location['ads_linked']))
        {
            $linked = $location['ads_linked'];
            if(sizeof($linked)> 1)
            {
                $rand = rand(0, sizeof($linked)-1);
                return $linked[$rand];
            }
            
            return $linked[0];
        }
    }
}

if(!function_exists('magellan_get_banner_by_location'))
{
    function magellan_get_banner_by_location($location='header_ad', $extra_wrapper_class='') 
    {
        $location = magellan_gs($location);
        $return = '';

        if($location && $location['ad_enabled'] == 'on')
        {
            $location_item = magellan_get_rotated_banner($location);
            $banner = magellan_get_banner_by_size_and_slug($location_item['ad_slug'], $location_item['ad_size']);

            if(!empty($banner) && !empty($banner['ad_enabled']))
            {
				$mobile_disabled = '';
				if(empty($banner['mobile_enabled']))
				{
					$mobile_disabled = 'mobile_disabled';
				}
				
				$return .= '<div class="banner banner-' . esc_attr($location_item['ad_size']) . ' ' . esc_attr($extra_wrapper_class) . ' ' . esc_attr($mobile_disabled) . '">';   
                
                if($banner['ad_type'] == 'banner') 
                {
                    $return .= '<a href="' . esc_url($banner['ad_link']) . '" target="_blank"><img src="' . esc_url(magellan_banner_image_src($banner['ad_file'])) . '" alt="' . esc_attr($banner['ad_title']) . '"></a>';
                }
                elseif($banner['ad_type'] == 'iframe')
                {
                    $return .= '<iframe class="iframe-' . esc_attr($location_item['ad_size']) . '" scrolling="no" src="' . esc_url($banner['ad_iframe_src']) . '"></iframe>';
                }
                elseif($banner['ad_type'] == 'shortcode')
                {
                    $return .= do_shortcode($banner['shortcode']);
                }
                else
                {                    
                    $return .= stripslashes($banner['googlead_content']);
                }
                
                $return .= '</div>';
            }
        }
        
        return $return;        
    }
}

if(!function_exists('magellan_get_active_banners'))
{
    function magellan_get_active_banners($size='728x90') 
    {
        $banners = magellan_gs($size);
        $return = array();
        if(!empty($banners))
        {
            foreach($banners as $banner)
            {
                if(!empty($banner['ad_enabled']) && $banner['ad_enabled'] == 'on')
                {
                    $return[] = $banner;
                }
            }
        }
        return $return;
    }
}

/*
 * Function for banner src. If argument is given as string - uri, it returns that. Else it loads attachment by id.
 */
if(!function_exists('magellan_banner_image_src'))
{
    function magellan_banner_image_src($id_or_uri = false) 
    {
		
		if(intval($id_or_uri) > 0)
		{
			return magellan_get_attachment_src($id_or_uri);
		}
		
		return $id_or_uri;
	}
}
	
if(!function_exists('magellan_excerpt'))
{
    function magellan_excerpt($limit=50) 
    {
          $excerpt = explode(' ', get_the_excerpt(), $limit);
          if (count($excerpt)>=$limit) {
            array_pop($excerpt);
            $excerpt = implode(" ",$excerpt).'...';
          } else {
            $excerpt = implode(" ",$excerpt);
          } 
          $excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
          $excerpt = strip_tags($excerpt);
          return $excerpt;
    }
}

if(!function_exists('magellan_get_banner_by_size_and_slug'))
{
    function magellan_get_banner_by_size_and_slug($slug='default', $size='728x90') 
    {
        if(empty($size) || empty($slug)) return array();
        
        $banners = magellan_gs($size);
        $banner = array();
        
        if(!empty($banners))
        {
            foreach($banners as $item)
            {
                if($item['ad_slug'] == $slug)
                {
                    $banner = $item;
                }
            }
        }   
        return $banner;
    }
}

if(!function_exists('magellan_kses_widget_html_field'))
{
    function magellan_kses_widget_html_field($text)
    {
        $allowedtags = array(
            'a' => array(
                'href' => true,
                'title' => true,
				'target' => true
            ),
            'img' => array(
                'src' => true,
                'width' => true,
                'height' => true,
                'alt' => true
            ),
            'span' => array(),
            'b' => array(),
            'em' => array(),
			'br' => array(),
            'i' => array(
                'class' => true
            ),
            'strong' => array(),
        );
        
        return wp_kses($text, $allowedtags);   
    }
}

if(!function_exists('magellan_kses_wrapper_html'))
{
    function magellan_kses_wrapper_html($text)
    {
        $allowedtags = array(
            'div' => array(
                'class' => true,
                'id' => true,

            ),
        );
        
        return wp_kses($text, $allowedtags);   
    }
}

if(!function_exists('magellan_get_sidebar_position'))
{
    function magellan_get_sidebar_position()
    {
		$sidebar = magellan_gs('sidebar_position');
		return $sidebar;
    }
}

if(!function_exists('magellan_get_attachment_src'))
{
    function magellan_get_attachment_src($image_id = false, $size = 'full')
    {
		$image = '';
		
		if(is_numeric($image_id))
		{
			$src_parts = wp_get_attachment_image_src($image_id, $size);
			if(!empty($src_parts))
			{
				$image = $src_parts[0];
			}
		}
		
		return $image;
    }
}

if(!function_exists('magellan_get_post_image_style'))
{
    function magellan_get_post_image_width($post_id)
    {
        //get image style for this post
        $post_image_width = magellan_gs('post_image_width');
        $local_image_size = get_post_meta( $post_id, $key = 'image_size', true);
        if($local_image_size == 'text_width' || $local_image_size == 'container_width' || $local_image_size == 'full_screen' || $local_image_size == 'no_image' || $local_image_size == 'video' || $local_image_size == 'video_autoplay')
        {
            $post_image_width = $local_image_size;
        }
        return $post_image_width;
    }
}


if(!function_exists('magellan_menu_items_wrap_filter'))
{
    function magellan_menu_items_wrap_filter()
    {
        $wrap  = '<ul id="%1$s" class="%2$s">';
		
		$wrap .= apply_filters('mega_menu_prepend_item', '');
		
		$wrap .= '%3$s';
		
		$wrap .= apply_filters('mega_menu_append_item', '');
		
		$wrap .= '</ul>';
		
		return $wrap;
    }
}

if(!function_exists('magellan_get_sidebars'))
{
    function magellan_get_sidebars()
    {
		$static = magellan_gs('static_sidebars', false);
		if(empty($static) || !is_array($static))
		{
			$static = array();
		}
		
		$saved = magellan_gs('sidebars');
		if(empty($saved) || !is_array($saved))
		{
			$saved = array();
		}	
        else
        {
            $template = magellan_gs('sidebar_template');
            
            foreach($saved as &$sidebar) //strip slashes from WP Database
            {
                $sidebar['before_widget'] = $template['before_widget'];
                $sidebar['after_widget'] = $template['after_widget'];
                $sidebar['before_title'] = $template['before_title'];
                $sidebar['after_title'] = $template['after_title'];
            }
        }
		
		return array_merge( $static, $saved);
	}
}