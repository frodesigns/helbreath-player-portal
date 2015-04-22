$(document).ready(function() {
	getGuildInfo();
	
	//set up text length counter
	$('textarea').keyup(function(e) {
        update_chars_left(140, $('textarea')[0], $(this).siblings(".help-block"));
    });
	
    //and fire it on doc ready, too
    update_chars_left(140, $('textarea')[0], $(this).siblings(".help-block"));
	
	// $("body").on("click", "#tag-user", function() {
		// $("#tag-input").show();
		// $("#add-tag").show();
		// $(this).hide();

		// return false;
	// });
	
	// $("body").on("click", "#add-tag", function() {
		// var user = $("#tag-input").val();
		
		// $("#tag-user").show();
		// $("#tag-input").val("").hide();
		// $(this).hide();
		
		// var text = $("textarea").val();
		// $("textarea").val(text + " @" + user + " ");

		// return false;
	// });
	
	$('.browse').click(function() {
		$('#file').click();
	});
	
	$('input[type=file]').bind('change', function() {
		var str = $(this).val();
		str = str.replace("C:\\fakepath\\", '');
		$("input.browse").val(str);
	}).change();
	
	$("#createPost").click(function() {
		var content = $(this).closest(".widget").find(".widget-inner");
		if (!content.is(":visible")) {
			content.slideDown(200);
			$(this).siblings(".collapse").find("i").removeClass("icon-chevron-down").addClass("icon-chevron-up");
		}
		$("#postForm").slideDown(200);
		$(this).hide();
	});
	
	$("#postForm .cancel").click(function() {
		$("#postForm").slideUp(200, function() {
			$("textarea").val("");
			$("#file").replaceWith("<input id='file' name='file' class='input-xlarge' type='file' />");
			$("input.browse").val("");
			update_chars_left(140, $('textarea')[0], $(this).siblings(".help-block"));
			$("#createPost").show();
		});		
	});
	
	$('#postForm').validate();
	
	$('#postForm').ajaxForm({
		beforeSubmit: function(formData, jqForm, options) {
			$("#postForm .form-actions input").hide();
			$("#postForm .form-actions .errors").html("");
			return $('#postForm').validate().form();
		},
		success: function(responseText, statusText, xhr, $form) {
			//alert('status: ' + statusText + '\n\nresponseText: \n' + responseText + '\n\nThe output div should have already been updated with the responseText.');
			var obj = jQuery.parseJSON(responseText);
			
			if (obj.success) {				
				$("#posts").prepend(obj.message);	
				$("textarea").val("");
				$("#file").replaceWith("<input id='file' name='file' class='input-xlarge' type='file' />");
				$("input.browse").val("");
				$("#postForm").hide();
				$("#createPost").show();		
				$(".postitem:first").slideDown(200);
			} else {
				$("#postForm .form-actions .errors").html(obj.message);
			}
			
			$("#postForm .form-actions input").show();
		}
	});	
	
	$('.guildList').change(function() {
		getGuildInfo();
	});
});

function getGuildInfo() {
	if ($("#guildInfo").length) {
		$("#guildInfo").html('<div class="ajaxLoader"><img src="/portal/img/ajax-loader.gif" alt="Loading..." /></div>');	
		
		var guildid = $(".guildList").val();
		var query = 'guildid='+ guildid;
		
		if (guildid != "") {
			$.ajax({
				type: "POST",
				url: '/portal/ajax/getGuildInfo.php',
				cache: false,
				data: query,
				success: function(data) {
					$("#guildInfo").html(data);
				}
			});
		} else {
			$("#guildInfo").html("No guilds found.");
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