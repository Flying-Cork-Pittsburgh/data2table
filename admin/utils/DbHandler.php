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
 * Database Handler to wrap the Wordpress wpdb functions
 *
 * Provides functions to handle and validate database requests
 *
 * @package    d2t
 * @subpackage d2t/admin/utils
 * @author     Martin Boy & Anja Kammer
 */
class D2T_DbHandler {

	/**
	 * Database variable of default setting for table name case
	 * http://dev.mysql.com/doc/refman/5.7/en/server-system-variables.html#sysvar_lower_case_table_names
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $is_lower_case_table_names boolean whether db var is true or false
	 */
	private $is_lower_case_table_names;

	public function __construct() {
		global $wpdb;
		// Value of Database var: `lower_case_table_names`
		$this->is_lower_case_table_names = count( $wpdb->get_row( "SHOW VARIABLES WHERE 
			variable_name = 'lower_case_table_names' AND value = '1';" ) ) > 0;
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
		if ( ! empty( $sql ) ) {
			if ( $this->sql_statement_is_valid( $sql ) ) {

				//https://codex.wordpress.org/Creating_Tables_with_Plugins#Creating_or_Updating_the_Table
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				$result = dbDelta( $sql );
				if ( ! $wpdb->result ) {
					$message = __( 'Failed to create table. The SQL Statement was not valid.', $this->d2t );
					throw new Exception( $message );
				}
				$wpdb->flush();

				return $this->check_table_exists( $this->get_table_name_from_sql( $sql ) );
			}
		}
		$message = __( 'Failed to create table. The SQL Statement was not given/valid.', $this->d2t );
		throw new Exception( $message );
	}

	/**
	 * Builds a sql statement from values which are given as a json object
	 *
	 * @since 1.0.0
	 *
	 * @param string $values JSON Object which contains values for table to create
	 *
	 * @return String
	 */
	public function build_sql_statement( $values = null ) {
		global $wpdb;
		$table_structure = json_decode($values);
		$sql = 'CREATE TABLE' . ' '
		       . $table_structure->table_name
		       . ' ('
		       . 'id int NOT NULL,';

		foreach ( $table_structure->columns as $column )
		{
			$sql .= ', ' . $column->name . ' '
			        . $column->type . ' '
			        . $column->constraint ;
		}
		$sql .= ' ' . 'PRIMARY KEY (id))';
		$sql .= ');';
		return $sql;
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

		if ( preg_match( '/(?i)(create table)( if exists)?/', $sql )  // should start with a "create table" statement
		     &&
		     preg_match( '/(\)\;)$/', $sql ) // statement should end with ');'
		) {
			if ( ! $this->check_table_exists( $this->get_table_name_from_sql( $sql ) ) ) {
				return true;
			}
		}
		$message = __( 'Can not create a table because the table name already exists, or it is no valid statement.', $this->d2t );
		throw new Exception( $message );
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
		preg_match( '/(?i)(create table)( if exists)?\s(?<tableName>[^\s]+)/', $sql, $result );

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
			$message = __( 'Table name is empty.', $this->d2t );
			throw new Exception( $message );
		}

		$valid_table_name = $this->is_lower_case_table_names ? strtolower( $table_name ) : $table_name;
		$result           = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $valid_table_name ) );

		return $valid_table_name === $result;
	}
}