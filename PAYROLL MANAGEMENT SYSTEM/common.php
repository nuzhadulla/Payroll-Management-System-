<?php  	
	ob_start();
	//ini_set('session.save_path', '/Applications/XAMPP/xamppfiles/temp');
	session_start();

	error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
	ini_set("display_errors", 1);

	set_time_limit(-1);
	ini_set('memory_limit', '1024M');

	if (!defined( "MAIN_INCLUDE_PATH" )) {
		define( "MAIN_INCLUDE_PATH", dirname(__FILE__)."/");
	}

//START: From config.php
	$global_config = array();
	$global_config["SiteName"]					= "OCS Management";
	$global_config["SiteDomainName"]			= "";
	$global_config["SiteGlobalPath"] 			= "https://localhost/yourfoldername/"; 
	$global_config["SiteLocalAdminPath"] 		= 'https://localhost/yourfoldername/admin/';
	$global_config["SiteLocalPath"] 			= $_SERVER['DOCUMENT_ROOT'].'/yourfoldername/';
	$global_config["SiteTitle"]					= "Employee Management";
	
	/** DB Details **/
	$global_config["DBHost"]					= 'localhost';
	$global_config["DBUserName"]  				= 'root';
	$global_config["DBPassword"]				= '';
	$global_config["DBDatabaseName"]			= '';
	$global_config["DBTablePrefix"]				= 'tbl_';
	/** DB Details **/

	/** Mail Settings **/
	$global_config["Mod_Rewrite"]				= "ON";
	$global_config["admin_mail"]				= "venkates@oclocksoftware.com";
	$global_config["support_mail"]				= "venkates@oclocksoftware.com";
	/** Mail Settings **/
			
/*
print "<pre>";
print_r($global_config); 
print "</pre>";
exit();
*/

	global $global_config;
	
	define("ABSOLUTE_PATH",					str_replace("includes/","",MAIN_INCLUDE_PATH));
	define("MAIN_CLASS_PATH",				MAIN_INCLUDE_PATH."Classes/");
	define("MAIN_COMMON_PATH",				MAIN_CLASS_PATH."Common/");
	define("SITEGLOBALPATH",  				$global_config["SiteGlobalPath"]);
	define("SITELOCALPATH",  				$global_config["SiteLocalPath"]);
	define("SITENAME", 						$global_config["SiteName"]);
	define("DOMAINNAME",					$global_config["SiteDomainName"]);	
	define("MAXPICSIZE",					10000000); // maximum size (512000) in bytes for files members upload
	define("PROFILEIMAGE_UPLOADPATH",		$global_config["SiteLocalPath"].'profile/');
	define("PROFILEIMAGE_THUMB_UPLOADPATH",	$global_config["SiteLocalPath"].'profile/thumb/');
	define("PROFILEIMAGE_SMALL_THUMB_UPLOADPATH",	$global_config["SiteLocalPath"].'profile/small/');
	define("PROFILEIMAGE_NORMAL_THUMB_UPLOADPATH",	$global_config["SiteLocalPath"].'profile/normal/');
	define("PROFILEIMAGE_THUMB_WIDTH",		170);
	define("PROFILEIMAGE_THUMB_HEIGHT",		170);
			
	define("PROFILEIMAGE_SMALL_THUMB_WIDTH", 70);
	define("PROFILEIMAGE_SMALL_THUMB_HEIGHT", 70);

	define("PROFILEIMAGE_NORMAL_THUMB_WIDTH", 123);
	define("PROFILEIMAGE_NORMAL_THUMB_HEIGHT", 123);

	define("PAGE_LIMIT",					$global_config["pageLimit"]);			
	require_once(MAIN_COMMON_PATH."class.Common.php");
	$objCommon = new Common();

	require_once("Tables.Config.php");
	require_once("functions.php");
?>