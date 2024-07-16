<?php
do_action( 'bp_before_activity_loop' );



if ( bp_has_activities( bp_ajax_querystring( 'activity' ) ) ) :
?>
	<noscript>
		<div class="pagination">
			<div class="pag-count"><?php bp_activity_pagination_count(); ?></div>
			<div class="pagination-links"><?php bp_activity_pagination_links(); ?></div>
		</div>
	</noscript>
	<?php if ( empty( $_POST['page'] ) ) : ?>
		<ul id="activity-stream" class="activity-list item-list">
	<?php endif; ?>

	<?php 
        $count_activities = 0;
        while ( bp_activities() ) : bp_the_activity(); ?>

		<?php gconnect_locate_template( array( 'activity/entry.php' ), true, false ); ?>

	<?php 
        if(++$count_activities > 9) break;
        endwhile; ?>

	<?php if ( bp_activity_has_more_items() ) : ?>
		<li class="load-more">
			<a href="#more"><?php _e( 'View More', 'buddypress' ); ?></a>
		</li>
	<?php endif; ?>

	<?php if ( empty( $_POST['page'] ) ) : ?>
		</ul>
	<?php endif; ?>

<?php else : ?>
	<div id="message" class="info">
		<p><?php 
                    //_e( 'Sorry, there was no activity found. Please try a different filter.', 'buddypress' ); 
                    _e( 'Sorry, there was no activity found. Be the first to contribute!', 'buddypress' ); 
                    ?></p>
	</div>
<?php
endif;

do_action( 'bp_after_activity_loop' );
?>

<form action="" name="activity-loop-form" id="activity-loop-form" method="post">
	<?php wp_nonce_field( 'activity_filter', '_wpnonce_activity_filter' ); ?>
</form>