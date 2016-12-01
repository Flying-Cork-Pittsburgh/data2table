<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/anjakammer/data2table
 * @since      1.0.0
 *
 * @package    d2t
 * @subpackage d2t/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    d2t
 * @subpackage d2t/admin
 * @author     Martin Boy & Anja Kammer
 */
class D2T_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $d2t The ID of this plugin.
	 */
	private $d2t;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * The name of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $name The current version of this plugin.
	 */
	private $name;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $d2t The name of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $d2t, $version, $name ) {

		$this->d2t     = $d2t;
		$this->version = $version;
		$this->name    = $name;
	}

	/**
	 * validate sql statement
	 *
	 * @since 1.0.0
	 *
	 * @param string $sql SQL statement for validation
	 *
	 * @return boolean
	 */
	public function sql_statement_is_valid( $sql = null ) {
		// TODO please to refactoring
		$message = '';
		if ( ! empty( $sql ) ) {

			if ( $this->check_table_exists( $this->get_table_name_from_sql( $sql ) ) ) {
				$message = __( 'Can not create a table because the table name already exists.', $this->d2t );
				error_log( $message, 0, plugin_dir_path( __FILE__ ) );

				return false;
			} else {

				return true;
			}
		} else {

			return false;
		}
	}

	/**
	 * gets table name from sql statement
	 *
	 * @since 1.0.0
	 *
	 * @param string $sql SQL statement
	 *
	 * @return String
	 */
	public function get_table_name_from_sql( $sql ) {
		if ( preg_match( '/(?i)create table if not exists\s+(?<tableName>[^\s]+)/', $sql ) ) {
			preg_match( '/(?i)create table if not exists\s+(?<tableName>[^\s]+)/', $sql, $result );
		} else {
			preg_match( '/(?i)create table\s+(?<tableName>[^\s]+)/', $sql, $result );
		}

		return $result['tableName'];
	}

	/**
	 * Create table on the database
	 *
	 * @since 1.0.0
	 *
	 * @param string $sql Valid SQL statement for creating a new table
	 *
	 * @return boolean
	 */
	public function create_table( $sql = null ) {
		global $wpdb;

		if ( $this->sql_statement_is_valid( $sql ) ) {

			//https://codex.wordpress.org/Creating_Tables_with_Plugins#Creating_or_Updating_the_Table
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
			$wpdb->flush();

			return ( $this->check_table_exists( $this->get_table_name_from_sql( $sql ) ) );
		} else {
			error_log( __( 'Failed to create table.', $this->d2t ), 0, plugin_dir_path( __FILE__ ) );

			return false;
		}
	}

	public function ajax_create_table() {

		// get form data
		$sql = ($_POST['sql']);

		if($this->create_table( $sql )){
			echo json_encode( "geklappt" );

		}else{
			echo json_encode( "fail" );
		}

		die();
	}

	/**
	 * Check whether table already exist in dab
	 *
	 * @since 1.0.0
	 *
	 * @param string $table_name [require]
	 *
	 * @return boolean
	 */
	public function check_table_exists( $table_name = null ) {
		global $wpdb;
		if ( empty( $table_name ) ) {
			error_log( __( 'Table name is empty.', $this->d2t ), 0, plugin_dir_path( __FILE__ ) );
			return false;
		}
		
		$result = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) );

		return $table_name === $result;
	}

	/**
	 * Register the menu entry for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function d2t_admin_menu() {
		add_menu_page(
			$this->name,                         // page title
			$this->name,                         // menu title
			// Change the capability to make the pages visible for other users
			'manage_database',                // capability
			$this->d2t,                         // menu slug
			function () {
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/d2t-admin-display.php';

			},              // callback function
			'dashicons-list-view',
			'3.5'                           // better decimal to avoid overwriting
		);
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in D2T_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The D2T_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->d2t, plugin_dir_url( __FILE__ ) . 'css/d2t-admin.css', array(), $this->version, 'all' );
		// Bootstrap
		wp_register_style( 'prefix_bootstrap', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css' );
		wp_enqueue_style( 'prefix_bootstrap' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in D2T_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The D2T_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->d2t, plugin_dir_url( __FILE__ ) . 'js/d2t-admin.js', array( 'jquery' ), $this->version, false );
		// Bootstrap
		wp_register_script( 'prefix_bootstrap', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js' );
		wp_enqueue_script( 'prefix_bootstrap' );
	}

	public function enqueue_ajax_sql_submission() {
		global $wp_query;
		wp_localize_script( 'd2t-admin', 'd2t_run_sql_statement',
			array(
				'ajaxurl'    => admin_url( 'admin-ajax.php' ),
				//url for php file that process ajax request to WP
				'nonce'      => wp_create_nonce( "d2t_run_sql_statement" ),
				// this is a unique token to prevent form hijacking
				'query_vars' => json_encode( $wp_query->query )
			)
		);
	}
}
