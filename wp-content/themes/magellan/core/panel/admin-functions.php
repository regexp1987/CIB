<?php

function magellan_load_admin_menus() 
{
	if(class_exists('Magellan_Magellan_Extension'))
    {
        Magellan_Magellan_Extension::add_admin_menus(magellan_gs('theme_name'), magellan_gs('theme_slug'), MAGELLAN_ADMIN_ASSET_URL . 'images/planetshine-cp-panel-icon.png');
    }
    else
    {
        add_theme_page(magellan_gs('theme_name'), magellan_gs('theme_name'), 'administrator', magellan_gs('theme_slug').'-admin', 'magellan_admin');
    }
}

function magellan_load_admin_styles($hook_suffix) 
{
    if($hook_suffix == 'toplevel_page_' . MAGELLAN_THEME_DOMAIN . '-admin' || $hook_suffix == 'appearance_page_' . MAGELLAN_THEME_DOMAIN . '-admin') {
        wp_enqueue_style('planetshine-admin-style', get_template_directory_uri() .'/core/panel/assets/css/style.css');
        $protocol = is_ssl() ? 'https' : 'http';
        wp_enqueue_style('planetshine-roboto', $protocol . '://fonts.googleapis.com/css?family=Roboto:100,300,400');
        wp_enqueue_style('planetshine-font-awesome', get_template_directory_uri() .'/core/panel/assets/css/font-awesome.css');
    }
    wp_enqueue_style('planetshine-global-style', get_template_directory_uri() .'/core/panel/assets/css/global-style.css');
}
 
function magellan_load_admin_scripts($hook_suffix) 
{
 	if($hook_suffix == 'toplevel_page_' . MAGELLAN_THEME_DOMAIN . '-admin' || $hook_suffix == 'appearance_page_' . MAGELLAN_THEME_DOMAIN . '-admin') {
        wp_enqueue_script('planetshine-uniform', get_template_directory_uri() .'/core/panel/assets/js/jquery.uniform.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget' ));
        wp_enqueue_script('planetshine-dropkick', get_template_directory_uri() .'/core/panel/assets/js/jquery.dropkick.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget' ));
		wp_enqueue_script('planetshine-progressbar', get_template_directory_uri() .'/core/panel/assets/js/progressbar.min.js', array( 'jquery'));
        wp_enqueue_script('plhs-admin-scripts', get_template_directory_uri() .'/core/panel/assets/js/scripts.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-accordion' ));
    }
}


function magellan_save_settings()
{
    check_ajax_referer('magellan_save_settings');
 	parse_str($_POST['data'],$data);
 	
    Magellan_Settings :: store_settings($data);
 	
 	die(json_encode(array('status' => 'ok', 'msg' => 'Settings saved!')));
}
 

function magellan_load_style_preset()
{
    check_ajax_referer('magellan_load_style_preset');
 	
    $preset = $_POST['preset'];
    $all_presets = magellan_gs('presets', false);
    $settings = $all_presets[$preset];
 	
    foreach($settings as $setting_group)
    {
        foreach($setting_group as $key => $value)
        {
            set_theme_mod($key, $value);
        }
    }
    
 	die(json_encode(array('status' => 'ok', 'msg' => 'Settings saved!')));
}

function magellan_import_settings()
{
    check_ajax_referer('magellan_import_settings');
    parse_str($_POST['data'],$data);
       
    Magellan_Settings :: import_settings($data['settings_export']);
    
    die(json_encode(array('status' => 'ok', 'msg' => 'Settings imported!')));
}

function magellan_reset_settings()
{
    check_ajax_referer('magellan_reset_settings');
    
    MAGELLAN_SETTINGS_INSTANCE()->reset_settings();
    
    die(json_encode(array('status' => 'ok', 'msg' => 'Settings reset!')));
}

function magellan_get_admin_template($name)
{
    $path = get_template_directory() . '/core/panel/templates/' . $name . '.php';    

    if(file_exists($path))
    {
        include($path);
    }
}

