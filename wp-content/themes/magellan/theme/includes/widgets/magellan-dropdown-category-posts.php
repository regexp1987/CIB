<?php 
class MagellanDropdownCategoryPosts extends WP_Widget {

    var $widget_cssclass;
	var $widget_description;
	var $widget_idbase;
	var $widget_name;
    
	function __construct() {

		/* Widget variable settings. */
		$this->widget_cssclass = 'magellan_dropdown_category_posts';
		$this->widget_description = esc_html__( 'Display dynamically switchabe post categories', 'magellan' );
		$this->widget_idbase = 'magellan_dropdown_category_posts';
		$this->widget_name = esc_html__( 'Magellan Dropdown Post Categories', 'magellan' );

		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->widget_cssclass, 'description' => $this->widget_description );

		/* Create the widget. */
		parent::__construct('magellan_dropdown_category_posts', $this->widget_name, $widget_ops);

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	function widget($args, $instance) 
    {
        global $post;
        
		$cache = wp_cache_get('magellan_dropdown_category_posts', 'widget');

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

        $categories = !empty( $instance['cats'] ) ? $instance['cats'] : array();
        $unique_id = uniqid();

        ?>
		<?php echo $before_widget; ?>
        
			<div class="sorting">
				<div class="buttons">
					<a href="#<?php echo esc_attr($unique_id); ?>_all" class="btn btn-sort active"><?php esc_html_e('All recent articles', 'magellan'); ?></a>
					
					<?php if(!empty($categories)) {
						foreach($categories as $cat)
						{
							$category = get_category_by_slug($cat);
							echo '<a href="#' . esc_attr($unique_id) . '_' . esc_attr($category->slug) . '" class="btn btn-sort">' . $category->name . '</a>';
						}
					} ?>
				</div>
			</div>

			<div class="container post-block post-image-90">
				<?php
					array_unshift($categories, 'all');

					foreach($categories as $key => $category)
					{
						$params = array();
						if($category != 'all')
						{
							$params['category_name'] = $category;
						}

						$items = magellan_get_post_collection($params, 6, 1);

						if(!empty($items))
						{
							$cat_obj = get_category_by_slug($category);
							if($category != 'all')
							{
								$view_all = get_category_link($cat_obj->cat_ID);
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
							
							echo '<div class="items" id="'  . esc_attr($unique_id) . '_' . esc_attr($category) . '" data-url="' . $view_all . '">';

							$chunks = array_chunk($items, 3);
							if(!empty($chunks))
							{	
								foreach($chunks as $chunk) 
								{		
									echo '<div class="row">';

									foreach($chunk as $key => $post)
									{
										echo '<div class="col-md-4">';

										setup_postdata($post);
										get_template_part('theme/templates/post-list-item-medium-with-overlay');

										echo '</div>';
									}

									echo '</div>';
								}
							}

							echo '</div>';
						}
					}

				?>
			</div>

			<?php
			if(get_option('show_on_front') == 'page')
			{
				$view_all = get_permalink( get_option( 'page_for_posts' ) );
			}
			else
			{
				$view_all = get_home_url();
			}
			?>
			<div class="container post-block btn-more">
				<a href="<?php echo esc_url($view_all); ?>" class="btn btn-default btn-dark"><?php esc_html_e('View all', 'magellan'); ?></a>
			</div>

		<?php echo $after_widget; ?>

        <?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('magellan_dropdown_category_posts', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) 
    {
		$instance = $old_instance;
        $instance['cats'] = esc_sql($new_instance['cats']);
        
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['magellan_dropdown_category_posts']) )
			delete_option('magellan_dropdown_category_posts');

		return $instance;
	}

	function flush_widget_cache() 
    {
		wp_cache_delete('magellan_dropdown_category_posts', 'widget');
	}

	function form( $instance ) 
    {
        //get post categories
        $post_categories = get_terms('category');
        foreach($post_categories as $pc)
        {
            $post_cats[$pc->slug] = $pc->slug;
        }

        $current_cats = (!empty( $instance['cats'] ) ? esc_sql( $instance['cats'] ) : array());
        
        ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'cats' )); ?>"><?php esc_html_e( 'Categories:', 'magellan' ); ?></label><br/>
                <select multiple="multiple" name="<?php echo esc_attr($this->get_field_name( 'cats' )); ?>[]" id="<?php echo esc_attr($this->get_field_id( 'cats' )); ?>" class="widefat">
                    <?php foreach($post_cats as $cat): ?>
                    <option value="<?php echo esc_attr($cat); ?>"<?php if(in_array($cat, $current_cats)) { echo ' selected="selected"'; } ?>><?php echo ucfirst($cat); ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
        <?php
	}
}