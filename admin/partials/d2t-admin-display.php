<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * Template : Plugin Admin Page
 * URL: `/wp-admin/admin.php?page=d2t`
 *
 * @link       https://github.com/anjakammer/data2table
 * @since      1.0.0
 *
 * @package    d2t
 * @subpackage d2t/admin/partials
 */

echo $this->say_hey( "mööp" );

if ( ! current_user_can( 'manage_database' ) ) {
	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
}
?>
<div class="wrap">
	<h2><?php _e( 'Run SQL Statements', 'd2t' ); ?></h2>
	<div>
		<strong><?php _e( 'Separate Multiple Statements With A New Line', 'd2t' ); ?></strong><br/>
		<p><?php _e( 'Use Only CREATE statements.', 'd2t' ); ?></p>
	</div>
	<form method="post"
	      action="<?php echo admin_url( 'admin.php?page=' . plugin_basename( __FILE__ ) ); ?>">
		<?php wp_nonce_field( 'd2t_create_table' ); ?>
		<div class="form-group">
			<textarea rows="10" name="sql_statement" class="form-control" dir="ltr"></textarea>
		</div>
		<div class="form-group">
			<div class="btn-group" role="group" aria-label="...">
				<input type="submit" class="btn btn-primary btn-lg" name="create_table"
				       value="<?php _e( 'Run', 'd2t' ); ?>"/>
				<input type="button" class="btn btn-default btn-lg" name="cancel"
				       value="<?php _e( 'Cancel', 'd2t' ); ?>"/>
			</div>
		</div>
	</form>
</div>