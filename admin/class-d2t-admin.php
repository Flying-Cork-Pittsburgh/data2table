<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    data2table
 * @subpackage data2table/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    data2table
 * @subpackage data2table/admin
 * @author     Your Name <email@example.com>
 */
class D2T_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $d2t    The ID of this plugin.
	 */
	private $d2t;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $d2t       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $d2t, $version ) {

		$this->d2t = $d2t;
		$this->version = $version;

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

	}

}
