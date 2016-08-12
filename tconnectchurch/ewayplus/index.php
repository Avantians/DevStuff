<?php
/** -------------------------------------------------------------------------
 * This program is Open Source; you can redistribute it
 * and/or modify it under the terms of the GNU General Public License
 * This program is coded in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY
 * KENWOO [06/12/2014] ::
 * NOTE : set cookie to protect administrator folder
 *  ------------------------------------------------------------------------- */
define( "_VALID_MOS", 1 );
define( "CONFIG_DOC_ROOT", preg_replace('/\W\w+\s*(\W*)$/', '$1', realpath(dirname(__FILE__))) );

/** -------------------------------------------------------------------------
 * [04/15/2013]::When you remove the SUB FOLDER,
 * you have to remove from other files as well. : getDownload.php	UD.02132014
 *  --------------------------------------------------------------------- */
require_once( CONFIG_DOC_ROOT."/globals.php" );
if ( !file_exists( CONFIG_DOC_ROOT."/config.php" ) || filesize( CONFIG_DOC_ROOT."/config.php" ) < 10 ) {
	echo "There is no constants file.";
	exit();
} else {
	require_once( CONFIG_DOC_ROOT."/config.php" );
}
require_once( CONFIG_DOC_ROOT."/common/definetxt.php" );
require_once( CONFIG_CONSTANTS."constants.php" );
require_once( CONFIG_COMMON."lib.php" );

$admin_cookie_code = "eWayPlus". strtotime(date("mdy"));
$_SESSION['eWaySession'] = $admin_cookie_code;
//setcookie("eWaySession", $admin_cookie_code, time()+3600,"/backend"); /* expire in 1 hour 3600*/
_getRedirect( CONFIG_SITE_URL ."/backend" );
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!
