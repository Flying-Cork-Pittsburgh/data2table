<?php

/**
 * Custom_Table_Example_List_Table class that will display our custom table
 * records in nice table
 */
class D2T_DataTable extends List_Table {

	/**
	 * The DB Handler is responsible for handling database request and validation.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      D2T_DbHandler $db handles all database requests and validation
	 */
	private $db;

	/**
	 * name of database table for displaying data
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $table_name
	 */
	private $table_name;

	/**
	 * properties of database table for displaying data
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $properties
	 */
	private $properties;

	/**
	 * property names of database table for displaying data
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $propertiy_names
	 */
	private $property_names;

	function __construct( $table_name, $db ) {
		$this->db             = $db;
		$this->table_name     = $table_name;
		$this->properties     = $this->db->get_columns( $this->table_name );
		$this->property_names = array();

		for ( $i = 0; $i < sizeof( $this->properties ); $i ++ ) {
			$this->property_names[ $i ] = $this->properties[ $i ]['field'];
		}

		parent::__construct( array(
				'singular' => 'item',
				'plural'   => 'items',
				'ajax'     => true
			)
		);
	}

	function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	function column_actions( $item ) {

		$actions = array(
			'edit'   => sprintf( '<a href="#top" class="%s" data-id="%s">Edit</a>',
				'edit-item btn btn-outline-info btn-sm', $item['id']
			),
			'delete' => sprintf( '<a href="#top" class="%s" data-id="%s">Delete</a>',
				'delete-item btn btn-outline-danger btn-sm', $item['id']
			),
		);

		return $this->row_actions( $actions );
	}

	function get_columns() {
		$columns = array(
			'actions' => 'actions'
		);
		foreach ( $this->properties as $property ) {
			$columns[ $property['field'] ] = $property['field'] . '</br> ' . $property['type'];
		}

		return $columns;
	}

	function get_sortable_columns() {
		$sortable_columns = array();
		foreach ( $this->properties as $property ) {
			$sortable_columns[ $property['field'] ] = array( $property['field'], true );
		}

		return $sortable_columns;
	}

	protected function display_tablenav( $which ) {
		?>
		<div class="tablenav <?php echo esc_attr( $which ); ?>">
			<?php
			$this->extra_tablenav( $which );
			$this->pagination( $which );
			?>

			<br class="clear"/>
		</div>
		<?php
	}

	public function prepare_items() {
		global $wpdb;

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$per_page    = 20;
		$total_items = $wpdb->get_var( 'SELECT COUNT(id) FROM ' . $this->table_name );
		$paged       = isset( $_REQUEST['paged'] ) ? max( 0, intval( $_REQUEST['paged'] - 1 ) * $per_page ) : 0;

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
				'orderby'     => ( isset( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'],
						array_keys( $this->get_sortable_columns() )
					) ) ? $_REQUEST['orderby'] : 'id ',
				'order'       => ( isset( $_REQUEST['order'] ) && in_array( $_REQUEST['order'],
						array( 'asc', 'desc' )
					) ) ? $_REQUEST['order'] : 'asc'
			)
		);

		$this->items = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT * FROM '
				. $this->table_name . ' ORDER BY '
				. $this->get_pagination_arg( 'orderby' ) . ' '
				. $this->get_pagination_arg( 'order' )
				. ' LIMIT %d OFFSET %d', $per_page, $paged
			), ARRAY_A
		);
	}

	public function display() {
		wp_nonce_field( 'ajax-custom-list-nonce', '_ajax_custom_list_nonce' );
		echo '<input type="hidden" id="order" name="order" value="' . $this->_pagination_args['order'] . '" />';
		echo '<input type="hidden" id="orderby" name="orderby" value="' . $this->_pagination_args['orderby'] . '" />';

		$table_name = $_REQUEST['table'];
		$table      = $this->get_data_table( $table_name );
		?>
		<div class="wrap">
			<h3><a name="top"><?php echo $table_name ?></a></h3>
			<?php include_once 'partials/alerts_inc.php' ?>

			<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
			<form id="table-items" method="get">
				<!-- For plugins, we also need to ensure that the form posts back to our current page -->
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
				<!-- Now we can render the completed list table -->
				<?php parent::display(); ?>
			</form>
		</div>
		<?php

	}

	public function ajax_response() {
		$this->prepare_items();
		extract( $this->_args );
		extract( $this->_pagination_args, EXTR_SKIP );
		ob_start();
		if ( ! empty( $_REQUEST['no_placeholder'] ) ) {
			$this->display_rows();
		} else {
			$this->display_rows_or_placeholder();
		}
		$rows = ob_get_clean();
		ob_start();
		$this->print_column_headers();
		$headers = ob_get_clean();
		ob_start();
		$this->pagination( 'top' );
		$pagination_top = ob_get_clean();
		ob_start();
		$this->pagination( 'bottom' );
		$pagination_bottom                = ob_get_clean();
		$response                         = array( 'rows' => $rows );
		$response['pagination']['top']    = $pagination_top;
		$response['pagination']['bottom'] = $pagination_bottom;
		$response['column_headers']       = $headers;
		if ( isset( $total_items ) ) {
			$response['total_items_i18n'] = sprintf( _n( '1 item', '%s items', $total_items ),
				number_format_i18n( $total_items )
			);
		}
		if ( isset( $total_pages ) ) {
			$response['total_pages']      = $total_pages;
			$response['total_pages_i18n'] = number_format_i18n( $total_pages );
		}
		die( json_encode( $response ) );
	}

	/**
	 * Callback function for 'wp_ajax__ajax_fetch_custom_list' action hook.
	 *
	 * Loads the Custom List Table Class and calls ajax_response method
	 */
	public function _ajax_fetch_custom_list_callback() {
		$this->ajax_response();
	}

	public function ajax_user_can() {
		return current_user_can( 'manage_options' );
	}
}

