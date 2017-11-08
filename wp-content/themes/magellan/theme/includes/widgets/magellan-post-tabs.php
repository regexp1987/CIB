<?php 
class MagellanPostTabs extends WP_Widget {

    var $widget_cssclass;
	var $widget_description;
	var $widget_idbase;
	var $widget_name;
    
	function __construct() {

		/* Widget variable settings. */
		$this->widget_cssclass = 'magellan_sidebar_post_tabs';
		$this->widget_description = esc_html__( 'Post tabs - latest, popular and recently commented posts ', 'magellan' );
		$this->widget_idbase = 'magellan_sidebar_post_tabs';
		$this->widget_name = esc_html__( 'Magellan Post Tabs', 'magellan' );

		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->widget_cssclass, 'description' => $this->widget_description );

		/* Create the widget. */
		parent::__construct('magellan_sidebar_post_tabs', $this->widget_name, $widget_ops);

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	function widget($args, $instance) 
    {
		$cache = wp_cache_get('magellan_sidebar_post_tabs', 'widget');

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

        $count = isset( $instance['count'] ) ? $instance['count'] : 5;
        $popular_range = isset( $instance['popular_range'] ) ? $instance['popular_range'] : 'weekly';
		
        $popular = MagellanInstance()->get_popular_posts($popular_range, $count);
        $commented = magellan_get_posts_with_latest_comments($count);
        $latest = magellan_get_post_collection(array(), $count);
        ?>

		<?php echo $before_widget; ?>
        
            <!-- Tabs -->
			<div class="post-block post-image-60 slider switchable-tabs">
				
				<div class="sorting">
					<div class="buttons">					
						<?php if(!empty($popular) && $popular !== false) { ?>
						<a href="#" class="btn btn-sort active"><?php esc_html_e('Popular', 'magellan' ); ?></a>
						<?php } ?>

						<?php if(!empty($latest)) { ?>
						<a href="#" class="btn btn-sort <?php if(!$popular) { echo 'active'; } ?>"><?php esc_html_e('Recent', 'magellan' ); ?></a>
						<?php } ?>

						<?php if(!empty($commented)) { ?>
						<a href="#" class="btn btn-sort"><?php esc_html_e('Comments', 'magellan' ); ?></a>
						<?php } ?>
					</div>
				</div>
				
			
            
				<div class="row">
					<div class="col-xs-12">

						<?php
						if(!empty($popular) && $popular !== false)
						{
							?>
								<div class="switcher-tab-content">
									<?php
									foreach($popular as $item)
									{
										?><div class="row ">
											<div class="col-xs-12"><?php

											$post = get_post($item->postid);
											if($post)
											{
												setup_postdata($post);
												get_template_part( 'theme/templates/post-list-item-small');
											}
										
											?></div>
										</div><?php
									}
									?>
								</div>
							<?php
						}
						?>

						<?php
						if(!empty($latest))
						{
							?>
								<div class="switcher-tab-content">
									<?php
									foreach($latest as $post)
									{
										?><div class="row ">
											<div class="col-xs-12"><?php

											if($post)
											{
												setup_postdata($post);
												get_template_part( 'theme/templates/post-list-item-small');
											}
										
											?></div>
										</div><?php
									}
									?>
								</div>
							<?php
						}
						?>

						<?php
						if(!empty($commented))
						{
							?>
								<div class="switcher-tab-content">
									<?php
									foreach($commented as $post)
									{
										?><div class="row ">
											<div class="col-xs-12"><?php

										if($post)
										{
											setup_postdata($post);
											get_template_part( 'theme/templates/post-list-item-small');
										}
										
											?></div>
										</div><?php
									}
									?>
								</div>
							<?php
						}
						?>
					</div>
				</div>
			</div>
            
		<?php echo $after_widget; ?>
			
        <?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('magellan_sidebar_post_tabs', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) 
    {
		$instance = $old_instance;
		$instance['count'] = strip_tags($new_instance['count']);
        $instance['popular_range'] = strip_tags($new_instance['popular_range']);

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['magellan_sidebar_post_tabs']) )
			delete_option('magellan_sidebar_post_tabs');

		return $instance;
	}

	function flush_widget_cache() 
    {
		wp_cache_delete('magellan_sidebar_post_tabs', 'widget');
	}

	function form( $instance ) 
    {
		$count = isset( $instance['count'] ) ? esc_attr( $instance['count'] ) : 6;
        $popular_range = isset( $instance['popular_range'] ) ? esc_attr( $instance['popular_range'] ) : 3;
        
        ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'count' )); ?>"><?php esc_html_e( 'Post count:', 'magellan' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'count' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'count' )); ?>" type="text" value="<?php echo esc_attr($count); ?>" />
            </p>
        
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'popular_range' )); ?>"><?php esc_html_e( 'Date range for popular items:', 'magellan' ); ?></label><br/>
                <select name="<?php echo esc_attr($this->get_field_name( 'popular_range' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'popular_range' )); ?>" class="widefat">
                    <option value="daily" <?php if($popular_range == 'daily') echo ' selected="selected"'; ?>>Today</option>
                    <option value="weekly" <?php if($popular_range == 'weekly') echo ' selected="selected"'; ?>>Last week</option>
                    <option value="monthly" <?php if($popular_range == 'monthly') echo ' selected="selected"'; ?>>Last month</option>
                    <option value="all" <?php if($popular_range == 'all') echo ' selected="selected"'; ?>>Since records began</option>
                </select>
            </p>
        
        <?php
	}
}