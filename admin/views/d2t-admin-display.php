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

<?
if ( ! current_user_can( 'manage_database' ) ) {
	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
}
?>
<div class="wrap">

	<ul class="tabs">
		<li class="tab-link current" data-tab="tab-gui"><?php _e( 'Click \'n\' Create', 'd2t' ); ?></li>
		<li class="tab-link" data-tab="tab-sql"><?php _e( 'SQL Editor', 'd2t' ); ?></li>
	</ul>


	<div id="tab-gui" class="tab-content current">
		<div id="table_name">
			<label for="table_name">Table name:</label>
			<input type="text" name="table_name" id="table_name" size="40" maxlength="64" value="" autofocus="" required />
		</div>
		<table id="columns" class="table table-striped">
			<tbody>
			<tr>
				<th>Name</th>
				<th>Type</th>
				<th>Default Value</th>
				<th>can be empty</th>
				<th>must be unique</th>
				<th>Comments</th>
			</tr>
			<tr>
				<td class="center"><!-- column name --><input id="field_0_1" type="text" name="field_name[0]"
				                                              maxlength="64" class="form-control" title="Column" size="10"
				                                              value=""></td>
				<td class="center"><!-- column type -->
					<select class="column_type form-control" name="field_type[0]" id="field_0_2">
						<option
							title="A 4-byte integer, signed range is -2,147,483,648 to 2,147,483,647, unsigned range is 0 to 4,294,967,295">
							INT
						</option>
						<option
							title="A variable-length (0-65,535) string, the effective maximum length is subject to the maximum row size">
							VARCHAR
						</option>
						<option
							title="A TEXT column with a maximum length of 65,535 (2^16 - 1) characters, stored with a two-byte prefix indicating the length of the value in bytes">
							TEXT
						</option>
						<option title="A date, supported range is 1000-01-01 to 9999-12-31">DATE</option>
					</select>
				</td>

				<td class="center"><!-- column default -->
					<select name="field_default_type[0]" id="field_0_4" class="default_type form-control">
						<option value="NONE">None</option>
						<option value="USER_DEFINED">As defined:</option>
						<option value="NULL">NULL</option>
						<option value="CURRENT_TIMESTAMP">CURRENT_TIMESTAMP</option>
					</select>
					<br><input type="text" name="field_default_value[0]" size="12" value=""
					           class="default_value form-control" style="display: none;">
				</td>
				<td class="center"><!-- column NULL -->
					<input name="field_null[0]" id="field_0_6" type="checkbox"
				                                              value="NULL" class="allow_null form-control">
				</td>
				<td class="center">
					<input name="field_null[0]" id="field_0_6_1" type="checkbox" value="NULL" class="is_unique form-control"></td>
				<td class="center"><!-- column comments -->
					<input id="field_0_9" type="text" name="field_comments[0]" size="12" maxlength="1024" value=""
					       class="form-control"></td>
			</tr>
			</tbody>
		</table>
		<button type="button" class="btn btn-outline-success">Add column</button>
	</div>

	<div id="tab-sql" class="tab-content">
		<h2><?php _e( 'Run SQL Statements', 'd2t' ); ?></h2>
		<div>
			<strong><?php _e( 'Separate Multiple Statements With A New Line', 'd2t' ); ?></strong><br/>
			<p><?php _e( 'Use Only CREATE statements.', 'd2t' ); ?></p>
		</div>
		<form id="sql-statement-form">
			<?php wp_nonce_field( 'd2t_run_sql_statement' ); ?>
			<div class="form-group">
					<textarea rows="10" name="sql_statement" id="sql_statement" class="form-control" dir="ltr"
					          placeholder="CREATE TABLE table_name ..."></textarea>
			</div>
			<div class="form-group">
				<div class="btn-group" role="group" aria-label="...">
					<input type="submit" id="submit-sql-statement" class="btn btn-primary btn-lg"
					       name="submit-sql-statement"
					       value="<?php _e( 'Run', 'd2t' ); ?>"/>
					<input type="button" class="btn btn-default btn-lg" name="cancel"
					       value="<?php _e( 'Cancel', 'd2t' ); ?>"/>
				</div>
			</div>
		</form>
	</div>


</div>