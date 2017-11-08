<?php 
class MagellanTagCloud extends WP_Widget {

    var $widget_cssclass;
	var $widget_description;
	var $widget_idbase;
	var $widget_name;
    
	function __construct() {

		/* Widget variable settings. */
		$this->widget_cssclass = 'magellan_tag_cloud';
		$this->widget_description = esc_html__( 'Custom tag cloud for Magellan', 'magellan' );
		$this->widget_idbase = 'magellan_tag_cloud';
		$this->widget_name = esc_html__( 'Magellan Tag Cloud', 'magellan' );

		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->widget_cssclass, 'description' => $this->widget_description );

		/* Create the widget. */
		parent::__construct('magellan_tag_cloud', $this->widget_name, $widget_ops);

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	function widget($args, $instance) 
    {
		$cache = wp_cache_get('magellan_tag_cloud', 'widget');

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

		$title = apply_filters('widget_title', empty($instance['title']) ? esc_html__('Magellan Tag Cloud', 'magellan') : $instance['title'], $instance, $this->id_base);
        $count = isset( $instance['count'] ) ? $instance['count'] : 20;
        
        
        $term_args = array(
            'orderby' => 'count',
            'order' => 'DESC',
            'number' => intval($count)
        );
        $post_tax_tags = get_terms('post_tag', $term_args);
		?>
        
		<?php echo $before_widget; ?>
			
			<div class="post-block widget-tags">
				<div class="title-default">
					<span><?php echo esc_html($title); ?></span>
				</div>
				<div class="tags">
					<?php
                        foreach($post_tax_tags as $pc)
                        {
                            echo '<a href="' . get_tag_link($pc->term_id) . '">' . $pc->name . '<span>' . $pc->count . '</span></a>';
                        }
                        ?>
				</div>
			</div>
			
		<?php echo $after_widget; ?>

        <?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('magellan_tag_cloud', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) 
    {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
        $instance['count'] = strip_tags($new_instance['count']);
        
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['magellan_tag_cloud']) )
			delete_option('magellan_tag_cloud');

		return $instance;
	}

	function flush_widget_cache() 
    {
		wp_cache_delete('magellan_tag_cloud', 'widget');
	}

	function form( $instance ) 
    {
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $count = isset( $instance['count'] ) ? esc_attr( $instance['count'] ) : 20;
                
        ?>
        <p><label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:', 'magellan' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
                
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'count' )); ?>"><?php esc_html_e( 'Max count:' , 'magellan'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'count' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'count' )); ?>" type="text" value="<?php echo esc_attr($count); ?>" />
        </p>
        <?php
	}
}