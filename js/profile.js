$(document).ready(function() {
	$("#preferencesForm").validate();
	
	$("#emailChangeForm").validate({
		rules: {
			newEmail: {
				required: true
			},
			confirmNewEmail: {
				required: true,
				equalTo: "#newEmail"
			}
		},
		messages: {
			confirmNewEmail: {
				equalTo: "Emails do not match."
			}
		}
	});
	
	$("#acctForm").validate({
		submitHandler: function(form) {
		    //form.submit();
		   	//e.preventDefault();
		
			$("#acctStatus").html("");
			
			var username = $("#acctUsername").val();
			var password = $("#acctPassword").val();
			var lastname = $("#acctLastname").val();
			var displayname = $("#acctDisplayname").val();
			
			var query = 'username='+ username + '&password=' + password + '&displayname=' + displayname;
			
			$.ajax({
				type: "POST",
				url: "/portal/ajax/addGameAccount.php",
				cache: false,
				data: query,
				success: function(data) {
					var obj = jQuery.parseJSON(data);
					if(obj.success) {
						$("#acctStatus").html(obj.message);
						var accountid = obj.accountid;
						$("#accountsTable tbody").append('<tr id="' + accountid + '"><td>' + username + '</td><td><span class="display"><button class="btnfloat editGameAccount btn btn-mini btn-warning"><i class="icon-pencil icon-white"></i> Rename</button> <span class="alias">' + displayname + '</span></span><span class="edit" style="display: none;"><input type="text" value="' + displayname + '" class="input-medium" /> <button class="saveEditGameAccount btn btn-success">Save</button> <button class="cancelEditGameAccount btn">Cancel</button></span></td><td><button class="removeGameAccount btn btn-mini btn-danger"><i class="icon-remove icon-white"></i> Remove</button></td></tr>');
						clearAcctForm();
						setTimeout(function() { $('#accountModal').modal('hide'); }, 1000);
					} else { 
						$("#acctStatus").html(obj.message);
					}
				}
			});
		}
	});
	
	$("body").on("click", ".removeitem", function(e){
		if (confirm("Are you sure you want to remove this item from your Trade List?")) {
			var therow = $(this).closest("tr");
			var sitemid = therow.attr("data-sitemid");
			var sid1 = therow.attr("data-sid1");
			var sid2 = therow.attr("data-sid2");
			var sid3 = therow.attr("data-sid3");
			
			var query = "sitemid=" + sitemid + "&sid1=" + sid1 + "&sid2=" + sid2 + "&sid3=" + sid3;
		
			$.ajax({
				type: "POST",
				url: "/portal/ajax/deleteTradeItem.php",
				cache: false,
				data: query,
				success: function(data) {
					var obj = jQuery.parseJSON(data);
					if (obj.success) {
						therow.hide();
						//alert(obj.message);
					} else { 
						alert(obj.message);
					}
				}
			});
		}
		
		return false;
	});
	
	$("body").on("click", ".removeGameAccount", function(e){
		if (confirm("Are you sure you want to remove this game account?")) {
			var accountID = $(this).closest("tr").attr("id");
			var tableRow = $(this).closest("tr");
			
			var query = 'accountid='+ accountID;
			
			$.ajax({
				type: "POST",
				url: "/portal/ajax/removeGameAccount.php",
				cache: false,
				data: query,
				success: function(data) {
					var obj = jQuery.parseJSON(data);
					if(obj.success) {
						tableRow.remove();
					} else { 
						alert(obj.message);
					}
				}
			});
		}
	});
	
	$("body").on("click", ".editGameAccount", function(e){
		$(this).closest("td").find(".display").hide();
		$(this).closest("td").find(".edit").show();
	});
	
	$("body").on("click", ".cancelEditGameAccount", function(e){
		$(this).closest("td").find(".display").show();
		$(this).closest("td").find(".edit").hide();
	});
	
	$("body").on("click", ".saveEditGameAccount", function(e){
		var accountID = $(this).closest("tr").attr("id");
		var tableRow = $(this).closest("tr");
		var newname = $(this).closest("td").find("span.edit input").val();
			
		var query = 'accountid='+ accountID + '&newname=' + newname;
			
		$.ajax({
			type: "POST",
			url: "/portal/ajax/editGameAccount.php",
			cache: false,
			data: query,
			success: function(data) {
				var obj = jQuery.parseJSON(data);
				if(obj.success) {
					tableRow.find("span.alias").text(newname);
					tableRow.find("span.display").show();
					tableRow.find("span.edit").hide();
				} else { 
					alert(obj.message);
				}
			}
		});
	});
	
	$("#passwordChangeForm").validate({
		rules: {
			newPassword: {
				required: true,
				minlength: 6
			},
			confirmNewPassword: {
				required: true,
				minlength: 6,
				equalTo: "#newPassword"
			}
		},
		messages: {
			newPassword: {
				required: "Please provide a password.",
				minlength: "Your password must be at least 6 characters long."
			},
			confirmNewPassword: {
				required: "Please provide a password.",
				minlength: "Your password must be at least 6 characters long.",
				equalTo: "Passwords do not match."
			}
		}
	});

});

function clearAcctForm() {		
	$("#acctUsername").val('');
	$("#acctPassword").val('');
	$("#acctLastname").val('');
	$("#acctDisplayname").val('');
	$("#acctForm").validate().resetForm();
}

$('#accountModal').on('hide', function () {
	$("#acctStatus").html("");
	
	clearAcctForm();
});