function magellan_save_sidebar()
{
    check_ajax_referer('magellan_save_sidebar');
 	parse_str($_POST['data'],$data);
 	    
    if(!empty($data['action']))
    {
       
        //add new sidebar
        if($data['action'] == 'new')
        {
            if(strlen($data['name']) > 0)
            {
                $sidebars = magellan_gs('sidebars');
                if(!empty($sidebars))
                {
                    foreach($sidebars as $sidebar)
                    {
                        if($sidebar['name'] == $data['name'])
                        {
                            die(json_encode(array('status' => 'fail', 'msg' => 'name taken')));
                        }
                    }
                }
                
                $id = strtolower($data['name']);
                $id = preg_replace('/[^A-Za-z0-9-]/', '', $id);
                
                if(strlen($id) == 0)
                {
                    die(json_encode(array('status' => 'fail', 'msg' => 'invalid string')));
                }
                
                $sidebars[] = array(
                    'name' => $data['name'],
                    'id'   => $id,
                    'description' => '',
                    'class' => '',			
				);
                
                magellan_ss('sidebars', $sidebars);
                
                $item = '<li style="display: none;"><span>' . $data['name'] . '</span> <a href="#" class="delete-sidebar" id="' . $id .  '"></a>';
                
                die(json_encode(array('status' => 'ok', 'msg' => 'saved', 'html' => $item)));
            }
            else 
            {
                die(json_encode(array('status' => 'fail', 'msg' => 'name empty')));
            }
        }
        else if($data['action'] == 'delete')
        {
            if(!empty($data['id']))
            {
                $sidebars = magellan_gs('sidebars');
                if(!empty($sidebars))
                {
                    foreach($sidebars as $key => $sidebar)
                    {
                        if($sidebar['id'] == $data['id'])
                        {
                            unset($sidebars[$key]);
                            magellan_ss('sidebars', $sidebars);
                            die(json_encode(array('status' => 'ok', 'msg' => 'deleted')));
                        }
                    }
                }
                die(json_encode(array('status' => 'fail', 'msg' => 'sidebar id not found')));
            }
        }
        else if($data['action'] == 'manage')
        {    
            unset($data['action']);
            
            $page_sidebars = magellan_gs('page_sidebars');
            
            $templates = magellan_gs('page_types');
            foreach($data as $key => $value)
            {
                if(in_array($key, array_keys($templates)))
                {
                    $page_sidebars[$key] = $value;
                }
            }
            
            magellan_ss('page_sidebars', $page_sidebars);
            
            die(json_encode(array('status' => 'ok', 'msg' => 'Sidebars saved')));
            
        }   
    }
     	
}


function magellan_save_ad_locations()
{
    check_ajax_referer('magellan_save_ad_locations');
        
    parse_str($_POST['data'],$data);
    
    
    $locations_data = MAGELLAN_SETTINGS_INSTANCE()->admin_body['ads_manager']['ad_locations'];
    $locations = array_keys($locations_data);
    
    foreach($locations as $location)
    {
        $enabled = (!empty($data[$location . '_ad_enabled']) ? 'on' : 'off');
        $size_ads = (!empty($data[$location]) ? $data[$location] : false );
        $size = $ad_slug = false;
        
        $location_ad_data = array();
        
        if($size_ads)
        {
            $ad_keys = array_keys($size_ads);
            foreach($ad_keys as $ad)
            {
                $parts = explode('__', $ad);
                if(!empty($parts))
                {
                    $location_ad_data[] = array('ad_size' => $parts[0], 'ad_slug' => $parts[1]);
                }
            }
        }
        
        $result = array(
            'ad_enabled' => $enabled,
            'ads_linked' => $location_ad_data
        );
                
        magellan_ss($location, $result);
    }
    
    die(json_encode(array('status' => 'ok', 'msg' => 'Ads saved')));
    
}

