<?php
	$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	$rootpath = "/portal";
	
	include("$serverrootpath/includes/db.inc.php");
	include("$includepath/common.php");
	
	page_protect();
	
	$user_email = $_SESSION['user_email'];
	
	if (!checkAdmin()) {
		header("Location: index.php");
		exit();
	}
	
	//send mass email
	if(isset($_POST['sendEmail']) && $_POST['sendEmail'] == 'Send') { 
		foreach($_POST as $key => $value) {
			$data[$key] = filter($value);
		}
		
		$subject = $data['subject'];
		$message = $data['message'];	
		
		$host  = $_SERVER['HTTP_HOST'];
		$host_upper = strtoupper($host);
		$path   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

		$result = QueryWS1("SELECT Email FROM PlayerPortalLogin WHERE AdminEmailPreference = 1 AND Approved = 1") or die(mssql_get_last_message());
		
		for ( $i = 0; $i < Num_Rows($result); $i++ ) {
			$email = Result($result, $i, "Email");
			
			$messagetosend = 
"Hello \n

$message
s
Thank You

Administrator
$host_upper
______________________________________________________
THIS IS AN AUTOMATED RESPONSE. 
***DO NOT RESPOND TO THIS EMAIL****
";
			
			mail("$email", "HBUSA Player Portal - $subject", $messagetosend,
			"From: \"HBUSA Player Portal\" <auto-reply@$host>\r\n" .
			 "X-Mailer: PHP/" . phpversion());
		 
		 }
		 
		$status = "Mass email sent successfully.";
		$subject = "";
		$message = "";
	}
	
	$result = QueryWS1("SELECT PPLoginID, Email, DisplayAlias, Approved, UserLevel, IP, RegDate, AdminEmailPreference FROM PlayerPortalLogin ORDER BY Approved ASC, RegDate DESC");
	
	//keep at bottom
	include "$includepath/base.php";
?>

<?php startblock('title') ?>
	Admin Control Panel
<?php endblock() ?>

<?php startblock('header') ?>
	Admin Control Panel
<?php endblock() ?>

<?php startblock('additionalScripts') ?>
	<script type="text/javascript" src="<?php echo $rootpath; ?>/js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="<?php echo $rootpath; ?>/js/admin.js"></script>
<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li class="active">Admin Control Panel</a>
<?php endblock() ?>

