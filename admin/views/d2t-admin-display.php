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
		<li class="tab-link current" data-tab="tab-gui"><?php _e( 'Create Table', 'd2t' ); ?></li>
		<li class="tab-link" data-tab="tab-sql"><?php _e( 'SQL Editor', 'd2t' ); ?></li>
	</ul>


	<div id="tab-gui" class="tab-content current">
		<div id="meta-data">
			<input type="text" name="table_name" id="table_name" size="40" maxlength="64" value="" placeholder="table_name" autofocus="" required />
		</div>

		<table id="columns" class="table table-striped">
			<tbody>
			<tr>
				<th></th>
				<th>Name</th>
				<th>Type</th>
				<th>Default Value</th>
				<th>can be <br>NULL</th>
				<th>must be <br>unique</th>
				<th>Comments</th>
			</tr>

			<?php
			for ($i = 1; $i <= 5; $i++): ?>
			<tr data-column="<?php echo $i ?>">
				<td>
					<button class="btn btn-danger delete-column" data-column="<?php echo $i ?>">X</button>
				</td>
				<td><!-- column name -->
					<input id="field_name_<?php echo $i ?>" type="text" name="field_name_<?php echo $i ?>"
					       data-column="<?php echo $i ?>"
					       maxlength="64" class="field_name form-control" title="Column" size="10"
					       value="" placeholder="column_name">
				</td>
				<td><!-- column type -->
					<select class="field_type form-control" name="field_type_<?php echo $i ?>"
					        id="field_type_<?php echo $i ?>" data-column="<?php echo $i ?>">
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
						<option title="A date, supported range is 1000-01-01 to 9999-<12-31">DATE</option>
					</select>
				</td>
				<td><!-- column default -->
					<select name="field_default_type_<?php echo $i ?>" id="field_default_type_<?php echo $i ?>"
					        class="field_default_type form-control" data-column="<?php echo $i ?>">
						<option value="NONE" title="Empty field value">None</option>
						<option value="USER_DEFINED" title="Click to define">As defined:</option>
						<option value="NULL" title="Missing or not existent data">NULL</option>
						<option value="CURRENT_TIMESTAMP" title="Format: YYYY-MM-DD">CURRENT_TIMESTAMP</option>
					</select>
					<input type="text" name="field_default_value_<?php echo $i ?>"
					       id="field_default_value_<?php echo $i ?>" class="field_default_value form-control"
					       data-column="<?php echo $i ?>" size="12" value="" style="display: none;">
				</td>
				<td><!-- column NULL -->
					<input name="field_allow_null_<?php echo $i ?>" id="field_allow_null_<?php echo $i ?>"
					       data-column="<?php echo $i ?>" type="checkbox"
				                                              value="NULL" class="field_allow_null form-control">
				</td>
				<td>
					<input name="field_is_unique_<?php echo $i ?>" id="field_is_unique_<?php echo $i ?>"
					       data-column="<?php echo $i ?>" type="checkbox" value="UNIQUE"
					       class="is_unique form-control"></td>
				<td><!-- column comments -->
					<input type="text" name="field_comments_<?php echo $i ?>" id="field_comments_<?php echo $i ?>"
					       size="12" maxlength="1024" value=""
					       class="field_comments form-control" data-column="<?php echo $i ?>" placeholder="comment">
				</td>
			</tr>
			<?php endfor; ?>
			</tbody>
		</table>
		<!-- TODO the column_id is not incremented - so the saving will not work -->
<!--		<button type="button" class="btn btn-outline-success add-column">Add column</button>-->
		<textarea rows="10" name="sql_from_creator" id="sql_from_creator" class="form-control" dir="ltr"
		          style="display: none;" ></textarea>
		<div class="form-group">
			<div class="btn-group" role="group" aria-label="...">
				<input type="submit" id="create-from-creator" class="btn btn-primary btn-lg"
				       name="create-from-creator"
				       value="<?php _e( 'Create SQL', 'd2t' ); ?>"/>
				<input type="submit" id="submit-from-creator" class="submit-sql-statement btn btn-primary btn-lg"
				       name="submit-from-creator" style="display: none;"
				       value="<?php _e( 'Submit SQL', 'd2t' ); ?>"/>
				<input type="button" class="btn btn-default btn-lg" name="cancel_creator"
				       value="<?php _e( 'Cancel', 'd2t' ); ?>"/>
			</div>
		</div>	</div>

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
					<input type="submit" id="submit-sql-from-text" class="submit-sql-statement btn btn-primary btn-lg"
					       name="submit-sql-from-text"
					       value="<?php _e( 'Run', 'd2t' ); ?>"/>
					<input type="button" class="btn btn-default btn-lg" name="cancel"
					       value="<?php _e( 'Cancel', 'd2t' ); ?>"/>
				</div>
			</div>
		</form>
	</div>


</div>