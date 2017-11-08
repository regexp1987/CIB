<!-- BEGIN .section-item -->
<div class="section-item clearfix" id="register_theme_section">

	<?php	
	$register = magellan_gs('theme_register');
	
	if($register['status'] != 'on') :
	?>
	
		<h3><?php esc_html_e('Register your theme', 'magellan'); ?></h3> 

		<p>
			<?php esc_html_e('Registering will enable to you to update the theme automatically from wp-admin and simplify getting support.', 'magellan'); ?>
			<br/>
			<strong><?php esc_html_e('To register take the following 3 steps:', 'magellan'); ?></strong>
		</p>

		<ol class="registration-steps">
			<li>Go to <a href="http://planetshine.net/support/?signup_page" target="_blank">Planetshine Support Center</a> and create an account if you don't already have one.</li>
			<li>Find your <a href="http://planetshine.net/where-can-i-find-my-themeforest-purchase-code/" target="_blank">Themeforest purchase code</a> and paste it in the appropriate box below.</li>
			<li>Go to Themeforest and <a href="http://planetshine.net/where-to-find-my-themeforest-api-key/" target="_blank">generate an API key</a>, copy it and then paste it in appropriate box below.</li>
		</ol>

	<?php else: ?>
	
		<h3><?php esc_html_e('Theme has been registered successfully!', 'magellan'); ?></h3> 
	
	<?php endif; ?>
	
	
	<form name="registration">

		<?php

		magellan_output_theme_setting(array(
			'slug' => 'tf_username',
			'title' => 'Themeforest username',
			'type'  => 'textbox',
			'value' => (!empty($register['tf_username']) ? $register['tf_username'] : '' )
		));

		magellan_output_theme_setting(array(
			'slug' => 'tf_purchase_code',
			'title' => 'Themeforest purchase code',
			'type'  => 'textbox',
			'value' => (!empty($register['tf_purchase_code']) ? $register['tf_purchase_code'] : '' )
		));

		magellan_output_theme_setting(array(
			'slug' => 'tf_api_key',
			'title' => 'Themeforest API key',
			'type'  => 'textbox',
			'value' => (!empty($register['tf_api_key']) ? $register['tf_api_key'] : '' )
		));
		?>

		<?php
		if($register['status'] != 'on') :
		?>
			<!-- BEGIN .preset-save -->
			<div class="register">
				<a href="#" id="register-theme" class="button-2"><?php esc_html_e('Register your theme', 'magellan'); ?></a>
			<!-- END .preset-save -->
			</div>
		<?php else: ?>
			
			<!-- BEGIN .preset-save -->
			<div class="register">
				<a href="#" id="cancel-theme-registration" class="button-3"><?php esc_html_e('Cancel your registration', 'magellan'); ?></a>
			<!-- END .preset-save -->
			</div>
			
		<?php endif; ?>
	
	</form>
	
	<script type="text/javascript">
		jQuery(document).ready(function () {

			jQuery('#register-theme').click(function(){

				save_register_data('on');
				return false;
			});
			
			
			jQuery('#cancel-theme-registration').click(function(){

				save_register_data('off');
				return false;
			});
			
			
			function save_register_data(status)
			{
				var nonce = '<?php echo wp_create_nonce('magellan_save_theme_registration') ?>';
				var form = jQuery('form[name=registration]');
				
				var data = {
					action: 'magellan_save_theme_registration',
					_ajax_nonce: nonce,
					username: form.find('input[name="tf_username"]').val(),
					api_key: form.find('input[name="tf_api_key"]').val(),
					purchase_code: form.find('input[name="tf_purchase_code"]').val(),
					status: status,
					wp_url: '<?php echo get_site_url(); ?>',
					tf_item_id: '<?php echo MAGELLAN_TF_ITEM_ID; ?>'
				};
				
				var admin_ajax = '<?php echo site_url().'/wp-admin/admin-ajax.php'; ?>';
				
				jQuery.post(admin_ajax,data,function(msg){
                                
					if(msg.message == '200 OK')
					{
                        msg.status = 'ok';
						
						if(status == 'on')
						{
							msg.msg = '<?php esc_html_e('Theme Registered Successfully!', 'magellan'); ?>';
						}
						else
						{
							msg.msg = '<?php esc_html_e('Registration Canceled!', 'magellan'); ?>';
						}
						
						admin.show_save_result(msg);
					}
					else
					{
						msg.msg = msg.message;
						admin.show_save_result(msg);
					}
				}, 'json');
				
			}

		});
	</script>

<!-- END .section-item -->
</div>