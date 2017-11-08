<?php 
class MagellanAbout extends WP_Widget {

    var $widget_cssclass;
	var $widget_description;
	var $widget_idbase;
	var $widget_name;
    
	function __construct() {

		/* Widget variable settings. */
		$this->widget_cssclass = 'about-social';
		$this->widget_description = esc_html__( 'Display your site name, description and social links', 'magellan' );
		$this->widget_idbase = 'magellan_about';
		$this->widget_name = esc_html__( 'Magellan About', 'magellan' );

		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->widget_cssclass, 'description' => $this->widget_description );

		/* Create the widget. */
		parent::__construct('magellan_about', $this->widget_name, $widget_ops);

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	function widget($args, $instance) 
    {
		$cache = wp_cache_get('magellan_about', 'widget');

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

        $description = isset( $instance['description'] ) ? $instance['description'] : '';
        $show_social = isset( $instance['show_social'] ) ? $instance['show_social'] : false;

?>
       <?php echo $before_widget; ?>
       
			<div class="logo-1">
				<a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
				<p><?php bloginfo('description'); ?></p>
			</div>

			<div class="about">
				<?php echo wpautop($description); ?>
			</div>
			<div class="social">
				<?php get_template_part( 'theme/templates/social-icons'); ?>
			</div>

        <?php echo $after_widget; ?>
        <?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('magellan_about', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) 
    {
		$instance = $old_instance;
        $instance['description'] = $new_instance['description'];        
        $instance['show_social'] = $new_instance['show_social'];
               
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['magellan_about']) )
			delete_option('magellan_about');

		return $instance;
	}

	function flush_widget_cache() 
    {
		wp_cache_delete('magellan_about', 'widget');
	}

	function form( $instance ) 
    {           
        $description   = isset( $instance['description'] ) ? $instance['description'] : ''; 
		$show_social = isset( $instance['show_social'] ) ? (bool) $instance['show_social'] : false;
        
        ?>     
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'description' )); ?>"><?php esc_html_e( 'Description:' , 'magellan'); ?></label>
                <textarea class="widefat" name="<?php echo esc_attr($this->get_field_name( 'description' )); ?>"><?php echo esc_html($description); ?></textarea>
            </p>            
            
            <p><input class="checkbox" type="checkbox" <?php checked( $show_social ); ?> id="<?php echo esc_attr($this->get_field_id( 'show_social' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'show_social' )); ?>" />
    		<label for="<?php echo esc_attr($this->get_field_id( 'show_social' )); ?>"><?php esc_html_e( 'Show social icons?', 'magellan' ); ?></label></p>
        <?php
	}
}