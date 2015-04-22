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
		
		if($id) {
			$result3 = QueryWS1("SELECT PostID FROM PPComments WHERE CommentID = $id");
			list($postid) = mssql_fetch_row($result3);
			
			$result4 = QueryWS1("UPDATE PPPosts SET CommentCount = CommentCount - 1 WHERE PostID = $postid");
			
			$result = QueryWS1("DELETE FROM PPComments WHERE CommentID = $id");
			$result2 = QueryWS1("DELETE FROM PPCommentVotes WHERE CommentID = $id");
			
			

			echo json_encode(array('success' => true, 'message' => 'Success!'));
		}  else { 
			echo json_encode(array('success' => false, 'message' => 'Error!'));
		}
	}
?>