$(document).ready(function() {
	$('input[type="number"]').each(function() { $(this).val(10); });
	
	updateInfo();
	
	var max = 367;	
	$('#remainingstats').html(max - 60);
	
	$('input[type="number"]').keyup(function() {	
		updateInfo();
	});
	
	$('input[type="number"]').change(function() {
		updateInfo();
	});
	
	$('input[type="number"]').blur(function() {
		if ( $(this).val() < 10 ) {
			$(this).val(10);
		} else if ( $(this).val() > 200 ) {
			$(this).val(200);
		}
	
		updateInfo();
	});
	
	$("#rejuvSubmit").click(function(e) {	
		e.preventDefault();
		
		var charname = $("#charname").val();
		
		if ( confirm("Is " + charname + " logged out of the game?") ) {
			if ( parseInt($('#remainingstats').html()) == 0 ) {
				var charid = $("#charid").val();
				
				var charStr = $("#str").val();
				var charDex = $("#dex").val();
				var charInt = $("#int").val();
				var charMag = $("#mag").val();
				var charVit = $("#vit").val();
				var charChr = $("#chr").val();
				
				var query = 'charid=' + charid + '&str=' + charStr + '&dex=' + charDex + '&int=' + charInt + '&mag=' + charMag + '&vit=' + charVit + '&chr=' + charChr;
				
				$("#skillsTable tbody tr.rejuvchange").each(function() {
					var skillid = $(this).attr("rel");
					var newPercent = parseInt($(this).find("td:nth-child(2)").html());
					
					query = query + "&" + skillid + "=" + newPercent;
				});
				
				$.ajax({
					type: "POST",
					url: "/portal/ajax/rejuvSubmit.php",
					cache: false,
					data: query,
					success: function(data) {
						var obj = jQuery.parseJSON(data);
						if (obj.success) {
							alert(obj.message);
							window.location = "/portal/charstats.php?charname=" + charname;
						} else { 
							alert(obj.message);
						}
					}
				});
			} else {
				alert("Error: Remaining Stat Points needs to be 0.");
			}
		}
	});
});

function updateInfo() {	
	var sum = 0;
	$('input[type="number"]').each(function() { sum += parseInt(this.value, 10); });
	var remaining = 367 - sum;
	
	if (remaining >= 0) {  
		$('#remainingstats').html(remaining);
	} else {
		$('#remainingstats').html("Too many stats spent! You must remove " + Math.abs(remaining) + " points.");
	}
	
	var charStr = $("#str").val();
	var charDex = $("#dex").val();
	var charInt = $("#int").val();
	var charMag = $("#mag").val();
	var charVit = $("#vit").val();
	var charChr = $("#chr").val();
	
	$("#skillsTable tbody tr").each(function() {
		var skillid = $(this).attr("rel");
		var skillpercent = parseInt($(this).find("td:nth-child(3)").html());
		var skillstat;
		
		if ( (skillid >= 6 && skillid <= 11) || skillid == 1 || skillid == 11 || skillid == 14 ) {
			skillstat = charDex;
		} else if ( skillid == 4 || skillid == 21 ) {
			skillstat = charMag;
		} else if ( skillid == 0 || skillid == 5 || skillid == 13 ) {
			skillstat = charStr;
		} else if ( skillid == 2 || skillid == 12 || skillid == 19 ) {
			skillstat = charInt;
		} else if ( skillid == 23 ) {
			skillstat = charVit;
		}
		
		if ( skillstat < (skillpercent / 2) ) {
			$(this).addClass("rejuvchange");
			var newskillpercent = (skillstat * 2);
			
			if ( newskillpercent > skillpercent ) {
				newskillpercent = skillpercent;
			}
			
			$(this).find("td:nth-child(2)").html( newskillpercent + "%");
		} else {
			$(this).find("td:nth-child(2)").html( skillpercent + "%");
			$(this).removeClass("rejuvchange");
		}
	});
	
	$("#spellsTable tbody tr").each(function() {
		if ( parseInt($(this).find("td:nth-child(2)").html()) > charInt ) {
			$(this).addClass("rejuvchange");
		} else {
			$(this).removeClass("rejuvchange");
		}
	});
}