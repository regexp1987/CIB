<?php get_header(); ?>

<div class="container-fluid page-title">
	<div class="container">
		<h2><?php esc_html_e('Photo galleries', 'magellan' ); ?></h2>
		<a href="<?php echo get_post_type_archive_link('gallery'); ?>" class="btn btn-default btn-dark"><?php esc_html_e('Back to gallery list', 'magellan' ); ?></a>
	</div>
</div>

<div class="container main-content-wrapper main-content-wrapper-fullwidth sidebar-disabled">

	<div class="main-content">
		
		<?php $attachments = new Attachments( 'magellan_galleries' ); ?>

			<div class="row">
				<div class="post-block post-gallery gallery-single">
					<div class="row">
						<div class="col-md-12">
							<div class="galleries">
								<div class="gallery-title">
									<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>

									<div class="legend">
										<a href="<?php echo get_day_link(get_the_time('Y'), get_the_time('m'), get_the_time('d')); ?>" class="time"><?php echo get_the_date(); ?></a>
										<?php if( $attachments->exist() ) : ?>
                                            <a href="<?php the_permalink(); ?>" class="photos"><?php echo esc_html($attachments->total()); ?></a>
                                        <?php endif; ?>
									</div>

								</div>
								
                                <?php if( $attachments->exist() ) : ?>
                                
                                    <div class="row">
                                        <div class="col-md-9 col-sm-12">
                                            <div class="gallery-slideshow" 
                                                data-cycle-swipe="true"
                                                data-cycle-swipe-fx="fade"
                                                data-index="1"
                                                data-cycle-log="false"
                                                data-cycle-fx="fade"
                                                data-cycle-timeout="0"
                                                data-cycle-speed="500"
                                                data-cycle-pager=""
                                                data-cycle-auto-height="calc"
                                                data-cycle-pager-active-class="active"
                                                data-cycle-pager-template=""
                                                data-cycle-slides="> .single-photo-active"
                                                data-cycle-prev="#prev"
                                                data-cycle-next="#next"
                                            >
                                                <?php
                                                while($attachments->get())
                                                {
                                                    ?>
                                                    <div class="single-photo-active">
                                                        <a href="#" class="btn-default btn-dark btn-maximize"></a>
                                                        <?php if($attachments->field( 'caption' ) != "") { ?><p class="caption"><?php echo esc_html($attachments->field( 'caption' )); ?></p><?php } ?>
                                                        <img src="<?php echo esc_url($attachments->src( 'magellan_gallery_item_large' )) ?>" alt="<?php echo esc_attr($attachments->field( 'caption' )); ?>" />
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            
                                            <div class="gallery-description">
                                                <div class="the-content-container"><?php the_content(); ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-12">
                                            <div class="single-photo-thumbs">
                                                <div class="title-default">
                                                    <span><?php esc_html_e('Photos', 'magellan'); ?></span>
                                                    <div class="controls right" data-total="<?php echo esc_attr($attachments->total()); ?>">
                                                        <s>1 / <?php echo esc_html($attachments->total()); ?></s>
                                                        <a href="#" id="prev" class="btn btn-default"><i class="fa fa-caret-left"></i></a>
                                                        <a href="#" id="next" class="btn btn-default"><i class="fa fa-caret-right"></i></a>
                                                    </div>
                                                </div>
                                                <div class="thumbs" id="pager">
                                                    <?php
                                                    $c = 0;
                                                    $attachments->rewind();

                                                    while($attachments->get())
                                                    {
                                                        $c++;

                                                        if($c%3 == 1)
                                                        {
                                                            echo '<div class="row">';
                                                        }

                                                        $class = '';
                                                        if($c == 1)
                                                        {
                                                            $class = 'active';
                                                        }

                                                        echo '<div class="col-md-4 col-sm-4 col-xs-4 thumb ' . $class . '">';
                                                        echo	'<a href="#">' . $attachments->image( 'magellan_gallery_item_small' ) . '</a>';
                                                        echo '</div>';

                                                        if($c%3 == 0)
                                                        {
                                                            echo '</div>';
                                                        }
                                                    }

                                                    if($c%3 != 0)
                                                    {
                                                        echo '</div>';
                                                    }

                                                    ?>
                                                </div>

                                                <?php
                                                    if ( is_active_sidebar( 'gallery_sidebar' ) )
                                                    {
                                                        dynamic_sidebar('gallery_sidebar'); 
                                                    }
                                                ?>

                                            </div>
                                        </div>
                                    </div>
                                
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
					
	</div>

</div>

<?php echo $banner = magellan_get_banner_by_location('single_gallery_ad', 'banner'); ?>

<?php if(magellan_gs('show_gallery_single_latest') == 'on') : ?>
	<div class="container galleries-recent"><?php echo do_shortcode('[photo_galleries count="4" columns="4"]'); ?></div>
<?php endif; ?>

<?php get_footer(); ?>