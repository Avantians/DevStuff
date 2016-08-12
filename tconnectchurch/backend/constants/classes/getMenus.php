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

class getMenus {

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

		$this->tbname				= "menu";
		$this->nodeValue			= "menu";
		$this->toolbarTitle			= "Menu Manager";
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

			$tpxtra			= isset( $gvalue['tp'] ) ? "&tp=".$gvalue['tp'] : "";
			$pxtra			= isset( $gvalue['p'] ) ? "&p=".$gvalue['p'] : "";
			$_action_url	= $this->basicURL."indexx.php?node={$this->nodeValue}{$tpxtra}{$pxtra}";

			if( $gvalue['m'] === "update" ){
				$this->dbConnect->getQuery( "UPDATE {$this->tbname} SET status = {$gvalue['st']} WHERE id = '{$gvalue['id']}'" );
				echo $this->setJS->setRedirectJScript( $this->nodeValue, $tpxtra, $pxtra );
			}

			$menutype = "<ul class=\"menutype\">";
			$menutype_query = $this->dbConnect->getQuery( "SELECT * FROM menu_type WHERE publish = '1' ORDER BY id" );
			while ( $menutype_values = $this->dbConnect->getFetch_Array( $menutype_query ) ){
					$menutype_count	= $this->dbConnect->getTotalNumber( $this->tbname, "publish = '1' AND status = '1' AND menutype = '{$menutype_values['type']}'" );
					$mURL					= $this->basicURL."indexx.php?node=menutype&amp;id={$menutype_values['id']}{$tpxtra}{$pxtra}&amp;m=view";
					$mtURL  				= $this->basicURL."indexx.php?node=menu&amp;tp={$menutype_values['type']}{$pxtra}";
					$menutype		   .= "<li><a href=\"{$mtURL}\" target=\"_self\" title=\"VIEW menus under '{$menutype_values['title']}'\" onfocus=\"this.blur()\">".$menutype_values['title']." ( ".$menutype_count." )</a>&nbsp;&nbsp;[<a href=\"{$mURL}\" target=\"_self\" title=\"EDIT - {$menutype_values['title']}\" onfocus=\"this.blur()\">edit</a>]</li>";
			}
			$menutype .= "</ul><br/><a href=\"".$this->basicURL."indexx.php?node=menutype{$pxtra}&amp;m=view"."\" title=\"Create menu type\" target=\"_self\" class=\"smallbutton\" onfocus=\"this.blur()\">Create</a>";

			if( isset( $gvalue['tp'] ) && !empty( $gvalue['tp'] ) ){
				$optail		= " AND menutype = '{$gvalue['tp']}'";
				$mtquery	= "SELECT * FROM menu_type WHERE type = '{$gvalue['tp']}'";
				$mtObject	= $this->dbConnect->getObject( $mtquery );
			}
			else{
				$optail		= " AND menutype = 'topmenu'";
				$mtquery	= "SELECT * FROM menu_type WHERE type = 'topmenu'";
				$mtObject	= $this->dbConnect->getObject( $mtquery );
			}


			$formbtns	= "<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"Update\" /><input type=\"submit\" name=\"submitbutton\" class=\"deletebutton\" value=\"Delete\" /><a href=\"".$this->basicURL."indexx.php?node={$this->nodeValue}{$tpxtra}{$pxtra}&amp;m=view"."\" class=\"abtn\" onfocus=\"this.blur()\"><span class=\"icon-32-new\" title=\"New\"></span>New</a>&nbsp;&nbsp;";

			$total_list = $this->dbConnect->getTotalNumber( $this->tbname, "publish = '1' AND status = '1'{$optail}" );

			$_boardlist  = $this->setAllelements->setToolbar( $formbtns, "", $this->toolbarTitle );
			$_boardlist .= "\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tr><td valign=\"top\">";
			$_boardlist .= "\n\t<div class=\"totlaitems\">Total of menu items for <strong>{$mtObject->mtitle}</strong>: {$total_list}</div>";
			$_boardlist .= "\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">\n\t<tbody>";
			$_boardlist .= $this->getListFormat( "boardtitle" );

