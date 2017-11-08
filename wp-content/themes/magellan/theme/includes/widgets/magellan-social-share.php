<?php 
class MagellanSocialShare extends WP_Widget {

    var $widget_cssclass;
	var $widget_description;
	var $widget_idbase;
	var $widget_name;
    
	function __construct() {

		/* Widget variable settings. */
		$this->widget_cssclass = 'magellan_social_share';
		$this->widget_description = esc_html__( 'Share icons for single posts, pages and galleries', 'magellan' );
		$this->widget_idbase = 'magellan_social_share';
		$this->widget_name = esc_html__( 'Magellan Social Share', 'magellan' );

		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->widget_cssclass, 'description' => $this->widget_description );

		/* Create the widget. */
		parent::__construct('magellan_social_share', $this->widget_name, $widget_ops);

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	function widget($args, $instance) 
    {
		$cache = wp_cache_get('magellan_social_share', 'widget');

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
		
		$title = apply_filters('widget_title', empty($instance['title']) ? esc_html__('Recent Posts', 'magellan') : $instance['title'], $instance, $this->id_base);
        ?>

		<?php echo $before_widget; ?>
        
		<?php echo $before_title . esc_html($title) . $after_title; ?>
		
			<?php if(is_singular(array('gallery', 'post', 'page'))) : ?>

				<?php
				if(is_singular('gallery'))
				{
					$attachments = new Attachments( 'magellan_galleries' );
					if( $attachments->exist() ) 
					{
						$attach_large = $attachments->get_single(0);
						$attachment = $attachments->get();
						$thumb = wp_get_attachment_image_src($attach_large->id, 'full');
						$thumb = $thumb[0];
					}
					else
					{
						$thumb = '';
					}
				}
				else
				{
					$src_parts = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );

					if(empty($src_parts[0]))
					{
						$thumb = '';
					}
					else
					{
						$thumb = $src_parts[0];
					}
				}
				
                $url = get_permalink();
                $title = get_the_title();
                $desc = urlencode(magellan_excerpt(20));
				?>
                <div class="social share-popup">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($url); ?>" target="_blank"><i class="fa fa-facebook-square"></i></a>
                    <a href="https://twitter.com/intent/tweet?source=<?php echo urlencode($url); ?>&text=<?php echo urlencode($title); ?>: <?php echo urlencode($url); ?>" target="_blank" title=""><i class="fa fa-twitter-square"></i></a>
                    <a href="https://plus.google.com/share?url=<?php echo urlencode($url); ?>" target="_blank" title="Share on Google+"><i class="fa fa-google-plus-square"></i></a>
                    <a href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode($url); ?>&media=<?php echo urlencode($thumb); ?>&description=<?php echo urlencode($desc); ?>" target="_blank" title="<?php _e('Pin it', 'magellan'); ?>"><i class="fa fa-pinterest-square"></i></a>
                    <a href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($url); ?>&title=<?php echo urlencode($title); ?>&summary=<?php echo urlencode($desc); ?>&source=<?php echo urlencode($url); ?>" target="_blank" title="<?php _e('Share on LinkedIn', 'magellan'); ?>"><i class="fa fa-linkedin-square"></i></a>
                </div>
			
			<?php endif; ?>

		<?php echo $after_widget; ?>
			
        <?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('magellan_social_share', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) 
    {
		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['magellan_social_share']) )
			delete_option('magellan_social_share');

		return $instance;
	}

	function flush_widget_cache() 
    {
		wp_cache_delete('magellan_social_share', 'widget');
	}

	function form( $instance ) 
    {
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
			
		?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:' , 'magellan'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
            </p>
			<p><?php esc_html_e('Social share settings can be setup in theme settings', 'magellan'); ?></p>
		<?php
	}
}