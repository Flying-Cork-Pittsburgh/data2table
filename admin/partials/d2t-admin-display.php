<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/anjakammer/data2table
 * @since      1.0.0
 *
 * @package    d2t
 * @subpackage d2t/admin/partials
 */


if ( !current_user_can( 'manage_database' ) )  {
wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
}
?>
<div class="wrap">
<p>Here comes content - whoop whoop</p>
<form method="post" action="<?php echo admin_url('admin.php?page='.plugin_basename(__FILE__)); ?>">
	<?php wp_nonce_field('d2t_create_table'); ?>
<div class="wrap">
    <h2><?php _e('Run SQL Statements', 'd2t'); ?></h2>
    <br style="clear" />
    <div>
        <strong><?php _e('Separate Multiple Statements With A New Line', 'd2t'); ?></strong><br />
        <p style="color: green;"><?php _e('Use Only CREATE statements.', 'd2t'); ?></p>
    </div>
    <table class="form-table">
        <tr>
            <td align="center"><textarea rows="10" name="sql_statement" style="width: 99%;" dir="ltr" ></textarea></td>
        </tr>
        <tr>
            <td align="center"><input type="submit" name="create_table" value="<?php _e('Run', 'd2t'); ?>" class="button"/>
                &nbsp;&nbsp;<input type="button" name="cancel" value="<?php _e('Cancel', 'd2t'); ?>" class="button" /></td>
        </tr>
    </table>
</div>
</form>
</div>