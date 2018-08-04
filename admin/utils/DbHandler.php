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
 * @author     Anja Kammer
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
			variable_name = 'lower_case_table_names' AND value = '1';"
			)
		                                   ) > 0;
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
				dbDelta( $sql );
				if ( ! $wpdb->result ) {
					$message = 'Failed to create table. The SQL Statement was not valid.';
					throw new Exception( $message );
				}
				$wpdb->flush();

				return $this->check_table_exists( $this->get_table_name_from_sql( $sql ) );
			}
		}
		$message = 'Failed to create table. The SQL Statement was not given.';
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
		$sql = 'CREATE TABLE' . ' '
		       . $values['table_name']
		       . ' ('
		       . 'id int NOT NULL AUTO_INCREMENT';
		foreach ( $values['columns'] as $column ) {
			$sql .= ', ' . $column['name'];
			$sql .= ' ' . $column['type'];
			$sql .= ( $column['default'] == '' ) ? '' : ' ' . $column['default'];
			$sql .= ( $column['constraint'] == '' ) ? '' : ' ' . $column['constraint'];
		}
		$sql .= ', ' . 'PRIMARY KEY (id)';
		$sql .= ' );';

		return $sql;
	}

	/**
	 * provides all tables which are not part of the Wordpress-System
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_tables() {
		global $wpdb;
		$result_set = array();
		$sql        = 'SELECT TABLE_NAME, TABLE_ROWS, CREATE_TIME FROM INFORMATION_SCHEMA.TABLES' .
		              ' WHERE TABLE_TYPE = \'BASE TABLE\' AND TABLE_SCHEMA=' . '\'' . $wpdb->dbname . '\'';

		$tables = $wpdb->get_results( $sql );
		foreach ( $tables as $table ) {
			$table_name = $table->TABLE_NAME;
			if ( ! preg_match( '/(?<!prefix )' . $wpdb->prefix . '/', $table_name ) ) {
				$result_set[ $table_name ]['row_count']    = $table->TABLE_ROWS;
				$result_set[ $table_name ]['created'] =
					( strlen( $table->CREATE_TIME ) < 1 ? '-' : $table->CREATE_TIME );
				$result_set[ $table_name ]['columns']      = $this->get_columns( $table_name );
			}
		}

		return $result_set;
	}

	/**
	 * provides all column names and types of a given table name
	 *
	 * @since 1.0.0
	 *
	 * @param string $table_name table name to describe
	 *
	 * @return array
	 */
	public function get_columns( $table_name ) {
		global $wpdb;
		$columns = $wpdb->get_results( 'DESCRIBE ' . $table_name . ';' );

		$result_set = [];
		foreach ( $columns as $column ) {
			$result_set[] = array( 'field' => $column->Field, 'type' => $column->Type );
		}

		return $result_set;
	}

	/**
	 * provides all column names only of a given table name
	 *
	 * @since 1.0.0
	 *
	 * @param string $table_name table name to describe
	 *
	 * @return array
	 */
	public function get_columns_without_types( $table_name ) {
		global $wpdb;
		$columns = $wpdb->get_results( 'DESCRIBE ' . $table_name . ';' );

		$result_set = [];
		foreach ( $columns as $column ) {
			$result_set[ $column->Field ] = $column->Field;
		}

		return $result_set;
	}

	/**
	 * provides all column names and types of a given table name
	 *
	 * @since 1.0.0
	 *
	 * @param string $table_name table name to fetch the data from
	 *
	 * @return array
	 */
	public function get_data( $table_name ) {
		global $wpdb;
		if ( $this->check_table_exists( $table_name ) ) {
			$results = $wpdb->get_results( 'SELECT * FROM ' . $table_name, ARRAY_A );

			return $results;
		}
		throw new Exception( 'Table ' . $table_name . ' does not exists.' );
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

	/**
	 * validate sql statement
	 *
	 * @since 1.0.0
	 *
	 * @param string $sql SQL statement for validation
	 *
	 * @return boolean
	 */
	private function sql_statement_is_valid( $sql = null ) {

		if ( preg_match( '/(?i)(create table)( if exists)?/', $sql )  // should start with a "create table" statement
		     &&
		     preg_match( '/(\)\;)$/', $sql ) // statement should end with ');'
		) {
			if ( ! $this->check_table_exists( $this->get_table_name_from_sql( $sql ) ) ) {
				return true;
			}
		}
		throw new Exception(
			'Can not create a table because the table name already exists, or it is no valid statement.'
		);
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
	private function get_table_name_from_sql( $sql ) {
		preg_match( '/(?i)(create table)( if exists)?\s(?<tableName>[^\s]+)/', $sql, $result );

		return $result['tableName'];
	}

	/**
	 * returns array of data to insert as a preview
	 *
	 * @since 1.0.0
	 *
	 * @param string $table_name name of table to insert data into
	 * @param array $data set of data to insert
	 *
	 * @return array preview of inserted data
	 */
	public function test_data_insert( $table_name, $data ) {
		global $wpdb;
		$table_clone = $this->create_table_clone( $table_name );
		$properties  = array_fill_keys(
			array_keys( $this->get_columns_without_types( $table_clone ) ),
			''
		);
		$result      = array();
		$count       = sizeof( $data );
		for ( $i = 0; $i < $count; $i ++ ) {
			$result[ $i ] = array_merge( $properties, $data[ $i ] );
		}
		try {
			$this->import_data( $table_clone, $result );
			$preview = $wpdb->get_results( "SELECT * FROM " . $table_clone, ARRAY_A );
		} finally {
			$sql = 'DROP TABLE ' . $table_clone;
			$wpdb->query( $sql );
		}
		return $preview;
	}

	/**
	 * imports data from file
	 *
	 * @since 1.0.0
	 *
	 * @param string $table_name name of table to insert data into
	 * @param array $data set of data to insert
	 */
	public function run_data_insert( $table_name, $data ) {
		$properties  = array_fill_keys(
			array_keys( $this->get_columns_without_types( $table_name ) ),
			''
		);
		$result      = array();
		$count       = sizeof( $data );
		for ( $i = 0; $i < $count; $i ++ ) {
			$result[ $i ] = array_merge( $properties, $data[ $i ] );
		}
		$this->import_data( $table_name, $result );
	}

	/**
	 * creates a table clone of an given table provided by table name
	 *
	 * @since 1.0.0
	 *
	 * @param string $table_name name of table to clone
	 *
	 * @return string table name of clone
	 */
	private function create_table_clone( $table_name ) {
		global $wpdb;
		$tmp_table_name = $table_name . '_clone';
		$sql            = 'CREATE TABLE ' . $tmp_table_name . ' LIKE ' . $table_name;
		$wpdb->query( $sql );

		return $tmp_table_name;
	}

	/**
	 * replaces all rows with unique value, and inserts all other rows
	 *
	 * @since 1.0.0
	 *
	 * @param string $table_name name of table to import
	 * @param array $data data to import
	 */
	private function import_data( $table_name, $data ) {
		global $wpdb;
		foreach($data as $row){
			$wpdb->replace( $table_name, $row );
			if ( ! $wpdb->result ) {
				throw new Exception( $wpdb->last_error );
			}
		}
	}
}