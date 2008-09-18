<?php
/*
Plugin Name: WP125
Plugin URI: http://www.webmaster-source.com/wp125-ad-plugin-wordpress/
Description: Easily manage 125x125 ads within your WordPress Dashboard.
Author: Matt Harzewski (redwall_hp)
Author URI: http://www.webmaster-source.com
Version: 1.1.5
*/


define("MANAGEMENT_PERMISSION", "edit_themes"); //The minimum privilege required to manage ads. http://tinyurl.com/wpprivs


//Ad Click Redirect
add_action('init', 'wp125_adclick');
function wp125_adclick() {
if ($_GET['adclick'] != "" && ctype_digit($_GET['adclick'])) {
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


//Stylesheet
function wp125_stylesheet() {
if (get_option("wp125_disable_default_style")=='') {
echo '<link rel="stylesheet" href="'.wp125_get_plugin_dir('url').'/wp125.css" type="text/css" media="screen" />'."\n";
} else { return; }
}
add_action('wp_print_scripts', 'wp125_stylesheet');


//Installer
function wp125_install () {
require_once(dirname(__FILE__).'/installer.php');
}
register_activation_hook(__FILE__,'wp125_install');


//Create Widget
function wp125_create_ad_widget() {
register_sidebar_widget(__('WP125'), 'wp125_write_ads_widget');
}
function wp125_write_ads_widget($args) {
extract($args);
echo $before_widget;
if (get_option("wp125_widget_title")!='') {
echo $before_title; echo get_option("wp125_widget_title"); echo $after_title;
}
wp125_write_ads();
echo $after_widget;
}




//Add the Admin Menus
if (is_admin()) {
function wp125_add_admin_menu() {
add_menu_page("125x125 Ads", "Ads", MANAGEMENT_PERMISSION, __FILE__, "wp125_write_managemenu");
add_submenu_page(__FILE__, "Manage 125x125 Ads", "Manage", MANAGEMENT_PERMISSION, __FILE__, "wp125_write_managemenu");
add_submenu_page(__FILE__, "Add/Edit 125x125 Ads", "Add/Edit", MANAGEMENT_PERMISSION, 'wp125_addedit', "wp125_write_addeditmenu");
add_submenu_page(__FILE__, "125x125 Ad Settings", "Settings", MANAGEMENT_PERMISSION, 'wp125_settings', "wp125_write_settingsmenu");
}

//Include menus
require_once(dirname(__FILE__).'/adminmenus.php');
}



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
sendExpirationEmail($theid);
} else { return; }
} else { return; }
}



