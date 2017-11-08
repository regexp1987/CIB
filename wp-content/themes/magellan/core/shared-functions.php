<?php

if(!function_exists('magellan_gs'))
{
    function magellan_gs($param = NULL, $allow_cache = true)	//get setting
    {
        return MAGELLAN_SETTINGS_INSTANCE()->get_single($param, $allow_cache);
        
        //legacy remove later
        if($param === NULL) return MAGELLAN_SETTINGS_INSTANCE()->active;
        if(!empty(MAGELLAN_SETTINGS_INSTANCE()->active[$param]) && $allow_cache == true) return MAGELLAN_SETTINGS_INSTANCE()->active[$param];
        if(!empty(MAGELLAN_SETTINGS_INSTANCE()->$param)) return MAGELLAN_SETTINGS_INSTANCE()->$param;
        if(!empty(MAGELLAN_SETTINGS_INSTANCE()->hidden[$param])) return MAGELLAN_SETTINGS_INSTANCE()->hidden[$param];
        return false;
    }
}

if(!function_exists('magellan_ss'))
{
    function magellan_ss($name, $value) //save setting
    {
        MAGELLAN_SETTINGS_INSTANCE()->update_single($name, $value);
    }
}

if(!function_exists('magellan_get_settings_admin_head'))
{
    function magellan_get_settings_admin_head()
    {
        return MAGELLAN_SETTINGS_INSTANCE()->admin_head;
    }
}

if(!function_exists('magellan_get_settings_admin_body'))
{
    function magellan_get_settings_admin_body()
    {
        return MAGELLAN_SETTINGS_INSTANCE()->admin_body;
    }
}

if(!function_exists('debug'))
{
    function debug($variable, $die=true)
    {
        if ((is_scalar($variable)) || (is_null($variable)))
        {
            if (is_null($variable))
            {
                $output = '<i>NULL</i>';
            }
            elseif (is_bool($variable))
            {
                $output = '<i>' . (($variable) ? 'TRUE' : 'FALSE') . '</i>';
            }
            else 
            {
                $output = $variable;
            }
            echo '<pre>variable: ' . $output . '</pre>';
        }
        else // non-scalar
        {
            echo '<pre>';
            print_r($variable);
            echo '</pre>';
        }

        if ($die)
        {
            die();
        }
    }
}    

if(!function_exists('magellan_dbSE'))
{
    function magellan_dbSE($value)
    {
        global $wpdb;
        return $wpdb->_real_escape($value);
    }
}

if(!function_exists('magellan_get'))
{
    function magellan_get( $array, $key, $default = NULL )
    {
        if(is_array($array))
        {
            if( !empty( $array[$key] ) )
            {
                return $array[$key];
            }
        }
        return $default;
    }
}

if(!function_exists('magellan_current_page_url'))
{
    function magellan_current_page_url() 
    {
        $pageURL = 'http';

        if (magellan_get($_SERVER, "HTTPS") == "on") {$pageURL .= "s";}

        $pageURL .= "://";

        if ($_SERVER["SERVER_PORT"] != "80") 
        {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } 
        else
        {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }


        return $pageURL;
    }
}

if(!function_exists('magellan_assamble_url'))
{
    function magellan_assamble_url($pageURL = false, $add_params = array(), $remove_params = array())
    {
        if(!$pageURL)
        {
            $pageURL = magellan_current_page_url();
        }

        if(!empty($remove_params))
        {
            foreach($remove_params as $remove)
            if(strpos($pageURL, $remove) !== false)
            {
                $parts = explode('?', $pageURL);
                if(count($parts) > 1)
                {
                    $query_parts = explode('&', $parts[1]);
                    foreach($query_parts as $key => $value)
                    {
                        if(strpos($value, $remove) !== false)
                        {
                            unset($query_parts[$key]);
                        }
                    }
                    if(!empty($query_parts))
                    {    
                        $parts[1] = implode('&', $query_parts);
                    }
                    else
                    {
                        unset($parts[1]);
                    }
                }

                $pageURL = implode('?', $parts);
            }
        }

        if(!empty($add_params))
        {
            foreach($add_params as $add)
            {        
                if(strpos($pageURL, '?') !== false)
                {
                    $pageURL .= '&' . $add;
                }
                else
                {
                    $pageURL .= '?' . $add;
                }
            }
        }

        return $pageURL;       
    }
}

