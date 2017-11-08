<?php get_header(); ?>

<div class="container-fluid page-title">
	<div class="container">
		<h2><?php esc_html_e('Photo galleries', 'magellan' ); ?></h2>
	</div>
</div>

<div class="container main-content-wrapper main-content-wrapper-fullwidth sidebar-disabled">

	<div class="main-content">
		
		<?php
        
        $counter = 0;
        
        if ( have_posts() ) : 
            while ( have_posts() ) : the_post();

				$counter++;

				if($counter % 4 == 1)
                {
                    ?><div class="row">
						<div class="post-block post-gallery">
							<div class="row">
								<div class="col-md-12">
									<div class="galleries">
										<div class="row">
					<?php
                }
                
				?><div class="col-md-3 col-sm-3 col-xs-12"><?php
				
                get_template_part('theme/templates/loop-gallery-list-item');
                
				?></div><?php

                if($counter % 4 == 0)
                {
                    ?>
										</div>
									</div>
								</div>	
							</div>
						</div>
					</div>			
					<?php
                }

            endwhile;
        else :
            echo esc_html_e('no posts found!', 'magellan');
        endif;
        
        if($counter % 4 != 0)
        {
			?>
								</div>
							</div>
						</div>	
					</div>
				</div>
			</div>			
			<?php
        }
        
        ?>
			
		<?php get_template_part('theme/templates/pagination'); ?>
		
	</div>

</div>
	
<?php echo magellan_get_banner_by_location('gallery_ad', 'banner-footer'); ?>

<?php get_footer(); ?>