<?php 
class Ni_Lead {
	function __construct() {
		
		add_action( 'init',  array( &$this, 'create_lead_master' ));
		add_action( 'init',  array( &$this, 'create_lead_taxonomies'), 0 ); 
		
		/*Meta Box For Lead Master*/
		add_action( 'admin_init', array( &$this, 'lead_meta_box') );
		
		/*Save Meta Box For Lead Master*/
		add_action( 'save_post', array( &$this, 'save_lead_meta_box'), 10, 2 );
		
		/*Get Lead List Columns*/
		//add_filter( 'manage_edit-lead_master_columns',  array( &$this,'get_lead_list_columns' ));
		add_filter( 'manage_edit-lead_master_columns',  array( &$this,'get_lead_list_columns' ));
		
		add_action( 'manage_posts_custom_column', array( &$this,'get_lead_list_columns_value' ), 10, 2 );
		
		/*Create Admin Menu*/
		add_action('admin_menu' , array( &$this,'crm_admin_menu'));
		
		add_action('admin_enqueue_scripts', array($this, 'wp_localize_script'));
		add_action('wp_ajax_crm_ajax', array( &$this, 'crm_ajax'));
		
		//add_action( 'admin_init', 'my_plugin_admin_init' );
		
		
		
		
	}
	
	function wp_localize_script($hook)
	{
		wp_register_style( 'ni-style', plugins_url('../css/ni-style.css', __FILE__) );
		wp_enqueue_style( 'ni-style' );
		
		wp_register_style( 'jquery-ui', plugins_url('../css/jquery-ui.css', __FILE__) );
		wp_enqueue_style( 'jquery-ui' );
		
		wp_enqueue_script( 'jquery-ui-js',  "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"); 
		 //wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js"), false);
		
		
		wp_register_script('ajax_script', plugins_url('../js/script.js', __FILE__));
		
		wp_enqueue_script( 'ni_lead_follow_up', plugins_url( '../js/ni-lead-follow-up.js', __FILE__ ));
		wp_enqueue_script('ajax_script');
		
		
		 
	
		wp_localize_script('ajax_script', 
			'ajax_object', 
			array( 
				'ajaxurl' 			=> admin_url( 'admin-ajax.php' )
				,'ajax_action' 		=> 'crm_ajax'
				,'current_date' 	=> date("Y-m-d")
			)
		); // setting ajaxurl
		//die;
	}
	/*Admin Menu*/
	function crm_admin_menu() { 
	
	add_submenu_page(
			'edit.php?post_type=lead_master',
			'Follow Up', /*page title*/
			'Follow Up', /*menu title*/
			'manage_options', /*roles and capabiliyt needed*/
			'follow_up',
			array( &$this,'add_menu_page') /*replace with your own function*/
		);
			
	}
	function crm_ajax()
	{   
		$sub_action = $_REQUEST["sub_action"];
		//print_r($_REQUEST);
		//die;
		//$page = $_REQUEST["page"];
		//print_r($_REQUEST);
		//die;
		switch ($sub_action) {
			case "save_follow_up":
				//echo "Your favorite color is red!";
				include_once("ni-lead-follow-up.php");
				$obj =  new Ni_Lead_Follow_Up();
				$obj->SaveFollowUp();
				break;
			case "delete_follow_up":
				include_once("ni-lead-follow-up.php");
				$obj =  new Ni_Lead_Follow_Up();
				$obj->DeleteFollowUp();
				break;
			case "get_follow_up":
				include_once("ni-lead-follow-up.php");
				$obj =  new Ni_Lead_Follow_Up();
				$obj->get_follow_up();
				break;
			case "paging_follow_up":
				include_once("ni-lead-follow-up.php");
				$obj =  new Ni_Lead_Follow_Up();
				$obj->get_follow_up();
				break;
			default:
				echo "echo some thing wrong";
		}
		die;
	}
	function add_menu_page()
	{
		 $page = $_REQUEST["page"];
		//die;
	
	//	echo $page;
		switch ($page) {
			case "follow_up":
				//echo "Your favorite color is red!";
				include_once("ni-lead-follow-up.php");
				$obj =  new Ni_Lead_Follow_Up();
				$obj->init();
				break;
			case "blue":
				echo "Your favorite color is blue!";
				break;
			case "green":
				echo "Your favorite color is green!";
				break;
			default:
				echo "echo some thing wrong";
		}
		//print_r($_REQUEST);	
	} 
	/*Lead Master*/
	function create_lead_master()
	{
		register_post_type( 'lead_master', /*Name of Custome Post Type */
			array(
				'labels' => array(
					'name' => 'Lead',
					'singular_name' => 'lead_master',
					'add_new' => 'Add New',
					'add_new_item' => 'Add New Lead',
					'edit' => 'Edit',
					'edit_item' => 'Edit Lead',
					'new_item' => 'New Lead',
					'view' => 'View',
					'view_item' => 'View Lead',
					'search_items' => 'Search Lead',
					'not_found' => 'No Lead found',
					'not_found_in_trash' => 'No Lead found in Trash',
					'parent' => 'Parent Lead'
				),
	 
				'public' => true,
				'menu_position' => 15,
				//'supports' => array( 'title', 'editor', 'comments', 'thumbnail', 'custom-fields' ),
				//'supports' => array( 'title', 'editor', 'thumbnail'),
				//'supports' => array( 'title'),
				'supports' => array('title'),
				'taxonomies' => array( '' ),
				'menu_icon' => plugins_url( '../images/lead.png', __FILE__ ),
				'has_archive' => true
			)
		);
	}
	/*create_lead_taxonomies*/
	function create_lead_taxonomies()
	{
		/*Service*/
		register_taxonomy(
			'service_master',
			'lead_master',
			array(
				'labels' => array(
					'name' => 'Service',
					'add_new_item' => 'Add New Service', /*Button Name*/
					'new_item_name' => "New Service"
				),
				'show_ui' => true,
				'show_tagcloud' => false,
				'hierarchical' => true
			)
		);
		/*Product*/
		register_taxonomy(
			'product_master',
			'lead_master',
			array(
				'labels' => array(
					'name' => 'Product',
					'add_new_item' => 'Add New Product', /*Button Name*/
					'new_item_name' => "New Product"
				),
				'show_ui' => true,
				'show_tagcloud' => false,
				'hierarchical' => true
			)
		);
		
		
		/*Status*/
		register_taxonomy(
			'status_master',
			'lead_master',
			array(
				'labels' => array(
					'name' => 'Status',
					'add_new_item' => 'Add New Status', /*Button Name*/
					'new_item_name' => "New Status",
					'type' => 'select',
					
					
				),
				'show_ui' => true,
				'show_tagcloud' => false,
				'hierarchical' => true,
				'type' => 'select',
				
			)
		);
	}
	/*lead_meta_box*/
	function lead_meta_box()
	{
		add_meta_box( 'movie_review_meta_box', 
				'Lead Information',
				array( &$this, 'display_lead_meta_box'), /*Name of Call Back or Display Meta Box Function*/
				'lead_master', /*Custom Post Type Name*/
				'normal', 
				'high'
			);	
	}
	/*display_lead_meta_box*/
	function display_lead_meta_box($lead)
	{
		$first_name = esc_html( get_post_meta( $lead->ID, '_first_name', true ) );
		$last_name = esc_html( get_post_meta( $lead->ID, '_last_name', true ) );
		$email_address = esc_html( get_post_meta( $lead->ID, '_email_address', true ) );
		$contact_no	 = esc_html( get_post_meta( $lead->ID, '_contact_no', true ) );
		$billing_address	 = esc_html( get_post_meta( $lead->ID, '_billing_address', true ) );
		$remarks	 = esc_html( get_post_meta( $lead->ID, '_remarks', true ) );
		?>
        <table>
        	<tr>
            	<td>First Name:</td>
               <td><input type="text" size="80" name="first_name" value="<?php echo $first_name; ?>" /></td>
            </tr>
            <tr>
            	<td>Last Name:</td>
               <td><input type="text" size="80" name="last_name" value="<?php echo $last_name; ?>" /></td>
            </tr>
            <tr>
            	<td>Email Address:</td>
               <td><input type="text" size="80" name="email_address" value="<?php echo $email_address; ?>" /></td>
            </tr>
            <tr>
            	<td>Contact No:</td>
               <td><input type="text" size="80" name="contact_no" value="<?php echo $contact_no; ?>" /></td>
            </tr>
            <tr>
            	<td>Billing Address:</td>
               <td><input type="text" size="80" name="billing_address" value="<?php echo $billing_address; ?>" /></td>
            </tr>
             <tr>
            	<td>Remarks:</td>
               <td><input type="textarea" size="80" name="remarks" value="<?php echo $remarks; ?>" /></td>
            </tr>
        </table>
        <?php
	}
	/*Save Lead Meta Box*/
	function save_lead_meta_box( $lead_id, $lead)
	{
		if ( $lead->post_type == 'lead_master' ) 
		{
			if ( isset( $_POST['first_name'] ) && $_POST['first_name'] != '' ) {
				update_post_meta( $lead_id, '_first_name', $_POST['first_name'] );
			}
			if ( isset( $_POST['last_name'] ) && $_POST['last_name'] != '' ) {
				update_post_meta( $lead_id, '_last_name', $_POST['last_name'] );
			}
			if ( isset( $_POST['email_address'] ) && $_POST['email_address'] != '' ) {
				update_post_meta( $lead_id, '_email_address', $_POST['email_address'] );
			}
			if ( isset( $_POST['contact_no'] ) && $_POST['contact_no'] != '' ) {
				update_post_meta( $lead_id, '_contact_no', $_POST['contact_no'] );
			}
			if ( isset( $_POST['billing_address'] ) && $_POST['billing_address'] != '' ) {
				update_post_meta( $lead_id, '_billing_address', $_POST['billing_address'] );
			}
			if ( isset( $_POST['remarks'] ) && $_POST['remarks'] != '' ) {
				update_post_meta( $lead_id, '_remarks', $_POST['remarks'] );
			}
		}
	}
	/*lead_list_columns*/
	function get_lead_list_columns($columns)
	{
		$columns['first_name'] = 'First Name';
		$columns['last_name'] = 'Last Name';
		$columns['email_address'] = 'Email Address';
		$columns['contact_no'] =  "Contact No";
		$columns['follow_up'] = 'Follow Up';
		$columns['product'] = 'Product';
		$columns['service'] = 'Service';
		//unset( $columns['title'] );
		return $columns;		
	}
	/*get_lead_list_columns_value*/
	function get_lead_list_columns_value($column,$post_id )
	{
		 if ( 'first_name' == $column ) {
			$first_name = esc_html( get_post_meta( get_the_ID(), '_first_name', true ) );
			echo $first_name;
		}
		if ( 'last_name' == $column ) {
			$last_name = esc_html( get_post_meta( get_the_ID(), '_last_name', true ) );
			echo $last_name;
		}
		if ( 'email_address' == $column ) {
			$email_address = esc_html( get_post_meta( get_the_ID(), '_email_address', true ) );
			echo $email_address;
		}
		if ( 'contact_no' == $column ) {
			$contact_no = esc_html( get_post_meta( get_the_ID(), '_contact_no', true ) );
			echo $contact_no;
		}
		if ( 'product' == $column ) {
			 $terms = get_the_term_list( $post_id , 'product_master' , '' , ',' , '' );
            		if ( is_string( $terms ) )
						echo $terms;
					else
						echo "-";
		}
		if ( 'service' == $column ) {
			 $terms = get_the_term_list( $post_id , 'service_master' , '' , ',' , '' );
				if ( is_string( $terms ) )
					echo $terms;
				else
					echo "-";
		}
		if ( 'follow_up' == $column ) {
			
			include_once("ni-activation.php");
			$obj  = new Ni_Activation();
			$obj->create_follow_up_table();
			
			global $wpdb;
			$table_name = $wpdb->prefix . "ni_crm_follow_up";
			
			$row = $wpdb->get_var('SELECT COUNT(lead_post_id) FROM '.$table_name.' WHERE lead_post_id ='.$post_id);
			//echo "<p>User count is {$row}</p>";
			//echo $row;
			//echo $wpdb->last_query;

			$url = admin_url().'edit.php?post_type=lead_master&page=follow_up&lead_post_id='.$post_id;
			?>
            <a href="<?php echo $url;?>"><?php echo "{$row}"; ?></a>
            <?php
		}
		

		
	}
	
}

?>