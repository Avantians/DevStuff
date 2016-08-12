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

class getCategories {

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

		$this->tbname				= "categories";
		$this->nodeValue			= "category";
		$this->toolbarTitle			= "Categories Manager";
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
			$pxtra 			= isset( $gvalue['p'] ) ? "&p=".$gvalue['p'] : "";
			$_action_url 	= $this->basicURL."indexx.php?node={$this->nodeValue}{$pxtra}";

			if( $gvalue['m'] === "update" ){
				$this->dbConnect->getQuery( "UPDATE {$this->tbname} SET status = {$gvalue['st']} WHERE id = '{$gvalue['id']}'" );
				echo $this->setJS->setRedirectJScript( $this->nodeValue, $pxtra );
			}

			$formbtns	= "<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"Update\" /><input type=\"submit\" name=\"submitbutton\" class=\"deletebutton\" value=\"Delete\" /><a href=\"".$this->basicURL."indexx.php?node={$this->nodeValue}{$pxtra}&amp;m=view"."\" class=\"abtn\" onfocus=\"this.blur()\"><span class=\"icon-32-new\" title=\"New\"></span>New</a>&nbsp;&nbsp;";

			$total_list			= $this->dbConnect->getTotalNumber( $this->tbname, "parent = '0'  AND publish = '1'" );
			$total_page		= ceil( $total_list/CONFIG_HOW_MANY_ARTICLES_PER_PAGE );
			$noPage			= isset( $gvalue['p'] ) ?  $gvalue['p'] : 1;
			$first 				= $total_list == 0 ? 1 : CONFIG_HOW_MANY_ARTICLES_PER_PAGE *( $noPage-1 );
			$last 				= $total_list == 0 ? 0 : CONFIG_HOW_MANY_ARTICLES_PER_PAGE * $noPage;
			$loop_number	= $total_list - ( $noPage-1 ) * CONFIG_HOW_MANY_ARTICLES_PER_PAGE;

			$_boardlist  = $this->setAllelements->setToolbar( $formbtns, "", $this->toolbarTitle );
			$_boardlist .= "\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tr><td valign=\"top\">";
			$_boardlist .= "\n\t<div class=\"totlaitems\">Total of menu items: {$total_list}</div>";
			$_boardlist .= "\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">\n\t<tbody>";
			$_boardlist .= $this->getListFormat( "boardtitle" );

			$list_query = $this->dbConnect->getQuery( "SELECT * FROM {$this->tbname} WHERE parent = '0' ORDER BY section, ordering LIMIT {$first}, ".CONFIG_HOW_MANY_ARTICLES_PER_PAGE."" );
			while ( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
    			$bgClass			= ( $loop_number/2 ) == floor( $loop_number/2 ) ? " class=\"list_odd\"" : " class=\"list_even\"";
				$pURL				= $this->basicURL."indexx.php?node={$this->nodeValue}&amp;id={$list_values['id']}{$pxtra}&amp;m=view";
				$section_name	= $this->dbConnect->getSectionsName( $list_values['section'] );

				$_boardlist .= sprintf( $this->getListFormat( "boardlist" ), $bgClass, "<input type=\"checkbox\" value=\"{$list_values['id']}\" name=\"boardNum[]\">", $loop_number, $pURL, stripslashes( $list_values['title'] ), stripslashes( $list_values['title'] ), $section_name, "<input type=\"text\" size=\"3\" name=\"Short[{$list_values['id']}]\" value=\"{$list_values['ordering']}\" class=\"blue orderarea\">", $this->setAllelements->setPublishIcon( $this->nodeValue, $list_values['id'], $list_values['status'] ), $list_values['id'] );

				if( $this->dbConnect->getTotalNumber( $this->tbname, "parent = '{$list_values['id']}'" ) > 0 ){
					$sublist_query = $this->dbConnect->getQuery( "SELECT * FROM {$this->tbname} WHERE parent = '{$list_values['id']}' ORDER BY section, ordering" );
					$subloop_number = 1;
					while ( $sublist_values = $this->dbConnect->getFetch_Array( $sublist_query ) ){
						$subpURL				= $this->basicURL."indexx.php?node={$this->nodeValue}&amp;id={$sublist_values['id']}{$pxtra}&amp;m=view";
						$subsection_name	= $this->dbConnect->getSectionsName( $sublist_values['section'] );

						$_boardlist .= sprintf( $this->getListFormat( "boardlist" ), $bgClass, "<input type=\"checkbox\" value=\"{$sublist_values['id']}\" name=\"boardNum[]\">", $loop_number_, $subpURL, stripslashes( $sublist_values['title'] ), "&nbsp;&nbsp;<div class=\"subsmalltxt\">-".sprintf( "%02d",$subloop_number )."|</div>".stripslashes( $sublist_values['title'] ), $subsection_name, "<input type=\"text\" size=\"3\" name=\"Short[{$sublist_values['id']}]\" value=\"{$sublist_values['ordering']}\" class=\"blue orderarea\">", $this->setAllelements->setPublishIcon( $this->nodeValue, $sublist_values['id'], $sublist_values['status'] ), $sublist_values['id'] );

						$subloop_number++;
					}
				}
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
		$pxtra = isset( $gvalue['p'] ) ? "&p=".$gvalue['p'] : "";

		$_action_url  = $this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$cxtra}{$pxtra}";

