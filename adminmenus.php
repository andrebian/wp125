<?php

//Write Manage Menu
function wp125_write_managemenu() {
echo '<div class="wrap">
<h2>Manage Ads</h2>';

//Handle deactivations
if ($_GET['wp125action'] == "deactivate") {
$theid = $_GET['theid'];
echo '<div id="message" class="updated fade"><p>Are you sure you want to deactivate the ad? <a href="admin.php?page=wp125/wp125.php&wp125action=deactivateconf&theid='.$theid.'">Yes</a> &nbsp; <a href="admin.php?page=wp125/wp125.php">No!</a></p></div>';
}
if ($_GET['wp125action'] == "deactivateconf") {
$theid = $_GET['theid'];
global $wpdb, $table_prefix;
$adtable_name = $wpdb->prefix . "wp125_ads";
$wpdb->query("UPDATE $adtable_name SET status = '0' WHERE id = '$theid'");
echo '<div id="message" class="updated fade"><p>Ad deactivated.</p></div>';
}

//Handle REactivations
if ($_GET['wp125action'] == "activate") {
$theid = $_GET['theid'];
echo '<div id="message" class="updated fade"><p>Are you sure you want to reactivate the ad? <a href="admin.php?page=wp125/wp125.php&showmanage=inactive&wp125action=activateconf&theid='.$theid.'">Yes</a> &nbsp; <a href="admin.php?page=wp125/wp125.php&showmanage=inactive">No!</a></p></div>';
}
if ($_GET['wp125action'] == "activateconf") {
$theid = $_GET['theid'];
global $wpdb, $table_prefix;
$adtable_name = $wpdb->prefix . "wp125_ads";
$wpdb->query("UPDATE $adtable_name SET status = '1' AND pre_exp_email='0' WHERE id = '$theid'");
echo '<div id="message" class="updated fade"><p>Ad activated.</p></div>';
}

echo '<ul class="subsubsub">'; ?>
<li><a href="admin.php?page=wp125/wp125.php"  <?php if ($_GET['showmanage'] != 'inactive') { echo 'class="current"'; } ?>>Active Ads</a> | </li><li><a href="admin.php?page=wp125/wp125.php&showmanage=inactive" <?php if ($_GET['showmanage'] == 'inactive') { echo 'class="current"'; } ?>>Inactive Ads</a></li>
<?php echo '</ul>
<table class="widefat">
<thead><tr>
<th scope="col">Slot</th>
<th scope="col">Name</th>
<th scope="col" class="num">Clicks</th>
<th scope="col">Start Date</th>
<th scope="col">End Date</th>
<th scope="col"></th>
<th scope="col" style="text-align:right;"><a href="admin.php?page=wp125_addedit" class="button rbutton">Add New</a></th>
</tr></thead>
<tbody>';

global $wpdb;
$adtable_name = $wpdb->prefix . "wp125_ads";
if ($_GET['showmanage'] == 'inactive') {
$wp125db = $wpdb->get_results("SELECT * FROM $adtable_name WHERE status = '0' ORDER BY id DESC", OBJECT);
} else {
$wp125db = $wpdb->get_results("SELECT * FROM $adtable_name WHERE status != '0' ORDER BY id DESC", OBJECT);
}
if ($wp125db) {
foreach ($wp125db as $wp125db){

echo '<tr>';
echo '<td>'.$wp125db->slot.'</td>';
echo '<td><strong>'.$wp125db->name.'</strong></td>';
if ($wp125db->clicks!='-1') { echo '<td class="num">'.$wp125db->clicks.'</td>'; } else { echo '<td class="num">N/A</td>'; }
echo '<td>'.$wp125db->start_date.'</td>';
echo '<td>'.$wp125db->end_date.'</td>';
echo '<td><a href="admin.php?page=wp125_addedit&editad='.$wp125db->id.'">Edit</a></td>';
if ($_GET['showmanage'] == 'inactive') {
echo '<td><a href="admin.php?page=wp125/wp125.php&showmanage=inactive&wp125action=activate&theid='.$wp125db->id.'">Activate</a></td>';
} else {
echo '<td><a href="admin.php?page=wp125/wp125.php&wp125action=deactivate&theid='.$wp125db->id.'">Deactivate</a></td>';
}
echo '</tr>';

}
} else { echo '<tr> <td colspan="8">No ads found.</td> </tr>'; }

echo '</tbody>
</table>';
wp125_admin_page_footer();
echo '</div>';
}

