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

class getAdvertises {

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

		$this->tbname				= "advertises";
		$this->nodeValue			= "adv";
		$this->toolbarTitle			= "Advertise Manager";
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

			$sxtra	 					= "";
			$optail	 					= "";
			$gxtra	 					= ( isset( $gvalue['gid'] ) && _getCheckNullorNot( $gvalue['gid'] ) ) ? "&gid=".$gvalue['gid'] : "";
			$gropu_id					= ( isset( $gvalue['gid'] ) && _getCheckNullorNot( $gvalue['gid'] ) ) ? " AND 	position = '".$gvalue['gid']."'" : "";
			$pxtra 						= isset( $gvalue['p'] ) ? "&p=".$gvalue['p'] : "";
			$_action_url  				= $this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$pxtra}{$gxtra}";

			if( $gvalue['m'] === "update" ){
				$this->dbConnect->getQuery( "UPDATE {$this->tbname} SET status = {$gvalue['st']}, modified_date = 'now()' WHERE id = '{$gvalue['id']}'" );
				echo $this->setJS->setRedirectJScript( $this->nodeValue, $sxtra, $gxtra, $pxtra );
			}
			if( isset( $gvalue['groupid'] ) ){
				$pxtra 	= ( isset( $gvalue['p'] ) && $gvalue['p'] > 1) ? "&p=1": "";
				$gxtra	 = _getCheckNullorNot( $gvalue['groupid'] ) ? "&gid=".$gvalue['groupid'] : "";
				echo $this->setJS->setRedirectJScript( $this->nodeValue, $sxtra, $gxtra, $pxtra );
			}

			$formbtns	= "<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" onfocus=\"this.blur()\" value=\"Copy\" /><input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"Update\" /><input type=\"submit\" name=\"submitbutton\" class=\"deletebutton\" value=\"Delete\" /><a href=\"".$this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$pxtra}&amp;m=view"."\" class=\"abtn\" onfocus=\"this.blur()\"><span class=\"icon-32-new\" title=\"New\"></span>New</a>&nbsp;&nbsp;";

			$total_list 			= $this->dbConnect->getTotalNumber( $this->tbname, "publish = '1'{$optail}{$gropu_id}" );
			$total_items 		= $total_list;
			$total_page 		= ceil( $total_list/CONFIG_HOW_MANY_ARTICLES_PER_PAGE );
			$noPage 			= isset( $gvalue['p'] ) ?  $gvalue['p'] : 1;
			$first 				= $total_list == 0 ? 1 : CONFIG_HOW_MANY_ARTICLES_PER_PAGE *( $noPage-1 );
			$last					= $total_list == 0 ? 0 : CONFIG_HOW_MANY_ARTICLES_PER_PAGE * $noPage;
			$loop_number	= $total_list - ( $noPage-1 ) * CONFIG_HOW_MANY_ARTICLES_PER_PAGE;
			$adgrouplist 		= $this->dbConnect->getAdsGroupList( "" );

			$_boardlist  = $this->setAllelements->setToolbar( $formbtns, "", $this->toolbarTitle );
			$_boardlist .= "\n\t<table width=\"100%\"><tr><td valign=\"top\">";
			$_boardlist .= "\n\t<div class=\"totlaitemsright\"><span class=\"totalTxt\">Total of item(s): {$total_items} </span>".$this->setAllelements->setPullDownMenuOnChange( "groupid", $adgrouplist , $gvalue['gid'], "id=\"groupid\" class=\"blue sfixedwidth\"" )."</div>";
			$_boardlist .= "\n\t<table width=\"100%\">";
			$_boardlist .= "\n\t<tbody>";
			$_boardlist .= $this->getListFormat( "boardtitle" );

			$list_query = $this->dbConnect->getQuery( "SELECT * FROM {$this->tbname} WHERE publish = '1'{$optail}{$gropu_id} ORDER BY ordering, created_date DESC LIMIT {$first}, ".CONFIG_HOW_MANY_ARTICLES_PER_PAGE."" );
			while ( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
    			$bgClass			= ( $loop_number/2 ) == floor( $loop_number/2 ) ? " class=\"list_odd\"" : " class=\"list_even\"";
				$pURL				= $this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}&amp;id={$list_values['id']}{$pxtra}{$gxtra}&amp;m=view";
				$created_date	= _getShortFormatDate( $list_values['created_date'] );

				$query = "SELECT title, name FROM advertises_group WHERE name = '{$list_values['position']}'";
				$valueObject = $this->dbConnect->getObject( $query );

				$_boardlist .= sprintf( $this->getListFormat( "boardlist" ), $bgClass, "<input type=\"checkbox\" value=\"{$list_values['id']}\" name=\"boardNum[]\">", $loop_number, $pURL, stripslashes( $list_values['title'] ), stripslashes( $list_values['title'] ), $pURL, stripslashes( $list_values['title'] ), getDisplayImgBK( $list_values['filename'], 150, false, $list_values['title'], false ), $list_values['urls'], $valueObject->name, $valueObject->title, "<input type=\"text\" size=\"3\" name=\"Short[{$list_values['id']}]\" value=\"{$list_values['ordering']}\" style=\"width:40px;font-size:11px;text-align:center\">", $this->setAllelements->setPublishIcon( $this->nodeValue, $list_values['id'], $list_values['status'], $sxtra, $gxtra, $pxtra ), $list_values['id'] );

				$loop_number--;
			}

			$_boardlist .= "\n\t</tbody>\n\t</table>";
			$_boardlist .= $this->setAllelements->setPagelistblock( $this->nodeValue, $total_page, $noPage, $sxtra, $gxtra );
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
		$gxtra	 		= isset( $gvalue['gid'] ) ? "&gid=".$gvalue['gid'] : "";

		$_action_url	= $this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$cxtra}{$pxtra}{$gxtra}";

		if( !empty( $gvalue['id'] ) ){
			$valueObject 				= $this->dbConnect->getContents( $this->tbname, "id = '{$gvalue['id']}' AND publish='1'" );
			$mpValue 						= $this->dbConnect->getBannerPage( "bannerid = {$valueObject->id}" );
			$buttontxt						= "Update";
			$buttonlink						= "parent.location='".$this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$pxtra}{$gxtra}'";
			$presstxt 						= "update";
			$extrabtn 						= "<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"Copy\" /><input type=\"submit\" name=\"submitbutton\" class=\"deletebutton\" value=\"Delete\" />";
			$vsectionid 					= $valueObject->sectionid;
			$vcategoriesid				= $valueObject->categoriesid;
			$valueObject_position	= $valueObject->position;

		} else {
			$buttontxt						= "Create";
			$presstxt 						= "create";
			$valueObject_position	= "top_banner";
			$buttonlink						= "parent.location='".$this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$pxtra}{$gxtra}'";
		}

		$position_array 	= $this->dbConnect->getAdGroupList( "ORDER BY title" );
		$page_array 		= $this->dbConnect->getPageList( "publish = '1' AND status = '1' ORDER BY ordering" );
		$target_array 	= array( array( "id"=>"_blank","text"=>"New Window [새창]" ), array( "id"=>"_self","text"=>"Same Window [현재창]" ) );
		$yesno_array 	= array( array( "id"=>"no","text"=>"NO" ), array( "id"=>"yes","text"=>"YES" ) );
		$formbtns			= $extrabtn . "<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"{$buttontxt}\" /> <input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"Apply\" /> <a onClick=\"{$buttonlink}\" class=\"cancelbutton\">Cancel</a>&nbsp;&nbsp;";

					$sdate = floor((time() - strtotime( $valueObject->starting_date )) / 86400);
					$edate = floor((strtotime( $valueObject->ending_date ) - time()) / 86400);

		$_boarditem  = $this->setJS->setEditor( "tinymce", "mceEditor", 695, 200 );
		$_boarditem .= $this->setJS->setBootstraptDatepickJScript();
		$_boarditem .= $this->setAllelements->setToolbar( $formbtns, "", $this->toolbarTitle );
		$_boarditem .= "\n<dl class=\"pages_component\">\n\t<dd>"
								."\n<lable for=\"title\" class=\"darkOrange_txt\">Title:</lable>"
								."\n<input id=\"title\" type=\"text\" name=\"title\" value=\"".$valueObject->title."\" class=\"extra53\"/><br />"

								."\n<lable for=\"fulltxt\" class=\"darkOrange_txt\">Advertise Text:</lable>"
									."<textarea id=\"fulltxt\" name=\"fulltxt\" class=\"Threeline\">". htmlspecialchars( $valueObject->fulltxt ) ."</textarea><br />"

								."\n<lable for=\"filename\" class=\"darkOrange_txt\">Attachment:</lable><small>To upload file. Please <strong>DO NOT</strong> use KOREAN file name and white space( s ). <strong>ex ) filename_date-012.jpg</strong></small>"
								."\n<input id=\"fileimg\" type=\"text\" name=\"fileimg\" value=\"".$valueObject->filename."\" class=\"extra26 floatleft\"/><input id=\"filename\" type=\"file\" name=\"filename\" size=\"20\" class=\" mspace2 \" maxlength=\"1\"  />"

								. getDisplayImg( $valueObject->filename, 300, false, $valueObject->title )

								."\n<lable for=\"linkfile\" class=\"darkOrange_txt\">File name:</lable><small>If you know the location for a file. <strong>ex ) /folder_name/filename</strong></small>"
								."\n<input id=\"linkfile\" type=\"text\" name=\"linkfile\" value=\"".$valueObject->linkfile."\" class=\"extra53\"  /><br />"

								."\n<lable for=\"urls\" class=\"darkOrange_txt\">Links:</lable><small><strong>http://</strong>를 포함하여 정확한 URL</small>"
								."\n<input id=\"urls\" type=\"text\" name=\"urls\" value=\"".$valueObject->urls."\" class=\"extra53\"  /><br />"

								."\n<table width=\"695\"><tr><td align=\"left\" valign=\"top\"  width=\"50%\">\n<lable for=\"casting_date\" class=\"darkOrange_txt\">Starting Date:</lable><small>YYYY-MM-DD</small>\n<input id=\"casting_date\" type=\"text\" name=\"casting_date\" value=\"".$valueObject->starting_date."\" class=\"extra26\"/></td><td align=\"left\" valign=\"top\" width=\"50%\">\n<lable for=\"publishing_date\" class=\"darkOrange_txt\">Ending Date:</lable><small>YYYY-MM-DD</small>\n<input id=\"publishing_date\" type=\"text\" name=\"publishing_date\" value=\"".$valueObject->ending_date."\" class=\"extra26\"/></td></tr></table>"

								."\n<table width=\"695\"><tr><td align=\"left\" valign=\"top\" width=\"50%\">\n<lable for=\"target_window\" class=\"darkOrange_txt\">Target window:</lable>\n".$this->setAllelements->setPullDownMenu( "target_window", $target_array ,$valueObject->target_window, "class=\"blue sfixedwidth\"" )."\n<lable for=\"extra_class\" class=\"darkOrange_txt\">Extra Class:</lable><small>Extra Class for 	&lsaquo;li&rsaquo;</small>\n<input id=\"extra_class\" type=\"text\" name=\"extra_class\" value=\"".$valueObject->extra_class."\" class=\"extra180\"  /></td><td  align=\"left\" valign=\"top\">\n<lable for=\"position\" class=\"darkOrange_txt\">Advertise group:</lable>\n".$this->setAllelements->setPullDownMenu( "position", $position_array, $valueObject_position, "id=\"position\" class=\"blue sfixedwidth\"" )."\n<br><br><table><tr><td width=\"40\" valign=\"top\"><lable for=\"order\" class=\"darkOrange_txt ordertxt\">Order:</lable></td><td width=\"50\"><input id=\"order\" type=\"text\" name=\"order\" value=\"".$valueObject->ordering."\" class=\"extra3\"  /></td><td width=\"40\" valign=\"top\"><lable for=\"views\" class=\"darkOrange_txt viewtxt\">Views:</lable></td><td width=\"50\"><input id=\"views\" type=\"text\" name=\"views\" value=\"".$valueObject->views."\" class=\"extra3\"  /></td></tr></table></td></tr></table>";

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
		$gxtra = isset( $allValues['gid'] ) ? "&gid=".$allValues['gid'] : "";
		$node = $allValues['node'];
		if ( $_FILES[filename][name] && $error_flag == false && empty( $file ) ){
			$uploading_file = getUploadingFile( $_FILES[filename], false );
			$filename = $uploading_file['name'];
			$filetype = $uploading_file['type'];
			$filesize = $uploading_file['size'];

			unset( $uploading_file );
		}
		elseif( _getCheckNullorNot($allValues['fileimg']) ) {
			$filename = $allValues['fileimg'];
		}

		if( $allValues['submitbutton'] === "Copy" && $allValues['press'] === "update" ){
				$cdate					= "now()";
				$cby = $_SESSION['session_user_id'];
				$extra_title			= "Copy - ";
				$allValues['views']	= 0;
				$re_status = 0;
				if( !_getCheckNullorNot( $filename ) && _getCheckNullorNot( $allValues['xfile'] ) ){
						$filename_array	= explode( ":", $allValues['xfile'] );
						$filename 			= $filename_array[0];
						$filesize 			= $filename_array[1];
				}
		}
		elseif( ( $allValues['submitbutton'] === "Update" || $allValues['submitbutton'] === "Apply" ) && $allValues['press'] === "update" ){
			$query = "SELECT * FROM {$this->tbname} WHERE id = '{$allValues['id']}' AND publish='1'";
			$valueObject = $this->dbConnect->getObject( $query );
			list( $year, $month, $date ) = explode( '-' , substr( $valueObject->created_date , 0 , 10 ) );
			$cdate = $valueObject->created_date;
			$cby = $valueObject->created_by;
			$re_status	= _getCheckNullorNot( $allValues['status'] ) ? $allValues['status'] : 1;

			if( !_getCheckNullorNot( $filename ) && _getCheckNullorNot( $allValues['xfile'] ) ){
				list($filename, $filesize) = explode( ":", $allValues['xfile'] );
			}
			elseif( _getCheckNullorNot( $filename ) ){
				$filename = $filename;
			}
			elseif( _getCheckNullorNot($allValues['fileimg']) && _getCheckNullorNot( $allValues['xfile'] )){
				list($filimg, $filesize) = explode( ":", $allValues['xfile'] );
				$filename = ( $allValues['fileimg'] != $filimg ) ? $allValues['fileimg'] : $filimg;
			}

		}else{
			$cdate = "now()";
			$cby = $_SESSION['session_user_id'];
			$re_status	= _getCheckNullorNot( $allValues['status'] ) ? $allValues['status'] : 1;
		}

		$total_pages = $this->dbConnect->getTotalNumber( $this->tbname, "publish = '1' AND status = '1'" );
		$allValues['order'] = empty( $allValues['order'] ) ? $total_pages + 1 : $allValues['order'];

		$sql_data_array = array(
												'title'					=> $extra_title . $allValues['title'],
												'fulltxt'				=> $allValues['fulltxt'],
												'linkfile'				=> $allValues['linkfile'],
												'filename'			=> $filename,
												'filesize'				=> $filesize,
												'urls'					=> $allValues['urls'],
												'target_window'	=> !empty( $allValues['target_window'] ) ? $allValues['target_window'] : "_self",
												'extra_class'		=> $allValues['extra_class'],
												'position'			=> $allValues['position'],
												'ordering'			=> $allValues['order'],
												'publish'				=> 1,
												'status'	 			=> $re_status,
												'views'				=> $allValues['views'],
												'starting_date' 	=> $allValues['casting_date'],
												'ending_date' 	=> $allValues['publishing_date'],
												'modified_date' 	=> "now()",
												'modified_by' 	=> $_SESSION['session_user_id'],
												'created_date'  	=> $cdate,
												'created_by'  	=> $cby
											 );

			if( ( $allValues['submitbutton'] === "Create" || $allValues['submitbutton'] === "Apply" ) && $allValues['press'] === "create" ){
				if( !empty( $allValues['title'] ) ){
					$this->dbConnect->getDBAction( $this->tbname, $sql_data_array );
					$inserted_article_id = $this->dbConnect->getInsertID();
					unset( $sef_sql_data_array );
				}

				unset( $sql_data_array );
				if( $allValues['submitbutton'] === "Create" ){
					echo $this->setJS->setRedirectJScript( $node, $sxtra, $gxtra, $pxtra );
				} elseif ( $allValues['submitbutton'] === "Apply" ){
					echo $this->setJS->setRedirectJScriptID( $node, $sxtra, $gxtra, $pxtra, "&id={$inserted_article_id}" );
				}
			}
			elseif( $allValues['submitbutton'] === "Copy" && $allValues['press'] === "update" ){
					if( _getCheckNullorNot( $allValues['title'] ) ){
						$this->dbConnect->getDBAction( $this->tbname, $sql_data_array );
					}
					unset( $sql_data_array );
					echo $this->setJS->setRedirectJScript( $node, $sxtra, $cxtra, $pxtra );
			}
			elseif( ( $allValues['submitbutton'] === "Update" || $allValues['submitbutton'] === "Apply" ) && $allValues['press'] === "update" ){
				if( !empty( $allValues['title'] ) ){
					$this->dbConnect->getDBAction( $this->tbname, $sql_data_array, "update", "id = {$allValues['id']}" );
					unset( $sef_sql_data_array );
				}
				unset( $sql_data_array );

				if( $allValues['submitbutton'] === "Update" ){
					echo $this->setJS->setRedirectJScript( $node, $sxtra, $gxtra, $pxtra );
				}
				elseif ( $allValues['submitbutton'] === "Apply" ){
					echo $this->setJS->setRedirectJScriptID( $node, $sxtra, $gxtra, $pxtra, "&id={$allValues['id']}" );
				}
			}
			elseif( $allValues['submitbutton'] === "Delete" ){
				if( count( $allValues['boardnum'] ) > 0 ){
					foreach ( $allValues['boardnum'] as $key => $kw ){
						$dquery = "SELECT * FROM {$this->tbname} WHERE id = '{$kw}'";
						$dObject = $this->dbConnect->getObject( $dquery );
						!@unlink( $_SERVER['DOCUMENT_ROOT'] ."/".$dObject->filename );
						clearstatcache();
						$this->dbConnect->getQuery( "DELETE FROM {$this->tbname} WHERE id = '{$kw}'" );
					}
				}
				else{
					if( !empty( $allValues['id'] ) ){
						if( !_getCheckNullorNot( $filename ) && _getCheckNullorNot( $allValues['xfile'] ) ){
							list($filename, $filesize) = explode( ":", $allValues['xfile'] );
						}
						elseif( _getCheckNullorNot( $filename ) ){
							$filename = $filename;
						}
						elseif( _getCheckNullorNot($allValues['fileimg']) && _getCheckNullorNot( $allValues['xfile'] )){
							list($filimg, $filesize) = explode( ":", $allValues['xfile'] );
							$filename = ( $allValues['fileimg'] != $filimg ) ? $allValues['fileimg'] : $filimg;
						}
						
						!@unlink( $_SERVER['DOCUMENT_ROOT'] ."/".$filename );
						$this->dbConnect->getQuery( "DELETE FROM {$this->tbname} WHERE id = '{$allValues['id']}'" );
						clearstatcache();
					}
					else{
						echo "<script type='text/javascript'>window.alert( 'Please select an item from the list to delete.' );</script>";
					}
				}
				unset( $allValues );

				echo $this->setJS->setRedirectJScript( $node, $sxtra, $gxtra, $pxtra );
			}
			elseif( $allValues['submitbutton'] === "Update" ){
				if( count( $allValues['boardnum'] )>0 ){
					foreach ( $allValues['boardnum'] as $key => $kw ){
						$this->dbConnect->getQuery( "UPDATE {$this->tbname} SET ordering = {$allValues['short'][$kw]} WHERE id = '{$kw}'" );
					}
				}
				else {
					echo "<script type='text/javascript'>window.alert( 'Please select an item from the list to update.' );</script>";
				}
				unset( $allValues );

				echo $this->setJS->setRedirectJScript( $node, $sxtra, $gxtra, $pxtra );
			}
			elseif( $allValues['submitbutton'] === "Copy" ){
				if( count( $allValues['boardnum'] ) > 0 ){
					foreach ( $allValues['boardnum'] as $key => $kw ){
						$apObject = $this->dbConnect->getObject( "SELECT * FROM {$this->tbname} WHERE id = '{$kw}'" );
						$copy_sql_data_array = array(
																			'title'					=> "Copy - ".$apObject->title,
																			'fulltxt'				=> $apObject->fulltxt,
																			'linkfile'				=> $apObject->linkfile,
																			'filename'			=> $apObject->filename,
																			'filesize'				=> $apObject->filesize,
																			'urls'					=> $apObject->urls,
																			'target_window'	=> $apObject->target_window,
																			'extra_class'		=> $apObject->extra_class,
																			'position'			=> $apObject->position,
																			'ordering'			=> $apObject->ordering,
																			'publish'				=> 1,
																			'status'	 			=> 0,
																			'views'				=> $apObject->views,
																			'starting_date' 	=> $apObject->casting_date,
																			'ending_date' 	=> $apObject->publishing_date,
																			'modified_date' 	=> "now()",
																			'modified_by' 	=> $_SESSION['session_user_id'],
																			'created_date'  	=> "now()",
																			'created_by'  	=> $_SESSION['session_user_id']
																	 );
						$this->dbConnect->getDBAction( $this->tbname, $copy_sql_data_array );
						$inserted_article_id = $this->dbConnect->getInsertID();
						unset( $copy_sql_data_array );
					}
				}
			  else{
					echo "<script type='text/javascript'>window.alert( 'Please select an item from the list to copy.' );</script>";
				}
			}
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get List Format
 *  ------------------------------------------------------------------------- */
	public function getListFormat( $opt = "" ){
		if( $opt === "boardtitle" ){
			$listtxt = "\n\t<tr id=\"board_bar\" height=\"25\">"
										."\n\t<th width=\"35\" align=\"center\" class=\"board_line\"><input type=\"checkbox\" value=\"\" name=\"master\" onClick=\"listCheckAll();\"></th>"
										."\n\t<th width=\"35\" align=\"center\" class=\"board_line\"><small>#</small></th>"
										."\n\t<th width=\"200\" align=\"center\" class=\"board_line\">Title</th>"
										."\n\t<th align=\"center\" class=\"board_line\">Advertise</th>"
										."\n\t<th width=\"200\" align=\"center\" class=\"board_line\">Link</th>"
										."\n\t<th width=\"70\" align=\"center\" class=\"board_line\">CODE</th>"
										."\n\t<th width=\"90\" align=\"center\" class=\"board_line\">Group</th>"
										."\n\t<th width=\"70\" align=\"center\" class=\"board_line\">Order</th>"
										."\n\t<th width=\"50\" align=\"center\" class=\"board_line\">Status</th>"
										."\n\t<th width=\"35\" align=\"center\" ><small>ID</small></th>"
										."\n\t</tr>";
		} elseif( $opt === "boardlist" ){
			$listtxt =  "\n\t<tr height=\"25\" %s>"
										."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"left\" valign=\"middle\"><a href=\"%s\" target=\"_self\" title=\"%s\" onfocus=\"this.blur()\"><small>&nbsp;&nbsp;%s</small></a></td>"
										."\n\t<td class=\"td_bot left_space thumbanner\" align=\"center\"><a href=\"%s\" target=\"_self\" title=\"%s\" onfocus=\"this.blur()\">%s</a></td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"left\" valign=\"middle\"><small>&nbsp;&nbsp;%s</small></td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
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