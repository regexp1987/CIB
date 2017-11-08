<?php
$c = 0;
$blog_item_style = magellan_gs('blog_item_style');


if ( have_posts() ) : 
    
	while ( have_posts() ) : the_post();

		$c++;

        $template = 'theme/templates/loop';
        $slug = '';

		if(get_post_type() && get_post_type() !== 'post')
        {
            $slug .= get_post_type();
        }
		else
		{
			$slug .= 'default';
		}
        
		if(is_search())
		{
			$slug .= '-search-item';
		}
		else
		{
			$slug .= '-list-item';
		}
        
		
        //if template does not exist, force default single/item template
        $found = locate_template($template . '-' . $slug . '.php');
        if(strlen($found) == 0)
        {
            $slug = 'default-list-item';
        }
      
		//open wrapper tags
		if($blog_item_style == 'compact_single' || $blog_item_style == 'large')
		{
			?>
				<div class="row">
					<div class="col-md-12">

			<?php
		}
		elseif($blog_item_style == 'compact_double')
		{
			if($c%2 > 0)	//odd
			{
				?>	
				<div class="row">
					<div class="col-md-6 col-xs-12">
				<?php
			}
			else
			{
				?>
					<div class="col-md-6 col-xs-12">
				<?php
			}
		
		}
		
		
        //load template
        get_template_part($template, $slug);
		
		
		//close wrapper tags
		if($blog_item_style == 'compact_single' || $blog_item_style == 'large')
		{
			?>
				</div>
			</div>
			<?php
		}
		elseif($blog_item_style == 'compact_double')
		{
			if($c%2 > 0)	//odd
			{
				?><!-- Close odd columns -->
				</div><?php
			}
			else
			{
				?>
				<!-- Close odd columnn & row -->
					</div>
				</div>
				<?php
			}
			?>	
						
			
			<?php
		}
        		
	endwhile;
else :
	echo esc_html_e('no posts found!', 'magellan');
endif;

//close last remaining row
if($blog_item_style == 'compact_double' && $c%2 > 0)
{
		?>
	<!-- Close remaining row -->
	</div>
	<?php
}

?>