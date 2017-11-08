<!-- Menu -->
<div class="container mega-menu-wrapper">
	<div class="mega-menu">
		<a class="togglemenu" href="#"><?php esc_html_e('Main menu', 'magellan'); ?></a>

		<div class="container">
			<?php
				wp_nav_menu( array(
					'menu_id'               => 'menu-primary',
					'menu'                  => 'primary-menu',
					'theme_location'        => 'primary-menu',
					'depth'                 => 3,
					'container'             => 'div',
					'container_class'       => 'default-menu',
					'container_id'          => '',
					'menu_class'            => 'nav',
					'link_before'           => '<span>',
					'link_after'            => '</span>',
					'child_ul_before'       => '',
					'child_ul_after'        =>  '',
					'mega_menu_class'       => 'mega-menu-item',
					'mega_parent_class'     => 'full-width',
					'default_menu_class'    => 'default-dropdown',
					'items_wrap'			=> magellan_menu_items_wrap_filter(),
					'fallback_cb'           => 'wp_bootstrap_navwalker::fallback',
					'walker'                => new wp_bootstrap_navwalker()
					)
				);				
			?>
		</div>
        
    </div>
</div>