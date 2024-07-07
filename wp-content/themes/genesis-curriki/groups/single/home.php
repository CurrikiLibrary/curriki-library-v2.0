<?php

get_header();

if ( bp_has_groups() ) : 
	while ( bp_groups() ) : 
		bp_the_group();
		do_action( 'bp_before_group_home_content' ); 
?>
	<div id="item-header">
		<?php locate_template( array( 'groups/single/group-header.php' ), true ) ?>
	</div>

<?php

$group_url = bp_get_group_permalink();
$member_count = groups_get_total_member_count ( bp_get_group_id() );
$forum_count = 0; 
$library_count =  cur_get_resource_total_from_group( bp_get_group_id() );

	// echo "----";
	// echo bp_current_action();
	// echo bp_current_component(); 
	// echo "----";

$forum_ids = groups_get_groupmeta( bp_get_group_id(), 'forum_id', true );
if( !(is_array($forum_ids) && count( $forum_ids ) > 0) )
{
    $forum_ids = array();
}
//$forum_count = count($forum_ids);
$forum_id = count($forum_ids) > 0 ? $forum_ids[0] : 0;
$forum_count = $wpdb->get_var("SELECT count(ID) FROM {$wpdb->prefix}posts where post_type = 'topic' AND post_status = 'publish' AND post_parent = $forum_id");


?>

<style type="text/css">
    .recently-active
    {
        min-width: 100% !important;
    }
    #whats-new-form
    {
        margin-left: 0px !important;
    }
    .internal-page #content ul li{
        float: none;
    }
</style>

<div class="member-tabs page-tabs"><div class="wrap container_12">        


        
<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" role="tablist">
	<li class="ui-state-default ui-corner-top <?php if ( bp_current_action() == "home" ) { ?>ui-tabs-active ui-state-active<?php } ?> " role="tab" tabindex="0" aria-controls="activity" aria-labelledby="ui-id-1" aria-selected="true" aria-expanded="true"><a href="<?php echo $group_url; ?>" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-1"><span class="tab-icon fa fa-home"></span> <span class="tab-text"><?php echo __('Activity','curriki'); ?></span></a></li><li class="ui-state-default ui-corner-top <?php if ( bp_current_action() == "members" ) { ?>ui-tabs-active ui-state-active<?php } ?>" role="tab" tabindex="-1" aria-controls="members" aria-labelledby="ui-id-2" aria-selected="false" aria-expanded="false"><a href="<?php echo $group_url; ?>members" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-2"><span class="tab-icon fa fa-user"></span> <span class="tab-text"><?php echo __('Members','curriki'); ?> <span class="group-number">(<?php echo $member_count; ?>)</span></span></a></li><li class="ui-state-default ui-corner-top <?php if ( bp_current_action() == "library" ) { ?>ui-tabs-active ui-state-active<?php } ?>" role="tab" tabindex="-1" aria-controls="resources" aria-labelledby="ui-id-3" aria-selected="false" aria-expanded="false"><a href="<?php echo $group_url; ?>library" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-3"><span class="tab-icon fa fa-book"></span> <span class="tab-text"><?php echo __('Resources','curriki'); ?> <span class="group-number">(<?php echo $library_count; ?>)</span></span></a></li><?php if( count( $forum_ids ) > 0 ) { ?><li class="ui-state-default ui-corner-top" role="tab" tabindex="-1" aria-controls="forums" aria-labelledby="ui-id-4" aria-selected="false" aria-expanded="false"><a href="<?php echo $group_url; ?>forum" class="ui-tabs-anchor <?php if ( bp_current_action() == "forum" ) { ?>ui-tabs-active ui-state-active<?php } ?>" role="presentation" tabindex="-1" id="ui-id-4"><span class="tab-icon fa fa-comments"></span> <span class="tab-text"><?php echo __('Forum','curriki'); ?> <span class="group-number">(<?php echo $forum_count; ?>)</span></span></a></li><?php } ?>
</ul>

</div></div>

