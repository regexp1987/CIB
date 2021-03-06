<?php
	$image_size = 'magellan_post_list_item_medium';
	if(magellan_gs('blog_item_style') == 'large')
	{
		$image_size = 'magellan_post_list_item_large';
	}

	$thumb = magellan_get_thumbnail($image_size, true, false);
    $class = '';
    if(!$thumb){
        $class .= ' no-image';
    }
?>

<div class="<?php post_class($class); ?>">
	<?php
    if($thumb)
    {
        ?>
        <div class="image">
            <a href="<?php the_permalink(); ?>"><img src="<?php echo esc_url($thumb); ?>" alt="<?php esc_attr(the_title()); ?>"/></a>
        </div>
        <?php
    }
    ?>
	<div class="title">
		
		<?php get_template_part('theme/templates/post-categories-compact-dropdown'); ?>		
		
		<h3><a href="<?php the_permalink(); ?>"><?php magellan_search_title_highlight(); ?></a></h3>
		<?php get_template_part('theme/templates/title-legend'); ?>
		<div class="intro">
			<?php magellan_search_excerpt_highlight(); ?>
		</div>
		
		<a href="<?php the_permalink(); ?>" class="btn btn-default"><?php esc_html_e('Read more', 'magellan'); ?></a>
	</div>
</div>