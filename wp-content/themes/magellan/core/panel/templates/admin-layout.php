<?php

    $head = MAGELLAN_SETTINGS_INSTANCE()->admin_head;
    $body = MAGELLAN_SETTINGS_INSTANCE()->admin_body;
    
    if(!empty($head) && !empty($body))
    {
        $view = magellan_get($_GET, 'view', $head[key($head)]['slug']);   //get view; defaults to first element of header
        $view_title = $head[$view]['name'];
        $section_title = '';
        
        if($view == 'ads_manager')
        {
            $section_key = magellan_get($_GET, 'section', 'ads_manager');
        }
		elseif($view == 'setup')
		{
			$section_key = magellan_get($_GET, 'section', 'status');
		}
        else
        {
            $section_key = magellan_get($_GET, 'section', false);
        }
        
        if($section_key && !empty($head[$view]['children'][$section_key]))
        {
            $section_title = ' / ' . $head[$view]['children'][$section_key]['name'];
        }
        
        ?>
        <!-- BEGIN .main-control-panel-wrapper -->
		<div class="main-control-panel-wrapper">
			
			<?php magellan_sidebar(); ?>
			
			<!-- BEGIN .main-content -->
			<div class="main-content-wrapper">
                <div class="main-content view-<?php echo esc_attr($view); ?>">
				
					<!-- BEGIN .header -->
					<div class="header">
						<h2><?php echo magellan_gs('theme_name'); ?><span>/ <?php echo esc_html($view_title . $section_title); ?></span></h2>
					<!-- END .header -->
					</div>
                    
                    <!-- BEGIN .save-message-1 -->
					<div class="save-message-1 clearfix">
						<span>Your settings have been saved!</span>
						<a href="#" class="close"></a>
					<!-- END .save-message-1 -->
					</div>
					
					<!-- BEGIN .error-message-1 -->
					<div class="error-message-1 clearfix">
						<span>Your settings have not been saved!</span>
						<a href="#" class="close"></a>
					<!-- END .error-message-1 -->
					</div>
                    <?php
                        if(!empty($head[$view]['type']))
                        {
                            if(function_exists($head[$view]['type']))
                            {
                                $head[$view]['type']();
                            }
                        }
                    ?>
				</div>
			<!-- END .main-content -->
			</div>
		
		<!-- END .main-control-panel-wrapper -->
		</div>
        <?php
    }
    ?>