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

class getModules {

	public static $error_flag;
	public static $error_message;
	public static $dbConnect;
	public static $dphp;

	public function __construct(){
		global $Bon_db;

		self::$error_flag			= false;
		self::$error_message	= array();
		self::$dbConnect	 	= $Bon_db;
		self::$dphp				= new getDirectCode();

		if ( !preg_match( "/$server_host/i", $_SERVER['SERVER_NAME'] ) ){
			self::$error_flag			= true;
			self::$error_message	= PLEASE_WRITE_ARTICLE_PROPERLY;
		}
	}

/** -------------------------------------------------------------------------
 * [10/08/2014]:: To
 *  ------------------------------------------------------------------------- */
	public function setDefault_id( $pid ){
		if ( !is_array($pid) && ($pid == "404" || $pid == "all") ){
			$pdquery		= "SELECT id FROM pages WHERE frontpage = '1'";
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
	public function setModules( $pid, $pposition ){

		$item_query = self::$dbConnect->getQuery("SELECT title, fulltxt, showtitle, position, filename, params FROM modules WHERE inpage ='0' AND id = '".$pid."' AND position = '".$pposition."' AND publish = '1' AND status = '1' AND access_level >= '0'");
		while( $_modules = self::$dbConnect->getFetch_Array($item_query) ){
			if ( _getCheckNullorNot( $_modules['filename'] ) ){
					$file = CONFIG_DOC_ROOT . "/modules/".$_modules['filename'].".inc";
					$output = self::$dphp->getDirectPHPS( file_get_contents($file, FILE_USE_INCLUDE_PATH), false);
			}
			else {
					$output = self::$dphp->getDirectPHPS($_modules['fulltxt'], false);
			}

			$module_title = $_modules['showtitle'] == 1 ? "<span class=\"module_title\">".$_modules['title']."</span>\n" : "";
			$module_position .= $module_title.$output."\n";
		}

		if (self::$dbConnect->getTotalNumber("banners", "pageid LIKE '%[".$pid."]%' AND publish = '1' AND status = '1'") > 0){
			$module_position .= self::setBanners( $pid, $pposition );
		}

		if (self::$dbConnect->getTotalNumber("advertises_pages", "pageid LIKE '%[".$pid."]%' AND publish = '1' AND status = '1'") > 0){
			$module_position .= getAdvertises::setAds( $pid, $pposition );
		}

		return $module_position;
	}

/** -------------------------------------------------------------------------
 * [10/08/2014]:: To
 *  ------------------------------------------------------------------------- */
	public function setBanners( $pid, $pgposition ){
			$mdquery = "SELECT frontpage FROM pages WHERE id = '". $pid ."'";
			$mdObject = self::$dbConnect->getObject( $mdquery );

			//Banner for EACH page
			if( self::$dbConnect->getTotalNumber("banners", "pageid LIKE '%[". $pid ."]%' AND position = '".$pgposition."'") > 0 && $mdObject->frontpage == 0 ){
				$banners_query = self::$dbConnect->getQuery("SELECT * FROM banners WHERE pageid LIKE '%[". $pid ."]%' AND position = '".$pgposition."' AND publish	= '1' AND status = '1' ORDER BY ordering");
				while( $banners_items = self::$dbConnect->getFetch_Array($banners_query) ){
					$banner_position .= "\n".getDisplayBanner( $banners_items['filename'], $banners_items['fulltxt'], $banners_items['title'], $banners_items['urls'], $banners_items['title'], $banners_items['target_window'], $banners_items['extra_class'], $mdObject->frontpage, false );
				}
			}
			//Sliding Banners for Front page
			elseif( self::$dbConnect->getTotalNumber("banners", "pageid LIKE '%[". $pid ."]%' AND position = '".$pgposition."'") > 0 && $mdObject->frontpage == 1 ){
				$banners_query = self::$dbConnect->getQuery("SELECT * FROM banners WHERE pageid LIKE '%[". $pid ."]%' AND position = '".$pgposition."' AND publish	= '1' AND status = '1' ORDER BY ordering");
				while( $banners_items = self::$dbConnect->getFetch_Array($banners_query) ){
					$banner_position .= "\n".getDisplayBanner( $banners_items['filename'], $banners_items['fulltxt'], $banners_items['title'], $banners_items['urls'], $banners_items['title'], $banners_items['target_window'], $banners_items['extra_class'], $mdObject->frontpage);
				}

				$banner_position = "<!--Bof Front Slider--><div class=\"slider-wrapper\"><div id=\"slider-area\"><!--Bof Actual Banner Sliding-->". $banner_position ."<!--Bof Actual Banner Sliding--></div><div id=\"slider-control-nav\"></div></div><!--Eof Front Slider-->";

			}

		return "<section class=\"content\">". $banner_position ."</section>";
	}
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!