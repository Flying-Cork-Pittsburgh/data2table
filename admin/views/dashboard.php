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
		<div class="card">
			<h4 class="card-header">table_name</h4>
			<div class="card-block">
				<p class="card-text">324 Rows - 1324MB</p>
			</div>
			<ul class="list-group list-group-flush">
				<li class="list-group-item">id (int)</li>
				<li class="list-group-item">name (varchar)</li>
				<li class="list-group-item">data (longtext)</li>
			</ul>
			<a href="#" class="btn btn-info btn-block">Manage</a>
		</div>
		<div class="card">
			<h4 class="card-header">table_name</h4>
			<div class="card-block">
				<p class="card-text">324 Rows - 1324MB</p>
			</div>
			<ul class="list-group list-group-flush">
				<li class="list-group-item">id (int)</li>
				<li class="list-group-item">name (varchar)</li>
				<li class="list-group-item">data (longtext)</li>
			</ul>
			<a href="#" class="btn btn-info btn-block">Manage</a>
		</div>
		<div class="card">
			<h4 class="card-header">table_name</h4>
			<div class="card-block">
				<p class="card-text">324 Rows - 1324MB</p>
			</div>
			<ul class="list-group list-group-flush">
				<li class="list-group-item">id (int)</li>
				<li class="list-group-item">name (varchar)</li>
				<li class="list-group-item">data (longtext)</li>
				<li class="list-group-item">id (int)</li>
				<li class="list-group-item">name (varchar)</li>
				<li class="list-group-item">data (longtext)</li>
			</ul>
			<a href="#" class="btn btn-info btn-block">Manage</a>
		</div>
		<div class="card">
			<h4 class="card-header">table_name</h4>
			<div class="card-block">
				<p class="card-text">324 Rows - 1324MB</p>
			</div>
			<ul class="list-group list-group-flush">
				<li class="list-group-item">id (int)</li>
				<li class="list-group-item">name (varchar)</li>
				<li class="list-group-item">data (longtext)</li>
				<li class="list-group-item">id (int)</li>
				<li class="list-group-item">name (varchar)</li>
				<li class="list-group-item">data (longtext)</li>
			</ul>
			<a href="#" class="btn btn-info btn-block">Manage</a>
		</div>
		<div class="card">
			<h4 class="card-header">table_name</h4>
			<div class="card-block">
				<p class="card-text">324 Rows - 1324MB</p>
			</div>
			<ul class="list-group list-group-flush">
				<li class="list-group-item">id (int)</li>
				<li class="list-group-item">name (varchar)</li>
				<li class="list-group-item">data (longtext)</li>
			</ul>
			<a href="#" class="btn btn-info btn-block">Manage</a>
		</div>
		<div class="card">
			<h4 class="card-header">table_name</h4>
			<div class="card-block">
				<p class="card-text">324 Rows - 1324MB</p>
			</div>
			<ul class="list-group list-group-flush">
				<li class="list-group-item">id (int)</li>
				<li class="list-group-item">name (varchar)</li>
				<li class="list-group-item">data (longtext)</li>
				<li class="list-group-item">id (int)</li>
				<li class="list-group-item">name (varchar)</li>
				<li class="list-group-item">data (longtext)</li>
			</ul>
			<a href="#" class="btn btn-info btn-block">Manage</a>
		</div>
	</div>

</div>