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
 * [v02-02/07/2011]:: Set flag, not allow to direct access
 *  ----------------------------------------------------------------------- */
defined( "_VALID_MOS" ) or die( "Your system is not working properly." );

require( CONFIG_COMMON . "general.php" );

allMajorFiles(CONFIG_BACKEND_CLASSES);

/** -------------------------------------------------------------------------
 * [00/00/2011]::To connect Db by class bon_batabase
 *  ------------------------------------------------------------------------- */
$Bon_db = new getDatabase( DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME );
$Bon_db->getOpen();

/** -------------------------------------------------------------------------
 * [00/00/2011]::Initialized Global Configuration values from the DataBase
 *  ----------------------------------------------------------------------- */
$configuration_query = $Bon_db->getQuery( "SELECT value_name AS cfgKey,  value AS cfgValue FROM core_opt" );
while ( $configuration = $Bon_db->getFetch_Array( $configuration_query ) ){
	define( $configuration['cfgKey'], $configuration['cfgValue'] );
}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Set the type of request (secure or not) AND get proper base URL
 * REDIRECT_CI_PATH
 *  ----------------------------------------------------------------------- */
$request_type	= (getenv('HTTPS') == 'on') ? 'SSL' : 'NONSSL';
$base_url			= getenv('HTTPS') == 'on' ? CONFIG_SITE_URL_SSL."/backend" : CONFIG_SITE_URL."/backend";
$allElements		= new getAllElements( $base_url );

$osType			= _getOSType(getenv('HTTP_USER_AGENT'));
$browserType	= _getBrowserType(getenv('HTTP_USER_AGENT'));
$user_ip			= _getIP();
$_ipno				= _getDot2LongIP($user_ip);

/** -------------------------------------------------------------------------
 * [00/00/2011]::_SESSION
 *  ------------------------------------------------------------------------- */
ini_set( "session.use_trans_sid", 0 );
ini_set( "url_rewriter.tags","" );
/*
if ( isset( $SESSION_CACHE_LIMITER ) ){
    @session_cache_limiter( $SESSION_CACHE_LIMITER );
}else{
    @session_cache_limiter( "no-cache, must-revalidate" );
}
*/
ini_set( "session.cache_expire", 180 );
ini_set( "session.gc_maxlifetime", 10800 );
ini_set( "session.gc_divisor", 100 );
ini_set( "session.gc_probability", 1 );
ini_set( "session.cookie_lifetime", "0" );
ini_set( "session.cookie_path", "/" );
ini_set( "session.cookie_domain", CONFIG_COOKIE_DOMAIN );
session_set_cookie_params( 0, "/" );
session_save_path( CONFIG_DCOCUMENT_ROOT."_data/_session" );
@session_start();

/** -------------------------------------------------------------------------
 * [00/00/2011]::Reinitialize $_POST and $_GET
 *  ------------------------------------------------------------------------- */
$gvalue	= array();
$xlist		= array("username", "password", );
if ( isset($_POST) && !empty($_POST) ){
		foreach ($_POST as $key => $kw){
				$va					= strtolower($key);
				if ( in_array($va, $xlist) ){
					$gvalue[$va]	= _getSanitize($kw);
				}
				elseif ( $va == "title" ){
					$gvalue[$va]	= _getSanitize($kw, "quot");
				}
				else {
					$gvalue[$va]	= _getSanitize($kw, "simple");
				}
		}
		unset($_POST);
}

if ( isset($_GET) &&  !empty($_GET) ){
	foreach ($_GET as $key => $kw){
		$va				= strtolower($key);
		$gvalue[$va]	= _getSanitize($kw);
	}
	unset($_GET);
}

/** -------------------------------------------------------------------------
 * [00/00/2011]:: change \ to /  |   remove  " /  " from the tail of URL
 *  ------------------------------------------------------------------------- */
$_ci_path = getenv('REQUEST_URI');
$_ci_path = strtr($_ci_path, "\\", "/");
# $_ci_path = preg_replace("/\/$/", "", $_ci_path);

/** -------------------------------------------------------------------------
 * [00/00/2011]::Page Loading Check - Beginning
 *  ------------------------------------------------------------------------- */
$start_time_chktmp 	= explode(' ',microtime());
$start_time_chk			= (double)$start_time_chktmp[1]+$start_time_chktmp[0];
?>