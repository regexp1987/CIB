<?php 
class MagellanRecentPostList extends WP_Widget {

    var $widget_cssclass;
	var $widget_description;
	var $widget_idbase;
	var $widget_name;
    
	function __construct() {

		/* Widget variable settings. */
		$this->widget_cssclass = 'magellan_recent_post_list';
		$this->widget_description = esc_html__( 'List of recent posts', 'magellan' );
		$this->widget_idbase = 'magellan_recent_post_list';
		$this->widget_name = esc_html__( 'Magellan Recent Post List', 'magellan' );

		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->widget_cssclass, 'description' => $this->widget_description );

		/* Create the widget. */
		parent::__construct('magellan_recent_post_list', $this->widget_name, $widget_ops);

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	function widget($args, $instance) 
    {
		$cache = wp_cache_get('magellan_recent_post_list', 'widget');

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
        $count = isset( $instance['count'] ) ? $instance['count'] : false;
        $category = isset( $instance['cat'] ) ? $instance['cat'] : false;
        		
		$unique_id = uniqid();

		/* Post List Query */
		$params = array(
			'category_name' => $category,
		);			
		$items = magellan_get_post_collection($params, $count, 1);
		
		
		//get link
		if(!empty($category))
		{
			$cat = get_category_by_slug($category);
			$view_all = get_category_link($cat->cat_ID);
		}
		else
		{
			if(get_option('show_on_front') == 'page')
			{
				$view_all = get_permalink( get_option( 'page_for_posts' ) );
			}
			else
			{
				$view_all = get_home_url();
			}
		}
		
		
		if(!empty($items)) : ?>

			<?php echo $before_widget;  ?>

				<div class="post-block">

					<div class="title-default">
						<span><?php echo esc_html($title); ?></span>
						<a href="<?php echo esc_url($view_all); ?>" class="more"><?php esc_html_e('View all', 'magellan'); ?></a>
					</div>

					<div class="post-block post-image-60">
						<?php foreach($items as $post) : ?>

							<div class="row">
								<div class="col-md-12">
									<?php 
									setup_postdata($post);
									get_template_part('theme/templates/post-list-item-medium-with-overlay');
									?>
								</div>
							</div>

						<?php endforeach; ?>
					</div>

				</div>

			<?php echo $after_widget; ?>

		<?php endif; ?>
			
        <?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('magellan_recent_post_list', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) 
    {
		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);
		$instance['count'] = strip_tags($new_instance['count']);
		$instance['cat'] = strip_tags($new_instance['cat']);
		
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['magellan_recent_post_list']) )
			delete_option('magellan_recent_post_list');

		return $instance;
	}

	function flush_widget_cache() 
    {
		wp_cache_delete('magellan_recent_post_list', 'widget');
	}

	function form( $instance ) 
    {
		//get post categories
        $post_categories = get_terms('category');
        $post_cats = array('' => '');    //blank entry
        foreach($post_categories as $pc)
        {
            $post_cats[$pc->slug] = $pc->slug;
        }

        $title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : 'Latest news';
        $count = isset( $instance['count'] ) ? esc_attr( $instance['count'] ) : 6;
        $current_cat = isset( $instance['cat'] ) ? esc_attr( $instance['cat'] ) : '';
        
        ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:' , 'magellan'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
            </p>    
        
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'count' )); ?>"><?php esc_html_e( 'Post count:' , 'magellan'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'count' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'count' )); ?>" type="text" value="<?php echo esc_attr($count); ?>" />
            </p>
            
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'cat' )); ?>"><?php esc_html_e( 'Category:', 'magellan' ); ?></label><br/>
                <select name="<?php echo esc_attr($this->get_field_name( 'cat' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'cat' )); ?>" class="widefat">
                    <?php foreach($post_cats as $cat): ?>
                        <option value="<?php echo esc_attr($cat); ?>"<?php if($cat == $current_cat) echo ' selected="selected"'; ?>><?php echo ucfirst($cat); ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
		<?php
	}
}