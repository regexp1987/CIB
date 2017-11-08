<!-- Sidebar -->
<div class="sidebar">
    <?php
    $page_sidebars = magellan_gs('page_sidebars');
    $sidebar_ids = array();
    $template = magellan_get_sidebar_page_type();
	
    if(is_page())
    {
        $custom_sidebar = get_post_meta( get_the_ID(), $key = 'custom_sidebar', $single = true );
        $sidebars =  magellan_get_sidebars();
        if(!empty($sidebars))
        {
            foreach($sidebars as $sidebar)
            {
                $sidebar_ids[] = $sidebar['id'];
            }
        }
    }
    	
    if(
        is_page()
        &&
        $custom_sidebar != 'global'
        &&
        in_array($custom_sidebar, $sidebar_ids)
    )
    {
        if ( is_active_sidebar( $custom_sidebar ) ) 
		{
			dynamic_sidebar($custom_sidebar);
		}
    }
    elseif(!empty($page_sidebars) && in_array($template, array_keys($page_sidebars)) && !empty($page_sidebars[$template]))
    {
		if ( is_active_sidebar( $page_sidebars[$template] ) ) 
		{
			dynamic_sidebar($page_sidebars[$template]);
		}
    }
    else
    {
		if ( is_active_sidebar( 'default_sidebar' ) ) 
		{
			dynamic_sidebar('default_sidebar');
		}
    }
    
    ?>
</div>