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

$sid_array= explode( ":", $gvalue['sectionid'] );
$gvalue["sectionid"] = end( $sid_array );

if( $gvalue["op"] == "multi" ){
	if( _getCheckNullorNot($gvalue["sectionid"]) ){
		$cateroies_array = $Bon_db->getCategoriesList( "publish = '1' AND status = '1' AND section = '{$gvalue["sectionid"]}' ORDER BY ordering" );
		echo getAllElements::setPullDownMenu( "categoriesid[]", $cateroies_array ,$caValue, "multiple=\"multiple\" id=\"categoriesid\" class=\"blue multiselect fixedwidth\"" );
	}else{
		$cateroies_array = $Bon_db->getCategoriesList( "publish = '1 'AND status = '1' ORDER BY ordering" );
		echo getAllElements::setPullDownMenu( "categoriesid[]", $cateroies_array ,$caValue, "multiple=\"multiple\" id=\"categoriesid\" class=\"blue multiselect fixedwidth\"" );
	}
}else{
	if( $gvalue["sectionid"] ){
		$cateroies_array = $Bon_db->getCategoriesList( "publish = '1' AND status = '1' AND section = '{$gvalue["sectionid"]}' ORDER BY ordering" );
		echo getAllElements::setPullDownMenu( "categoriesid", $cateroies_array ,$vcategoriesid, "id=\"categoriesid\" class=\"blue fixedwidth\"", "", true );
	}else{
		$cateroies_array = $Bon_db->getCategoriesList( "publish = '1 'AND status = '1' AND ( ctype != 'staff' AND ctype != 'page' AND ctype != 'forms' ) ORDER BY ordering" );
		echo getAllElements::setPullDownMenu( "categoriesid", $cateroies_array ,$vcategoriesid, "id=\"categoriesid\" class=\"blue fixedwidth\"", "", true );
	}
}
/*
$cateroies_array = $Bon_db->getCategoriesList( "publish = '1' AND status = '1' AND section = '{$gvalue["sectionid"]}' ORDER BY ordering" );

	if( $gvalue["op"] == "multi" ){
		if( count($cateroies_array) > 10 && count($cateroies_array) <= 15 ){
				$extCSS = " multiselect10";
		} elseif( count($cateroies_array) > 15 ){
				$extCSS = " largemultiselect";
		} else {
				$extCSS = " multiselect";
		}
		$cidSection = "categoriesid[]";
		$mutiSelect = "multiple=\"multiple\"";
	} else {
		$cidSection = "categoriesid";
		$mutiSelect = "";
	}
if( _getCheckNullorNot($gvalue["sectionid"]) ){
	$cateroies_array = $Bon_db->getCategoriesList( "publish = '1' AND status = '1' AND section = '{$gvalue["sectionid"]}' ORDER BY ordering" );
	echo getAllElements::setPullDownMenu( $cidSection, $cateroies_array ,$caValue, $mutiSelect." id=\"categoriesid\" class=\"blue".$extCSS." fixedwidth\"" );
}else{
	echo getAllElements::setPullDownMenu( $cidSection, $cateroies_array ,$caValue, $mutiSelect." id=\"categoriesid\" class=\"blue".$extCSS." fixedwidth\"" );
}*/
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!