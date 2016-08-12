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

class getBanners {

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

		$this->tbname				= "banners";
		$this->nodeValue			= "banner";
		$this->toolbarTitle			= "Banner Manager";
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
			$pxtra = isset( $gvalue['p'] ) ? "&p=".$gvalue['p'] : "";

			if ( ( isset( $gvalue['pid'] ) && ( int )$gvalue['pid'] >= 0 ) ){
					$optail = " AND pageid LIKE '%[".$gvalue['pid']."]%'";

					if ( $gvalue['pid'] != 0 ){
						$mtquery			= "SELECT * FROM pages WHERE id = '{$gvalue['pid']}'";
						$mtObject			= $this->dbConnect->getObject( $mtquery );
						$mtObject_title	= $mtObject->title;
					}
					else {
						$mtObject_title = "Selected Page( s )";
					}
					$sxtra = "&pid=".$gvalue['pid'];
			}
			else {
				$fpquery 			= "SELECT id FROM pages WHERE publish = '1' AND status = '1' AND frontpage = '1'";
				$fpObject			= $this->dbConnect->getObject( $fpquery );
				$optail				= " AND pageid LIKE '%[".$fpObject->id."]%'";
				$mtObject_title 	= "HOME";
			}
			$_action_url  = $this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$pxtra}";

			if ( $gvalue['m'] === "update" ){
				$this->dbConnect->getQuery( "UPDATE {$this->tbname} SET status = {$gvalue['st']}, modified_date = 'now()' WHERE id = '{$gvalue['id']}'" );
				echo $this->setJS->setRedirectJScript( $this->nodeValue, $sxtra, $cxtra, $pxtra );
			}

			$menutype	= "<ul class=\"menutype\">";
			$p_count		= $this->dbConnect->getTotalNumber( $this->tbname, "publish = '1' AND status = '1'" );
			$mURL  		= $this->basicURL."indexx.php?node=page&amp;id=0&amp;m=view";
			$mtURL  		= $this->basicURL."indexx.php?node={$this->nodeValue}&amp;pid=0";
			$menutype   .= "<li><a href=\"{$mtURL}\" target=\"_self\" title=\"View banners for selected page( s )\" onfocus=\"this.blur()\">Selected Page( s ) ( ".$p_count." )</a></li>";

			$menutype_query = $this->dbConnect->getQuery( "SELECT * FROM pages WHERE status = '1' ORDER BY ordering" );
			while ( $menutype_values = $this->dbConnect->getFetch_Array( $menutype_query ) ){
				$menutype_count	= $this->dbConnect->getTotalNumber( $this->tbname, "publish = '1' AND status = '1' AND pageid LIKE '%[".$menutype_values['id']."]%'" );
				if ( $menutype_count > 0 ){
					$mURL				= $this->basicURL."indexx.php?node=page&amp;id={$menutype_values['id']}&amp;m=view";
					$mtURL				= $this->basicURL."indexx.php?node={$this->nodeValue}&amp;pid={$menutype_values['id']}";
					$menutype	   .= "<li><a href=\"{$mtURL}\" target=\"_self\" title=\"View banners for {$menutype_values['title']}\" onfocus=\"this.blur()\">".$menutype_values['title']." ( ".$menutype_count." )</a>&nbsp;[<a href=\"{$mURL}\" target=\"_self\" title=\"EDIT-{$menutype_values['title']} page\" onfocus=\"this.blur()\">edit</a>]</li>";
				}
			}

			$menutype .= "</ul><br/><a href=\"".$this->basicURL."indexx.php?node=page&amp;m=view"."\"  title=\"Create new page\" target=\"_self\" class=\"smallbutton\" onfocus=\"this.blur()\">Create page</a>";

			$formbtns	= "<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"Update\" /><input type=\"submit\" name=\"submitbutton\" class=\"deletebutton\" value=\"Delete\" /><a href=\"".$this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$pxtra}&amp;m=view"."\" class=\"abtn\" onfocus=\"this.blur()\"><span class=\"icon-32-new\" title=\"New\"></span>New</a>&nbsp;&nbsp;";

			$total_list 			= $this->dbConnect->getTotalNumber( $this->tbname, "publish = '1'{$optail}" );
			$total_items 		= $total_list;
			$total_page 		= ceil( $total_list/CONFIG_HOW_MANY_ARTICLES_PER_PAGE );
			$noPage 			= isset( $gvalue['p'] ) ?  $gvalue['p'] : 1;
			$first 				= $total_list == 0 ? 1 : CONFIG_HOW_MANY_ARTICLES_PER_PAGE *( $noPage-1 );
			$last					= $total_list == 0 ? 0 : CONFIG_HOW_MANY_ARTICLES_PER_PAGE * $noPage;
			$loop_number	= $total_list - ( $noPage-1 ) * CONFIG_HOW_MANY_ARTICLES_PER_PAGE;

			$_boardlist  = $this->setAllelements->setToolbar( $formbtns, "", $this->toolbarTitle );
			$_boardlist .= "\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tr><td valign=\"top\">";
			$_boardlist .= "\n\t<div class=\"totlaitems\">Total of menu items for <strong>{$mtObject_title}</strong>: {$total_items}</div>";
			$_boardlist .= "\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">";
			$_boardlist .= "\n\t<tbody>";
			$_boardlist .= $this->getListFormat( "boardtitle" );

			$list_query = $this->dbConnect->getQuery( "SELECT * FROM {$this->tbname} WHERE publish = '1'{$optail} ORDER BY ordering, created_date DESC LIMIT {$first}, ".CONFIG_HOW_MANY_ARTICLES_PER_PAGE."" );
			while ( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
    			$bgClass			= ( $loop_number/2 ) == floor( $loop_number/2 ) ? " class=\"list_odd\"" : " class=\"list_even\"";
				$pURL				= $this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}&amp;id={$list_values['id']}{$pxtra}&amp;m=view";
				$pageTitle			= ( $list_values['pid'] != 0 ) ? $this->dbConnect->getPageName( $list_values['pid'] ) : "PAGE";
				$created_date	= _getShortFormatDate( $list_values['created_date'] );

				$_boardlist .= sprintf( $this->getListFormat( "boardlist" ), $bgClass, "<input type=\"checkbox\" value=\"{$list_values['id']}\" name=\"boardNum[]\">", $loop_number, $pURL, stripslashes( $list_values['title'] ), stripslashes( $list_values['title'] ), $pURL, stripslashes( $list_values['title'] ), getDisplayImgBK( $list_values['filename'], 150, false, $list_values['title'], false ), $list_values['urls'], $pageTitle, "<input type=\"text\" size=\"3\" name=\"Short[{$list_values['id']}]\" value=\"{$list_values['ordering']}\" style=\"width:40px;font-size:11px;text-align:center\">", $this->setAllelements->setPublishIcon( $this->nodeValue, $list_values['id'], $list_values['status'], $sxtra ), $list_values['id'] );

				$loop_number--;
			}

			$_boardlist .= "\n\t</tbody>";
			$_boardlist .= "\n\t</table>";
			$_boardlist .= $this->setAllelements->setPagelistblock( $this->nodeValue, $total_page, $noPage, $sxtra, $cxtra );
			$_boardlist .= "\n\t</td><td width=\"225\" valign=\"top\" align=\"left\">";
			$_boardlist .= "\n\t<div class=\"leftsubmenu\"><span class=\"inside_title_eng\">Pages</span>";
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
		$sxtra			= isset( $gvalue['pid'] ) ? "&pid=".$gvalue['pid'] : "";
		$pxtra			= isset( $gvalue['p'] ) ? "&p=".$gvalue['p'] : "";

		$_action_url	= $this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$cxtra}{$pxtra}";

		if ( !empty( $gvalue['id'] ) ){
			$valueObject 	= $this->dbConnect->getContents( $this->tbname, "id = '{$gvalue['id']}' AND publish='1'" );
			$mpValue						= explode( ";", str_replace( array( "[", "]" ), array( "", "" ), $valueObject->pageid ));
			$buttontxt			= "Update";
			$buttonlink			= "parent.location='".$this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$pxtra}'";
			$presstxt 			= "update";
			$extrabtn 			= "<input type=\"submit\" name=\"submitbutton\" class=\"deletebutton\" value=\"Delete\" />";
			$vsectionid 				= $valueObject->sectionid;
			$vcategoriesid			= $valueObject->categoriesid;
			$valueObject_position	= $valueObject->position;
		}
		else {
			$buttontxt						= "Create";
			$presstxt 						= "create";
			$valueObject_position		= "top_banner";
			$buttonlink						= "parent.location='".$this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$pxtra}'";
		}

		$position_array 	= $this->dbConnect->getModulePostionList( "ORDER BY title" );
		$page_array 		= $this->dbConnect->getPageList( "publish = '1' AND status = '1' ORDER BY ordering" );
		$target_array 	= array( array( "id"=>"_blank","text"=>"New Window [새창]" ), array( "id"=>"_self","text"=>"Same Window [현재창]" ) );
		$yesno_array 	= array( array( "id"=>"no","text"=>"NO" ), array( "id"=>"yes","text"=>"YES" ) );
		$formbtns			= $extrabtn . "<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"{$buttontxt}\" /> <input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"Apply\" /> <a onClick=\"{$buttonlink}\" class=\"cancelbutton\">Cancel</a>&nbsp;&nbsp;";

		$_boarditem  = $this->setJS->setEditor( "tinymce", "mceEditor", 695, 200 );
		$_boarditem .= $this->setAllelements->setToolbar( $formbtns, "", $this->toolbarTitle );
		$_boarditem .= "\n<dl class=\"pages_component\">\n\t<dd>"
								."\n<lable for=\"title\" class=\"darkOrange_txt\">Title:</lable>"
								."\n<input id=\"title\" type=\"text\" name=\"title\" value=\"".$valueObject->title."\" class=\"extra53\"/><br />"

								."\n<lable for=\"fulltxt\" class=\"darkOrange_txt\">Banner Text:</lable>"
									."<textarea id=\"fulltxt\" name=\"fulltxt\" class=\"Threeline\">". htmlspecialchars( $valueObject->fulltxt ) ."</textarea><br />"

								."\n<lable for=\"filename\" class=\"darkOrange_txt\">Attachment:</lable><small>To upload file. Please <strong>DO NOT</strong> use KOREAN file name and white space( s ). <strong>ex ) filename_date-012.jpg</strong></small>"
								."\n<input id=\"filename\" type=\"file\" name=\"filename\" size=\"30\" class=\"\" /><small>".$valueObject->filename."</small><br />"

								. getDisplayImgBK( $valueObject->filename, 300, false, $valueObject->title )

								."\n<br/><lable for=\"linkfile\" class=\"darkOrange_txt\">File name:</lable><small>If you know the location for a file. <strong>ex ) /folder_name/filename</strong></small>"
								."\n<input id=\"linkfile\" type=\"text\" name=\"linkfile\" value=\"".$valueObject->linkfile."\" class=\"extra53\"  /><br />"

								."\n<lable for=\"urls\" class=\"darkOrange_txt\">Links:</lable><small><strong>http://</strong>를 포함하여 정확한 URL</small>"
								."\n<input id=\"urls\" type=\"text\" name=\"urls\" value=\"".$valueObject->urls."\" class=\"extra53\"  /><br />"

								."\n<table border=\"0\" width=\"695\"><tr><td  align=\"left\" valign=\"top\" width=\"200\">\n<lable for=\"page\" class=\"darkOrange_txt\">Target page:</lable>\n".$this->setAllelements->setPullDownMenu( "pgid[]", $page_array , $mpValue, "multiple=\"multiple\" size=\"7\" id=\"pgid\" class=\"blue multiselect sfixedwidth\"" )."</td><td align=\"left\" valign=\"top\">\n<lable for=\"target_window\" class=\"darkOrange_txt\">Target window:</lable>\n".$this->setAllelements->setPullDownMenu( "target_window", $target_array ,$valueObject->target_window, "class=\"blue sfixedwidth\"" )."\n<lable for=\"extra_class\" class=\"darkOrange_txt\">Extra Class:</lable>\n<input id=\"extra_class\" type=\"text\" name=\"extra_class\" value=\"".$valueObject->extra_class."\" class=\"extra180\"  /></td><td  align=\"left\" valign=\"top\">\n<lable for=\"position\" class=\"darkOrange_txt\">Banner position:</lable>\n".$this->setAllelements->setPullDownMenu( "position", $position_array, $valueObject_position, "id=\"position\" class=\"blue sfixedwidth\"" )."</td></tr></table><br /><br />"

								."\n<table border=\"0\"><tr><td width=\"40\" valign=\"top\"><lable for=\"order\" class=\"darkOrange_txt ordertxt\">Order:</lable></td><td width=\"50\"><input id=\"order\" type=\"text\" name=\"order\" value=\"".$valueObject->ordering."\" class=\"extra3\"  /></td><td width=\"80\" valign=\"top\"><lable for=\"views\" class=\"darkOrange_txt viewtxt\">Views:</lable></td><td width=\"50\"><input id=\"views\" type=\"text\" name=\"views\" value=\"".$valueObject->views."\" class=\"extra3\"  /></td></tr></table><br />";

		$_boarditem .=  "\n<input type=\"hidden\" name=\"id\" value=\"{$valueObject->id}\" />"
								."\n<input type=\"hidden\" name=\"xfile\" value=\"{$valueObject->filename}:{$valueObject->filesize}\" />"
								."\n<input type=\"hidden\" name=\"press\" value=\"{$presstxt}\" />"
								."</dd>\n</dl>";

		$_boardlistWithForm = sprintf( $this->form, $_action_url, "\n<script type=\"text/javascript\" src=\"". $this->static_url . CONFIG_STATIC_SUBFOLDER ."/js/back-menutype.js\"></script>".$_boarditem );

		$bItems  = "\n<!--// Bof Contents //-->";
		$bItems .= $_boardlistWithForm;
		$bItems .= "\n<!--// Eof Contents //-->\n\n";

		return $bItems;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get create
 *  ------------------------------------------------------------------------- */
	public function getCreate( $allValues ){
		global $user_ip;

		$sxtra = isset( $allValues['pid'] ) ? "&pid=".$allValues['pid'] : "";
		$pxtra = isset( $allValues['p'] ) ? "&p=".$allValues['p'] : "";
		$node = $allValues['node'];
		if ( $_FILES[filename][name] && $error_flag == false && empty( $file ) ){
			$uploading_file = getUploadingFile( $_FILES[filename], false );
			$filename = $uploading_file['name'];
			$filetype = $uploading_file['type'];
			$filesize = $uploading_file['size'];
			unset( $uploading_file );
		}

		if ( ( $allValues['submitbutton'] === "Update" || $allValues['submitbutton'] === "Apply" ) && $allValues['press'] === "update" ){
			$query = "SELECT * FROM {$this->tbname} WHERE id = '{$allValues['id']}' AND publish='1'";
			$valueObject = $this->dbConnect->getObject( $query );
			list( $year, $month, $date ) = explode( '-' , substr( $valueObject->created_date , 0 , 10 ) );
			$cdate = $valueObject->created_date;
			$cby = $valueObject->created_by;

			if ( empty( $filename ) && !empty( $allValues['xfile'] ) ){
				list($filename, $filesize) = explode( ":", $allValues['xfile'] );
			}
		}
		else{
			$cdate = "now()";
			$cby = $_SESSION['session_user_id'];
		}

		for( $j=0; $j < count( $allValues['pgid'] ); $j++ ){
			$divider = ( $j == count( $allValues['pgid'] )-1 ) ? "":";";
			$pids .= "[".$allValues['pgid'][$j]."]".$divider;
		}

		$total_banners = $this->dbConnect->getTotalNumber( $this->tbname, "publish = '1'" );
		$allValues['order'] = empty( $allValues['order'] ) ? $total_banners + 1 : $allValues['order'];

		$sql_data_array = array(
												'title'					=> $allValues['title'],
												'fulltxt'				=> $allValues['fulltxt'],
												'linkfile'				=> $allValues['linkfile'],
												'filename'			=> $filename,
												'filesize'				=> $filesize,
												'urls'					=> $allValues['urls'],
												'target_window'	=> !empty( $allValues['target_window'] ) ? $allValues['target_window'] : "_self",
												'extra_class'		=> $allValues['extra_class'],
												'position'			=> $allValues['position'],
												'pageid' 				=> $pids,
												'ordering'			=> $allValues['order'],
												'publish'				=> 1,
												'status'	 			=> 1,
												'views'				=> $allValues['views'],
												'modified_date' 	=> "now()",
												'modified_by' 	=> $_SESSION['session_user_id'],
												'created_date'  	=> $cdate,
												'created_by'  	=> $cby
											 );

			if (( $allValues['submitbutton'] === "Create" || $allValues['submitbutton'] === "Apply" ) && $allValues['press'] === "create" ){
					if ( !empty( $allValues['title'] ) ){
						$this->dbConnect->getDBAction( $this->tbname, $sql_data_array );
						$inserted_article_id = $this->dbConnect->getInsertID();
					}
					unset( $sql_data_array );
					if ( $allValues['submitbutton'] === "Create" ){
						echo $this->setJS->setRedirectJScript( $node, $sxtra, $cxtra, $pxtra );
					}
					elseif ( $allValues['submitbutton'] === "Apply" ){
						echo $this->setJS->setRedirectJScriptID( $node, $sxtra, $cxtra, $pxtra, "&id={$inserted_article_id}" );
					}
			}
			elseif( ( $allValues['submitbutton'] === "Update" || $allValues['submitbutton'] === "Apply" ) && $allValues['press'] === "update" ){
					if( !empty( $allValues['title'] ) ){
						$this->dbConnect->getDBAction( $this->tbname, $sql_data_array, "update", "id = {$allValues['id']}" );
					}
					unset( $sql_data_array );

					if( $allValues['submitbutton'] === "Update" ){
						echo $this->setJS->setRedirectJScript( $node, $sxtra, $cxtra, $pxtra );
					}
					elseif ( $allValues['submitbutton'] === "Apply" ){
						echo $this->setJS->setRedirectJScriptID( $node, $sxtra, $cxtra, $pxtra, "&id={$allValues['id']}" );
					}
			}
			elseif ( $allValues['submitbutton'] === "Delete" ){
				if ( count( $allValues['boardnum'] ) > 0 ){
						foreach ( $allValues['boardnum'] as $key => $kw ){
							$dquery = "SELECT * FROM {$this->tbname} WHERE id = '{$kw}'";
							$dObject = $this->dbConnect->getObject( $dquery );
							!@unlink( $_SERVER['DOCUMENT_ROOT'] ."/".$dObject->filename );
							clearstatcache();
							$this->dbConnect->getQuery( "DELETE FROM {$this->tbname} WHERE id = '{$kw}'" );
						}
				}
				else{
					if ( !empty( $allValues['id'] ) ){
							if ( empty( $filename ) && !empty( $allValues['xfile'] ) ){
								list($filename, $filesize) = explode( ":", $allValues['xfile'] );
							}

							!@unlink( $_SERVER['DOCUMENT_ROOT'] ."/".$filename );
							$this->dbConnect->getQuery( "DELETE FROM {$this->tbname} WHERE id = '{$allValues['id']}'" );
							clearstatcache();
					}
					else {
							echo "<script type='text/javascript'>window.alert( 'Please select an Article from the list to delete.' );</script>";
					}
				}
				unset( $allValues );
				echo $this->setJS->setRedirectJScript( $node, $sxtra, $cxtra, $pxtra );
			}
			elseif ( $allValues['submitbutton'] === "Update" ){
				if ( count( $allValues['boardnum'] )>0 ){
					foreach ( $allValues['boardnum'] as $key => $kw ){
						$this->dbConnect->getQuery( "UPDATE {$this->tbname} SET ordering = {$allValues['short'][$kw]} WHERE id = '{$kw}'" );
					}
				}
				else {
					echo "<script type='text/javascript'>window.alert( 'Please select an Article from the list to update.' );</script>";
				}
				unset( $allValues );
				echo $this->setJS->setRedirectJScript( $node, $sxtra, $cxtra, $pxtra );
			}
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get List Format
 *  ------------------------------------------------------------------------- */
	public function getListFormat( $opt = "" ){
		if ( $opt === "boardtitle" ){
			$listtxt = "\n\t<tr id=\"board_bar\" height=\"25\">"
										."\n\t<th width=\"35\" align=\"center\" class=\"board_line\"><input type=\"checkbox\" value=\"\" name=\"master\" onClick=\"listCheckAll();\"></th>"
										."\n\t<th width=\"35\" align=\"center\" class=\"board_line\"><small>#</small></th>"
										."\n\t<th width=\"200\" align=\"center\" class=\"board_line\">Title</th>"
										."\n\t<th align=\"center\" class=\"board_line\">Banner</th>"
										."\n\t<th width=\"200\" align=\"center\" class=\"board_line\">Link</th>"
										."\n\t<th width=\"90\" align=\"center\" class=\"board_line\">Page</th>"
										."\n\t<th width=\"70\" align=\"center\" class=\"board_line\">Order</th>"
										."\n\t<th width=\"50\" align=\"center\" class=\"board_line\">Status</th>"
										."\n\t<th width=\"35\"align=\"center\" ><small>ID</small></th>"
										."\n\t</tr>";
		}
		elseif ( $opt === "boardlist" ){
			$listtxt =  "\n\t<tr height=\"25\" %s>"
										."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"left\" valign=\"middle\"><a href=\"%s\" target=\"_self\" title=\"%s\" onfocus=\"this.blur()\"><small>&nbsp;&nbsp;%s</small></a></td>"
										."\n\t<td class=\"td_bot left_space thumbanner\" align=\"center\"><a href=\"%s\" target=\"_self\" title=\"%s\" onfocus=\"this.blur()\">%s</a></td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"left\" valign=\"middle\"><small>&nbsp;&nbsp;%s</small></td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
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