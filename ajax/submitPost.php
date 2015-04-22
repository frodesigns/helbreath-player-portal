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
		
		$postcontent = str_replace("'", "''", trim($_POST['postcontent']));
		$postcontent = strip_tags($postcontent);
		
		// $atpos = strpos($postcontent, "@");
		// if ($atpos !== false) {
			// $spacepos = strpos($postcontent, " ", $atpos);
			// if ($spacepos > $atpos) {
				// $end = $spacepos - $atpos;
			// } else {
				// $end = strlen($postcontent);
			// }
			// $userref = substr($postcontent, $atpos, $end);
			// $username = str_replace("@", "", $userref);
			
			// $postcontent = str_replace($userref, '<a href="' . $rootpath . '/userprofile.php?username=' . $username . '">' . $userref . '</a>', $postcontent);
		// }
				
		$file_name = "";
		$errors = false;
		
		if ($_FILES) {
			if ($_FILES['file']['type'] == "image/jpeg") {
				if ($_FILES['file']['error'] > 0) {
					echo json_encode(array('success' => false, 'message' => 'There was an error uploading your screenshot.'));
					$errors = true;
				} else {
					$file_name = str_replace("'", "", $_FILES['file']['name']);
					$file_path = $_SERVER['DOCUMENT_ROOT'] . "/portal/upload/";
					$file_path = str_replace("\dev_helbreath", "", $file_path);
					$full_file_path = $file_path . $file_name;
					
					if (strpos($file_name,'HelShot') !== false) {
						if (file_exists($full_file_path)) {
							$unique_id = md5(uniqid(time())); 
							$filesplit = explode('.', $file_name);
								
							$file_name = $filesplit[0] . "_" . $unique_id . "." . $filesplit[1];
							$full_file_path = $file_path . $file_name;
						}
							
						move_uploaded_file($_FILES['file']['tmp_name'], $full_file_path);
					} else {
						echo json_encode(array('success' => false, 'message' => 'Not a valid helbreath screenshot.'));
						$errors = true;
					}				
				}
			} else { 
				echo json_encode(array('success' => false, 'message' => 'Not a valid helbreath screenshot.'));
				$errors = true;
			}
		}
		
		if ($errors == false) {
			$sql_insert = "INSERT INTO PPPosts
				(PPLoginID, PostContent, PostRating, ScreenshotUrl, CommentCount)
				VALUES
				($user_id, '$postcontent', 1, '$file_name', 0)";
				
			QueryWS1($sql_insert) or die("Insertion Failed:" . mssql_get_last_message());
			
			$result = QueryWS1("SELECT TOP 1 PostID, UpdateDT, PostContent FROM PPPosts ORDER BY PostID DESC");
			list($postid, $timestamp, $newpostcontent) = mssql_fetch_row($result);
			
			$sql_insert2 = "INSERT INTO PPPostVotes
				(PPLoginID, PostID, Vote)
				VALUES
				($user_id, $postid, 1)";
				
			QueryWS1($sql_insert2) or die("Insertion Failed:" . mssql_get_last_message());
			
			$timestamp = getRelativeTime($timestamp);
			
			if ($file_name != "") {
				echo json_encode(array('success' => true, 'message' => "<div class='postitem' id='$postid' style='display: none;'>
					<a href='$rootpath/post.php?postid=$postid'><img src='$rootpath/upload/$file_name' /></a>
					<div class='voting up'>
						<a class='upvote' href='#'><i class='icon-thumbs-up'></i></a>
						<div>1</div>
						<a class='downvote' href='#'><i class='icon-thumbs-down'></i></a>
					</div>		
					$newpostcontent<br /><br />
					Posted <em>Just Now</em> by <a href='$rootpath/userprofile.php?username=$user_name'>$user_name</a> | <a href='$rootpath/post.php?postid=$postid'><span class='badge badge-info'>0 Comments</span></a> | <a class='deletepost' href='#'>Delete</a>
				</div>
				"));
			} else {
				echo json_encode(array('success' => true, 'message' => "<div class='postitem' id='$postid' style='display: none;'>
					<div class='voting up'>
						<a class='upvote' href='#'><i class='icon-thumbs-up'></i></a>
						<div>1</div>
						<a class='downvote' href='#'><i class='icon-thumbs-down'></i></a>
					</div>		
					$newpostcontent<br /><br />
					Posted <em>Just Now</em> by <a href='$rootpath/userprofile.php?username=$user_name'>$user_name</a> | <a href='$rootpath/post.php?postid=$postid'><span class='badge badge-info'>0 Comments</span></a> | <a class='deletepost' href='#'>Delete</a>
				</div>
				"));
			}			
		}
	}
?>