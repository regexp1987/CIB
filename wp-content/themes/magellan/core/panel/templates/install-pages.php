<?php if(class_exists('Magellan_Magellan_Extension')) : ?>


        <p class="page-install-desc"><?php esc_html_e('Use page installer to re-create pages from Magellan theme demo in your site.', 'magellan'); ?><br/>
        <strong><?php esc_html_e('This will only set the content for the page', 'magellan'); ?></strong> <?php esc_html_e('- color settings etc will not be affected', 'magellan'); ?>.</p>
    <?php
        if(function_exists('get_demo_content_install_page_list'))
        {
            $magellan_install_page_list = get_demo_content_install_page_list();
        }
        if(function_exists('get_demo_content_install_page_list_desc'))
        {
            $magellan_install_page_list_desc = get_demo_content_install_page_list_desc();
        }


    if(!empty($magellan_install_page_list))
    {    
        ?><div class="import-tabs"><?php
        $c=0;
        foreach($magellan_install_page_list as $gkey => $group)
        {
            $class = '';
            if($c==0)
            {
                $class = 'active';
            }
            echo '<div class="tab-item ' . $class . '"><a href="#tab-' . esc_attr($gkey) . '">' . $magellan_install_page_list_desc[$gkey] . '</a></div>';
            $c++;
        }
        ?></div><?php

        ?><div class="install-page-list"><?php
        foreach($magellan_install_page_list as $gkey => $group)
        {
            ?>
            <div class="page-group" id="tab-<?php echo esc_attr($gkey); ?>">
            <?php
                foreach($group as $key => $page)
                {
                    ?>     
                    <form name="<?php echo esc_attr($key); ?>">
                        <input type="hidden" name="group" value="<?php echo esc_attr($gkey); ?>"/>
                        <input type="hidden" name="key" value="<?php echo esc_attr($key); ?>"/>

                        <div class="install-page-item">
                            <div class="image"><img src="<?php echo MAGELLAN_IMG_URL . $page['image']; ?>" alt="" /></div>
                            <div class="page-description">
                                <h3><?php echo esc_html($page['name']); ?></h3>
                                <p><?php echo magellan_kses_widget_html_field($page['description']); ?>&nbsp;</p>

                                <div class="import-button">
                                    <a href="#" class="button-2">Create page</a>
                                </div>

                                <?php if($page['role'] !== '')
                                {
                                    ?>
                                    <div class="page-role">

                                    <input type="checkbox" class="styled" id="<?php echo esc_attr($key); ?>_set_role" name="set_role" />
                                    <?php if($page['role'] == 'home') : ?>
                                    <label for="<?php echo esc_attr($key); ?>_set_role">Set as home page</label>
                                    <?php elseif($page['role'] == 'blog'): ?>
                                    <label for="<?php echo esc_attr($key); ?>_set_role">Set as blog</label>
                                    <?php endif; ?>

                                    </div>
                                    <?php
                                }
                                ?>

                            </div>
                        </div>
                    </form>
                    <?php
                }
            ?>
            </div>
            <?php
        }
        ?></div><?php
    }
    else
    {
        echo '<p>no pages found</p>';
    }

    if(!empty($_GET['magellan_action']) && !empty($_GET['p']) && $_GET['magellan_action'] == 'export_page')
    {
        $params = array('p' => intval($_GET['p']));
        echo '<div style="width: 100%; clear: both; word-break: break-all;">';
            echo base64_encode(serialize(Magellan_Demo_Export::exportPages($params)));
        echo '</div>';
    }

    ?>
    <script type="text/javascript">
    jQuery(document).ready(function () {

        jQuery('.install-page-item .import-button a').click(function(){
            var data = jQuery(this).parents('form').serialize();

            var admin_ajax = '<?php echo site_url().'/wp-admin/admin-ajax.php'; ?>';
            var nonce = '<?php echo wp_create_nonce('magellan_import_page') ?>';
            var data = { action: 'magellan_import_page', _ajax_nonce: nonce, data: data};
            var page_url = '<?php echo site_url().'/wp-admin/post.php?action=edit&message=6&post='; ?>';

            jQuery.post(admin_ajax, data ,function(msg){

                if(msg.status == 'ok')
                {
                    window.location = page_url + msg.id;
                }

            }, 'json');

            return false;
        });
    });
    </script>
<?php else:
    echo '<div class="section-item"><h4>' . esc_html__('Please active the "Planetshine Magellan Theme Extension" plugin to enable the page install', 'magellan') . '</h4></div>';
endif; ?>