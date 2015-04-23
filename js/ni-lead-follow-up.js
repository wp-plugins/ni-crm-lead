// JavaScript Document
//alert("a");
jQuery(function($){
	
	$(".MsgBox").hide();
	 //$( ".datepicker" ).datepicker();
	 $(".datepicker").datepicker({ dateFormat: 'yy-mm-dd' });
	  
	 $("#frmFollowUp").submit(function(){
		//alert(ajax_object.ajaxurl);
		//alert(JSON.stringify($("#frmFollowUp").serialize()));
		$.ajax({
			url:ajax_object.ajaxurl,
			data:$("#frmFollowUp").serialize(),
			success:function(data){
				$("._followup_list").html(data);
				//alert(JSON.stringify(data));
				$('.MsgBox').delay(3000).fadeOut('slow');
				//alert(data);
			}		
		});
		
		return false;
	});
	/*Delete function*/
	$(document).on('click', ".tablenav-pages a", function() {
		//alert($(this).attr("data-id"));
		var id =$(this).attr("data-id");
		var data = {};
		
		var lead_post_id = $("#lead_post_id").val();
		
		var pagenum = $(this).text();
		
		data['action'] = "crm_ajax";
		data['sub_action'] = "paging_follow_up";
		data['id'] = id;
		data['lead_post_id'] = lead_post_id;
		data['pagenum'] = pagenum;
		
		
		
		jQuery.ajax({
			type: "POST",
			url:ajax_object.ajaxurl,
			data:  data,
		//	dataType: "json",
			success:function(data) {
				$("._followup_list").html(data);
				//alert(JSON.stringify(data));
				//alert(data);
				
				//alert(pagenum);
			},
			error: function(jqxhr, textStatus, error ){
				alert("error");
				alert(JSON.stringify(jqxhr));
			}
		});
		return false;
	});
	
	
	$(document).on('click', "._delete_follow_up", function() {
		
		//alert($(this).attr("data-id"));
		var id =$(this).attr("data-id");
		var data = {};
		
		var lead_post_id = $("#lead_post_id").val();
		
		data['action'] = "crm_ajax";
		data['sub_action'] = "delete_follow_up";
		data['id'] = id;
		data['lead_post_id'] = lead_post_id;
		
		
	//	alert(lead_post_id)
		jQuery.ajax({
			type: "POST",
			url:ajax_object.ajaxurl,
			data:  data,
		//	dataType: "json",
			success:function(data) {
				$("._followup_list").html(data);
				//alert(JSON.stringify(data));
				//alert(data);
				$('.MsgBox').delay(3000).fadeOut('slow');
			},
			error: function(jqxhr, textStatus, error ){
				alert("error");
				alert(JSON.stringify(jqxhr));
			}
		});
		return false;
	});
	/*Load Follow up List*/
	//get_follow_up();
	
});

