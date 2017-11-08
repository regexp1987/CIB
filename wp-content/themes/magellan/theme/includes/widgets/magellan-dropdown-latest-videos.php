<?php 
class MagellanDropdownLatestVideos extends WP_Widget {

    var $widget_cssclass;
	var $widget_description;
	var $widget_idbase;
	var $widget_name;
    
	function __construct() {

		/* Widget variable settings. */
		$this->widget_cssclass = 'magellan_dropdown_latest_videos';
		$this->widget_description = esc_html__( 'Row of latest videos', 'magellan' );
		$this->widget_idbase = 'magellan_dropdown_latest_videos';
		$this->widget_name = esc_html__( 'Magellan Dropdown Latest Videos', 'magellan' );

		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->widget_cssclass, 'description' => $this->widget_description );

		/* Create the widget. */
		parent::__construct('magellan_dropdown_latest_videos', $this->widget_name, $widget_ops);

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	function widget($args, $instance) 
    {
		$cache = wp_cache_get('magellan_dropdown_latest_videos', 'widget');

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
        		
        global $post;
		
        $title = apply_filters('widget_title', empty($instance['title']) ? esc_html__('Latest videos', 'magellan') : $instance['title'], $instance, $this->id_base);
        
		$unique_id = uniqid();

		/* Post List Query */
		$items = magellan_get_posts_by_meta('image_size', array('video_autoplay', 'video'), 4, 1);
		
		//get link
		$view_all = '#';
				
		if(!empty($items)) : ?>

			<?php echo $before_widget; ?>

			<div class="title-default">
				<span><?php echo esc_html($title); ?></span>
			</div>

			<div class="container post-block post-video">
				<div class="row">
												
				<?php foreach($items as $post) : ?>

					<div class="col-md-3">
						<?php 
						setup_postdata($post);
						get_template_part('theme/templates/post-slider-item');
						?>
					</div>

				<?php endforeach; ?>
					
				</div>
			</div>

			<?php /*
			<div class="container post-block btn-more">
				<a href="<?php echo esc_url($view_all); ?>" class="btn btn-default btn-dark"><?php esc_html_e('View all', 'magellan') ?></a>
			</div> */ ?>

			<?php echo $after_widget; ?>

		<?php endif; ?>
			
        <?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('magellan_dropdown_latest_videos', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) 
    {
		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);
		
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['magellan_dropdown_latest_videos']) )
			delete_option('magellan_dropdown_latest_videos');

		return $instance;
	}

	function flush_widget_cache() 
    {
		wp_cache_delete('magellan_dropdown_latest_videos', 'widget');
	}

	function form( $instance ) 
    {
        $title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : 'Featured news';
        
        ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:' , 'magellan'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
            </p>    
        
		<?php
	}
}