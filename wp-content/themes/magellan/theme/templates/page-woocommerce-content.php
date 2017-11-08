<?php
//setup post in case its not there
if(!in_the_loop())
{
    if ( have_posts() ) 
    {
        the_post();
    }
}
?>

<div class="container-fluid page-title">
	<div class="container">
		<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
	</div>
</div>	


<div class="container main-content-wrapper post-main-wrapper sidebar-disabled">
    
    <div <?php post_class('main-content hentry'); ?>>

		<div class="row">
			<div class="col-md-12 post-block">
								
				<div class="the-content-container"><?php the_content(); ?></div>
				
			</div>
		</div>

    </div>
    
</div>