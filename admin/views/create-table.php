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
}
?>
<div class="wrap">
	<h3>Create Table</h3>
	<?php include_once 'partials/alerts_inc.php' ?>

	<ul class="tabs">
		<li class="tab-link current" data-tab="tab-gui"><?php _e( 'Creator', 'd2t' ); ?></li>
		<li class="tab-link" data-tab="tab-sql"><?php _e( 'SQL Editor', 'd2t' ); ?></li>
	</ul>

	<div id="tab-gui" class="tab-content current" role="form">
		<div id="meta-data">
			<input type="text" id="table_name" size="40" maxlength="64" value=""
			       placeholder="table_name" autofocus="" required class="form-control required-input"/>
		</div>

		<table id="columns" class="table table-striped">
			<tbody>
			<tr>
				<th>Name</th>
				<th>Type</th>
				<th>Default Value</th>
				<th>NOT NULL</th>
				<th>UNIQUE</th>
			</tr>
			<tr>
				<td><input value="id" disabled class="field_type form-control"></td>
				<td><input value="int" disabled class="field_type form-control"></td>
				<td><input value="AUTO_INCREMENT" disabled class="field_type form-control"></td>
				<td><input type="checkbox" checked disabled class="field_type form-check"></td>
				<td><input type="checkbox" checked disabled class="field_type form-check"></td>
			</tr>

			<?php
			for ( $i = 1; $i <= 5; $i ++ ): ?>
				<tr class="column">
					<td><!-- column name -->
						<input type="text" maxlength="64" class="field_name form-control" title="Column" size="10"
						       value="" placeholder="column_name" required>
					</td>
					<td><!-- column type -->
						<select class="field_type form-control">
							<option
								title="A 4-byte integer, signed range is -2,147,483,648 to 2,147,483,647, unsigned range is 0 to 4,294,967,295">
								int
							</option>
							<option
								title="A string with a length of 255 bytes, the effective maximum length is subject to the maximum row size">
								varchar(255)
							</option>
							<option
								title="A TEXT column with a maximum length of 4,294,967,295 or 4GiB (2^32 - 1) characters, stored with a four-byte prefix indicating the length of the value in bytes">
								longtext
							</option>
							<option title="A date, supported range is 1000-01-01 to 9999-<12-31">
								date
							</option>
						</select>
					</td>
					<td><!-- column default -->
						<input type="text" class="field_default_value form-control"
						       size="12" value="" placeholder="NONE">
					</td>
					<td><!-- column NULL -->
						<input type="checkbox" value="NOT NULL" class="field_not_null form-check">
					</td>
					<td>
						<input type="checkbox" value="UNIQUE" class="field_is_unique form-check">
					</td>
				</tr>
			<?php endfor; ?>
			</tbody>
		</table>
		<div class="btn-group pull-right" id="table-actions" role="group" aria-label="...">
				<button type="button" class="btn btn-outline-success add-column">Add column</button>
				<button type="button" class="btn btn-outline-danger delete-column">remove column</button>
		</div>
		<form id="sql-creator-form">
			<?php wp_nonce_field( 'd2t_create_sql_statement' ); ?>
			<textarea rows="10" name="sql_from_creator" id="sql_from_creator" class="form-control" dir="ltr"
			          style="display: none;"></textarea>
			<div class="form-group">
				<div class="btn-group" role="group" aria-label="...">
					<input type="submit" id="create-sql-from-creator" class="btn btn-primary"
					       name="create-sql-from-creator"
					       value="<?php _e( 'Create SQL', 'd2t' ); ?>"/>
					<input type="submit" id="submit-from-creator" class="submit-sql-statement btn btn-primary"
					       name="submit-from-creator" style="display: none;"
					       value="<?php _e( 'Submit SQL', 'd2t' ); ?>"/>
					<input type="button" id="clear-sql-from-creator" class="clear-input btn btn-default"
					       name="cancel_creator" disabled
					       value="<?php _e( 'Cancel', 'd2t' ); ?>"/>
				</div>
			</div>
		</form>
	</div>

	<div id="tab-sql" class="tab-content">
		<h2><?php _e( 'Create-Table Statement', 'd2t' ); ?></h2>
		<div>
			<strong><?php _e( 'Separate Multiple Statements With A New Line', 'd2t' ); ?></strong><br/>
		</div>
		<form id="sql-statement-form">
			<?php wp_nonce_field( 'd2t_run_sql_statement' ); ?>
			<div class="form-group">
					<textarea rows="10" name="sql_statement" id="sql_statement" class="form-control" dir="ltr"
					          placeholder="CREATE TABLE table_name ..."></textarea>
			</div>
			<div class="form-group">
				<div class="btn-group" role="group" aria-label="...">
					<input type="submit" id="submit-sql-from-text" class="submit-sql-statement btn btn-primary"
					       name="submit-sql-from-text"
					       value="<?php _e( 'Run', 'd2t' ); ?>"/>
					<input type="button" id="clear-sql-from-text" class="clear-input btn btn-default"
					       name="cancel"
					       value="<?php _e( 'Cancel', 'd2t' ); ?>"/>
				</div>
			</div>
		</form>
	</div>
</div>