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
	private $max_file_size;
	private $upload_path;
	private $fixed_file_name;


	public function __construct($db) {
		$this->db = $db;
		$this->allowed_file_types = array(
			'.csv',
			'.txt'
		);    // These will be the types of file that will pass the validation.
		$this->max_file_size = 524288;                     // Maximum file size in BYTES (currently 0.5MB).
		$wp_upload_dir       = wp_upload_dir();
		$this->upload_path   = $wp_upload_dir['basedir'] . '/files/'; // or plugin_dir_path( __FILE__ ) . 'files/';
		$this->fixed_file_name = 'data';
	}

	public function validate_data( $file, $table_name ) {
		if($this->file_is_valid($file)){
			$columns = $this->db->get_columns($table_name);
			// todo check columns of file with columns of database by name
		}
		throw new Exception('File is not valid.');
	}

	private function file_is_valid($file){
		$filename_temp = $file['name'];
		$ext           = substr( $filename_temp, strpos( $filename_temp, '.' ), strlen( $filename_temp ) - 1 );
		$file_size     = filesize($file['tmp_name']);

		// file validation
		if ( ! in_array( $ext, $this->allowed_file_types ) ) {
			throw new Exception( 'The file has to be: ' . implode( ", ", $this->allowed_file_types ) );
		}
		if ( $file_size > $this->max_file_size ) {
			throw new Exception(
				'The file you attempted to upload is too large. Max file size is: '
				. round( $file_size / 1024 / 1024, 1 ) . 'MB'
			);
		}
		if ( is_writable( $this->upload_path ) ) {
			$file_destination = $this->get_file_destination($ext);
			if ( move_uploaded_file( $file['tmp_name'], $file_destination ) ) {
				chmod( $file_destination, 0777 );
				return true;
			}
			throw new Exception('Failed to move File into directory: '
			                    . $this->upload_path .' please contact Administrator.');
		}
		throw new Exception('You cannot upload to the specified directory, please contact Administrator.');
	}

	private function get_file_destination($ext){
		return $this->upload_path . $this->fixed_file_name . $ext;
	}

}