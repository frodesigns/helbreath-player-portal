<?php
	$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	$rootpath = "/portal";
	
	include("$serverrootpath/includes/db.inc.php");
	include("$includepath/common.php");
	
	page_protect();
	
	$user_id = $_SESSION['user_id'];
	
	$result = QueryWS1("SELECT AccountName, DisplayAlias FROM PlayerPortalLinkedAccount WHERE PPLoginID = '$user_id' ORDER BY DisplayAlias ASC");
	
	$result0 = QueryWS1("SELECT DisplayAlias FROM PlayerPortalLogin WHERE Approved = 1 ORDER BY DisplayAlias ASC");
	
	$membersarray = array();
	
	for ( $i = 0; $i < Num_Rows($result0); $i++ ) {
		$user = Result($result0, $i, "DisplayAlias");
		
		array_push($membersarray, "$user");
	}
	
	//keep at bottom
	include "$includepath/base.php";
?>

<?php startblock('title') ?>
	Home
<?php endblock() ?>

<?php startblock('header') ?>
	Home
<?php endblock() ?>

<?php startblock('additionalScripts') ?>
	<script type="text/javascript" src="<?php echo $rootpath; ?>/js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="<?php echo $rootpath; ?>/js/jquery.form.js"></script>
	<script type="text/javascript" src="<?php echo $rootpath; ?>/js/index.js"></script>
	<!--[if gte IE 9]>
	<style type="text/css">
		.ares, .elv {
		   filter: none;
		}
	</style>
	<![endif]-->
<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li class="active">Home</li>
<?php endblock() ?>

