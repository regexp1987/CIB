<?php
/***** Background *****/

$default = str_replace(array('https:', 'http:'), '', magellan_gs('background_image'));
$mod = get_theme_mod('background_image', $default);
if ( ! empty( $mod ) ) 
{
	magellan_generate_css('body', 'background-image', 'background_image', 'url(', ') !important' );
}
else
{
	echo 'body { background-image: none; }' . "\n";
}

//repeat
magellan_generate_css('body', 'background-repeat', 'background_repeat', '', '!important' );

//if repeat is off, then stretch the image
$mod = get_theme_mod('background_repeat', magellan_gs('background_repeat'));
if($mod == 'no-repeat')
{
    echo 'body { background-size: cover !important; background-position: top center; } '. "\n";
}

magellan_generate_css('body', 'background-attachment', 'background_attachment', '', '!important' );


/* header offset */
$mod = get_theme_mod('enable-header-offset', magellan_gs('enable-header-offset'));
if($mod)
{
    $use_mobile = get_theme_mod('enable-header-offset-mobile', magellan_gs('enable-header-offset-mobile')); 
    if(!$use_mobile)
    {
        echo '@media only screen and (min-width: 768px) {';
    }
    
    $height = get_theme_mod('header-offset-height', magellan_gs('header-offset-height'));
    $top = get_theme_mod('header-offset-padding-top', magellan_gs('header-offset-padding-top'));
    $bottom = get_theme_mod('header-offset-padding-bottom', magellan_gs('header-offset-padding-bottom'));
    
    $height = intval($top) + intval($bottom) + intval($height);
    
    $top_offset  = $top_offset_mobile = $height + 40; //height plus margin
    if(magellan_gs('show_header_dock') == 'on')
    {
        $top_offset += 70;
        $top_offset_mobile += 90;
    }
    
    $top_offset += 38;  //menu half height
    
    echo '.header { height: ' . esc_attr($height) . 'px; padding-top: ' . esc_attr($top) . '; padding-bottom: ' . esc_attr($bottom) . '; }'. "\n";
    echo 'body.header-offset:before { position: absolute !important; top: ' . esc_attr($top_offset) . 'px; }'. "\n";
       
    if(!$use_mobile)
    {
        echo '}';
    }
    else
    {
        echo '@media only screen and (max-width: 767px) { body.header-offset:before { top: ' . esc_attr($top_offset_mobile) . 'px; } }'. "\n";
    }
}
?>