//Write the Ads
function wp125_write_ads() {
global $wpdb;
$setting_ad_orientation = get_option("wp125_ad_orientation");
$setting_num_slots = get_option("wp125_num_slots");
$setting_ad_order = get_option("wp125_ad_order");
$setting_buyad_url = get_option("wp125_buyad_url");
$setting_defaultad = get_option("wp125_defaultad");
$adtable_name = $wpdb->prefix . "wp125_ads";
if ($setting_ad_order == 'random') { $theorder = 'RAND() LIMIT '.$setting_num_slots; } else { $theorder = 'slot ASC'; }
$theads = $wpdb->get_results("SELECT * FROM $adtable_name WHERE status = '1' ORDER BY $theorder", ARRAY_A);
if ($theads) {
if ($setting_ad_orientation=='1c') {
echo '<div id="wp125adwrap_1c">';
$arraycount = 0;
foreach ($theads as $thead){
wp125_CheckAdDate($thead['end_date'], $thead['id']);
$theslot = $thead['slot'];
$adguidearray[$theslot] = $thead;
$arraycount++;
}
if ($setting_ad_order == 'random') {
srand((float)microtime() * 1000000);
shuffle($adguidearray);
$adguidearray_shufflefix = $adguidearray[0]; $adguidearray[0]=''; $adguidearray[]=$adguidearray_shufflefix;
}
for ($curslot=1; $curslot <= $setting_num_slots; $curslot++) {
if (isset($adguidearray[$curslot])) {
if ($adguidearray[$curslot]['clicks'] != -1) { $linkurl = get_option('blogurl').'index.php?adclick='.$adguidearray[$curslot]['id']; } else { $linkurl = $adguidearray[$curslot]['target']; }
echo '<div class="wp125ad"><a href="'.$linkurl.'" rel="nofollow"><img src="'.$adguidearray[$curslot]['image_url'].'" alt="'.$adguidearray[$curslot]['name'].'" /></a></div>';
} else { echo '<div class="wp125ad"><a href="'.$setting_buyad_url.'" rel="nofollow"><img src="'.$setting_defaultad.'" alt="" /></a></div>'; }
}
echo '</div>';
}
if ($setting_ad_orientation=='2c') {
echo '<div id="wp125adwrap_2c">';
$arraycount = 0;
foreach ($theads as $thead){
wp125_CheckAdDate($thead['end_date'], $thead['id']);
$theslot = $thead['slot'];
$adguidearray[$theslot] = $thead;
$arraycount++;
}
if ($setting_ad_order == 'random') {
srand((float)microtime() * 1000000);
shuffle($adguidearray);
$adguidearray_shufflefix = $adguidearray[0]; $adguidearray[0]=''; $adguidearray[]=$adguidearray_shufflefix;
}
for ($curslot=1; $curslot <= $setting_num_slots; $curslot++) {
if (isset($adguidearray[$curslot])) {
if ($adguidearray[$curslot]['clicks'] != -1) { $linkurl = get_option('blogurl').'index.php?adclick='.$adguidearray[$curslot]['id']; } else { $linkurl = $adguidearray[$curslot]['target']; }
echo '<div class="wp125ad"><a href="'.$linkurl.'" rel="nofollow"><img src="'.$adguidearray[$curslot]['image_url'].'" alt="'.$adguidearray[$curslot]['name'].'" /></a></div>';
} else { echo '<div class="wp125ad"><a href="'.$setting_buyad_url.'" rel="nofollow"><img src="'.$setting_defaultad.'" alt="" /></a></div>'; }
}
echo '</div>';
}
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
} else { echo '<a href="'.get_option("wp125_buyad_url").'" rel="nofollow"><img src="'.get_option("wp125_defaultad").'" alt="Your Ad Here" /></a>'; }
}


//Return path to plugin directory (url/path)
function wp125_get_plugin_dir($type) {
if ( !defined('WP_CONTENT_URL') )
define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if ( !defined('WP_CONTENT_DIR') )
define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ($type=='path') { return WP_CONTENT_DIR.'/plugins/'.plugin_basename(dirname(__FILE__)); }
else { return WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)); }
}


//Send email alert to admin when an ad expires
function sendExpirationEmail($theid) {
global $wpdb;
$adtable_name = $wpdb->prefix . "wp125_ads";
$thead = $wpdb->get_row("SELECT * FROM $adtable_name WHERE id='$theid'", OBJECT);
if (get_option('wp125_emailonexp')!='') {
$theblog = get_option('blogname');
$from = get_option('admin_email');
$message = "One of the advertisements on $theblog has expired.\n\nAD NAME: ".$thead->name."\nAD URL: ".$thead->target."\nSTART DATE: ".$thead->start_date."\nEND DATE: ".$thead->end_date."\n\nFor more information, and to manage your ads, please log in to your WordPress administration.\n\n\n*** Powered by WordPress and WP125 ***";
$headers = "From: $from\r\nReply-To: $from";
$mail_sent = @mail(get_option('wp125_emailonexp'), "An ad on your blog has expired", $message, $headers);
}
return;
}


//Hooks
add_action("plugins_loaded", "wp125_create_ad_widget"); //Create the Widget
if (is_admin()) { add_action('admin_menu', 'wp125_add_admin_menu'); } //Admin pages



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