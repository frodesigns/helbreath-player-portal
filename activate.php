<?php
	$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	$rootpath = "/portal";
	
	include("$serverrootpath/includes/db.inc.php");
	include("$includepath/common.php");
	
	foreach($_GET as $key => $value) {
		$get[$key] = filter($value);
	}
	
	if(isset($get['user']) && !empty($get['activ_code']) && !empty($get['user']) && is_numeric($get['activ_code']) ) {
		$err = array();
		$msg = array();

		$user = mssql_escape($get['user']);
		$activ = mssql_escape($get['activ_code']);

		$rs_check = QueryWS1("SELECT PPLoginID FROM PlayerPortalLogin WHERE md5UserID = '$user' AND ActivationCode = '$activ'") or die (mssql_get_last_message()); 
		$num = mssql_num_rows($rs_check);

		if ( $num <= 0 ) { 
			$err[] = "Sorry no such account exists, or your activation code is invalid.";
		}

		if(empty($err)) {
			$rs_activ = QueryWS1("UPDATE PlayerPortalLogin SET Approved = 1 WHERE md5UserID = '$user' AND ActivationCode = '$activ' ") or die(mssql_get_last_message());
			$msg[] = "Thank you. Your account has been activated. <a href='$rootpath/loginregister.php'>You may now log in by clicking here</a>.";
		}
	}
	
	//keep at bottom
	include "$includepath/base.php";
?>

<?php startblock('title') ?>
	Account Activation
<?php endblock() ?>

<?php startblock('header') ?>
	Account Activation
<?php endblock() ?>

<?php startblock('additionalScripts') ?>

<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li class="active">Account Activation</a>
<?php endblock() ?>

<?php startblock('containerContent') ?>
	<div class="row-fluid">
		<div class="span12">
			<?php	
				if(!empty($err)) {
					echo "<div class=\"errors\">";
					foreach ($err as $e) {
						echo "* $e <br>";
					}
					echo "</div>";
				}
			?>
			<?php	
				if(!empty($msg)) {
					echo "<div class=\"msg\">" . $msg[0] . "</div>";
				}
			?>
		</div>
	</div>
<?php endblock() ?>