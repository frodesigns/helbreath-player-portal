<?php
	if($_POST) {
		$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
		$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
		$rootpath = "/portal";
		
		include("$serverrootpath/includes/db.inc.php");
		include("$includepath/common.php");
		
		page_protect();
		$user_id = $_SESSION['user_id'];
	
		$id = str_replace( "'", "''", stripslashes($_POST["postid"]) );
		$vote = str_replace( "'", "''", stripslashes($_POST["vote"]) );
		
		if($id > 0 && $vote >= 0) {
			$result = QueryWS1("SELECT * FROM PPPostVotes WHERE PostID = $id AND PPLoginID = $user_id");
			
			if (Num_Rows($result) > 0 ) {
				$result2 = QueryWS1("UPDATE PPPostVotes SET Vote = $vote WHERE PostID = $id AND PPLoginID = $user_id");
				
				if ($vote == 1) {
					$result3 = QueryWS1("UPDATE PPPosts SET PostRating = PostRating + 2 WHERE PostID = $id");
				} else if ($vote == 0) {
					$result3 = QueryWS1("UPDATE PPPosts SET PostRating = PostRating - 2 WHERE PostID = $id");
				}
			} else  {
				$result2 = QueryWS1("INSERT INTO PPPostVotes (PostID, PPLoginID, Vote) VALUES ($id, $user_id, $vote)");
				
				if ($vote == 1) {
					$result3 = QueryWS1("UPDATE PPPosts SET PostRating = PostRating + 1 WHERE PostID = $id");
				} else if ($vote == 0) {
					$result3 = QueryWS1("UPDATE PPPosts SET PostRating = PostRating - 1 WHERE PostID = $id");
				}
			}
			
			echo json_encode( array( 'success' => true, 'message' => 'Success!', 'numrows' => Num_Rows($result) ) );
		}  else { 
			echo json_encode(array('success' => false, 'message' => 'Error!'));
		}
	}
?>