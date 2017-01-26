<?php
/**
 * Data Importer file to validate and import data from files into database
 *
 * @link       https://github.com/anjakammer/data2table
 * @since      1.0.0
 *
 * @package    d2t
 * @subpackage d2t/admin/utils
 */

/**
 * Data Importer file to validate and import data from files into database
 *
 * Provides functions to validate and import data into database
 *
 * @package    d2t
 * @subpackage d2t/admin/utils
 * @author     Martin Boy & Anja Kammer
 */
class D2T_DataImporter {

	/**
	 * The DB Handler is responsible for handling database request and validation.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      D2T_DbHandler $db handles all database requests and validation
	 */
	protected $db;

	private $allowed_file_types;
	private $max_filesize;
	private $upload_path;
	private $fixed_file_name;
	private $delimiter;
	private $date_pattern;

	public function __construct( $db ) {
		$this->db                 = $db;
		$this->allowed_file_types = array(
			'.csv',
			'.txt'
		);    // These will be the types of file that will pass the validation.
		$this->max_filesize       = 524288;                     // Maximum file size in BYTES (currently 0.5MB).
		$wp_upload_dir            = wp_upload_dir();
		$this->upload_path        = $wp_upload_dir['basedir'] . '/files/'; // or plugin_dir_path( __FILE__ ) . 'files/';
		$this->fixed_file_name    = 'data';
		$this->delimiter          = ',';
		$this->date_pattern       = 'yyyy-MM-dd';
	}

	/**
	 * validates and uploads file, returns filepath
	 *
	 * @since 1.0.0
	 *
	 * @param Object $file file to upload
	 * @param string $table_name table name to describe
	 *
	 * @return string filepath
	 */
	public function upload_file( $file, $table_name ) {
		$filename_temp = $file['name'];
		$ext           = substr( $filename_temp, strpos( $filename_temp, '.' ), strlen( $filename_temp ) - 1 );

		if ( ! $this->db->check_table_exists( $table_name ) ) {
			throw new Exception( 'Table: ' . $table_name . ' does not exists!' );
		}
		if ( ! file_exists( $this->upload_path ) ) {
			mkdir( $this->upload_path, 0777, true );
		}
		if ( ! $this->file_is_valid( $file, $ext ) && ! is_writable( $this->upload_path ) ) {
			throw new Exception( 'You cannot upload to the specified directory, please contact Administrator.' );
		}
		if ( ! move_uploaded_file( $file['tmp_name'], $this->upload_path . $this->fixed_file_name . $ext ) ) {
			throw new Exception( 'Failed to move File into directory: '
			                     . $this->upload_path . ' please contact Administrator.'
			);
		}

		return $this->upload_path . $this->fixed_file_name . $ext;
	}

	/**
	 * returns data of file as array, properties as strings
	 *
	 * @since 1.0.0
	 *
	 * @param string $filepath path to file
	 *
	 * @return array
	 */
	public function get_file_data( $filepath, $delimiter = null, $date_pattern = null ) {
		$delimiter    = ( isset( $delimiter ) ? $delimiter : $this->delimiter );
		$date_pattern = ( isset( $date_pattern ) ? $date_pattern : $this->date_pattern );   // TODO not used yet

		$data = array_map( function ( $d ) use ( $delimiter ) {
			return str_getcsv( $d, $delimiter );
		}, file( $filepath )
		);
		array_walk( $data, function ( &$a ) use ( $data ) {
			$a = array_combine( $data[0], $a );
		}
		);

		return $data;
	}

	/**
	 * returns missing properties of dataset to import
	 *
	 * @since 1.0.0
	 *
	 * @param string $table_name table name to describe
	 *
	 * @return array
	 */
	public function get_property_difference( $data, $table_name ) {
		$columns = $this->db->get_columns_without_types( $table_name );

		return array_diff( $columns, array_keys( $data ) );
	}

	/**
	 * returns first 5 row, to check before import data
	 *
	 * @since 1.0.0
	 *
	 * @param array $data data set to import
	 * @param string $table_name table name to describe
	 *
	 * @return array
	 */
	public function get_preview( $data, $table_name ) {
		$test_set = array_values( array_slice( $data, 1, 5, true ) );
		return $this->db->test_data_insert( $table_name, $test_set );
	}

	/**
	 * imports data from file
	 *
	 * @since 1.0.0
	 *
	 * @param array $data data set to import
	 * @param string $table_name table name to describe
	 */
	public function import_data( $data, $table_name ) {
		$this->db->run_data_insert( $table_name, $data );
	}

	/**
	 * Check if file is valid
	 *
	 * @param object $file file to upload
	 * @param string $ext extension of file
	 *
	 * @return bool
	 * @throws Exception
	 */
	private function file_is_valid( $file, $ext ) {
		$filesize = filesize( $file['tmp_name'] );

		if ( ! in_array( $ext, $this->allowed_file_types ) ) {
			throw new Exception( 'The file has to be: ' . implode( ", ", $this->allowed_file_types ) );
		}
		if ( $filesize > $this->max_filesize ) {
			throw new Exception(
				'The file you attempted to upload is too large. Max file size is: '
				. round( $filesize / 1024 / 1024, 1 ) . 'MB'
			);
		}

		return true;
	}
}