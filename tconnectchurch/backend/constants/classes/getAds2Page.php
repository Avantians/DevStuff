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

class getAds2Page {

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

		$this->tbname				= "advertises_pages";
		$this->toolbarTitle			= "Ads Pages Manager";
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

			$pxtra			= isset( $gvalue['p'] ) ? "&p=".$gvalue['p'] : "";
			$_action_url	= $this->basicURL."indexx.php?node={$this->tbname}{$pxtra}";

			if( $gvalue['m'] === "update" ){
				$this->dbConnect->getQuery( "UPDATE {$this->tbname} SET status = {$gvalue['st']} WHERE id = '{$gvalue['id']}'" );
				echo $this->setJS->setRedirectJScript( "modules", $sxtra, $cxtra, $pxtra );
			}
			else {
				echo $this->setJS->setRedirectJScript( "modules", $sxtra, $cxtra, $pxtra );
			}
/*
			$formbtns	= "<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" onfocus=\"this.blur()\" value=\"Copy\" /><input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"Update\" /><input type=\"submit\" name=\"submitbutton\" class=\"deletebutton\" value=\"Delete\" /><a href=\"".$this->basicURL."indexx.php?node={$this->tbname}{$pxtra}&amp;m=view"."\" class=\"abtn\" onfocus=\"this.blur()\"><span class=\"icon-32-new\" title=\"New\"></span>New</a>&nbsp;&nbsp;";

			$total_list			= $this->dbConnect->getTotalNumber( $this->tbname, "publish = '1'" );
			$total_page		= ceil( $total_list/CONFIG_HOW_MANY_ARTICLES_PER_PAGE );
			$noPage			= isset( $gvalue['p'] ) ?  $gvalue['p'] : 1;
			$first					= $total_list == 0 ? 1 : CONFIG_HOW_MANY_ARTICLES_PER_PAGE *( $noPage-1 );
			$last					= $total_list == 0 ? 0 : CONFIG_HOW_MANY_ARTICLES_PER_PAGE * $noPage;
			$loop_number	= $total_list - ( $noPage-1 ) * CONFIG_HOW_MANY_ARTICLES_PER_PAGE;

			$_boardlist  = $this->setAllelements->setToolbar( $formbtns, "", $this->toolbarTitle );
			$_boardlist .= "\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tr><td valign=\"top\">";
			$_boardlist .= "\n\t<div class=\"totlaitems\">Total of menu items: {$total_list}</div>";
			$_boardlist .= "\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">\n\t<tbody>";
			$_boardlist .= $this->getListFormat( "boardtitle" );

			$list_query = $this->dbConnect->getQuery( "SELECT * FROM {$this->tbname} ORDER BY  ordering LIMIT {$first}, ".CONFIG_HOW_MANY_ARTICLES_PER_PAGE."" );
			while ( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
    			$bgClass	= ( $loop_number/2 ) == floor( $loop_number/2 ) ? " class=\"list_odd\"" : " class=\"list_even\"";
				$pURL		= $this->basicURL."indexx.php?node={$this->tbname}&amp;id={$list_values['id']}{$pxtra}&amp;m=view";

				$query 		= "SELECT title, name FROM advertises_group WHERE name = '{$list_values['ads_group']}'";
				$vObject 	= $this->dbConnect->getObject( $query );

				$_boardlist .= sprintf( $this->getListFormat( "boardlist" ), $bgClass, "<input type=\"checkbox\" value=\"{$list_values['id']}\" name=\"boardNum[]\">", $loop_number, $pURL, stripslashes( $list_values['title'] ), stripslashes( $list_values['title'] ), $vObject->title, "<input type=\"text\" size=\"3\" name=\"Short[{$list_values['id']}]\" value=\"{$list_values['ordering']}\" style=\"width:40px;font-size:11px;text-align:center\">", $this->setAllelements->setPublishIcon( $this->tbname, $list_values['id'], $list_values['status'], $pxtra ), $list_values['id'] );

				$loop_number--;
			}
			$_boardlist .= "\n\t</tbody>\n\t</table>";
			$_boardlist .= $this->setAllelements->setPagelistblock( $this->tbname, $total_page, $noPage, $sxtra, $cxtra );
			$_boardlist .= "\n\t</td></tr></table>";

			$_boardlistWithForm = sprintf( $this->form, $_action_url, $_boardlist );

			return $_boardlistWithForm;
*/
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get display
 *  ------------------------------------------------------------------------- */
	public function getDisplay( $gvalue ){

		$pxtra			= isset( $gvalue['p'] ) ? "&p=".$gvalue['p'] : "";
		$_action_url	= $this->basicURL."indexx.php?node={$this->tbname}{$pxtra}";

		if( _getCheckNullorNot( $gvalue['id'] ) ){
			$valueObject					= $this->dbConnect->getContents( $this->tbname, "id = '{$gvalue['id']}' AND publish='1'" );
			$mpValue						= explode( ";", str_replace( array( "[", "]" ), array( "", "" ), $valueObject->pageid ));

			$buttontxt						= "Update";
			$buttonlink						= "parent.location='".$this->basicURL."indexx.php?node=modules{$pxtra}'";
			$presstxt						= "update";
			$extrabtn						= "<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"Copy\" /><input type=\"submit\" name=\"submitbutton\" class=\"deletebutton\" value=\"Delete\" />";
		} else {
			$buttontxt						= "Create";
			$buttonlink						= "parent.location='".$this->basicURL."indexx.php?node=modules{$pxtra}'";
			$presstxt						= "create";
		}

		$formbtns	= $extrabtn . "<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"{$buttontxt}\" /> <input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"Apply\" /> <a onClick=\"{$buttonlink}\" class=\"cancelbutton\">Cancel</a>&nbsp;&nbsp;";

		$stype_array		= array( array( "id"=>"page","text"=>"PAGE" ), array( "id"=>"casting","text"=>"CASTING" ), array( "id"=>"board","text"=>"BBS" ), array( "id"=>"blog","text"=>"BLOG" ), array( "id"=>"photo","text"=>"PHOTO" ), array( "id"=>"staff","text"=>"STAFF" ) );
		$showtitle_array = array( array( "id"=>"0","text"=>"NO" ), array( "id"=>"1","text"=>"YES" ) );

		$position_array	= $this->dbConnect->getModulePostionList( "ORDER BY title" );
		$group_array		= $this->dbConnect->getAdGroupList( "ORDER BY title" );
		$page_array		= $this->dbConnect->getPageList( "publish = '1' AND status = '1' ORDER BY ordering" );

		$_boarditem  = $this->setAllelements->setToolbar( $formbtns, "", $this->toolbarTitle );
		$_boarditem .=
								"\n<dl class=\"pages_component\">\n\t<dd>"
								."\n<lable for=\"title\" class=\"darkOrange_txt\">Title:</lable>"
								."\n<input id=\"title\" type=\"text\" name=\"title\" value=\"".$valueObject->title."\" class=\"extra53\"/><br />"

								."\n<lable for=\"fulltxt\" class=\"darkOrange_txt\">Text:</lable>"
								."\n<textarea id=\"fulltxt\" name=\"adsfulltxt\" class=\"Threeline\">".$valueObject->adsfulltxt."</textarea><br/><br/>"

								."\n<table border=\"0\" width=\"695\"><tr><td align=\"left\" valign=\"top\" width=\"395\">\n<lable for=\"outsideclass\" class=\"darkOrange_txt\">Outside Class:</lable>\n<input id=\"outsideclass\" type=\"text\" name=\"outsideclass\" value=\"".$valueObject->outsideclass."\" class=\"extra26\"/></td><td align=\"left\" valign=\"top\">\n<lable for=\"innerclass\" class=\"darkOrange_txt\">Inner Class:</lable>\n<input id=\"innerclass\" type=\"text\" name=\"innerclass\" value=\"".$valueObject->innerclass."\" class=\"extra26\"/></td></tr></table>"

								."\n<table border=\"0\" width=\"695\"><tr><td  align=\"left\" valign=\"top\" width=\"255\">\n<lable for=\"ads_group\" class=\"darkOrange_txt\">Advertise group:</lable>\n".$this->setAllelements->setPullDownMenu( "ads_group", $group_array, $valueObject->ads_group, "id=\"ads_group\" class=\"blue sfixedwidth\"", "", true )."<lable for=\"position\" class=\"darkOrange_txt\">Advertise location:</lable>\n".$this->setAllelements->setPullDownMenu( "position", $position_array ,$valueObject->position, "id=\"position\" class=\"blue fixedwidth\"" )."<table border=\"0\"><tr><td valign=\"top\" align=\"right\"><lable for=\"order\" class=\"darkOrange_txt ordertxt\">Order:</lable></td><td><input id=\"order\" type=\"text\" name=\"order\" value=\"".$valueObject->ordering."\" class=\"extra3\" /></td></tr></table></td><td align=\"left\" valign=\"top\">\n<lable for=\"pid\" class=\"darkOrange_txt\">Pages:</lable>\n".$this->setAllelements->setPullDownMenu( "pid[]", $page_array ,$mpValue, "multiple=\"multiple\" size=\"7\" id=\"categoriesid\" class=\"blue halffixedwidth multiselect\"" )."</td></tr></table><br/>"

								."\n<lable for=\"params\" class=\"darkOrange_txt\">Params:</lable>"
								."\n<textarea id=\"params\" name=\"params\" class=\"Threeline\">".$valueObject->params."</textarea><br/>";

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

				$pxtra						= isset( $allValues['p'] ) ? "&p=".$allValues['p'] : "";
				$node						= $allValues['node'];

				if( $allValues['submitbutton'] === "Copy" && $allValues['press'] === "update" ){
						$cdate					= "now()";
						$extra_title			= "Copy - ";
						$allValues['views']	= 0;
						$re_status				= 0;
				}
				elseif( ( $allValues['submitbutton'] === "Update" || $allValues['submitbutton'] === "Apply" ) && $allValues['press'] === "update" ){
					$query								= "SELECT * FROM {$this->tbname} WHERE id = '{$allValues['id']}' AND publish='1'";
					$valueObject						= $this->dbConnect->getObject( $query );
					list( $year, $month, $date )	= explode( '-' , substr( $valueObject->created_date , 0 , 10 ) );
					$cdate 								= $valueObject->created_date;
					$re_status	= _getCheckNullorNot( $allValues['status'] ) ? $allValues['status'] : 1;
				}else{
					$cdate = "now()";
					$re_status	= _getCheckNullorNot( $allValues['status'] ) ? $allValues['status'] : 1;
				}

				for( $j=0; $j < count( $allValues['pid'] ); $j++ ){
					$divider = ( $j == count( $allValues['pid'] )-1 ) ? "":";";
					$pids .= "[".$allValues['pid'][$j]."]".$divider;
				}

				$total_page 				= $this->dbConnect->getTotalNumber( "pages", "publish = '1' AND status = '1'" );
				$allValues['params']	= str_replace( "all#", "", trim( $allValues['params'] ) );
				$allValues_params		= ( count( $allValues['pid'] ) == $total_page ) ? "all#" : "";
				$sql_data_array = array(
																	'title' 					=> $extra_title . $allValues['title'],
																	'adsfulltxt' 			=> $allValues['adsfulltxt'],
																	'pageid' 				=> $pids,
																	'ads_group' 		=> $allValues['ads_group'],
																	'position' 			=> $allValues['position'],
																	'outsideclass' 	=> $allValues['outsideclass'],
																	'innerclass' 		=> $allValues['innerclass'],
																	'ordering' 			=> $allValues['order'],
																	'access_level'	=> 7,
																	'group_level'		=> 1,
																	'params' 			=> $allValues['params'],
																	'publish'				=> 1,
																	'status'				=> $re_status
																	);

			if( ( $allValues['submitbutton'] === "Create" || $allValues['submitbutton'] === "Apply" ) && $allValues['press'] === "create" ){
				if( _getCheckNullorNot( $allValues['title'] ) ){
					$this->dbConnect->getDBAction( $this->tbname, $sql_data_array );
					$inserted_article_id = $this->dbConnect->getInsertID();
					$sef_sql_data_array = array(
															'app_id'			=> $inserted_article_id,
															'app_position'	=> $allValues['position'],
															'app_node'		=> $this->tbname,
															'pageid' 				=> $pids,
															'params'			=> $allValues_params,
															'ordering'		=> $allValues['order']
														 );
					$this->dbConnect->getDBAction( 'apps_pages', $sef_sql_data_array );
					unset( $sef_sql_data_array );
				}
				unset( $sql_data_array );

				if( $allValues['submitbutton'] === "Create" ){
					echo $this->setJS->setRedirectJScript( "modules", $pxtra );
				}
				elseif ( $allValues['submitbutton'] === "Apply" ){
					echo $this->setJS->setRedirectJScriptID( $this->tbname, $pxtra, "&id={$inserted_article_id}" );
				}
			}
			elseif( $allValues['submitbutton'] === "Copy" && $allValues['press'] === "update" ){
					if( _getCheckNullorNot( $allValues['title'] ) ){
						$this->dbConnect->getDBAction( $this->tbname, $sql_data_array );
						$copied_article_id = $this->dbConnect->getInsertID();
						$sef_sql_data_array = array(
																'app_id'			=> $copied_article_id,
																'app_position'	=> $allValues['position'],
																'app_node'		=> $this->tbname,
																'pageid' 				=> $pids,
																'params'			=> $allValues_params,
																'ordering'		=> $allValues['order']
															 );
						$this->dbConnect->getDBAction( 'apps_pages', $sef_sql_data_array );
						unset( $sef_sql_data_array );
					}
					unset( $sql_data_array );
					echo $this->setJS->setRedirectJScript( $node, $sxtra, $cxtra, $pxtra );
			}
			elseif( ( $allValues['submitbutton'] === "Update" || $allValues['submitbutton'] === "Apply" ) && $allValues['press'] === "update" ){
				if( _getCheckNullorNot( $allValues['title'] ) ){
					$this->dbConnect->getDBAction( $this->tbname, $sql_data_array, "update", "id = {$allValues['id']}" );
					$sef_sql_data_array = array(
															'app_id'			=> $allValues['id'],
															'app_position'	=> $allValues['position'],
															'app_node'		=> $this->tbname,
															'pageid' 				=> $pids,
															'params'			=> $allValues_params,
															'ordering'		=> $allValues['order']
														 );
#					$this->dbConnect->getDBAction( 'apps_pages', $sef_sql_data_array );
					$this->dbConnect->getDBAction( 'apps_pages', $sef_sql_data_array, "update", "app_id = {$allValues['id']} AND app_node = '{$this->tbname}'" );
					unset( $sef_sql_data_array );
				}
				unset( $sql_data_array );

				if( $allValues['submitbutton'] === "Update" ){
					echo $this->setJS->setRedirectJScript(  "modules", $pxtra );
				}
				elseif ( $allValues['submitbutton'] === "Apply" ){
					echo $this->setJS->setRedirectJScriptID(  $this->tbname, $pxtra, "&id={$allValues['id']}" );
				}
			}
			elseif( $allValues['submitbutton'] === "Delete" ){
				if( _getCheckNullorNot( $allValues['id'] ) ){
					$this->dbConnect->getQuery( "DELETE FROM {$this->tbname} WHERE id = '{$allValues['id']}'" );
					$this->dbConnect->getQuery( "DELETE FROM apps_pages WHERE app_node = '{$allValues['node']}' AND app_id = '{$allValues['id']}'" );
				}
				else{
					echo "<script type='text/javascript'>window.alert( 'Please select a MODULE from the list to delete.' );</script>";
				}
				unset( $allValues );
				echo $this->setJS->setRedirectJScript( "modules", $pxtra );
			}
/*
			elseif( $allValues['submitbutton'] === "Update" ){
				if( count( $allValues['boardnum'] )>0 ){
					foreach ( $allValues['boardnum'] as $key => $kw ){
						$kw_array	=  explode( '-' , $kw );
						$target_tb	= $kw_array[0];
						$kw 			= end($kw_array);
						$this->dbConnect->getQuery( "UPDATE apps_pages SET ordering = {$allValues['short'][$kw]} WHERE app_node = '{$target_tb}' AND app_id = '{$allValues['id']}'" );
					}
				}
				else {
					echo "<script type='text/javascript'>window.alert( 'Please select a MODULE from the list to update.' );</script>";
				}
				unset( $allValues );
				echo $this->setJS->setRedirectJScript( $node, $pxtra );
			}

			elseif( $allValues['submitbutton'] === "Copy" ){
				if( count( $allValues['boardnum'] ) > 0 ){
					foreach ( $allValues['boardnum'] as $key => $kw ){
						$apObject = $this->dbConnect->getObject( "SELECT * FROM {$this->tbname} WHERE id = '{$kw}'" );
						$copy_sql_data_array = array(
																	'title' 					=> "Copy - ".$apObject->title,
																	'adsfulltxt' 			=> $apObject->adsfulltxt,
																	'pageid' 				=> "",
																	'ads_group' 		=> $apObject->ads_group,
																	'position' 	=> $apObject->position,
																	'outsideclass' 	=> $apObject->outsideclass,
																	'innerclass' 		=> $apObject->innerclass,
																	'ordering' 			=> $apObject->order,
																	'access_level'	=> 7,
																	'group_level'		=> 1,
																	'params' 			=> $apObject->params,
																	'publish'				=> 1,
																	'status'				=> 0
																	 );
						$this->dbConnect->getDBAction( $this->tbname, $copy_sql_data_array );
						$inserted_article_id = $this->dbConnect->getInsertID();
						unset( $copy_sql_data_array );
					}
				}
				else{
					echo "<script type='text/javascript'>window.alert( 'Please select a MODULE from the list to copy.' );</script>";
				}
			}
*/
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get List Format
 *  ------------------------------------------------------------------------- */
	public function getListFormat( $opt = "" ){
		if( $opt === "boardtitle" ){
			$listtxt = "\n\t<tr id=\"board_bar\" height=\"25\">"
                          ."\n\t<th width=\"35\" align=\"center\" class=\"board_line\"><input type=\"checkbox\" value=\"\" name=\"master\" onClick=\"listCheckAll();\"></th>"
                          ."\n\t<th width=\"15\" align=\"center\" class=\"board_line\"><small>#</small></th>"
                          ."\n\t<th align=\"center\" class=\"board_line\">Title</th>"
													."\n\t<th width=\"130\" align=\"center\" class=\"board_line\">Group</th>"
                          ."\n\t<th width=\"70\" align=\"center\" class=\"board_line\">Order</th>"
                          ."\n\t<th width=\"70\" align=\"center\" class=\"board_line\">Status</th>"
                          ."\n\t<th width=\"15\" align=\"center\"><small>ID</small></th>"
                          ."\n\t</tr>";
		}
		elseif( $opt === "boardlist" ){
			$listtxt =  "\n\t<tr height=\"25\" %s>"
                           ."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
                           ."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
                           ."\n\t<td class=\"td_bot left_space\" align=\"left\" ><a href=\"%s\" target=\"_self\" title=\"%s\" onfocus=\"this.blur()\">%s</a></td>"
													 ."\n\t<td class=\"td_bot\" align=\"center\"><small>%s</small></td>"
                           ."\n\t<td class=\"td_bot engtxt\" align=\"center\">%s</td>"
                           ."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
                           ."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
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