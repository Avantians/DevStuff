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
 * [v02-02/07/2011]:: Set flag which is not allow no direct access
 *  ----------------------------------------------------------------------- */
define( "_VALID_MOS", 1 );
define( "CONFIG_DOC_ROOT", dirname(dirname(__FILE__)) );

$Config_document_root = getenv( "DOCUMENT_ROOT" );
require_once( CONFIG_DOC_ROOT.'/globals.php' );

if ( !file_exists( CONFIG_DOC_ROOT.'/config.php' ) || filesize( CONFIG_DOC_ROOT.'/config.php' ) < 10 ) {
	echo "There is no constants file.";
	exit();
} else {
	require_once( CONFIG_DOC_ROOT.'/config.php' );
}

require_once( CONFIG_DOC_ROOT."/common/definetxt.php" );
require_once( CONFIG_DOC_ROOT."/common/lib.php" );
require_once( CONFIG_DOC_ROOT."/constants/constants.php" );

if( $gvalue["op"] == "multi" ){
	if( _getCheckNullorNot($gvalue["menutype"]) ){
		$menu_array = $Bon_db->getMenuList( "publish = '1' AND status = '1' AND menutype ='{$gvalue["menutype"]}' ORDER BY ordering" );
		echo getAllElements::setPullDownMenu( "parents[]", $menu_array ,$vaparents, "multiple=\"multiple\" class=\"blue multiselect fixedwidth\"", "", true );
	} else {
		$menu_array = $Bon_db->getMenuList( "publish = '1' AND status = '1' ORDER BY ordering" );
		echo getAllElements::setPullDownMenu( "parents[]", $menu_array ,$vaparents, "multiple=\"multiple\" class=\"blue multiselect fixedwidth\"", "", true );
	}

} else {
	if( $gvalue["menutype"] ){
		$menu_array = $Bon_db->getMenuList( "publish = '1' AND status = '1' AND menutype ='{$gvalue["menutype"]}' ORDER BY ordering" );
		echo getAllElements::setPullDownMenu( "parents", $menu_array ,$vaparents, "class=\"blue fixedwidth\"", "", true );
	} else {
		$menu_array = $Bon_db->getMenuList( "publish = '1' AND status = '1' ORDER BY ordering" );
		echo getAllElements::setPullDownMenu( "parents", $menu_array ,$vaparents, "class=\"blue fixedwidth\"", "", true );
	}
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!