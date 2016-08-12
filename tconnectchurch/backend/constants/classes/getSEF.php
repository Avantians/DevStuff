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
 *  ------------------------------------------------------------------------- */
defined( "_VALID_MOS" ) or die( "Your system is not working properly." );

class getSEF {

	public $dbConnect;
	public $basicURL;
	public $static_url;
	public $setJS;
	public $setAllelements;
	public $error_flag;
	public $error_message;

	public $tbname;
	public $nodeValue;
	public $toolbarTitle;
	public $form;

	public function __construct( $baseURL ){
		global $Bon_db, $allElements;

		$this->dbConnect	 		= $Bon_db;
		$this->basicURL			= $baseURL;
		$this->static_url 			= rtrim( str_replace( "www", CONFIG_STATIC_SUBDOMAIN, str_replace( "/backend", "", $this->basicURL ) ), "/" );
		$this->setJS					= new getJavascripts($this->basicURL);
		$this->setAllelements	 	= $allElements;
		$this->error_flag				= false;
		$this->error_message		= array();

		$this->tbname				= "opensef";
		$this->nodeValue			= "sef";
		$this->toolbarTitle			= "SEF Manager";
		$this->form					= $this->setJS->setDeleteJScript() . "\n<form id=\"stylizedForm\" name=\"anybovard\" method=\"post\" action=\"%s\" enctype=\"multipart/form-data\">%s\n</form>";
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get contents
 *  ------------------------------------------------------------------------- */
	public function getContents( $gvalue ){

		$_contents  = "\n<!--// Bof Main Contents //-->\n\n".$this->setJS->setExtraJScript()."\n<dl>";
		$_contents .= "\n<dd class=\"pages_boardlist\">";
		$_contents .= $this->getBoardList( $gvalue );
		$_contents .= "\n</dd>";
		$_contents .= "\n</dl>\n<!--// Eof Main Contents //-->\n";

		return $_contents;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get board list
 *  ------------------------------------------------------------------------- */
	public function  getBoardList( $gvalue ){

			$pxtra = isset( $gvalue['p'] ) ? "&p=".$gvalue['p'] : "";

			$_action_url  = $this->basicURL."indexx.php?node={$this->nodeValue}{$pxtra}";

			if( $gvalue['m'] === "update" ){
				$this->dbConnect->getQuery( "UPDATE {$this->tbname} SET status = {$gvalue['st']} WHERE id = '{$gvalue['id']}'" );
				echo $this->setJS->setRedirectJScript( $this->nodeValue, $pxtra );
			}

			if( !empty( $gvalue['txt'] ) && $gvalue['search'] === "Search" ){
				$searchtxt = " AND external  LIKE  '%" . $gvalue['txt'] ."%'";
				# echo $this->setJS->setRedirectJScript( $this->nodeValue, $pxtra );
			}

			$formbtns	= "<input type=\"submit\" name=\"submitbutton\" class=\"deletebutton\" value=\"Delete\" />&nbsp;&nbsp;";

			$total_list = $this->dbConnect->getTotalNumber( $this->tbname, "publish = '1'".$searchtxt );
			$total_page = ceil( $total_list/CONFIG_HOW_MANY_ARTICLES_PER_PAGE );
			$noPage = isset( $gvalue['p'] ) ?  $gvalue['p'] : 1;
			$first = $total_list == 0 ? 1 : CONFIG_HOW_MANY_ARTICLES_PER_PAGE *( $noPage-1 );
			$last = $total_list == 0 ? 0 : CONFIG_HOW_MANY_ARTICLES_PER_PAGE * $noPage;
			$loop_number = $total_list - ( $noPage-1 ) * CONFIG_HOW_MANY_ARTICLES_PER_PAGE;

			$_boardlist  = $this->setAllelements->setToolbar( $formbtns, "", $this->toolbarTitle );
			$_boardlist .= "\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">";
			$_boardlist .= "\n\t<tr><td colspan=\"2\" valign=\"top\" align=\"left\">";
			$_boardlist .= "\n\t<input id=\"txt\" type=\"text\" name=\"txt\" value=\"\" class=\"extra180\"/>&nbsp;&nbsp;<input type=\"submit\" name=\"search\" class=\"submittxt\" value=\"Search\" /></td></tr>";
			$_boardlist .= "\n\t<tr><td valign=\"top\">";
			$_boardlist .= "\n\t<div class=\"totlaitems\">Total of menu items: {$total_list}</div>";
			$_boardlist .= "\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">";
			$_boardlist .= "\n\t<tbody>";
			$_boardlist .= $this->getListFormat( "boardtitle" );

			$list_query = $this->dbConnect->getQuery( "SELECT o.id as id, m.alias as alias, o.external as external, o.pid as pid, m.id as mid, o.tbname as tbname, o.tid as tid FROM {$this->tbname} o LEFT JOIN menu m ON o.pid = m.pid WHERE o.publish='1'{$searchtxt} ORDER BY o.id DESC LIMIT {$first}, ".CONFIG_HOW_MANY_ARTICLES_PER_PAGE."" );
			while ( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
    			$bgClass = ( $loop_number/2 ) == floor( $loop_number/2 ) ? " class=\"list_odd\"" : " class=\"list_even\"";
				$pURL = $this->basicURL."indexx.php?node={$this->nodeValue}&amp;id={$list_values['id']}&amp;m=view";
				$previewURL = "<a href=\"".CONFIG_SITE_URL . $list_values['alias']. stripslashes( $list_values['external'] )."\" target=\"_blank\" title=\"Preview this page\" onfocus=\"this.blur()\">VIEW</a>";
				$mtquery = "SELECT sectionid, categoriesid FROM pages WHERE id = '{$list_values['pid']}'";
				$mtObject = $this->dbConnect->getObject( $mtquery );

				$sectionName = $this->dbConnect->getSectionsName( $mtObject->sectionid );
				$categoryName = $this->dbConnect->getCategoriesName( $mtObject->categoriesid );

				$_boardlist .= sprintf( $this->getListFormat( "boardlist" ), $bgClass, "<input type=\"checkbox\" value=\"{$list_values['id']}\" name=\"boardNum[]\">", $loop_number, $pURL, stripslashes( $list_values['external'] ), stripslashes( $list_values['external'] ),  $previewURL, $sectionName, $categoryName, $list_values['tbname'], strtoupper( $list_values['tid'] ), $list_values['id'] );
				$loop_number--;
			}
			$_boardlist .= "\n\t</tbody>";
			$_boardlist .= "\n\t</table>";
			$_boardlist .= $this->setAllelements->setPagelistblock( $this->nodeValue, $total_page, $noPage, $sxtra, $cxtra );
			$_boardlist .= "\n\t</td></tr></table>";

			$_boardlistWithForm = sprintf( $this->form, $_action_url, $_boardlist );

			return $_boardlistWithForm;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get display
 *  ------------------------------------------------------------------------- */
	public function getDisplay( $gvalue ){

		$pxtra = isset( $gvalue['p'] ) ? "&p=".$gvalue['p'] : "";

		$_action_url  = $this->basicURL."indexx.php?node={$this->nodeValue}{$pxtra}";

		if( !empty( $gvalue['id'] ) ){
			$valueObject 	= $this->dbConnect->getContents( $this->tbname, "id = '{$gvalue['id']}' AND publish='1'" );

			$mtquery			= "SELECT title, sectionid, categoriesid FROM pages WHERE id = '{$valueObject->pid}'";
			$mtObject			= $this->dbConnect->getObject( $mtquery );
			$sectionName	= $this->dbConnect->getSectionsName( $mtObject->sectionid );
			$categoryName	= $this->dbConnect->getCategoriesName( $mtObject->categoriesid );
			$pageTitle 		= stripslashes( $mtObject->title );

			$buttontxt			= "Update";
			$buttonlink			= "parent.location='".$this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$pxtra}{$gxtra}'";
			$presstxt 			= "update";
			$extrabtn 			= "<input type=\"submit\" name=\"submitbutton\" class=\"deletebutton\" value=\"Delete\" />";
		} else {
			$buttontxt			= "Create";
			$buttonlink			= "parent.location='".$this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$pxtra}{$gxtra}'";
			$presstxt 			= "create";
		}

		$formbtns	= $extrabtn . "<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"{$buttontxt}\" /> <a onClick=\"{$buttonlink}\" class=\"cancelbutton\">Cancel</a>&nbsp;&nbsp;";

		$_boarditem  = $this->setAllelements->setToolbar( $formbtns, "", $this->toolbarTitle );
		$_boarditem .=   "\n<dl class=\"pages_component\">\n\t<dd>"
								."\n<lable for=\"external\" class=\"darkOrange_txt\">Friendly URL:</lable>"
								."\n<input id=\"external\" type=\"text\" name=\"external\" value=\"".$valueObject->external."\" class=\"extra53\"/><br />"

								."\n<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"695\"><tr><td  align=\"left\" valign=\"top\" width=\"395\">\n<lable for=\"section\" class=\"darkOrange_txt\">Section name:</lable>\n<input id=\"section\" type=\"text\" name=\"section\" value=\"".$sectionName."\" class=\"extra26 readyonly\"  readonly=\"readonly\"/></td><td align=\"left\" valign=\"top\"><lable for=\"category\" class=\"darkOrange_txt\">Category Name:</lable>\n<input id=\"category\" type=\"text\" name=\"category\" value=\"".$categoryName."\" class=\"extra26 readyonly\"  readonly=\"readonly\"/></td></tr></table>"

								."\n<lable for=\"tname\" class=\"darkOrange_txt\">Database Table Name:</lable>"
								."\n<input id=\"tname\" type=\"text\" name=\"tname\" value=\"".$valueObject->tbname."\" class=\"extra26 readyonly\"  readonly=\"readonly\"/><br />"

								."\n<lable for=\"page\" class=\"darkOrange_txt\">Page:</lable>"
								."\n<input id=\"page\" type=\"text\" name=\"page\" value=\"".$pageTitle."\" class=\"extra53 readyonly\"  readonly=\"readonly\"/><br />"

								."\n<lable for=\"notes\" class=\"darkOrange_txt\" >Note:</lable>"
								."\n<textarea id=\"notes\" name=\"notes\" class=\"Threeline\">".$valueObject->notes."</textarea><br/><br/>";

		$_boarditem .=  "\n<input type=\"hidden\" name=\"id\" value=\"{$valueObject->id}\" />"
								."\n<input type=\"hidden\" name=\"press\" value=\"{$presstxt}\" />"
								."</dd>\n</dl>";

		$_boardlistWithForm = sprintf( $this->form, $_action_url, "\n<script type=\"text/javascript\" src=\"". $this->static_url . CONFIG_STATIC_SUBFOLDER ."/js/back-sections.js\"></script>".$_boarditem );

		$bItems  = "\n<!--// Bof Contents //-->";
		$bItems .= $_boardlistWithForm;
		$bItems .= "\n<!--// Eof Contents //-->\n\n";

		return $bItems;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get create
 *  ------------------------------------------------------------------------- */
	public function getCreate( $allValues ){

		$pxtra = isset( $allValues['p'] ) ? "&p=".$allValues['p'] : "";
		$node = $allValues['node'];
		$sql_data_array = array(
												'external'			=> $allValues['external'],
												'notes'				=> $allValues['notes'],
												'modified_date'	=> "now()"
											 );

			if( $allValues['submitbutton'] === "Create" && $allValues['press'] === "create" ){
				if( !empty( $allValues['external'] ) ){
					$this->dbConnect->getDBAction( $this->tbname, $sql_data_array );
				}
				unset( $sql_data_array );
				echo $this->setJS->setRedirectJScript( $node, $pxtra );
			} elseif( $allValues['submitbutton'] === "Update" && $allValues['press'] === "update" ){
				if( !empty( $allValues['external'] ) ){
					$this->dbConnect->getDBAction( $this->tbname, $sql_data_array, "update", "id = {$allValues['id']}" );
				}
				unset( $sql_data_array );
				echo $this->setJS->setRedirectJScript( $node, $pxtra );
			} elseif( $allValues['submitbutton'] === "Delete" ){
				if( count( $allValues['boardnum'] ) > 0 ){
					foreach ( $allValues['boardnum'] as $key => $kw ){
						$this->dbConnect->getQuery( "DELETE FROM {$this->tbname} WHERE id = '{$kw}'" );
					}
				}else{
					if( !empty( $allValues['id'] ) ){
						$this->dbConnect->getQuery( "DELETE FROM {$this->tbname} WHERE id = '{$allValues['id']}'" );
					}else{
						echo "<script type='text/javascript'>window.alert( 'Please select an Article from the list to delete.' );</script>";
					}
				}
				unset( $allValues );
				echo $this->setJS->setRedirectJScript( $node, $pxtra );
			} elseif( $allValues['submitbutton'] === "Update" ){
				if( count( $allValues['boardnum'] )>0 ){
					foreach ( $allValues['boardnum'] as $key => $kw ){
						$this->dbConnect->getQuery( "UPDATE {$this->tbname} SET ordering = {$allValues['short'][$kw]} WHERE id = '{$kw}'" );
					}
				} else {
					echo "<script type='text/javascript'>window.alert( 'Please select an Article from the list to update.' );</script>";
				}
				unset( $allValues );
				echo $this->setJS->setRedirectJScript( $node, $pxtra );
			}
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get List Format
 *  ------------------------------------------------------------------------- */
	public function getListFormat( $opt = "" ){
		if( $opt === "boardtitle" ){
			$listtxt = "\n\t<tr id=\"board_bar\" height=\"25\">"
										."\n\t<th width=\"35\" align=\"center\" class=\"board_line\"><input type=\"checkbox\" value=\"\" name=\"master\" onClick=\"listCheckAll();\"></th>"
										."\n\t<th width=\"50\" align=\"center\" class=\"board_line\"><small>#</small></th>"
										."\n\t<th align=\"center\" class=\"board_line\">Friendly URL</th>"
										."\n\t<th width=\"60\"align=\"center\" class=\"board_line\">Preview</th>"
										."\n\t<th width=\"90\"align=\"center\" class=\"board_line\">Section</th>"
										."\n\t<th width=\"90\"align=\"center\" class=\"board_line\">Category</th>"
										."\n\t<th width=\"50\" align=\"center\" class=\"board_line\">Table</th>"
										."\n\t<th width=\"50\" align=\"center\" class=\"board_line\">tID</th>"
										."\n\t<th width=\"50\" align=\"center\">ID</th>"
										."\n\t</tr>";
		} elseif( $opt === "boardlist" ){
			$listtxt =  "\n\t<tr height=\"25\" %s>"
										."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
										."\n\t<td class=\"td_bot left_space\" align=\"left\" ><a href=\"%s\" target=\"_self\" title=\"%s\" onfocus=\"this.blur()\">%s</a></td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small</td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
										."\n\t</tr>";
		} else {
			$listtxt =  "No Frame";
		}

		return $listtxt;
	}
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!