<?php 
class MagellanTweetsEmbed extends WP_Widget {

    var $widget_cssclass;
	var $widget_description;
	var $widget_idbase;
	var $widget_name;
    
	function __construct() {

		/* Widget variable settings. */
		$this->widget_cssclass = 'twitter';
		$this->widget_description = esc_html__( 'Display latest tweets.', 'magellan' );
		$this->widget_idbase = 'magellan_tweets_embed';
		$this->widget_name = esc_html__( 'Magellan Twitter Feed Embed', 'magellan' );

		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->widget_cssclass, 'description' => $this->widget_description );

		/* Create the widget. */
		parent::__construct('magellan_tweets_embed', $this->widget_name, $widget_ops);

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	function widget($args, $instance) 
    {
		$cache = wp_cache_get('magellan_tweets_embed', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( ! isset( $args['widget_embed'] ) )
			$args['widget_embed'] = $this->id;

		if ( isset( $cache[ $args['widget_embed'] ] ) ) {
			echo $cache[ $args['widget_embed'] ];
			return;
		}

		ob_start();
		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? esc_html__('Magellan Twitter feed', 'magellan') : $instance['title'], $instance, $this->id_base);
		$widget_embed = isset( $instance['widget_embed'] ) ? $instance['widget_embed'] : '';
        $color = isset( $instance['color'] ) ? $instance['color'] : false;
		
		$twitter_data = [];
		$twitter_data['class'] = 'class="twitter-timeline"';
		$twitter_data['limit'] = 'data-tweet-limit="4"';
		$twitter_data['link_color'] = 'data-link-color="'. esc_attr(get_theme_mod('accent-color-1', magellan_gs('accent-color-1'))) .'"';
		$twitter_data['theme'] = 'data-theme="'. esc_attr($color) . '"';
		$twitter_data['chrome'] = 'data-chrome="noheader nofooter noscrollbar noborders transparent"';
		$twitter_data['dnt'] = 'data-dnt="true"';
		$twitter_atts = implode(" ", $twitter_data);
		?>

		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
        
            <div class="twitter-feed embed scrollable">
				<?php echo str_replace($twitter_data['class'], $twitter_atts, $widget_embed); ?> 
            </div>

		<?php echo $after_widget; ?>
        <?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		$cache[$args['widget_embed']] = ob_get_flush();
		wp_cache_set('magellan_tweets_embed', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) 
    {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
        $instance['color'] = strip_tags($new_instance['color']);
		$instance['widget_embed'] = $new_instance['widget_embed'];

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['magellan_tweets_embed']) )
			delete_option('magellan_tweets_embed');

		return $instance;
	}

	function flush_widget_cache() 
    {
		wp_cache_delete('magellan_tweets_embed', 'widget');
	}

	function form( $instance ) 
    {
		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$widget_embed = isset( $instance['widget_embed'] ) ? esc_attr( $instance['widget_embed'] ) : '';
        $color = isset( $instance['color'] ) ? esc_attr( $instance['color'] ) : 'dark';
?>
		<p><label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:', 'magellan' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<p><label for="<?php echo esc_attr($this->get_field_id( 'widget_embed' )); ?>"><?php esc_html_e( 'Embed Code:', 'magellan' ); ?></label>
		<textarea class="widefat" name="<?php echo esc_attr($this->get_field_name( 'widget_embed' )); ?>"><?php echo esc_html($widget_embed); ?></textarea></p>
        
        <p>
            <input type="radio" id="lightcolor" name="<?php echo esc_attr($this->get_field_name( 'color' )); ?>" value="light" <?php if($color == 'light') echo 'checked'; ?> />
            <label for="lightcolor"><?php esc_html_e( 'Light', 'magellan' ); ?></label>
        </p>
        <p>
            <input type="radio" id="darkcolor" name="<?php echo esc_attr($this->get_field_name( 'color' )); ?>" value="dark" <?php if($color == 'dark') echo 'checked'; ?> />
            <label for="darkcolor"><?php esc_html_e( 'Dark', 'magellan' ); ?></label>
        </p>
        
<?php
	}
}