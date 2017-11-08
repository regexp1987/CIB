<div class="lightbox lightbox-search">
	<a href="#" class="btn btn-default btn-dark close"><i class="fa fa-times"></i></a>
	<div class="container">
		<div class="row">
			<form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="search-form">
				<p class="search-wrapper"><input type="text" placeholder="<?php esc_html_e( 'Search here', 'magellan' ); ?>" class="search-input-lightbox" name="s"><input type="submit" value="<?php esc_html_e( 'Search', 'magellan' ); ?>" class="btn-search-lightbox"></p>
			</form>

			<div class="row lightbox-items">
				<?php
					if ( is_active_sidebar( 'search_sidebar' ) )
					{
						dynamic_sidebar('search_sidebar'); 
					}
				?>
			</div>
		</div>
	</div>
</div>