			$list_query = $this->dbConnect->getQuery( "SELECT * FROM {$this->tbname} WHERE parent = '0'{$optail} ORDER BY ordering" );
			while ( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
    			$bgClass = ( $total_list/2 ) == floor( $total_list/2 ) ? " class=\"list_odd\"" : " class=\"list_even\"";
				$pURL = $this->basicURL."indexx.php?node={$this->nodeValue}&amp;id={$list_values['id']}{$tpxtra}{$pxtra}&amp;m=view";

				$_boardlist .= sprintf( $this->getListFormat( "boardlist" ), $bgClass, "<input type=\"checkbox\" value=\"{$list_values['id']}\" name=\"boardNum[]\">", "", $pURL, stripslashes( $list_values['mtitle'] ), stripslashes( $list_values['mtitle'] ), $list_values['alias'], "<input type=\"text\" size=\"3\" name=\"Short[{$list_values['id']}]\" value=\"{$list_values['ordering']}\" class=\"blue orderarea\">", $this->setAllelements->setPublishIcon( $this->nodeValue, $list_values['id'], $list_values['status'] ), $list_values['id'] );

					if( $this->dbConnect->getTotalNumber( $this->tbname, "publish = '1' AND parent = '{$list_values['id']}'" ) > 0 ){
						$sublist_query = $this->dbConnect->getQuery( "SELECT * FROM {$this->tbname} WHERE parent = '{$list_values['id']}' ORDER BY ordering" );
						while ( $sublist_values = $this->dbConnect->getFetch_Array( $sublist_query ) ){
							$subpURL = $this->basicURL."indexx.php?node={$this->nodeValue}&amp;id={$sublist_values['id']}{$tpxtra}{$pxtra}&amp;m=view";
							$bgClass_a = $bgClass == " class=\"list_odd\"" ? " class=\"list_odd_a\"" : " class=\"list_even_a\"";
							$_boardlist .= sprintf( $this->getListFormat( "boardlist" ), $bgClass_a, "<input type=\"checkbox\" value=\"{$sublist_values['id']}\" name=\"boardNum[]\">", "", $subpURL, stripslashes( $sublist_values['mtitle'] ), "&nbsp;&#187;&nbsp;".stripslashes( $sublist_values['mtitle'] ), $sublist_values['alias'], "<input type=\"text\" size=\"3\" name=\"Short[{$sublist_values['id']}]\" value=\"{$sublist_values['ordering']}\" class=\"blue orderarea\">", $this->setAllelements->setPublishIcon( $this->nodeValue, $sublist_values['id'], $sublist_values['status'] ), $sublist_values['id'] );

								if( $this->dbConnect->getTotalNumber( $this->tbname, "publish = '1' AND parent = '{$sublist_values['id']}'" ) > 0 ){
									$ssublist_query = $this->dbConnect->getQuery( "SELECT * FROM {$this->tbname} WHERE parent = '{$sublist_values['id']}' ORDER BY ordering" );
									while ( $ssublist_values = $this->dbConnect->getFetch_Array( $ssublist_query ) ){
										$subpURL = $this->basicURL."indexx.php?node={$this->nodeValue}&amp;id={$ssublist_values['id']}{$tpxtra}{$pxtra}&amp;m=view";
										$bgClass_b = $bgClass == " class=\"list_odd\"" ? " class=\"list_odd_b\"" : " class=\"list_even_b\"";
										$_boardlist .= sprintf( $this->getListFormat( "boardlist" ), $bgClass_b, "<input type=\"checkbox\" value=\"{$ssublist_values['id']}\" name=\"boardNum[]\">", "", $subpURL, stripslashes( $ssublist_values['mtitle'] ), "&nbsp;&#187;&#187;&nbsp;".stripslashes( $ssublist_values['mtitle'] ), $ssublist_values['alias'], "<input type=\"text\" size=\"3\" name=\"Short[{$ssublist_values['id']}]\" value=\"{$ssublist_values['ordering']}\" class=\"blue orderarea\">", $this->setAllelements->setPublishIcon( $this->nodeValue, $ssublist_values['id'], $ssublist_values['status'] ), $ssublist_values['id'] );
									}
								}
						}
					}

				$total_list--;
			}
			$_boardlist .= "\n\t</tbody>\n\t</table>\n\t</td><td width=\"225\" valign=\"top\" align=\"left\">";
			$_boardlist .= "\n\t<div class=\"leftsubmenu\"><span class=\"inside_title_eng\">Menu Type</span>";
			$_boardlist .= $menutype;
			$_boardlist .= "\n\t</div>";
			$_boardlist .= "\n\t</td></tr></table>";

