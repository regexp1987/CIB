<?php 
class MagellanSocialNewsletter extends WP_Widget {

    var $widget_cssclass;
	var $widget_description;
	var $widget_idbase;
	var $widget_name;
    
	function __construct() {

		/* Widget variable settings. */
		$this->widget_cssclass = 'newsletter';
		$this->widget_description = esc_html__( 'Display a newsletter signup form', 'magellan' );
		$this->widget_idbase = 'magellan_social_newsletter';
		$this->widget_name = esc_html__( 'Magellan Newsletter & Social Networks', 'magellan' );

		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->widget_cssclass, 'description' => $this->widget_description );

		/* Create the widget. */
		parent::__construct('magellan_social_newsletter', $this->widget_name, $widget_ops);

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	function widget($args, $instance) 
    {
		$cache = wp_cache_get('magellan_social_newsletter', 'widget');

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

		$title = apply_filters('widget_title', empty($instance['title']) ? esc_html__('Follow us', 'magellan') : $instance['title'], $instance, $this->id_base);
        $description = isset( $instance['description'] ) ? $instance['description'] : '';
        $show_newsletter = isset( $instance['show_newsletter'] ) ? $instance['show_newsletter'] : false;
		$show_social = isset( $instance['show_social'] ) ? $instance['show_social'] : false;
				
		?>
       
		<?php echo $before_widget;  ?>
			
			<div class="newsletter">
				<div class="title-default">
					<span><?php echo esc_html($title); ?></span>
				</div>

				<?php if($show_social) : ?>
					<div class="social">
						<?php get_template_part('theme/templates/social-icons'); ?>
					</div>
				<?php endif; ?>
				
				<?php echo wpautop($description); ?>

				<?php if($show_newsletter) : ?>
					<form action="<?php echo magellan_gs('newsletter_form_action'); ?>" method="<?php echo magellan_gs('newsletter_form_method'); ?>">
						<p class="input-wrapper"><input type="text" name="<?php echo magellan_gs('newsletter_email_field'); ?>" placeholder="<?php esc_html_e('E-mail address', 'magellan'); ?>"><input type="submit" value="<?php esc_html_e('Subscribe', 'magellan'); ?>" /></p>
					</form>
				<?php endif; ?>
			</div>
		
		<?php echo $after_widget; ?>
	
        <?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('magellan_social_newsletter', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) 
    {
				
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);        
        $instance['description'] = $new_instance['description'];        
        $instance['show_newsletter'] = isset( $new_instance['show_newsletter'] ) ? $new_instance['show_newsletter'] : false;
		$instance['show_social'] = isset( $new_instance['show_social'] ) ? $new_instance['show_social'] : false;
               
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['magellan_social_newsletter']) )
			delete_option('magellan_social_newsletter');

		return $instance;
	}

	function flush_widget_cache() 
    {
		wp_cache_delete('magellan_social_newsletter', 'widget');
	}

	function form( $instance ) 
    {           
        $title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $description   = isset( $instance['description'] ) ? $instance['description'] : ''; 
        $show_newsletter = isset( $instance['show_newsletter'] ) ? (bool) $instance['show_newsletter'] : false;
		$show_social = isset( $instance['show_social'] ) ? (bool) $instance['show_social'] : false;
        
        ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:' , 'magellan'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
            </p>    
                
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'description' )); ?>"><?php esc_html_e( 'Description:' , 'magellan'); ?></label>
                <textarea class="widefat" name="<?php echo esc_attr($this->get_field_name( 'description' )); ?>"><?php echo esc_html($description); ?></textarea>
            </p>            
            
            <p>
                <input class="checkbox" type="checkbox" <?php checked( $show_newsletter ); ?> id="<?php echo esc_attr($this->get_field_id( 'show_newsletter' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'show_newsletter' )); ?>" />
                <label for="<?php echo esc_attr($this->get_field_id( 'show_newsletter' )); ?>"><?php esc_html_e( 'Show newsletter form?', 'magellan' ); ?></label>
            </p>
			
			<p>
                <input class="checkbox" type="checkbox" <?php checked( $show_social ); ?> id="<?php echo esc_attr($this->get_field_id( 'show_social' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'show_social' )); ?>" />
                <label for="<?php echo esc_attr($this->get_field_id( 'show_social' )); ?>"><?php esc_html_e( 'Show social icons?', 'magellan' ); ?></label>
            </p>
        <?php
	}
}