function magellan_save_ads()
{
    check_ajax_referer('magellan_save_ads');
        
    $data = explode(';', $_POST['data']);
    array_pop($data); //remove last
    
    if(!empty($_POST['action']) && $_POST['action'] == 'magellan_save_ads')
    {
        foreach($data as $ad_group_key)
        {
            $ad_group_string = $_POST[$ad_group_key];
            parse_str($ad_group_string,$ad_group);
            
            $key_concat = implode(';', array_keys($ad_group));
            if(strpos($key_concat, '--') === false)    //if this is one item banner list
            {
                $new_group = array();
                foreach($ad_group as $key => $value)
                {
                    $start = strlen($ad_group_key . '__');
                    $new_key = substr($key, $start);
                    $new_group[$new_key] = $value;
                }
                
                magellan_ss($ad_group_key, $new_group);
            }
            else    //if this is multiple item banner list
            {
                $new_groups = array();
                foreach($ad_group as $key => $value)    //split content in new nicely separated arrays
                {
                    $parts = explode('--', $key);
                    $val_slug = $parts[1];
                    
                    $key_parts = explode('__', $parts[0]);                    
                    $key_slug = $key_parts[1];
                    
                    if(empty($new_groups[$key_slug]))
                    {
                        $new_groups[$key_slug] = array();
                    }
                    $new_groups[$key_slug][$val_slug] = $value;
                }
                
                foreach($new_groups as $key => $ng_item)
                {
                    if(!empty($ng_item['ad_slug']) && $ng_item['ad_slug']=='NA')
                    {
                        $new_groups[$key]['ad_slug'] = uniqid();
                    }
                }
                
                magellan_ss($ad_group_key, $new_groups);
            }
        }
    }
    
    die(json_encode(array('status' => 'ok', 'msg' => 'Ads saved')));
}


function magellan_import_page() {
    
    if(function_exists('get_demo_content_install_page_list'))
    {
        $magellan_install_page_list = get_demo_content_install_page_list();
        check_ajax_referer('magellan_import_page');

        parse_str($_POST['data']);
        $pageid = false;

        if(!empty($magellan_install_page_list[$group]) && !empty($magellan_install_page_list[$group][$key]))
        {
            $page = $magellan_install_page_list[$group][$key];
            $pageid = Magellan_Demo_Import::importSinglePage($page, $set_role); 
        }

        if($pageid)
        {
            die(json_encode(array('status' => 'ok', 'id' => $pageid)));
        }

        die(json_encode(array('status' => 'fail')));
    }
}

