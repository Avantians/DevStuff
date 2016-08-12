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

class getSections {

	public $dbConnect;
	public $basicURL;
	public $static_url;
	public $setJS;
	public $setAllelements;
	public $error_flag;
	public $error_message;
	
	public $sections;
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

		$this->sections				= array();
		$this->tbname				= "sections";
		$this->nodeValue			= "section";
		$this->toolbarTitle			= "Section Manager";
		$this->form					= $this->setJS->setDeleteJScript() . "\n<form id=\"stylizedForm\" name=\"anybovard\" method=\"post\" action=\"%s\" enctype=\"multipart/form-data\">%s\n</form>";
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get contents
 *  ------------------------------------------------------------------------- */
	public function getContents( $gvalue ){

		$_contents  = "\n<!--// Bof Main Contents //-->\n\n". $this->setJS->setExtraJScript() ."\n<dl>\n<dd class=\"pages_boardlist\">";
		$_contents .= $this->getBoardList( $gvalue );
		$_contents .= "\n</dd>\n</dl>\n<!--// Eof Main Contents //-->\n";

		return $_contents;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To board list
 *  ------------------------------------------------------------------------- */
	public function  getBoardList( $gvalue ){

			$pxtra			= isset( $gvalue['p'] ) ? "&p=".$gvalue['p'] : "";
			$_action_url	= $this->basicURL."indexx.php?node={$this->nodeValue}{$pxtra}";

			if( $gvalue['m'] === "update" ){
				$this->dbConnect->getQuery( "UPDATE {$this->tbname} SET status = {$gvalue['st']} WHERE id = '{$gvalue['id']}'" );
				echo $this->setJS->setRedirectJScript( $this->nodeValue, $sxtra, $cxtra, $pxtra );
			}

			$formbtns	= "<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"Update\" /><input type=\"submit\" name=\"submitbutton\" class=\"deletebutton\" value=\"Delete\" /><a href=\"".$this->basicURL."indexx.php?node={$this->nodeValue}{$pxtra}&amp;m=view"."\" class=\"abtn\" onfocus=\"this.blur()\"><span class=\"icon-32-new\" title=\"New\"></span>New</a>&nbsp;&nbsp;";

			$total_list			= $this->dbConnect->getTotalNumber( $this->tbname, "publish = '1'" );
			$total_page		= ceil( $total_list/CONFIG_HOW_MANY_ARTICLES_PER_PAGE );
			$noPage			= isset( $gvalue['p'] ) ?  $gvalue['p'] : 1;
			$first					= $total_list == 0 ? 1 : CONFIG_HOW_MANY_ARTICLES_PER_PAGE *( $noPage-1 );
			$last					= $total_list == 0 ? 0 : CONFIG_HOW_MANY_ARTICLES_PER_PAGE * $noPage;
			$loop_number	= $total_list - ( $noPage-1 ) * CONFIG_HOW_MANY_ARTICLES_PER_PAGE;

			$_boardlist  = $this->setAllelements->setToolbar( $formbtns, "", $this->toolbarTitle );
			$_boardlist .= "\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tr><td valign=\"top\">";
			$_boardlist .= "\n\t<div class=\"totlaitems\">Total of menu items for <strong>{$mtObject->title}</strong>: {$total_list}</div>";
			$_boardlist .= "\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">\n\t<tbody>";
			$_boardlist .= $this->getListFormat( "boardtitle" );

			$list_query = $this->dbConnect->getQuery( "SELECT * FROM {$this->tbname} ORDER BY ordering LIMIT {$first}, ".CONFIG_HOW_MANY_ARTICLES_PER_PAGE."" );
			while ( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
    			$bgClass = ( $loop_number/2 ) == floor( $loop_number/2 ) ? " class=\"list_odd\"" : " class=\"list_even\"";
				$pURL = $this->basicURL."indexx.php?node={$this->nodeValue}&amp;id={$list_values['id']}{$pxtra}&amp;m=view";
				$no_cat = $this->dbConnect->getTotalNumber( "categories", "publish = '1' AND status = '1' AND section = '{$list_values['id']}'" );

				$_boardlist .= sprintf( $this->getListFormat( "boardlist" ), $bgClass, "<input type=\"checkbox\" value=\"{$list_values['id']}\" name=\"boardNum[]\">", $loop_number, $pURL, stripslashes( $list_values['title'] ), stripslashes( $list_values['title'] ), $no_cat, "<input type=\"text\" size=\"3\" name=\"Short[{$list_values['id']}]\" value=\"{$list_values['ordering']}\"  class=\"blue orderarea\">", strtoupper( $list_values['stype'] ), $this->setAllelements->setPublishIcon( $this->nodeValue, $list_values['id'], $list_values['status'] ), $list_values['id'], $list_values['tbname'] );

				$loop_number--;
			}
			$_boardlist .= "\n\t</tbody>\n\t</table>";
			$_boardlist .= $this->setAllelements->setPagelistblock( $this->nodeValue, $total_page, $noPage, $sxtra, $cxtra );
			$_boardlist .= "\n\t</td>\n</tr></table>";

			$_boardlistWithForm = sprintf( $this->form, $_action_url, $_boardlist );

			return $_boardlistWithForm;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get display
 *  ------------------------------------------------------------------------- */
	public function getDisplay( $id ){
		global $Config_notallowed_table;

		$pxtra			= isset( $gvalue['p'] ) ? "&p=".$gvalue['p'] : "";
		$_action_url	= $this->basicURL."indexx.php?node={$this->nodeValue}{$pxtra}";

		if( _getCheckNullorNot( $id ) ){
			$valueObject	= $this->dbConnect->getContents( $this->tbname, "id = '{$id}' AND publish='1'" );
			$buttontxt		= "Update";
			$buttonlink		= "parent.location='".$this->basicURL."indexx.php?node={$this->nodeValue}{$pxtra}'";
			$presstxt 		= "update";
			$extrabtn 		= "<input type=\"submit\" name=\"submitbutton\" class=\"deletebutton\" value=\"Delete\" />";
		} else {
			$buttontxt		= "Create";
			$buttonlink		="parent.location='".$this->basicURL."indexx.php?node={$this->nodeValue}{$pxtra}'";
			$presstxt 		= "create";
		}

		$formbtns	= $extrabtn . "<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"{$buttontxt}\" /> <input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"Apply\" /> <a onClick=\"{$buttonlink}\" class=\"cancelbutton\">Cancel</a>&nbsp;&nbsp;";

		$stype_array = array( array( "id"=>"page","text"=>"PAGE" ), array( "id"=>"casting","text"=>"CASTING" ), array( "id"=>"board","text"=>"BBS" ), array( "id"=>"blog","text"=>"BLOG" ), array( "id"=>"photo","text"=>"PHOTO" ), array( "id"=>"staff","text"=>"STAFF" ), array( "id"=>"forms","text"=>"FORM" ) );

		$_boarditem  = $this->setAllelements->setToolbar( $formbtns, "", $this->toolbarTitle );
		$_boarditem .=  "\n<dl class=\"pages_component\">\n\t<dd>"
								."\n<lable for=\"title\" class=\"darkOrange_txt\">Title:</lable>"
								."\n<input id=\"title\" type=\"text\" name=\"title\" value=\"".$valueObject->title."\" class=\"extra53\"/><br />"

								."\n<lable for=\"uname\" class=\"darkOrange_txt\">Unique name:</lable>"
								."\n<input id=\"uname\" type=\"text\" name=\"uname\" value=\"".$valueObject->name."\" class=\"extra53\"  /><br />"

								."\n<lable for=\"short_description\" class=\"darkOrange_txt\">Short Description:</lable>"
								."\n<input id=\"short_description\" type=\"text\" name=\"short_description\" value=\"".$valueObject->description."\" class=\"extra53\"  /><br />"

								."\n<table border=\"0\"> <tr> <td valign=\"top\" align=\"left\"><lable for=\"tname\" class=\"darkOrange_txt\">Database Table Name:</lable></td> <td valign=\"top\" align=\"left\"><lable for=\"stype\" class=\"darkOrange_txt dtypetxt\">Display type:</lable></td> <td valign=\"top\" align=\"left\"><lable for=\"order\" class=\"darkOrange_txt ordertxt\">Order:</lable></td> </tr> <tr> <td valign=\"top\">".$this->setAllelements->setPullDownMenu( "tname", $this->dbConnect->getTables( $Config_notallowed_table ), $valueObject->tbname, "id=\"tname\" class=\"blue fixedwidth\"" )."&nbsp;&nbsp;&nbsp;</td> <td  valign=\"top\">".$this->setAllelements->setPullDownMenu( "stype", $stype_array ,$valueObject->stype, "id=\"stype\" class=\"blue\"" )."&nbsp;&nbsp;&nbsp;</td> <td><input id=\"order\" type=\"text\" name=\"order\" value=\"".$valueObject->ordering."\" class=\"extra3\"  />&nbsp;&nbsp;&nbsp;</td> </tr> </table><br />";

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

		$pxtra 					= isset( $allValues['p'] ) ? "&p=".$allValues['p'] : "";
		$node 					= $allValues['node'];
		$total_pages			= $this->dbConnect->getTotalNumber( $this->tbname, "publish = '1' AND status = '1'" );
		$allValues['stype'] 	= !empty( $allValues['stype'] ) ? $allValues['stype'] : "bbs";
		$allValues['uname']	= !empty( $allValues['uname'] ) ? $allValues['uname'] : str_replace( " ", "_", trim( strtolower( $allValues['title'] ) ) );
		$allValues['order']	= empty( $allValues['order'] ) ? $total_pages + 1 : $allValues['order'];

		$sql_data_array = array(
											'title'					=> $allValues['title'],
											'name'				=> $allValues['uname'],
											'description'		=> $allValues['short_description'],
											'tbname'				=> $allValues['tname'],
											'stype'				=> $allValues['stype'],
											'publish'				=> 1,
											'status'				=> 1,
											'ordering'			=> $allValues['order'],
											'access_level' 	=> 7,
											'group_level'		=> 1,
											'created_date' 	=> "now()"
											 );

			if( ( $allValues['submitbutton'] === "Create" || $allValues['submitbutton'] === "Apply" ) && $allValues['press'] === "create" ){
				if( !empty( $allValues['title'] ) ){
					$this->dbConnect->getDBAction( $this->tbname, $sql_data_array );
					$inserted_article_id = $this->dbConnect->getInsertID();
				}
				unset( $sql_data_array );

				if( $allValues['submitbutton'] === "Create" ){
					echo $this->setJS->setRedirectJScript( $node, $pxtra );
				} elseif ( $allValues['submitbutton'] === "Apply" ){
					echo $this->setJS->setRedirectJScriptID( $node, $pxtra, "&id={$inserted_article_id}" );
				}

			} elseif( ( $allValues['submitbutton'] === "Update" || $allValues['submitbutton'] === "Apply" ) && $allValues['press'] === "update" ){
				if( !empty( $allValues['title'] ) ){
					$this->dbConnect->getDBAction( $this->tbname, $sql_data_array, "update", "id = {$allValues['id']}" );
				}
				unset( $sql_data_array );

				if( $allValues['submitbutton'] === "Update" ){
					echo $this->setJS->setRedirectJScript( $node, $pxtra );
				} elseif ( $allValues['submitbutton'] === "Apply" ){
					echo $this->setJS->setRedirectJScriptID( $node, $pxtra, "&id={$allValues['id']}" );
				}

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
			$listtxt = "\n\t<tr id=\"board_bar\">"
										."\n\t<th width=\"35\" align=\"center\" class=\"board_line\"><input type=\"checkbox\" value=\"\" name=\"master\" onClick=\"listCheckAll();\"></th>"
										."\n\t<th width=\"15\" align=\"center\" class=\"board_line\"><small>#</small></th>"
										."\n\t<th align=\"center\" class=\"board_line\">Title</th>"
										."\n\t<th width=\"95\" align=\"center\" class=\"board_line\"># Categories</th>"
										."\n\t<th width=\"50\" align=\"center\" class=\"board_line\">Order</th>"
										."\n\t<th width=\"70\" align=\"center\" class=\"board_line\">Style</th>"
										."\n\t<th width=\"50\" align=\"center\" class=\"board_line\">Status</th>"
										."\n\t<th width=\"70\" align=\"center\" class=\"board_line\">ID</th>"
										."\n\t<th width=\"90\"align=\"center\">DB Table</th>"
										."\n\t</tr>";
		} elseif( $opt === "boardlist" ){
			$listtxt =  "\n\t<tr %s>"
										."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
										."\n\t<td class=\"td_bot left_space\" align=\"left\" ><a href=\"%s\" target=\"_self\" title=\"%s\" onfocus=\"this.blur()\">%s</a></td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\">%s</td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
										."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
										."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
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