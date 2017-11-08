<?php
		
$attachments = new Attachments( 'magellan_galleries' );
if( $attachments->exist() ) : ?>

	<div class="lightbox lightbox-gallery lightbox-hidden">
		<a href="#" class="btn btn-default btn-dark close"><i class="fa fa-times"></i></a>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-9 image-wrapper">
					<div class="gallery-slideshow" 
						data-cycle-swipe="true"
						data-cycle-swipe-fx="fade"
						data-index="1"
						data-cycle-log="false"
						data-cycle-fx="fade"
						data-cycle-timeout="0"
						data-cycle-speed="500"
						data-cycle-pager="#pager-lightbox"
						data-cycle-auto-height="false"
						data-cycle-pager-active-class="active"
						data-cycle-pager-template=""
						data-cycle-slides="> .image"
						data-cycle-prev="#prev-lightbox"
						data-cycle-next="#next-lightbox"
					>
						<?php
						while($attachments->get())
						{
							?>
							<div class="image">
								<?php if($attachments->field( 'caption' ) != "") { ?><p class="caption"><?php echo esc_html($attachments->field( 'caption' )); ?></p><?php } ?>
								<img src="<?php echo esc_url($attachments->src( 'magellan_post_single_full_screen' )) ?>" alt="<?php echo esc_attr($attachments->field( 'caption' )); ?>" />
							</div>
							<?php
						}
						?>
					</div>
				</div>
				<div class="col-md-3 thumbs-wrapper">

					<div class="gallery-title">
						<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

						<div class="legend">
							<a href="<?php echo get_day_link(get_the_time('Y'), get_the_time('m'), get_the_time('d')); ?>" class="time"><?php echo get_the_date(); ?></a>
							<a href="<?php the_permalink(); ?>" class="photos"><?php echo esc_html($attachments->total()); ?></a>
						</div>
					</div>
					<div class="single-photo-thumbs">
						
						<div class="title-default title-dark">
							<span><?php esc_html_e('Photos', 'magellan'); ?></span>
							<div class="controls right" data-total="<?php echo esc_attr($attachments->total()); ?>">
								<s>1 / <?php echo esc_html($attachments->total()); ?></s>
								<a href="#" id="prev-lightbox" class="btn btn-default btn-dark"><i class="fa fa-caret-left"></i></a>
								<a href="#" id="next-lightbox" class="btn btn-default btn-dark"><i class="fa fa-caret-right"></i></a>
							</div>
						</div>
						
						<div class="thumbs-scroll">
							<div class="thumbs" id="pager-lightbox">
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
		</div>
	</div>

<?php endif; ?>