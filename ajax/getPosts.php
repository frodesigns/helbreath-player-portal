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
		$sort2 = str_replace( "'", "''", stripslashes(trim($_POST["sort2"])) );
		$count = str_replace( "'", "''", stripslashes(trim($_POST["count"])) );
		
		if ($count == 0) {
			$count = "";
		} else {
			$count = " TOP $count";
		}
		
		if ($sort == "new") {
			$order = "ORDER BY PPPosts.UpdateDT DESC";
		} else if ($sort == "hot") {
			$order = "ORDER BY PPPosts.CommentCount DESC";
		} else if ($sort == "best") {
			$order = "ORDER BY PPPosts.PostRating DESC";
		} else if ($sort == "worst") {
			$order = "ORDER BY PPPosts.PostRating ASC";
		} else {
			$order = "ORDER BY PPPosts.UpdateDT DESC";
		}
		
		if ($sort2 == "all") {
			$where = "";
		} else if ($sort2 == "month") {
			$where = "WHERE PPPosts.UpdateDT > GETDATE() - 30";
		} else if ($sort2 == "week") {
			$where = "WHERE PPPosts.UpdateDT > GETDATE() - 7";
		} else if ($sort2 == "day") {
			$where = "WHERE PPPosts.UpdateDT > GETDATE() - 1";
		} else {
			$where = "";
		}
		
		if($sort) {		
			//code to get table column names
			// $query = 'select * from PPPosts';
			// $result = QueryWS1($query); 
			// if (!$result) { 
				// $message = 'ERROR: ' . mssql_get_last_message(); 
				// return $message; 
			// } else { 
				// $i = 0; 
				// echo '<html><body><table><tr>'; 
				// while ($i < mssql_num_fields($result)) { 
					// $meta = mssql_fetch_field($result, $i); 
					// echo '<td>' . $meta->name . '</td>'; 
					// $i = $i + 1; 
				// } 
				// echo '</tr>'; 
				// while ( ($row = mssql_fetch_row($result))) { 
					// $count = count($row); 
					// $y = 0; 
					// echo '<tr>'; 
					// while ($y < $count) { 
						// $c_row = current($row); echo '<td>' . $c_row . '</td>'; 
						// next($row); $y = $y + 1; 
					// } 
					// echo '</tr>'; 
				// } 
				// mssql_free_result($result); echo '</table></body></html>'; 
			// }

			$result = QueryWS1("SELECT$count PPPosts.*, PlayerPortalLogin.DisplayAlias
				FROM PPPosts 
				INNER JOIN PlayerPortalLogin ON PPPosts.PPLoginID = PlayerPortalLogin.PPLoginID 
				$where 
				$order");
						
			if (Num_Rows($result) > 0 ) {
				for ( $i = 0; $i < Num_Rows($result); $i++ ) {
					$postid = Result($result, $i, "PostID");
					$pploginid = Result($result, $i, "PPLoginID");
					$displayalias = Result($result, $i, "DisplayAlias");
					$postcontent = Result($result, $i, "PostContent");
					$postrating = Result($result, $i, "PostRating");
					$screenshoturl = Result($result, $i, "ScreenshotUrl");
					$commentcount = Result($result, $i, "CommentCount");
					$timestamp = getRelativeTime(Result($result, $i, "UpdateDT"));
					
					if ($screenshoturl) {
						$screenshoturl = "$rootpath/upload/" . $screenshoturl;
						
						if (strpos($serverrootpath, "dev_helbreath")) {
							$screenshoturl = "http://www.helbreathusa.com" . $screenshoturl;
						}
					}
					
					$class = "";
					
					if (isset($_SESSION['user_id'])) {
						$result2 = QueryWS1("SELECT Vote 
							FROM PPPostVotes
							WHERE PostID = $postid AND PPLoginID = $user_id");
							
						if (Num_Rows($result2) > 0 ) {
							list($vote) = mssql_fetch_row($result2);
							if ($vote == 0) {	
								$class = "down";
							} else if ($vote == 1) {
								$class = "up";
							}
						} else {
							$class = "";
						}
					}
					
					$s = "s";
					if ($commentcount == 1) {
						$s = "";
					}
					
					echo "<div class='postitem' id='$postid'>";		
						if ($screenshoturl) {
							echo "<a href='$rootpath/post.php?postid=$postid'><img src='$screenshoturl' /></a>";
						}
						echo "<div class='voting $class'>";
							if (isset($_SESSION['user_id'])) {
								echo "<a class='upvote' href='#'><i class='icon-thumbs-up'></i></a>";
							} else {
								echo "<a class='upvote itemhide' href='#'><i class='icon-thumbs-up'></i></a>";
							}
							echo "<div>$postrating</div>";
							if (isset($_SESSION['user_id'])) {
								echo "<a class='downvote' href='#'><i class='icon-thumbs-down'></i></a>";
							} else {
								echo "<a class='downvote itemhide' href='#'><i class='icon-thumbs-down'></i></a>";
							}
						echo "</div>";		
						echo "$postcontent<br /><br />";
						echo "<small>Posted <em>$timestamp</em> by <a href='$rootpath/userprofile.php?username=$displayalias'>$displayalias</a> | <a href='$rootpath/post.php?postid=$postid'><span class='badge badge-info'>$commentcount Comment$s</span></a>";
						if (isset($_SESSION['user_id'])) {
							if ($pploginid == $user_id || checkAdmin() || checkMod()) {
								echo " | <a class='deletepost' href='#'>Delete</a>";
							}
						}
					echo "</small></div>";
				}
			} else {
				echo "No posts found.";
			}
		} else { 
			echo "Error";
		}
	}
?>