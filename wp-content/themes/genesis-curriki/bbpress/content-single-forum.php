<?php

/**
 * Single Forum Content Part
 *
 * @package bbPress
 * @subpackage Theme
 */

?>
<?php

get_header();
global $wpdb;
$group_url = bp_get_group_permalink();
$member_count = groups_get_total_member_count ( bp_get_group_id() );
$library_count =  cur_get_resource_total_from_group( bp_get_group_id() );
$forum_ids = groups_get_groupmeta( bp_get_group_id(), 'forum_id', true );
if( !(is_array($forum_ids) && count( $forum_ids ) > 0) )
{
    $forum_ids = array();
}
//$forum_count = count($forum_ids);
$forum_id = count($forum_ids) > 0 ? $forum_ids[0] : 0;
//$forum_count = $wpdb->get_var("SELECT count(ID) FROM {$wpdb->prefix}posts where post_type = 'topic' AND post_status = 'publish' AND post_parent = $forum_id");    //prepared statement added

$forum_count = $wpdb->get_var( $wpdb->prepare(      
        "
                SELECT count(ID)
                FROM {$wpdb->prefix}posts
                where post_type = 'topic'
                AND post_status = 'publish'
                AND post_parent = %d
        ", 
        $forum_id
) );
?>
<style type="text/css">
    div#item-nav {
        display: none;
    }
</style>
<div class="member-tabs page-tabs"><div class="wrap container_12">                
<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" role="tablist">
<li class="ui-state-default ui-corner-top <?php if ( bp_current_action() == "home" ) { ?>ui-tabs-active ui-state-active<?php } ?> " role="tab" tabindex="0" aria-controls="activity" aria-labelledby="ui-id-1" aria-selected="true" aria-expanded="true"><a href="<?php echo $group_url; ?>" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-1"><span class="tab-icon fa fa-home"></span> <span class="tab-text"><?php echo __('Activity','curriki'); ?></span></a></li><li class="ui-state-default ui-corner-top <?php if ( bp_current_action() == "members" ) { ?>ui-tabs-active ui-state-active<?php } ?>" role="tab" tabindex="-1" aria-controls="members" aria-labelledby="ui-id-2" aria-selected="false" aria-expanded="false"><a href="<?php echo $group_url; ?>members" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-2"><span class="tab-icon fa fa-user"></span> <span class="tab-text"><?php echo __('Members','curriki'); ?> <span class="group-number">(<?php echo $member_count; ?>)</span></span></a></li><li class="ui-state-default ui-corner-top <?php if ( bp_current_action() == "library" ) { ?>ui-tabs-active ui-state-active<?php } ?>" role="tab" tabindex="-1" aria-controls="resources" aria-labelledby="ui-id-3" aria-selected="false" aria-expanded="false"><a href="<?php echo $group_url; ?>library" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-3"><span class="tab-icon fa fa-book"></span> <span class="tab-text"><?php echo __('Resources','curriki'); ?> <span class="group-number">(<?php echo $library_count; ?>)</span></span></a></li><?php if( count( $forum_ids ) > 0 ) { ?><li class="ui-state-default ui-corner-top <?php if ( bp_current_action() == "forum" ) { ?>ui-tabs-active ui-state-active<?php } ?>" role="tab" tabindex="-1" aria-controls="forums" aria-labelledby="ui-id-4" aria-selected="false" aria-expanded="false"><a href="<?php echo $group_url; ?>forum" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-4"><span class="tab-icon fa fa-comments"></span> <span class="tab-text"><?php echo __('Forum','curriki'); ?> <span class="group-number">(<?php echo $forum_count; ?>)</span></span></a></li><?php } ?>
</ul>

</div></div>


<div class="wrap container_12">
                                <div class="tab-contents ui-tabs-panel ui-widget-content ui-corner-bottom" id="activity" aria-labelledby="ui-id-1" role="tabpanel" aria-hidden="false">
                                    <div class="activity-sidebar page-sidebar grid_2">
                                        
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
                                            //print_r ($members);

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
                                                        $userid = $the_member_id;
                                                        
