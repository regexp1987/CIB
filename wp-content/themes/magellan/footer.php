
        <!-- Footer -->
		<div class="container-flud footer">
			<div class="container">

				<div class="row">
					<?php
						if ( is_active_sidebar( 'footer_sidebar' ) )
						{
							dynamic_sidebar('footer_sidebar'); 
						}
					?>
				</div>
				
				<!-- Copyright -->
				<div class="row">
					<div class="col-md-12 copyright">
						<?php echo magellan_kses_widget_html_field(stripslashes(magellan_gs('copyright'))); ?>
					</div>
				</div>
				
			</div>
		</div>	

		<a href="#" class="back-to-top"><i class="fa fa-caret-up"></i></a>
		
		<!-- END .focus -->
		</div>
	
    <?php wp_footer();?>
        
	<!-- END body -->
	</body>
	
<!-- END html -->
</html>