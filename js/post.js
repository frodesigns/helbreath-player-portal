$(document).ready(function() {
	getComments(0);
	
	//set up text length counter
    $('textarea').keyup(function() {
        update_chars_left(140, $('textarea')[0], $(this).siblings(".help-block"));
    });
	
    //and fire it on doc ready, too
    update_chars_left(140, $('textarea')[0], $(this).siblings(".help-block"));
	
	$("#addComment").click(function() {
		$("#commentForm").slideDown(200);
		$(this).hide();
	});
	
	$("#commentForm .cancel").click(function() {
		$("#commentForm").slideUp(200, function() {
			$("textarea").val("");
			update_chars_left(140, $('textarea')[0], $(this).siblings(".help-block"));
			$("#addComment").show();
		});		
	});
	
	$('#commentForm').validate();
	
	$('#commentForm').ajaxForm({
		beforeSubmit: function(formData, jqForm, options) {
			$("#commentForm .form-actions input").hide();
			$("#commentForm .form-actions .errors").html("");
			return $('#commentForm').validate().form();
		},
		success: function(responseText, statusText, xhr, $form) {
			//alert('status: ' + statusText + '\n\nresponseText: \n' + responseText + '\n\nThe output div should have already been updated with the responseText.');
			var obj = jQuery.parseJSON(responseText);
			
			if (obj.success) {				
				$("#comments").prepend(obj.message);	
				$("textarea").val("");
				$("#commentForm").hide();
				$("#addComment").show();		
				$(".commentitem:first").slideDown(200);
			} else {
				$("#commentForm .form-actions .errors").html(obj.message);
			}
			
			$("#commentForm .form-actions input").show();
		}
	});	
	
	$('.comments').change(function() {
		getComments(0);
	});
	
});

function getComments(count) {
	if ($("#comments").length) {
		$("#comments").html('<div class="ajaxLoader"><img src="/portal/img/ajax-loader.gif" alt="Loading..." /></div>');	
		
		var sort = $("#sort").val();
		var postid = $("#postid").val();

		var query = 'postid=' + postid + '&sort=' + sort;
		
		if (sort != "") {
			$.ajax({
				type: "POST",
				url: '/portal/ajax/getComments.php',
				cache: false,
				data: query,
				success: function(data) {
					$("#comments").html(data);
				}
			});
		} else {
			$("#comments").html("No comments found.");
		}
	}
}

function update_chars_left(max_len, target_input, display_element) {
   var text_len = target_input.value.length;
   if (text_len >= max_len) {
       target_input.value = target_input.value.substring(0, max_len); // truncate
       display_element.html("0 characters remaining.");
   } else {
       display_element.html(max_len - text_len + " characters remaining.");
   }
}