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

class getConfigs {

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

		$this->tbname				= "core_opt";
		$this->nodeValue			= "config";
		$this->toolbarTitle			= "Core Option Manager";
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
 * [00/00/2011]::To get board list
 *  ------------------------------------------------------------------------- */
	public function  getBoardList( $gvalue ){
			$_action_url  		= $this->basicURL."indexx.php?node={$this->nodeValue}";

			$formbtns			= "<a href=\"".$this->basicURL."indexx.php?node={$this->nodeValue}&amp;m=view"."\" class=\"abtn\" onfocus=\"this.blur()\"><span class=\"icon-32-new\" title=\"New\"></span>&nbsp;New</a>&nbsp;&nbsp;";
			$total_list 			= $this->dbConnect->getTotalNumber( $this->tbname, "status = '1' AND coreshow = '1'" );
			$noPage 			= isset( $gvalue['p'] ) ?  $gvalue['p'] : 1;
			$loop_number	= $total_list - ( $noPage-1 ) * CONFIG_HOW_MANY_ARTICLES_PER_PAGE;

			$_boardlist  = $this->setAllelements->setToolbar( $formbtns, "", $this->toolbarTitle );
			$_boardlist .= "\n\t<ul class=\"tabs\">";
			$_boardlist .= "\n\t\t<li><a href=\"#tcontentsa\" onfocus=\"this.blur()\"><span>Site</span></a></li>";
			$_boardlist .= "\n\t\t<li><a href=\"#tcontentsb\" onfocus=\"this.blur()\"><span>Page</span></a></li>";
			$_boardlist .= "\n\t\t<li><a href=\"#tcontentsc\" onfocus=\"this.blur()\"><span>Article</span></a></li>";
			$_boardlist .= "\n\t\t<li><a href=\"#tcontentsd\" onfocus=\"this.blur()\"><span>System</span></a></li>";
			$_boardlist .= "\n\t</ul>";
			$_boardlist .= "\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tr><td valign=\"top\">";
			$_boardlist .= "\n\t<div class=\"totlaitems\">Total of menu items for <strong>Core Option</strong>: {$total_list}</div>";
			$_boardlist .= "\n\t<div id=\"tcontentsa\" class=\"tab_content\">\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">\n\t<tbody>";
			$_boardlist .= $this->getListFormat( "boardtitle" );

			$list_query = $this->dbConnect->getQuery( "SELECT * FROM {$this->tbname} WHERE status = '1' AND coregroup = 'a' AND coreshow = '1' ORDER BY ordering" );
			while ( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
    			$bgClass = ( $loop_number/2 ) == floor( $loop_number/2 ) ? " class=\"list_odd\"" : " class=\"list_even\"";
				$pURL = $this->basicURL."indexx.php?node={$this->nodeValue}&amp;id={$list_values['id']}&amp;m=view";

				$_boardlist .= sprintf( $this->getListFormat( "boardlist" ), $bgClass, $pURL, stripslashes( $list_values['title'] ), stripslashes( $list_values['title'] ), _mb_strcut( stripslashes( $list_values['value'] ), 0, 200 ) );

				$loop_number--;
			}
			$_boardlist .= "\n\t</tbody>\n\t</table>\n\t</div>";

			$_boardlist .= "\n\t<div id=\"tcontentsb\" class=\"tab_content\">\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">\n\t<tbody>";
			$_boardlist .= $this->getListFormat( "boardtitle" );

			$list_query = $this->dbConnect->getQuery( "SELECT * FROM {$this->tbname} WHERE status = '1' AND coregroup = 'b' AND coreshow = '1' ORDER BY ordering" );
			while ( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
    			$bgClass = ( $loop_number/2 ) == floor( $loop_number/2 ) ? " class=\"list_odd\"" : " class=\"list_even\"";
				$pURL = $this->basicURL."indexx.php?node={$this->nodeValue}&amp;id={$list_values['id']}&amp;m=view";

				$_boardlist .= sprintf( $this->getListFormat( "boardlist" ), $bgClass, $pURL, stripslashes( $list_values['title'] ), stripslashes( $list_values['title'] ), _mb_strcut( stripslashes( $list_values['value'] ), 0, 200 ) );

				$loop_number--;
			}
			$_boardlist .= "\n\t</tbody>\n\t</table>\n\t</div>";
			$_boardlist .= "\n\t<div id=\"tcontentsc\" class=\"tab_content\">\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">\n\t<tbody>";
			$_boardlist .= $this->getListFormat( "boardtitle" );

			$list_query = $this->dbConnect->getQuery( "SELECT * FROM {$this->tbname} WHERE status = '1' AND coregroup = 'd' AND coreshow = '1' ORDER BY ordering" );
			while ( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
    			$bgClass = ( $loop_number/2 ) == floor( $loop_number/2 ) ? " class=\"list_odd\"" : " class=\"list_even\"";
				$pURL = $this->basicURL."indexx.php?node={$this->nodeValue}&amp;id={$list_values['id']}&amp;m=view";

				$_boardlist .= sprintf( $this->getListFormat( "boardlist" ), $bgClass, $pURL, stripslashes( $list_values['title'] ), stripslashes( $list_values['title'] ), _mb_strcut( stripslashes( $list_values['value'] ), 0, 200 ) );

				$loop_number--;
			}
			$_boardlist .= "\n\t</tbody>\n\t</table>\n\t</div>";
			$_boardlist .= "\n\t<div id=\"tcontentsd\" class=\"tab_content\">\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">\n\t<tbody>";
			$_boardlist .= $this->getListFormat( "boardtitle" );

			$list_query = $this->dbConnect->getQuery( "SELECT * FROM {$this->tbname} WHERE status = '1' AND coregroup = 'c' AND coreshow = '1' ORDER BY ordering" );
			while ( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
    			$bgClass = ( $loop_number/2 ) == floor( $loop_number/2 ) ? " class=\"list_odd\"" : " class=\"list_even\"";
				$pURL = $this->basicURL."indexx.php?node={$this->nodeValue}&amp;id={$list_values['id']}&amp;m=view";

				$_boardlist .= sprintf( $this->getListFormat( "boardlist" ), $bgClass, $pURL, stripslashes( $list_values['title'] ), stripslashes( $list_values['title'] ), _mb_strcut( stripslashes( $list_values['value'] ), 0, 200 ) );

				$loop_number--;
			}
			$_boardlist .= "\n\t</tbody>\n\t</table>\n\t</div>\n\t</td></tr></table>";

			$_boardlistWithForm = sprintf( $this->form, $_action_url, $_boardlist );

			return $_boardlistWithForm;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get display
 *  ------------------------------------------------------------------------- */
	public function getDisplay( $gvalue ){
		$_action_url  = $this->basicURL."indexx.php?node={$this->nodeValue}";
		if( !empty( $gvalue['id'] ) ){
			$valueObject  = $this->dbConnect->getContents( $this->tbname, "id = '{$gvalue['id']}'" );
			$buttontxt		= "Update";
			$buttonlink		="history.go( -1 )";
			$presstxt		= "update";
			$extrabtn		= "";
		} else {
			$buttontxt		= "Create";
			$buttonlink		= "parent.location='".$this->basicURL."indexx.php?node={$this->nodeValue}{$pxtra}'";
			$presstxt		= "create";
			$extrabtn		= "";
		}


		$formbtns		= $extrabtn . "<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"{$buttontxt}\" /> <a onClick=\"{$buttonlink}\" class=\"cancelbutton\">Cancel</a>&nbsp;&nbsp;";
		$notice_array	= array( array( "id"=>"0","text"=>"NO" ), array( "id"=>"1","text"=>"YES" ) );
		$group_array = array( array( "id"=>"a","text"=>"SITE" ), array( "id"=>"b","text"=>"PAGE" ), array( "id"=>"d","text"=>"ARTICLE" ), array( "id"=>"c","text"=>"SYSTEM" ) );

		$_boarditem  = $this->setAllelements->setToolbar( $formbtns, "", $this->toolbarTitle );
		$_boarditem .=  "\n<dl class=\"pages_component\">\n\t<dd>"
								."\n<lable for=\"title\" class=\"darkOrange_txt\">Title:</lable>"
								."\n<input id=\"title\" type=\"text\" name=\"title\" value=\"".$valueObject->title."\" class=\"extra53 readyonly\" readonly/><br />"

								."\n<lable for=\"value\" class=\"darkOrange_txt\">Option Value:</lable>"
								."\n<input id=\"value\" type=\"text\" name=\"value\" value=\"".$valueObject->value."\" class=\"extra53\"  /><br />"

								."\n<lable for=\"name\" class=\"darkOrange_txt\">Short Description:</lable>"
								."\n<input id=\"name\" type=\"text\" name=\"name\" value=\"".$valueObject->name."\" class=\"extra53\"  /><br />"

								."\n<br/><table border=\"0\" align=\"right\"><tr><td width=\"40\" valign=\"top\"><lable for=\"ordering\" class=\"darkOrange_txt ordertxt\">Order:</lable></td><td width=\"50\"><input id=\"ordering\" type=\"text\" name=\"ordering\" value=\"".$valueObject->ordering."\" class=\"extra3\"  /></td><td width=\"90\" valign=\"top\"><lable for=\"coregroup\" class=\"darkOrange_txt dtypetxt\">Group:</lable></td><td width=\"90\">".$this->setAllelements->setPullDownMenu( "coregroup", $group_array ,$valueObject->coregroup, "id=\"coregroup\" class=\"blue\"" )."</td><td valign=\"top\"><lable for=\"status\" class=\"darkOrange_txt dtypetxt\">Status:</lable></td><td width=\"70\">".$this->setAllelements->setPullDownMenu( "status", $notice_array ,$valueObject->status, "id=\"status\"  class=\"blue\"" )."</tr></table><br />";

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
		$node						= $allValues['node'];
		$total_pages				= $this->dbConnect->getTotalNumber( $this->tbname );
		$sql_data_array = array(
											  'title' 				=> $allValues['title'],
											  'value'				=> $allValues['value'],
											  'ordering'			=> empty( $allValues['ordering'] ) ? $total_pages + 1 : $allValues['ordering'],
											  'status'			=> $allValues['status'],
											  'coregroup'		=> $allValues['coregroup'],
											  'name' 			=> $allValues['name']
											 );

			if( $allValues['submitbutton'] === "Update" && $allValues['press'] === "update" ){
				if( !empty( $allValues['title'] ) ){
					$this->dbConnect->getDBAction( $this->tbname, $sql_data_array, "update", "id = {$allValues['id']}" );
				}
				unset( $sql_data_array );

				echo $this->setJS->setRedirectJScript( $node );
			}
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get List Format
 *  ------------------------------------------------------------------------- */
	public function getListFormat( $opt = "" ){
		if( $opt === "boardtitle" ){
			$listtxt = "\n\t<tr id=\"board_bar\" height=\"25\">"
										."\n\t<th width=\"280\" align=\"center\" class=\"board_line\">Title</th>"
										."\n\t<th align=\"center\">Value</th>"
										."\n\t</tr>";
		} elseif( $opt === "boardlist" ){
			$listtxt =  "\n\t<tr height=\"25\" %s>"
										."\n\t<td class=\"td_bot left_space\" align=\"right\" valign=\"top\"><a href=\"%s\" target=\"_self\" title=\"%s\" onfocus=\"this.blur()\">%s</a>&nbsp;&nbsp;</td>"
										."\n\t<td class=\"td_bot\" align=\"left\"><small>&nbsp;&nbsp;%s</small></td>"
										."\n\t</tr>";
		} else {
			$listtxt =  "No Frame";
		}

		return $listtxt;
	}
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!