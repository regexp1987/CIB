<div class="color-preset-list clearfix">
    <?php
    $editor_url = get_admin_url() . 'customize.php?return=' . get_admin_url() . 'admin.php?page=' . magellan_gs('theme_slug') . '-admin&view=setup&section=load_preset'; ;
    ?>
    <p>Color presets are a collection of pre-defined settings for themes colors, backgrounds, fonts etc. that can be loaded with a single click. You can also edit these settings one-by-one in <a href="<?php echo esc_url($editor_url); ?>">Visual Editor</a>.</p>
    <p><strong>Warning: Loading a preset will override all current Visual Editor settings!</strong></p>
    <?php
    $presets = magellan_gs('presets', false);
    $meta = magellan_gs('presets_meta', false);
    $install_url = get_admin_url() . 'admin.php?page=' . magellan_gs('theme_slug') . '-admin&view=setup&section=load_preset';
    
    if(!empty($presets))
    {
        $c = 0;
        foreach($presets as $key => $preset)
        {
            if(!empty($meta[$key]))
            {
                $item_meta = $meta[$key];
                
                ?>
                    <div class="preset-item" id="<?php echo esc_attr($key); ?>">
                        <div class="preset-image"><img src="<?php echo MAGELLAN_IMG_URL . $item_meta['image']; ?>" alt="" /></div>
                        <div class="page-description">
							<h3><?php echo esc_html($item_meta['name']); ?></h3>
							<div class="form-item clearfix">
								<a href="<?php echo esc_url($install_url); ?>&preset=<?php echo esc_attr($key); ?>" class="button-2">Load preset</a>
							</div>
						</div>
                    </div>
                <?php
                if($c%2 != 1)
                {
                    echo '<div class="preset-item-divider"></div>';
                }
                $c++;
            }
        }
    }
    else
    {
        echo '<p>No presets found</p>';
    }
    ?>
</div>
<script type="text/javascript">
    jQuery('.preset-item .button-2').click(function(e){		//option update

        var preset = jQuery(this).parents('.preset-item').attr('id');

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
</script>