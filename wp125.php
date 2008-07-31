<?php
/*
Plugin Name: WP125
Plugin URI: http://www.webmaster-source.com/wordpress-plugins/
Description: Easily manage 125x125 ads within your WordPress Dashboard.
Author: Matt Harzewski (redwall_hp)
Author URI: http://www.webmaster-source.com
Version: 1.0.0
*/


//Ad Click Redirect
add_action('init', 'wp125_adclick');
function wp125_adclick() {
if ($_GET['adclick'] != "") {
$theid = $_GET['adclick'];
global $wpdb;
$adtable_name = $wpdb->prefix . "wp125_ads";
$thead = $wpdb->get_row("SELECT target FROM $adtable_name WHERE id = '$theid'", OBJECT);
$update = "UPDATE ". $adtable_name ." SET clicks=clicks+1 WHERE id='$theid'";
$results = $wpdb->query( $update );
header("Location: $thead->target");
exit;
}
}


//Installer
require_once(dirname(__FILE__).'/installer.php');
register_activation_hook(__FILE__,'wp125_install');


//Create Widget
function wp125_create_ad_widget() {
register_sidebar_widget(__('WP125'), 'wp125_write_ads_widget');
}
function wp125_write_ads_widget($args) {
extract($args);
echo $before_widget;
echo $before_title;?>Ads<?php echo $after_title;
wp125_write_ads();
echo $after_widget;
}



//Add the Admin Menus
function wp125_add_admin_menu() {
add_menu_page("125x125 Ads", "Ads", "edit_themes", __FILE__, "wp125_write_managemenu");
add_submenu_page(__FILE__, "Manage 125x125 Ads", "Manage", "edit_themes", __FILE__, "wp125_write_managemenu");
add_submenu_page(__FILE__, "Add/Edit 125x125 Ads", "Add/Edit", "edit_themes", 'wp125_addedit', "wp125_write_addeditmenu");
add_submenu_page(__FILE__, "125x125 Ad Settings", "Settings", "edit_themes", 'wp125_settings', "wp125_write_settingsmenu");
}


//Include menus
require_once(dirname(__FILE__).'/adminmenus.php');



//Check Ad Date, and deactivate if the time is up
function wp125_CheckAdDate($thedate, $theid) {
global $wpdb;
$adtable_name = $wpdb->prefix . "wp125_ads";
if ($thedate!='00/00/0000') {
$today = strtotime(date('m').'/'.date('d').'/'.date('Y'));
$thedate = strtotime($thedate);
if ($today > $thedate) {
$updatedb = "UPDATE $adtable_name SET status='0' WHERE id='$theid'";
$results = $wpdb->query($updatedb);
} else { return; }
} else { return; }
}



//Write the Ads
function wp125_write_ads() {
global $wpdb;
$adtable_name = $wpdb->prefix . "wp125_ads";
$settingtable_name = $wpdb->prefix . "wp125_settings";
$thesettings = $wpdb->get_row("SELECT * FROM $settingtable_name WHERE ad_orientation != '' ", OBJECT);
if ($thesettings->ad_order == 'random') { $theorder = 'RAND() LIMIT '.$thesettings->num_slots; } else { $theorder = 'slot ASC'; }
$theads = $wpdb->get_results("SELECT * FROM $adtable_name WHERE status = '1' ORDER BY $theorder", OBJECT);
if ($theads) {
if ($thesettings->ad_orientation=='1c') {
echo '<div id="1c125adwrap" style="width:100%;">';
foreach ($theads as $thead){
echo '<div class="125ad" style="margin-bottom:10px;">';
wp125_CheckAdDate($thead->end_date, $thead->id);
if ($thead->clicks != -1) { $linkurl = get_option('blogurl').'index.php?adclick='.$thead->id; } else { $linkurl = $thead->target; }
echo '<a href="'.$linkurl.'" rel="nofollow"><img src="'.$thead->image_url.'" alt="'.$thead->name.'" /></a>';
echo '</div>';
}
echo '</div>';
}
if ($thesettings->ad_orientation=='2c') {
echo '<div id="2c125adwrap" style="width:100%;">';
foreach ($theads as $thead){
wp125_CheckAdDate($thead->end_date, $thead->id);
if ($thead->clicks != -1) { $linkurl = get_option('blogurl').'index.php?adclick='.$thead->id; } else { $linkurl = $thead->target; }
echo '<div class="125ad" style="width:125px; float:left; padding:10px;"><a href="'.$linkurl.'" rel="nofollow"><img src="'.$thead->image_url.'" alt="'.$thead->name.'" /></a></div>';
}
echo '</div>';
}
if ($thesettings->buyad_url!='') { echo '<div style="padding:10px; clear:both;"><a href="'.$thesettings->buyad_url.'">Your ad here.</a></div>'; }
}
}


function wp125_single_ad($theslot) {
global $wpdb;
$adtable_name = $wpdb->prefix . "wp125_ads";
$thead = $wpdb->get_row("SELECT * FROM $adtable_name WHERE slot = '$theslot' AND status = '1' ORDER BY id DESC", OBJECT);
if ($thead) {
wp125_CheckAdDate($thead->end_date, $thead->id);
if ($thead->clicks != -1) { $linkurl = get_option('blogurl').'index.php?adclick='.$thead->id; } else { $linkurl = $thead->target; }
echo '<a href="'.$linkurl.'" rel="nofollow"><img src="'.$thead->image_url.'" alt="'.$thead->name.'" /></a>';
}
}


//Hooks
add_action("plugins_loaded", "wp125_create_ad_widget"); //Create the Widget
add_action('admin_menu', 'wp125_add_admin_menu'); //Admin pages



/*
Copyright 2008 Matt Harzewski

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

?>