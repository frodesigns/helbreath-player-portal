<?php
	$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	$rootpath = "/portal";
	
	include("$serverrootpath/includes/db.inc.php");
	include("$includepath/common.php");
	
	if (isset($_POST['doReset']) && $_POST['doReset'] == 'Reset') {
		$err = array();
		$msg = array();
		
		foreach($_POST as $key => $value) {
			$data[$key] = filter($value);
		}
		
		if(!isEmail($data['resetEmail'])) {
			$err[] = "ERROR - Please enter a valid email."; 
		}
		
		$user_email = $data['resetEmail'];
		
		$rs_check = QueryWS1("SELECT PPLoginID FROM PlayerPortalLogin WHERE Email = '$user_email'") or die (mssql_get_last_message()); 
		$num = mssql_num_rows($rs_check);
		  
		if ( $num <= 0 ) { 
			$err[] = "Error - Sorry. No such account exists.";
		}
		
		if(empty($err)) {
			$new_pwd = GenPwd();
			$pwd_reset = PwdHash($new_pwd);

			$rs_activ = QueryWS1("UPDATE PlayerPortalLogin SET Passwd = '$pwd_reset' WHERE Email = '$user_email'") or die(mssql_get_last_message());
									 
			$host  = $_SERVER['HTTP_HOST'];
			$host_upper = strtoupper($host);
			
			$message = 
"Here are your new password details ...\n
Email: $user_email \n
Password: $new_pwd \n

Thank You

Administrator
$host_upper
______________________________________________________
THIS IS AN AUTOMATED RESPONSE. 
***DO NOT RESPOND TO THIS EMAIL****
";

			mail($user_email, "Reset Password", $message,
			"From: \"HBUSA Player Portal Password Reset\" <auto-reply@$host>\r\n" .
			 "X-Mailer: PHP/" . phpversion());						 
									 
			$msg[] = "Your account password has been reset and a new password has been sent to your email address.";
		}
	}
	
	//keep at bottom
	include "$includepath/base.php";
?>

<?php startblock('title') ?>
	Forgot Password
<?php endblock() ?>

<?php startblock('header') ?>
	Forgot Password
<?php endblock() ?>

<?php startblock('additionalScripts') ?>

<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li class="active">Forgot Password</a>
<?php endblock() ?>

<?php startblock('containerContent') ?>
	<div class="row-fluid">
		<div class="span12">
			<form id="resetForm" method="post" action="<?php echo $rootpath; ?>/forgotpassword.php" class="well">
				<fieldset>
					<?php	
						if(!empty($err)) {
							echo "<div class=\"errors\">";
							foreach ($err as $e) {
								echo "* $e <br>";
							}
							echo "</div>";
						}
						if(!empty($msg))  {
							echo "<div class=\"msg\">" . $msg[0] . "</div>";
						}
					?>
					<legend>Reset Your Password</legend>
					<div class="control-group">
						<label class="control-label" for="resetEmail">Your Email:</label>
						<div class="controls">
							<input type="email" class="required input-xlarge" name="resetEmail" id="resetEmail" value="<?php if(isset($_POST['doLogin'])) { echo $data['resetEmail']; } ?>" />
						</div>
					</div>
					<div class="form-actions">
						<input class="btn btn-primary" type="submit" name="doReset" id="doReset" value="Reset" />
					</div>
				</fieldset>
			</form>
		</div>
	</div>
<?php endblock() ?>