function magellan_output_theme_setting($option) {
	 
    if(!empty($option['value']))
    {
        $value = $option['value'];
    }
    else
    {
        $value = magellan_gs($option['slug']);
    }    
    
	$value = stripslashes($value);    
    
	$depend_class = $display_class = '';
	if(!empty($option['dependant'])) 	//if this option is dependant of other option
	{		
		$dep_slug = $option['dependant'];
        $dep_parts = explode(' ', $dep_slug);
        foreach($dep_parts as $dep_part)
        {
            $depend_class .= " depend_".$dep_part;
        }
		
		$display_class = 'depend_hide';
				
		if(magellan_gs($dep_slug)) 
		{
			if(magellan_gs($dep_slug) == 'on')
			{
				$display_class = '';
			}
		}		
	}
	
	$return = '<div class="form-item clearfix type-' . $option['type'] . ' ' . $depend_class . ' ' . $display_class. '">';
    
    $description = '';
    if(!empty($option['description']))
    {
        $description = '<span class="tooltip-1"><i>' . $option['description'] . '</i></span>';
    }
	
	 if($option['type'] == "textbox") {
	 	
        $return.= '<p class="label">' . $option['title'] . ' ' . $description . '</p>';
        
        if(!empty($option['warning']))
        {
            $return.= '<div class="row-wrapper-2">';
            $return.= '      <div class="row">';
            $return.= '          <input name="' . $option['slug'] . '" value="' . htmlspecialchars($value) . '" type="text" />';
            $return.= '      </div>';
            $return.= '     <div class="row">';
            $return.= '         <div class="info-message-1">' . $option['warning'] . '</div>';
            $return.= '     </div>';
            $return.= '</div>';
        }
        else
        {
            $return.= '<input name="' . $option['slug'] . '" value="' . htmlspecialchars($value) . '" type="text" />';
        }        
        
	 }
	  elseif($option['type'] == "textarea") {
          
		$return.= '<p class="label">' . $option['title'] . ' ' . $description . '</p>';
        
        if(!empty($option['warning']))
        {
            $return.= '<div class="row-wrapper-2">';
            $return.= '      <div class="row">';
            $return.= '          <textarea name="' . $option['slug'] . '" $value>' . stripslashes(htmlspecialchars($value)) . '</textarea>';
            $return.= '      </div>';
            $return.= '     <div class="row">';
            $return.= '         <div class="info-message-1">' . $option['warning'] . '</div>';
            $return.= '     </div>';
            $return.= '</div>';
        }
        else
        {
            $return.= '<textarea name="' . $option['slug'] . '">' . stripslashes(htmlspecialchars($value)) . '</textarea>';
        }
	 }
	 elseif($option['type'] == "checkbox") {
	
        $return.= '<p class="label">' . $option['title'] . ' ' . $description . '</p>';
        $return.= '<input name="' . $option['slug'] . '" id="' . $option['slug'] . '" type="checkbox" class="styled"';
        if($value == 'on') { $return.= ' checked="checked"'; }
		$return.= ' />'; 
		
		//$return.= '<div class="description"><label for="'.$option['slug'].'">';
		//$return.= $option['description'];
		//$return.= '</label></div>';
	
	 }
     elseif($option['type'] == "switcher") {
	
        $return.= '<p class="label">' . $option['title'] . ' ' . $description . '</p>';
		
        if(!empty($option['warning']))
        {
            $return.= '<div class="row-wrapper-2">';
            $return.= '      <div class="row">';
            $return.= '     <label class="switch-wrapper"><input name="' . $option['slug'] . '" id="' . $option['slug'] . '" type="checkbox" class="switch"';
            if($value == 'on') { $return.= ' checked="checked"'; }
            $return.= '     /></label>';           
            $return.= '      </div>';
            $return.= '     <div class="row">';
            $return.= '         <div class="info-message-1">' . $option['warning'] . '</div>';
            $return.= '     </div>';
            $return.= '</div>';
        }
        else
        {
            $return.= '<label class="switch-wrapper"><input name="' . $option['slug'] . '" id="' . $option['slug'] . '" type="checkbox" class="switch"';
            if($value == 'on') { $return.= ' checked="checked"'; }
            $return.= ' /></label>'; 
        }       
        
	
	 }
	 elseif($option['type'] == "select") {
	
        $return.= '<p class="label">' . $option['title'] . ' ' . $description . '</p>';
        
        if(!empty($option['warning']))
        {
            $return.= '<div class="row-wrapper-2">';
            $return.= '      <div class="row">';
            
            $return.= '<select name="'.$option['slug'].'" class="default" style="width: 347px;">';
		
            foreach($option['data'] as $key => $data) {
                $return.= '<option value="'. $key .'"';
                if($key == $value) { $return.= ' selected="selected"'; }
                $return.= '>' . $data . '</option>';
            }

            $return.= '</select>';
            
            $return.= '      </div>';
            $return.= '     <div class="row">';
            $return.= '         <div class="info-message-1">' . $option['warning'] . '</div>';
            $return.= '     </div>';
            $return.= '</div>';
        }
        else
        {
            $return.= '<select name="'.$option['slug'].'" class="default" style="width: 347px;">';
		
            foreach($option['data'] as $key => $data) {
                $return.= '<option value="'. $key .'"';
                if($key == $value) { $return.= ' selected="selected"'; }
                $return.= '>' . $data . '</option>';
            }

            $return.= '</select>';
        }
		
		//$return.= $option['description'];

	
	 }
	 elseif($option['type'] == "fileupload") {
	
        $return.= '<p class="label">' . $option['title'] . ' ' . $description . '</p>';
        $return.= '<input type="text" id="' .  $option['slug'] . '" name="' .  $option['slug'] . '" value="' . $value . '" class="styled image-id" />';
		$return.= '<a href="#" class="button-1 upload_image_button">' . esc_html__('Select file', 'magellan') . '</a>';
	 }
	 	 
	$return.= '</div>'; 
 	//$return.= '</div>';
 	
 	echo $return;
 }

function magellan_handle_admin_actions() 
{
    $action = (!empty($_GET['magellan_action']) ? $_GET['magellan_action'] : '' );
	if($action == 'install-auto-pages')
    {
        magellan_add_auto_pages();
        add_action('admin_notices', 'magellan_page_install_success_notification');
        remove_action('admin_notices', 'magellan_page_install_notification');
    }
    elseif($action == 'dismiss-auto-pages')
    {
        update_option('magellan_page_install_dismissed', true);
        remove_action('admin_notices', 'magellan_page_install_notification');
    }
    elseif($action == 'dismiss-thumb-regen')
    {
        update_option('magellan_page_thumb_regen_dismissed', true);
        remove_action('admin_notices', 'magellan_thumbnail_regenerate_notification');
    }
}


