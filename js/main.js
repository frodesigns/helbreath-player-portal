$(document).ready(function() {

	$('[rel="tooltip"]').tooltip();
	
	$('[rel="tooltip"]').tooltip('hide');
	
	getTweet();
	
	$("#refresh-tweet").click(function() {
		getTweet();
		return false;
	});
	
	$("body").on("click", "#posts .upvote, #posts .downvote", function() {
		var postid = $(this).closest(".postitem").attr("id");
		var rating = parseInt( $(this).siblings("div").html() );
		
		if ($(this).hasClass("upvote")) {
			var vote = 1;
			if ($(this).parent().hasClass("up") || $(this).parent().hasClass("down")) {
				rating = rating + 2;
			} else {
				rating = rating + 1;
			}
			$(this).parent().removeClass("down").addClass("up");
		} else if ($(this).hasClass("downvote")) {
			var vote = 0;
			if ($(this).parent().hasClass("up") || $(this).parent().hasClass("down")) {
				rating = rating - 2;
			} else {
				rating = rating - 1;
			}
			$(this).parent().removeClass("up").addClass("down");
		}
		
		$(this).siblings("div").html(rating);
		
		var query = 'postid=' + postid + '&vote=' + vote;
		
		$.ajax({
			type: "POST",
			url: '/portal/ajax/postVote.php',
			cache: false,
			data: query,
			success: function(data) {
			
			}
		});
		
		return false;
	});
	
	$("body").on("click", "#postinfo .upvote, #postinfo .downvote", function() {
		var postid = $("#postid").val();
		var rating = parseInt( $(this).siblings("div").html() );
		
		if ($(this).hasClass("upvote")) {
			var vote = 1;
			if ($(this).parent().hasClass("up") || $(this).parent().hasClass("down")) {
				rating = rating + 2;
			} else {
				rating = rating + 1;
			}
			$(this).parent().removeClass("down").addClass("up");
		} else if ($(this).hasClass("downvote")) {
			var vote = 0;
			if ($(this).parent().hasClass("up") || $(this).parent().hasClass("down")) {
				rating = rating - 2;
			} else {
				rating = rating - 1;
			}
			$(this).parent().removeClass("up").addClass("down");
		}
		
		$(this).siblings("div").html(rating);
		
		var query = 'postid=' + postid + '&vote=' + vote;
		
		$.ajax({
			type: "POST",
			url: '/portal/ajax/postVote.php',
			cache: false,
			data: query,
			success: function(data) {
				
			}
		});
		
		return false;
	});
	
	$("body").on("click", "#comments .upvote, #comments .downvote", function() {
		var commentid = $(this).closest(".commentitem").attr("id");
		var rating = parseInt( $(this).siblings("div").html() );
		
		if ($(this).hasClass("upvote")) {
			var vote = 1;
			if ($(this).parent().hasClass("up") || $(this).parent().hasClass("down")) {
				rating = rating + 2;
			} else {
				rating = rating + 1;
			}
			$(this).parent().removeClass("down").addClass("up");
		} else if ($(this).hasClass("downvote")) {
			var vote = 0;
			if ($(this).parent().hasClass("up") || $(this).parent().hasClass("down")) {
				rating = rating - 2;
			} else {
				rating = rating - 1;
			}
			$(this).parent().removeClass("up").addClass("down");
		}
		
		$(this).siblings("div").html(rating);
		
		var query = 'commentid=' + commentid + '&vote=' + vote;
		
		$.ajax({
			type: "POST",
			url: '/portal/ajax/commentVote.php',
			cache: false,
			data: query,
			success: function(data) {
				
			}
		});
		
		return false;
	});
	
	$("body").on("click", ".deletepost", function() {
		var postid = $(this).closest(".postitem").attr("id");
		var query = 'postid=' + postid;
		if (confirm("Are you sure you want to delete this post?")) {
			$.ajax({
				type: "POST",
				url: '/portal/ajax/deletePost.php',
				cache: false,
				data: query,
				success: function(data) {
					$("#posts #" + postid).slideUp(200);
				}
			});
		}
		return false;
	});
	
	$("body").on("click", ".deletecomment", function() {
		var commentid = $(this).closest(".commentitem").attr("id");
		var query = 'commentid=' + commentid;
		if (confirm("Are you sure you want to delete this comment?")) {
			$.ajax({
				type: "POST",
				url: '/portal/ajax/deleteComment.php',
				cache: false,
				data: query,
				success: function(data) {
					$("#comments #" + commentid).slideUp(200);
				}
			});
		}
		return false;
	});
	
	//scroll to top
	$(".scrollToTop").click(function() {
		$('html, body').animate({ scrollTop: '0px' }, 500);	
		return false;
	});
	
	$("#warehouseTable tbody td:not(:last-child), #equippedTable tbody td:not(:last-child), #bagTable tbody td:not(:last-child)").click(function() {
		var therow = $(this).closest("tr");
		if (therow.attr("data-sitemid") != 0 && therow.attr("data-sid1") != 0 && therow.attr("data-sid2") != 0&& therow.attr("data-sid3") != 0 && !therow.find("td::nth-child(1)").hasClass("bound")) {
			var title = therow.find("td::nth-child(1)").text() + " " + therow.find("td::nth-child(2)").text() + " " + therow.find("td::nth-child(3)").text();
			$("#sitemid").val(therow.attr("data-sitemid"));
			$("#sid1").val(therow.attr("data-sid1"));
			$("#sid2").val(therow.attr("data-sid2"));
			$("#sid3").val(therow.attr("data-sid3"));
			$("#itemModal .modal-header h3").text(title);
			$('#itemModal').modal('show');
		}
	});
	
	$("body").on("click", "a.collapse", function() {
		$(this).closest(".widget").find(".widget-inner").slideToggle(200);
		$(this).find("i").toggleClass("icon-chevron-up").toggleClass("icon-chevron-down");
		
		return false;
	});
	
	$("#addTradeItem").click(function() {
		var thebtn = $(this);
		thebtn.hide();
		var sitemid = $("#sitemid").val();
		var sid1 = $("#sid1").val();
		var sid2 = $("#sid2").val();
		var sid3 = $("#sid3").val();
		
		var query = "sitemid=" + sitemid + "&sid1=" + sid1 + "&sid2=" + sid2 + "&sid3=" + sid3;
		
		$.ajax({
			type: "POST",
			url: "/portal/ajax/addTradeItem.php",
			cache: false,
			data: query,
			success: function(data) {
				var obj = jQuery.parseJSON(data);
				if (obj.success) {
					$('#itemModal').modal('hide');
					alert(obj.message);
				} else { 
					alert(obj.message);
				}
				thebtn.show();
			}
		});
	
		return false;
	});
	
	$("#addGuildItem").click(function() {
		var thebtn = $(this);
		thebtn.hide();
		var sitemid = $("#sitemid").val();
		var sid1 = $("#sid1").val();
		var sid2 = $("#sid2").val();
		var sid3 = $("#sid3").val();
		var guildid = $("select.guilds").val();
		
		var query = "sitemid=" + sitemid + "&sid1=" + sid1 + "&sid2=" + sid2 + "&sid3=" + sid3 + "&guildid=" + guildid;
		
		$.ajax({
			type: "POST",
			url: "/portal/ajax/addGuildItem.php",
			cache: false,
			data: query,
			success: function(data) {
				var obj = jQuery.parseJSON(data);
				if (obj.success) {
					$('#itemModal').modal('hide');
					alert(obj.message);
				} else { 
					alert(obj.message);
				}
				thebtn.show();
			}
		});
	
		return false;
	});
	
	$("body").on("click", ".removeguilditem", function(e){
		if (confirm("Are you sure you want to remove this Guild Item?")) {
			var therow = $(this).closest("tr");
			var sitemid = therow.attr("data-sitemid");
			var sid1 = therow.attr("data-sid1");
			var sid2 = therow.attr("data-sid2");
			var sid3 = therow.attr("data-sid3");
			
			var query = "sitemid=" + sitemid + "&sid1=" + sid1 + "&sid2=" + sid2 + "&sid3=" + sid3;
		
			$.ajax({
				type: "POST",
				url: "/portal/ajax/deleteGuildItem.php",
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
	
	$(".announcements a").click(function() {
		var postid = $(this).attr("rel");
		var title = $(this).text();
		var content = $("div#" + postid).html();
		
		$("#announcementsModal .modal-body").html(content);
		$("#announcementsModal .modal-header h3").text(title);
		$('#announcementsModal').modal('show');
		
		return false;
	});
	
	//highlight current page in nav
	$(".nav li").removeClass("active");
	var url = window.location.pathname + window.location.search;
	$(".nav li").find("a[href='" + url + "']").parent().addClass("active");
	
	//character quick change
	$(".characterList").change(function () {
		var charname = $(this).val();
		window.location = window.location.pathname + "?charname=" + charname;
		return false;
	});
	
	//table sorting
	$('body').on("click", ".sortable th", function(e){
		var th = $(this);
		var tableID = th.closest("table").attr("id");
		var column = th.index() + 1;		
		
		// a simple compare function, used by the sort below
		var compare_rows = function (a,b){
			var a_val = $(a).find("td::nth-child(" + column + ")").text().toLowerCase();
			var b_val = $(b).find("td::nth-child(" + column + ")").text().toLowerCase();
			
			var timestamp = Date.parse(a_val);
			var isdate = false;
			//chrome date parse bug fix
			if (isNumeric(a_val.charAt( a_val.length - 1 )) || a_val.charAt( a_val.length - 1 ) == "%") {
				timestamp = "NaN";
			} else if (a_val.charAt( a_val.length - 1 ) == "m" && (a_val.charAt( a_val.length - 2 ) == "p" || a_val.charAt( a_val.length - 2 ) == "a")) {
				isdate = true;
			}
			
			if (isNumeric(a_val.substr(0,1))) {
				a_val = Number(a_val.replaceAll(".","").replace(/[^0-9\.]+/g,""));
				b_val = Number(b_val.replaceAll(".","").replace(/[^0-9\.]+/g,""));
			} else if (!isNaN(timestamp) || isdate) {
				a_val = new Date(a_val.replace(" -", ""));
				b_val = new Date(b_val.replace(" -", ""));
			}
			
			if (th.find(".desc").length > 0) {
				if (a_val < b_val){
					return -1;
				} 
				if (a_val > b_val){
					return 1;
				}
				return 0;
			} else {
				if (a_val > b_val){
					return -1;
				}
				if (a_val < b_val){
					return 1;
				}
				return 0;
			}
		};

		// the actual sort
		$('#' + tableID + ' tbody tr').sort(compare_rows).appendTo('#' + tableID + ' tbody');		
		$('#' + tableID + ' .grandtotal').appendTo('#' + tableID + ' tbody');

		if (!th.find(".desc").length && !th.find(".asc").length) {	
			$('#' + tableID + ' th .sort').remove();
			th.append('<i class="sort desc icon-chevron-down"></i>');
		} else if (th.find(".desc").length) {
			$('#' + tableID + ' th .sort').remove();
			th.append('<i class="sort asc icon-chevron-up"></i>');
		} else if (th.find(".asc").length) {
			$('#' + tableID + ' th .sort').remove();
			th.append('<i class="sort desc icon-chevron-down"></i>');
		}
		
	});
	
	$("#warehouseTable th:nth-child(2), #equippedTable th:nth-child(2), #bagTable th:nth-child(2)").click();
});	

function updateClock() {			
	serverTime.setSeconds(serverTime.getSeconds() + 1);
	$("#serverTime").text(formatTime(serverTime));
}	

function formatTime(d) {
	var hh = d.getHours();
    var m = d.getMinutes();
    var s = d.getSeconds();
    var dd = "AM";
    var h = hh;
    if (h >= 12) {
        h = hh-12;
        dd = "PM";
    }
    if (h == 0) {
        h = 12;
    }
    m = m<10?"0"+m:m;

    s = s<10?"0"+s:s;

    return h + ":" + m + ":" + s + " " + dd;
}

function isNumeric(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

String.prototype.replaceAll = function(str1, str2, ignore) 
{
	return this.replace(new RegExp(str1.replace(/([\/\,\!\\\^\$\{\}\[\]\(\)\.\*\+\?\|\<\>\-\&])/g,"\\$&"),(ignore?"gi":"g")),(typeof(str2)=="string")?str2.replace(/\$/g,"$$$$"):str2);
}

function getTweet() {
	$.ajax({
		url: 'http://twitter.com/statuses/user_timeline.json?screen_name=HelbreathUSA&count=1&callback=?',
		dataType: 'json',
		cache: true,
		success: function(data) {
			var tweet = parseTweet(data[0].text);

			$("#lasttweet").html(tweet);
			
			$('#lasttweet a').attr('target', '_blank');
		}
	});
}

function parseTweet(text) {
	// Parse URIs
	text = text.replace(/[A-Za-z]+:\/\/[A-Za-z0-9-_]+\.[A-Za-z0-9-_:%&\?\/.=]+/, function(uri) {
		return uri.link(uri);
	});
	 
	// Parse Twitter usernames
	text = text.replace(/[@]+[A-Za-z0-9-_]+/, function(u) {
		var username = u.replace("@","")
		return u.link("http://twitter.com/"+username);
	});
	 
	// Parse Twitter hash tags
	text = text.replace(/[#]+[A-Za-z0-9-_]+/, function(t) {
		var tag = t.replace("#","%23")
		return t.link("http://search.twitter.com/search?q="+tag);
	});
	
	return text;
}