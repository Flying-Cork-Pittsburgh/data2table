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
}elseif(!isset($_REQUEST['table'])){
	wp_die( __( 'Please go back to the dashboard and select a table to manage.' ) );
}


$table_name = $_REQUEST['table'] ;


$table = $this->get_data_table($table_name);

?>
	<div class="wrap">

		<div id="icon-users" class="icon32"><br/></div>
		<h3> <?php echo $table_name ?></h3>
		<?php
		$message = '';
		if ('delete' === $table->current_action()) {
		$message = '
		<div class="updated below-h2" id="message"><p>' . sprintf('Items deleted: %d',count($_REQUEST['id'])
		                                                                              . '</p></div>');
		}
		?>
		<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
		<form id="movies-filter" method="get">
			<!-- For plugins, we also need to ensure that the form posts back to our current page -->
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<!-- Now we can render the completed list table -->
			<?php $table->display() ?>
		</form>

	</div>
	<?php
//
//$table_name = $_REQUEST['table'] ;
//
//
//echo '<h3>' . $table_name . '</h3>';
//$data = $this->get_data_table($table_name);
//$properties = $this->get_properties($table_name);
//echo '<table id="data" class="table table-striped">'
//			. '<tbody>'
//			. '<tr>';
//foreach ( $properties as $property ) {
//	echo '<th><strong>' . $property['field'] . '</strong> </br> '. $property['type'] . '</th>';
//}
//			echo '</tr>';
//for ( $i = 0; $i < 5; $i++) {
//	echo '<tr>';
//	foreach ( $properties as $property ) {
//		echo '<td>'.$data[$i][$property['field']].'</td>';
//	}
//	echo '</tr>';
//}
//
//


?>
