$(document).ready(function() {

	$("a.getmembers").click(function() {
		var name = $(this).attr("title");
		
		$("#assistsModal .modal-body").html('<div class="ajaxLoader"><img src="/portal/img/ajax-loader.gif" alt="Loading..." /></div>');
		$("#assistsModal .modal-header h3").text(name);
		$('#assistsModal').modal('show');
		
		$.ajax({
			type: "GET",
			url: $(this).attr("href"),
			cache: false,
			success: function(data) {
				$("#assistsModal .modal-body").html(data);
			}
		});
		
		return false;
	}); 
	
});