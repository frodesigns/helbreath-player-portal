<?php
	$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	$rootpath = "/portal";
	
	include("$serverrootpath/includes/db.inc.php");
	include("$includepath/common.php");
	include("$includepath/itemdecode.php");
	include("$includepath/items.inc.php");
	
	user_protect();
	if (isset($_SESSION['user_id'])) {
		$user_id = $_SESSION['user_id'];
	}
	
	$username = str_replace("'", "", trim($_GET['username']));
	
	$result = QueryWS1("SELECT PPLoginID FROM PlayerPortalLogin WHERE DisplayAlias = '$username'");
	list($pploginid) = mssql_fetch_row($result);
	
	$result = QueryWS1("SELECT AccountName, DisplayAlias FROM PlayerPortalLinkedAccount WHERE PPLoginID = '$pploginid' ORDER BY DisplayAlias ASC");
	
	//keep at bottom
	include "$includepath/base.php";
?>

<?php startblock('title') ?>
	<?php echo $username; ?>'s Profile
<?php endblock() ?>

<?php startblock('header') ?>
	<?php echo $username; ?>'s Profile
<?php endblock() ?>

<?php startblock('additionalScripts') ?>

<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li><a href="<?php echo $rootpath; ?>/members.php">Members</a></li> <span class="divider">/</span>
	<li class="active"><?php echo $username; ?>'s Profile</a>
<?php endblock() ?>

