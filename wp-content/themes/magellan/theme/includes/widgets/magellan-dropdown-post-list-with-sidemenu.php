<?php 
class MagellanDropdownPostListWithSidemenu extends WP_Widget {

    var $widget_cssclass;
	var $widget_description;
	var $widget_idbase;
	var $widget_name;
    
	function __construct() {

		/* Widget variable settings. */
		$this->widget_cssclass = 'magellan_dropdown_post_list_with_sidemenu';
		$this->widget_description = esc_html__( 'Post list with sidemenu and a large featured post', 'magellan' );
		$this->widget_idbase = 'magellan_dropdown_post_list_with_sidemenu';
		$this->widget_name = esc_html__( 'Magellan Dropdown Post List With Sidemenu', 'magellan' );

		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->widget_cssclass, 'description' => $this->widget_description );

		/* Create the widget. */
		parent::__construct('magellan_dropdown_post_list_with_sidemenu', $this->widget_name, $widget_ops);

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	function widget($args, $instance) 
    {
        global $post;
        
		$cache = wp_cache_get('magellan_dropdown_post_list_with_sidemenu', 'widget');

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
		$menu = !empty( $instance['menu'] ) ? $instance['menu'] : false;
		
		$count = 4;
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
			
		//if featured not found, take the first from items
		if(empty($featured) && !empty($items))
		{
			$featured = array_shift($items);
		}
		
		if(!empty($featured) || !empty($items)) :
        ?>
		
		<?php echo $before_widget; ?>

			<div class="container post-block post-image-90">
				
				<div class="row">
					<div class="col-md-4">
						<?php
						if(!empty($menu))
						{
							$menu_items = wp_get_nav_menu_items($menu);
							
							if(!empty($menu_items))
							{
								echo '<ul>';
								
								foreach ( (array) $menu_items as $key => $menu_item )
								{
									$title = $menu_item->title;
									$url = $menu_item->url;
									echo "<li><a href=\"" . $url . "\">" . $title . "</a></li>";
								}
								
								echo '</ul>';
							}

						}
						?>
					</div>
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
					<div class="col-md-4">
						<?php
							if(!empty($items))
							{	
								foreach($items as $post) 
								{		
									echo '<div class="row">';
									echo '<div class="col-md-12">';

									setup_postdata($post);
									get_template_part('theme/templates/post-list-item-medium-with-overlay');

									echo '</div>';
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
		wp_cache_set('magellan_dropdown_post_list_with_sidemenu', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) 
    {
		$instance = $old_instance;
        $instance['cat'] = $new_instance['cat'];
		$instance['menu'] = $new_instance['menu'];
				
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['magellan_dropdown_post_list_with_sidemenu']) )
			delete_option('magellan_dropdown_post_list_with_sidemenu');

		return $instance;
	}

	function flush_widget_cache() 
    {
		wp_cache_delete('magellan_dropdown_post_list_with_sidemenu', 'widget');
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
		$current_menu = (!empty( $instance['menu'] ) ? esc_sql( $instance['menu'] ) : '');
		
		$menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) );
        
        ?>
			<p>
                <label for="<?php echo esc_attr($this->get_field_id( 'cat' )); ?>"><?php esc_html_e( 'Category:', 'magellan' ); ?></label><br/>
                <select name="<?php echo esc_attr($this->get_field_name( 'cat' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'cat' )); ?>" class="widefat">
                    <?php foreach($post_cats as $cat_item): ?>
                    <option value="<?php echo esc_attr($cat_item); ?>"<?php if($cat_item == $current_cat) { echo ' selected="selected"'; } ?>><?php echo ucfirst($cat_item); ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
			<p>
                <label for="<?php echo esc_attr($this->get_field_id( 'menu' )); ?>"><?php esc_html_e( 'Menu:', 'magellan' ); ?></label><br/>
                <select name="<?php echo esc_attr($this->get_field_name( 'menu' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'menu' )); ?>" class="widefat">
                    <?php foreach($menus as $menu): ?>
                    <option value="<?php echo esc_attr($menu->term_id); ?>"<?php if($menu->term_id == $current_menu) { echo ' selected="selected"'; } ?>><?php echo ucfirst($menu->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
        <?php
	}
}