if(!function_exists('magellan_get_post_id_from_slug'))
{
    function magellan_get_post_id_from_slug( $slug, $post_type = 'post' ) 
    {
        global $wpdb;

        $query = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type = %s", $slug, $post_type );
        $id = $wpdb->get_var( $query );  
        if ( ! empty( $id ) ) {
            return $id;
        } else {
            return 0;
        }
    }
}

if(!function_exists('magellan_get_post_slug_from_id'))
{
    function magellan_get_post_slug_from_id( $post_id ) 
    {
        $post = get_post( $post_id );
        if ( isset( $post->post_name ) ) {
            return $post->post_name;
        } else {
            return null;
        }
    }
}

if(!function_exists('magellan_thumbnail_regenerate_notification'))
{
    function magellan_thumbnail_regenerate_notification()
    {
        $dismissed = get_option('magellan_page_thumb_regen_dismissed', false);
        
        if(!$dismissed)
        {
            
            $dismiss_link = get_admin_url() . 'admin.php?page=' . magellan_gs('theme_slug') . '-admin&magellan_action=dismiss-thumb-regen';

            ?>
            <div class="updated planetshine-auto-page-notification">
                <p>
                    <?php printf( esc_html__('If your blog already has posts with images, please <strong>Install</strong> & <strong>Run</strong> the bundled <strong>Regenerate Thumbnails</strong> plugin! This will ensure faster page load speeds.', 'magellan') ); ?>
                    <a class="planetshine-dismiss" href="<?php echo esc_url($dismiss_link); ?>"><?php esc_html_e('dismiss', 'magellan'); ?></a>
                </p>
            </div>
            <?php
        }
    }
}

if(!function_exists('magellan_page_install_notification'))
{
    function magellan_page_install_notification() 
    {
        $dismissed = get_option('magellan_page_install_dismissed', false);
        
        if(!$dismissed && class_exists('Magellan_Magellan_Extension'))
        {
            $pages = magellan_get_auto_pages();
            $pages_installed = true;
            foreach($pages as &$page)
            {
                if(empty($page['id']) || get_post($page['id']) == NULL) //if page is not created of has been deleted
                {
                    $pages_installed = false;
                }
            }

			//look for demo import
			$demo_imported = false;
			if(class_exists('Magellan_Demo_Export') && class_exists('Magellan_Demo_Import'))
			{
				$demo_imported = Magellan_Demo_Import :: getCurrentImport();
			}
			
            if(!$pages_installed && $demo_imported == false)
            {
//                $install_link = get_admin_url() . 'admin.php?page=' . magellan_gs('theme_slug') . '-admin&magellan_action=install-auto-pages';
				$import_link = get_admin_url() . 'admin.php?page=' . magellan_gs('theme_slug') . '-admin&view=setup&section=demo_import';
                $import_pages_link = get_admin_url() . 'admin.php?page=' . magellan_gs('theme_slug') . '-admin&view=setup&section=install_pages';
                $dismiss_link = get_admin_url() . 'admin.php?page=' . magellan_gs('theme_slug') . '-admin&magellan_action=dismiss-auto-pages';
                ?>
                <div class="updated planetshine-auto-page-notification">
                    <p>
                        <?php
                            esc_html_e('Click here to', 'magellan'); 
                            echo ' <a href="' . esc_url($import_link) . '">' . esc_html__('import full theme demo', 'magellan') . '</a>. ';
                            esc_html_e('Or click here to', 'magellan');
                            echo ' <a href="' . esc_url($import_pages_link) . '">' . esc_html__('import homepage and other individual pages', 'magellan') . '</a>';
                        ?>
						<a class="planetshine-dismiss" href="<?php echo esc_url($dismiss_link); ?>"><?php esc_html_e('dismiss', 'magellan'); ?></a>
                    </p>
                </div>
                <?php
            }
        }
    }
}


