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
		global $status, $page;
		$this->db             = $db;
		$this->table_name     = $table_name;
		$this->properties     = $this->db->get_columns( $this->table_name );
		$this->property_names = array();

		for ( $i = 0; $i < sizeof( $this->properties); $i ++ ) {
			$this->property_names[$i] = $this->properties[$i]['field'];
		}

		parent::__construct( array(
				'singular' => 'item',
				'plural'   => 'items',
				'ajax'     => true        //does this table support ajax?
			)
		);
	}

	/**
	 * [REQUIRED] this is a default column renderer
	 *
	 * @param $item - row (key, value array)
	 * @param $column_name - string (key)
	 *
	 * @return HTML
	 */
	function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	function column_id( $item ) {

		//Build row actions
		$actions = array(
			'edit'   => sprintf( '<a href="?table=%s&page=%s&action=%s&%s=%s">Edit</a>',
				$this->table_name,
				$_REQUEST['page'],
				$this->_args['singular'],
				'edit', $item['id']
			),
			'delete' => sprintf( '<a href="?table=%s&page=%s&action=%s&%s=%s">Delete</a>',
				$this->table_name,
				$_REQUEST['page'],
				$this->_args['singular'],
				'delete', $item['id']
			),
		);

		//Return the title contents
		return sprintf( '%1$s %2$s',
			$item['id'],
			$this->row_actions( $actions )
		);
	}

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/
			$this->_args['singular'],
			/*$2%s*/
			$item['id']
		);
	}

	/**
	 * [REQUIRED] This method return columns to display in table
	 * you can skip columns that you do not want to show
	 * like content, or description
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />'
		);
		foreach ( $this->properties as $property ) {
			$columns[ $property['field'] ] = $property['field'] . '</br> ' . $property['type'];
		}

		return $columns;
	}

	/**
	 * [OPTIONAL] This method return columns that may be used to sort table
	 * all strings in array - is column names
	 * notice that true on name column means that its default sort
	 *
	 * @return array
	 */
	function get_sortable_columns() {
		$sortable_columns = array();
		foreach ( $this->properties as $property ) {
			$sortable_columns[ $property['field'] ] = array( $property['field'], true );
		}

		return $sortable_columns;
	}

	/**
	 * [OPTIONAL] Return array of bulk actions if has any
	 *
	 * @return array
	 */
	function get_bulk_actions() {
		$actions = array(
			'delete' => 'Delete'
		);

		return $actions;
	}

	/**
	 * [OPTIONAL] This method processes bulk actions
	 * it can be outside of class
	 * it can not use wp_redirect coz there is output already
	 * in this example we are processing delete action
	 * message about successful deletion will be shown on page in next part
	 */
	function process_bulk_action() {
		global $wpdb;
		if ( 'delete' === $this->current_action() ) {
			$ids = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : array();
			if ( is_array( $ids ) ) {
				$ids = implode( ',', $ids );
			}

			if ( ! empty( $ids ) ) {
				$wpdb->query( 'DELETE FROM ' . $this->table_name . ' WHERE id IN(' . $ids . ')' );
			}
		}
	}

	/**
	 * [REQUIRED] This is the most important method
	 *
	 * It will get rows from database and prepare them to be showed in table
	 */
	function prepare_items() {
		global $wpdb;

		$per_page = 20; // constant, how much records will be shown per page

		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		// here we configure table headers, defined in our methods
		$this->_column_headers = array( $columns, $hidden, $sortable );

		// [OPTIONAL] process bulk action if any
		$this->process_bulk_action();

		// will be used in pagination settings
		$total_items = $wpdb->get_var( 'SELECT COUNT(id) FROM ' . $this->table_name );

		// prepare query params, as usual current page, order by and order direction
		$paged   = isset( $_REQUEST['paged'] ) ? max( 0, intval( $_REQUEST['paged'] - 1 ) * $per_page ) : 0;
		$orderby = ( isset( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'], array_keys( $this->get_sortable_columns() ) ) ) ? $_REQUEST['orderby'] : 'id ';
		$order   = ( isset( $_REQUEST['order'] ) && in_array( $_REQUEST['order'], array(
					'asc',
					'desc'
				)
			) ) ? $_REQUEST['order'] : 'asc';

		// [REQUIRED] define $items array
		// notice that last argument is ARRAY_A, so we will retrieve array
		$this->items = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $this->table_name . ' ORDER BY '
		. $orderby . $order . ' LIMIT %d OFFSET %d', $per_page, $paged ), ARRAY_A );


		/* If the value is not NULL, do a search for it. */
		if ( isset( $_REQUEST['s'] ) && $_REQUEST['s'] != '' ) {
			$search = $_REQUEST['s'];

			$query  = 'SELECT * FROM ' . $this->table_name . ' WHERE ';
			$length = sizeof( $this->property_names );

			for ( $i = 0; $i < $length; $i ++ ) {
				$query .= "`" . $this->property_names . "` LIKE '%%%s%%'";
				$search[$i] = trim( $search );
				if ( $i != $length - 1 ) {
					$query .= " OR ";
				}
			}

			$query .= " ORDER BY " . $orderby . $order . " LIMIT %d OFFSET %d";
			$this->items = $wpdb->get_results( $wpdb->prepare( $query, $search, $per_page, $paged ), ARRAY_A );
			$total_items = count( $this->items );
		}
		// [REQUIRED] configure pagination
		$this->set_pagination_args( array(
				'total_items' => $total_items, // total items defined above
				'per_page'    => $per_page, // per page constant defined at top of method
				'total_pages' => ceil( $total_items / $per_page ) // calculate pages count

			)
		);
	}
}

