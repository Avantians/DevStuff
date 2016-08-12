<?php
/** -------------------------------------------------------------------------
 * This program is Open Source; you can redistribute it
 * and/or modify it under the terms of the GNU General Public License
 * This program is coded in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY
 * @package  CMS
 * @author     Kenwoo - iweb@kenwoo.ca
 * @license    http://creativecommons.org/licenses/by/4.0/ Creative Commons
 *
 * [v01-09/01/2015]:: Set flag, not allow to direct access
 *  ------------------------------------------------------------------------- */
defined( "_VALID_MOS" ) or die( "Your system is not working properly." );

global $Bon_db;

$basic_action			= array("post","edit","delete","reply","register","activation","login","logout");
$_ci_path						= preg_replace("/\/$/", "", $_ci_path);
$_ci_path						= preg_replace("/\&$/", "", $_ci_path);
$pageInfo_array	= explode("/", $_ci_path);

echo "<nav>
<a href=\"". $base_url ."\" title=\"Connect with God Connect with People\" target=\"_self\" class=\"logoclick\" onfocus=\"this.blur()\"><span>Toronto Connect Church</span></a>\n
<a href=\"#\" id=\"pull\" onfocus=\"this.blur()\"> </a>
<ul class=\"top_navigation\">";

if( in_array(end($pageInfo_array), $basic_action) ){
    $_ci_path 		= rtrim(str_replace(end($pageInfo_array), "", $_ci_path), "/");
}

$ci_array = explode("&",$_ci_path);
if(count($ci_array) > 1){
    $_ci_path = str_replace("&".end($ci_array), '', $_ci_path);
}

$ci_array		= explode("/", $_ci_path);
if ( is_numeric(end($ci_array)) )  {
    $_ci_path 		= rtrim(str_replace(end($ci_array), "", $_ci_path), "/");
}

$withoutlast_path = rtrim(str_replace(end($ci_array), "", $_ci_path), "/");

$count = 1;
$mquery = $Bon_db->getQuery("SELECT * FROM menu WHERE menutype = 'topmenu' AND publish = '1' AND status = '1' AND parent = '0' AND hideshow = '1' ORDER BY ordering");
while( $_menus5 = $Bon_db->getFetch_Array($mquery) ){

	$subcount = ( $Bon_db->getTotalNumber("menu", "menutype = 'topmenu' AND publish = '1' AND status = '1' AND parent = '{$_menus5['id']}'") > 0) ? $Bon_db->getTotalNumber("menu", "menutype = 'topmenu' AND publish = '1' AND status = '1' AND parent = '{$_menus5['id']}'") : $subcount;

	if( !empty($_menus5['link']) ){
			$pharse_url = explode("//", $_menus5['link']);
			$plink 					= $pharse_url[0] == "http:" ? $_menus5['link'] : $base_url.$_menus5['link'];
	}
	else {
			$plink 					= $base_url.$_menus5['alias'];
	}

	$addclass = $_ci_path == $_menus5['alias'] ? " class=\"top crt\"" : " class=\"top\"";
	echo "<li class=\"firstLevel cs".$count."\"><a href=\"".$plink."\" title=\"".$_menus5['mtitle']."\" target=\"".$_menus5['target_window']."\"".$addclass.">".$_menus5['mtitle']."</a>"; 
	
	if($Bon_db->getTotalNumber( "menu", "menutype = 'topmenu' AND publish = '1' AND status = '1' AND parent = '{$_menus5['id']}'") > 0){
			$toopen = $withoutlast_path == $_menus5['alias'] ? " class=\"now\"" : "";

			if( empty($toopen) ){
			//$toopen = $maObject->alias == $_menus5['alias'] ? " class=\"now pst".$count."\"" : " class=\"pst".$count."\"";
			}
			$ssublist_query = $Bon_db->getQuery("SELECT * FROM menu WHERE menutype = 'topmenu' AND publish = '1' AND status = '1' AND hideshow = '1' AND parent = '{$_menus5['id']}' ORDER BY ordering");

			echo "\n<ul".$toopen.">\n";
			$subcount = 1;
			while ($ssublist_values = $Bon_db->getFetch_Array($ssublist_query)) {
						if( !empty($ssublist_values['link']) ){
										$subpharse_url	= explode("//", $ssublist_values['link']);
										$subplink						= $subpharse_url[0] == "http:" ? $ssublist_values['link'] : $base_url.$ssublist_values['link'];
						}
						else {
										$subplink						= $base_url.$ssublist_values['alias'];
						}
						$addclass2	= $_ci_path == $ssublist_values['alias'] ? " class=\"crt" : "";
						$childclass		= empty($addclass2) ?  " class=\"chil\"" : $addclass2." chil\"" ;
						echo "<li><a href=\"".$subplink."\" title=\"".$ssublist_values['mtitle']."\" target=\"".$ssublist_values['target_window']."\"".$childclass.">".$ssublist_values['mtitle']."</a></li>\n"; $subcount++;
			}
			echo "</ul>\n</li>\n";
			$subcount = 0;
			$count++;
	}
	else{
			echo "</li>\n";
			$count++;
	}
}
echo "</ul> </nav>";
?>