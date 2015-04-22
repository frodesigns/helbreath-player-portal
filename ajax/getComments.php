<?php
	if($_POST) {
		$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
		$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
		$rootpath = "/portal";
		
		include("$serverrootpath/includes/db.inc.php");
		include("$includepath/common.php");
		
		user_protect();
		if (isset($_SESSION['user_id'])) {
			$user_id = $_SESSION['user_id'];
		}
	
		$sort = str_replace( "'", "''", stripslashes(trim($_POST["sort"])) );
		$postid = str_replace( "'", "''", stripslashes(trim($_POST["postid"])) );
		
		if ($sort == "new") {
			$order = "ORDER BY PPComments.UpdateDT DESC";
		} else if ($sort == "old") {
			$order = "ORDER BY PPComments.UpdateDT ASC";
		} else if ($sort == "best") {
			$order = "ORDER BY PPComments.CommentRating DESC";
		} else {
			$order = "ORDER BY PPComments.UpdateDT DESC";
		}
		
		if($sort && $postid) {
			$result = QueryWS1("SELECT PPComments.*, PlayerPortalLogin.DisplayAlias
				FROM PPComments
				INNER JOIN PlayerPortalLogin ON PPComments.PPLoginID = PlayerPortalLogin.PPLoginID 
				WHERE PostID = $postid
				$order");
						
			if (Num_Rows($result) > 0 ) {
				for ( $i = 0; $i < Num_Rows($result); $i++ ) {
					$commentid = Result($result, $i, "CommentID");
					$displayalias = Result($result, $i, "DisplayAlias");
					$pploginid = Result($result, $i, "PPLoginID");
					$commentcontent = Result($result, $i, "CommentContent");
					$commentrating = Result($result, $i, "CommentRating");
					$timestamp = getRelativeTime(Result($result, $i, "UpdateDT"));
					
					$class = "";
					
					if (isset($_SESSION['user_id'])) {
						$result3 = QueryWS1("SELECT Vote 
							FROM PPCommentVotes
							WHERE CommentID = $commentid AND PPLoginID = $user_id");
							
						if (Num_Rows($result3) > 0 ) {
							list($vote) = mssql_fetch_row($result3);
							if ($vote == 0) {	
								$class = "down";
							} else if ($vote == 1) {
								$class = "up";
							}
						} else {
							$class = "";
						}
					}
					
					if ($commentrating <= -5) {
						$commentcontent = "<em>This comment has been hidden due to excessive downvotes.</em>";
					}
					
					echo "<div class='commentitem' id='$commentid'>";		
						echo "<div class='voting $class'>";
							if (isset($_SESSION['user_id'])) {
								echo "<a class='upvote' href='#'><i class='icon-thumbs-up'></i></a>";
							} else {
								echo "<a class='upvote itemhide' href='#'><i class='icon-thumbs-up'></i></a>";
							}
							echo "<div>$commentrating</div>";
							if (isset($_SESSION['user_id'])) {
								echo "<a class='downvote' href='#'><i class='icon-thumbs-down'></i></a>";
							} else {
								echo "<a class='downvote itemhide' href='#'><i class='icon-thumbs-down'></i></a>";
							}
						echo "</div>";		
						echo "$commentcontent<br /><br />";
						echo "<small>Posted <em>$timestamp</em> by <a href='$rootpath/userprofile.php?username=$displayalias'>$displayalias</a>";
						if (isset($_SESSION['user_id'])) {
							if ($pploginid == $user_id || checkAdmin() || checkMod()) {
								echo " | <a class='deletecomment' href='#'>Delete</a>";
							}
						}
					echo "</small></div>";
				}
			}
		} else { 
			echo "Error";
		}
	}
?>