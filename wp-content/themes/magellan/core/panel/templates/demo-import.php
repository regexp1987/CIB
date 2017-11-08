<?php
    if(function_exists('get_demo_content_demo_list'))
    {
        $magellan_demo_list = get_demo_content_demo_list();
    }
    if(function_exists('get_demo_content_demo_list'))
    {
        $magellan_demo_description = get_demo_content_demo_desc();
    }
            	
	//only proceed if import classes present
	if(class_exists('Magellan_Demo_Export') && class_exists('Magellan_Demo_Import'))
	{
	
		if(!empty($_GET['magellan_action']))
		{
			if(
				$_GET['magellan_action'] == 'install-demo'
				&&
				!empty($_GET['demo'])
				&&
				!empty($magellan_demo_list)
			)
			{
				if(in_array($_GET['demo'], $magellan_demo_list))
				{
					$settings_url = get_admin_url() . 'admin.php?page=' . magellan_gs('theme_slug') . '-admin&view=setup&section=demo_import';
					?>
					<div class="section-item demo-install-report clearfix">
						<p>Do not stop the import process by refreshing/closing the page. Import can take 1-2 minutes to complete. If afterwards, you wish to remove the imported content, simply uninstall the demo.</p>
						<div class="import-report-log">
							<p>
								<strong>Categories:</strong> 
								<span class="loading"><i class="fa fa-spinner fa-spin"></i></span>
								<span class="success"><i class="fa fa-check"></i></span>
								<span class="fail"><i class="fa fa-times"></i></span>
							</p>
							<p>
								<strong>Pages:</strong> 
								<span class="loading"><i class="fa fa-spinner fa-spin"></i></span>
								<span class="success"><i class="fa fa-check"></i></span>
								<span class="fail"><i class="fa fa-times"></i></span>
							</p>
							<p>
								<strong>Posts:</strong> 
								<span class="loading"><i class="fa fa-spinner fa-spin"></i></span>
								<span class="success"><i class="fa fa-check"></i></span>
								<span class="fail"><i class="fa fa-times"></i></span>
							</p>
							<p>
								<strong>Products:</strong> 
								<span class="loading"><i class="fa fa-spinner fa-spin"></i></span>
								<span class="success"><i class="fa fa-check"></i></span>
								<span class="fail"><i class="fa fa-times"></i></span>
							</p>
							<p>
								<strong>Menus:</strong> 
								<span class="loading"><i class="fa fa-spinner fa-spin"></i></span>
								<span class="success"><i class="fa fa-check"></i></span>
								<span class="fail"><i class="fa fa-times"></i></span>
							</p>
							<p>
								<strong>Sidebars:</strong> 
								<span class="loading"><i class="fa fa-spinner fa-spin"></i></span>
								<span class="success"><i class="fa fa-check"></i></span>
								<span class="fail"><i class="fa fa-times"></i></span>
							</p>

							<p>
								<strong>Color settings:</strong> 
								<span class="loading"><i class="fa fa-spinner fa-spin"></i></span>
								<span class="success"><i class="fa fa-check"></i></span>
								<span class="fail"><i class="fa fa-times"></i></span>
							</p>
						</div>
						<div class="success-message">
							<p><strong>Demo installed!</strong></p>
							<p><a href="<?php echo esc_url($settings_url); ?>">Back to theme settings</a></p>
						</div>
					</div>
					<script type="text/javascript">
						jQuery(document).ready(function () {

							var step = 1;
							var demo = '<?php echo esc_attr($_GET['demo']); ?>';
							var key = '<?php echo substr(md5($_GET['demo'] . time()), 0, 8); ?>';
							launch_import_step(demo, key, step);

							function launch_import_step(demo, key, step)
							{
								var data = 'demo=' + demo + '&key=' + key + '&step=' + step;

								var admin_ajax = '<?php echo site_url().'/wp-admin/admin-ajax.php'; ?>';
								var nonce = '<?php echo wp_create_nonce('magellan_demo_import_launcher') ?>';
								var data = { action: 'magellan_demo_import_launcher', _ajax_nonce: nonce, data: data};

								jQuery('.import-report-log p').eq(step-1).addClass('running');

								jQuery.post(admin_ajax, data ,function(msg){
									jQuery('.import-report-log p').eq(step-1).removeClass('running').addClass('done');
									step += 1;
									jQuery('.import-report-log p').eq(step-1).addClass('running');

									if(step < 8)
									{
										launch_import_step(demo, key, step);
									}
									else
									{
										jQuery('.success-message').show();
									}
								}, 'json')
								.fail(function(){
									jQuery('.import-report-log p').eq(step-1).removeClass('running').addClass('failed');
									step += 1;
									jQuery('.import-report-log p').eq(step-1).addClass('running');

									if(step < 8)
									{
										launch_import_step(demo, key, step);
									}
								});
							}
						});
					</script>
					<?php				
				}
			}
			elseif($_GET['magellan_action'] == 'rollback-demo')
			{
				$result = Magellan_Demo_Import::rollbackDemo();
				$settings_url = get_admin_url() . 'admin.php?page=' . magellan_gs('theme_slug') . '-admin&view=setup&section=demo_import';
				?>
				<div class="section-item demo-install-report clearfix">
					<?php
					if($result) 
					{
						?>
						<p><strong>Demo has been uninstalled!</strong></p>
						<p><a href="<?php echo esc_url($settings_url); ?>">Back to theme settings</a></p>
						<?php
					}
					else
					{
						?>
						<p><strong>Error - Previous demo not found!</strong></p>
						<p><a href="<?php echo esc_url($settings_url); ?>">Back to theme settings</a></p>
						<?php
					}
					?>
				</div>
				<?php
			}
			elseif($_GET['magellan_action'] == 'export')
			{
				/* Export */
				if(isset($_GET['preset']))
				{
					$preset = esc_attr($_GET['preset']);
					Magellan_Demo_Export::launchExport($preset);
			
					$upload_dir = wp_upload_dir();
					$url = $upload_dir['baseurl'] .'/' . $preset . '.txt';
					?><a href="<?php echo esc_url($url); ?>">Exported File</a><?php
				}
				else
				{ 
                   ?>No preset provided<?php  
                   
				}
			}
		}
		else
		{
			$install_url = get_admin_url() . 'admin.php?page=' . magellan_gs('theme_slug') . '-admin&view=setup&section=demo_import&magellan_action=install-demo';
			$rollback_url = get_admin_url() . 'admin.php?page=' . magellan_gs('theme_slug') . '-admin&view=setup&section=demo_import&magellan_action=rollback-demo';
			$current = Magellan_Demo_Import::getCurrentImport();
			?>
			<?php if(empty($current)) : ?>
				<div class="section-item clearfix">
					<p>If you wish to start building your site with one of the theme's demo page's you can install it by clicking below.</p>
					<p>Because of licensing prohibitions imported demo's DO NOT contain any images or sliders.</p>
					<p><strong>This is only recommended for empty sites that do not have previous content because a lot of new pages, posts, categories etc. will be created!</strong></p>
				</div>
			<?php endif; ?>

			<?php if(!empty($current) && !empty($magellan_demo_list) && !empty($magellan_demo_description)) : ?>
				<div class="section-item current-active-demo clearfix">
					<h3>Current active demo import</h3>
					<?php 
						if(!empty($magellan_demo_description[$current['demo']]))
						{
							$description = $magellan_demo_description[$current['demo']];
							?>
							<div class="demo-item">
								<div class="demo-image"><img src="<?php echo MAGELLAN_IMG_URL . $description['image']; ?>" alt="" /></div>
								<div class="page-description">
									<h3><?php echo esc_html($description['name']); ?></h3>
									<div class="form-item clearfix">
										<a href="<?php echo esc_url($rollback_url); ?>" class="button-3">Uninstall demo</a>
									</div>
								</div>
							</div>

							<div class="current-demo-description">
								<p>You can remove it by clicking the red uninstall button.
								<strong>Uninstall will delete ALL the previously inserted posts, pages, categories, menus and reset all widgets even if you have made changes to them. Backup your database before proceeding!</strong>
								</p>
								<?php echo '<p>Installed on: <strong>' . $current['date'] . '</strong></p>'; ?>
							</div>
							<?php
						}
					?>
				</div>
			<?php endif; ?>

			<div class="demo-import-list clearfix">
				<h3>Demo's available for import</h3>

				<?php if(!empty($current)) : ?>
					<p>To import a new demo, please uninstall the current one.</p>
				<?php endif; ?>

				<?php 
				if(!empty($magellan_demo_list) && !empty($magellan_demo_description))
				{
					$c = 0;
					foreach($magellan_demo_list as $demo_item)
					{
						if(!empty($magellan_demo_description[$demo_item]) && (empty($current) || $current['demo'] != $demo_item ))
						{
							?>
							<div class="demo-item">
								<div class="demo-image"><img src="<?php echo MAGELLAN_IMG_URL . $magellan_demo_description[$demo_item]['image']; ?>" alt="" /></div>
								<div class="page-description">
									<h3>
										<?php echo esc_html($magellan_demo_description[$demo_item]['name']); ?>
										<?php if(!empty($current) && $current['demo'] == $demo_item) { echo ' - <strong>active</strong>'; } ?>
									</h3>

									<?php if(empty($current)) : ?>
										<div class="form-item clearfix">
											<a href="<?php echo esc_url($install_url); ?>&demo=<?php echo esc_attr($demo_item); ?>" class="button-2">Install demo</a>
										</div>
									<?php endif; ?>
								</div>
							</div>
							<?php

							if($c%2 != 1)
							{
								echo '<div class="demo-item-divider"></div>';
							}
							$c++;
						}
					}
				}
				?>
			</div>
			<?php
		}
	}
	else
	{
		
		echo '<div class="section-item"><h4>' . esc_html__('Please active the "Planetshine Magellan Theme Extension" plugin to enable the demo import feature', 'magellan') . '</h4></div>';
	}