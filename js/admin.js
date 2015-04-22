$(document).ready(function() {
	//keep active tab on refresh
	var url = document.location.toString();
	if (url.match('#')) {
		$('.nav-tabs a[href=#'+url.split('#')[1]+']').tab('show') ;
		$('html, body').animate({ scrollTop: '0px' }, 0);
	} else {
		$('.nav-tabs a:first').tab('show');
		$('html, body').animate({ scrollTop: '0px' }, 0);
	}

	$('.nav-tabs a').on('shown', function (e) {
		window.location.hash = e.target.hash;
		$('html, body').animate({ scrollTop: '0px' }, 0);
	});	

	$("#massEmailForm").validate();

	$("body").on("click", ".activateUser", function(e){
		var accountID = $(this).closest("tr").attr("id");
		var activate = $(this).attr("rel");
		var query = 'accountid='+ accountID + '&approved=' + activate;
		var button = $(this);
		
		if (activate == 1) {
			var newrel = "0";
			var btntext = "Deactivate";
			var text = "Activated";
			var oldclass = "btn-success";
			var newclass = "btn-danger";
		} else if (activate == 0) {
			var newrel = "1";
			var btntext = "Activate";
			var text = "Pending Activation";
			var oldclass = "btn-danger";
			var newclass = "btn-success";
		}
		
		$.ajax({
			type: "POST",
			url: "/portal/ajax/adminActivateAccount.php",
			cache: false,
			data: query,
			success: function(data) {
				var obj = jQuery.parseJSON(data);
				if(obj.success) {
					button.attr("rel", newrel).text(btntext).removeClass(oldclass).addClass(newclass);
					button.siblings("span").text(text);
				} else { 
					alert(obj.message);
				}
			}
		});
	});
	
	$("body").on("click", ".promoteUser", function(e){
		var accountID = $(this).closest("tr").attr("id");
		var admin = $(this).attr("rel");
		var query = 'accountid='+ accountID + '&admin=' + admin;
		var button = $(this);
		
		if (admin == 2) {
			var newrel = "0";
			var btntext = "Demote";
			var text = "Admin";
			var oldclass = "btn-success";
			var newclass = "btn-danger";
		} else if (admin == 1) {
			var newrel = "2";
			var btntext = "Promote";
			var text = "Moderator";
			var oldclass = "btn-danger";
			var newclass = "btn-success";
		} else if (admin == 0) {
			var newrel = "1";
			var btntext = "Promote";
			var text = "Member";
			var oldclass = "btn-danger";
			var newclass = "btn-success";
		}
		
		$.ajax({
			type: "POST",
			url: "/portal/ajax/adminPromoteAccount.php",
			cache: false,
			data: query,
			success: function(data) {
				var obj = jQuery.parseJSON(data);
				if(obj.success) {
					button.attr("rel", newrel).text(btntext).removeClass(oldclass).addClass(newclass);
					button.siblings("span").text(text);
				} else { 
					alert(obj.message);
				}
			}
		});
	});
	
	$("body").on("click", ".deleteUser", function(e){
		if (confirm("Are you sure you want to permanently delete this account?")) {
			var accountID = $(this).closest("tr").attr("id");
			var theRow = $(this).closest("tr");
			var query = 'accountid='+ accountID;
			
			$.ajax({
				type: "POST",
				url: "/portal/ajax/adminDeleteAccount.php",
				cache: false,
				data: query,
				success: function(data) {
					var obj = jQuery.parseJSON(data);
					if(obj.success) {
						theRow.remove();
					} else { 
						alert(obj.message);
					}
				}
			});
		}
	});
	
	$("body").on("click", ".viewLinkedAccounts", function(e){
		var email = $(this).parent().siblings(".useremail").text();
		var userid = $(this).closest("tr").attr("id");
		
		$("#linkedAccountsModal .modal-body").html('<div class="ajaxLoader"><img src="/portal/img/ajax-loader.gif" alt="Loading..." /></div>');
		$("#linkedAccountsModal .modal-header h3").text(email + "'s Linked Accounts");
		$('#linkedAccountsModal').modal('show');
		
		$.ajax({
			type: "GET",
			url: "/portal/ajax/adminViewLinkedAccounts.php?userid=" + userid,
			cache: false,
			success: function(data) {
				$("#linkedAccountsModal .modal-body").html(data);
			}
		});
	});
});