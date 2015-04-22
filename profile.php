<?php
	$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	$rootpath = "/portal";
	
	include("$serverrootpath/includes/db.inc.php");
	include("$includepath/common.php");
	include("$includepath/itemdecode.php");
	include("$includepath/items.inc.php");
	
	page_protect();
	
	$user_email = $_SESSION['user_email'];
	$user_id = $_SESSION['user_id'];
	
	$getmd5id = QueryWS1("SELECT md5UserID FROM PlayerPortalLogin WHERE PPLoginID = '$user_id'") or die(mssql_get_last_message());
	list($md5id) = mssql_fetch_row($getmd5id);
	
	$result = QueryWS1("SELECT PPLoginID, Passwd, UserLevel, IP, RegDate, AdminEmailPreference, DisplayAlias FROM PlayerPortalLogin WHERE PPLoginID = '$user_id'") or die (mssql_get_last_message());
	list($id, $pwd, $user_level, $ip, $regdate, $adminemailpreference, $displayname) = mssql_fetch_row($result);
	
	if ($adminemailpreference == 1) {
		$adminemailchecked = "checked";
	} else {
		$adminemailchecked = "";
	}	
	
	$result2 = QueryWS1("SELECT PPLinkedAccountID, AccountName, DisplayAlias FROM PlayerPortalLinkedAccount WHERE PPLoginID = '$id' ORDER BY DisplayAlias ASC") or die (mssql_get_last_message());
	
	//handle password reset
	if (isset($_POST['doPassChange']) && $_POST['doPassChange'] == 'Change') {
		$err = array();
		$msg = array();
		
		foreach($_POST as $key => $value) {
			$data[$key] = filter($value);
		}
		
		// Check User Passwords
		if (!checkPwd($data['newPassword'],$data['confirmNewPassword'])) {
			$err[] = "ERROR - Invalid Password or mismatch. Enter 6 chars or more";
		}
		
		$currentpass = $data['currentPassword'];
		
		if ($pwd !== PwdHash($currentpass,substr($pwd,0,9))) {
			$err[] = "Error - Incorrect current password.";
		}
		
		if(empty($err)) {
			$sha1pass = PwdHash($data['newPassword']);
			QueryWS1("UPDATE PlayerPortalLogin SET Passwd = '$sha1pass' WHERE Email = '$user_email'") or die(mssql_get_last_message());
			
			$msg[] = "Your password has been successfully changed.";
		}
	}
	
	//handle email change
	if (isset($_POST['doEmailChange']) && $_POST['doEmailChange'] == 'Change') {
		$emailerr = array();
		$emailmsg = array();
		
		foreach($_POST as $key => $value) {
			$data[$key] = filter($value);
		}
		
		//validate emails
		if(!isEmail($data['newEmail']) || !isEmail($data['confirmNewEmail'])) {
			$emailerr[] = "ERROR - Invalid email address.";
		}
		
		if ($data['newEmail'] != $data['confirmNewEmail']) {
			$emailerr[] = "ERROR - Emails do not match.";
		}
		
		// Automatically collects the hostname or domain  like example.com) 
		$host  = $_SERVER['HTTP_HOST'];
		$host_upper = strtoupper($host);
		$path   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		
		$activ_code = rand(1000,9999);
		
		/************ USER EMAIL CHECK ************************************
		This code does a second check on the server side if the email already exists. It 
		queries the database and if it has any existing email it throws user email already exists
		*******************************************************************/
		$newEmail = $data['newEmail'];
		$rs_duplicate = QueryWS1("SELECT COUNT(*) AS total FROM PlayerPortalLogin WHERE Email = '$newEmail'") or die(mssql_get_last_message());
		list($total) = mssql_fetch_row($rs_duplicate);

		if ($total > 0) {
			$emailerr[] = "ERROR - The email already exists. Please try again with different email.";
		}
		
		if(empty($emailerr)) {
			$sql_update = "UPDATE PlayerPortalLogin SET Email = '$newEmail', ActivationCode = '$activ_code', Approved = 0 WHERE PPLoginID = '$user_id'";
			
			QueryWS1($sql_update) or die("Update Failed:" . mssql_get_last_message());
			
			$a_link = "
			*****ACTIVATION LINK*****\n
			http://$host$path/activate.php?user=$md5id&activ_code=$activ_code
			"; 

			$message = 
"Hello \n
Your email address has been updated. Here is your activation link...\n

Email: $newEmail \n 

$a_link

Thank You

Administrator
$host_upper
______________________________________________________
THIS IS AN AUTOMATED RESPONSE. 
***DO NOT RESPOND TO THIS EMAIL****
";

			mail("$newEmail", "New Email Activation", $message,
			"From: \"HBUSA Player Portal\" <auto-reply@$host>\r\n" .
			 "X-Mailer: PHP/" . phpversion());
			 
			logout();
		}
	}
	
	//handle preferences
	if (isset($_POST['doPreferences']) && $_POST['doPreferences'] == 'Save') {
		$preferr = array();
		$prefmsg = array();
		
		foreach($_POST as $key => $value) {
			$data[$key] = filter($value);
		}
	
		if(!isset($_POST['adminemails'])){
			$adminemails = 0;
			$adminemailchecked = "";
		} else {
			$adminemails = 1;
			$adminemailchecked = "checked";
		}
		if ($displayname != $data['prefDisplayName'] && $data['prefDisplayName'] != "") {
			$displayname = $data['prefDisplayName'];
			$dn_duplicate = QueryWS1("SELECT COUNT(*) AS total FROM PlayerPortalLogin WHERE DisplayAlias = '$displayname'") or die(mssql_get_last_message());
			list($totaldn) = mssql_fetch_row($dn_duplicate);

			if ($totaldn > 0) {
				$preferr[] = "ERROR - The Display Name you entered already exists. Please choose a different one.";
			}
		}
		
		if(empty($preferr)) {
			$sql_update= "UPDATE PlayerPortalLogin SET AdminEmailPreference = $adminemails, DisplayAlias = '$displayname' WHERE PPLoginID = $user_id";
			
			QueryWS1($sql_update) or die("Update Failed:" . mssql_get_last_message());
			
			$prefmsg[] = "Your preferences have been updated.";
		}
	}
	
	//keep at bottom
	include "$includepath/base.php";