function magellan_demo_import_launcher()
{
    check_ajax_referer('magellan_demo_import_launcher');    
    parse_str($_POST['data'], $data);

    ob_start();
    
    if($data['step'] == 1)
    {
        $import = new Magellan_Demo_Import($data['demo']);
    }
    else
    {
        $import = get_option('magellan_import_' . $data['key']);
    }
    
    switch($data['step']) {
        case 1:
            $import->setupDefaultThumb();
            $import->importCategories();
			$import->importProductCategories();
            break;
        case 2:
            $import->importPages();
			$import->importHomeAndBlog();
            break;
        case 3:
            $import->importPosts();
			$import->importGalleries();
            break;
        case 4:
            $import->importProducts();
            break;
        case 5:
            $import->importMenus();
            $import->importConstellation();
            break;
        case 6:
            $import->importSidebarsWidgets();
            $import->importCustomSidebars();
            break;
        case 7:
            $import->modifyPreset();
            $import->saveImportLogToDB();
            break;
    }

    update_option('magellan_import_' . $data['key'], $import);
    
    echo json_encode(array('status' => 'ok'));

    $response = ob_get_contents();
    ob_end_clean();
    die($response);
}
 
function magellan_remove_newsletter_notification()
{
	check_ajax_referer('magellan_remove_newsletter_notification');    
    ob_start();
	
	update_option('magellan_hide_admin_newsletter', true);
	
	echo json_encode(array('status' => 'ok'));
	
	$response = ob_get_contents();
    ob_end_clean();
    die($response);
}

function magellan_extra_google_fonts()
{
	check_ajax_referer('magellan_extra_google_fonts');
	parse_str($_POST['data'], $data);
    ob_start();
	
	if(!empty($data['fonts']))
	{
		$fonts = explode(',', $data['fonts']);
		update_option('magellan_extra_google_fonts', $fonts);
	}
	else
	{
		update_option('magellan_extra_google_fonts', array());
	}
	
	$response = ob_get_contents();
    ob_end_clean();
    die($response);
}


function magellan_save_theme_registration()
{
	check_ajax_referer('magellan_save_theme_registration');
    ob_start();
	
	if(!empty($_POST['status']))
	{
		$status = $_POST['status'];
		
		if($status == 'on')
		{
			$url = MAGELLAN_THEME_REGISTER_API_URL;
			
			//validate variables
			$attrs = array('username', 'api_key', 'purchase_code', 'wp_url', 'tf_item_id');
			foreach($attrs as $attr)
			{
				if(!empty($_POST[$attr]))
				{
					$url = add_query_arg($attr, esc_attr($_POST[$attr]), $url);
				}
			}
						
			$response_json = wp_remote_get($url);
			$response_json = $response_json['body'];
			$response = json_decode($response_json, true);
						            
			//success. Save the result
			if($response['message'] == '200 OK')
			{	
				$data = array(
					'tf_username' => $_POST['username'],
					'tf_purchase_code' => $_POST['purchase_code'],
					'tf_api_key' => $_POST['api_key'],
					'status' => 'on'
				);		

				magellan_ss('theme_register', $data);
			}
			
			//output result
			echo $response_json;
		}
		elseif($status == 'off')
		{
			$url = MAGELLAN_THEME_DEREGISTER_API_URL;

			//validate variables
			$attrs = array('username', 'purchase_code');
			foreach($attrs as $attr)
			{
				if(!empty($_POST[$attr]))
				{
					$url = add_query_arg($attr, esc_attr($_POST[$attr]), $url);
				}
			}
			
			$response_json = wp_remote_get($url);
			$response_json = $response_json['body'];
			$response = json_decode($response_json, true);
			
			//success. Save the result
			if($response['message'] == '200 OK')
			{	
				$data = array(
					'tf_username' => $_POST['username'],
					'tf_purchase_code' => $_POST['purchase_code'],
					'tf_api_key' => $_POST['api_key'],
					'status' => 'off'
				);		

				magellan_ss('theme_register', $data);
			}
			
			//output result
			echo $response_json;
		}
	}
	
	$response = ob_get_contents();
    ob_end_clean();
    die($response);
}
?>