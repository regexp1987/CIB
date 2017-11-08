<?php get_header(); ?>

<?php if(!is_page()) :
$archive_url = '';
if( get_option( 'show_on_front' ) == 'page' ) { $archive_url = get_permalink( get_option('page_for_posts' ) ); } else { $archive_url = home_url(); } ?>
<!-- Catalog -->			
<div class="container-fluid page-title">
    <div class="container">
        <div class="title-wrapper">
            <h1><?php echo magellan_archive_title( get_wpml_admin_text_string('blog_title') ); ?></h1>
        </div>
    </div>
</div>

<?php endif; ?>

<div class="container main-content-wrapper sidebar-<?php echo magellan_gs('sidebar_position'); ?>">
    
    <div class="main-content">
        <!-- Blog list -->

        <div class="row">
			<div class="col-md-12 col-xs-12">
				
				<?php
				//open wrapper tags
				if(magellan_gs('blog_item_style') == 'compact_double' || magellan_gs('blog_item_style') == 'large')
				{
					?><div class="post-block post-image-top"><?php
				}
				else
				{
					?><div class="post-block post-image-300"><?php
				}	
				?>
				
				<?php get_template_part( 'theme/templates/blog-loop'); ?>
						
				
				</div>
				
            </div>
        </div>
        
		<div class="row">
			<div class="col-md-12 col-xs-12">
				
				<?php get_template_part( 'theme/templates/pagination' ); ?>
				
			</div>
		</div>
                
    </div>
    
    <?php get_sidebar(); ?>
            
</div>

<?php echo $banner = magellan_get_banner_by_location('blog_ad', 'banner-footer'); ?>
    
<?php get_footer(); ?>