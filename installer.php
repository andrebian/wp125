<?php
//***** Installer *****
function wp125_install () {
require_once(ABSPATH . 'wp-admin/upgrade.php');
//***Installer variables***
global $wpdb;
$table_name = $wpdb->prefix . "wp125_settings";
$table2_name = $wpdb->prefix . "wp125_ads";
$wp125_db_version = "0.8";
//***Installer***
if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
$sql = "CREATE TABLE " . $table_name . " (
	  ad_orientation varchar(3) NOT NULL,
	  num_slots int(3) NOT NULL,
	  ad_order varchar(8) NOT NULL,
	  buyad_url text NOT NULL
	);";
dbDelta($sql);
$sql = "CREATE TABLE " . $table2_name . " (
	  id int(12) NOT NULL auto_increment,
	  slot int(2) NOT NULL,
	  name text NOT NULL,
	  clicks int(7) NOT NULL,
	  start_date varchar(12) NOT NULL,
	  end_date varchar(12) NOT NULL,
	  status int(1) NOT NULL,
	  target text NOT NULL,
	  image_url text NOT NULL,
	  PRIMARY KEY  (id)
	);";
dbDelta($sql);

$def_adorient = "2c";
$def_num_slots = 6;
$def_ad_order = "normal";
$def_buyad_url = '';
$insert = "INSERT INTO " . $table_name . " (ad_orientation, num_slots, ad_order, buyad_url) " . "VALUES ('" . $wpdb->escape($def_adorient) . "','" . $wpdb->escape($def_num_slots) .  "','" . $wpdb->escape($def_ad_order) .  "','" . $wpdb->escape($def_buyad_url) ."')";
$results = $wpdb->query( $insert );
add_option("wp125_db_version", $wp125_db_version);
}
//***Upgrader***
$installed_ver = get_option( "wp125_db_version" );
if( $installed_ver != $wp125_db_version ) {
$sql = "CREATE TABLE " . $table_name . " (
	  ad_orientation varchar(3) NOT NULL,
	  num_slots int(3) NOT NULL,
	  ad_order varchar(8) NOT NULL,
	  buyad_url text NOT NULL
	);";
dbDelta($sql);
$sql = "CREATE TABLE " . $table2_name . " (
	  id int(12) NOT NULL auto_increment,
	  slot int(2) NOT NULL,
	  name text NOT NULL,
	  clicks int(7) NOT NULL,
	  start_date varchar(12) NOT NULL,
	  end_date varchar(12) NOT NULL,
	  status int(1) NOT NULL,
	  target text NOT NULL,
	  image_url text NOT NULL,
	  PRIMARY KEY  (id)
	);";
dbDelta($sql);
update_option( "wp125_db_version", $wp125_db_version );
}
}
//***** End Installer *****
?>