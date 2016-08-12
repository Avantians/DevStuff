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

class getTemplates {

	public $dbConnect;
	public $basicURL;
	public $static_url;
	public $setJS;
	public $setAllelements;
	public $error_flag;
	public $error_message;

	public $design_folders;
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

		$this->design_folders		= allDesign( CONFIG_DESIGN );
		$this->tbname				= "menu_templates";
		$this->nodeValue			= "templates";
		$this->toolbarTitle			= "Template Manager";
		$this->form					= $this->setJS->setDeleteJScript() . "\n<form id=\"stylizedForm\" name=\"anybovard\" method=\"post\" action=\"%s\" enctype=\"multipart/form-data\">%s\n</form>";
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get contents
 *  ------------------------------------------------------------------------- */
	public function getContents( $gvalue ){

		$_contents  = "\n<!--// Bof Main Contents //-->\n\n". $this->setJS->setExtraJScript() ."\n<dl>";
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

			$tpxtra = isset( $gvalue['tp'] ) ? "&tp=".$gvalue['tp'] : "";
			$pxtra = isset( $gvalue['p'] ) ? "&p=".$gvalue['p'] : "";
			$_action_url  = $this->basicURL."indexx.php?node={$this->nodeValue}{$tpxtra}{$pxtra}";

			$formbtns	= "<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"Default\" />&nbsp;&nbsp;";

			$total_list = count( allDesign( CONFIG_DESIGN ) );
			$file_array = allDesign( CONFIG_DESIGN );

			$_boardlist  = $this->setAllelements->setToolbar( $formbtns, "", $this->toolbarTitle );
			$_boardlist .= "\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tr><td valign=\"top\">";
			$_boardlist .= "\n\t<div class=\"totlaitems\">Total of template items for <strong>{$mtObject->mtitle}</strong>: {$total_list}</div>";
			$_boardlist .= "\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">\n\t<tbody>";
			$_boardlist .= $this->getListFormat( "boardtitle" );

			for ( $k=0; $k < count( $file_array ); $k++ ){
				$dTemplateObject = $this->dbConnect->getContents( "core_opt", "value_name = 'CONFIG_FRONT_TEMPLATE' AND status='1'" );
				$sValue = $dTemplateObject->value == $file_array[$k]['id'] ? "<span class=\"orangetxt\">Default</span>" : "none";

				if ( $sValue == "none" ){
					$sValue = $this->dbConnect->getTotalNumber( $this->tbname, "publish = '1' AND status = '1' AND template = '".$file_array[$k]['id']."'" ) > 0 ? "Active" : "NONE";
				}

    			$bgClass = ( $total_list/2 ) == floor( $total_list/2 ) ? " class=\"list_odd\"" : " class=\"list_even\"";
				$pURL = $this->basicURL."indexx.php?node={$this->nodeValue}&amp;id={$file_array[$k]['id']}{$tpxtra}{$pxtra}&amp;m=view";

				$_boardlist .= sprintf( $this->getListFormat( "boardlist" ), $bgClass, "<input type=\"checkbox\" value=\"{$file_array[$k]['id']}\" name=\"boardNum[]\">", $total_list, $pURL, stripslashes( $file_array[$k]['text'] ), stripslashes( $file_array[$k]['text'] ), $sValue );

				$total_list--;
			}
			$_boardlist .= "\n\t</tbody>\n\t</table>\n\t</td></tr></table>";

			$_boardlistWithForm = sprintf( $this->form, $_action_url, $_boardlist );

			return $_boardlistWithForm;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get display
 *  ------------------------------------------------------------------------- */
	public function getDisplay( $gvalue ){

		$pxtra = isset( $gvalue['p'] ) ? "&p=".$gvalue['p'] : "";

		$_action_url  = $this->basicURL."indexx.php?node={$this->nodeValue}{$pxtra}";

		if ( _getCheckNullorNot( $gvalue['id'] ) ){
			$valueObject	= $this->dbConnect->getContents( $this->tbname, "template = '{$gvalue['id']}' AND publish='1'" );
			$mdValue 	= $this->dbConnect->getMenuDesign( "template = '{$valueObject->template}'" );

			$buttontxt		= "Update";
			$buttonlink		= "parent.location='".$this->basicURL."indexx.php?node={$this->nodeValue}{$pxtra}'";
			$presstxt 		= "create";
			$extrabtn 		= "";
		}
		else{
			$buttontxt		= "Create";
			$buttonlink		= "parent.location='".$this->basicURL."indexx.php?node={$this->nodeValue}{$pxtra}'";
			$presstxt 		= "create";
		}

		$notice_array	= array( array( "id"=>"0","text"=>"NO" ), array( "id"=>"1","text"=>"YES" ) );
		$disabled				= ( $gvalue['id'] == CONFIG_FRONT_TEMPLATE ) ? " disabled" : "";
		$setdefault			= ( $gvalue['id'] == CONFIG_FRONT_TEMPLATE ) ? " 1" : "0";

		$menuArray	= $this->dbConnect->getMenuList( "publish = '1' AND status = '1'" );
		$formbtns		= $extrabtn."<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"{$buttontxt}\" /><input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"Apply\" /><a onClick=\"{$buttonlink}\" class=\"cancelbutton\">Cancel</a>&nbsp;&nbsp;";

		$_boarditem  = $this->setAllelements->setToolbar( $formbtns, "", $this->toolbarTitle );
		$_boarditem .= "\n<dl class=\"pages_component\">\n\t<dd>"
								."\n<lable for=\"title\" >Design Name: <span class=\"normaltxt orangetxt\">".strtoupper( $gvalue['id'] )."</span></lable><br />\n"
								."\n<lable for=\"type\" class=\"darkOrange_txt\">Menu:</lable>"
								."\n".$this->setAllelements->setPullDownMenu( "menuid[]", $menuArray , $mdValue, "multiple=\"multiple\" size=\"7\" id=\"menuid\" class=\"blue largemultiselect flexiblewidth\"", $disabled, true );
		$_boarditem .=  "\n<input type=\"hidden\" name=\"template\" value=\"{$gvalue['id']}\" />"
								."\n<input type=\"hidden\" name=\"id\" value=\"{$valueObject->id}\" />"
								."\n<input type=\"hidden\" name=\"press\" value=\"{$presstxt}\" />"
								."</dd>\n</dl>";

		$_boardlistWithForm = sprintf( $this->form, $_action_url, $_boarditem );

		$bItems  = "\n<!--// Bof Contents //-->". $_boardlistWithForm ."\n<!--// Eof Contents //-->\n\n";

		return $bItems;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get create
 *  ------------------------------------------------------------------------- */
	public function getCreate( $allValues ){

		$pxtra = isset( $allValues['p'] ) ? "&p=".$allValues['p'] : "";
		$node = $allValues['node'];
		$this->basicURL."/indexx.php?node={$this->nodeValue}{$pxtra}";

		if ( ( $allValues['submitbutton'] == "Update" || $allValues['submitbutton'] == "Apply" ) && $allValues['press'] == "create" ){

			if ( count( $allValues['menuid'] ) > 0 ){
				if ( count( $allValues['menuid'] ) == 1 && !_getCheckNullorNot( $allValues['menuid'][0] ) ){
					$this->dbConnect->getQuery( "DELETE FROM {$this->tbname} WHERE template = '".$allValues['template']."'" );
				}
				else {
					if ( $this->dbConnect->getTotalNumber( $this->tbname, "publish = '1' AND status = '1' AND template = '".$allValues['template']."'" ) > 0 ){
						$this->dbConnect->getQuery( "DELETE FROM {$this->tbname} WHERE template = '".$allValues['template']."'" );
					}
					foreach ( $allValues['menuid'] as $key => $menuid ){
						$sql_data_array = array(
																'template'	=> $allValues['template'],
																'menuid'	=> $menuid,
																'publish'		=> 1,
																'status'		=> 1,
																'ordering'	=> $allValues['order']
															 );
						$this->dbConnect->getDBAction( $this->tbname, $sql_data_array );
					}
					unset( $sql_data_array );
				}
			}

			if ( $this->dbConnect->getTotalNumber( $this->tbname ) == 0 ){
				$this->dbConnect->getQuery( "TRUNCATE TABLE  `{$this->tbname}`" );
			}

			if ( $allValues['submitbutton'] == "Update" ){
				echo $this->setJS->setRedirectJScript( $node, $pxtra );
			}
			elseif ( $allValues['submitbutton'] == "Apply" ){
				echo $this->setJS->setRedirectJScriptID( $node, $pxtra, "&id={$allValues['template']}" );
			}
		}
		elseif ( $allValues['submitbutton'] === "Default" ){
			if ( count( $allValues['boardnum'] ) == 1 ){
				foreach ( $allValues['boardnum'] as $key => $value ){
					$this->dbConnect->getQuery( "UPDATE core_opt SET value = '{$value}' WHERE value_name = 'CONFIG_FRONT_TEMPLATE'" );
					if ( $this->dbConnect->getTotalNumber( $this->tbname, "publish = '1' AND status = '1' AND template = '".$value."'" ) > 0 ){
						$this->dbConnect->getQuery( "DELETE FROM {$this->tbname} WHERE template = '".$value."'" );
					}
				}
			}
			elseif ( count( $allValues['boardnum'] ) > 1 ){
				echo "<script type='text/javascript'>window.alert( 'Select ONLY ONE Template from the list to set as Default.' );</script>";
			}
			else{
				echo "<script type='text/javascript'>window.alert( 'Please select an Template from the list to set as Default.' );</script>";
			}
			unset( $allValues );
			echo $this->setJS->setRedirectJScript( $node, $pxtra );
		}
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get List Format
 *  ------------------------------------------------------------------------- */
	public function getListFormat( $opt = "" ){
		if ( $opt === "boardtitle" ){
			$listtxt = "\n\t<tr id=\"board_bar\" height=\"25\">"
										."\n\t<th width=\"35\" align=\"center\" class=\"board_line\"><input type=\"checkbox\" value=\"\" name=\"master\" onClick=\"listCheckAll();\"></th>"
										."\n\t<th width=\"5\" align=\"center\" class=\"board_line\"></th>"
										."\n\t<th align=\"center\" class=\"board_line\">Title</th>"
										."\n\t<th width=\"100\" align=\"center\">Assigned</th>"
										."\n\t</tr>";
		}
		elseif ( $opt === "boardlist" ){
			$listtxt =  "\n\t<tr height=\"25\" %s>"
										 ."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
										 ."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
										 ."\n\t<td class=\"td_bot left_space\" align=\"left\" ><a href=\"%s\" target=\"_self\" title=\"%s\" onfocus=\"this.blur()\">%s</a></td>"
										 ."\n\t<td class=\"td_bot\" align=\"center\"><small>%s</small></td>"
										 ."\n\t</tr>";
		}
		else {
			$listtxt =  "No Frame";
		}

		return $listtxt;
	}
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!