if(!function_exists('magellan_db_update_notification'))
{
    function magellan_db_update_notification()
    {
        $install_link = get_admin_url() . 'admin.php?page=' . magellan_gs('theme_slug') . '-admin&magellan_action=planetshine-db-migrate';
        ?>
        <div class="update-nag planetshine-auto-page-notification">
            <p>
                <?php
                    echo esc_html__('Theme needs to update your sites database to ensure full compatibility with the latest version of theme.', 'magellan');
                    echo ' <a href="' . esc_url($install_link) . '">' . esc_html__('Click here to update', 'magellan')  . '</a>';
                ?>
            </p>
        </div>
        <?php
    }
}

if(!function_exists('magellan_page_install_success_notification'))
{
    function magellan_page_install_success_notification() 
    {
        ?>
        <div class="updated planetshine-auto-page-notification">
            <p>
                <?php esc_html_e('The pages have been installed successfully!', 'magellan'); ?>
            </p>
        </div>
        <?php
    }
}
    
if(!function_exists('magellan_add_auto_pages'))
{
    function magellan_add_auto_pages() 
    {
        $pages = magellan_get_auto_pages();

        foreach($pages as &$page)
        {
            if(empty($page['id']) || get_post($page['id']) == NULL) //if page is not created of has been deleted
            {
                $page['id'] = magellan_create_page($page);

                //set up frontpage & blog page
                if($page['role'] == 'front_page')
                {
                    update_option( 'page_on_front', $page['id'] );
                    update_option( 'show_on_front', 'page' );
                }
                if($page['role'] == 'posts')
                {
                    update_option( 'page_for_posts', $page['id'] );
                }

                if(!empty($page['template']))
                {
                    update_post_meta( $page['id'], '_wp_page_template', $page['template'] );
                }
            }
        }

        update_option('magellan_auto_pages', json_encode($pages));
    }
}

if(!function_exists('magellan_get_auto_pages'))
{
    function magellan_get_auto_pages()
    {
        $default_pages = magellan_gs('auto_pages');
        $pages = get_option('magellan_auto_pages', json_encode($default_pages));
        return json_decode($pages, true);
    }
}

if(!function_exists('magellan_create_page'))
{
    function magellan_create_page($page) 
    {
        $page_data = array(
            'post_status' 		=> 'publish',
            'post_type' 		=> 'page',
            'post_author' 		=> 1,
            'post_name' 		=> esc_sql( $page['slug'] ),
            'post_title' 		=> $page['name'],
            'post_content' 		=> $page['content'],
            'post_parent' 		=> 0,
            'comment_status' 	=> 'closed'
        );

        $page_id = wp_insert_post( $page_data );
        if($page['role'] == 'front_page')
        {
            update_post_meta( $page_id, '_wp_page_template', 'page-home.php' );
        }
        return $page_id;
    }
}

