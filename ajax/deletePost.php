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
		
		if($id) {
			$result = QueryWS1("DELETE FROM PPPosts WHERE PostID = $id");
			$result2 = QueryWS1("DELETE FROM PPPostVotes WHERE PostID = $id");			
			$result3 = QueryWS1("SELECT CommentID FROM PPComments WHERE PostID = $id");
			
			for ( $i = 0; $i < Num_Rows($result3); $i++ ) {
				$commentid = Result($result3, $i, "CommentID");
				
				$result4 = QueryWS1("DELETE FROM PPCommentVotes WHERE CommentID = $commentid");
			}
			
			$result5 = QueryWS1("DELETE FROM PPComments WHERE PostID = $id");

			echo json_encode(array('success' => true, 'message' => 'Success!'));
		}  else { 
			echo json_encode(array('success' => false, 'message' => 'Error!'));
		}
	}
?>