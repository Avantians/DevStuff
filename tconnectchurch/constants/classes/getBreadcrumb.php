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

class getBreadcrumb {

	public $dbConnect;
	public $basicURL;

	public function __construct( $basicURL ){
		global $Bon_db;

		$this->dbConnect	= $Bon_db;
		$this->basicURL	= $basicURL;
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To create breadcrumb
 *  ----------------------------------------------------------------------- */
	public function setBreadcrumb(){
		global $_ci_path;

		$basic_action		= array( "post","edit","delete","reply","register","activation","login","logout" );
		$breadcrumb_path			= preg_replace( "/\/$/", "", $_ci_path );
		$breadcrumb_path			= preg_replace( "/\&$/", "", $breadcrumb_path );
		$pageInfo_array		= explode("/", $breadcrumb_path);

		if ( in_array(end($pageInfo_array), $basic_action) ){
			$do 			= end($pageInfo_array);
			$breadcrumb_path 		= rtrim(str_replace($do, "", $breadcrumb_path), "/");
		}

		$ci_array = explode("&",$breadcrumb_path);
		if (count($ci_array) > 1){
			if ( is_numeric(end($ci_array)) )  {
				$breadcrumb_path = str_replace("&".end($ci_array), '', $breadcrumb_path);
				$xno = "&".end($ci_array);
			}
			else {
				$xno = "";
			}
		}

		$menu_array	= explode("/", $breadcrumb_path);
		$article_id = end($menu_array);
		if ( is_numeric($article_id)){
			$breadcrumb_path 	= rtrim(str_replace($article_id, "", $breadcrumb_path), "/");
		}
		else {
			$article_id ="-+-";
			$breadcrumb_path = $breadcrumb_path;
		}

		$count = count($menu_array);
		for ($i = $count; $i > 0; $i --){
			foreach ($menu_array as $key => $pt) {
				if ($key != 0){
					$newone .= "/".$pt;
				}
			}

			$iclass = $i == $count ? " class=\"last\"" : "";
			if ( !empty($newone) ){
				if ($this->dbConnect->getTotalNumber("menu", "publish = '1' AND status = '1' AND alias = '{$newone}'") > 0){
					$ids_qry = "SELECT mtitle, alias, link FROM menu WHERE publish = '1' AND status = '1' AND alias = '{$newone}'";
					$valueObject = $this->dbConnect->getObject( $ids_qry );
					$valueObject->alias = empty($valueObject->link) ? $valueObject->alias : $valueObject->link;
				}
				else {
					if ($this->dbConnect->getTotalNumber("opensef", "publish = '1' AND external LIKE '%{$article_id}%'") > 0){
						$ids_qry = "SELECT tid, tbname, pid FROM opensef WHERE publish = '1' AND external LIKE '%{$article_id}%'";
						$idsObject = $this->dbConnect->getObject( $ids_qry );

						$menuqry = "SELECT title FROM {$idsObject->tbname} WHERE publish = '1' AND status = '1' AND id = '{$idsObject->tid}'";
						$valueObject = $this->dbConnect->getObject( $menuqry );
					}
				}

				$link =  $i == $count ? $valueObject->mtitle : "<a href=\"".$this->basicURL.$valueObject->alias ."\" title=\"".$valueObject->mtitle."\" target=\"_self\">".$valueObject->mtitle."</a>";
				$insidetxt .= "<li". $iclass.">".$link ."</li>";
			}
			else {
				$insidetxt .= "<li class=\"home\"><a href=\"".$this->basicURL."\" title=\"Home\" target=\"_self\">HOME</a></li>";
			}
			unset($menu_array[$i - 1]);
			unset($newone);
		}

		$breadcrumbmenu = "<ul>\n".$insidetxt."\n</ul>";

		return $breadcrumbmenu;
	}
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!