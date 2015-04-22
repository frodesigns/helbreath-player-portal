<?php
	$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	$rootpath = "/portal";
	
	include("$serverrootpath/includes/db.inc.php");
	include("$includepath/common.php");
	include("$includepath/itemdecode.php");
	include("$includepath/items.inc.php");
	
	user_protect();
	if (isset($_SESSION['user_id'])) {
		$user_id = $_SESSION['user_id'];
	}
	
	//keep at bottom
	include "$includepath/base.php";
?>

<?php startblock('title') ?>
	Market
<?php endblock() ?>

<?php startblock('header') ?>
	Market
<?php endblock() ?>

<?php startblock('additionalScripts') ?>
	<script type="text/javascript" src="<?php echo $rootpath; ?>/js/market.js"></script>
<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li class="active">Market</a>
<?php endblock() ?>

<?php startblock('containerContent') ?>
	<div class="row-fluid">
		<div class="span12">
			<div class="widget">
				<h2>
					Filters:
					<div class="input-prepend nomargin guildlistdiv">
						<span class="add-on"><i class="icon-list"></i></span><select id="sort" class="tradeitems">
							<option value=''>All Items</option>
							<option value='0'>Un-Equippable Items</option>
							<option value='1'>Helms</option>
							<option value='2'>Chest Armors</option>
							<option value='3'>Hauberks</option>
							<option value='4'>Legs</option>
							<option value='5'>Shoes</option>
							<option value='6'>Necklaces</option>
							<option value='7'>Shields</option>
							<option value='8'>One-Handed Weapons</option>
							<option value='9'>Two-Handed Weapons</option>
							<option value='10'>Rings</option>
							<option value='11'>Gems/Angels</option>
							<option value='12'>Capes</option>
							<?php
								// $marketfilterarray = array();
								
								// foreach($itemarray as $itemid=>$itemname) {
									// if (array_key_exists($itemname, $itemnamearray)) {
										// $realitemname = trim($itemnamearray[$itemname]);
									// } else {
										// $realitemname = $itemname;
									// }								
									
									// $marketfilterarray[$itemid] = "$realitemname";
								// }	
								
								// natcasesort($marketfilterarray);
								
								// foreach($marketfilterarray as $itemid=>$itemname) {
									// echo "<option value='$itemid'>$itemname</option>";
								// }
							?>
						</select>
					</div>
					<div class="input-prepend nomargin guildlistdiv">
						<span class="add-on"><i class="icon-calendar"></i></span><select id="sort2" class="tradeitems">
							<option value="month">Month</option>
							<option value="week">Week</option>
							<option value="day">Day</option>
							<option value="all">All Time</option>
						</select>
					</div>
					<div class="input-prepend nomargin guildlistdiv">
						<span class="add-on"><i class="icon-eye-open"></i></span><select id="count" class="tradeitems">
							<option value="50">50</option>
							<option value="100">100</option>
							<option value="500">500 (Slow!)</option>
						</select>
					</div>
				</h2>
				<div class="widget-inner">
					<div id="market"></div>
				</div>
			</div>			
		</div>
	</div>
<?php endblock() ?>