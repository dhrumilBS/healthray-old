<?php
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class cf7anyapi_List_Table extends WP_List_Table{

	public $logs_data;

    public function __construct(){
    	global $status, $page;
        parent::__construct(
        	array(
            	'singular'  => __( 'cf7anyapi_logs', 'contact-form-to-any-api' ),  
            	'plural'    => __( 'cf7anyapi_logs', 'contact-form-to-any-api' ), 
            	'ajax'      => false,
    		)
        );
    }

  	public function column_default($item, $column_name){
    	switch($column_name){ 
        	case 'form_id':
        		return '<a href="'.esc_url(site_url())."/wp-admin/admin.php?page=wpcf7&post=".esc_attr($item[ $column_name ])."&action=edit".'" target="_blank">'.esc_html(get_the_title($item[$column_name])).'</a>';
        	case 'post_id':
        		return '<a href="'.esc_url(get_edit_post_link($item[$column_name])).'" target="_blank">'.esc_html(get_the_title($item[$column_name])).'</a>';
        	case 'form_data':
        	case 'log':
            	return '<pre>'.esc_html($item[$column_name]).'</pre><span class="view_more">Expand JSON</span>';
            case 'created_date':
            	return esc_html($item[ $column_name ]);
        	default:
            	return print_r($item, true); 
    	}
  	}

	public function get_columns(){
        $columns = array(
        	'cb' => '<input type="checkbox" />',
            'form_id' => __( 'Form Name', 'contact-form-to-any-api' ),
            'post_id' => __( 'API Name', 'contact-form-to-any-api' ),
            'form_data' => __( 'Submitted Data', 'contact-form-to-any-api' ),
            'log' => __( 'API Response', 'contact-form-to-any-api' ),
            'created_date' => __( 'Created Date', 'contact-form-to-any-api' )
        );
        return $columns;
    }

    public static function default_logs_data($page_number = 1){
		global $wpdb;
		if(!empty($_REQUEST['paged'])){
			$page_number = absint(wp_unslash($_REQUEST['paged']));
		}

		$sql = "SELECT * FROM {$wpdb->prefix}cf7anyapi_logs WHERE 1=1";
		
		 if (!empty($_GET['cf7_form_filter'])) {
	        $form_id = absint($_GET['cf7_form_filter']);
	        $sql .= $wpdb->prepare(" AND form_id = %d", $form_id);
	    }
		//Alllow List for ordering
		$allowed_order = ['asc', 'desc'];
		$allowed_orderby = ['form_id', 'post_id', 'created_date'];

		if(!empty($_REQUEST['orderby'])){
			$orderby = sanitize_sql_orderby(wp_unslash($_REQUEST['orderby']));
			$orderby = in_array($orderby, $allowed_orderby, true) ? $orderby : 'created_date';
	        $order = !empty($_REQUEST['order']) ? sanitize_text_field(wp_unslash($_REQUEST['order'])) : 'asc';
	        $order = in_array($order, $allowed_order, true) ? $order : 'asc';
	        $sql .= " ORDER BY $orderby $order";
		}
		else{
			$sql .= ' ORDER BY created_date DESC';
		}

		// Limit and offset for pagination
	    $limit = 10;
	    $offset = ($page_number - 1) * $limit;

		$result = $wpdb->get_results($wpdb->prepare("$sql LIMIT %d OFFSET %d", $limit, $offset), 'ARRAY_A');
		return $result;
	}

	public static function get_logs_data(){
		global $wpdb;
		return $wpdb->get_results($wpdb->prepare("SELECT * FROM %i","{$wpdb->prefix}cf7anyapi_logs"),ARRAY_A);
    }

	public function prepare_items(){
		$this->logs_data = $this->get_logs_data();

  		$columns = $this->get_columns();
  		$hidden = array();
  		$sortable = $this->get_sortable_columns();
  		$this->_column_headers = array( $columns, $hidden, $sortable);

  		/* pagination */
        $per_page = 10;
        $current_page = $this->get_pagenum();
        $total_items = count($this->logs_data);

        $this->logs_data = array_slice($this->logs_data, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args(array(
              'total_items' => $total_items, // total number of items
              'per_page'    => $per_page // items to show on a page
        ));

  		$this->items = self::default_logs_data();
	}

	public function get_sortable_columns(){
		$sortable_columns = array(
			'form_id' => array( 'form_id', true ),
			'post_id' => array( 'post_id', true ),
			'created_date' => array( 'created_date', true ),
		);

		return $sortable_columns;
	}

	public function usort_reorder($a, $b){
		//Alllow List for ordering
		$allowed_order = ['asc', 'desc'];
		$allowed_orderby = ['form_id', 'post_id', 'created_date'];
		// If no sort, default to user_login
		$orderby = (!empty($_GET['orderby'])) ? sanitize_text_field(wp_unslash($_GET['orderby'])) : 'form_id';
		$orderby = in_array($orderby, $allowed_orderby, true) ? $orderby : 'form_id';
		// If no order, default to asc
		$order = (!empty($_GET['order'])) ? sanitize_text_field(wp_unslash($_GET['order'])) : 'asc';
		$order = in_array($order, $allowed_order, true) ? $order : 'asc';
		// Determine sort order
		$result = strcmp($a[$orderby], $b[$orderby]);
		// Send final sort direction to usort
		return ($order === 'asc') ? $result : -$result;
	}

	public function extra_tablenav( $which ) {
	    if ( $which === 'top' ) {
	        $selected_form = isset($_GET['cf7_form_filter']) ? absint($_GET['cf7_form_filter']) : '';
	        $cf7_forms = get_posts( array(
	            'post_type'      => 'wpcf7_contact_form',
	            'posts_per_page' => -1,
	        ) );
	        ?>
	        <div class="cf7log_filters alignleft actions">
		        <form id="cf7log-filter" method="get" action="<?php echo esc_url( admin_url( 'edit.php' ) ); ?>">
		        	<input type="hidden" name="post_type" value="cf7_to_any_api">
    				<input type="hidden" name="page" value="cf7anyapi_logs">
		            <select name="cf7_form_filter" id="cf7_form_filter">
		                <option value=""><?php esc_html_e( 'All Contact Forms', 'contact-form-to-any-api' ); ?></option>
		                <?php foreach ( $cf7_forms as $form ) : ?>
		                    <option value="<?php echo esc_attr( $form->ID ); ?>" <?php selected( $selected_form, $form->ID ); ?>>
		                        <?php echo esc_html( $form->post_title ); ?>
		                    </option>
		                <?php endforeach; ?>
		            </select>
		            <input type="submit" class="button" value="<?php esc_attr_e( 'Filter' ); ?>">
		            <div class="cf7anyapi_log_button">
			        	<button href="javascript:void(0);" class="cf7anyapi_bulk_log_delete button"><?php echo esc_html__( 'Delete Log', 'contact-form-to-any-api' );?></button>
			        </div>
			    </form>
	        </div>
	        <?php
	    }
	}
	public function column_cb($item){
	    return sprintf(
	        '<input type="checkbox" name="log_ids[]" value="%s" />',
	        esc_attr($item['id']) // assuming `id` is the primary key
	    );
	}


}