<div class="wrap container_12">
                                <div class="tab-contents ui-tabs-panel ui-widget-content ui-corner-bottom" id="activity" aria-labelledby="ui-id-1" role="tabpanel" aria-hidden="false">
                                    <div class="activity-sidebar page-sidebar grid_2">
                                        <?php
                                            $user_groups = bp_get_user_groups( get_current_user_id(), array(
                                                'is_admin'     => true,
                                                'is_mod'       => null,
                                            ) );

                                            if ( isset( $user_groups[ bp_get_group_id() ] ) )
                                            {                                                
                                                $protocol = is_ssl() ? 'https://' : 'http://';                   
                                                $lang_in_slug = "";
                                                if( defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE != 'en')
                                                {
                                                    $lang_in_slug = '/'.ICL_LANGUAGE_CODE;
                                                }
                                        ?>
                                                <p>
                                                    <a href="<?php echo site_url().$lang_in_slug. "/groups/".  bp_get_group_slug()."/admin"; ?>" class="button manage-group-button"><?php echo __('Manage Group','curriki'); ?></a>
                                                </p>
                                        <?php
                                            }
                                        ?>
                                        <?php if ( $member_count > 0 ) { 

                                            $args = array(
                                                'group_id'            => bp_get_current_group_id(),
                                                'per_page'            => 3,
                                                'page'                => 1,
                                                'exclude_admins_mods' => true,
                                                'exclude_banned'      => true,
                                                'exclude'             => false,
                                                'group_role'          => array(),
                                                'search_terms'        => false,
                                                'type'                => 'last_joined', // last_active?
                                            );

                                            $members = groups_get_group_members( $args );
                                            $members = $members["members"];
                                            //echo "<pre>";
                                            //var_dump ($members);die

                                        ?>
                                        <h4 class="sidebar-title"><?php echo __('Recently Active','curriki'); ?></h4>
                                        <div class="recently-active member-card card rounded-borders-full border-grey">
                                            <ul>
                                            <?php

                                                foreach ( $members as $member ) {

                                                    // print_r ($member);

                                                    $the_member_id = $member->ID;

                                                    $location_arr = array();
                                                    
                                                    $city = cur_get_user_nonwp_data ( $the_member_id, 'city' );                                                                                                        
                                                    if(strlen($city) > 0)
                                                        $location_arr[] = $city;
                                                    
                                                    
                                                    $state = cur_get_user_nonwp_data ( $the_member_id, 'state' );                                                    
                                                    if(strlen($state) > 0)
                                                        $location_arr[] = $state;
                                                    
                                                    $country = cur_get_user_nonwp_data ( $the_member_id, 'country' );                                                    
                                                    if(strlen($country) > 0)
                                                        $location_arr[] = $country;
                                                    
                                                    /*
                                                    if ( $city || $state || $country ) {
                                                        $location = $city . ', ' . $state . ' ' . $country;
                                                    }
                                                    */
                                                    $location = implode(',', $location_arr);
                                                    $bio = cur_get_user_nonwp_data ( $the_member_id, 'bio' );
                                                    $profession = false;


                                            ?>
                                            <li class="member">
                                                <!-- <img class="border-grey" src="placehold.it/100x100" alt="member-name" /> -->
                                                <?php //echo bp_core_fetch_avatar( 'item_id='.$the_member_id ); ?>
                                                <?php       
                                                        $ignore_img_ext = array("docx");
                                                        $userid = $the_member_id;
                                                        global $wpdb;
                                                        $q_userinfo = "select * from users where userid = '".$userid."'";        
                                                        $userinfo = $wpdb->get_row($q_userinfo);                                                        
                                                      
                                                        if(empty($userinfo->uniqueavatarfile) || (!empty($userinfo->uniqueavatarfile) && in_array(pathinfo($userinfo->uniqueavatarfile,PATHINFO_EXTENSION), $ignore_img_ext)) ){
                                                            echo '<img width="150" height="150" alt="Profile picture of '.$full_name.'" class="avatar user-'.$userinfo->uniqueavatarfile.'-avatar avatar-'.$userinfo->uniqueavatarfile.' photo" src="'.get_stylesheet_directory_uri().'/images/user-icon-sample.png">';
                                                        }else{
                                                            echo '<img width="150" height="150" alt="Profile picture of '.$full_name.'" class="avatar user-'.$userinfo->uniqueavatarfile.'-avatar avatar-'.$userinfo->uniqueavatarfile.' photo" src="https://archivecurrikicdn.s3-us-west-2.amazonaws.com/avatars/'.$userinfo->uniqueavatarfile.'">';
                                                        }                                                        
                                                ?>
                                                
                                                <div class="member-info">
                                                    <span class="member-name name"><?php echo bp_core_get_userlink( $the_member_id ); ?></span>
                                                    <span class="occupation"><?php if ( $profession ) { echo $profession; } ?></span><span class="location"><?php if ( $location ) { echo $location; } ?></span>
                                                </div>
                                            </li>
                                            <?php } ?>
                                            </ul>
                                            <a href="<?php echo $group_url; ?>members">
                                                <div class="card-button"><?php echo __('Browse All Members','curriki'); ?></div>
                                            </a>
                                        </div>
                                        <?php } ?>
                                        <!--  <h4 class="sidebar-title">Recent Discussions</h4>
                                        <div class="recent-discussion card rounded-borders-full border-grey">
                                            <ul class="discussion">
                                                <li><a href="#">Discussion Topic Can be Very Long at Times, so We Need Space</a></li>
                                                <li><a href="#">Discussion Topic Goes Here</a></li>
                                                <li><a href="#">Discussion Topic Can be Very Long at Times, so We Need Space</a></li>
                                            </ul>
                                            <a href="<?php //echo $group_url; ?>forums">
                                                <div class="card-button">Browse All Conversations</div>
                                            </a>
                                        </div>-->
                                    </div>
                                    <div class="activity-content grid_10">
