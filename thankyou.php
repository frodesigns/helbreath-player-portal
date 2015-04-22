<?php
	$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	$rootpath = "/portal";
	
	include("$serverrootpath/includes/db.inc.php");
	include("$includepath/common.php");
	
	//keep at bottom
	include "$includepath/base.php";
?>

<?php startblock('title') ?>
	Thank You
<?php endblock() ?>

<?php startblock('header') ?>
	Thank You
<?php endblock() ?>

<?php startblock('additionalScripts') ?>

<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li class="active">Thank You</a>
<?php endblock() ?>

<?php startblock('containerContent') ?>
	<div class="row-fluid">
		<div class="span12">
			<h3>Your registration is now complete!</h3>
			<p>You will recieve an e-mail shortly with a link to activate your account.  If you do not receive the e-mail, please check your spam folder.  If you still can't find it, please contact Trinny, Avour, or GMGromus to have your account activated manually.  DO NOT MAKE ANOTHER ACCOUNT.</p>
		</div>
	</div>
<?php endblock() ?>