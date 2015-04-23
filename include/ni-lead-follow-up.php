<?php 
class Ni_Lead_Follow_Up 
{
	function __construct() 
	{
		
	}
	function init()
	{
		$this->create_form();
	}
	
	function create_form()
	{
		$lead_post_id= isset($_REQUEST["lead_post_id"])?$_REQUEST["lead_post_id"]:0;
		
		$display_form = '';
		if (!isset($_REQUEST["lead_post_id"])) $display_form =  ' style="display:none"';
	?>
    <div class="ni_form_wrap">
    <form id="frmFollowUp"  class="ni_followup_form" method="post"<?php echo $display_form ;?>>
    	<div class="form_title">Follow up </div>
        <table>
        	<tr>
            	<td>Follow Date</td>
                <td><input type="text" class="datepicker" value="<?php echo date("Y-m-d"); ?>" id="txtFollowDate" name="txtFollowDate"> </td>
            </tr>
            <tr>
            	<td>Follow Up</td>
                <td>
                <select id="ddlFollowUpID" name="ddlFollowUpID">
                  <option value="1">Meeting</option>
                  <option value="2">Call</option>
                  <option value="3">Mail</option>
                  <option value="4">Fax</option>
                </select>
                </td>
            </tr>
             <tr>
            	<td>Follow Up Note</td>
                <td><textarea id="txtFollowUpNote" name="txtFollowUpNote" rows="10" cols="50"></textarea></td>
            </tr>
            <tr>
            	<td><input type="submit" /></td>
            </tr>
        </table>
          <div class="ajax_content"></div>
          <input type="hidden" name="action" value="crm_ajax" />
          <input type="hidden" name="sub_action" value="save_follow_up" />
          <input type="hidden" name="page" value="<?php echo $_REQUEST["page"]; ?>" />
          <input type="hidden" id="lead_post_id" name="lead_post_id" value="<?php echo $lead_post_id; ?>" />
    </form>
    
    <div div class="_followup_list"><?php  $this->get_follow_up($lead_post_id)?></div>
    </div>
    <?php
	
	//	echo '<div class="followup_list">'. $this->get_follow_up($lead_post_id).'</div>';
	}
	
	function SaveFollowUp()
	{
		
		$current_user = wp_get_current_user();
		include_once("ni-activation.php");
		$obj  = new Ni_Activation();
		$obj->create_follow_up_table();
		
		 global $wpdb;
		 $table_name = $wpdb->prefix . "ni_crm_follow_up";
		 $result = 	  $wpdb->insert( 
					"$table_name", 
					array( 
						'created_date' => date("Y-m-d"), 
						'follow_up_date' => $_REQUEST["txtFollowDate"] ,
						'follow_up_note' => $_REQUEST["txtFollowUpNote"],
						'follo_up_id' => $_REQUEST["ddlFollowUpID"],
						'lead_post_id' =>  $_REQUEST["lead_post_id"],
						'created_user_id' => $current_user->ID
					)
				);
		mysql_error();		
		if ($result==1){
			$message["message"] ="Save";
			?>
            <div class="MsgBox alert-box success">Follow Up Save</div>
            <?php
            		
			$this->get_follow_up($_REQUEST["lead_post_id"]);
		}
		else{
			$message["message"] ="Fail";
		}
		//echo json_encode($message["message"]);
		die;
	}
	
	function get_follow_up($lead_post_id=NULL)
	{
		
		$pagenum = isset($_REQUEST['pagenum'] ) ? absint( $_REQUEST['pagenum'] ) : 1;
		$lead_post_id = isset( $_REQUEST['lead_post_id'] ) ? absint( $_REQUEST['lead_post_id'] ) : '';
		$limit = 10;
		$offset = ( $pagenum - 1 ) * $limit;
		
		
		
		global $wpdb;
		$table_name = $wpdb->prefix . "ni_crm_follow_up";
		$sql  = "SELECT * FROM ".$table_name ." WHERE 1=1 " ;
		
		if ($lead_post_id)
		$sql  .= " AND lead_post_id=".$lead_post_id;
		
		$sql  .= " order by id desc";
		$sql  .= " LIMIT $offset, $limit";
		
		$rows = $wpdb->get_results($sql);
		?>
      		 <table class="ni_follow_table">
                <thead>
                <tr>
                    <th>Created Date</th>
                    <th>Created By</th>
                    <th>Follow Up Date</th>
                    <th>Follow Up</th>
                    <th>Notes</th>
                    <th>Delete</th>
                </tr>
                </thead>
                <tbody>
		<?php
		$follow = array("1"=>"Meeting", "2"=>"Call", "3"=>"Mail", "4"=>"Fax", "5"=>"other");		
		foreach($rows as $key => $value){
 		?>
        <tr>
            <td><?php echo  date("Y-m-d", strtotime($value->created_date) );?></td>
            <td><?php  echo $this->get_display_name(  $value->created_user_id) ; ?></td>
            <td><?php  echo  date("Y-m-d", strtotime($value->follow_up_date) ); ?></td>
            <td><?php echo $follow[$value->follo_up_id] ?></td>
            <td><?php echo $value->follow_up_note; ?></td>
               <td><a data-id="<?php echo $value->id; ?>" class="_delete_follow_up" href="#" data-lead_post_id = "40">Delete</a></td>
        </tr>
        <?php }?>
        		</tbody>
                
			</table>
        	<?php 
            $sql  = "SELECT COUNT('id') FROM ".$table_name ." WHERE 1=1 " ;
                if ($lead_post_id)
                $sql  .= " AND lead_post_id=".$lead_post_id;
                
                    $total = $wpdb->get_var(	$sql);
                    $num_of_pages = ceil( $total / $limit );
                    $page_links = paginate_links( array(
                        'base' => add_query_arg( 'pagenum', '%#%' ),
                        'format' => '',
                        'prev_text' => __( '&laquo;', 'aag' ),
                        'next_text' => __( '&raquo;', 'aag' ),
                        'total' => $num_of_pages,
                        'current' => $pagenum
                    ) );
            
            if ( $page_links ) {
            echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
            }
            ?>
        	
        <?php

	}
	function DeleteFollowUp()
	{
		//print_r($_REQUEST);
			//die;
		$id =	$_REQUEST['id'];
		//die;
		global $wpdb;
		$table_name = $wpdb->prefix . "ni_crm_follow_up";
		$result = $wpdb->query(" DELETE FROM ".$table_name." WHERE id = " .$id);
		//echo $wpdb->last_query;
		//echo $result;
		if ($result==1){
			$message["message"] ="Delete";
			 	//echo "Delete";
			//echo '<div class="display_message"></div>';
			?>
            <div class="MsgBox alert-box success">Follow Up Delete</div>
            <?php	
				
			$this->get_follow_up($_REQUEST["lead_post_id"]);
		}
		else{
			$message["message"] ="Fail";
		}
		//echo json_encode($message["message"]);
		
		die;
	}
	function get_display_name($user_id) {
		if (!$user = get_userdata($user_id))
			return false;
		return $user->data->display_name;
	}
	
		
}
?>