	<?php if ( comments_open() ) : ?>
    
        <div class="row">
            <div class="col-md-12 comments comments-wrapper">
        
			<?php if ( have_comments() ) : ?>
		
				<div class="title-default">
					<span><?php esc_html_e('Comments', 'magellan'); ?></span>
				</div>

				<ul>
					<?php wp_list_comments( array( 'callback' => 'magellan_comments' ) ); ?>
				</ul>

				<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
					<!-- BEGIN .pages -->	
					<div class="pages clearfix">
						<div class="nav-previous"><?php previous_comments_link( '<span></span>' .  esc_html__('Previous', 'magellan')); ?></div>
						<div class="nav-next"><?php next_comments_link(esc_html__('Next', 'magellan') . '<span></span>'); ?></div>
					</div>
				<?php endif; ?>
				
			<?php endif; ?>	
		
					
			<?php
				$user_id = get_current_user_id();
                $avatar = get_avatar( $user_id, $size='30' );
                $post_id = get_the_ID();
                $commenter = wp_get_current_commenter();
                $req = get_option( 'require_name_email' );
                $aria_req = ( $req ? " aria-required='true'" : '' );
                
                $args = array(
                    'fields' => apply_filters( 'comment_form_default_fields', array(
                         'author' =>
                            '<div class="row"><div class="col-md-4 col-sm-12"><p class="input-wrapper">' .
                            '<label>' . esc_html__('Name', 'magellan') . ( $req ? ' <b>*</b>' : '' ) . '</label><input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '"' .
                             $aria_req . ' /></p></div>',

                          'email' =>
                            '<div class="col-md-4 col-sm-12"><p class="input-wrapper">' .
							'<label>' . esc_html__('E-mail', 'magellan') . ( $req ? ' <b>*</b>' : '' ) . '</label><input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '"' .
                             $aria_req . ' /></p></div>',

                          'url' =>
							'<div class="col-md-4 col-sm-12"><p class="input-wrapper">' .
                            '<label>' . esc_html__('Website', 'magellan') .'</label><input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '"/></p></div></div>',
                        )
                    ),
                    'comment_field' =>  '<div class="row"><div class="col-md-12"><p class="input-wrapper">' .
                                        '<textarea id="comment" name="comment" aria-required="true"></textarea>' .
                                        '</p></div></div>',
                    'logged_in_as' => '<p><div class="logged_in_inner">' . $avatar . ' <a class="user" href="' . get_edit_user_link() . '">' . $user_identity . '</a>' . ' <a class="logout" href="' . wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) . '">' . esc_html__('Log out', 'magellan') . '</a>' . '</div></p>',
                    'submit_button' => '<div class="row"><div class="col-md-12"><p class="input-wrapper">' . '<input type="submit" value="' . esc_html__('Post comment', 'magellan') . '">' . '<span class="notes">' . esc_html__('Your email address will not be published. Required fields are marked ', 'magellan') . '<b>*</b></span>' . '</p></div></div>',
                    'comment_notes_before' => '',
					'id_submit' => 'hidden-submit',
                    'title_reply' => ''
                );
                comment_form($args);
            ?>
            </div>
        </div>

    <?php endif; ?>