		if( _getCheckNullorNot( $id ) ){
			$valueObject = $this->dbConnect->getContents( $this->tbname, "id = '{$id}' AND publish='1'" );
			$buttontxt							= "Update";
			$buttonlink							= "parent.location='".$this->basicURL."indexx.php?node={$this->nodeValue}{$pxtra}'";
			$presstxt 							= "update";
			$extrabtn 							= "<input type=\"submit\" name=\"submitbutton\" class=\"deletebutton\" value=\"Delete\" />";
			$valueObject_access_level	= $valueObject->access_level;
			$valueObject_group_level	= $valueObject->group_level;
		} else {
			$buttontxt								= "Create";
			$buttonlink								="parent.location='".$this->basicURL."indexx.php?node={$this->nodeValue}{$pxtra}'";
			$presstxt 								= "create";
			$valueObject_access_level 	= 7;
			$valueObject_group_level 		= 1;
		}

		$formbtns	= $extrabtn . "<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"{$buttontxt}\" /> <input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"Apply\" /> <a onClick=\"{$buttonlink}\" class=\"cancelbutton\">Cancel</a>&nbsp;&nbsp;";
		
		$dispalytype_array = array( array( "id"=>"page","text"=>"PAGE" ), array( "id"=>"board","text"=>"BBS" ),  array( "id"=>"list_thumb_txt","text"=>"LIST WITH THUMB + DISCRIPTION" ), array( "id"=>"onlythumb","text"=>"ONLY THUMBNAIL" ), array( "id"=>"blog","text"=>"BLOG" ), array( "id"=>"photo","text"=>"PHOTO" ), array( "id"=>"casting","text"=>"CASTING" ),  array( "id"=>"castingthumblist","text"=>"CASTHUMB+LIST" ), array( "id"=>"forms","text"=>"FORM" ) );
		
		$sections_array			= $this->dbConnect->getSectionsList( "publish = '1' AND status = '1' ORDER BY ordering" );
		$cateroies_array		= $this->dbConnect->getCategoriesList( "publish = '1' AND status = '1' ORDER BY ordering" );
		$memberGroup_array	= $this->dbConnect->getMemberGroup( "members_group_status = '1' ORDER BY members_group_id" );
		$accessLevel_array	= $this->dbConnect->getAccessLevel( "members_level_status = '1' ORDER BY members_level_id" );
		$yesno_array				= array( array( "id"=>"0","text"=>"NO" ), array( "id"=>"1","text"=>"YES" ) );
		$rsstype_array			= array( array( "id"=>"text","text"=>"TEXT" ), array( "id"=>"mp3","text"=>"MP3" ), array( "id"=>"vod","text"=>"VOD" ) );

		$_boarditem  = $this->setAllelements->setToolbar( $formbtns, "", $this->toolbarTitle );
		$_boarditem .=  "\n<dl class=\"pages_component\">\n\t<dd>";

