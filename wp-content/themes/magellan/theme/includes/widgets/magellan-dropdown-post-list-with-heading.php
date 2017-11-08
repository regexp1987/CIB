<?php 
class MagellanDropdownPostListWithHeading extends WP_Widget {

    var $widget_cssclass;
	var $widget_description;
	var $widget_idbase;
	var $widget_name;
    
	function __construct() {

		/* Widget variable settings. */
		$this->widget_cssclass = 'magellan_dropdown_post_list_with_heading';
		$this->widget_description = esc_html__( 'Post list with one large featured post', 'magellan' );
		$this->widget_idbase = 'magellan_dropdown_post_list_with_heading';
		$this->widget_name = esc_html__( 'Magellan Dropdown Post List With Featured Item', 'magellan' );

		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->widget_cssclass, 'description' => $this->widget_description );

		/* Create the widget. */
		parent::__construct('magellan_dropdown_post_list_with_heading', $this->widget_name, $widget_ops);

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	function widget($args, $instance) 
    {
        global $post;
        
		$cache = wp_cache_get('magellan_dropdown_post_list_with_heading', 'widget');

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

        $category = !empty( $instance['cat'] ) ? $instance['cat'] : '';
		$title = apply_filters('widget_title', empty($instance['title']) ? esc_html__('Recent Posts', 'magellan') : $instance['title'], $instance, $this->id_base);
		$unique_id = uniqid();
		$count = 7;
		
				$unique_id = uniqid();

		/* Featured Post Query */
		$params = array(
			'category_name' => $category,
			'meta_key' => 'is_featured',
			'meta_value' => 'on'
		);

		$skip_id = array();
		$featured = magellan_get_post_collection($params, 1, 1);
		if(!empty($featured))   //if featured post found, reduce the overal count
		{
			$featured = $featured[0];
			$count--;
			$skip_id[] = $featured->ID;
		}

		
		/* Post List Query */
		$params = array(
			'category_name' => $category,
			'post__not_in' => $skip_id
		);			
		$items = magellan_get_post_collection($params, $count, 1);
		
		//get link
		/*if(!empty($category))
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
		}*/
		
		
		//if featured not found, take the first from items
		if(empty($featured) && !empty($items))
		{
			$featured = array_shift($items);
		}
		
		if(!empty($featured) || !empty($items)) :
        ?>
		
		<?php echo $before_widget; ?>
        
			<div class="title-default">
				<span><?php echo esc_html($title); ?></span>
			</div>

			<div class="container post-block post-image-90">
				
				<div class="row">
					<div class="col-md-4">
						<?php
						if(!empty($featured))
						{
							$post = $featured;
							setup_postdata($post);
							get_template_part('theme/templates/featured-medium-post');
						}
						?>
					</div>
					<div class="col-md-8">
						<?php
							$chunks = array_chunk($items, 2);
							if(!empty($chunks))
							{	
								foreach($chunks as $chunk) 
								{		
									echo '<div class="row">';

									foreach($chunk as $key => $post)
									{
										echo '<div class="col-md-6">';

										setup_postdata($post);
										get_template_part('theme/templates/post-list-item-medium-with-overlay');

										echo '</div>';
									}

									echo '</div>';
								}
							}
						?>
					</div>	
				</div>
				
			</div>

		<?php echo $after_widget; ?>
				
		<?php endif; ?>

        <?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('magellan_dropdown_post_list_with_heading', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) 
    {
		$instance = $old_instance;
        $instance['cat'] = esc_sql($new_instance['cat']);
		$instance['title'] = strip_tags($new_instance['title']);
		
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['magellan_dropdown_post_list_with_heading']) )
			delete_option('magellan_dropdown_post_list_with_heading');

		return $instance;
	}

	function flush_widget_cache() 
    {
		wp_cache_delete('magellan_dropdown_post_list_with_heading', 'widget');
	}

	function form( $instance ) 
    {
        //get post categories
        $post_categories = get_terms('category');
        foreach($post_categories as $pc)
        {
            $post_cats[$pc->slug] = $pc->slug;
        }

        $current_cat = (!empty( $instance['cat'] ) ? esc_sql( $instance['cat'] ) : '');
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        
        ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:' , 'magellan'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
            </p>
			<p>
                <label for="<?php echo esc_attr($this->get_field_id( 'cat' )); ?>"><?php esc_html_e( 'Category:', 'magellan' ); ?></label><br/>
                <select name="<?php echo esc_attr($this->get_field_name( 'cat' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'cat' )); ?>" class="widefat">
                    <?php foreach($post_cats as $cat_item): ?>
                    <option value="<?php echo esc_attr($cat_item); ?>"<?php if($cat_item == $current_cat) { echo ' selected="selected"'; } ?>><?php echo ucfirst($cat_item); ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
        <?php
	}
}