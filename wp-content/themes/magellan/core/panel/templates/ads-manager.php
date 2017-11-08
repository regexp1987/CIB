<?php
    $head = MAGELLAN_SETTINGS_INSTANCE()->admin_head;
    $body = MAGELLAN_SETTINGS_INSTANCE()->admin_body;
    $view = magellan_get($_GET, 'view', $head[key($head)]['slug']);   //get view; defaults to first element of header
    $section = magellan_get($_GET, 'section', 'ads_manager');   //get view; defaults to first element of header
    
    if($section == 'ads_manager')
    {
        magellan_get_admin_template('ads-edit');
    }
    else
    {
        magellan_get_admin_template('ads-locations');
    }
?>