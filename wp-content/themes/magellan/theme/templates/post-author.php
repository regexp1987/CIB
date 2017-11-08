<?php
if(magellan_gs('show_about_author') == 'on' && is_singular('post'))
{
    ?>

	<div class="row">
		<div class="col-md-12 about-author" id="about-author">
			<div class="title-default">
				<span><?php esc_html_e('About author', 'magellan'); ?></span>
			</div>
			<div class="image">
				<?php echo get_avatar( get_the_author_meta('ID'), 120); ?> 
			</div>
			<div class="text">
				<h2><a href="<?php echo esc_url( get_the_author_meta('url') ); ?>"><?php echo get_the_author(); ?></a></h2>
				<div class="legend">
					<?php 
						$position = get_the_author_meta( 'position' );
						if($position)
						{
							echo '<a href="' . esc_url( get_the_author_meta('url') ) . '" class="user">' . $position  . '</a>';
						}
					?>
				</div>
				<?php
					$description = get_the_author_meta( 'description' );
					if($description)
					{
						echo '<p>' . $description . '</p>';
					}
				?>
			</div>
			<?php
				$twitter = get_the_author_meta( 'twitter' );
				$facebook = get_the_author_meta( 'facebook' );
				$youtube = get_the_author_meta( 'youtube' );
				$gplus = get_the_author_meta( 'gplus' );
				$pinterest = get_the_author_meta( 'pinterest' );
				$instagram = get_the_author_meta( 'instagram' );
				
				if($twitter || $facebook || $youtube || $gplus || $pinterest || $instagram) :
			?>	
			<div class="post-controls">
				<?php
                    if($twitter) { echo '<div><a href="' . esc_url($twitter) .'"><i class="fa fa-twitter-square"></i> Twitter</a></div>'; }
                    if($facebook) { echo '<div><a href="' . esc_url($facebook) .'"><i class="fa fa-facebook-square"></i> Facebook</a></div>'; }
                    if($youtube) { echo '<div><a href="' . esc_url($youtube) .'"><i class="fa fa-youtube-square"></i> Youtube</a></div>'; }
                    if($gplus) { echo '<div><a href="' . esc_url($gplus) .'"><i class="fa fa-google-plus-square"></i> Google+</a></div>'; }
                    if($pinterest) { echo '<div><a href="' . esc_url($pinterest) .'"><i class="fa fa-pinterest-square"></i> Pinterest</a></div>'; } 
					if($instagram) { echo '<div><a href="' . esc_url($instagram) .'"><i class="fa fa-instagram"></i> Instagram</a></div>'; } 
                ?>
			</div>
			<?php endif; ?>
		</div>
	</div>

<?php } ?>