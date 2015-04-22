$(document).ready(function() {
	getTradeItems(0);
	
	$('.tradeitems').change(function() {
		getTradeItems(0);
	});
	
});

function getTradeItems(count) {
	if ($("#market").length) {
		$("#market").html('<div class="ajaxLoader"><img src="/portal/img/ajax-loader.gif" alt="Loading..." /></div>');	
		
		var sort = $("#sort").val();
		var sort2 = $("#sort2").val();
		if ($("#count").length) {
			count = $("#count").val();
		}
		var query = 'sort=' + sort + '&sort2=' + sort2 + '&count=' + count;
		
		$.ajax({
			type: "POST",
			url: '/portal/ajax/getTradeItems.php',
			cache: false,
			data: query,
			success: function(data) {
				$("#market").html(data);
			}
		});
	}
}