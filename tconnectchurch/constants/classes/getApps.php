<?php
/** -------------------------------------------------------------------------
 * This program is Open Source; you can redistribute it
 * and/or modify it under the terms of the GNU General Public License
 * This program is coded in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY
 * @package  CMS
 * @author      Kenwoo - iweb@kenwoo.ca
 * @license    http://creativecommons.org/licenses/by/4.0/ Creative Commons
 *
 * [v02-10/08/2014]:: Set flag, not allow to direct access
 *  ------------------------------------------------------------------------- */
defined( "_VALID_MOS" ) or die( "Your system is not working properly." );

class getApps {

	public static $error_flag;
	public static $error_message;
	public static $dbConnect;
	public static $modules;
	public static $minifyHtml;

	public function __construct(){
		global $Bon_db;

		self::$error_flag						= false;
		self::$error_message	= array();
		self::$dbConnect	 			= $Bon_db;
		self::$modules						= new getModules("404");
		self::$minifyHtml				= new getHTMLOptimize();

		if ( !preg_match( "/$server_host/i", $_SERVER['SERVER_NAME'] ) ){
			self::$error_flag			= true;
			self::$error_message	= PLEASE_WRITE_ARTICLE_PROPERLY;
		}
	}

/** -------------------------------------------------------------------------
 * [10/08/2014]:: To
 *  ------------------------------------------------------------------------- */
	public function setPgid( $pid ){
		if ( !is_array($pid) && ($pid == "404" || $pid == "all") ){
			$pdquery				= "SELECT id FROM pages WHERE status = '1' AND publish = '1'";
			$pdObject		= self::$dbConnect->getObject( $pdquery );
			$default_pid	= $pdObject->id;
		}
		else {
			$default_pid = $pid['pid'];
		}
		unset( $pid );

		return $default_pid;
	}

/** -------------------------------------------------------------------------
 * [10/08/2014]:: To
 *  ------------------------------------------------------------------------- */
	public function setApps( $pid ){
		//This is to call all of modules.
		if (self::$dbConnect->getTotalNumber("apps_pages", "pageid LIKE '%[".self::setPgid($pid)."]%' AND publish = '1'") > 0){
			$apps_query = self::$dbConnect->getQuery("SELECT * FROM apps_pages WHERE pageid LIKE '%[".self::setPgid($pid)."]%' AND publish = '1' ORDER BY app_position, ordering");
			while( $apps_items = self::$dbConnect->getFetch_Array($apps_query) ){
					$module_position[$apps_items['app_position']] .= self::$minifyHtml->minifyHTML(self::$modules->setModules($apps_items['app_id'], $apps_items['app_position']));
			}
		}

		//This is to call all of banners.
		if (self::$dbConnect->getTotalNumber("banners", "pageid LIKE '%[".self::setPgid($pid)."]%' AND publish = '1' AND status = '1'") > 0){
			$banner_query = "SELECT id, position FROM banners WHERE pageid LIKE '%[".self::setPgid($pid)."]%' AND publish = '1' AND status = '1' ORDER BY ordering";
			$bnObject		  = self::$dbConnect->getObject( $banner_query );
			$module_position[$bnObject->position] .= self::$minifyHtml->minifyHTML(self::$modules->setBanners( self::setPgid($pid), $bnObject->position ));
		}

		return $module_position;
	}
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!