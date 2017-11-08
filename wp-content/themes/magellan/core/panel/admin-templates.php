<?php

function magellan_sidebar()
{
    magellan_get_admin_template('sidebar');
}

function magellan_option_list()
{
    magellan_get_admin_template('option-list');
}

function magellan_sidebar_manager()
{
    magellan_get_admin_template('sidebar-manager');
}

function magellan_ads_manager() 
{   
    magellan_get_admin_template('ads-manager');
}

function magellan_admin()
{
    magellan_get_admin_template('admin-layout');
}

function magellan_support_iframe()
{
    ?>
        <iframe class="support-iframe" src="<?php echo magellan_gs('support_url') ?>" height="100%" border="none"></iframe>
    <?php
}

function magellan_google_fonts()
{
	magellan_get_admin_template('google-fonts');
}

function magellan_register_theme()
{
	magellan_get_admin_template('register-theme');
}


function magellan_setup_section() 
{
    $section_key = magellan_get($_GET, 'section', 'status');
            
    if($section_key == 'demo_import')
    {
        magellan_get_admin_template('demo-import');
    }
    elseif($section_key == 'backup_reset')
    {
        magellan_get_admin_template('backup-reset');
    }
    elseif($section_key == 'load_preset')
    {
        magellan_get_admin_template('load-preset');
    }
    elseif($section_key == 'install_pages')
    {
        magellan_get_admin_template('install-pages');
    }
    else
    {
        magellan_get_admin_template('status');
    }
}
?>