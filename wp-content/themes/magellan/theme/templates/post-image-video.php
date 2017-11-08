<?php
	global $post;
	
    $video_url = get_post_meta( $post->ID, $key = 'video_url', true);
	$size_w = 1170;
	$size_h = 658;		
	
	//enable autoplay
	$autoplay = '';
	if(magellan_get_post_image_width($post->ID) == 'video_autoplay')
	{
		$autoplay = '?autoplay=1&rel=0';
	}
		
	if(!empty($video_url))
	{
		if(strpos($video_url, 'youtube') || strpos($video_url, 'youtu.be'))	//youtube
		{
            if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $video_url, $id)) {
                $values = $id[1];
            } 
            else if (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $video_url, $id)) {
                $values = $id[1];
            }
            else if (preg_match('/youtube\.com\/v\/([^\&\?\/]+)/', $video_url, $id)) {
                $values = $id[1];
            }
            else if (preg_match('/youtu\.be\/([^\&\?\/]+)/', $video_url, $id)) {
                $values = $id[1];
            }
            else if (preg_match('/youtube\.com\/verify_age\?next_url=\/watch%3Fv%3D([^\&\?\/]+)/', $video_url, $id)) {
              $values = $id[1];
            }
			
            if(!empty($values))
            {
                $video_url = 'https://www.youtube.com/embed/' . $values;
            }
		}
        if(strpos($video_url, 'youtu.be'))	//youtube
        {
            if (preg_match('%(?:youtu(?:-nocookie)?\.be/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video_url, $match)) {
				$id = $match[1];
			}
			
			$video_url = 'https://www.youtube.com/embed/' . $id;
        }
		elseif(strpos($video_url, 'vimeo'))		//vimeo
		{
			$url_parts = parse_url($video_url);
			
			if(!empty($url_parts['path']))
			{
				preg_match_all('!\d+!', $url_parts['path'], $numbers);
				if(!empty($numbers[0]) && !empty($numbers[0][0]))
				{
					$id = $numbers[0][0];
					$video_url = 'https://player.vimeo.com/video/' . $id;
				}
			}
		}
		
		?>
		<div class="container-fluid page-title post-page-title">
			<div class="container">
				<div class="video-content">
					<iframe width="<?php echo esc_attr($size_w); ?>" height="<?php echo esc_attr($size_h); ?>" src="<?php echo esc_attr($video_url . $autoplay); ?>" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
				</div>
			</div>
		</div>
		<?php
	}
?>