<?php startblock('containerContent') ?>
	<?php
		if (Num_Rows($result) == 0) {
			echo "<h3>There seems to be nothing here.</h3>";
		} else {
	?>
	<div class="row-fluid">
		<div class="span6">
			<div class='widget'>
				<h2>
					<div class="pull-right guildlistdiv">
						<a href="#" class="collapse"><i class="icon-chevron-up icon-white"></i></a>
					</div>
					Recent Posts
				</h2>
				<div class='widget-inner'>
					<div id="posts">
					<?php
						$result7 = QueryWS1("SELECT TOP 20 *
							FROM PPPosts 
							WHERE PPLoginID = $pploginid
							ORDER BY UpdateDT DESC");
						
						if (Num_Rows($result7) > 0 ) {
							for ( $i = 0; $i < Num_Rows($result7); $i++ ) {
								$postid = Result($result7, $i, "PostID");
								$pploginid = Result($result7, $i, "PPLoginID");
								$postcontent = Result($result7, $i, "PostContent");
								$postrating = Result($result7, $i, "PostRating");
								$screenshoturl = Result($result7, $i, "ScreenshotUrl");
								$commentcount = Result($result7, $i, "CommentCount");
								$timestamp = getRelativeTime(Result($result7, $i, "UpdateDT"));
								
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
									echo "<small>Posted <em>$timestamp</em> | <a href='$rootpath/post.php?postid=$postid'><span class='badge badge-info'>$commentcount Comment$s</span></a>";
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
			
			<div class='widget'>
				<h2>
					<div class="pull-right guildlistdiv">
						<a href="#" class="collapse"><i class="icon-chevron-up icon-white"></i></a>
					</div>
					Recent Comments
				</h2>
				<div class='widget-inner'>
					<div id="comments">
					<?php
						$result20 = QueryWS1("SELECT TOP 10 PPComments.*, PlayerPortalLogin.DisplayAlias, PPPosts.PostContent 
							FROM PPComments 
							INNER JOIN PlayerPortalLogin ON PPComments.PPLoginID = PlayerPortalLogin.PPLoginID 
							INNER JOIN PPPosts ON PPPosts.PostID = PPComments.PostID
							WHERE PPComments.PPLoginID = $pploginid
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
								
								if ($commentrating <= -10) {
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
									echo "<small>Posted <em>$timestamp</em> in <a href='$rootpath/post.php?postid=$postid'>$shortcontent</a>";
									if (isset($_SESSION['user_id'])) {
										if ($pploginid == $user_id || checkAdmin() || checkMod()) {
											echo " | <a class='deletecomment' href='#'>Delete</a>";
										}
									}
								echo "</small></div>";
							}
						} else {
							echo "No comments found.";
						}
					?>
					</div>
				</div>
			</div>
		</div>
		<div class="span6">
			<div class='widget'>
				<h2>
					<div class="pull-right guildlistdiv">
						<a href="#" class="collapse"><i class="icon-chevron-up icon-white"></i></a>
					</div>
					Trade List
				</h2>
				<div class='widget-inner'>
				<?php
					$result8 = QueryWS1("SELECT t.* 
						FROM PPTradeItems t 
						WHERE t.PPLoginID = $pploginid 
						ORDER BY t.UpdateDT DESC");
						
					if (Num_Rows($result8) > 0 ) {
						echo "<table id='tradeItemTable' class='table table-bordered table-striped table-condensed sortable'>";
						echo "<thead>
							<tr>
								<th>Item</th>
								<th>Main Stat</th>
								<th>Sub Stat</th>
							</tr>
						</thead>
						<tbody>";
						
						for ( $i = 0; $i < Num_Rows($result8); $i++ ) {
							$sItemID = Result($result8, $i, "sItemID");
							$sID1 = Result($result8, $i, "sID1");
							$sID2 = Result($result8, $i, "sID2");
							$sID3 = Result($result8, $i, "sID3");
							$PPLoginID = Result($result8, $i, "PPLoginID");
							
							$result9 = QueryWS1("SELECT * FROM ITEM_T WHERE sItemID = $sItemID AND sID1 = $sID1 AND sID2 = $sID2 AND sID3 = $sID3");
							
							if (Num_Rows($result9) > 0 ) {
							
								for ( $j = 0; $j < Num_Rows($result9); $j++ ) {		
									$sEffect2 = Result($result9, $j, "sEffect2");
									$iCount = Result($result9, $j, "iCount");
									$iAttribute = Result($result9, $j, "iAttribute");
									$sItemType = Result($result9, $j, "sItemType");
								
									if ($iCount > 1) {
										$qty = " (" . number_format($iCount) . ")";
									} else {
										$qty = "";
									}
									
									if ($sItemID >= 881 && $sItemID <= 884) {
										$completion = " " . $sEffect2 . "%";
									} else {
										$completion = "";
									}
									
									$tablettype = "";
									if ($sItemID == 867) {
										if ($sEffect2 == 1) {
											$tablettype = "Health";
										} else if ($sEffect2 == 2) {
											$tablettype = "Berserk";
										} else if ($sEffect2 == 3) {
											$tablettype = "Mana";
										} else if ($sEffect2 == 4) {
											$tablettype = "Experience";
										}
									}

									$itemname = $itemarray[$sItemID];
									if (array_key_exists($itemname, $itemnamearray)) {
										$realitemname = trim($itemnamearray[$itemname]);
									} else {
										$realitemname = $itemname;
									}						
									
									$stats = getItemStats($iAttribute, $sItemType, $sEffect2, $iCount);
									
									$plusvalue = $stats['plusvalue'];
									if ($sItemID >= 908 && $sItemID <= 911) {
										$plusvalue = "+" . $sEffect2;
									}
									
									echo "<tr data-sItemID='$sItemID' data-sID1='$sID1' data-sID2='$sID2' data-sID3='$sID3' data-attribute='$iAttribute' class='$stats[class]'><td>$realitemname$plusvalue$completion$qty</td><td>$stats[mainstat]$tablettype$stats[mainstatpercent]</td><td>$stats[substat] $stats[substatpercent]</td></tr>";						
								}
							
							} else {
							
								$result10 = QueryWS1("SELECT * FROM BANKITEM_T WHERE sItemID = $sItemID AND sID1 = $sID1 AND sID2 = $sID2 AND sID3 = $sID3");
								
								if (Num_Rows($result10) > 0 ) {
						
									for ( $j = 0; $j < Num_Rows($result10); $j++ ) {		
										$sEffect2 = Result($result10, $j, "sEffect2");
										$iCount = Result($result10, $j, "iCount");
										$iAttribute = Result($result10, $j, "iAttribute");
										$sItemType = Result($result10, $j, "sItemType");
									
										if ($iCount > 1) {
											$qty = " (" . number_format($iCount) . ")";
										} else {
											$qty = "";
										}
										
										if ($sItemID >= 881 && $sItemID <= 884) {
											$completion = " " . $sEffect2 . "%";
										} else {
											$completion = "";
										}
										
										$tablettype = "";
										if ($sItemID == 867) {
											if ($sEffect2 == 1) {
												$tablettype = "Health";
											} else if ($sEffect2 == 2) {
												$tablettype = "Berserk";
											} else if ($sEffect2 == 3) {
												$tablettype = "Mana";
											} else if ($sEffect2 == 4) {
												$tablettype = "Experience";
											}
										}

										$itemname = $itemarray[$sItemID];
										if (array_key_exists($itemname, $itemnamearray)) {
											$realitemname = trim($itemnamearray[$itemname]);
										} else {
											$realitemname = $itemname;
										}						
										
										$stats = getItemStats($iAttribute, $sItemType, $sEffect2, $iCount);
										
										$plusvalue = $stats['plusvalue'];
										if ($sItemID >= 908 && $sItemID <= 911) {
											$plusvalue = "+" . $sEffect2;
										}
										
										echo "<tr data-sItemID='$sItemID' data-sID1='$sID1' data-sID2='$sID2' data-sID3='$sID3' data-attribute='$iAttribute' class='$stats[class]'><td>$realitemname$plusvalue$completion$qty</td><td>$stats[mainstat]$tablettype$stats[mainstatpercent]</td><td>$stats[substat] $stats[substatpercent]</td></tr>";						
									}
								
								}
							
							}
						}
						
						echo "</tbody></table>";
					} else {
						echo "No items found.";
					}
				?>
				</div>
			</div>
			
			<?php
				$totalquests = 0;
				$totaleks = 0;
				$totalekshero = 0;
				
				echo "<div class='widget'>";
				echo "<h2><div class='pull-right guildlistdiv'><a href='#' class='collapse'><i class='icon-chevron-up icon-white'></i></a></div>Trophies</h2>";
				echo "<div class='widget-inner'>";
				echo "<div class='hero'>";
				
				for ( $i = 0; $i < Num_Rows($result); $i++ ) {
					$AccountName = Result($result, $i, "AccountName");
					$DisplayAlias = Result($result, $i, "DisplayAlias");
					
					$result2 = QueryWS1("SELECT TOP 4 cCharName, CharID, sLevel, bGender, iExp, cNation, cMapLoc, cGuildName, sGuildRank, sStr, sDex, sVit, sMag, sInt, sChar, iGizoneItemUpgradeLeft, iEK, iPopular, sID1, sID2, sID3 FROM CHARACTER_T WHERE cAccountID = '$AccountName' ORDER BY cCharName ASC, FileSaveDate DESC");
					
					if (Num_Rows($result2) > 0 ) {
						for ( $j = 0; $j < Num_Rows($result2); $j++ ) {
							$charname = Result($result2, $j, "cCharName");
							$charid = Result($result2, $j, "CharID");
							$charsID1 = Result($result2, $j, "sID1");
							$charsID2 = Result($result2, $j, "sID2");
							$charsID3 = Result($result2, $j, "sID3");
							$eks = Result($result2, $j, "iEK");
							
							$totaleks += $eks;
							$totalekshero += $eks;
							
							$result3 = QueryWS1("SELECT TotalContribs FROM CharacterContribs_vw WHERE CharID = $charid");	
							
							$questscompleted = Result($result3, 0, "TotalContribs");
							$totalquests += $questscompleted;
							
							$result4 = QueryWS1("SELECT * FROM ITEM_T WHERE CharID = $charid AND sID1 = $charsID1 AND sID2 = $charsID2 AND sID3 = $charsID3 AND (sItemID >= 400 AND sItemID <= 428 AND sItemID <> 402 OR sItemID >= 745 AND sItemID <= 746)");
		
							$result5 = QueryWS1("SELECT * FROM BANKITEM_T WHERE CharID = $charid AND sID1 = $charsID1 AND sID2 = $charsID2 AND sID3 = $charsID3 AND (sItemID >= 400 AND sItemID <= 428 AND sItemID <> 402 OR sItemID >= 745 AND sItemID <= 746)");
							
							$result6 = QueryWS1("SELECT sSkillID, sSkillMastery, iSkillSSN FROM SKILL_T WHERE CharID = '$charid' AND sSkillMastery = 100 AND (iSkillSSN <> '' OR sSkillID = 13) AND (sSkillID = 0 OR sSkillID = 1 OR sSkillID = 2 OR sSkillID = 12 OR sSkillID = 13)");							

							for ( $k = 0; $k < Num_Rows($result4); $k++ ) {
								$sItemID = Result($result4, $k, "sItemID");
								$iAttribute = Result($result4, $k, "iAttribute");
								$sItemType = Result($result4, $k, "sItemType");
								$sEffect2 = Result($result4, $k, "sEffect2");
								$iCount = Result($result4, $k, "iCount");
								
								$itemname = $itemarray[$sItemID];
								$realitemname = trim($itemnamearray[$itemname]);
								
								$stats = getItemStats($iAttribute, $sItemType, $sEffect2, $iCount);
								
								if ($sItemID >= 745 && $sItemID <= 746) {
									$plusvalue = $stats['plusvalue'];
									$realitemname .= $plusvalue;
									
									if ($plusvalue == 2) {
										$maj = 2;
									} else if ($plusvalue == 4) {
										$maj = 6;
									} else if ($plusvalue == 6) {
										$maj = 13;
									} else if ($plusvalue == 8) {
										$maj = 24;
									} else if ($plusvalue == 10) {
										$maj = 40;
									} else if ($plusvalue == 12) {
										$maj = 62;
									} else if ($plusvalue == 14) {
										$maj = 91;
									} else if ($plusvalue == 15) {
										$maj = 128;
										$sItemID .= ".15";
									}

									$cost = "$maj Maj";
								} else {
									$ekcost = getHeroEKCost($sItemID);
									$totalekshero += $ekcost;
									$cost = $ekcost . " EKs";
								}
								
								echo "<div class='heropiece'><img rel='tooltip' title='$realitemname - $cost' src='$rootpath/img/item-bag-images/$sItemID.png' alt='$realitemname' /></div>";
							}
							
							for ( $k = 0; $k < Num_Rows($result5); $k++ ) {
								$sItemID = Result($result5, $k, "sItemID");
								$iAttribute = Result($result5, $k, "iAttribute");
								$sItemType = Result($result5, $k, "sItemType");
								$sEffect2 = Result($result5, $k, "sEffect2");
								$iCount = Result($result5, $k, "iCount");
								
								$itemname = $itemarray[$sItemID];
								$realitemname = trim($itemnamearray[$itemname]);
								
								$stats = getItemStats($iAttribute, $sItemType, $sEffect2, $iCount);
								
								if ($sItemID >= 745 && $sItemID <= 746) {
									$plusvalue = $stats['plusvalue'];
									$realitemname .= $plusvalue;
									
									if ($plusvalue == 2) {
										$maj = 2;
									} else if ($plusvalue == 4) {
										$maj = 6;
									} else if ($plusvalue == 6) {
										$maj = 13;
									} else if ($plusvalue == 8) {
										$maj = 24;
									} else if ($plusvalue == 10) {
										$maj = 40;
									} else if ($plusvalue == 12) {
										$maj = 62;
									} else if ($plusvalue == 14) {
										$maj = 91;
									} else if ($plusvalue == 15) {
										$maj = 128;
										$sItemID .= ".15";
									}

									$cost = "$maj Maj";
								} else {
									$ekcost = getHeroEKCost($sItemID);
									$totalekshero += $ekcost;
									$cost = $ekcost . " EKs";
								}
								
								echo "<div class='heropiece'><img rel='tooltip' title='$realitemname - $cost' src='$rootpath/img/item-bag-images/$sItemID.png' alt='$realitemname' /></div>";
							}
							
							for ( $k = 0; $k < Num_Rows($result6); $k++ ) {
								$skillid = Result($result6, $k, "sSkillID");
								$skillmastery = Result($result6, $k, "sSkillMastery");
								$skill = getSkillName($skillid);
								
								//mining 0, fishing 1, farming 2, alch 12, manu 13
								if ($skillid == 0) {
									$itemid = 231;
								} else if ($skillid == 1) {
									$itemid = 105;
								} else if ($skillid == 2) {
									$itemid = 232;
								} else if ($skillid == 12) {
									$itemid = 227;
								} else if ($skillid == 13) {
									$itemid = 236;
								}
								
								echo "<div class='heropiece'><img rel='tooltip' title='100% $skill' src='$rootpath/img/item-bag-images/$itemid.png' alt='100% $skill' /></div>";
							}							
						}
					}					
				}
				
				$postkarma = 0;
				$commentkarma = 0;
				
				$result20 = QueryWS1("SELECT PostRating FROM PPPosts WHERE PPLoginID = $pploginid");
				
				for ( $i = 0; $i < Num_Rows($result20); $i++ ) {
					$postrating = Result($result20, $i, "PostRating");
					
					$postkarma += $postrating;
				}
				
				$result21 = QueryWS1("SELECT CommentRating FROM PPComments WHERE PPLoginID = $pploginid");
				
				for ( $i = 0; $i < Num_Rows($result21); $i++ ) {
					$commentrating = Result($result21, $i, "CommentRating");
					
					$commentkarma += $commentrating;
				}
				
				echo "</div></div></div>";
				echo "<div class='widget'>";
					echo "<h2><div class='pull-right guildlistdiv'><a href='#' class='collapse'><i class='icon-chevron-up icon-white'></i></a></div>Statistics</h2>";
					echo "<div class='widget-inner'>";
						echo "<strong>Post Karma:</strong> $postkarma<br />";
						echo "<strong>Comment Karma:</strong> $commentkarma<br /><br />";
						//echo "<strong>Total Quests Completed:</strong> $totalquests<br />";
						echo "<strong>Total Enemy Kills:</strong> $totaleks<br />";
						echo "<strong>Total EKs Including Hero:</strong> $totalekshero<br />";
					echo "</div>";
				echo "</div>";
			?>

		</div>		
	</div>
	<?php } ?>
<?php endblock() ?>