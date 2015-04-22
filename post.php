<?php
	$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	$rootpath = "/portal";
	
	include("$serverrootpath/includes/db.inc.php");
	include("$includepath/common.php");
	
	user_protect();
	
	if (isset($_SESSION['user_id'])) {
		$user_id = $_SESSION['user_id'];
	}
	
	$thispostid = str_replace("'", "", trim($_GET['postid']));
	
	$shortcontent = "Post";
	
	if ($postid) {
		$result = QueryWS1("SELECT p.PPLoginID, p.PostContent, p.PostRating, p.ScreenshotUrl, p.CommentCount, p.UpdateDT, PlayerPortalLogin.DisplayAlias FROM PPPosts p INNER JOIN PlayerPortalLogin ON p.PPLoginID = PlayerPortalLogin.PPLoginID WHERE PostID = $thispostid");
		list($posterid, $postcontent, $postrating, $screenshoturl, $commentcount, $timestamp, $displayalias) = mssql_fetch_row($result);
		
		$timestamp = getRelativeTime($timestamp);
		$screenshoturl = trim($screenshoturl);
		
		if ($screenshoturl) {
			$screenshoturl = "$rootpath/upload/" . $screenshoturl;
			
			if (strpos($serverrootpath, "dev_helbreath")) {
				$screenshoturl = "http://www.helbreathusa.com" . $screenshoturl;
			}
		}
		
		$class = "";
		
		if (isset($_SESSION['user_id'])) {
			$result4 = QueryWS1("SELECT Vote 
				FROM PPPostVotes
				WHERE PostID = $thispostid AND PPLoginID = $user_id");
			
			if (Num_Rows($result4) > 0 ) {
				list($vote) = mssql_fetch_row($result4);
				if ($vote == 0) {	
					$class = "down";
				} else if ($vote == 1) {
					$class = "up";
				}
			} else {
				$class = "";
			}
		}
		
		$maxlen = 26;
		if (strlen($postcontent) > $maxlen) {
			$shortcontent = preg_replace('/\s+?(\S+)?$/', '', substr($postcontent, 0, $maxlen)) . "...";
		} else {
			$shortcontent = $postcontent;
		}
	}
	
	//keep at bottom
	include "$includepath/base.php";
?>

<?php startblock('title') ?>
	<? echo $shortcontent; ?> - Posts
<?php endblock() ?>

<?php startblock('header') ?>
	Single Post
<?php endblock() ?>

<?php startblock('additionalScripts') ?>
	<style type="text/css">
		h1 { display: none; }
	</style>
	<script type="text/javascript" src="<?php echo $rootpath; ?>/js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="<?php echo $rootpath; ?>/js/jquery.form.js"></script>
	<script type="text/javascript" src="<?php echo $rootpath; ?>/js/post.js"></script>
<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li><a href="<?php echo $rootpath; ?>/posts.php">Posts</a></li> <span class="divider">/</span>
	<li class="active"><? echo $shortcontent; ?></a>
<?php endblock() ?>

<?php startblock('containerContent') ?>
	<div class="row-fluid">
		<div id="postinfo" class="postitem span12">
			<div class="votes">
				<?php
					$result11 = QueryWS1("SELECT COUNT(*) FROM PPPostVotes WHERE Vote = 1 AND PostID = $thispostid");						
					list($upvotes) = mssql_fetch_row($result11);

					$result12 = QueryWS1("SELECT COUNT(*) FROM PPPostVotes WHERE Vote = 0 AND PostID = $thispostid");						
					list($downvotes) = mssql_fetch_row($result12);
					
					$s1 = "s";
					if ($upvotes == 1) {
						$s1 = "";
					}
					
					$s2 = "s";
					if ($downvotes == 1) {
						$s2 = "";
					}
					
					echo "<span class='thumbsup'>$upvotes Upvote$s1</span><br /><span class='thumbsdown'>$downvotes Downvote$s2</span>";
					
					if ($upvotes > 0 || $downvotes > 0) {
						$percent = round(($upvotes / ($upvotes + $downvotes)) * 100);
						echo "<br /><small>$percent% Liked This</small>";
					}
				?>
			</div>
			<div class="voting <?php echo $class; ?>">
				<?php if (isset($_SESSION['user_id'])) { ?>
					<a href="#" class="upvote"><i class="icon-thumbs-up"></i></a>
				<?php } else { ?>
					<a href="#" class="upvote itemhide"><i class="icon-thumbs-up"></i></a>
				<?php } ?>
				<div><?php echo $postrating; ?></div>
				<?php if (isset($_SESSION['user_id'])) { ?>
					<a href="#" class="downvote"><i class="icon-thumbs-down"></i></a>
				<?php } else { ?>
					<a href="#" class="downvote itemhide"><i class="icon-thumbs-down"></i></a>
				<?php } ?>
			</div>
			<?php echo $postcontent . "<br /><br />"; ?>
			<?php echo "Posted <em>$timestamp</em> by <a href='$rootpath/userprofile.php?username=$displayalias'>$displayalias</a>"; ?>			
		</div>
	</div>
	<div class="row-fluid">
	<?php if ($screenshoturl) { ?>
		<div class="span7">
			<img src="<?php echo $screenshoturl; ?>" /><br /><br />
		</div>
		<div class="span5">
	<?php } else { ?>
		<div class="span12">
	<?php } ?>
			<div class="widget">
				<h2>
					<div class="pull-right guildlistdiv">
						<div class="input-prepend nomargin">
							<span class="add-on"><i class="icon-list"></i></span><select id="sort" class="comments">
								<option value='new'>New First</option>
								<option value='old'>Old First</option>
								<option value='best'>Best First</option>
							</select>
						</div>
						<a id="addComment" class="btn btn-success"><i class="icon-plus icon-white"></i> Comment</a>
					</div>
					Comments
				</h2>				
				<div class="widget-inner">
					<form id="commentForm" method="post" action="<?php echo $rootpath; ?>/ajax/submitComment.php" class="well">
						<fieldset>
							<legend>Add Comment</legend>
							<div class="control-group">
								<label class="control-label" for="commentcontent">Comment:</label>
								<div class="controls">
									<textarea rows="3" id="textarea" name="commentcontent" class="required input-xlarge"></textarea>
									<input type="hidden" id="postid" name="postid" value="<?php echo $thispostid; ?>" />
									<p class="help-block">140 characters remaining.</p>
								</div>
							</div>
							<div class="form-actions">								
								<input type="submit" class="btn btn-primary" value="Submit" />
								<a class="cancel btn">Cancel</a>
								<div class="errors"></div>
							</div>
						</fieldset>
					</form>
					<div id="comments">
					<?php
						// $result2 = QueryWS1("SELECT PPComments.*, PlayerPortalLogin.DisplayAlias FROM PPComments INNER JOIN PlayerPortalLogin ON PPComments.PPLoginID = PlayerPortalLogin.PPLoginID WHERE PostID = $thispostid ORDER BY UpdateDT DESC");
						
						// if (Num_Rows($result2) > 0 ) {
							// for ( $i = 0; $i < Num_Rows($result2); $i++ ) {
								// $postid = Result($result2, $i, "PostID");
								// $commentid = Result($result2, $i, "CommentID");
								// $pploginid = Result($result2, $i, "PPLoginID");
								// $displayalias = Result($result2, $i, "DisplayAlias");
								// $commentcontent = Result($result2, $i, "CommentContent");
								// $commentrating = Result($result2, $i, "CommentRating");
								// $timestamp = getRelativeTime(Result($result2, $i, "UpdateDT"));
								
								// $class = "";
								
								// if (isset($_SESSION['user_id'])) {
									// $result3 = QueryWS1("SELECT Vote 
										// FROM PPCommentVotes
										// WHERE CommentID = $commentid AND PPLoginID = $user_id");
										
									// if (Num_Rows($result3) > 0 ) {
										// list($vote) = mssql_fetch_row($result3);
										// if ($vote == 0) {	
											// $class = "down";
										// } else if ($vote == 1) {
											// $class = "up";
										// }
									// } else {
										// $class = "";
									// }
								// }
								
								// if ($commentrating <= -5) {
									// $commentcontent = "<em>This comment has been hidden due to excessive downvotes.</em>";
								// }
								
								// echo "<div class='commentitem' id='$commentid'>";		
									// echo "<div class='voting $class'>";
										// if (isset($_SESSION['user_id'])) {
											// echo "<a class='upvote' href='#'><i class='icon-thumbs-up'></i></a>";
										// } else {
											// echo "<a class='upvote itemhide' href='#'><i class='icon-thumbs-up'></i></a>";
										// }
										// echo "<div>$commentrating</div>";
										// if (isset($_SESSION['user_id'])) {
											// echo "<a class='downvote' href='#'><i class='icon-thumbs-down'></i></a>";
										// } else {
											// echo "<a class='downvote itemhide' href='#'><i class='icon-thumbs-down'></i></a>";
										// }
									// echo "</div>";		
									// echo "$commentcontent<br /><br />";
									// echo "<small>Posted <em>$timestamp</em> by <a href='$rootpath/userprofile.php?username=$displayalias'>$displayalias</a>";
									// if (isset($_SESSION['user_id'])) {
										// if ($pploginid == $user_id || checkAdmin() || checkMod()) {
											// echo " | <a class='deletecomment' href='#'>Delete</a>";
										// }
									// }
								// echo "</small></div>";
							// }
						// }
					?>
					</div>
				</div>
			</div>			
			<?php
			if (isset($_SESSION['user_id'])) {
				if (checkAdmin()) {
					$result13 = QueryWS1("SELECT v.*, p.DisplayAlias FROM PPPostVotes v INNER JOIN PlayerPortalLogin p on p.PPLoginID = v.PPLoginID  WHERE v.PostID = $thispostid");						
					echo "<div class='widget'><h2>
						<div class='pull-right guildlistdiv'>
							<a href='#' class='collapse'><i class='icon-chevron-down icon-white'></i></a>
						</div>
						Post Votes (Admin Only)
					</h2>
					<div class='widget-inner' style='display: none;'>";
					
					for ( $i = 0; $i < Num_Rows($result13); $i++ ) {
						$vote = Result($result13, $i, "Vote");
						$user = Result($result13, $i, "DisplayAlias");
						
						if ($vote == 1) {
							$vote = "Up";
						} else {
							$vote = "Down";
						}
						
						echo "$user voted $vote<br />";
					}
					
					echo "</div></div>";
				}
			}
			?>			
		</div>
	</div>
<?php endblock() ?>