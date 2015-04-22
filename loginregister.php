<?php
	$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	$rootpath = "/portal";
	
	include("$serverrootpath/includes/db.inc.php");
	include("$includepath/common.php");
	
	user_protect();
	
	if (isset($_SESSION['user_id'])) {
		header("Location: index.php");
		exit();
	}
	
	$rememberchecked = "";
	$adminemailchecked = "checked";
	
	//handle login
	if (isset($_POST['doLogin']) && $_POST['doLogin']=='Login') {
		$loginerr = array();
		
		if(isset($_POST['remember'])){
			$rememberchecked = "checked";
		}
		
		foreach($_POST as $key => $value) {
			$data[$key] = filter($value);
		}
		
		$user_email = $data['loginEmail'];
		$pass = $data['loginPassword'];
		
		$result = QueryWS1("SELECT PPLoginID, Passwd, Approved, UserLevel, DisplayAlias FROM PlayerPortalLogin WHERE Email = '$user_email'") or die (mssql_get_last_message()); 
		$num = mssql_num_rows($result);
		
		if ( $num > 0 ) { 
			list($id, $pwd, $approved, $user_level, $displayname) = mssql_fetch_row($result);
			
			if($approved != 1) {
				$loginerr[] = "Account not activated. Please check your email for activation code";
			}
			
			if ($pwd === PwdHash($pass,substr($pwd,0,9))) {
				if(empty($loginerr)){
					session_save_path($_SERVER['DOCUMENT_ROOT'] . "/portal/sessions");
					ini_set('session.gc_probability', 1);
					session_start();
					session_regenerate_id (true);
					
					$_SESSION['user_id'] = $id;  
					$_SESSION['display_name'] = $displayname;
					$_SESSION['user_email'] = $user_email;
					$_SESSION['user_level'] = $user_level;
					$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
					
					$stamp = time();
					$ckey = GenKey();
					QueryWS1("UPDATE PlayerPortalLogin SET ctime = '$stamp', ckey = '$ckey' WHERE PPLoginID = $id") or die(mysql_error());
					
					if(isset($_POST['remember'])){
						setcookie("user_id", $_SESSION['user_id'], time()+60*60*24*COOKIE_TIME_OUT, "/");
						setcookie("user_key", sha1($ckey), time()+60*60*24*COOKIE_TIME_OUT, "/");
						setcookie("user_email",$_SESSION['user_email'], time()+60*60*24*COOKIE_TIME_OUT, "/");
					}
					
					header("Location: index.php");
				}
			} else {
				$loginerr[] = "Email/Password combination not found.";
			}
		} else {
			$loginerr[] = "Error - Invalid login. No such user exists";
		}	
	}
	
	//handle registration
	if(isset($_POST['doRegister']) && $_POST['doRegister'] == 'Register') { 		
		$regerr = array();
		
		if(!isset($_POST['adminemails'])){
			$adminemailchecked = "";
			$adminemails = 0;
		} else {
			$adminemails = 1;
		}
		
		foreach($_POST as $key => $value) {
			$data[$key] = filter($value);
		}
		
		//validate email
		if(!isEmail($data['regEmail'])) {
			$regerr[] = "ERROR - Invalid email address.";
		}
		
		//validate human
		if(strtolower(trim($data['regHuman'])) != "abaddon") {
			$regerr[] = "ERROR - You answered the human verification question incorrectly.";
		}
		
		// Check User Passwords
		if (!checkPwd($data['regPassword'],$data['regConfirmPassword'])) {
			$regerr[] = "ERROR - Invalid Password or mismatch. Enter 6 chars or more";
		}
			  
		$user_ip = $_SERVER['REMOTE_ADDR'];

		// stores sha1 of password
		$sha1pass = PwdHash($data['regPassword']);
		
		// Automatically collects the hostname or domain  like example.com) 
		$host  = $_SERVER['HTTP_HOST'];
		$host_upper = strtoupper($host);
		$path   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		
		// Generates activation code simple 4 digit number
		$activ_code = rand(1000,9999);
		
		/************ USER EMAIL CHECK ************************************
		This code does a second check on the server side if the email already exists. It 
		queries the database and if it has any existing email it throws user email already exists
		*******************************************************************/
		$regEmail = $data['regEmail'];
		$rs_duplicate = QueryWS1("SELECT COUNT(*) AS total FROM PlayerPortalLogin WHERE Email = '$regEmail'") or die(mssql_get_last_message());
		list($total) = mssql_fetch_row($rs_duplicate);

		if ($total > 0) {
			$regerr[] = "ERROR - The Email already exists. Please try again with different email.";
		}
		
		$regDisplayName = $data['regDisplayName'];
		$dn_duplicate = QueryWS1("SELECT COUNT(*) AS total FROM PlayerPortalLogin WHERE DisplayAlias = '$regDisplayName'") or die(mssql_get_last_message());
		list($totaldn) = mssql_fetch_row($dn_duplicate);

		if ($totaldn > 0) {
			$regerr[] = "ERROR - The Display Name you entered already exists. Please choose a different one.";
		}
		
		if(empty($regerr)) {
			$sql_insert = "INSERT into PlayerPortalLogin
  			(Email, DisplayAlias, Passwd, IP, ActivationCode, AdminEmailPreference)
		    VALUES
		    ('$regEmail', '$regDisplayName', '$sha1pass', '$user_ip', $activ_code, $adminemails)";
			
			QueryWS1($sql_insert) or die("Insertion Failed:" . mssql_get_last_message());
			
			$q = QueryWS1("SELECT TOP 1 PPLoginID FROM PlayerPortalLogin ORDER BY PPLoginID DESC") or die(mssql_get_last_message());			
			$r = mssql_fetch_assoc($q);
			
			$user_id = $r['PPLoginID'];
			$md5_id = md5($user_id);
			
			QueryWS1("UPDATE PlayerPortalLogin SET md5UserID = '$md5_id' WHERE PPLoginID = '$user_id'") or die(mssql_get_last_message());
			
			if ($data["gameAccounts"] != "") {
				$accounts = explode(";", $data["gameAccounts"]);
				foreach ($accounts as $a) {
					$account = explode(":", $a);
					$accountname = $account[0];
					$displayname = $account[1];
					
					if ($accountname != "" && $displayname != "") {
						$sql_insert = "INSERT into PlayerPortalLinkedAccount
						(PPLoginID, AccountName, DisplayAlias)
						VALUES
						('$user_id','$accountname','$displayname')";
						
						QueryWS1($sql_insert) or die("Insertion Failed:" . mssql_get_last_message());
					}
				}
			}
			
			$a_link = "
*****ACTIVATION LINK*****\n
http://$host$path/activate.php?user=$md5_id&activ_code=$activ_code
"; 

$message = 
"Hello \n
Thank you for registering. Here are your login details...\n

Email: $regEmail \n 

$a_link

Thank You

Administrator
$host_upper
______________________________________________________
THIS IS AN AUTOMATED RESPONSE. 
***DO NOT RESPOND TO THIS EMAIL****
";

			mail("$regEmail", "Login Details", $message,
			"From: \"HBUSA Player Portal Registration\" <auto-reply@$host>\r\n" .
			 "X-Mailer: PHP/" . phpversion());

			header("Location: thankyou.php");  
			exit();
		}
	}
	
	//keep at bottom
	include "$includepath/base.php";
