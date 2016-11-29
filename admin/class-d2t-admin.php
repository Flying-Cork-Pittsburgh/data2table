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
     * The name of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $name    The current version of this plugin.
     */
    private $name;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $d2t       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $d2t, $version, $name ) {

		$this->d2t = $d2t;
		$this->version = $version;
        $this->name = $name;
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
