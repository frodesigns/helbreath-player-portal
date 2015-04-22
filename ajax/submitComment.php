<?php
	if($_POST) {
		$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
		$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
		$rootpath = "/portal";
		
		include("$serverrootpath/includes/db.inc.php");
		include("$includepath/common.php");
		
		page_protect();
		$user_id = $_SESSION['user_id'];
		$user_name = $_SESSION['display_name'];
		
		$commentcontent = str_replace("'", "''", trim($_POST['commentcontent']));
		$commentcontent = strip_tags($commentcontent);
		$postid = str_replace("'", "", trim($_POST['postid']));
		
		// $atpos = strpos($commentcontent, "@");
		// if ($atpos !== false) {
			// $spacepos = strpos($commentcontent, " ", $atpos);
			// if ($spacepos > $atpos) {
				// $end = $spacepos - $atpos;
			// } else {
				// $end = strlen($commentcontent);
			// }
			// $userref = substr($commentcontent, $atpos, $end);
			// $username = str_replace("@", "", $userref);
			
			// $commentcontent = str_replace($userref, '<a href="' . $rootpath . '/userprofile.php?username=' . $username . '">' . $userref . '</a>', $commentcontent);
		// }
		
		if ($commentcontent && $postid) {
			$sql_insert = "INSERT INTO PPComments
				(PPLoginID, CommentContent, CommentRating, PostID)
				VALUES
				($user_id, '$commentcontent', 1, $postid)";				
			QueryWS1($sql_insert) or die("Insertion Failed:" . mssql_get_last_message());
			
			$sql_update = "UPDATE PPPosts SET CommentCount = CommentCount + 1 WHERE PostID = $postid";			
			QueryWS1($sql_update) or die("Update Failed:" . mssql_get_last_message());
			
			$result = QueryWS1("SELECT TOP 1 CommentID, UpdateDT, CommentContent FROM PPComments ORDER BY CommentID DESC");
			list($commentid, $timestamp, $newcommentcontent) = mssql_fetch_row($result);
			
			$sql_insert2 = "INSERT INTO PPCommentVotes
				(PPLoginID, CommentID, Vote)
				VALUES
				($user_id, $commentid, 1)";
				
			QueryWS1($sql_insert2) or die("Insertion Failed:" . mssql_get_last_message());
			
			$timestamp = getRelativeTime($timestamp);
			
			echo json_encode(array('success' => true, 'message' => "<div class='commentitem' id='$commentid' style='display: none;'>
				<div class='voting up'>
					<a class='upvote' href='#'><i class='icon-thumbs-up'></i></a>
					<div>1</div>
					<a class='downvote' href='#'><i class='icon-thumbs-down'></i></a>
				</div>		
				$newcommentcontent<br /><br />
				Posted <em>Just Now</em> by <a href='$rootpath/userprofile.php?username=$user_name'>$user_name</a> | <a class='deletecomment' href='#'>Delete</a>
			</div>
			"));		
	}
	}
?>