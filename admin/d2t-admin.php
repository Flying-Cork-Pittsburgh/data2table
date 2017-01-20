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
	 * The DB Handler is responsible for handling database request and validation.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      D2T_DbHandler $db handles all database requests and validation
	 */
	protected $db;

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

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/utils/DbHandler.php';
		$this->db = new D2T_DbHandler();

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/utils/ListTable.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/utils/DataTable.php';

	}

	/**
	 * provides all tables which are not part of the Host-System
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_custom_tables() {
		return $this->db->get_tables();
	}

	/**
	 * returns a table object to display data
	 *
	 * @since 1.0.
	 *
	 * @param string $table_name table name to fetch the data from
	 *
	 * @return object
	 */
	public function get_data_table( $table_name ) {

		$data_table = new D2T_DataTable($table_name, $this->db);
		$data_table->prepare_items();
		return $data_table;
//		try{
//			return $this->db->get_data($table_name);
//		}catch(Exception $e){
//			return array();
//		}
	}

	/**
	 * handles ajax request: create table
	 *
	 * @since 1.0.0
	 *
	 */
	public function ajax_create_table() {
		$sql = ( $_POST['sql'] );

		try{
			$this->db->create_table( $sql );
		}catch (Exception $e){
			echo wp_send_json_error($e->getMessage());
		}
		echo wp_send_json_success(  __( 'Table successfully created', $this->d2t ) );
	}

	/**
	 * handles ajax request: build sql statement
	 *
	 * @since 1.0.0
	 *
	 */
	public function ajax_build_sql_statement() {
		$values = ( $_POST['values'] );
		$sql = '';

		try{
			$sql = $this->db->build_sql_statement( $values );
		}catch (Exception $e){
			echo wp_send_json_error($e->getMessage());
		}
		echo wp_send_json_success( $sql );
	}

	/**
	 * Register the menu entry for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function d2t_admin_menu() {
		add_menu_page(
			$this->name,                          // page title
			$this->name,                         // menu title
			// Change the capability to make the pages visible for other users
			'activate_plugins',                // capability
			$this->d2t . '-dashboard',                         // menu slug
			function () {
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/views/dashboard.php';

			},              // callback function
			'dashicons-list-view',
			'3.5'                           // better decimal to avoid overwriting
		);

		add_submenu_page(
			$this->d2t . '-dashboard',
			__('new Table', $this->d2t),
			__('new Table', $this->d2t),
			'activate_plugins',
			$this->d2t .'-new-table',
			function () {
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/views/create-table.php';
			}
			);
		add_submenu_page(
			null,
			__('manage Table', $this->d2t),
			__('manage Table', $this->d2t),
			'activate_plugins',
			$this->d2t .'-manage-table',
			function () {
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/views/manage-table.php';
			}
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
		wp_enqueue_script( $this->d2t + '-ajax-requests', plugin_dir_url( __FILE__ ) . 'js/ajax-requests.js',
			array( 'jquery' ), $this->version, false );
		// Bootstrap
		wp_register_script( 'prefix_bootstrap', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js' );
		wp_enqueue_script( 'prefix_bootstrap' );
	}

	public function enqueue_ajax_sql_submission() {
		global $wp_query;
		wp_localize_script( 'ajax-requests', 'd2t_run_sql_statement',
			array(
				'ajaxurl'    => admin_url( 'admin-ajax.php' ),
				//url for php file that process ajax request to WP
				'nonce'      => wp_create_nonce( "d2t_run_sql_statement" ),
				// this is a unique token to prevent form hijacking
				'query_vars' => json_encode( $wp_query->query )
			)
		);

		wp_localize_script( 'ajax-requests', 'd2t_create_sql_statement',
			array(
				'ajaxurl'    => admin_url( 'admin-ajax.php' ),
				//url for php file that process ajax request to WP
				'nonce'      => wp_create_nonce( "d2t_create_sql_statement" ),
				// this is a unique token to prevent form hijacking
				'query_vars' => json_encode( $wp_query->query )
			)
		);
	}
}