			$_boardlistWithForm = sprintf( $this->form, $_action_url, $_boardlist );

			return $_boardlistWithForm;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get display
 *  ------------------------------------------------------------------------- */
	public function getDisplay( $gvalue ){

		$tpxtra = isset( $gvalue['tp'] ) ? "&tp=".$gvalue['tp'] : "";
		$pxtra = isset( $gvalue['p'] ) ? "&p=".$gvalue['p'] : "";

		$_action_url  = $this->basicURL."indexx.php?node={$this->nodeValue}{$tpxtra}{$pxtra}";

		if( _getCheckNullorNot( $gvalue['id'] ) ){
			$valueObject							= $this->dbConnect->getContents( $this->tbname, "id = '{$gvalue['id']}' AND publish='1'" );
			$valueObject_target_window	= $valueObject->target_window;
			$valueObject_menutype			= $valueObject->menutype;
			$valueObject_status				= $valueObject->status;
			$valueObject_hideshow			= $valueObject->hideshow;
			$valueObject_access_level 	= $valueObject->access_level;
			$valueObject_group_level 		= $valueObject->group_level;
			$valueObject_ordering 			= $valueObject->ordering;
			$buttontxt								= "Update";
			$buttonlink								= "parent.location='".$this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$cxtra}{$pxtra}'";
			$presstxt 								= "update";
			$extrabtn 								= "<input type=\"submit\" name=\"submitbutton\" class=\"deletebutton\" value=\"Delete\" />";
		} else{
			$valueObject_target_window	= "_self";
			$valueObject_menutype			= isset( $gvalue['tp'] ) ? $gvalue['tp'] : "topmenu";
			$valueObject_status				= 1;
			$valueObject_hideshow			= 1;
			$valueObject_access_level 	= 7;
			$valueObject_group_level 		= 1;
			$valueObject_ordering 			= $this->dbConnect->getTotalNumber( $this->tbname, "publish != '2' AND status != '2'" ) + 1;
			$buttontxt								= "Create";
			$buttonlink								= "parent.location='".$this->basicURL."indexx.php?node={$this->nodeValue}{$tpxtra}{$pxtra}'";
			$presstxt								= "create";
		}

		$formbtns	= $extrabtn."<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"{$buttontxt}\" /> <input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"Apply\" /> <a onClick=\"{$buttonlink}\" class=\"cancelbutton\">Cancel</a>&nbsp;&nbsp;";

		$menutype_array		= $this->dbConnect->getMenuTypeList( "publish = '1'" );
		$memberGroup_array	= $this->dbConnect->getMemberGroup( "members_group_status = '1' ORDER BY members_group_id" );
		$accessLevel_array	= $this->dbConnect->getAccessLevel( "members_level_status = '1' ORDER BY members_level_id" );
		$menu_array				= $this->dbConnect->getMenuList( "status = '1'" );
		$page_array				= $this->dbConnect->getPageList( "status = '1' AND publish = '1' ORDER BY ordering" );
		$target_array 				= array( array( "id"=>"_blank","text"=>"New Window [New Window]" ), array( "id"=>"_self","text"=>"Same Window [Same Window]" ) );
		$notice_array				= array( array( "id"=>"0","text"=>"NO" ), array( "id"=>"1","text"=>"YES" ) );
		$hideshow_array		= array( array( "id"=>"0","text"=>"Hide" ), array( "id"=>"1","text"=>"Show" ) );

		$_boarditem  = $this->setAllelements->setToolbar( $formbtns, "", $this->toolbarTitle );
		$_boarditem .= $this->setJS->setAjaxJScript( "", "menus" );
		$_boarditem .= "\n<dl class=\"pages_component\">\n\t<dd>";