//                                                        $q_userinfo = "select * from users where userid = '".$userid."'";        //prepared statement added
//                                                        $userinfo = $wpdb->get_row($q_userinfo);
                                                        
                                                        $userinfo = $wpdb->get_row( $wpdb->prepare(      
                                                                "
                                                                    select * from users where userid = %d
                                                                ", 
                                                                $userid
                                                        ) );
                                                        
                                                        $userinfo->full_name = trim($userinfo->firstname.' '.$userinfo->lastname);
                                                        if(empty($userinfo->uniqueavatarfile)){
                                                            echo '<img width="150" height="150" alt="Profile picture of '.$userinfo->full_name.'" class="avatar user-'.$userinfo->uniqueavatarfile.'-avatar avatar-'.$userinfo->uniqueavatarfile.' photo" src="'.get_stylesheet_directory_uri().'/images/user-icon-sample.png">';
                                                        }else{
                                                            echo '<img width="150" height="150" alt="Profile picture of '.$userinfo->full_name.'" class="avatar user-'.$userinfo->uniqueavatarfile.'-avatar avatar-'.$userinfo->uniqueavatarfile.' photo" src="https://archivecurrikicdn.s3-us-west-2.amazonaws.com/avatars/'.$userinfo->uniqueavatarfile.'">';
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

<script type="text/javascript">    
    jq(document).ready(function () {
        <?php
        if ( is_user_logged_in() && groups_is_user_member( get_current_user_id() , bp_get_group_id() ))
        {
        ?>
            jq(".forum-home-tabs").tabs();

            if(jq("#tabs-2 .error").get().length > 0)
            {
                jq(".forum-home-tabs").tabs("option", "active", 1);
            }
        <?php         
        } ?>
      
    });
</script>    
    
<div id="bbpress-forums-wrapper" class="forum-home-tabs">

	<?php bbp_breadcrumb(); ?>
    
       <?php
        if ( is_user_logged_in() && groups_is_user_member( get_current_user_id() , bp_get_group_id() ) )
        {
        ?>
        <ul>
            <li><a class="forum-tab-btn" href="#tabs-1"><?php echo __('Topics','buddypress'); ?></a></li>
            <li><a class="forum-tab-btn" href="#tabs-2"><?php echo __('New Topic','buddypress'); ?></a></li>
        </ul>       
    
        <div class="secondary-btn subscribe-btn">
            <?php bbp_forum_subscription_link(); ?>
        </div>
	
        <?php         
        } ?>
    
	<?php do_action( 'bbp_template_before_single_forum' ); ?>

	<?php if ( post_password_required() ) : ?>

		<?php bbp_get_template_part( 'form', 'protected' ); ?>

	<?php else : ?>

		<?php bbp_single_forum_description(); ?>

		<?php if ( bbp_has_forums() ) : ?>

			<?php bbp_get_template_part( 'loop', 'forums' ); ?>

		<?php endif; ?>

		<?php if ( !bbp_is_forum_category() && bbp_has_topics() ) : ?>                        
                        <div id="tabs-1">        
                            <?php bbp_get_template_part( 'pagination', 'topics'    ); ?>

                            <?php bbp_get_template_part( 'loop',       'topics'    ); ?>

                            <?php bbp_get_template_part( 'pagination', 'topics'    ); ?>
                        </div>                        
    
                        <?php
                        if ( groups_is_user_member( get_current_user_id() , bp_get_group_id() ))
                        {
                        ?>
                        <div id="tabs-2">
                            <?php bbp_get_template_part( 'form',       'topic'     ); ?>
                        </div>
                        <?php } ?> 
    
		<?php elseif ( !bbp_is_forum_category() ) : ?>
                        <div id="tabs-1">
			<?php   bbp_get_template_part( 'feedback',   'no-topics' ); ?>
                        </div>
    
                        <?php
                        if ( groups_is_user_member( get_current_user_id() , bp_get_group_id() ))
                        {
                        ?>
                            <div id="tabs-2">
                                <?php bbp_get_template_part( 'form',       'topic'     ); ?>
                            </div>
                        <?php                         
                        } ?>
    
		<?php endif; ?>

	<?php endif; ?>

	<?php do_action( 'bbp_template_after_single_forum' ); ?>

</div>

                                        </div>
                                    </div>
                                </div>
</div>

