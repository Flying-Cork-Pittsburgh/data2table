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
 * @subpackage d2t/admin/views
 */
?>
<script type="text/javascript">
	var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
</script>

<?php
if ( ! current_user_can( 'manage_database' ) ) {
	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
} elseif ( ! isset( $_REQUEST['table'] ) ) {
	wp_die( __( 'Please go back to the dashboard and select a table to manage.' ) );
}

$table_name = $_REQUEST['table'];
$table      = $this->get_data_table( $table_name );
?>
<div class="wrap">
	<h3><a name="top"><?php echo $table_name ?></a></h3>
	<?php include_once 'partials/alerts_inc.php' ?>

	<ul class="tabs">
		<li class="tab-link current" data-tab="tab-data"><?php _e( 'Data', 'd2t' ); ?></li>
		<li class="tab-link" data-tab="tab-import"><?php _e( 'Import', 'd2t' ); ?></li>
	</ul>

	<div id="tab-data" class="tab-content current" role="form">
		<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
		<form id="table-items" method="get">
			<!-- For plugins, we also need to ensure that the form posts back to our current page -->
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
			<!-- Now we can render the completed list table -->
			<?php $table->display() ?>
		</form>
	</div>
	<div id="tab-import" class="tab-content" role="form">

	</div>
</div>
