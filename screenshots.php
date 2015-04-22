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
	
	//keep at bottom
	include "$includepath/base.php";
?>

<?php startblock('title') ?>
	Screenshot Gallery
<?php endblock() ?>

<?php startblock('header') ?>
	Screenshot Gallery
<?php endblock() ?>

<?php startblock('additionalScripts') ?>
	<script type="text/javascript" src="<?php echo $rootpath; ?>/js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="<?php echo $rootpath; ?>/js/jquery.form.js"></script>
	<script type="text/javascript" src="<?php echo $rootpath; ?>/js/screenshots.js"></script>
<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li class="active">Screenshot Gallery</a>
<?php endblock() ?>

<?php startblock('containerContent') ?>
	<div class="row-fluid">
		<div class="span12">
			<div class="widget">
				<h2>
					Filters:
					<div class="input-prepend nomargin guildlistdiv">
						<span class="add-on"><i class="icon-list"></i></span><select id="sort" class="screenshots">
							<option value="new">Newest</option>
							<option value="hot">Hot</option>
							<option value="best">Best</option>
							<option value="worst">Worst</option>
						</select>
					</div>
					<div class="input-prepend nomargin guildlistdiv">
						<span class="add-on"><i class="icon-calendar"></i></span><select id="sort2" class="screenshots">
							<option value="month">Month</option>
							<option value="week">Week</option>
							<option value="day">Day</option>
							<option value="all">All Time</option>
						</select>
					</div>
					<div class="input-prepend nomargin guildlistdiv">
						<span class="add-on"><i class="icon-eye-open"></i></span><select id="count" class="screenshots">
							<option value="10">10</option>
							<option value="50">50</option>
							<option value="0">All</option>
						</select>
					</div>
				</h2>
			</div>
			<div id="myCarousel" class="carousel slide">
				<div class="carousel-inner"></div>
				<a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
				<a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
			</div>
		</div>
	</div>
<?php endblock() ?>