function wp125_write_addeditmenu() {
//DB Data
global $wpdb;
$adtable_name = $wpdb->prefix . "wp125_ads";
// Retrieve settings
$setting_ad_orientation = get_option("wp125_ad_orientation");
$setting_num_slots = get_option("wp125_num_slots");
$setting_ad_order = get_option("wp125_ad_order");
$setting_buyad_url = get_option("wp125_buyad_url");
$setting_widget_title = get_option("wp125_widget_title");
$setting_disable_default_style = get_option("wp125_disable_default_style");
$setting_emailonexp = get_option("wp125_emailonexp");
$setting_defaultad = get_option("wp125_defaultad");
//If post is being edited, grab current info
if ($_GET['editad']!='') {
$theid = $_GET['editad'];
$editingad = $wpdb->get_row("SELECT * FROM $adtable_name WHERE id = '$theid'", OBJECT);
}
?><div class="wrap">

<?php
if ($_POST['Submit']) {
$post_editedad = $wpdb->escape($_POST['editedad']);
$post_adname = $wpdb->escape($_POST['adname']);
$post_adslot = $wpdb->escape($_POST['adslot']);
$post_adtarget = $wpdb->escape($_POST['adtarget']);
$post_adexp = $wpdb->escape($_POST['adexp']);
$post_adexpmo = $wpdb->escape($_POST['adexp-mo']);
$post_adexpday = $wpdb->escape($_POST['adexp-day']);
$post_adexpyr = $wpdb->escape($_POST['adexp-yr']);
$post_countclicks = $wpdb->escape($_POST['countclicks']);
$post_adimage = $wpdb->escape($_POST['adimage']);
if ($post_countclicks=='on') { $post_countclicks = '0'; } else { $post_countclicks = '-1'; }
$today = date('m').'/'.date('d').'/'.date('Y');
if ($post_adexp=='manual') { $theenddate = '00/00/0000'; }
if ($post_adexp=='other') { $theenddate = $post_adexpmo.'/'.$post_adexpday.'/'.$post_adexpyr; }
if ($post_adexp=='30') { $expiry = time() + 30 * 24 * 60 * 60; $expiry = strftime('%m/%d/%Y', $expiry); $theenddate = $expiry; }
if ($post_adexp=='60') { $expiry = time() + 60 * 24 * 60 * 60; $expiry = strftime('%m/%d/%Y', $expiry); $theenddate = $expiry; }
if ($post_adexp=='90') { $expiry = time() + 90 * 24 * 60 * 60; $expiry = strftime('%m/%d/%Y', $expiry); $theenddate = $expiry; }
if ($post_adexp=='120') { $expiry = time() + 120 * 24 * 60 * 60; $expiry = strftime('%m/%d/%Y', $expiry); $theenddate = $expiry; }
if ($post_editedad!='') { $theenddate = $post_adexpmo.'/'.$post_adexpday.'/'.$post_adexpyr; }
if ($post_editedad=='') {
$updatedb = "INSERT INTO $adtable_name (slot, name, start_date, end_date, clicks, status, target, image_url) VALUES ('$post_adslot', '$post_adname', '$today','$theenddate','$post_countclicks', '1', '$post_adtarget','$post_adimage')";
$results = $wpdb->query($updatedb);
echo '<div id="message" class="updated fade"><p>Ad &quot;'.$post_adname.'&quot; created.</p></div>';
} else {
$updatedb = "UPDATE $adtable_name SET slot = '$post_adslot', name = '$post_adname', end_date = '$theenddate', target = '$post_adtarget', image_url = '$post_adimage', pre_exp_email = '0' WHERE id='$post_editedad'";
$results = $wpdb->query($updatedb);
echo '<div id="message" class="updated fade"><p>Ad &quot;'.$post_adname.'&quot; updated.</p></div>';
}
}
if ($_POST['deletead']) {
$post_editedad = $wpdb->escape($_POST['editedad']);
echo '<div id="message" class="updated fade"><p>Do you really want to delete this ad record? This action cannot be undone. <a href="admin.php?page=wp125_addedit&deletead='.$post_editedad.'">Yes</a> &nbsp; <a href="admin.php?page=wp125_addedit&editad='.$post_editedad.'">No!</a></p></div>';
}
if ($_GET['deletead']!='') {
$thead=$_GET['deletead'];
$updatedb = "DELETE FROM $adtable_name WHERE id='$thead'";
$results = $wpdb->query($updatedb);
echo '<div id="message" class="updated fade"><p>Ad deleted.</p></div>';
}
?>

<h2>Add/Edit Ads</h2>

<form method="post" action="admin.php?page=wp125_addedit">
<table class="form-table">

<?php if ($_GET['editad']!='') { echo '<input name="editedad" type="hidden" value="'.$_GET['editad'].'" />'; } ?>

<tr valign="top">
<th scope="row">Name</th>
<td><input name="adname" type="text" id="adname" value="<?php echo $editingad->name; ?>" size="40" /><br/>Whose ad is this?</td>
</tr>

<tr valign="top">
<th scope="row">Slot</th>
<td><label for="adslot">
<select name="adslot" id="adslot">
<?php for ($count = 1; $count <= $setting_num_slots; $count += 1) { ?>
<option value="<?php echo $count; ?>" <?php if ($count == $editingad->slot) { echo 'selected="selected"'; } ?>>#<?php echo $count; ?></option>
<?php } ?>
</select></label>
</td></tr>

<tr valign="top">
<th scope="row">Target URL</th>
<td><input name="adtarget" type="text" id="adtarget" value="<?php if ($editingad->target!='') { echo $editingad->target; } else { echo 'http://'; } ?>" size="40" /><br/>Where should the ad link to?</td>
</tr>

<?php if ($_GET['editad']!='') {
$enddate = $editingad->end_date;
if ($enddate != '00/00/0000') {
$enddate = strtotime($enddate);
$endmonth = date('m', $enddate);
$endday = date('d', $enddate);
$endyear = date('Y', $enddate);
} else { $endmonth='00'; $endday='00'; $endyear='0000'; }
} ?>
<tr valign="top">
<th scope="row">Expiration</th>
<td><label for="adexp">
<?php if ($_GET['editad']=='') { ?><select name="adexp" id="adexp" onChange="isOtherDate(this.value)">
<option value="manual">I'll remove it manually</option>
<option selected="selected" value="30">30 Days</option>
<option value="60">60 Days</option>
<option value="90">90 Days</option>
<option value="120">120 Days</option>
<option value="other">Other</option>
</select><?php } ?></label>
 <span id="adexp-date">&nbsp;&nbsp; Month: <input type="text" name="adexp-mo" id="adexp-mo" size="2" value="<?php if ($endmonth!='') { echo $endmonth; } else { echo date('m'); } ?>" /> Day: <input type="text" name="adexp-day" id="adexp-day" size="2" value="<?php if ($endday!='') { echo $endday; } else {  echo date('d'); } ?>" /> Year: <input type="text" name="adexp-yr" id="adexp-yr" size="4" value="<?php if ($endyear!='') { echo $endyear; } else {  echo date('Y'); } ?>" /> <?php if ($_GET['editad']!='') { ?><br /> &nbsp;&nbsp; Use 00/00/0000 for manual removal.<?php } ?></span>
</td></tr>

<?php if ($_GET['editad']=='') { ?><script type="text/javascript">
document.getElementById("adexp-date").style.display = "none";
function isOtherDate(obj) {
if (obj=="other") {
document.getElementById("adexp-date").style.display = "inline";
} else {
document.getElementById("adexp-date").style.display = "none";
}
}
</script><?php } ?>

<?php if ($_GET['editad']=='') { ?>
<tr valign="top">
<th scope="row">Click Tracking</th>
<td><input type="checkbox" name="countclicks" checked="checked" /> Count the number of times this ad is clicked</td>
</tr>
<?php } ?>

<tr valign="top">
<th scope="row">Ad Image</th>
<td><input name="adimage" type="text" id="adimage" value="<?php if ($editingad->image_url!='') { echo $editingad->image_url; } else { echo 'http://'; } ?>" size="40" /></td>
</tr>

</table>
<p class="submit"><input type="submit" name="Submit" value="Save Ad" /> &nbsp; <?php if ($_GET['editad']!='') { ?><input type="submit" name="deletead" value="Delete Ad" /><?php } ?></p>
</form>
<?php wp125_admin_page_footer(); ?>
</div><?php
}

