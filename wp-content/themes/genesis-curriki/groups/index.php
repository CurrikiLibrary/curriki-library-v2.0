<?php 
// gconnect_get_header();
get_header('custom');
do_action( 'bp_before_directory_groups_page' );
do_action( 'bp_before_directory_groups' );

global $group_loop_source;
$group_loop_source = "group";

?>
<style type="text/css">
    .container_12{
        /*border: 5px solid red;*/        
    }
    .card-button .generic-button{
        margin-bottom: 0px !important;
    }    

    .group-header {
        background: #F4F8FF;
        padding-top: 25px;
        padding-bottom: 25px;
    }

    .group-heading-title {
        color: #084892;
        font-family: "Montserrat", Sans-serif;
        font-size: 48px;
        font-weight: 600;
        line-height: 1.4em;
        text-align: left;
    }

    .group-listing-text {
        text-align: left;
        font-family: "Montserrat", Sans-serif;
        font-size: 24px;
        font-weight: 400;
    }

    .breadcrumb {
        background-color: transparent;
    }

    .container {
        margin-top: 20px;
        margin-bottom: 20px;
    }

    .group-header-text-align {
        text-align: right;
    }

    .group-header-text-align-left {
        text-align: left;
    }

    .search-dropdown-custom {
        width: 100%;
    }

    .filter-controls {
        margin-bottom: 20px;
    }
</style>

<div class="group-header">
    <div class="container">				
        <h3 class="group-heading-title"><?php _e( 'Community', 'buddypress' ); ?></h3>
        <p class="group-listing-text">Groups Listing</p>
    </div>
</div>

<div class="container">
    <div class="row filter-controls">
        <div class="col-md-6">
            <?php
                if(is_user_logged_in())
                {
            ?> 
                <nav class="group-header-text-align-left" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li><a href="#">Community</a></li>
                        <li class="active">My Groups</li>
                    </ol>
                </nav>
            <?php
                } else {
            ?>
                <nav class="group-header-text-align-left" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li><a href="#">Community</a></li>
                        <li class="active">Groups</li>
                    </ol>
                </nav>
            <?php
                }
            ?>
        </div>
        <div class="col-md-6">
            <div class="search-dropdown search-dropdown-custom grid_6 omega group-header-text-align">
                <?php bp_directory_groups_search_form(); ?>
            </div>
        </div>
    </div>

    <div class="row filter-controls">
        <div class="col-md-6">
            x
        </div>
        <div class="col-md-6">
            y
        </div>
    </div>
</div>

<div class="main-container">
    <div class="wrap container_12">
            
            
                <div class="actions-row grid_12 clearfix">
                    <div class="grid_6 alpha">
                    <?php if ( is_user_logged_in() && bp_user_can_create_groups() ) : ?> 
                        <a class="button create-group-button" href="<?php echo trailingslashit( get_site_url() . '/' . bp_get_groups_root_slug() . '/create' ); ?>"><?php _e( 'Create a Group', 'buddypress' ); ?></a>
                    <?php endif; ?>
                    </div>
                   
                </div>
		<?php //do_action( 'bp_before_directory_groups_content' ); ?>
        <!--
		<div id="group-dir-search" class="dir-search">
			<?php //bp_directory_groups_search_form(); ?>
		</div>-->
        <form action="" method="post" id="groups-directory-form" class="dir-form">
            <div class="item-list-tabs" id="subnav" aria-label="<?php esc_attr_e( 'Groups directory secondary navigation', 'buddypress' ); ?>" role="navigation">
                <ul>
                    <?php

                    /**
                     * Fires inside the groups directory group types.
                     *
                     * @since 1.2.0
                     */
                    do_action( 'bp_groups_directory_group_types' ); ?>

                    <li id="groups-order-select" class="last filter">

                        <label for="groups-order-by"><?php esc_html_e( 'Order By:', 'buddypress' ); ?></label>

                        <select id="groups-order-by">
                            <option value="active"><?php esc_html_e( 'Last Active', 'buddypress' ); ?></option>
                            <option value="popular"><?php esc_html_e( 'Most Members', 'buddypress' ); ?></option>
                            <option value="newest"><?php esc_html_e( 'Newly Created', 'buddypress' ); ?></option>
                            <option value="alphabetical"><?php esc_html_e( 'Alphabetical', 'buddypress' ); ?></option>

                            <?php

                            /**
                             * Fires inside the groups directory group order options.
                             *
                             * @since 1.2.0
                             */
                            do_action( 'bp_groups_directory_order_options' ); ?>
                        </select>
                    </li>
                </ul>
            </div>
                    
            <div id="groups-dir-list" class="groups dir-list">
                    <?php locate_template( array( 'groups/groups-loop.php' ), true ); ?>
            </div><!-- #groups-dir-list -->
                    

            <?php do_action( 'bp_directory_groups_content' ); wp_nonce_field( 'directory_groups', '_wpnonce-groups-filter' ); do_action( 'bp_after_directory_groups_content' ); ?>

        </form><!-- #groups-directory-form -->
        
    </div>
</div>

<?php 
do_action( 'bp_after_directory_groups' );
do_action( 'bp_after_directory_groups_page' );
get_footer();
