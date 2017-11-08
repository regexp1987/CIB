<?php get_header(); ?>

<div class="container page-not-found">
	<h6>404</h6>
	<h3><?php esc_html_e('Page not found', 'magellan'); ?></h3>
	<p><?php esc_html_e('The page you are looking for could have been deleted, or has never existed.', 'magellan'); ?></p>
	<p><?php esc_html_e('You can go back', 'magellan');?> <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e('Home', 'magellan'); ?></a> <?php esc_html_e('or try to search something else', 'magellan'); ?></p>
</div>

<?php get_footer(); ?>