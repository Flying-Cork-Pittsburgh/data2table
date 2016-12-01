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
?>
<script type="text/javascript">
    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
</script>

<?
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
	<form id="sql-statement-form">
		<?php wp_nonce_field( 'd2t_run_sql_statement' ); ?>
		<div class="form-group">
			<textarea rows="10" name="sql_statement" id="sql_statement" class="form-control" dir="ltr" placeholder="CREATE TABLE table_name ..."></textarea>
		</div>
		<div class="form-group">
			<div class="btn-group" role="group" aria-label="...">
				<input type="submit" id="submit-sql-statement" class="btn btn-primary btn-lg" name="submit-sql-statement"
				       value="<?php _e( 'Run', 'd2t' ); ?>"/>
				<input type="button" class="btn btn-default btn-lg" name="cancel"
				       value="<?php _e( 'Cancel', 'd2t' ); ?>"/>
			</div>
		</div>
	</form>
</div>