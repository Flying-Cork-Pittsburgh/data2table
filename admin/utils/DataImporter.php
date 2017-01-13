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
	}

	public function upload_file( $file, $table_name ) {
		$filename_temp = $file['name'];
		$ext           = substr( $filename_temp, strpos( $filename_temp, '.' ), strlen( $filename_temp ) - 1 );

		if (!file_exists($this->upload_path)) {
			mkdir($this->upload_path, 0777, true);
		}

		if ( ! $this->file_is_valid( $file, $ext ) && ! is_writable( $this->upload_path ) ) {
			throw new Exception( 'You cannot upload to the specified directory, please contact Administrator.' );
		}
		if ( ! move_uploaded_file( $file['tmp_name'], $this->upload_path . $this->fixed_file_name . $ext  ) ) {
			throw new Exception( 'Failed to move File into directory: '
			                     . $this->upload_path . ' please contact Administrator.'
			);
		}
		// todo check if table exists
		$columns = $this->db->get_columns( $table_name );
		// todo check columns of file with columns of database by name
		$array  = array_map( 'str_getcsv', file( $this->upload_path . $this->fixed_file_name .$ext ) );
		$header = array_shift( $array );
		array_walk( $array, '_combine_array', $header );

		function _combine_array( &$row, $key, $header ) {
			$row = array_combine( $header, $row );
		}

		return true;
	}

	private function file_is_valid( $file, $ext ) {
		$filesize = filesize( $file['tmp_name'] );

		// file validation
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