if(!function_exists('magellan_is_shop_installed'))
{
    function magellan_is_shop_installed() 
    {
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        $woocommerce = 'woocommerce/woocommerce.php';
        if( is_plugin_active( $woocommerce ) ) 
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}

if(!function_exists('magellan_is_woocommerce_active'))
{
    function magellan_is_woocommerce_active()
    {
        if ( magellan_is_shop_installed())
        {
            return true;
        }
        return false;
    }
}

if(!function_exists('magellan_not_woocommerce_special_content'))
{
    function magellan_not_woocommerce_special_content()
    {
        if(magellan_is_woocommerce_active())
        {
            if( is_cart() || is_checkout() )
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        return true;
    }
}

if(!function_exists('magellan_dump_included_files'))
{
    function magellan_dump_included_files()
    {
        $included_files = get_included_files();
        $stylesheet_dir = str_replace( '\\', '/', get_stylesheet_directory() );
        $template_dir   = str_replace( '\\', '/', get_template_directory() );

        foreach ( $included_files as $key => $path ) {

            $path   = str_replace( '\\', '/', $path );

            if ( false === strpos( $path, $stylesheet_dir ) && false === strpos( $path, $template_dir ) )
                unset( $included_files[$key] );
        }

        debug( $included_files );
    }
}

if(!function_exists('magellan_get_posts_by_type'))
{
    function magellan_get_posts_by_type($post_type = 'post', $count = 8)
    {
        global $wpdb;

        if(function_exists('icl_get_languages')) //if wpml
        {
            $querydetails = $wpdb->prepare("
                SELECT wposts.*
                FROM $wpdb->posts as wposts
                LEFT JOIN ". $wpdb->base_prefix ."icl_translations 
                ON wposts.ID = ". $wpdb->base_prefix ."icl_translations.element_id
                WHERE
                wposts.post_status = 'publish'
                AND wposts.post_type = %s
                AND ". $wpdb->base_prefix ."icl_translations.language_code = %s
                ORDER BY wposts.post_date DESC
                LIMIT 0, %d
            ",
            magellan_dbSE($post_type),
            ICL_LANGUAGE_CODE,
            $count);
        }
        else
        {
            $querydetails = $wpdb->prepare("
                SELECT wposts.*
                FROM $wpdb->posts wposts
                WHERE
                wposts.post_status = 'publish'
                AND wposts.post_type = %s
                ORDER BY wposts.post_date DESC
                LIMIT 0, %d",
                magellan_dbSE($post_type),
                $count
                );
        }

        return $wpdb->get_results($querydetails, OBJECT);
    }
}

if(!function_exists('magellan_get_posts_by_meta'))
{
    function magellan_get_posts_by_meta($key, $value, $count, $page=1, $post_type = 'post')
    {
        global $wpdb;
        $limit = ($page-1) * $count;
		
		$q = "
            SELECT wposts.*
            FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta
            WHERE wposts.ID = wpostmeta.post_id
            AND wpostmeta.meta_key = %s";
		
		if(is_array($value))
		{
			$q .= "AND wpostmeta.meta_value IN (" . implode(', ', array_fill(0, count($value), '%s')) . ")";
		}
		else
		{
			$q .= "AND wpostmeta.meta_value = %s";
			$value = array($value);
		}
		
        $q .= "
            AND wposts.post_status = 'publish'
            AND wposts.post_type = %s
            ORDER BY wposts.post_date DESC
            LIMIT %d, %d";

		$querydetails = call_user_func_array(array($wpdb, 'prepare'), array_merge(array($q), array(magellan_dbSE($key)), $value, array(magellan_dbSE($post_type)), array($limit), array($count)) );
		
        return $wpdb->get_results($querydetails, OBJECT);
    }
}

if(!function_exists('magellan_get_post_count_by_meta'))
{
    function magellan_get_post_count_by_meta($key, $value, $post_type = 'post')
    {
        global $wpdb;

        $querydetails = $wpdb->prepare("
            SELECT COUNT(*) as count
            FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta
            WHERE wposts.ID = wpostmeta.post_id
            AND wpostmeta.meta_key = %s
            AND wpostmeta.meta_value = %s
            AND wposts.post_status = 'publish'
            AND wposts.post_type = %s",
            magellan_dbSE($key),
            magellan_dbSE($value),
            magellan_dbSE($post_type)
        );
        
        $values = $wpdb->get_results($querydetails, ARRAY_A);
        if(!empty($values))
        {
            return $values[0]['count'];
        }
        return 0;
    }
}

if(!function_exists('magellan_get_post_collection'))
{
    function magellan_get_post_collection($params = array(), $count = NULL, $page=1, $orderby = 'date', $dir = 'DESC', $type='post')
    {
        $args = array();
        if(!empty($params))
        {
            foreach($params as $key => $value)
            {
                if($value != NULL) $args[$key] = $value;
            }
        }

        $args['orderby'] = $orderby;
        $args['order'] = $dir;
        $args['post_status'] = 'publish';
        $args['ignore_sticky_posts'] = 1;
        $args['paged'] = $page;
        $args['post_type'] = $type;
        if($count) $args['posts_per_page'] = $count;
        $posts = new WP_Query( $args);
        return $posts->posts;
    }
}

if(!function_exists('magellan_get_taxonomy_hierarchy'))
{
    function magellan_get_taxonomy_hierarchy($taxonomy, $parent_id = 0)
    {
        $args = array(
            'type'                     => 'post',
            'parent'                   => $parent_id,
            'orderby'                  => 'name',
            'order'                    => 'ASC',
            'hide_empty'               => 1,
            //'hierarchical'             => 1,
            'taxonomy'                 => $taxonomy,
            'pad_counts'               => false 
        );
        $categories = get_categories( $args );

        foreach($categories as $key => $value)
        {
            $categories[$key]->children = magellan_get_taxonomy_hierarchy($taxonomy, $value->term_id);
        }

        return $categories;
    }
}

if(!function_exists('magellan_get_posts_with_latest_comments'))
{
    function magellan_get_posts_with_latest_comments($count, $page=1, $type='post')
    {
        global $wpdb;
        $limit = ($page-1) * $count;

        $querydetails = $wpdb->prepare("
            select wp_posts.*,
            coalesce(
                (
                    select max(comment_date)
                    from $wpdb->comments wpc
                    where wpc.comment_post_id = wp_posts.id
                ),
                wp_posts.post_date
            ) as mcomment_date
            from $wpdb->posts wp_posts
            where post_type = %s
            and post_status = 'publish'
            and comment_count > 0
            order by mcomment_date desc
            limit %d, %d",
            $type,
            $limit,
            $count
            );

        return $wpdb->get_results($querydetails, OBJECT);    
    }
}

if(!function_exists('magellan_get_posts_with_comments_count'))
{
    function magellan_get_posts_with_comments_count($type='post')
    {
        global $wpdb;

        $querydetails = $wpdb->prepare("
            select COUNT(*) as count
            from $wpdb->posts wp_posts
            where post_type = %s
            and post_status = 'publish'
            and comment_count > 0",
            $type
        );

        $values = $wpdb->get_results($querydetails, ARRAY_A);    
        if(!empty($values))
        {
            return $values[0]['count'];
        }
        return 0;
    }
}

if(!function_exists('magellan_generate_css'))
{
    function magellan_generate_css( $selector, $style, $mod_name, $prefix='', $postfix='', $echo=true )
    {
        $return = '';
        $default = magellan_gs($mod_name);
        $mod = get_theme_mod($mod_name, $default);
        if ( ! empty( $mod ) )
        {
            $mod = str_replace('#', '', $mod);
            $mod = str_replace('+', ' ', $mod);
            $return = sprintf('%s { %s:%s; }',
               $selector,
               $style,
               $prefix.$mod.$postfix
            );
            if ( $echo )
            {
               echo $return . "\n";
            }
        }
        return $return;
    }
}

if(!function_exists('magellan_map_visual_settings_to_less'))
{
	function magellan_map_visual_settings_to_less($vars, $handle)
	{
		$body = MAGELLAN_SETTINGS_INSTANCE()->admin_body;		
		$keys = array_merge(
			array_keys($body['visual_editor']['visual_colors']),
			array_keys($body['visual_editor']['visual_background']),
			array_keys($body['visual_editor']['visual_header']),
			array_keys($body['visual_editor']['visual_footer']),
			array_keys($body['visual_editor']['visual_fonts']),
			array_keys($body['visual_editor']['visual_gradients']),
            array_keys($body['visual_editor']['visual_header_offset'])
		);
        
		foreach($keys as $key)
		{
			$default = magellan_gs($key);
			$mod = get_theme_mod($key, $default);
			
			if ( ! empty( $mod ) )
			{
				$vars[$key] = str_replace('+', ' ', $mod);
			}
            else
            {
                $default = str_replace('+', ' ', magellan_gs($key, false));
                if($default)
                {
                    $vars[$key] = $default;
                }
            }
		}
        				
		return $vars;
	}
}

/* Add additional params to wp_get_archives thus enabling filter by year functionality */
if(!function_exists('magellan_archive_where'))
{
    function magellan_archive_where($where,$args){
        $year = isset($args['year']) ? $args['year'] : '';
        $month = isset($args['month']) ? $args['month'] : '';

        if($year){
        $where .= " AND YEAR(post_date) = '$year' ";
        $where .= $month ? " AND MONTH(post_date) = '$month' " : '';
        }
        if($month){
        $where .= " AND MONTH(post_date) = '$month' ";
        }

        return $where;
    }
}

if(!function_exists('magellan_is_blog'))
{
    function magellan_is_blog()
    {
        if ( is_front_page() && is_home() ) 
        {
            return false;
        } 
        elseif ( is_front_page() ) 
        {
            return false;
        } 
        elseif ( is_home() ) {
            return true;
        } else {
            return false;
        }
    }
}

if(!function_exists('magellan_log_theme_version'))
{
    function magellan_log_theme_version()
    {
        $theme = wp_get_theme();
        $version = $theme->get('Version');
		                
        $curr_version = get_option('magellan_current_' . MAGELLAN_THEME_DOMAIN .'_version', '0');
		
		if($version != $curr_version)
		{
			update_option('magellan_previous_' . MAGELLAN_THEME_DOMAIN .'_version', $curr_version);
			update_option('magellan_current_' . MAGELLAN_THEME_DOMAIN .'_version', $version);
		}
    }
}

if(!function_exists('magellan_redirect_to_status'))
{
    function magellan_redirect_to_status()
    {
		
		if ( is_admin() && isset( $_GET['activated'] ) ) {
			$url = get_admin_url() . 'admin.php?page=' . magellan_gs('theme_slug') . '-admin' . '&view=setup';
			wp_redirect($url);
			exit;
		}
    }
}

if(!function_exists('magellan_init_updater'))
{
    function magellan_init_updater()
    {
		$register = magellan_gs('theme_register');
        if(!empty($register))
        {
            if(in_array($register['status'], array('ok', 'on'))) 
            {
                Envato_WP_Theme_Updater::init( $register['tf_username'], $register['tf_api_key'], 'Planetshine' );
            }
        }
    }
}


//replacement function to allow to use shortcodes without VC
if(!function_exists('vc_map'))
{
    function vc_build_link($url)
    {
        return array('url' => $url);
    }
}

//wp filesysytem based put contents
if(!function_exists('magellan_wp_file_put_contents'))
{
	function magellan_wp_file_put_contents($path, $file_contents) {

		global $wp_filesystem;
		// Initialize the WP filesystem, no more using 'file-put-contents' function
		if (empty($wp_filesystem)) {
			require_once (ABSPATH . '/wp-admin/includes/file.php');
			WP_Filesystem();
		}

		if(!$wp_filesystem->put_contents( $path, $file_contents, 0644) ) {
			return esc_html__('Failed to put file', 'magellan');
		}
		
	}
}

//wp filesystem based get contents
if(!function_exists('magellan_wp_file_get_contents'))
{
	function magellan_wp_file_get_contents($path) {

		global $wp_filesystem;
		// Initialize the WP filesystem, no more using 'file-get-contents' function
		if (empty($wp_filesystem)) {
			require_once (ABSPATH . '/wp-admin/includes/file.php');
			WP_Filesystem();
		}

		return $wp_filesystem->get_contents($path);
		
	}
}

if(!function_exists('magellan_get_bundled_plugin_version'))
{
	function magellan_get_bundled_plugin_version($slug = '')
	{
		global $plsh_bundled_versions;
		
		if(!empty($plsh_bundled_versions[$slug]))
		{
			return $plsh_bundled_versions[$slug];
		}
		
		return false;
	}
}
?>