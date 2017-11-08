<?php

function magellan_version_migrate()
{
    $prev_version = get_option('magellan_previous_' . MAGELLAN_THEME_DOMAIN .'_version', '1.0');   //for now use 1.0.4 as prev version
    $migrated_version = get_option('magellan_' . MAGELLAN_THEME_DOMAIN .'_migrated_version', $prev_version);
    $theme = wp_get_theme();
    $version = $theme->get('Version');

    //Only run in admin
    if( is_admin())
    {
        if(!empty($version_index[$version]) && !empty($version_index[$migrated_version]))
        {

        }
    }
}