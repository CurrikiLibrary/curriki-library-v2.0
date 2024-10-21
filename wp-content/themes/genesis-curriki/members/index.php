<?php
get_header("custom");
do_action( 'bp_before_directory_members_page' );
do_action( 'bp_before_directory_members' );
?>
	<style>
		.member-header {
			background: #F4F8FF;
			padding-top: 25px;
			padding-bottom: 25px;
		}

		.member-heading-title {
			color: #084892;
			font-family: "Montserrat", Sans-serif;
			font-size: 48px;
			font-weight: 600;
			line-height: 1.4em;
			text-align: left;
		}

		.member-listing-text {
			text-align: left;
			font-family: "Montserrat", Sans-serif;
			font-size: 24px;
			font-weight: 400;
		}
		.item-list-tabs {
			width: 50%;
		}
		.dir-search-custom {
			padding-top: 6px;
		}

		.row {
			margin-right: 0px;
			margin-left: 0px;
		}

		input:hover[type="submit"] {
			background-color: var( --primary-hover-cur ) !important;
		}

		.order-by-tabs {
			text-align: right;
		}

		.members-header-text-align {
			text-align: right;
		}

		.members-header-text-align-left {
			text-align: left;
		}

		.container {
			margin-top: 20px;
			margin-bottom: 20px;
		}

		.filter-controls {
			margin-bottom: 20px;
		}

		.members-list-custom {
			/* margin-left: 85px; */
		}
	</style>
	<form action="" method="post" id="members-directory-form" class="dir-form">
		<div class="member-header">
			<div class="container">				
				<h3 class="member-heading-title"><?php _e( 'Members Directory', 'buddypress' ); ?></h3>
				<p class="member-listing-text">Members Listing</p>
			</div>
		</div>		
		<?php do_action( 'bp_before_directory_members_content' ); ?>
		<!-- bootstrap 2 column grid -->
		<div class="container">
			<div class="row filter-controls">
				<div class="col-md-6">
					<div class="members-header-text-align-left" role="navigation">
						<ul>
							<li class="selected" id="members-all"><a href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_members_root_slug() ); ?>"><?php printf( __( 'All Members <span>%s</span>', 'buddypress' ), bp_get_total_member_count() ); ?></a></li>

							<?php if ( is_user_logged_in() && bp_is_active( 'friends' ) && bp_get_total_friend_count( bp_loggedin_user_id() ) ) : ?>
								<li id="members-personal"><a href="<?php echo bp_loggedin_user_domain() . bp_get_friends_slug() . '/my-friends/'; ?>"><?php printf( __( 'My Friends <span>%s</span>', 'buddypress' ), bp_get_total_friend_count( bp_loggedin_user_id() ) ); ?></a></li>
							<?php endif; ?>
							<?php do_action( 'bp_members_directory_member_types' ); ?>
						</ul>
						<div class="clear"></div>
					</div><!-- .item-list-tabs -->
				</div>
				<div class="col-md-6">
					<div id="members-dir-search" class="members-header-text-align" role="search">
						<?php bp_directory_members_search_form(); ?>
					</div><!-- #members-dir-search -->
				</div>
			</div>

			<div class="row filter-controls">
				<div class="col-md-6">
					<?php if ( bp_has_members( bp_ajax_querystring( 'members' ) ) ) { ?>
						<div id="pag-top" class="members-header-text-align-left">
							<div class="pagination-links" id="member-dir-pag-top">
								<?php bp_members_pagination_links(); ?>
							</div>
						</div>
					<?php } ?>
				</div>
				<div class="col-md-6">
					<div class="members-header-text-align" id="bpsubnav" role="navigation">
						<ul>
							<?php do_action( 'bp_members_directory_member_sub_types' ); ?>

							<li id="members-order-select" class="last filter">
								<label for="members-order-by"><?php _e( 'Order By:', 'buddypress' ); ?></label>
								<select id="members-order-by">
									<option value="active"><?php _e( 'Last Active', 'buddypress' ); ?></option>
									<option value="newest"><?php _e( 'Newest Registered', 'buddypress' ); ?></option>

									<?php if ( bp_is_active( 'xprofile' ) ) : ?>
										<option value="alphabetical"><?php _e( 'Alphabetical', 'buddypress' ); ?></option>
									<?php endif; ?>
									<?php do_action( 'bp_members_directory_order_options' ); ?>
								</select>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div class="container">
			<?php locate_template( array( 'members/members-loop.php' ), true ); ?>
		</div>

		<div class="container">
			<?php if ( bp_has_members( bp_ajax_querystring( 'members' ) ) ) { ?>
				<?php do_action( 'bp_after_directory_members_list' ); bp_member_hidden_fields(); ?>

				<div id="pag-bottom" class="pagination">
					<!-- <div class="pag-count" id="member-dir-count-bottom">
						<?php // bp_members_pagination_count(); ?>
					</div> -->
					<div class="pagination-links" id="member-dir-pag-bottom">
						<?php bp_members_pagination_links(); ?>
					</div>
				</div>
			<?php } else { ?>
				<div id="message" class="info">
						<?php
						global $bp;            
						if($bp->current_component == 'following')
						{ ?>
								<p><?php echo __('This user is not currently following anyone','curriki'); ?></p>
						<?php }else{ ?>
					<p><?php _e( "Sorry, no members were found.", 'buddypress' ); ?></p>
						<?php } ?>
				</div>
			<?php } ?>
		</div>

		<?php do_action( 'bp_directory_members_content' ); wp_nonce_field( 'directory_members', '_wpnonce-member-filter' ); do_action( 'bp_after_directory_members_content' ); ?>
	</form><!-- #members-directory-form -->
<?php
do_action( 'bp_after_directory_members' );
do_action( 'bp_after_directory_members_page' );
get_footer();
