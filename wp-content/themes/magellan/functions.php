<?php
	require_once(get_template_directory() . '/core/' . 'init.php');	//initialize framework
	require_once(get_template_directory() . '/theme/' . 'theme.php');	//initialize theme
    
	//if child theme has defined an overriding class, load that instead
	if(class_exists('MagellanChildTheme'))
	{
        new MagellanChildTheme();
	}
	else
	{
		new Magellan();	//initialize theme, all its action and filters
	}
	
/*--------------------------- CUSTOM FUNCTIONS --------------------------*/
/*	Add your custom functions below											 */
/*-----------------------------------------------------------------------*/

    if ( ! isset( $content_width ) )
    {
        $content_width = 680;
    }
    
?>