?>

<?php startblock('title') ?>
	Edit Profile
<?php endblock() ?>

<?php startblock('header') ?>
	Edit Profile
<?php endblock() ?>

<?php startblock('additionalScripts') ?>
	<script type="text/javascript" src="<?php echo $rootpath; ?>/js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="<?php echo $rootpath; ?>/js/profile.js"></script>
<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li class="active">Edit Profile</a>
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
	
	<div class="row-fluid">		
		<div class="span6">
			<h3>Linked Game Accounts <button data-toggle="modal" href="#accountModal" class="addAccountButton btn btn-mini btn-inverse"><i class="icon-plus icon-white"></i> Add a Game Account</button></h3>
			<table id="accountsTable" class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>Account Name</th>
						<th>Display Name</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				<?php
					for ( $i = 0; $i < Num_Rows($result2); $i++ ) {
						$PPLinkedAccountID = Result($result2, $i, "PPLinkedAccountID");
						$AccountName = Result($result2, $i, "AccountName");
						$DisplayAlias = Result($result2, $i, "DisplayAlias");
						echo "<tr id='$PPLinkedAccountID'>";
						echo "<td>$AccountName</td><td><span class='display'><button class='btnfloat editGameAccount btn btn-mini btn-warning'><i class='icon-pencil icon-white'></i> Rename</button> <span class='alias'>$DisplayAlias</span></span><span class='edit' style='display: none;'><input type='text' value='$DisplayAlias' class='input-medium' /> <button class='saveEditGameAccount btn btn-success'>Save</button> <button class='cancelEditGameAccount btn'>Cancel</button></span></td><td><button class='removeGameAccount btn btn-mini btn-danger'><i class='icon-remove icon-white'></i> Remove</button></td>";
						echo "</tr>";
					}
				?>
				</tbody>
			</table>

			<h3>Manage Trade List</h3>
			<?php
				$result8 = QueryWS1("SELECT t.* 
					FROM PPTradeItems t 
					WHERE t.PPLoginID = $user_id 
					ORDER BY t.UpdateDT DESC");
					
				if (Num_Rows($result8) > 0 ) {
					echo "<table id='tradeItemTable' class='table table-bordered table-striped table-condensed sortable'>";
					echo "<thead>
						<tr>
							<th>Item</th>
							<th>Main Stat</th>
							<th>Sub Stat</th>
							<th></th>
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
							
							echo "<tr data-sItemID='$sItemID' data-sID1='$sID1' data-sID2='$sID2' data-sID3='$sID3' data-attribute='$iAttribute' class='$stats[class]'><td>$realitemname$plusvalue$completion$qty</td><td>$stats[mainstat]$tablettype$stats[mainstatpercent]</td><td>$stats[substat] $stats[substatpercent]</td><td><a href='#' class='removeitem'>Remove</a></td></tr>";						
						}
						
						$result10 = QueryWS1("SELECT * FROM BANKITEM_T WHERE sItemID = $sItemID AND sID1 = $sID1 AND sID2 = $sID2 AND sID3 = $sID3");
						
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
							
							echo "<tr data-sItemID='$sItemID' data-sID1='$sID1' data-sID2='$sID2' data-sID3='$sID3' data-attribute='$iAttribute' class='$stats[class]'><td>$realitemname$plusvalue$completion$qty</td><td>$stats[mainstat]$tablettype$stats[mainstatpercent]</td><td>$stats[substat] $stats[substatpercent]</td><td><a href='#' class='removeitem'>Remove</a></td></tr>";						
						}
					}
					
					echo "</tbody></table>";
				} else {
					echo "No items found.";
				}
			?>
		</div>
		
		<div class="span6">
			<form id="preferencesForm" method="post" action="<?php echo $rootpath; ?>/profile.php" class="well">
				<fieldset>
					<?php	
						if(!empty($preferr)) {
							echo "<div class=\"errors\">";
							foreach ($preferr as $e) {
								echo "* $e <br>";
							}
							echo "</div>";
						}
						if(!empty($prefmsg))  {
							echo "<div class=\"msg\">" . $prefmsg[0] . "</div>";
						}
					?>
					<legend>Preferences</legend>
					<div class="control-group">
						<label class="control-label" for="prefDisplayName">Display Name:</label>
						<div class="controls">
							<input type="text" name="prefDisplayName" class="required input-xlarge" id="prefDisplayName" maxlength="20" value="<?php echo $displayname; ?>" />
							<p class="help-block">Your publically visible name.</p>
						</div>
					</div>
					<div class="control-group">
						<label class="checkbox">
							<input id="adminemails" name="adminemails" type="checkbox" value="1" <?php echo $adminemailchecked; ?> /> Would you like to receive email updates from the site administrators?
						</label>
					</div>
					<div class="form-actions">
						<input name="doPreferences" id="doPreferences" class="btn btn-primary" type="submit" value="Save" />
					</div>
				</fieldset>
			</form>
			
			<form id="passwordChangeForm" method="post" action="<?php echo $rootpath; ?>/profile.php" class="well">
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
					<legend>Change Password</legend>
					<div class="control-group">
						<label class="control-label" for="currentPassword">Current Password:</label>
						<div class="controls">
							<input type="password" class="required input-xlarge" name="currentPassword" id="currentPassword" value="<?php if(isset($_POST['doChange'])) { echo $data['currentPassword']; } ?>" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="newPassword">New Password:</label>
						<div class="controls">
							<input type="password" class="required input-xlarge" name="newPassword" id="newPassword" value="<?php if(isset($_POST['doChange'])) { echo $data['newPassword']; } ?>" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="confirmNewPassword">Confirm New Password:</label>
						<div class="controls">
							<input type="password" class="required input-xlarge" name="confirmNewPassword" id="confirmNewPassword" value="<?php if(isset($_POST['doChange'])) { echo $data['confirmNewPassword']; } ?>" />
						</div>
					</div>
					<div class="form-actions">
						<input class="btn btn-primary" type="submit" name="doPassChange" id="doPassChange" value="Change" />
					</div>
				</fieldset>
			</form>
			
			<form id="emailChangeForm" method="post" action="<?php echo $rootpath; ?>/profile.php" class="well">
				<fieldset>
					<div class="errors">
						<em>WARNING: Your account will be deactivated until you re-activate it using the link sent to your new email address.<br /><br />
						You will be logged out automatically when your email is successfully changed.</em><br />
						<?php
						if(!empty($emailerr)) {
							foreach ($emailerr as $e) {
								echo "<br />* $e";
							}
						}
						?>
					</div>
					<legend>Change Email</legend>
					<div class="control-group">
						<label class="control-label" for="newEmail">New Email:</label>
						<div class="controls">
							<input type="email" class="required input-xlarge" name="newEmail" id="newEmail" value="<?php if(isset($_POST['doEmailChange'])) { echo $data['newEmail']; } ?>" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="confirmNewEmail">Confirm New Email:</label>
						<div class="controls">
							<input type="email" class="required input-xlarge" name="confirmNewEmail" id="confirmNewEmail" value="<?php if(isset($_POST['doEmailChange'])) { echo $data['confirmNewEmail']; } ?>" />
						</div>
					</div>
					<div class="form-actions">
						<input name="doEmailChange" id="doEmailChange" class="btn btn-primary" type="submit" value="Change" />
					</div>
				</fieldset>
			</form>
		</div>
	</div>
<?php endblock() ?>