?>

<?php startblock('title') ?>
	Welcome
<?php endblock() ?>

<?php startblock('header') ?>
	Welcome
<?php endblock() ?>

<?php startblock('additionalScripts') ?>
	<script type="text/javascript" src="<?php echo $rootpath; ?>/js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="<?php echo $rootpath; ?>/js/loginregister.js"></script>
<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li class="active">Login/Register</a>
<?php endblock() ?>

<?php startblock('containerContent') ?>
    <div class="modal hide fade" id="accountModal">
		<div class="modal-header">
			<button class="close" data-dismiss="modal">&times;</button>
			<h3>Add a Game Account</h3>
		</div>
		<div class="modal-body">
			<form id="acctForm">
				<div class="control-group">
					<label class="control-label" for="acctUsername">HBUSA Account Name:</label>
					<div class="controls">
						<input type="text" class="required input-xlarge" name="acctUsername" id="acctUsername">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="acctPassword">HBUSA Account Password:</label>
					<div class="controls">
						<input type="password" class="required input-xlarge" name="acctPassword" id="acctPassword">
					</div>
				</div>
				<!--<div class="control-group">
					<label class="control-label" for="acctLastname">Last Name:</label>
					<div class="controls">
						<input type="text" class="required input-xlarge" name="acctLastname" id="acctLastname">
						<p class="help-block">The last name associated with this account.</p>
					</div>
				</div>-->
				<div class="control-group">
					<label class="control-label" for="acctDisplayname">Display Name:</label>
					<div class="controls">
						<input type="text" class="required input-xlarge" name="acctDisplayname" maxlength="20" id="acctDisplayname">
						<p class="help-block">Publically visible name for this account. Do NOT use your HBUSA Account Name.</p>
					</div>
				</div>
				<div class="form-actions">
					<button id="addAccount" class="btn btn-primary" type="submit">Connect</button>
					<a data-dismiss="modal" class="btn" href="#">Cancel</a>
					<span id="acctStatus"></span>
				</div>
			</form>
		</div>
    </div>
	
	<div class="row-fluid welcome">
		<div class="span12">
			<p class="lead">
				Welcome to Helbreath USA's Player Portal. Here you can link multiple accounts together in order to view character stats as well as look at your inventory, saved quests, completed quests, track your EK Assists and manage your guilds!
			</p>
			<p>
				Register for an account and opt into the emails and we can send you important infromation on the game including updates and events. <strong>For the best experience, try out <a href="http://www.google.com/chrome/" target="_blank">Google Chrome</a>!</strong>
			</p>
		</div>
	</div>
	
	<div class="row-fluid">
		<div class="span6">
			<form id="loginForm" method="post" action="<?php echo $rootpath; ?>/loginregister.php" class="well">
				<fieldset>
					<?php	
						if(!empty($loginerr)) {
							echo "<div class=\"errors\">";
							foreach ($loginerr as $e) {
								echo "* $e <br>";
							}
							echo "</div>";
						}
					?>
					<legend>Login</legend>
					<div class="control-group">
						<label class="control-label" for="loginEmail">Email:</label>
						<div class="controls">
							<input type="email" class="required input-xlarge" name="loginEmail" id="loginEmail" value="<?php if(isset($_POST['doLogin'])) { echo $data['loginEmail']; } ?>" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="loginPassword">Password:</label>
						<div class="controls">
							<input type="password" class="required input-xlarge" name="loginPassword" id="loginPassword" value="<?php if(isset($_POST['doLogin'])) { echo $data['loginPassword']; } ?>" />
						</div>
					</div>
					<div class="control-group">
						<label class="checkbox">
							<input id="remember" name="remember" type="checkbox" value="1" <?php echo $rememberchecked; ?> /> Remember Me
						</label>
					</div>
					<div class="form-actions">
						<input class="btn btn-primary" type="submit" name="doLogin" id="doLogin" value="Login" />
						<a href="forgotpassword.php" class="btn btn-warning" type="submit"><i class="icon-exclamation-sign icon-white"></i> Forgot Password?</a>
					</div>
				</fieldset>
			</form>
			
			If you haven't recieved your activation email (check your spam folder!), please contact Trinny, Avour, or GMGromus with your Display Name or Email.
		</div>
		
		<div class="span6">
			<form id="registerForm" method="post" action="<?php echo $rootpath; ?>/loginregister.php" class="well">
				<fieldset>
					<?php	
						if(!empty($regerr)) {
							echo "<div class=\"errors\">";
							foreach ($regerr as $e) {
								echo "* $e <br>";
							}
							echo "</div>";
						}
					?>
					<legend>Register</legend>
					<div class="control-group">
						<label class="control-label" for="regEmail">Email:</label>
						<div class="controls">
							<input type="email" name="regEmail" class="required input-xlarge" id="regEmail" value="<?php if(isset($_POST['doRegister'])) { echo $data['regEmail']; } ?>" />
							<p class="help-block">Used to log in.  Will not be published.</p>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="regDisplayName">Display Name:</label>
						<div class="controls">
							<input type="text" name="regDisplayName" class="required input-xlarge" maxlength="20" id="regDisplayName" value="<?php if(isset($_POST['doRegister'])) { echo $data['regDisplayName']; } ?>" />
							<p class="help-block">This is the name your friends will see! Do NOT use your HBUSA Account Name.</p>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="regPassword">Password:</label>
						<div class="controls">
							<input type="password" class="required input-xlarge" name="regPassword" id="regPassword" value="<?php if(isset($_POST['doRegister'])) { echo $data['regPassword']; } ?>">
							<p class="help-block"><a href="http://passphra.se/" target="_blank">Click here to generate a strong password.</a></p>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="regConfirmPassword">Confirm Password:</label>
						<div class="controls">
							<input type="password" class="required input-xlarge" name="regConfirmPassword" id="regConfirmPassword" value="<?php if(isset($_POST['doRegister'])) { echo $data['regConfirmPassword']; } ?>">
							<p class="help-block">Must match the Password field.</p>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Game Accounts:</label>
						<div id="accounts">
							<table class="table table-bordered table-striped table-condensed">
								<thead>
									<th>Account</th>
									<th>Display Name</th>
									<th></th>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
						<div class="controls">
							<input type="hidden" name="gameAccounts" id="gameAccounts" value="<?php if(isset($_POST['doRegister'])) { echo $data['gameAccounts']; } ?>">
							<button data-toggle="modal" href="#accountModal" class="btn btn-inverse"><i class="icon-plus icon-white"></i> Add a Game Account</button>
							<p class="help-block">Optional. You can add accounts after logging in also.</p>
						</div>
					</div>
					<div class="control-group">
						<label class="checkbox">
							<input id="adminemails" name="adminemails" type="checkbox" value="1" <?php echo $adminemailchecked; ?> /> Would you like to receive email updates about this website and the Helbreath game?
						</label>
						<p class="help-block">You can change your preference at any time.</p>
					</div>
					<div class="control-group">
						<label class="control-label" for="regHuman">What is the name of the boss monster in Helbreath?:</label>
						<div class="controls">
							<input type="text" class="required input-xlarge" name="regHuman" id="regHuman" value="<?php if(isset($_POST['doRegister'])) { echo $data['regHuman']; } ?>">
							<p class="help-block">Verify that you are human.</p>
						</div>
					</div>
					<div class="form-actions">
						<input name="doRegister" id="doRegister" class="btn btn-primary" type="submit" value="Register" />
					</div>
				</fieldset>
			</form>
		</div>
	</div>
<?php endblock() ?>