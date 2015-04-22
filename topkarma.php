<?php
	$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	$rootpath = "/portal";
	
	include("$serverrootpath/includes/db.inc.php");
	include("$includepath/common.php");
	
	user_protect();
	
	$result = QueryWS1("SELECT TOP 100 a.DisplayAlias, (SELECT SUM(b.PostRating) FROM PPPosts b WHERE b.PPLoginID = a.PPLoginID) AS PostKarma FROM PlayerPortalLogin a WHERE a.Approved = 1 ORDER BY PostKarma DESC, RegDate ASC");
	
	$result2 = QueryWS1("SELECT TOP 100 a.DisplayAlias, (SELECT SUM(b.CommentRating) FROM PPComments b WHERE b.PPLoginID = a.PPLoginID) AS CommentKarma FROM PlayerPortalLogin a WHERE a.Approved = 1 ORDER BY CommentKarma DESC, RegDate ASC");
	
	//keep at bottom
	include "$includepath/base.php";
?>

<?php startblock('title') ?>
	Top Portal Karma
<?php endblock() ?>

<?php startblock('header') ?>
	Top Portal Karma
<?php endblock() ?>

<?php startblock('additionalScripts') ?>

<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li class="active">Top Portal Karma</a>
<?php endblock() ?>

<?php startblock('containerContent') ?>
	<div class="row-fluid">
		<div class="span6">
			<div class="widget">
				<h2>
					<div class="pull-right guildlistdiv">
						<a href="#" class="collapse"><i class="icon-chevron-up icon-white"></i></a>
					</div>
					Posts
				</h2>
				<div class="widget-inner">
					<table class="table table-striped table-bordered table-condensed">
						<thead>
							<tr>
								<th>Rank</th>
								<th>User</th>
								<th>Karma</th>
							</tr>
						</thead>
						<tbody>
						<?php
							for ( $i = 0; $i < Num_Rows($result); $i++ ) {
								$displayalias = Result($result, $i, "DisplayAlias"); 
								$postkarma = Result($result, $i, "PostKarma"); 
								$rank = $i + 1;
								
								if ($postkarma != "") {						
									echo "<tr>";
									echo "<td>$rank</td>";
									echo "<td><a href='$rootpath/userprofile.php?username=$displayalias'>$displayalias</a></td>";
									echo "<td>$postkarma</td>";		
									echo "</tr>";
								}
							}
						?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="span6">
			<div class="widget">
				<h2>
					<div class="pull-right guildlistdiv">
						<a href="#" class="collapse"><i class="icon-chevron-up icon-white"></i></a>
					</div>
					Comments
				</h2>
				<div class="widget-inner">
					<table class="table table-striped table-bordered table-condensed">
						<thead>
							<tr>
								<th>Rank</th>
								<th>User</th>
								<th>Karma</th>
							</tr>
						</thead>
						<tbody>
						<?php
							for ( $i = 0; $i < Num_Rows($result2); $i++ ) {
								$displayalias = Result($result2, $i, "DisplayAlias"); 
								$commentkarma = Result($result2, $i, "CommentKarma"); 
								$rank = $i + 1;
								
								if ($commentkarma != "") {						
									echo "<tr>";
									echo "<td>$rank</td>";
									echo "<td><a href='$rootpath/userprofile.php?username=$displayalias'>$displayalias</a></td>";
									echo "<td>$commentkarma</td>";		
									echo "</tr>";
								}
							}
						?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
<?php endblock() ?>