<?php startblock('containerContent') ?>
	<div id="dashboard" class="row-fluid">
		<div class="span6">
			<div class="widget">
				<h2>
					<div class="pull-right guildlistdiv">
						<a id="createPost" class="btn btn-success"><i class="icon-plus icon-white"></i> Post</a>
						<a href="#" class="collapse"><i class="icon-chevron-up icon-white"></i></a>
					</div>
					Recent Posts
				</h2>
				<div class="widget-inner">
					<form id="postForm" enctype="multipart/form-data" method="post" action="<?php echo $rootpath; ?>/ajax/submitPost.php" class="well">
						<fieldset>
							<legend>Create New Post</legend>
							<div class="control-group">
								<label class="control-label" for="loginEmail">Post Content:</label>
								<div class="controls">
									<textarea rows="3" id="textarea" name="postcontent" class="required input-xlarge"></textarea>
									<p class="help-block">140 characters remaining.</p>
									<!--<div>
										<a id="tag-user" class="btn btn-inverse" href="#"><i class="icon-user icon-white"></i> Tag Someone</a> 
										<input id="tag-input" data-provide="typeahead" data-source='<?php echo json_encode($membersarray); ?>' type="text" style="display: none;" />
										<a id="add-tag" class="btn btn-inverse" href="#" style="display: none;"><i class="icon-plus icon-white"></i> Add Tag</a> 
									</div>-->
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="loginEmail">Screenshot:</label>
								<div class="controls">
									<div class="input-prepend nomargin">
										<span class="add-on"><i class="icon-picture"></i></span><input class="browse" type="text" /> <a class="browse btn">Browse...</a>
										<input id="file" name="file" class="input-xlarge" type="file" />
									</div>
									<p class="help-block">Optional. Only screenshots taken by the game client are allowed. Do not rename the file.</p>
								</div>
							</div>
							<div class="form-actions">								
								<input type="submit" class="btn btn-primary" value="Submit" />
								<a class="cancel btn">Cancel</a>
								<div class="errors"></div>
							</div>
						</fieldset>
					</form>
					<div id="posts">
						<?php
							$result11 = QueryWS1("SELECT TOP 10 PPPosts.*, PlayerPortalLogin.DisplayAlias
								FROM PPPosts 
								INNER JOIN PlayerPortalLogin ON PPPosts.PPLoginID = PlayerPortalLogin.PPLoginID 
								ORDER BY PPPosts.UpdateDT DESC");
										
							if (Num_Rows($result11) > 0 ) {
								for ( $i = 0; $i < Num_Rows($result11); $i++ ) {
									$postid = Result($result11, $i, "PostID");
									$pploginid = Result($result11, $i, "PPLoginID");
									$displayalias = Result($result11, $i, "DisplayAlias");
									$postcontent = Result($result11, $i, "PostContent");
									$postrating = Result($result11, $i, "PostRating");
									$screenshoturl = Result($result11, $i, "ScreenshotUrl");
									$commentcount = Result($result11, $i, "CommentCount");
									$timestamp = getRelativeTime(Result($result11, $i, "UpdateDT"));
									
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
						?>
					</div>
				</div>
			</div>
			
			<div class="widget">
				<h2>
					<div class="pull-right guildlistdiv">
						<a href="#" class="collapse"><i class="icon-chevron-up icon-white"></i></a>
					</div>
					Recent Comments
				</h2>
				<div class="widget-inner">
					<div id="comments">
						<?php
						$result20 = QueryWS1("SELECT TOP 10 PPComments.*, PlayerPortalLogin.DisplayAlias, PPPosts.PostContent 
							FROM PPComments 
							INNER JOIN PlayerPortalLogin ON PPComments.PPLoginID = PlayerPortalLogin.PPLoginID 
							INNER JOIN PPPosts ON PPPosts.PostID = PPComments.PostID
							ORDER BY PPComments.UpdateDT DESC");
						
						if (Num_Rows($result20) > 0 ) {
							for ( $i = 0; $i < Num_Rows($result20); $i++ ) {
								$postid = Result($result20, $i, "PostID");
								$commentid = Result($result20, $i, "CommentID");
								$pploginid = Result($result20, $i, "PPLoginID");
								$displayalias = Result($result20, $i, "DisplayAlias");
								$commentcontent = Result($result20, $i, "CommentContent");
								$commentrating = Result($result20, $i, "CommentRating");
								$postcontent = Result($result20, $i, "PostContent");
								$timestamp = getRelativeTime(Result($result20, $i, "UpdateDT"));
								
								$maxlen = 26;
								if (strlen($postcontent) > $maxlen) {
									$shortcontent = preg_replace('/\s+?(\S+)?$/', '', substr($postcontent, 0, $maxlen)) . "...";
								} else {
									$shortcontent = $postcontent;
								}
								
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
									echo "<small>Posted <em>$timestamp</em> by <a href='$rootpath/userprofile.php?username=$displayalias'>$displayalias</a> in <a href='$rootpath/post.php?postid=$postid'>$shortcontent</a>";
									if (isset($_SESSION['user_id'])) {
										if ($pploginid == $user_id || checkAdmin() || checkMod()) {
											echo " | <a class='deletecomment' href='#'>Delete</a>";
										}
									}
								echo "</small></div>";
							}
						}
						?>
					</div>
				</div>
			</div>
		</div>
		
		<div class="span6">
			<div class="widget">
				<h2>
					<div class="pull-right guildlistdiv">
						<a href="#" class="collapse"><i class="icon-chevron-up icon-white"></i></a>
					</div>
					Characters
				</h2>
				<div class="widget-inner">
				<?php
					if (Num_Rows($result) == 0) {
						echo "<h3>There seems to be nothing here.</h3>";
						echo "<a href='$rootpath/profile.php'>Add some game accounts!</a>";
					} else {
						$totalquests = 0;
						$totalmaj = 0;
						$totalexp = 0;
						$totaleks = 0;
						$totalheroeks = 0;
						$totalrep = 0;
						$totalgold = 0;
						$totalportal = 0;
						$totalrejuv = 0;
						$totalwild = 0;
						$totalmyst = 0;
						
						for ( $i = 0; $i < Num_Rows($result); $i++ ) {
							$AccountName = Result($result, $i, "AccountName");
							$DisplayAlias = Result($result, $i, "DisplayAlias");
							
							$result2 = QueryWS1("SELECT TOP 4 cCharName, CharID, sLevel, bGender, iExp, cNation, cMapLoc, cGuildName, sGuildRank, sStr, sDex, sVit, sMag, sInt, sChar, iGizoneItemUpgradeLeft, iEK, iPopular FROM CHARACTER_T WHERE cAccountID = '$AccountName' ORDER BY FileSaveDate DESC");			
							
							echo "<h3>$DisplayAlias ($AccountName)</h3>";
							
							if (Num_Rows($result2) > 0 ) {
								for ( $j = 0; $j < Num_Rows($result2); $j++ ) {
									$charname = Result($result2, $j, "cCharName");
									$charid = Result($result2, $j, "CharID");
									$level = Result($result2, $j, "sLevel");
									$gender = Result($result2, $j, "bGender");
									$exp = Result($result2, $j, "iExp");
									$nation = Result($result2, $j, "cNation");
									$location = Result($result2, $j, "cMapLoc");
									$guild = Result($result2, $j, "cGuildName");
									$guildrank = Result($result2, $j, "sGuildRank");
									$str = Result($result2, $j, "sStr");
									$dex = Result($result2, $j, "sDex");
									$vit = Result($result2, $j, "sVit");
									$int = Result($result2, $j, "sInt");
									$mag = Result($result2, $j, "sMag");
									$cha = Result($result2, $j, "sChar");
									
									$maj = Result($result2, $j, "iGizoneItemUpgradeLeft");
									$eks = Result($result2, $j, "iEK");
									$rep = Result($result2, $j, "iPopular");
									
									$totalmaj += $maj;
									$totalexp += $exp;
									$totaleks += $eks;
									$totalheroeks += $eks;
									$totalrep += $rep;
									
									//select by needed item ids
									//gold = 90, portal = 856, rejuv = 964, wild = 748, myst = 927
									$result3 = QueryWS1("SELECT * FROM ITEM_T WHERE CharID = $charid AND sItemID > 0 AND (sItemID = 90 OR sItemID = 856 OR sItemID = 964 OR sItemID = 748 OR sItemID = 927 OR (sItemID >= 400 AND sItemID <= 428 AND sItemID <> 402))");
									
									for ( $k = 0; $k < Num_Rows($result3); $k++ ) {
										$sItemID = Result($result3, $k, "sItemID");
										$iCount = Result($result3, $k, "iCount");
										$sID1 = Result($result3, $k, "sID1");
										$sID2 = Result($result3, $k, "sID2");
										$sID3 = Result($result3, $k, "sID3");
										
										if ($sItemID == 90) {
											$totalgold += $iCount;
										} else if ($sItemID == 856) {
											$totalportal += $iCount;
										} else if ($sItemID == 964) {
											$totalrejuv += $iCount;
										} else if ($sItemID == 748) {
											$totalwild += $iCount;
										} else if ($sItemID == 927) {
											$totalmyst += $iCount;
										} else if ($sItemID >= 400 && $sItemID <= 428) {
											$ekcost = getHeroEKCost($sItemID);
											$totalheroeks += $ekcost;
										}
									}
		
									$result4 = QueryWS1("SELECT * FROM BANKITEM_T WHERE CharID = $charid AND sItemID > 0 AND (sItemID = 90 OR sItemID = 856 OR sItemID = 964 OR sItemID = 748 OR sItemID = 927 OR (sItemID >= 400 AND sItemID <= 428 AND sItemID <> 402))");
									
									for ( $k = 0; $k < Num_Rows($result4); $k++ ) {
										$sItemID = Result($result4, $k, "sItemID");
										$iCount = Result($result4, $k, "iCount");
										$sID1 = Result($result4, $k, "sID1");
										$sID2 = Result($result4, $k, "sID2");
										$sID3 = Result($result4, $k, "sID3");
										
										if ($sItemID == 90) {
											$totalgold += $iCount;
										} else if ($sItemID == 856) {
											$totalportal += $iCount;
										} else if ($sItemID == 964) {
											$totalrejuv += $iCount;
										} else if ($sItemID == 748) {
											$totalwild += $iCount;
										} else if ($sItemID == 927) {
											$totalmyst += $iCount;
										} else if ($sItemID >= 400 && $sItemID <= 428) {
											$ekcost = getHeroEKCost($sItemID);
											$totalheroeks += $ekcost;
										}
									}
									
									$result5 = QueryWS1("SELECT TotalContribs FROM CharacterContribs_vw WHERE CharID = $charid");	
									
									$questscompleted = Result($result5, 0, "TotalContribs");
									$totalquests += $questscompleted;
									
									$chartype = charType($str, $dex, $int, $mag, $vit, $cha);
									$nation = getNation($nation);
									$guild = getGuild($guild, $guildrank);
									
									if (strpos($nation,'Aresden') !== false) {
										$class = "ares";
									} else if (strpos($nation,'Elvine') !== false) {
										$class = "elv";
									} else {
										$class = "trav";
									}
									
									echo "<div class='dashboardCharacter $class'>";
										echo "<ul class='pull-right'>";
											echo "<li><a href='$rootpath/charstats.php?charname=$charname' rel='tooltip' title='Character Stats'><i class='icon-signal icon-white'></i></a></li>";
											echo "<li><a href='$rootpath/inventory.php?charname=$charname' rel='tooltip' title='Inventory'><i class='icon-th icon-white'></i></a></li>";
											echo "<li><a href='$rootpath/quests.php?charname=$charname' rel='tooltip' title='Saved Quests'><i class='icon-book icon-white'></i></a></li>";
											echo "<li><a href='$rootpath/questscompleted.php?charname=$charname' rel='tooltip' title='Completed Quests'><i class='icon-ok icon-white'></i></a></li>";
											echo "<li><a href='$rootpath/ekassists.php?charname=$charname' rel='tooltip' title='EK Assists'><i class='icon-fire icon-white'></i></a></li>";
										echo "</ul>";
										if ($class != "trav") {
											echo "<img src='$rootpath/img/$class-symbol-small.png' />";
										}
										echo "<h4>$charname</h4>";
									echo "</div>";
								}
							} else {
								echo "No characters found on this account.";								
							}
						}
					}
				?>
				</div>
			</div>
			
			<div class="widget">
				<h2>
					<div class="pull-right guildlistdiv">
						<a href="#" class="collapse"><i class="icon-chevron-up icon-white"></i></a>
					</div>
					Combined Statistics
				</h2>
				<div class="widget-inner">
					<div class="row-fluid">
						<div class="span6">
						<?php
							echo "<h3>PvM:</h3>";					
							echo "<strong>Total Majestic Points:</strong> $totalmaj<br />";
							echo "<strong>Total Quests Completed:</strong> $totalquests<br />";
							echo "<strong>Total Experience:</strong> " . number_format($totalexp) . "<br />";					
							echo "<h3>PvP:</h3>";
							echo "<strong>Total Enemy Kills:</strong> $totaleks<br />";
							echo "<strong>Total EKs Including Hero:</strong> $totalheroeks<br />";
							echo "<strong>Total Reputation:</strong> $totalrep";
						?>
						</div>
						<div class="span6">
						<?php
							echo "<h3>Wealth:</h3>";
							echo "<strong>Total Gold:</strong> " . number_format($totalgold) . "<br />";
							echo "<strong>Total Portal Tokens:</strong> $totalportal<br />";
							echo "<strong>Total Rejuvenation Emblems:</strong> $totalrejuv<br />";
							echo "<strong>Total Wild Scavenger Tokens:</strong> $totalwild<br />";
							echo "<strong>Total Mysterious Tokens:</strong> $totalmyst";
						?>
						</div>
					</div>
				</div>
			</div>
			
			<div class="widget">
				<h2>
					<div class="pull-right guildlistdiv">						
						<div class="input-prepend nomargin">
							<span class="add-on"><i class="icon-eye-open"></i></span><select class="guildList">
								<?php 
									$guilds = getGuilds($user_id);
									
									$g = 0;
									foreach ($guilds as $guildid => $guildname) {
										$g++;
										if ($g == 1) {
											echo "<option value='$guildid' selected>$guildname</option>";
										} else {
											echo "<option value='$guildid'>$guildname</option>";
										}
									}
								?>
							</select>
						</div>
						<a href="#" class="collapse"><i class="icon-chevron-up icon-white"></i></a>
					</div>
					Guild Info			
				</h2>
				<div id="guildInfo" class="widget-inner">
				<?php
					
				?>
				</div>
			</div>
		</div>
	</div>
<?php endblock() ?>