		$_boarditem .= "\n\t<ul class=\"tabs\">";
		$_boarditem .= "\n\t\t<li><a href=\"#tcontentsa\" onfocus=\"this.blur()\"><span>Categories</span></a></li>";
#		$_boarditem .= "\n\t\t<li><a href=\"#tcontentsb\" onfocus=\"this.blur()\"><span>RSS Setting</span></a></li>";
		$_boarditem .= "\n\t\t<li><a href=\"#tcontentsc\" onfocus=\"this.blur()\"><span>Parameters ( Advanced )</span></a></li>";
		$_boarditem .= "\n\t</ul>";

		$_boarditem .= "\n\t<div id=\"tcontentsa\" class=\"tab_content\"><br />";

		$_boarditem .= "\n<table border=\"0\"><tr><td valign=\"top\"><lable for=\"stype\" class=\"darkOrange_txt txt80\">Section:</lable></td><td>".$this->setAllelements->setPullDownMenu( "section", $sections_array ,$valueObject->section, "id=\"section\" class=\"blue fixedwidth\"", "", true )."</td><td valign=\"top\"><lable for=\"parents\" class=\"darkOrange_txt txt110\">Parent Category:</lable></td><td>".$this->setAllelements->setPullDownMenu( "parents", $cateroies_array ,$valueObject->parent, "class=\"blue fixedwidth\"", "", true )."</td></tr></table>"

								."\n<lable for=\"title\" class=\"darkOrange_txt\">Title:</lable>"
								."\n<input id=\"title\" type=\"text\" name=\"title\" value=\"".$valueObject->title."\" class=\"extra53\"/><br />"

								."\n<lable for=\"uname\" class=\"darkOrange_txt\">Unique name:</lable>"
								."\n<input id=\"uname\" type=\"text\" name=\"uname\" value=\"".$valueObject->name."\" class=\"extra53\"  /><br />"

								."\n<lable for=\"short_description\" class=\"darkOrange_txt\">Short Description:</lable>"
								."\n<input id=\"short_description\" type=\"text\" name=\"short_description\" value=\"".$valueObject->description."\" class=\"extra53\"  /><br />"

								."\n<br/><table border=\"0\"><tr><td  valign=\"top\"><lable for=\"order\" class=\"darkOrange_txt ordertxt\">Order:</lable></td><td><input id=\"order\" type=\"text\" name=\"order\" value=\"".$valueObject->ordering."\" class=\"extra3\"  />&nbsp;&nbsp;</td><td valign=\"top\"><lable for=\"stype\" class=\"darkOrange_txt dtypetxt\">Display type:</lable></td><td valign=\"top\">".$this->setAllelements->setPullDownMenu( "ctype", $dispalytype_array ,$valueObject->ctype, "id=\"ctype\" class=\"blue\"" )."</td></tr></table><br />\n\t</div>";

#		$_boarditem .= "\n\t<div id=\"tcontentsb\" class=\"tab_content\"><br />"
#					  ."\n<table border=\"0\"><tr><td valign=\"top\"><lable for=\"rssstatus\" class=\"darkOrange_txt txt80\">RSS Status:</lable></td><td>".$this->setAllelements->setPullDownMenu( "rssstatus", $yesno_array ,$valueObject->rssstatus, "id=\"rssstatus\" class=\"blue\"" )."</td><td valign=\"top\"><lable for=\"rssstype\" class=\"darkOrange_txt txt80\">RSS Type:</lable></td><td>".$this->setAllelements->setPullDownMenu( "rssstype", $rsstype_array ,$valueObject->rssstype, "id=\"rssstype\" class=\"blue\"" )."</td></tr></table>"

#						."\n<lable for=\"rsstitle\" class=\"darkOrange_txt\">RSS Title:</lable>"
#						."\n<input id=\"rsstitle\" type=\"text\" name=\"rsstitle\" value=\"".$valueObject->rsstitle."\" class=\"extra53\"/><br />\n\t</div>";

