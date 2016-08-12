<?php
/** -------------------------------------------------------------------------
 * This program is Open Source; you can redistribute it
 * and/or modify it under the terms of the GNU General Public License
 * This program is coded in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY
 * @package  CMS
 * @author      Kenwoo - iweb@kenwoo.ca
 * @license    http://creativecommons.org/licenses/by/2.0/ Creative Commons
 *
 * [v02-02/07/2011]:: Set flag, not allow to direct access
 *  ------------------------------------------------------------------------- */
defined( "_VALID_MOS" ) or die( "Your system is not working properly." );

class getMenutype {

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

		$this->tbname				= "menu_type";
		$this->nodeValue			= "menutype";
		$this->toolbarTitle			= "Menu Manager";
		$this->form					= $this->setJS->setDeleteJScript() . "\n<form id=\"stylizedForm\" name=\"anybovard\" method=\"post\" action=\"%s\" enctype=\"multipart/form-data\">%s\n</form>";
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get display
 *  ------------------------------------------------------------------------- */
	public function getDisplay( $gvalue ){

		$pxtra = isset( $gvalue['p'] ) ? "&p=".$gvalue['p'] : "";

		$_action_url  = $this->basicURL."indexx.php?node={$this->nodeValue}{$pxtra}";

		if( !empty( $gvalue['id'] ) ){
			$valueObject	= $this->dbConnect->getContents( $this->tbname, "id = '{$gvalue['id']}' AND publish='1'" );
			$buttontxt		= "Update";
			$buttonlink		= "parent.location='".$this->basicURL."indexx.php?node={$this->nodeValue}{$pxtra}'";
			$presstxt 		= "update";
			$extrabtn 		= "<input type=\"submit\" name=\"submitbutton\" class=\"deletebutton\" value=\"Delete\" />";

		} else {
			$buttontxt		= "Create";
			$buttonlink		="parent.location='".$this->basicURL."indexx.php?node={$this->nodeValue}{$pxtra}'";
			$presstxt 		= "create";
		}

		$formbtns	= $extrabtn."<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"{$buttontxt}\" /> <a onClick=\"{$buttonlink}\" class=\"cancelbutton\">Cancel</a>&nbsp;&nbsp;";

		$_boarditem  = $this->setAllelements->setToolbar( $formbtns, "", $this->toolbarTitle );
		$_boarditem .=
								"\n<dl class=\"pages_component\">\n\t<dd>"
								."\n<lable for=\"title\" class=\"darkOrange_txt\">Title:</lable>"
								."\n<input id=\"title\" type=\"text\" name=\"title\" value=\"".$valueObject->title."\" class=\"extra53\"/><br />"

								."\n<lable for=\"type\" class=\"darkOrange_txt\">Menu Type:</lable>"
								."\n<input id=\"type\" type=\"text\" name=\"type\" value=\"".$valueObject->type."\" class=\"extra53\"  /><br />";

		$_boarditem .=  "\n<input type=\"hidden\" name=\"id\" value=\"{$valueObject->id}\" />"
								."\n<input type=\"hidden\" name=\"press\" value=\"{$presstxt}\" />"
								."</dd>\n</dl>";

		$_boardlistWithForm = sprintf( $this->form, $_action_url, $_boarditem );

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
		$this->basicURL."/indexx.php?node={$this->nodeValue}{$pxtra}";

		$sql_data_array = array(
								'title'						=> $allValues['title'],
								'type'						=> $allValues['type'],
								'publish'					=> 1
								 );

			if( $allValues['submitbutton'] === "Create" && $allValues['press'] === "create" ){
				if( !empty( $allValues['title'] ) ){
					$this->dbConnect->getDBAction( $this->tbname, $sql_data_array );
				}
				unset( $sql_data_array );
				echo $this->setJS->setRedirectJScript( $node, $pxtra );

			} elseif( $allValues['submitbutton'] === "Update" && $allValues['press'] === "update" ){
				if( !empty( $allValues['title'] ) ){
					$this->dbConnect->getDBAction( $this->tbname, $sql_data_array, "update", "id = {$allValues['id']}" );
				}
				unset( $sql_data_array );
				echo $this->setJS->setRedirectJScript( $node, $pxtra );

			} elseif( $allValues['submitbutton'] === "Delete" ){
				if( !empty( $allValues['id'] ) ){
					if( $allValues['id'] != 1 ){
						$this->dbConnect->getQuery( "DELETE FROM {$this->tbname} WHERE id = '{$allValues['id']}'" );
					} else {
						echo "<script type='text/javascript'>window.alert( 'You cannot delete default menu type.' );</script>";
					}

				}else{
					echo "<script type='text/javascript'>window.alert( 'Please select an Article from the list to delete.' );</script>";
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
										."\n\t<th width=\"35\" align=\"center\" class=\"board_line\"><span style=\"cursor:pointer;\" onClick=\"listCheckAll();\"><small>ALL</small></span></th>"
										."\n\t<th width=\"5\" align=\"center\" class=\"board_line\"></th>"
										."\n\t<th align=\"center\" class=\"board_line\">Title</th>"
										."\n\t<th width=\"250\" align=\"center\" class=\"board_line\">Menu alias</th>"
										."\n\t<th width=\"70\" align=\"center\" class=\"board_line\">Order</th>"
										."\n\t<th width=\"50\" align=\"center\" class=\"board_line\">Status</th>"
										."\n\t<th width=\"50\"align=\"center\">ID</th>"
										."\n\t</tr>";
		} elseif( $opt === "boardlist" ){
			$listtxt =  "\n\t<tr height=\"25\" %s>"
										."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
										."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
										."\n\t<td class=\"td_bot left_space\" align=\"left\" ><a href=\"%s\" target=\"_self\" title=\"MENU: %s\" onfocus=\"this.blur()\">%s</a></td>"
										."\n\t<td class=\"td_bot menualias\" align=\"left\">&nbsp;%s</td>"
										."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
										."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
										."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
										."\n\t</tr>";
		} else {
			$listtxt =  "No Frame";
		}

		return $listtxt;
	}
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!