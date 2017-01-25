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
if ( ! current_user_can( 'activate_plugins' ) ) {
	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
} elseif ( ! isset( $_REQUEST['table'] ) ) {
	wp_die( __( 'Please go back to the dashboard and select a table to manage.' ) );
}

$table_name = $_REQUEST['table'];
$table      = $this->get_data_table( $table_name );
?>
<div class="wrap">
	<h3><a name="top"><?php echo $table_name ?></a></h3>
	<input id="table_name" type="hidden" value="<?php echo $table_name ?>"/>
	<?php include 'partials/alerts_inc.php' ?>

	<ul class="tabs">
		<li class="tab-link current" id="tab-data" data-tab="tab-data"><?php _e( 'Data', 'd2t' ); ?></li>
		<li class="tab-link" data-tab="tab-import"><?php _e( 'Import', 'd2t' ); ?></li>
	</ul>

	<div id="tab-data" class="tab-content current" role="form">
		<form id="table-items" method="get">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
			<div id="data-table">
				<?php $table->display() ?>
			</div>
		</form>
	</div>
	<div id="tab-import" class="tab-content">

		<h5>Upload</h5>

		<form method="post" enctype="multipart/form-data" class="custom-file-upload">
			<?php wp_nonce_field( 'd2t_upload_file' ); ?>
			<input name="FileInput" id="FileInput" class="file-upload-input" accept=".csv,.txt" type="file"/>
			<input type="submit" id="submit-btn" class="file-upload-button btn btn-default" value="Upload file"/>
			<input type="submit" id="submit-btn" class="confirm-import btn btn-default" value="Import Data" style="display: none;"/>
			<table id="options">
				<tr style="display: none;">
					<td>
						<label>Date Format</label>
					</td>
					<td>
						<select id="date_pattern">
							<option value="yyyy-MM-dd" selected>yyyy-MM-dd</option>
							<option value="yy/MM/dd">yy/MM/dd"</option>
							<option value="M/d/yyyy">M/d/yyyy</option>
							<option value="M/d/yy">M/d/yy</option>
							<option value="MM/dd/yy">MM/dd/yy</option>
							<option value="dd-MMM-yy">dd-MMM-yy</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<label>Delimiter</label>
					</td>
					<td>
						<select id="delimiter">
							<option value="," selected>,</option>
							<option value=";">;</option>
							<option value="\t">tab</option>
							<option value="|">|</option>
						</select>
					</td>
				</tr>
			</table>
		</form>
		<div class="clear"></div>

		<div id="preview" style="display: none;">
			<h5>Preview</h5>
			<div id="preview-alert" class="alert alert-warning" role="alert">
				<strong>Warning! </strong>
				<span class="message">The id's might not match the actual database state. </span>
				<span id="import-info"></span>
			</div>
			<div id="data-table-preview"></div>

		</div>

	</div>
</div>
