<?php 
class MagellanBannerLarge extends WP_Widget {

    var $widget_cssclass;
	var $widget_description;
	var $widget_idbase;
	var $widget_name;
    
	function __construct() {

		/* Widget variable settings. */
		$this->widget_cssclass = 'magellan_sidebar_banner';
		$this->widget_description = esc_html__( '300x300px banner', 'magellan' );
		$this->widget_idbase = 'magellan_sidebar_banner';
		$this->widget_name = esc_html__( 'Magellan Sidebar Banner', 'magellan' );

		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->widget_cssclass, 'description' => $this->widget_description );

		/* Create the widget. */
		parent::__construct('magellan_sidebar_banner', $this->widget_name, $widget_ops);

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	function widget($args, $instance) 
    {
		$cache = wp_cache_get('magellan_sidebar_banner', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();
		extract($args);

        $banner_string = isset( $instance['banner'] ) ? esc_attr( $instance['banner'] ) : '';
        $current_banners = explode('#', $banner_string);
        
        if(!empty($current_banners))
        {
            $rand = rand(0, sizeof($current_banners)-1);    //banner rotation
            $banner = $current_banners[$rand];
            $banner_data = magellan_get_banner_by_size_and_slug($banner, '300x300');

            if($banner_data)
            {
                ?>
                <?php echo $before_widget; ?>

				<?php
					$mobile_disabled = '';
					if(empty($banner_data['mobile_enabled']))
					{
						$mobile_disabled = 'mobile_disabled';
					}
				?>

                <div class="banner banner-300x300 <?php echo(esc_attr($mobile_disabled)); ?>">
					<?php if($banner_data['ad_type'] == 'banner') { ?>
							<a href="<?php echo esc_url($banner_data['ad_link']); ?>" target="_blank"><img src="<?php echo esc_url(magellan_banner_image_src($banner_data['ad_file'])); ?>" alt="<?php echo esc_attr($banner_data['ad_title']); ?>"></a>
					<?php } elseif($banner_data['ad_type'] == 'iframe') { ?>
						<iframe class="iframe-300x300" scrolling="no" src="<?php echo esc_url($banner_data['ad_iframe_src']); ?>"></iframe>                        
					<?php } elseif($banner_data['ad_type'] == 'shortcode') { ?>
						<?php echo do_shortcode($banner_data['shortcode']);  ?>
					<?php } else {
							echo stripslashes($banner_data['googlead_content']);
					} ?>    
				</div>

                <?php echo $after_widget; ?>
                <?php
            }
        }
        
        // Reset the global $the_post as this query will have stomped on it
        wp_reset_postdata();

        $cache[$args['widget_id']] = ob_get_flush();
        wp_cache_set('magellan_sidebar_banner', $cache, 'widget');

	}

	function update( $new_instance, $old_instance ) 
    {
		$instance = $old_instance;        
        $instance['banner'] = implode('#', array_keys($new_instance));

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['magellan_sidebar_banner']) )
			delete_option('magellan_sidebar_banner');

		return $instance;
	}

	function flush_widget_cache() 
    {
		wp_cache_delete('magellan_sidebar_banner', 'widget');
	}

	function form( $instance ) 
    {
		$banner_string = isset( $instance['banner'] ) ? esc_attr( $instance['banner'] ) : '';
        $current_banners = explode('#', $banner_string);
        
        $banners = magellan_get_active_banners('300x300');
        $ad_url = admin_url( 'admin.php?page=' . magellan_gs('theme_slug') . '-admin&view=ads_manager' );
        
        if(!empty($banners))
        {
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'banner' )); ?>"><?php esc_html_e( 'Banners:', 'magellan' ); ?></label><br/>
                
                <?php foreach($banners as $banner): ?>
                    <?php $checked = (in_array($banner['ad_slug'], $current_banners) ? 'checked' : ''); ?>
                    <input type="checkbox" id="<?php echo esc_attr($this->get_field_id( $banner['ad_slug'] )); ?>" name="<?php echo esc_attr($this->get_field_name( $banner['ad_slug'] )); ?>" <?php echo esc_attr($checked); ?> /><label for="<?php echo esc_attr($this->get_field_id( $banner['ad_slug'] )); ?>"><?php echo ucfirst($banner['ad_title']); ?></label><br/>
                <?php endforeach; ?>

            </p>
            <?php
        }
        else
        {            
            echo '<p>'
                . esc_html__('There are no active ads for this location. ', 'magellan')
                . esc_html__('Supports: ', 'magellan') .'300x300px ads. '
                . '<strong><a href="' . esc_url($ad_url) . '">' . esc_html__('Create a new Ad!', 'magellan')  . '</a></strong>'
            .'</p>';
        }
	}
}