		$_boarditem .= "\n\t<div id=\"tcontentsc\" class=\"tab_content\"><br />"
						."\n<table border=\"0\"><tr><td valign=\"top\"><lable for=\"access_level\" class=\"darkOrange_txt txt90\">Access Level:</lable></td><td>".$this->setAllelements->setPullDownMenu( "access_level", $accessLevel_array ,$valueObject_access_level, "id=\"members_type\" class=\"blue sfixedwidth\"" )."</td><td valign=\"top\"><lable for=\"group_level\" class=\"darkOrange_txt txt55\">Group:</lable></td><td>".$this->setAllelements->setPullDownMenu( "group_level", $memberGroup_array ,$valueObject_group_level, "id=\"group_level\" class=\"blue fixedwidth\"" )."</td></tr></table>\n\t</div>";

		$_boarditem .=  "\n<input type=\"hidden\" name=\"id\" value=\"{$valueObject->id}\" />\n<input type=\"hidden\" name=\"press\" value=\"{$presstxt}\" /></dd>\n</dl>";

		$_boardlistWithForm = sprintf( $this->form, $_action_url, "\n<script type=\"text/javascript\" src=\"". $this->static_url . CONFIG_STATIC_SUBFOLDER ."/js/back-categories.js\"></script>".$_boarditem );

		$bItems  = "\n<!--// Bof Contents //-->";
		$bItems .= $_boardlistWithForm;
		$bItems .= "\n<!--// Eof Contents //-->\n\n";

		return $bItems;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get create
 *  ------------------------------------------------------------------------- */
	public function getCreate( $allValues ){
		$pxtra					= isset( $allValues['p'] ) ? "&p=".$allValues['p'] : "";
		$node 					= $allValues['node'];
		$total_pages 			= $this->dbConnect->getTotalNumber( $this->tbname, "publish = '1' AND status = '1'" );
		$allValues['stype']	= !empty( $allValues['stype'] ) ? $allValues['stype'] : "bbs";
		$allValues['uname']	= !empty( $allValues['uname'] ) ? $allValues['uname'] : str_replace( " ", "_", trim( strtolower( $allValues['title'] ) ) );
		$allValues['order'] 	= empty( $allValues['order'] ) ? $total_pages + 1 : $allValues['order'];

		if( ( $allValues['submitbutton'] === "Update" || $allValues['submitbutton'] === "Apply" ) && $allValues['press'] === "update" ){
			$allValues['rsstitle'] = _getCheckNullorNot( $allValues['rsstitle'] ) ? $allValues['rsstitle'] : $allValues['title'];
		}
		$sql_data_array = array(
								'ctype'				=> $allValues['ctype'],
								'parent'				=> $allValues['parents'],
								'title'					=> $allValues['title'],
								'name'				=> $allValues['uname'],
								'image'				=> $allValues['image'],
								'section'				=> $allValues['section'],
								'description'		=> $allValues['short_description'],
								'rssstatus'			=> $allValues['rssstatus'],
								'rssstype'			=> $allValues['rssstype'],
								'rsstitle'				=> $allValues['rsstitle'],
								'publish'				=> 1,
								'status'				=> 1,
								'ordering'			=> $allValues['order'],
								'access_level'	=> $allValues['access_level'],
								'group_level'		=> $allValues['group_level'],
								'params'				=> "",
								'created_date'		=> "now()"
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
										."\n\t<th width=\"130\" align=\"center\" class=\"board_line\">Section Name</th>"
										."\n\t<th width=\"50\" align=\"center\" class=\"board_line\">Order</th>"
										."\n\t<th width=\"50\" align=\"center\" class=\"board_line\">Status</th>"
										."\n\t<th width=\"15\"align=\"center\"><small>ID</small></th>"
										."\n\t</tr>";
		} elseif( $opt === "boardlist" ){
			$listtxt =  "\n\t<tr %s>"
										."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
										."\n\t<td class=\"td_bot left_space\" align=\"left\" ><a href=\"%s\" target=\"_self\" title=\"%s\" onfocus=\"this.blur()\">%s</a></td>"
										."\n\t<td class=\"td_bot\" align=\"center\"><small>%s</small></td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\">%s</td>"
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