<?php startblock('containerContent') ?>	
	<div class="modal hide fade" id="linkedAccountsModal">
		<div class="modal-header">
			<button class="close" data-dismiss="modal">&times;</button>
			<h3></h3>
		</div>
		<div class="modal-body">
		</div>		
	</div>
		
	<div class="row-fluid">		
		<div class="span-12">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#accounts" data-toggle="tab">Accounts</a></li>
				<li><a href="#massemail" data-toggle="tab">Mass Email</a></li>
				<li><a href="#howto" data-toggle="tab">How To</a></li>
				<li><a href="#itemlookup" data-toggle="tab">Item Lookup</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="accounts">
					<h3>Accounts - <?php echo Num_Rows($result); ?></h3>
					<table id="accountsAdminTable" class="table table-bordered table-striped sortable">
						<thead>
							<tr>
								<th>Display Name</th>
								<th>Email</th>
								<th>Accounts</th>
								<th>Status</th>
								<th>User Level</th>
								<th>Email Subscribed</th>
								<th>IP</th>
								<th>Registration Date</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
						<?php
							for ( $i = 0; $i < Num_Rows($result); $i++ ) {
								$id = Result($result, $i, "PPLoginID");
								$email = Result($result, $i, "Email");
								$approved = Result($result, $i, "Approved");
								$userlevel = Result($result, $i, "UserLevel");
								$ip = Result($result, $i, "IP");
								$regdate = Result($result, $i, "RegDate");
								$regdate = date("F j, Y - g:i a", strtotime($regdate));
								$adminemailpreference = Result($result, $i, "AdminEmailPreference");
								$displayalias = Result($result, $i, "DisplayAlias");
								
								if ($adminemailpreference == 1) {
									$adminemails = "Yes";
								} else {
									$adminemails = "No";
								}	
								
								if ($userlevel == 0) {
									$usertype = "Member";
								} else if ($userlevel == 1) {
									$usertype = "Moderator";
								} else if ($userlevel == 2) {
									$usertype = "Admin";
								}
								
								if ($approved == 0) {
									$approval = "Pending Activation";
								} else if ($approved == 1) {
									$approval = "Activated";
								}
								
								echo "<tr id='$id'>";
									echo "<td><a href='$rootpath/userprofile.php?username=$displayalias'>$displayalias</a></td>";
									echo "<td class='useremail'>$email</td>";
									echo "<td><button class='viewLinkedAccounts btn btn-mini btn-info'>View List</button></td>";
									echo "<td>";										
										if ($approved == 0) {
											echo "<button rel='1' class='btnfloat activateUser btn btn-mini btn-success'>Activate</button>";
										} else if ($approved == 1 && $id != $user_id) {
											echo "<button rel='0' class='btnfloat activateUser btn btn-mini btn-danger'>Deactivate</button>";
										}
										echo "<span>$approval</span>";
									echo "</td>";
									echo "<td>";										
										if ($userlevel == 0) {
											echo "<button rel='1' class='btnfloat promoteUser btn btn-mini btn-success'>Promote</button>";
										} else if ($userlevel == 1 && $id != $user_id) {
											echo "<button rel='0' class='btnfloat promoteUser btn btn-mini btn-danger'>Demote</button>";
										}
										echo "<span>$usertype</span>";
									echo "</td>";
									echo "<td>$adminemails</td>";
									echo "<td>$ip</td>";
									echo "<td>$regdate</td>";
									echo "<td>";
									if ($id != $user_id) {
										echo "<button class='deleteUser btn btn-mini btn-danger'><i class='icon-trash icon-white'></i> Delete Account</button>";
									}
									echo "</td>";
								echo "</tr>";
							}
						?>
						</tbody>
					</table>
				</div>
				<div class="tab-pane" id="massemail">
					<form id="massEmailForm" action="<?php echo $rootpath; ?>/admin.php#massemail" method="post" class="well">
						<fieldset>
							<?php	
								if (isset($status)) {
									echo "<div class=\"msg\">";
									echo $status;
									echo "</div>";
								}
							?>
							<legend>Send Mass Email</legend>
							<div class="control-group">
								<label for="subject" class="control-label">Subject:</label>
								<div class="controls">
									<div class="input-prepend">
										<span class="add-on">HBUSA Player Portal - </span><input type="text" class="required input-xlarge" name="subject" id="subject" value="<?php if(isset($_POST['sendEmail'])) { echo $subject; } ?>" />
									</div>
								</div>
							</div>	
							<div class="control-group">
								<label for="subject" class="control-label">Message:</label>
								<div class="controls">
									<textarea rows="5" id="message" name="message" class="required input-xlarge"><?php if(isset($_POST['sendEmail'])) { echo $message; } ?></textarea>
								</div>
							</div>
							<div class="form-actions">
								<input id="sendEmail" name="sendEmail" class="btn btn-primary" type="submit" value="Send" />
							</div>
						</fieldset>
					</form>
				</div>
				<div class="tab-pane" id="howto">
					<h3>How To Update The Website from Dev to Live</h3>
					<ol>
						<li>Connect to the FTP</li>
						<li>Navigate to "/dev_helbreath/portal-versions/v2/"</li>
						<li>Right-click and download "portal" to your desktop</li>
						<li>Navigate to "/"</li>
						<li>Right-click and upload "portal" to "/" from your desktop</li>
					</ol>
				</div>
				<div class="tab-pane" id="itemlookup">
					Coming Soon!
					<?php
						//$result3 = QueryWS1("SELECT i.*, c.cCharName FROM ITEM_T i INNER JOIN CHARACTER_T c ON i.CharID = c.CharID WHERE sItemID = 650");
		
						//$result4 = QueryWS1("SELECT i.*, c.cCharName FROM BANKITEM_T i INNER JOIN CHARACTER_T c ON i.CharID = c.CharID WHERE sItemID = 650");	
					?>
				</div>
			</div>
		</div>
	</div>
<?php endblock() ?>