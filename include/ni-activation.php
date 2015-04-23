<?php 
class Ni_Activation {
	function __construct() {
		//$this->create_follow_up_table();
		}
	
	function create_follow_up_table(){
		global $wpdb;
		$table_name = $wpdb->prefix . "ni_crm_follow_up";
		$charset_collate = $wpdb->get_charset_collate();
		
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			$sql = "CREATE TABLE $table_name (
				id int(11) NOT NULL AUTO_INCREMENT,
				created_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				follow_up_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				follow_up_note text DEFAULT '' NOT NULL,
				follo_up_id int(11) NOT NULL,
				lead_post_id int(11) NOT NULL,
				created_user_id int(11) NOT NULL,
				UNIQUE KEY id (id)
				) $charset_collate;";
			//url varchar(55) DEFAULT '' NOT NULL,
			//reference to upgrade.php file
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}		
	}
}
?>