		$_boarditem .= "\n\t<ul class=\"tabs\">";
		$_boarditem .= "\n\t\t<li><a href=\"#tcontentsa\" onfocus=\"this.blur()\"><span>Article</span></a></li>";
		$_boarditem .= "\n\t\t<li><a href=\"#tcontentsb\" onfocus=\"this.blur()\"><span>Parameters ( Advanced )</span></a></li>";
		$_boarditem .= "\n\t</ul>";

		$_boarditem .= "\n\t<div id=\"tcontentsa\" class=\"tab_content\"><br />";
		$_boarditem .= "\n<table border=\"0\"><tr><td valign=\"top\" align=\"right\"><lable for=\"stype\" class=\"darkOrange_txt txt75\">Menu type:</lable></td><td>".$this->setAllelements->setPullDownMenu( "menutype", $menutype_array ,$valueObject_menutype, "id=\"menutype\" class=\"blue sfixedwidth\"", "", true )."</td><td valign=\"top\" align=\"right\"><lable for=\"parents\" class=\"darkOrange_txt txt110\">Parent Menu:</lable></td><td><div class=\"categoriesid\">".$this->setAllelements->setPullDownMenu( "parents", $menu_array ,$valueObject->parent, "class=\"blue fixedwidth\"", "", true )."</div></td></tr></table>"

								."\n<lable for=\"title\" class=\"darkOrange_txt\">Title:</lable>"
								."\n<input id=\"title\" type=\"text\" name=\"mtitle\" value=\"".$valueObject->mtitle."\" class=\"extra53\"/><br />"

								."\n<lable for=\"alias\" class=\"darkOrange_txt\">Menu Alias:</lable>"
								."\n<input id=\"alias\" type=\"text\" name=\"alias\" value=\"".$valueObject->alias."\" class=\"extra53\"  /><br />"
								."\n<div class=\"speeicalarea\">"
								."\n<lable for=\"pid\" class=\"darkOrange_txt\">Select a target page:</lable>"
								."\n".$this->setAllelements->setPullDownMenu( "pid", $page_array ,$valueObject->pid, "class=\"blue\" style=\"width:517px;\"", "", true )
								."\n <br><small class=\"smallpadding10\">-- OR --</small>"
								."\n<lable for=\"link\" class=\"darkOrange_txt zeromargin-top\">Put a targeted Link:</lable><small>http://www.example.com</small>"
								."\n<input id=\"link\" type=\"text\" name=\"link\" value=\"".$valueObject->link."\" class=\"extra35\"/><br />"
								."\n<lable for=\"target_window\" class=\"darkOrange_txt\">Target Window:</lable>"
								."\n".$this->setAllelements->setPullDownMenu( "target_window", $target_array ,$valueObject_target_window, "class=\"blue\"  style=\"margin-bottom:3px;\"" )."<br />\n</div>"
								."\n<lable for=\"short_description\" class=\"darkOrange_txt\">Short Description:</lable>"
								."\n<input id=\"short_description\" type=\"text\" name=\"short_description\" value=\"".$valueObject->description."\" class=\"extra53\"  /><br />"
								."\n<lable for=\"subclass\" class=\"darkOrange_txt\">Sub class for the menu:</lable>"
								."\n<input id=\"subclass\" type=\"text\" name=\"subclass\" value=\"".$valueObject->subclass."\" class=\"extra53\"  /><br /><br />\n\t</div>";

