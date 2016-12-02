<?php
/**
 * Database Handler file to wrap the Wordpress wpdb functions
 *
 * @link       https://github.com/anjakammer/data2table
 * @since      1.0.0
 *
 * @package    d2t
 * @subpackage d2t/admin/utils
 */

/**
 * Database Handler file to wrap the Wordpress wpdb functions
 *
 * Provides functions to handle and validate database requests
 *
 * @package    d2t
 * @subpackage d2t/admin/utils
 * @author     Martin Boy & Anja Kammer
 */
class D2T_DbHandler {

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
		if ( ! empty( $sql ) ) {
			if ( $this->sql_statement_is_valid( $sql ) ) {

				//https://codex.wordpress.org/Creating_Tables_with_Plugins#Creating_or_Updating_the_Table
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );
				$wpdb->flush();

				// returns always false
				return $this->check_table_exists( $this->get_table_name_from_sql( $sql ) );
			}
		}
		error_log( __( 'Failed to create table.', $this->d2t ), 0, plugin_dir_path( __FILE__ ) );
		return false;
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
		// TODO please do more validation
		$message = '';

		if ( $this->check_table_exists( $this->get_table_name_from_sql( $sql ) ) ) {
			$message = __( 'Can not create a table because the table name already exists.', $this->d2t );
			error_log( $message, 0, plugin_dir_path( __FILE__ ) );

			return false;
		} else {

			return true;
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
		preg_match( '/(?i)create table if not exists\s+(?<tableName>[^\s]+)/', $sql, $result );
		if ( count( $result ) === 0 ) {
			preg_match( '/(?i)create table\s+(?<tableName>[^\s]+)/', $sql, $result );
		}

		return $result['tableName'];
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
		// TODO get mysql setting lower_table_nam 1/0
		$result = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", strtolower( $table_name ) ) );

		return $table_name === $result;
	}
}