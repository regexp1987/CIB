<?php

/*-----------------------------------------------------------------------------------*/
/* Define Constants */
/*-----------------------------------------------------------------------------------*/

    define('MAGELLAN_URL', get_template_directory_uri() . '/');
	define('MAGELLAN_THEME_URL', get_template_directory_uri() . '/theme/');
    define('MAGELLAN_ADMIN_ASSET_URL', get_template_directory_uri() . '/core/panel/assets/');
	
	define('MAGELLAN_IMG_URL', MAGELLAN_THEME_URL . 'assets/images/');
	define('MAGELLAN_JS_URL', MAGELLAN_THEME_URL . 'assets/js/');
	define('MAGELLAN_CSS_URL', MAGELLAN_THEME_URL . 'assets/css/');
    define('MAGELLAN_LESS_URL', MAGELLAN_THEME_URL . 'assets/less/');
    
    $upload_dir = wp_upload_dir();
    define('MAGELLAN_UPLOAD_URL',  $upload_dir['baseurl'] . '/magellan/');
    define('MAGELLAN_UPLOAD_PATH',  $upload_dir['basedir'] . '/magellan/');
    
    define('MAGELLAN_IS_CHILD', is_child_theme());

	define('MAGELLAN_THEME_REGISTER_API_URL', 'http://planetshine.net/api/register/');
	define('MAGELLAN_THEME_DEREGISTER_API_URL', 'http://planetshine.net/api/deregister/');
	define('MAGELLAN_TF_ITEM_ID', '15401147');
    
    if(MAGELLAN_IS_CHILD)
    {
        define('MAGELLAN_CHILD_PATH', get_stylesheet_directory());
        define('MAGELLAN_CHILD_THEME_PATH', get_stylesheet_directory() . '/theme/');
        define('MAGELLAN_CHILD_TEMPLATE_PATH', MAGELLAN_CHILD_THEME_PATH . 'templates/');
    }
	
/*-----------------------------------------------------------------------------------*/
/* Load the required Framework Files */
/*-----------------------------------------------------------------------------------*/

	include_once( get_template_directory() . '/core/' . 'shared-functions.php' );
	include_once( get_template_directory() . '/core/panel/' . 'admin-functions.php' );
	include_once( get_template_directory() . '/core/panel/' . 'admin-templates.php' );
    include_once( get_template_directory() . '/core/' . 'template-functions.php' );
	include_once( get_template_directory() . '/core/lib/' . 'settings.class.php' );
    include_once( get_template_directory() . '/core/lib/' . 'tgmPluginActivation.class.php' );
    include_once( get_template_directory() . '/core/lib/' . 'wpBootstrapNavwalker.class.php' );
	include_once( get_template_directory() . '/core/lib/' . 'lessc.inc.php' );
    include_once( get_template_directory() . '/core/lib/' . 'wp-less.class.php' );
	include_once( get_template_directory() . '/core/lib/' . 'envatoProtectedAPI.class.php' );
	include_once( get_template_directory() . '/core/lib/' . 'envatoWpThemeUpdater.class.php' );
	include_once( get_template_directory() . '/theme/includes/' . 'google-fonts.php');
	include_once( get_template_directory() . '/theme/includes/' . 'settings.php' );
    include_once( get_template_directory() . '/theme/plugins/' . 'versions.php' );
    include_once( get_template_directory() . '/theme/' . 'migrate.php');	//theme version change migrate
	
    
/*-----------------------------------------------------------------------------------*/
/* Load settings */
/*-----------------------------------------------------------------------------------*/

	$_SETTINGS = new Magellan_Settings();

/*-----------------------------------------------------------------------------------*/
/* Constants */
/*-----------------------------------------------------------------------------------*/
	
	define('MAGELLAN_THEME_DOMAIN', magellan_gs('theme_slug'));

/*-----------------------------------------------------------------------------------*/
/* Add actions */
/*-----------------------------------------------------------------------------------*/
    
if( is_admin())
{
	magellan_init_updater();
	
	add_action('admin_menu', 'magellan_load_admin_menus');
    add_action('admin_enqueue_scripts', 'magellan_load_admin_styles');
    add_action('admin_enqueue_scripts', 'magellan_load_admin_scripts');
	add_action('wp_ajax_magellan_save_sidebar', 'magellan_save_sidebar');
	add_action('wp_ajax_magellan_save_settings', 'magellan_save_settings');
    add_action('wp_ajax_magellan_load_style_preset', 'magellan_load_style_preset');
    add_action('wp_ajax_magellan_save_ads', 'magellan_save_ads');
    add_action('wp_ajax_magellan_save_ad_locations', 'magellan_save_ad_locations');
    add_action('wp_ajax_magellan_import_settings', 'magellan_import_settings');
    add_action('wp_ajax_magellan_reset_settings', 'magellan_reset_settings');
    add_action('wp_ajax_magellan_upload_image', 'magellan_upload_image');
	add_action('wp_ajax_magellan_remove_newsletter_notification', 'magellan_remove_newsletter_notification');
	add_action('wp_ajax_magellan_extra_google_fonts', 'magellan_extra_google_fonts');
	add_action('wp_ajax_magellan_save_theme_registration', 'magellan_save_theme_registration');
    add_action('wp_loaded', 'magellan_version_migrate');
    add_action('admin_notices', 'magellan_handle_admin_actions', 5);
	add_action('wp_ajax_magellan_demo_import_launcher', 'magellan_demo_import_launcher');
    add_action('wp_ajax_magellan_import_page', 'magellan_import_page');
    add_action('admin_notices', 'magellan_page_install_notification');
//	add_action('admin_notices', 'magellan_thumbnail_regenerate_notification');
    add_action('after_switch_theme', 'magellan_log_theme_version');
	//add_action('after_switch_theme', 'magellan_redirect_to_status', 999);
}

?>