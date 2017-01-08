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
	<h3>Dashboard</h3>
	<div class="card-columns">
		<?php
		$tables = $this->get_custom_tables();
		foreach ( array_keys( $tables ) as $table_name ) {
			echo '<div class="card">';
			echo '<h4 class="card-header">' . $table_name . '</h4>';
			echo '<div class="card-block">';
			echo '<p class="card-text">'.
			     $tables[ $table_name ]['row_count'] . ' Rows | '.
			     'Last Updated: '.
			     $tables[ $table_name ]['last_updated'] . '</p>' .
			     '</div>' .
			     '<ul class="list-group list-group-flush">';

			$columns      = $tables[ $table_name ]['columns'];
			$column_count = sizeof($columns);
			for ( $i = 0; $i < 5; $i++) {
				echo '<li class="list-group-item"><strong>' . $columns[$i]['field'] . '</strong> - '
				     . $columns[$i]['type'] . '</li>';
			}
			if ( $column_count > 4 ) {
				echo '<li class="list-group-item">...</li>';
			}
			echo '</ul>';
			echo '<a href="'
			     . get_admin_url(get_current_blog_id(), 'admin.php?page=d2t-manage-table&table=' . $table_name )
			     .'" class="btn btn-info btn-block">Manage</a>' .
			     '</div>';
		}
		?>
	</div>
</div>