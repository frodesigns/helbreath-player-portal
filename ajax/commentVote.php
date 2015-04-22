<?php
	if($_POST) {
		$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
		$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
		$rootpath = "/portal";
		
		include("$serverrootpath/includes/db.inc.php");
		include("$includepath/common.php");
		
		page_protect();
		$user_id = $_SESSION['user_id'];
	
		$id = str_replace( "'", "''", stripslashes($_POST["commentid"]) );
		$vote = str_replace( "'", "''", stripslashes($_POST["vote"]) );
		
		if($id && $vote >= 0) {
			$result = QueryWS1("SELECT * FROM PPCommentVotes WHERE CommentID = $id AND PPLoginID = $user_id");
			
			if (Num_Rows($result) > 0 ) {
				$result2 = QueryWS1("UPDATE PPCommentVotes SET Vote = $vote WHERE CommentID = $id AND PPLoginID = $user_id");
				
				if ($vote == 1) {
					$result3 = QueryWS1("UPDATE PPComments SET CommentRating = CommentRating + 2 WHERE CommentID = $id");
				} else if ($vote == 0) {
					$result3 = QueryWS1("UPDATE PPComments SET CommentRating = CommentRating - 2 WHERE CommentID = $id");
				}
			} else  {
				$result2 = QueryWS1("INSERT INTO PPCommentVotes (CommentID, PPLoginID, Vote) VALUES ($id, $user_id, $vote)");
				
				if ($vote == 1) {
					$result3 = QueryWS1("UPDATE PPComments SET CommentRating = CommentRating + 1 WHERE CommentID = $id");
				} else if ($vote == 0) {
					$result3 = QueryWS1("UPDATE PPComments SET CommentRating = CommentRating - 1 WHERE CommentID = $id");
				}
			}
			
			echo json_encode(array('success' => true, 'message' => 'Success!'));
		}  else { 
			echo json_encode(array('success' => false, 'message' => 'Error!'));
		}
	}
?>