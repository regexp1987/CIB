<?php    
    $head = MAGELLAN_SETTINGS_INSTANCE()->admin_head;
    $body = MAGELLAN_SETTINGS_INSTANCE()->admin_body;
    $view = magellan_get($_GET, 'view', $head[key($head)]['slug']);   //get view; defaults to first element of header
?>
    
    <form name="settings" class="no-submit">
            
        <?php
            if(!empty($body[$view]))
            {
                foreach($body[$view] as $key => $section)
                {
                    echo '<!-- BEGIN .section-item -->
                            <div class="section-item clearfix" id="' . $key . '">';

                    echo '<h3>' . $head[$view]['children'][$key]['name'] . '</h3>';

					if(!empty($head[$view]['children'][$key]['description']))
					{
						echo '<p class="section-description">' . $head[$view]['children'][$key]['description'] . '</p>';
					}
					
                    foreach($section as $option)
                    {
                        magellan_output_theme_setting($option);
                    }

                    echo '<!-- END .section-item -->
                          </div>';
                }
            }
        ?>
					
        <!-- BEGIN .section-save -->
        <div class="section-save">
            <a href="#" id="save" class="button-2">Save changes</a>
        <!-- END .section-save -->
        </div>
    </form>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            jQuery('#save').click(function(e){		//option update
                var result = jQuery('form[name=settings]').serializeArray();

                result = result.concat(
                    jQuery('form[name=settings] input[type=checkbox]:not(:checked)').map(
                        function() {
                            return {"name": this.name, "value": 'off'}
                        }).get()
                );

                var admin_ajax = '<?php echo site_url() .'/wp-admin/admin-ajax.php'; ?>';
                var nonce = '<?php echo wp_create_nonce('magellan_save_settings') ?>';
                var data = { action: 'magellan_save_settings', _ajax_nonce: nonce, data: result};

                jQuery.ajax({
                    type: "POST",
                    url: admin_ajax,
                    traditional: true,
                    dataType: 'json',
                    data: { action: 'magellan_save_settings', _ajax_nonce: nonce, data: jQuery.param(result) },
                    success: function(msg){
                        admin.show_save_result(msg);
                    }
                });

                e.preventDefault();
            });
            
            
            jQuery('#save-preset').click(function(e){		//option update

                var preset = jQuery('select[name=preset]').val();
                
                var admin_ajax = '<?php echo site_url() .'/wp-admin/admin-ajax.php'; ?>';
                var nonce = '<?php echo wp_create_nonce('magellan_load_style_preset') ?>';
                var data = { action: 'magellan_load_style_preset', _ajax_nonce: nonce, preset: preset};

                jQuery.ajax({
                    type: "POST",
                    url: admin_ajax,
                    traditional: true,
                    dataType: 'json',
                    data: data,
                    success: function(msg){
                        admin.show_save_result(msg);
                    }
                });

                e.preventDefault();
            });
            
            
        });
        
        var global_image_url = '<?php echo site_url() .'/wp-admin/admin-ajax.php'; ?>?action=magellan_upload_image&_ajax_nonce=<?php echo wp_create_nonce('magellan_upload_image') ?>';
        
    </script>