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

	public function prepare_preview($items) {
		$this->items = $items;

		$columns               = $this->get_columns();
		$this->_column_headers = array( $columns, array(), array() );

		$per_page    = 5;
		$total_items = 5;

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
	}

}

