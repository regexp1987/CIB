<div class="lightbox lightbox-login">
	<a href="#" class="btn btn-default btn-dark close"><i class="fa fa-times"></i></a>
	<div class="container">
		<div class="row">
			<form class="magellan-login" name="loginform" id="loginform" action="<?php echo get_home_url() . '/wp-login.php' ?>" method="post">
				<p class="input-wrapper">
					<input name="log" id="user_login" type="text" placeholder="<?php esc_html_e('Username', 'magellan'); ?>" />
				</p>
				<p class="input-wrapper">
					<input type="password" name="pwd" id="user_pass" placeholder="<?php esc_html_e('Password', 'magellan'); ?>" />
				</p>
				<p class="input-wrapper">
					<input type="submit" name="wp-submit" id="wp-submit" value="<?php esc_html_e('Login', 'magellan'); ?>" />
				</p>
				<p class="input-wrapper">
					<input type="checkbox" name="rememberme" value="forever" id="rememberme"><label><?php esc_html_e('Remember me', 'magellan'); ?></label>
					<a href="<?php echo get_home_url() . '/wp-login.php?action=lostpassword' ?>" class="lost-password"><?php esc_html_e('Lost your password?', 'magellan'); ?></a>
				</p>
			</form>
		</div>
	</div>
</div>