		$_boarditem .= "\n\t<div id=\"tcontentsb\" class=\"tab_content\"><br />";
		$_boarditem .= "\n<table border=\"0\"><tr><td valign=\"top\" align=\"right\"><lable for=\"access_level\" class=\"darkOrange_txt txt90\">Access Level:</lable></td><td>".$this->setAllelements->setPullDownMenu( "access_level", $accessLevel_array ,$valueObject_access_level, "id=\"members_type\" class=\"blue sfixedwidth\"" )."</td><td valign=\"top\" align=\"right\"><lable for=\"group_level\" class=\"darkOrange_txt txt55\">Group:</lable></td><td>".$this->setAllelements->setPullDownMenu( "group_level", $memberGroup_array ,$valueObject_group_level, "id=\"group_level\" class=\"blue fixedwidth\"" )."</td><td></td></tr></table>\n<table border=\"0\"><tr><td align=\"right\" valign=\"top\"><lable for=\"order\" class=\"darkOrange_txt ordertxt\">Order:</lable></td><td><input id=\"order\" type=\"text\" name=\"order\" value=\"".$valueObject_ordering."\" class=\"extra3\"/></td><td valign=\"top\"><lable for=\"status\" class=\"darkOrange_txt stypetxt\">Status:</lable></td><td>".$this->setAllelements->setPullDownMenu( "status", $notice_array ,$valueObject_status, "id=\"status\" class=\"blue\"" )."</td><td valign=\"top\"><lable for=\"hideshow\" class=\"darkOrange_txt txt90\">Hide/Show:</lable></td><td>".$this->setAllelements->setPullDownMenu( "hideshow", $hideshow_array ,$valueObject_hideshow, "id=\"hideshow\" class=\"blue\"" )."</td></tr></table>\n\t</div>";

		$_boarditem .=  "\n<input type=\"hidden\" name=\"id\" value=\"{$valueObject->id}\" />"
								."\n<input type=\"hidden\" name=\"press\" value=\"{$presstxt}\" />"
								."\n<input type=\"hidden\" name=\"type\" value=\"type\" />"
								."</dd>\n</dl>";

		$_boardlistWithForm = sprintf( $this->form, $_action_url, "\n<script type=\"text/javascript\" src=\"". $this->static_url . CONFIG_STATIC_SUBFOLDER ."/js/back-menu.js\"></script>".$_boarditem );

		$bItems  = "\n<!--// Bof Contents //-->";
		$bItems .= $_boardlistWithForm;
		$bItems .= "\n<!--// Eof Contents //-->\n\n";