function wp125_write_settingsmenu() {
//DB Data
global $wpdb;
//Add settings, if submitted
if ($_POST['issubmitted']=='yes') {
$post_adorient = $wpdb->escape($_POST['adorient']);
$post_numslots = $wpdb->escape($_POST['numads']);
$post_adorder = $wpdb->escape($_POST['adorder']);
$post_salespage = $wpdb->escape($_POST['salespage']);
$post_widgettitle = $wpdb->escape($_POST['widgettitle']);
$post_defaultstyle = $wpdb->escape($_POST['defaultstyle']);
$post_emailonexp = $wpdb->escape($_POST['emailonexp']);
$post_daysbeforeexp = $wpdb->escape($_POST['daysbeforeexp']);
$post_defaultad = $wpdb->escape($_POST['defaultad']);
if ($post_defaultstyle!='on') { $post_defaultstyle = 'yes'; } else { $post_defaultstyle = ''; }
update_option("wp125_ad_orientation", $post_adorient);
update_option("wp125_num_slots", $post_numslots);
update_option("wp125_ad_order", $post_adorder);
update_option("wp125_buyad_url", $post_salespage);
update_option("wp125_widget_title", $post_widgettitle);
update_option("wp125_disable_default_style", $post_defaultstyle);
update_option("wp125_emailonexp", $post_emailonexp);
update_option("wp125_daysbeforeexp", $post_daysbeforeexp);
update_option("wp125_defaultad", $post_defaultad);
echo '<div id="message" class="updated fade"><p>Settings updated.</p></div>';
}
//Retrieve settings
$setting_ad_orientation = get_option("wp125_ad_orientation");
$setting_num_slots = get_option("wp125_num_slots");
$setting_ad_order = get_option("wp125_ad_order");
$setting_buyad_url = get_option("wp125_buyad_url");
$setting_widget_title = get_option("wp125_widget_title");
$setting_disable_default_style = get_option("wp125_disable_default_style");
$setting_emailonexp = get_option("wp125_emailonexp");
$setting_defaultad = get_option("wp125_defaultad");
$setting_daysbeforeexp = get_option("wp125_daysbeforeexp");
?><div class="wrap">
<h2>Settings</h2>
<form method="post" action="admin.php?page=wp125_settings">
<table class="form-table">

<tr valign="top">
<th scope="row">Ad Orientation</th>
<td><label for="adorient">
<select name="adorient" id="adorient">
<option <?php if ($setting_ad_orientation=='1c') { echo 'selected="selected"'; } ?> value="1c">One Column</option>
<option <?php if ($setting_ad_orientation=='2c') { echo 'selected="selected"'; } ?> value="2c">Two Column</option>
</select></label>
<br/>How many columns should the ads be displayed in?
</td></tr>

<tr valign="top">
<th scope="row">Number of Ad Slots</th>
<td><input name="numads" type="text" id="numads" value="<?php echo $setting_num_slots; ?>" size="2" /><br/>How many ads should be shown?</td>
</tr>

<tr valign="top">
<th scope="row">Ad Order</th>
<td><label for="adorder">
<select name="adorder" id="adorder">
<option selected="selected" value="normal" <?php if ($setting_ad_order=='normal') { echo 'selected="selected"'; } ?>>Normal</option>
<option value="random" <?php if ($setting_ad_order=='random') { echo 'selected="selected"'; } ?>>Random</option>
</select></label>
</td></tr>

<tr valign="top">
<th scope="row">Widget Title</th>
<td><input name="widgettitle" type="text" id="widgettitle" value="<?php echo $setting_widget_title; ?>" size="50" /><br/>The title to be displayed in the widget. <em>(Leave blank to disable.)</em></td>
</tr>

<tr valign="top">
<th scope="row">Ad Sales Page</th>
<td><input name="salespage" type="text" id="salespage" value="<?php echo $setting_buyad_url; ?>" size="50" /><br/>Do you have a page with statistics and prices? <em>(Default Ads will link here.)</em></td>
</tr>

<tr valign="top">
<th scope="row">Default Style</th>
<td><input type="checkbox" name="defaultstyle" <?php if ($setting_disable_default_style=='') { echo 'checked="checked"'; } ?> /> Include default ad stylesheet? <br/>Leave checked unless you want to use your own CSS to style the ads. Refer to the documentation for further help.</td>
</tr>

<tr valign="top">
<th scope="row">Expiration Email</th>
<td><input name="emailonexp" type="text" id="emailonexp" value="<?php echo $setting_emailonexp; ?>" size="50" /><br/>Enter your email address if you would like to be emailed when an ad expires. <em>(Leave blank to disable.)</em></td>
</tr>

<tr valign="top">
<th scope="row">Pre-Expiration Email</th>
<td>Remind me <input name="daysbeforeexp" type="text" id="daysbeforeexp" value="<?php echo $setting_daysbeforeexp; ?>" size="2" /> days before an ad expires. <em>(Emails will be sent to the address specified above).</em></td>
</tr>

<tr valign="top">
<th scope="row">Default Ad</th>
<td><input name="defaultad" type="text" id="defaultad" value="<?php echo $setting_defaultad; ?>" size="50" /><br/>Which image should be shown as a placeholder when an ad slot is empty? (<a href="<?php echo wp125_get_plugin_dir('url').'/youradhere.jpg'; ?>">Default</a>)</td>
</tr>

</table>
<input name="issubmitted" type="hidden" value="yes" />
<p class="submit"><input type="submit" name="Submit" value="Save Changes" /></p>
</form>
<br/>
<p>Your ads can be displayed using either the included widget, or by using the <strong>&lt;?php wp125_write_ads();  ?&gt;</strong> template tag. Also, you can display a single ad, without any formatting, using <strong>&lt;?php wp125_single_ad(<em>num</em>);  ?&gt;</strong>, where <em>num</em> is the number of the ad slot you wish to show. This is useful for cases where your theme prevents the default formatting from working properly, or where you wish to display your ads in an unforeseen manner.</p>
<?php wp125_admin_page_footer(); ?>
</div><?php
}



function wp125_admin_page_footer() {
echo '<div style="margin-top:45px; font-size:0.87em;">';
echo '<div style="float:right;"><a href="http://www.webmaster-source.com/donate/" title="Why should you donate a few dollars? Click to find out..."><img src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" alt="Donate" /></a></div>';
echo '<div><a href="'.wp125_get_plugin_dir('url').'/readme.txt">Documentation</a> | <a href="http://www.webmaster-source.com/wp125-ad-plugin-wordpress/">WP125 Homepage</a></div>';
echo '</div>';
}

?>