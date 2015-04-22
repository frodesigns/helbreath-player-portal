$(document).ready(function() {
	getScreenshots(0);	
	
	$('.screenshots').change(function() {
		getScreenshots(0);
	});
	
});

function getScreenshots(count) {
	if ($(".carousel-inner").length) {
		$(".carousel-inner").html('<div class="ajaxLoader"><img src="/portal/img/ajax-loader.gif" alt="Loading..." /></div>');	
		
		var sort = $("#sort").val();
		var sort2 = $("#sort2").val();
		if ($("#count").length) {
			count = $("#count").val();
		}
		var query = 'sort=' + sort + '&sort2=' + sort2 + '&count=' + count;
		
		if (sort != "") {
			$.ajax({
				type: "POST",
				url: '/portal/ajax/getScreenshots.php',
				cache: false,
				data: query,
				success: function(data) {
					$(".carousel-inner").html(data);
					$('.carousel').carousel();
				}
			});
		} else {
			$("#posts").html("No posts found.");
		}
	}
}