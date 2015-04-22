$(document).ready(function() {
	
	$("#spells1").addClass("active");

	$("#msWand, #msNeck").change(function() {
		var wandpercent = $("#msWand").val();
		var neckpercent = $("#msNeck").val();
		var percentcost = (100 - wandpercent - neckpercent) / 100;
		
		$('.mpcost').each(function(index) {
			var origcost = $(this).find("input").val();
			var newcost = Math.round(origcost * percentcost);
			$(this).find("span").text(newcost);
		});
	});

});