$(document).ready(function() {
	//keep active tab on refresh
	var url = document.location.toString();
	if (url.match('#')) {
		$('.nav-tabs a[href=#'+url.split('#')[1]+']').tab('show') ;
	} else {
		$('.nav-tabs a:first').tab('show');
	}

	$('.nav-tabs a').on('shown', function (e) {
		window.location.hash = e.target.hash;
		$('html, body').animate({ scrollTop: '0px' }, 0);
	});	
	
	$("body").on("click", ".removeGuildmember", function(e) {
		var character = $(this).attr("title");
		var guild = $(this).attr("rel");
		var tableRow = $(this).closest("tr");
		
		if (confirm("Are you sure you want to remove " + character + " from the guild? Is " + character + " logged out of the game?")) {
			$.ajax({
				type: "POST",
				url: "/portal/ajax/removeGuildMember.php",
				data: { "CharName" : character, "GuildName" : guild },
				dataType: "json",
				success: function(data){
					if (data.error === false) {
						$(".remove[title=" + character + "]").parent().parent().hide();
						tableRow.remove();
					}
					alert(data.msg);
				}
			});
		}
		
		return false;
	});
	
	$("body").on("click", ".promoteGuildmember", function(e) {
		var character = $(this).attr("title");
		var type = $(this).text();
		var theButton = $(this);
		
		if (type == "Promote") {
			var newtype = "Demote";
			var newbtnclass = "btn-danger";
			var newclass = "demote";
		} else {
			var newtype = "Promote";
			var newbtnclass = "btn-success";
			var newclass = "promote";
		}
		
		if (confirm("Is " + character + " logged out of the game?")) {
			$.ajax({
				type: "POST",
				url: "/portal/ajax/promoteGuildMember.php",
				data: { CharName : character, Type : type },
				dataType: "json",
				success: function(data){
					if (data.error === false) {
						theButton.replaceWith("<a class='promoteGuildmember " + newclass + "' title='" + character + "'>" + newtype + "</a>");
						if ($('.demote').length < 2) {
							$('.promote').show();
						}
						if ($('.demote').length == 2) {
							$('.promote').hide();
						}
					}
					alert(data.msg);
				}
			});
		}
		
		return false;
	});
});