<!--                                        
                                        <div class="group-search page-search">
                                            <div class="search-input grid_6 alpha">
                                                <div class="search-field">
                                                    <input type="text" placeholder="Search" class="rounded-borders-left">
                                                </div>
                                                <div class="search-button">
                                                    <button class="rounded-borders-right" type="submit"><span class="search-button-icon fa fa-search"></span></button>
                                                </div>
                                            </div>
                                            <div class="search-dropdown grid_4 omega">
                                                <select>
                                                    <option>English</option>
                                                </select>
                                            </div>
                                        </div>
                                        -->
                                        <div class="group-activity-container page-container rounded-borders-full border-grey">
                                            
											<div id="item-body">
											<?php 
												do_action( 'bp_before_group_body' );
                                                        
                                                /**
                                                 * Does this next bit look familiar? If not, go check out WordPress's
                                                 * /wp-includes/template-loader.php file.
                                                 *
                                                 * @todo A real template hierarchy? Gasp!
                                                 */

                                                    // Looking at home location
                                                    if ( bp_is_group_home() ) :

                                                        if ( bp_group_is_visible() ) {

                                                            // Load appropriate front template
                                                            bp_groups_front_template_part();

                                                        } else {

                                                            /**
                                                             * Fires before the display of the group status message.
                                                             *
                                                             * @since 1.1.0
                                                             */
                                                            do_action( 'bp_before_group_status_message' ); ?>

                                                            <div id="message" class="info">
                                                                <p><?php bp_group_status_message(); ?></p>
                                                            </div>

                                                            <?php

                                                            /**
                                                             * Fires after the display of the group status message.
                                                             *
                                                             * @since 1.1.0
                                                             */
                                                            do_action( 'bp_after_group_status_message' );

                                                        }

                                                    // Not looking at home
                                                    else :

                                                        // Group Admin
                                                        if     ( bp_is_group_admin_page() ) : bp_get_template_part( 'groups/single/admin'        );

                                                        // Group Activity
                                                        elseif ( bp_is_group_activity()   ) : bp_get_template_part( 'groups/single/activity'     );

                                                        // Group Members
                                                        elseif ( bp_is_group_members()    ) : bp_groups_members_template_part();

                                                        // Group Invitations
                                                        elseif ( bp_is_group_invites()    ) : bp_get_template_part( 'groups/single/send-invites' );

                                                        // Membership request
                                                        elseif ( bp_is_group_membership_request() ) : bp_get_template_part( 'groups/single/request-membership' );

                                                        // Anything else (plugins mostly)
                                                        else                                : bp_get_template_part( 'groups/single/plugins'      );

                                                        endif;

                                                    endif;

                                                /**
                                                 * Fires after the display of the group home body.
                                                 *
                                                 * @since 1.2.0
                                                 */
												do_action( 'bp_after_group_body' ); 
											?>
											</div>

                                        </div>
                                    </div>
                                </div>

                                
                            </div>
                            

						


                        </div>

<?php /* 
	<div id="item-nav">
		<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
			<ul>
				<?php bp_get_options_nav(); do_action( 'bp_group_options_nav' ); ?>
			</ul>
			<div class="clear"></div>
		</div>
	</div> */ ?>


<?php 
		do_action( 'bp_after_group_home_content' );
	endwhile; 
endif;
get_footer();

