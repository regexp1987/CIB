<?php
    if(class_exists('Attachments')) :

        $attachments = new Attachments( 'magellan_galleries' );
        $attach_large = false;
        $large_obj = false;
        if( $attachments->exist() )
        {
            //get first
            $attach_large = $attachments->get_single(0);
            $attachment = $attachments->get();
            $large_obj = wp_get_attachment_image_src($attach_large->id, 'magellan_post_list_item_medium');
        }
    
    endif;
?>
<div class="post gallery-block">
	
    <?php if(class_exists('Attachments')) : ?>
    
        <?php get_template_part('theme/templates/post-categories-compact'); ?>

        <div class="overlay-wrapper">
            <div class="content">
                <div>
                    <a href="<?php the_permalink(); ?>" class="btn-circle btn-photo"></a>
                    <a href="<?php the_permalink(); ?>" class="link"></a>
                </div>
            </div>
            <div class="overlay" <?php if(!empty($large_obj)) { ?>style="background-image: url(<?php echo esc_url($large_obj[0]); ?>);" <?php } ?>></div>
        </div>

        <?php if($attachments->exist()) : ?>
            <div class="image <?php  MagellanInstance()->get_image_gradient_class(); ?>">
                <a href="<?php the_permalink(); ?>" class="btn-circle btn-photo"></a>
                <a href="<?php the_permalink(); ?>"><?php echo $attachments->image('magellan_post_list_item_medium'); ?></a>
            </div>
        <?php endif; ?>

        <div class="thumbs">
            <div class="row">
                <?php
                    if($attachments->exist())
                    {
                        for( $i = 1; $i <= 3; $i++ )
                        {
                            $attachment = $attachments->get();
                            if($attachment)
                            {
                                ?>
                                <div class="col-md-4 col-sm-4 col-xs-4 thumb">
                                    <a href="<?php the_permalink(); ?>" class="<?php  MagellanInstance()->get_image_gradient_class(); ?>"><?php echo $attachments->image('magellan_gallery_item_small'); ?></a>
                                </div>
                                <?php
                            }
                        }
                    }
                ?>
            </div>
        </div>
	
    <?php endif; ?>
    
	<div class="title">
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		
		<?php get_template_part('theme/templates/title-legend'); ?>
	</div>
</div>