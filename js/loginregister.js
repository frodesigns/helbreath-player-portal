$(document).ready(function() {
	
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
				url: "/portal/ajax/verifyGameAccount.php",
				cache: false,
				data: query,
				success: function(data) {
					var obj = jQuery.parseJSON(data);
					if(obj.success) {						
						var accounts = $("#gameAccounts").val();
						if (accounts.indexOf(username) == -1 && accounts.indexOf(displayname) == -1) {
							accounts += username + ":" + displayname + ";";
							$("#gameAccounts").val(accounts);
							$("#accounts tbody").append('<tr rel="' + username + ':' + displayname + '"><td>' + username + '</td><td>' + displayname + '</td><td><button class="acctRemove btn btn-mini btn-danger"><i class="icon-remove icon-white"></i> Remove</button></td></tr>');
							$("#acctStatus").html(obj.message);
							clearAcctForm();
							setTimeout(function() { $('#accountModal').modal('hide'); }, 1000);
						} else {
							$("#acctStatus").html("Error: This Account or Display Name is already listed.");
						}
					} else { 
						$("#acctStatus").html(obj.message);
					}
				}
			});
		}
	});
	
	$("#loginForm").validate();
	
	$("#registerForm").validate({
		rules: {
			regPassword: {
				required: true,
				minlength: 6
			},
			regConfirmPassword: {
				required: true,
				minlength: 6,
				equalTo: "#regPassword"
			}
		},
		messages: {
			regPassword: {
				required: "Please provide a password.",
				minlength: "Your password must be at least 6 characters long."
			},
			regConfirmPassword: {
				required: "Please provide a password.",
				minlength: "Your password must be at least 6 characters long.",
				equalTo: "Passwords do not match."
			}
		}
	});
	
	$("body").on("click", ".acctRemove", function(e) {
		if (confirm("Are you sure you want to remove this account?")) {
			e.preventDefault();
			
			var item = $(this).parent().attr('rel');
			var accounts = $("#gameAccounts").val();
			accounts = accounts.replace(item + ";", "");
			$("#gameAccounts").val(accounts);
						
			$(this).closest("tr").remove();
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