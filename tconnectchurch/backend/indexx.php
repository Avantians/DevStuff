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
 * [v02-02/07/2011]:: Set flag for parent file / Get Document Root Path
 *  ------------------------------------------------------------------------- */
define( "_VALID_MOS", 1 );
define( "CONFIG_DOC_ROOT", preg_replace('/\W\w+\s*(\W*)$/', '$1', realpath(dirname(__FILE__)))  );

if ( !file_exists( CONFIG_DOC_ROOT."/globals.php" ) ) {
		http_response_code(404);		// Set a 400 (bad request) response code and exit.
		echo "There is no constants file.";
		die();
}
else {
		require_once( CONFIG_DOC_ROOT."/globals.php" );
		if ( !file_exists( CONFIG_DOC_ROOT."/config.php" ) || filesize( CONFIG_DOC_ROOT."/config.php" ) < 10 ) {
				http_response_code(404);		// Set a 400 (bad request) response code and exit.
				echo "There is no constants file.";
				die();
		}
		else {
				require_once( CONFIG_DOC_ROOT."/config.php" );
		}	
}
require_once( CONFIG_DOC_ROOT."/common/definetxt.php" );
require_once( CONFIG_BACKEND_CONSTANTS."constants.php" );
require_once( CONFIG_COMMON."lib.php" );

/**
 * KENWOO [06/12/2014] ::
 * NOTE : checking cookie to protect administrator folder
 */
if ( !isset($_SESSION['eWaySession']) && $_SESSION['eWaySession'] !== "eWayPlus". strtotime(date("mdy")) ){
	_getRedirect( CONFIG_SITE_URL );
}

if ( isset( $_SESSION ) && $_SESSION['guest'] == 9 ){
	if ( $_SESSION['session_userlevel'] > 3 ){
		_getRedirect( $base_url );
	}
	else {
		_getCheckingSession( $base_url );
	}
}
else {
	_getRedirect( $base_url."/index.php?node=login" );
}

$backLoad		= new getBackLoad( $base_url );
$all_contents	= $backLoad->setLoading( $_ci_path );

if ( _getISmobile() ){
  $user_agent = ( _getISmobile() && preg_match( "/iPad|iPod|iPhone/", $_SERVER['HTTP_USER_AGENT'] ) ) ? "ios" : "mobile";
	#$Config_template_name =  "mobile";
	$Config_template_name = $all_contents['template'] ? $all_contents['template'] : CONFIG_BACKEND_TEMPLATE;
}
else {
	$Config_template_name = $all_contents['template'] ? $all_contents['template'] : CONFIG_BACKEND_TEMPLATE;
}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Set Header before loading templatefile
 *  --------------------------------------------------------------------- */
header( "Content-Type: text/html; charset=UTF-8" );
header( "Content-Encoding: gzip" );
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0, max-age=0', false );
header( 'Pragma: no-cache' );

/** -------------------------------------------------------------------------
 * [00/00/2011]::Loading template file
 *  ----------------------------------------------------------------------- */
if ( !file_exists( CONFIG_DCOCUMENT_ROOT ."/backend/design/". $Config_template_name ."/index.php" ) ){
	http_response_code(404);		// Set a 400 (bad request) response code and exit.
	echo CONFIG_TEMPLATE_WARN_TXT . $Config_template_name;
	die();
}
else {
	require_once( CONFIG_DCOCUMENT_ROOT ."/backend/design/". $Config_template_name ."/index.php" );
	echo "\n<!-- PVision:". $PHPVERSION ." Start ". time() ." -->";
}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Page Loading Time Check - End
 *  ------------------------------------------------------------------------- */
$end_time_chktmp	= explode( ' ',microtime() );
$end_time_chk		= ( double )$end_time_chktmp[1]+$end_time_chktmp[0];
$loading_chk			= $end_time_chk - $start_time_chk;
echo "\n<!-- Page Loading:". $loading_chk ."-". _getBrowserType( $_SERVER['HTTP_USER_AGENT'] ) ."-". _getOSType( $_SERVER['HTTP_USER_AGENT'] )." -->";

/** -------------------------------------------------------------------------
 * [00/00/2011]::Closign Database connection /
 * Flush ( send ) the output buffer and turn off output buffering /
 * The clearstatcache() function to clear the information that PHP caches about a file.
 *  ----------------------------------------------------------------------- */
unset( $_POST );
unset( $_GET );
clearstatcache();
$Bon_db->getClose();

/** -------------------------------------------------------------------------
 * [00/00/2011]::Flush ( send ) the output buffer and turn off output buffering
 *  ----------------------------------------------------------------------- */
if ( ob_get_length() ){
	@ob_end_flush();
	@flush();
}
exit();
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!