<?php 
	$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	$rootpath = "/portal";
	
	require_once "$includepath/ti.php";
	require_once("$includepath/simplepie.inc");
	
	$feed = new SimplePie();
	$feed->set_feed_url('http://helbreathusa.wordpress.com/category/news/feed/');
	$feed->set_output_encoding('UTF-8');
	$feed->set_cache_duration(300);
	$feed->enable_order_by_date(false);
	$feed->set_cache_location('cache');
	$feed->force_feed(true);
	$feed->init();
	$feed->handle_content_type();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php startblock('title') ?><?php endblock() ?> - HBUSA Player Portal</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=0, width=device-width" />

	<link href="<?php echo $rootpath; ?>/css/bootstrap.min.css" rel="stylesheet" />
	<link href="<?php echo $rootpath; ?>/css/styles.css" rel="stylesheet" />
	<link href="<?php echo $rootpath; ?>/css/bootstrap-responsive.css" rel="stylesheet" />
	
	<!--[if lt IE 9]>
	  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!--<link rel="shortcut icon" href="../assets/ico/favicon.ico">-->
</head>

<body>

	<div class="modal hide fade" id="announcementsModal">
		<div class="modal-header">
			<button class="close" data-dismiss="modal">&times;</button>
			<h3></h3>
		</div>
		<div class="modal-body">
		</div>		
	</div>
	
	<div class="desktop">
		<div class="navbar navbar-fixed-top desktopheader">
			<div class="navbar-inner">
				<div class="container-fluid">	
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
				    </a>
					<a class="brand" href="<?php echo $rootpath; ?>"><img src="<?php echo $rootpath; ?>/img/logo.png" alt="HBUSA Player Portal" /></a>
					<?php if (isset($_SESSION['display_name'])) { ?>
					<div class="accountdropdown btn-group pull-right">
						<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
							<i class="icon-globe"></i> <?php echo $_SESSION['display_name']; ?>
							<span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							<?php if (checkAdmin()) { ?>
								<li><a href="<?php echo $rootpath; ?>/admin.php">Admin Control Panel</a></li>
							<?php } ?>
							<li><a href="<?php echo $rootpath; ?>/profile.php">Edit Profile</a></li>
							<li><a href="<?php echo $rootpath; ?>/userprofile.php?username=<?php echo $_SESSION['display_name']; ?>">View Profile</a></li>
							<li class="divider"></li>
							<li><a href="<?php echo $rootpath; ?>/logout.php">Log Out</a></li>
						</ul>
					</div>
					<?php } ?>
					<div class="nav-collapse">
						<ul class="nav">
							<li class="dropdown">
								<a data-toggle="dropdown" class="dropdown-toggle" href="#">Posts <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo $rootpath; ?>/posts.php">All Posts</a></li>
									<li><a href="<?php echo $rootpath; ?>/screenshots.php">Screenshot Gallery</a></li>
								</ul>
							</li>
							<li><a href="<?php echo $rootpath; ?>/market.php">Market</a></li>
							<?php if (isset($_SESSION['display_name'])) { ?>
								<li><a href="<?php echo $rootpath; ?>/warehouse.php">e-Warehouse</a></li>
							<?php } ?>							
							<li class="dropdown">
								<a data-toggle="dropdown" class="dropdown-toggle" href="#">Questing <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo $rootpath; ?>/questlist.php">Quest List</a></li>
									<li class="divider"></li>
									<?php if (isset($_SESSION['display_name'])) { ?>
										<li><a href="<?php echo $rootpath; ?>/questsummary.php">My Quest Summary</a></li>									
									<li class="divider"></li>
									<?php } ?>
									<li><a href="<?php echo $rootpath; ?>/topquesters.php">Top Questing</a></li>
									<li><a href="<?php echo $rootpath; ?>/topguildquesters.php">Guild Top Questing</a></li>
									<li><a href="<?php echo $rootpath; ?>/townquests.php">Town Questing</a></li>
								</ul>
							</li>
							<li class="dropdown">
								<a data-toggle="dropdown" class="dropdown-toggle" href="#">Guilds <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo $rootpath; ?>/guilds.php">Guilds Overview</a></li>									
									<?php if (isset($_SESSION['display_name'])) { ?>
										<li class="divider"></li>
										<li class="nav-header">My Guilds</li>
										<?php 
											$guilds = getGuilds($_SESSION['user_id']);
											
											foreach ($guilds as $guildid => $guildname) {
												echo "<li><a href='$rootpath/guildmembers.php?guildid=$guildid'>$guildname</a></li>";
											}
										?>
									<?php } ?>
								</ul>
							</li>								
							<li class="dropdown">
								<a data-toggle="dropdown" class="dropdown-toggle" href="#">Top Players <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo $rootpath; ?>/topkarma.php">Portal Karma</a></li>
									<li class="divider"></li>
									<li><a href="<?php echo $rootpath; ?>/topeks.php">Enemy Kills</a></li>
									<li><a href="<?php echo $rootpath; ?>/topekassists.php">EK Assists</a></li>																				
									<li><a href="<?php echo $rootpath; ?>/topalchemy.php">Alchemy</a></li>
									<li><a href="<?php echo $rootpath; ?>/topmanufacturing.php">Manufacturing</a></li>
									<li><a href="<?php echo $rootpath; ?>/topfarming.php">Farming</a></li>
									<li><a href="<?php echo $rootpath; ?>/topmining.php">Mining</a></li>
									<li><a href="<?php echo $rootpath; ?>/topfishing.php">Fishing</a></li>
									<li><a href="<?php echo $rootpath; ?>/raceto180.php">Race To 180</a></li>
								</ul>
							</li>	
							<li><a href="<?php echo $rootpath; ?>/members.php">Members</a></li>
						</ul>
					</div>
				</div>			
			</div>
		</div>				
	</div>
	<div class="mobile mobileheader">
		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container-fluid">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
				    </a>
					<a class="brand" href="<?php echo $rootpath; ?>">HBUSA Player Portal</a>
					<?php if (isset($_SESSION['display_name'])) { ?>
					<div class="btn-group pull-right">
						<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
							<i class="icon-globe"></i> <?php echo $_SESSION['display_name']; ?>
							<span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							<?php if (checkAdmin()) { ?>
								<li><a href="<?php echo $rootpath; ?>/admin.php">Admin Control Panel</a></li>
							<?php } ?>
							<li><a href="<?php echo $rootpath; ?>/profile.php">Edit Profile</a></li>
							<li><a href="<?php echo $rootpath; ?>/userprofile.php?username=<?php echo $_SESSION['display_name']; ?>">View Profile</a></li>
							<li class="divider"></li>
							<li><a href="<?php echo $rootpath; ?>/logout.php">Log Out</a></li>
						</ul>
					</div>
					<?php } ?>
					<div class="nav-collapse">
						<ul class="nav">							
							<li class="dropdown">
								<a data-toggle="dropdown" class="dropdown-toggle" href="#">Posts <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo $rootpath; ?>/posts.php">All Posts</a></li>
									<li><a href="<?php echo $rootpath; ?>/screenshots.php">Screenshot Gallery</a></li>
								</ul>
							</li>
							<li><a href="<?php echo $rootpath; ?>/market.php">Market</a></li>
							<?php if (isset($_SESSION['display_name'])) { ?>
								<li><a href="<?php echo $rootpath; ?>/warehouse.php">e-Warehouse</a></li>
							<?php } ?>
							<li class="dropdown">
								<a data-toggle="dropdown" class="dropdown-toggle" href="#">Questing <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo $rootpath; ?>/questlist.php">Quest List</a></li>
									<li class="divider"></li>
									<?php if (isset($_SESSION['display_name'])) { ?>
										<li><a href="<?php echo $rootpath; ?>/questsummary.php">My Quest Summary</a></li>
									<li class="divider"></li>
									<?php } ?>
									<li><a href="<?php echo $rootpath; ?>/topquesters.php">Top Questing</a></li>
									<li><a href="<?php echo $rootpath; ?>/topguildquesters.php">Guild Top Questing</a></li>
									<li><a href="<?php echo $rootpath; ?>/townquests.php">Town Questing</a></li>
								</ul>
							</li>
							<li class="dropdown">
								<a data-toggle="dropdown" class="dropdown-toggle" href="#">Guilds <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo $rootpath; ?>/guilds.php">Guilds Overview</a></li>									
									<?php if (isset($_SESSION['display_name'])) { ?>
										<li class="divider"></li>
										<li class="nav-header">My Guilds</li>
										<?php 
											$guilds = getGuilds($_SESSION['user_id']);
											
											foreach ($guilds as $guildid => $guildname) {
												echo "<li><a href='$rootpath/guildmembers.php?guildid=$guildid'>$guildname</a></li>";
											}
										?>
									<?php } ?>
								</ul>
							</li>
							<li class="dropdown">
								<a data-toggle="dropdown" class="dropdown-toggle" href="#">Top Players <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo $rootpath; ?>/topkarma.php">Portal Karma</a></li>
									<li class="divider"></li>
									<li><a href="<?php echo $rootpath; ?>/topeks.php">Enemy Kills</a></li>
									<li><a href="<?php echo $rootpath; ?>/topekassists.php">EK Assists</a></li>	
									<li><a href="<?php echo $rootpath; ?>/topquesters.php">Questers</a></li>									
									<li><a href="<?php echo $rootpath; ?>/topalchemy.php">Alchemy</a></li>
									<li><a href="<?php echo $rootpath; ?>/topmanufacturing.php">Manufacturing</a></li>
									<li><a href="<?php echo $rootpath; ?>/topfarming.php">Farming</a></li>
									<li><a href="<?php echo $rootpath; ?>/topmining.php">Mining</a></li>
									<li><a href="<?php echo $rootpath; ?>/topfishing.php">Fishing</a></li>
									<li><a href="<?php echo $rootpath; ?>/raceto180.php">Race To 180</a></li>
								</ul>
							</li>
							<li><a href="<?php echo $rootpath; ?>/members.php">Members</a></li>
						</ul>
					</div>					
				</div>	
			</div>
		</div>
	</div>	

    <div class="container-fluid content">
		<ul class="breadcrumb">
			<?php startblock('breadcrumbs') ?><?php endblock() ?>
		</ul>
		<h1><?php startblock('header') ?><?php endblock() ?></h1>
		<?php if (isset($charname)) { ?>
		<div class="navbar">
			<div class="navbar-inner">
				<div class="container">
					<div class="btn-group pull-right">
						<div class="input-prepend nomargin">
							<span class="add-on"><i class="icon-eye-open"></i></span><select class="characterList">
								<?php 
									$characters = getCharacters($user_id);

									foreach ($characters as $character) {
										if ($charname == $character) {
											echo "<option value='$character' selected>$character</option>";
										} else {
											echo "<option value='$character'>$character</option>";
										}
									}
								?>
							</select>
						</div>
					</div>
					<ul class="nav charnav">
						<li><a href="<?php echo "$rootpath/charstats.php?charname=$charname"; ?>">Character Stats</a></li>
						<li><a href="<?php echo "$rootpath/inventory.php?charname=$charname"; ?>">Inventory</a></li>
						<li><a href="<?php echo "$rootpath/quests.php?charname=$charname"; ?>">Saved Quests</a></li>
						<li><a href="<?php echo "$rootpath/questscompleted.php?charname=$charname"; ?>">Completed Quests</a></li>
						<li><a href="<?php echo "$rootpath/ekassists.php?charname=$charname"; ?>">EK Assists</a></li>
					</ul>
				</div>
			</div>
		</div>
		<?php } ?>
		<div id="row-wrapper">
		<?php startblock('containerContent') ?><?php endblock() ?>
		</div>
    		
		<footer class="desktop">
			<div class="twitter">
				<div class="row-fluid">
					<div class="span12">
						<div class="pull-right">
							<a href="https://twitter.com/HelbreathUSA" class="twitter-follow-button" data-show-count="false">Follow @HelbreathUSA</a>
							<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
						</div>
						<img src="<?php echo $rootpath; ?>/img/twitter-logo.png" alt="Twitter" /> <a href="https://twitter.com/#!/helbreathusa" target="_blank">@HelbreathUSA</a> 
						<span id="lasttweet">Could not connect to Twitter.</span> <!--<a id="refresh-tweet" class="btn btn-info" href="#"><i class="icon-refresh icon-white"></i></a>-->
					</div>
				</div>
			</div>
			<div class="desktopfooter">
				<div class="row-fluid">
					<div class="span3">
					<h3>Helbreath USA</h3>
						<p>
							Helbreath USA is a classic 2D RPG game that allows players to participate in massive scale battles, wield mighty weapons and cast powerful spells. You have the choice between two cities, many different character looks including hair styles and colors, numerous weapons and armors that you can customize.
						</p>
					</div>					
					<div class="span3 announcements">
						<h4>Announcements</h4>
						<?php 
							foreach ($feed->get_items(0, 5) as $item): 
								$postid = str_replace("http://helbreathusa.wordpress.com/?p=", "", $item->get_id());
								$title = $item->get_title();
								$content = $item->get_content();
								$date = $item->get_date("n/j/Y");
								
								echo "<a rel='$postid' href='#'><img src='$rootpath/img/news-icon.png' alt='' /> <span class='newstitle'>$date | $title</span></a><div id='$postid' style='display: none;'>$content</div>";						
							endforeach; 
						?>
					</div>
					<div class="span3">
						<h4>Popular Guides</h4>
						<p>
							<a href="http://www.helbreathusa.com/service.php" target="_blank">Service Commands</a><br />
							<a href="http://www.helbreathusa.com/events.php" target="_blank">Event Information</a><br />
							<a href="http://www.helbreathusa.com/quests.php" target="_blank">Quest Information</a><br />
							<a href="http://www.helbreathusa.com/faq.php" target="_blank">FAQs</a><br />
							<a href="http://www.helbreathusa.com/rules.php" target="_blank">Rules</a><br />
							<a href="http://www.helbreathusa.com/activeGMs.php" target="_blank">Active Game Masters</a>
						</p>
					</div>
					<div class="span3">
						<h4>Useful Links</h4>
						<p>
							<a href="http://www.helbreathusa.com" target="_blank">Helbreath USA Official Website</a><br />
							<a href="https://secure.ient.com/hb/cash-shop-subscribers2.php" target="_blank">Cash Shop</a><br />
							<a href="http://www.hbtixx.com" target="_blank">HelSupport (Tixx) System</a><br />
							<a href="http://forums.hbportal.net" target="_blank">Official HBUSA Forums</a><br />
							<a href="http://www.facebook.com/HelbreathUSAIent" target="_blank">HBUSA on Facebook</a><br /><br />
							<a href="mailto:&#103;&#097;&#109;&#101;&#109;&#097;&#115;&#116;&#101;&#114;&#104;&#098;&#117;&#115;&#097;&#064;&#103;&#109;&#097;&#105;&#108;&#046;&#099;&#111;&#109;">Email the Gamemaster</a>
						</p>
					</div>
				</div>
			</div>
			<div class="copyright">
				<div class="row-fluid">
					<div class="span12">
						<p class="pull-right">Brought to you by Trinny, GMGromus, GMStarr, and Avour. The current server time is <span id="serverTime"><?php echo date('g:i A', time()); ?></span>.</p>
						<a class="scrollToTop" href="#"><i class="icon-chevron-up icon-white"></i> Back to Top</a>
					</div>
				</div>
			</div>
		</footer>
		<footer class="mobile footer">
			<p class="pull-right"><a class="scrollToTop" href="#"><i class="icon-chevron-up"></i> Back to Top</a></p>
			<p>Brought to you by Trinny, GMGromus, GMStarr, and Avour. The current server time is <span id="serverTime"><?php echo date('g:i A', time()); ?></span>.</p>
		</footer>	
	
	</div>
	
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="<?php echo $rootpath; ?>/js/bootstrap.min.js"></script>
	<script type="text/javascript">
		//tracking code
		// var _gaq = _gaq || [];
		// _gaq.push(['_setAccount', 'UA-32744817-1']);
		// _gaq.push(['_trackPageview']);
		// (function() {
			// var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			// ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			// var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		// })();
	</script>
	<script type="text/javascript" src="<?php echo $rootpath; ?>/js/main.js"></script>
	<?php startblock('additionalScripts') ?><?php endblock() ?>
</body>

</html>
  