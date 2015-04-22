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
	Posts
<?php endblock() ?>

<?php startblock('header') ?>
	Posts
<?php endblock() ?>

<?php startblock('additionalScripts') ?>
	<script type="text/javascript" src="<?php echo $rootpath; ?>/js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="<?php echo $rootpath; ?>/js/jquery.form.js"></script>
	<script type="text/javascript" src="<?php echo $rootpath; ?>/js/posts.js"></script>
<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li class="active">Posts</a>
<?php endblock() ?>

<?php startblock('containerContent') ?>
	<div class="row-fluid">
		<div class="span12">
			<div class="widget">
				<h2>
					<?php if (isset($_SESSION['user_id'])) { ?>
					<div class="pull-right guildlistdiv">
						<a id="createPost" class="btn btn-success"><i class="icon-plus icon-white"></i> Post</a>
					</div>
					<?php } ?>
					Filters:
					<div class="input-prepend nomargin guildlistdiv">
						<span class="add-on"><i class="icon-list"></i></span><select id="sort" class="posts">
							<option value="new">Newest</option>
							<option value="hot">Hot</option>
							<option value="best">Best</option>
							<option value="worst">Worst</option>
						</select>
					</div>
					<div class="input-prepend nomargin guildlistdiv">
						<span class="add-on"><i class="icon-calendar"></i></span><select id="sort2" class="posts">
							<option value="month">Month</option>
							<option value="week">Week</option>
							<option value="day">Day</option>
							<option value="all">All Time</option>
						</select>
					</div>
					<div class="input-prepend nomargin guildlistdiv">
						<span class="add-on"><i class="icon-eye-open"></i></span><select id="count" class="posts">
							<option value="100">100</option>
							<option value="500">500</option>
							<option value="0">All</option>
						</select>
					</div>
				</h2>
			</div>
			<form id="postForm" enctype="multipart/form-data" method="post" action="<?php echo $rootpath; ?>/ajax/submitPost.php" class="well">
				<fieldset>
					<legend>Create New Post</legend>
					<div class="control-group">
						<label class="control-label" for="loginEmail">Post Content:</label>
						<div class="controls">
							<textarea rows="3" id="textarea" name="postcontent" class="required input-xlarge"></textarea>
							<p class="help-block">140 characters remaining.</p>
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
			<div id="posts"></div>
		</div>
	</div>
<?php endblock() ?>