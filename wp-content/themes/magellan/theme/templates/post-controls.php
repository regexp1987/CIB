<?php 
global $post;
	
$meta = get_post_meta(get_the_ID());
$show_share = true;
if(is_page() && (empty($meta['show_share']) || (!empty($meta['show_share']) && $meta['show_share'][0] != 'on')))
{
	$show_share = false;
}

if(
	(magellan_gs('enable_post_viewcount') == 'on' && class_exists('WordpressPopularPosts') )
	||
	(magellan_gs('show_post_share') == 'on' && $show_share)
	||
	magellan_gs('enable_post_like') == 'on'
)
{
	?>
	<div class="post-controls" id="postid-<?php echo esc_attr(get_the_ID()); ?>" data-nonce="<?php echo wp_create_nonce('post_like_' . get_the_ID()); ?>">

		<?php 
		if(magellan_gs('enable_post_viewcount') == 'on' && class_exists('WordpressPopularPosts'))
		{
			?>
			<div>
				<i class="fa fa-eye"></i><?php echo esc_attr(MagellanInstance()->get_post_pageviews(get_the_ID())) . ' '; esc_html_e('views', 'magellan'); ?>
			</div>
			<?php
		}
		?>

		<?php
			$src_parts = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
			if(empty($src_parts[0]))
			{
				$thumb = '';
			}
			else
			{
				$thumb = $src_parts[0];
			}
		?>


		<?php if(magellan_gs('show_post_share') == 'on' && $show_share) : ?>
			<div>
				<share-button class="share-button" data-button-text="<?php esc_html_e('Share', 'magellan'); ?>" data-url="<?php the_permalink(); ?>" data-title="<?php the_title(); ?>" data-description="<?php echo esc_attr(magellan_excerpt(50)); ?>" data-image="<?php echo esc_url($thumb); ?>"></share-button>
			</div>
		<?php endif; ?>

		<?php if(magellan_gs('enable_post_like') == 'on'): ?>

			<div class="like">
				<a href="#"><i class="fa fa-thumbs-up"></i><?php esc_html_e('Like', 'magellan');?></a>
			</div>
			<div class="dislike">
				<a href="#"><i class="fa fa-thumbs-down"></i><?php esc_html_e('Dislike', 'magellan');?></a>
			</div>

			<?php
				$id = get_the_ID();
				$meta = get_post_meta($id);
				$likes = (!empty($meta['post_likes']) ? intval($meta['post_likes'][0]) : 0);
				$dislikes = (!empty($meta['post_dislikes']) ? intval($meta['post_dislikes'][0]) : 0);
				if($likes+$dislikes > 0)
				{
					$percent = ($likes/($likes + $dislikes)) * 100;
				}
				else
				{
					$percent = 0;
				}
			?>
			<div class="rating">
				<span class="likes"><?php echo esc_html($likes); ?></span>
				<span class="bar"><s style="width: <?php echo esc_attr(intval($percent)); ?>%;"></s></span>
				<span class="dislikes"><?php echo esc_html($dislikes); ?></span>
			</div>

		<?php endif; ?>
	</div>
<?php } ?>