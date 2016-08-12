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
 * [v02-02/07/2011]:: Set flag for parent file
 *  ----------------------------------------------------------------------- */
define( "_VALID_MOS", 1 );
define( "CONFIG_DOC_ROOT", realpath(dirname(__FILE__)) );

if ( !file_exists( CONFIG_DOC_ROOT."/globals.php" ) ) {
    http_response_code(404);		// Set a 400 (bad request) response code and exit.
    die( "There is no constants file." );
}
else {
    require_once( CONFIG_DOC_ROOT."/globals.php" );
    if ( !file_exists( CONFIG_DOC_ROOT."/config.php" ) || filesize( CONFIG_DOC_ROOT."/config.php" ) < 10 ) {
        http_response_code(404);		// Set a 400 (bad request) response code and exit.
        die( "There is no constants file." );
    }
    else {
            require_once( CONFIG_DOC_ROOT."/config.php" );
    }
}
require_once( CONFIG_DOC_ROOT."/common/definetxt.php" );
require_once( CONFIG_CONSTANTS."constants.php" );
require_once( CONFIG_COMMON."lib.php" );

if ( CONFIG_PROTECT_IP_FRONT === "true" && !_getAccessbyIP( $user_ip ) ){
    _getRedirect( CONFIG_SITE_URL );
}

if( _getCheckNullorNot( UNDER_SUBFOLDER ) ){
    $_ci_path	= str_replace( trim( UNDER_SUBFOLDER, "/" )."/", "", $_ci_path );
}

$kLoad			= new getLoad( $base_url );
$all_contents	= $kLoad->setLoading( $_ci_path );

/** -------------------------------------------------------------------------
 * [00/00/2011]::Set Header before loading templatefile	UD.02132014
 *  ----------------------------------------------------------------------- */
header( "Content-Type: text/html; charset=UTF-8" );
header( "Content-Encoding: gzip" );
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
header( "Last-Modified: " . gmdate( 'D, d M Y H:i:s' ) . " GMT" );
header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Cache-Control: post-check=0, pre-check=0, max-age=0", false );
header( "Pragma: no-cache" );

/** -------------------------------------------------------------------------
 * [00/00/2011]::Loading template file	UD.02132014
 *  ----------------------------------------------------------------------- */
$Config_template_name = $all_contents['design']['template'] ? $all_contents['design']['template'] : CONFIG_FRONT_TEMPLATE;
if ( !file_exists( CONFIG_DCOCUMENT_ROOT ."/design/". $Config_template_name ."/index.php" ) ) {
    // Set a 400 (bad request) response code and exit.
    http_response_code(404);
    echo CONFIG_TEMPLATE_WARN_TXT . $Config_template_name;
    die();
}
else {
    require_once( CONFIG_DCOCUMENT_ROOT ."/design/". $Config_template_name ."/index.php" );
    echo "\n<!--PVision:". $PHPVERSION ." Start ". time() ."-->";
}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Page Loading Time Check - End	UD.02132014
 *  ----------------------------------------------------------------------- */
$end_time_chktmp	= explode( ' ',microtime() );
$end_time_chk		= ( double )$end_time_chktmp[1]+$end_time_chktmp[0];
$loading_chk		= $end_time_chk - $start_time_chk;
echo "\n<!--Page Loading:". $loading_chk ."-".$browserType."-". $osType."-->";

/** -------------------------------------------------------------------------
 * [00/00/2011]::Closign Database connection
 *  clearstatcache() function to clear the information that PHP caches about a file.	UD.02132014
 *  ----------------------------------------------------------------------- */
//clearstatcache();
$Bon_db->getClose();

if ( date('d') == 1 || date('d') == 15 || date('d') == 30){
    $folders = CONFIG_DOC_ROOT. "/static/filemanager/thumbs/";
    _getRemovedir( $folders );
}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Flush the output buffer and turn off output buffering	UD.02132014
 *  ----------------------------------------------------------------------- */
if ( ob_get_length() ){
    @ob_end_flush();
    @flush();
}
exit();
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!