		return $bItems;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get create
 *  ------------------------------------------------------------------------- */
	public function getCreate( $allValues ){

		$tpxtra					= isset( $allValues['tp'] ) ? "&tp=".$allValues['tp'] : "";
		$pxtra					= isset( $allValues['p'] ) ? "&p=".$allValues['p'] : "";
		$node					= $allValues['node'];
		$total_pages			= $this->dbConnect->getTotalNumber( $this->tbname, "publish = '1' AND status = '1'" );
		$allValues['order']	= empty( $allValues['order'] ) ? $total_pages + 1 : $allValues['order'];

		$new_string 			= preg_replace( "[^A-Za-z0-9]", "", str_replace( "  ", " ", rtrim( $allValues['mtitle'] ) ) );
		$new_string 			= strtolower( str_replace( " ", "-", rtrim( $new_string ) ) );
		$allValues['alias']	= _getCheckNullorNot( $allValues['alias'] ) ?  "/". trim( $allValues['alias'], "/" ) : "/".$new_string;
		$allValues['alias']	= _getCheckNullorNot( $allValues['link'] ) ?  $allValues['alias'] :  $allValues['alias'];

		$sql_data_array = array(
								'menutype'				=> $allValues['menutype'],
								'mtitle'						=> $allValues['mtitle'],
								'alias'						=> $allValues['alias'],
								'short_description'		=> $allValues['short_description'],
								'subclass'					=> $allValues['subclass'],
								'link'							=> $allValues['link'],
								'target_window'			=> $allValues['target_window'],
								'type'						=> $allValues['type'],
								'sublevel'					=> $allValues['sublevel'],
								'parent'						=> $allValues['parents'],
								'publish'					=> 1,
								'status'						=> $allValues['status'],
								'ordering'					=> $allValues['order'],
								'pid'							=> $allValues['pid'],
								'sid'							=> 0,
								'hideshow'				=> $allValues['hideshow'],
								'group_level'				=> $allValues['group_level'],
								'access_level'			=> $allValues['access_level'],
								'utaccess_level'			=> 1
								 );

			if( ( $allValues['submitbutton'] === "Create" || $allValues['submitbutton'] === "Apply" ) && $allValues['press'] === "create" ){
				if( !empty( $allValues['mtitle'] ) ){
					$this->dbConnect->getDBAction( $this->tbname, $sql_data_array );
					$inserted_article_id = $this->dbConnect->getInsertID();
				}
				unset( $sql_data_array );

				if( $allValues['submitbutton'] === "Create" ){
					echo $this->setJS->setRedirectJScript( $node, $tpxtra, $pxtra );
				} elseif( $allValues['submitbutton'] === "Apply" ){
					echo $this->setJS->setRedirectJScriptID( $node, $sxtra, $cxtra, $pxtra, "&id={$inserted_article_id}" );
				}
			}
			elseif( ( $allValues['submitbutton'] === "Update" || $allValues['submitbutton'] === "Apply" ) && $allValues['press'] === "update" ){
				if( !empty( $allValues['mtitle'] ) ){
					$this->dbConnect->getDBAction( $this->tbname, $sql_data_array, "update", "id = {$allValues['id']}" );
				}
				unset( $sql_data_array );

				if( $allValues['submitbutton'] === "Update" ){
					echo $this->setJS->setRedirectJScript( $node, $tpxtra, $pxtra );
				} elseif( $allValues['submitbutton'] === "Apply" ){
					echo $this->setJS->setRedirectJScriptID( $node, $sxtra, $cxtra, $pxtra, "&id={$allValues['id']}" );
				}

			} elseif( $allValues['submitbutton'] === "Delete" ){
				if( count( $allValues['boardnum'] ) > 0 ){
					foreach ( $allValues['boardnum'] as $key => $kw ){
						$this->dbConnect->getQuery( "DELETE FROM {$this->tbname} WHERE id = '{$kw}'" );
					}

				} else{
					if( !empty( $allValues['id'] ) ){
						$this->dbConnect->getQuery( "DELETE FROM {$this->tbname} WHERE id = '{$allValues['id']}'" );
					} else{
						echo "<script type='text/javascript'>window.alert( 'Please select an Article from the list to delete.' );</script>";
					}
				}
				unset( $allValues );
				echo $this->setJS->setRedirectJScript( $node, $tpxtra, $pxtra );

			} elseif( $allValues['submitbutton'] === "Update" ){
				if( count( $allValues['boardnum'] )>0 ){
					foreach ( $allValues['boardnum'] as $key => $kw ){
						$this->dbConnect->getQuery( "UPDATE {$this->tbname} SET ordering = {$allValues['short'][$kw]} WHERE id = '{$kw}'" );
					}

				} else{
					echo "<script type='text/javascript'>window.alert( 'Please select an Article from the list to update.' );</script>";
				}
				unset( $allValues );
				echo $this->setJS->setRedirectJScript( $node, $tpxtra, $pxtra );
			}
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get List Format
 *  ------------------------------------------------------------------------- */
	public function getListFormat( $opt = "" ){
		if( $opt === "boardtitle" ){
			$listtxt = "\n\t<tr id=\"board_bar\" height=\"25\">"
										."\n\t<th width=\"35\" align=\"center\" class=\"board_line\"><input type=\"checkbox\" value=\"\" name=\"master\" onClick=\"listCheckAll();\"></th>"
										."\n\t<th width=\"5\" align=\"center\" class=\"board_line\"></th>"
										."\n\t<th align=\"center\" class=\"board_line\">Title</th>"
										."\n\t<th width=\"270\" align=\"center\" class=\"board_line\">Menu alias</th>"
										."\n\t<th width=\"50\" align=\"center\" class=\"board_line\">Order</th>"
										."\n\t<th width=\"50\" align=\"center\" class=\"board_line\">Status</th>"
										."\n\t<th width=\"25\"align=\"center\"><small>ID</small></th>"
										."\n\t</tr>";
		} elseif( $opt === "boardlist" ){
			$listtxt =  "\n\t<tr height=\"25\" %s>"
										."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
										."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
										."\n\t<td class=\"td_bot left_space\" align=\"left\" ><a href=\"%s\" target=\"_self\" title=\"MENU: %s\" onfocus=\"this.blur()\">%s</a></td>"
										."\n\t<td class=\"td_bot menualias\" align=\"left\">&nbsp;%s</td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\">%s</td>"
										."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
										."\n\t</tr>";
		} else{
			$listtxt =  "No Frame";
		}

		return $listtxt;
	}
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!