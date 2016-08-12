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
 *  ----------------------------------------------------------------------- */
defined( "_VALID_MOS" ) or die( "Your system is not working properly." );

class getContent {

	public $dbConnect;
	public $basicURL;
	public $error_flag;
	public $error_message;
	public $contents;
	public $castlist;
	public $GeneralItems;
	public $dphp;

	public function __construct( $baseURL ){
		global $Bon_db;

		$this->dbConnect	 	= $Bon_db;
		$this->basicURL		= $baseURL;
		$this->error_flag			= false;
		$this->error_message	= array();
		$this->contents			= array();
		$this->castlist			= array( "casting", "castingthumb", "castingthumblist" );
		$this->GeneralItems	= new getGeneral( $this->basicURL );
		$this->dphp				= new getDirectCode();
		$this->minifyHtml		= new getHTMLOptimize();
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: @return text html
 * Display actual content
 *  ------------------------------------------------------------------------- */
	public function getContents( $kPages = "" ){
		//Query option for group and access level
		//All elements class to set access level with session
		$iWhere =" group_level >= '".getAllElements::setAccesslevel("gid")."' AND access_level >= '".getAllElements::setAccesslevel("ulevel")."' AND publish = '1' AND status = '1'";

		if ( ( $this->dbConnect->getTotalNumber( "pages", "id = '".$kPages['pid']."' AND".$iWhere ) > 0 ) && !isset( $kPages['tid'] ) ){
				//Display a page
				$_contents = $this->getDisplayPage( $kPages, "frontpage = '0' AND ".$iWhere, getAllElements::setAccesslevel("gid"), getAllElements::setAccesslevel("ulevel") );
		}
		else {
			if ( isset( $kPages['tid'] ) && _getCheckNullorNot( $kPages['tid'] ) ){
				//Display a board article
				$_contents = ( $this->dbConnect->getTotalNumber( $kPages['tbname'], "id = '".$kPages['tid']."' AND".$iWhere ) > 0 ) ? $this->getDisplayBoard( $kPages, $iWhere ) : "";
			}
			else {
				$_contents 	= "\n<!--// Bof Contents //-->\n<div id=\"effect\">";
				$_contents .= "\n".NOT_FIND_PAGES ."<a href=\"mailto:". CONFIG_SITE_EMAIL ."?subject=I am looking for a page in ". CONFIG_SITE_NAME .".\" title=\"Email to the site admin\">".  CONFIG_SITE_EMAIL . "</a>.<br />- INSIDE";
				$_contents .= "\n</div>\n<!--// Eof Contents //-->\n";
			}
		}

		return $this->minifyHtml->minifyHTML($_contents);
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 *  ------------------------------------------------------------------------- */
	public function getDisplayPage( $kPages, $iWhere, $Gid, $Userlevel ){
		global $_ci_path;

		$this->contents = $this->dbConnect->getPageContent( $kPages['pid'], $iWhere );
		$_page = "<!--// Bof Main Contents //-->";
		//Display page text or image TITLE with or without link
		if ( CONFIG_SHOW_TITLE == "true" ){
			if ( $this->contents['showtitle'] == 1 ){
				if ( _getCheckNullorNot( $this->contents['title_images'] ) && CONFIG_USE_IMAGE_PAGE_TITLE == "true" ){
					$_page .= "\n<h1 class=\"page_img_title\">". getDisplayImg( $this->contents['title_images'], 500, false, $this->contents['title'], false ) ."</h1>";
				}
				else {
					$_page .= ( CONFIG_USE_TXT_LINK == "true" ) ? "\n<h1 class=\"page_title\"><a href=\"".$this->basicURL.getAllElements::setJustci_path( $_ci_path )."\" target=\"_self\" title=\"".stripslashes( $this->contents['title'] )."\" onfocus=\"this.blur()\">".stripslashes( $this->contents['title'] )."</a></h1>" : "\n<h1 class=\"page_title\">".stripslashes( $this->contents['title'] )."</h1>";
				}
			}
		}
		$_page .= "<div class=\"inside-article\">";
		//Share icons for facebook and twitter under page TITLE
		if ( CONFIG_SHARING_ICONS == "true" ){
			$sharing_icons = new getGeneral($baseURL);
			$_page .= $sharing_icons->getAddthis( $this->basicURL.getAllElements::setJustci_path( $_ci_path ), stripslashes( $this->contents['title'] ) );
		}
		
		$_page .= ( CONFIG_DISPLAY_PAGE_METAS == "true" ) ? "\n<div class=\"pages_meta\">\nWRITTEN BY ".stripslashes( $this->contents['created_by'] )." ON ".stripslashes( $this->contents['created_date'] )."[".stripslashes( $this->contents['views'] )."]\n</div>" : "";
		$_page .= ( _getCheckNullorNot( $this->contents['urls'] ) ) ? "\n<div class=\"pages_urls\">\n".$this->GeneralItems->getProperLink( stripslashes( $this->contents['urls'] ) )."\n</div>" : "";
		$_page .= ( _getCheckNullorNot( $this->contents['embed_code'] ) ) ? "\n<div class=\"pages_embed\">\n".stripslashes( $this->contents['embed_code'] )."\n</div>" : "";
		$_page .= "\n" . stripslashes( $this->dphp->getDirectPHPS( $this->contents['fulltxt'], false, $this->contents['id'] ) )."\n";

		if ( $this->contents['sectionid'] != 0 ){
			$sectionType = $this->dbConnect->getAllContents( "categories","id = '{$this->contents['categoriesid']}' AND group_level >= '{$Gid}' AND access_level >= '{$Userlevel}' AND publish = '1' AND status = '1'" );
			$boardList .= $this->getBoardList( $this->contents['id'], $this->contents['sectionid'], $this->contents['categoriesid'], $sectionType['ctype'], $kPages );
			$boardlist_tail = $sectionType['ctype'] == "blog" ? "_board" : "";
			$_page .= "\n<div class=\"pages_articlelist".$boardlist_tail."\">".$boardList."\n</div>";
		}
		
		$_page .= "</div>\n<!--// Eof Main Contents //-->";

		$this->dbConnect->getQuery( "UPDATE pages SET views = views + 1 WHERE publish = '1' AND status = '1' AND id = '{$kPages['pid']}'" );

		return $_page;
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To print out the board content
 *  ------------------------------------------------------------------------- */
	public function getDisplayBoard( $kPages, $iWhere ){
		global $_ci_path, $Config_allowed_image_extension;

		//Display basic page information
		$pgcontents	= $this->dbConnect->getPageContent( $kPages['pid'], "frontpage = '0' AND ".$iWhere );
		$_page			= "\n<!--// Bof Main Contents //-->";
		if ( CONFIG_SHOW_TITLE === "true" ){
			if ( _getCheckNullorNot( $pgcontents['title_images'] ) && CONFIG_USE_IMAGE_PAGE_TITLE === "true" ){
				$_page .= "\n<div class=\"pages_img_title\">". getDisplayImg( $pgcontents['title_images'], 500, false, $pgcontents['title'], false ) ."</div>";
			}
			else {
				//Display page text or image TITLE with or without link
				if ( CONFIG_USE_TXT_LINK === "true" ){
					$cipg_array	= explode("&", $_ci_path);
					if ( count($cipg_array) > 1 ){
						$add_later 			= end($cipg_array);
						$other_ci_path 		= rtrim(str_replace("&".end($cipg_array), "", $_ci_path));
						$ci_array				= explode("/", $other_ci_path);
						$justpage_ci_path 	= ( is_numeric(end($ci_array)) ) ? rtrim(str_replace(end($ci_array), "", $other_ci_path), "/")."&amp;".$add_later : "";
					}
					else {
	 					$ci_array				= explode("/", $_ci_path);
						$justpage_ci_path	= ( is_numeric(end($ci_array)) ) ? rtrim(str_replace(end($ci_array), "", $_ci_path), "/") : "";
					}
					$_page .= "\n<h1 class=\"page_title\"><a href=\"".$this->basicURL.getAllElements::setJustci_path( $justpage_ci_path )."\" target=\"_self\" title=\"".stripslashes( $pgcontents['title'] )."\" onfocus=\"this.blur()\">".stripslashes( $pgcontents['title'] )."</a></h1>";
				}
				else {
					$_page .= "\n<h1 class=\"page_title\">".stripslashes( $pgcontents['title'] )."</h1>";
				}
			}
		}
		$_page .= "\n<!--// Eof Main Contents //-->\n";

		//Display Board article
		$iWhere 			=" b.group_level >= '".getAllElements::setAccesslevel("gid")."' AND b.access_level >= '".getAllElements::setAccesslevel("ulevel")."' AND b.publish = '1' AND b.status = '1'";
		$this->contents	= $this->dbConnect->getBoardContent( $kPages, $iWhere );

		//Share icons for facebook and twitter
		if ( CONFIG_SHARING_ICONS == "true" ){
			$sharing_icons = new getGeneral($baseURL);
			$_page 			  .= $sharing_icons->getAddthis( $this->basicURL.getAllElements::setJustci_path( $_ci_path ), trim( stripslashes( $this->contents['title'] ) ));
		}

		if ( in_array( $kPages['ctype'], $this->castlist )){
			$created_by 		= _getCheckNullorNot($this->contents['host']) ? TEXT_AUTHOR ."<strong>". stripslashes( $this->contents['host'] ) ."</strong>" : "" ;
			$verses				= _getCheckNullorNot( $this->contents['casting_description'] ) ? "\n[ ".stripslashes( $this->contents['casting_description'] )." ]" : "";
		}
		else {
			$member_alias	= $this->dbConnect->getMemberInfo( $this->contents['created_by'] );
			$created_by 		= !_getCheckNullorNot( $this->contents['created_by_alias'] ) ? stripslashes( $member_alias['members_alias'] ) : stripslashes( $this->contents['created_by_alias'] );
		}
        $castingDate     = _getLongFormatDate( $this->contents['casting_date'] );
		$formatedDate	= _getLongFormatDate( $this->contents['created_date'] );
		$shortDate  		= _getShortFormatDate( $this->contents['created_date'] );
		$urls 				= _getCheckNullorNot( $this->contents['urls'] ) ? "\n<div class=\"fileink\"><a href=\"".$this->GeneralItems->getProperLink( stripslashes( $this->contents['urls'] ) )."\" title=\"\" target=\"_blank\" onfocus=\"this.blur()\">".stripslashes( $this->contents['urls'] )."</a></div><br/><div class=\"bottomlineINitem\"></div>" : "";

		if ( _getCheckNullorNot( $this->contents['filename'] ) ){
			$filen_array = explode( ";", $this->contents['filename'] );
			for ( $z=0; $z < count( $filen_array ); $z++ ){
				$filename[$z] 		= explode( "/", $filen_array[$z] );
				$justfilename[$z]	= end( $filename[$z] );
				$display_css 		= ( count( $filen_array ) == 1 ) ? "fileink" : ( $z+1 )%2 == 0 ? "fileink" : "fileinkleft";
				
				$attachedfile		   .= "\n<div class=\"{$display_css}\"><a href=\"".$this->basicURL."/download/".urldecode( stripslashes( $filen_array[$z] ) )."\" title=\"\" target=\"_self\" onfocus=\"this.blur()\">".stripslashes( $justfilename[$z] )."</a></div>";
				$attachedfile		   .= !_getCheckNullorNot( $this->contents['linkfile'] ) ? $z == count( $filen_array )-1 ? "<br/><div class=\"bottomlineINitem\"></div>":"" : "";

				//Getting file extension only to dispaly an image
				$filetype[$z] 		= explode( ".", $filen_array[$z] );
				$extension[$z]	= end( $filetype[$z] );
				if ( in_array( $extension[$z], $Config_allowed_image_extension ) ){
					$imgs .= "\n<p class=\"pageimg\">".getDisplayImg( $filen_array[$z], CONFIG_IMG_SIZE_IN_PAGE, false, $justfilename[$z], false )."</p>";
				}

				if ( $extension[$z] == "mp3" ){
					if ( file_exists( $_SERVER['DOCUMENT_ROOT'] .'/'. CONFIG_FILES_UPLOAD_ROOT . stripslashes( $filen_array[$z] ) ) ){
						$mpplayer .= "\n<p class=\"mptitle engtxt\">".TEXT_MP3."</p><audio src=\"".$this->basicURL ."/". CONFIG_FILES_UPLOAD_ROOT . stripslashes( $filen_array[$z] )."\" preload=\"none\"/></audio>";
					}
				}
			}
		}

		if ( _getCheckNullorNot( $this->contents['linkfile'] ) ){
				$filename 		= explode( "/", $this->contents['linkfile'] );
				$justfilename	= end( $filename );
				$attachedfile		   .= "\n<div class=\"fileink bottomline\"><a href=\"".$this->basicURL."/download/".urldecode( stripslashes( $this->contents['linkfile'] ) )."\" title=\"\" target=\"_self\" onfocus=\"this.blur()\">".stripslashes( $justfilename )."</a></div>";

				$filetype 		= explode( ".", $justfilename );
				if ( end( $filetype ) == "mp3" ){
					if ( file_exists( $_SERVER['DOCUMENT_ROOT'] .'/'. CONFIG_FILES_UPLOAD_ROOT . stripslashes( $this->contents['linkfile'] ) ) ){
						$mpplayer .= "\n<p class=\"mptitle engtxt\">".TEXT_MP3."</p><audio src=\"".$this->basicURL ."/". CONFIG_FILES_UPLOAD_ROOT . stripslashes( $this->contents['linkfile'] )."\" preload=\"none\"/></audio>";
					}
				}
		}
		$embeds = ( _getCheckNullorNot( $this->contents['embed_bigcode'] ) ) ? "\n<div class=\"embed_video\">\n".stripslashes( $this->contents['embed_bigcode'] )."</div>" : "";
		
		if ( in_array( $kPages['ctype'], $this->castlist ) ){
			$embeds = "\n<div class=\"authorArea\">
									<span class=\"author\">". $created_by ."&nbsp; ". $verses ."</span>
									<span class=\"counts\">
										<span class=\"wdate\">". $castingDate."</span>
										<span class=\"read\">VIEW <span class=\"num\">". $this->contents['views'] ."</span></span>
									</span>
								</div>";	
			if ( _getCheckNullorNot( $this->contents['embed_bigcode'] ) ){
				$embeds .= "\n<div class=\"embed_video\">\n".stripslashes( $this->contents['embed_bigcode'] )."</div>";
			}								
		}

		$fulltxt				= _getCheckNullorNot( $this->contents['fulltxt'] ) ? stripslashes( $this->contents['fulltxt'] ) : stripslashes( $this->contents['summarytxt'] );
		$allContents		= $urls . $imgs . $embeds . $mpplayer .  $fulltxt;
		$returnURL		= $this->GeneralItems->getMenuURL( $kPages['pid'] );
		$returnURL_tail	=  ( _getCheckNullorNot( $kPages['page_no'] ) && $kPages['page_no'] > 1 ) ? "&amp;".$kPages['page_no'] : "";
		//Set Buttons to delete or modify
		if ( ( $_SESSION['session_user_id'] == $this->contents['created_by'] ) && $_SESSION['guest'] == 1 ){
			$editBtns = "&nbsp;<a href=\"".$this->basicURL.$this->contents['alias'].$kPages['external'].$returnURL_tail."/delete\" targer=\"_self\" title=\"\" class=\"deleteBTN page_numbers\" onfocus=\"this.blur()\">".TEXT_DELET."</a>&nbsp;<a href=\"".$this->basicURL.$this->contents['alias'].$kPages['external'].$returnURL_tail."/edit\" targer=\"_self\" title=\"\" class=\"wirteBTN page_numbers\" onfocus=\"this.blur()\">".TEXT_EDIT."</a>&nbsp;&nbsp;";
		}
		
		//Set Buttons to delete or modify
		$editBTNgroup	= ( isset( $_SESSION )  && $_SESSION['session_user_id'] && $_SESSION['guest'] == 1 ) ? $editBtns."<a href=\"".$this->basicURL.$returnURL['alias'].$returnURL_tail."\" targer=\"_self\" title=\"\" class=\"backTolistBTN page_numbers\" onfocus=\"this.blur()\">".TEXT_LIST."</a>" : "&nbsp;<a href=\"".$this->basicURL.$returnURL['alias'].$returnURL_tail."\" targer=\"_self\" title=\"\" class=\"backTolistBTN page_numbers\" onfocus=\"this.blur()\">".TEXT_LIST."</a>";
		$article_title		= "<a href=\"".$this->basicURL.getAllElements::setJustci_path( $_ci_path )."\" target=\"_self\" title=\"".stripslashes( $this->contents['title'] )."\" onfocus=\"this.blur()\">".stripslashes( $this->contents['title'] )."</a>";

		if ( in_array( $kPages['ctype'], $this->castlist ) ){
			$_boarditem = sprintf( $this->getListFormat( "castingitem" ), $article_title, $attachedfile, $allContents );
		}
		else {
			$_boarditem = sprintf( $this->getListFormat( "boarditem" ), $article_title, $created_by, $verses, $this->contents['views'], $shortDate, $attachedfile, $allContents );
		}
		$bItems  = "\n<!--// Bof Contents //-->\n<dl class=\"pages_component\">";
		$bItems .= "\n\t<dd class=\"pages_board\">".$_boarditem."</dd>";
		$bItems .= "\n\t<dd class=\"pages_board_list\">". $this->getPageBoardList( $kPages['tbname'], $this->contents['tid'], $this->contents['sectionid'], $this->contents['categoriesid'], 5, $add_later) ."</dd>";
		$bItems .= "\n\t<dd class=\"btngroup\">".$icons."</dd>\n</dl>\n\t<div class=\"sharingbtns\">".$editBTNgroup."<br class=\"clearfix\"></div><!--// Eof Contents //-->\n";

		$this->dbConnect->getQuery( "UPDATE {$kPages['tbname']} SET views = views + 1 WHERE publish = '1' AND status = '1' AND id = '{$kPages['tid']}'" );

		return $_page . $bItems;
	}


/** -------------------------------------------------------------------------
 * [06/16/2014]:: To pritnt out all kind of list
 *  ------------------------------------------------------------------------- */
	public function getPageBoardList( $tname, $itemid, $sectionid, $categoriesid, $max = 3, $noPage ){
			global $Config_allowed_image_extension;
			
			$list_tail = " AND sectionid = '{$sectionid}' AND categoriesid = '{$categoriesid}'";
			$list_query = $this->dbConnect->getQuery( "SELECT b.id, b.title, b.title_alias, b.thumbnail, b.summarytxt, b.casting_description, b.qrcode, b.fulltxt, b.linkfile, b.filename, b.filesize, b.urls, b.host, b.casting_description, b.casting_date, b.embed_bigcode, b.embed_code, b.sectionid, b.categoriesid, b.ordering, b.metatitle, b.metakey, b.metadesc, b.access_level, b.publish, b.status, b.notice, b.linkOpt, b.loginDownload, b.downloadAlias, b.views, b.votes, b.password, b.user_ip, b.modified_date, b.modified_by, b.created_date, b.created_by, b.created_by_alias, b.publish_date, o.external, o.pid, m.alias FROM {$tname} b, opensef  o LEFT JOIN menu m ON o.pid = m.pid  WHERE b.id = o.tid AND o.tbname = '{$tname}' AND b.notice = '0' AND o.publish = '1' AND m.status = '1' AND ( b.id > {$itemid} OR b.id < {$itemid} ) AND b.access_level >= '".getAllElements::setAccesslevel("ulevel")."' AND b.group_level >= '".getAllElements::setAccesslevel("gid")."' AND b.publish = '1' AND b.status = '1' {$list_tail} ORDER BY b.ordering DESC LIMIT 0, {$max}" );

			$count = 1;
			while ( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
				$bgClass 			= ( $count/2 ) == floor( $count/2 ) ? " class=\"list_even" : " class=\"list_odd";
				$casting_date	= ( _getCheckNullorNot( $list_values['casting_date'] ) && $list_values['casting_date']  != "0000-00-00" ) ? "<span class=\"castingDate\">"._getLongFormatDate( stripslashes( $list_values['casting_date'] ) )."</span>" : "";
				$short_dscp		= _getCheckNullorNot( $list_values['summarytxt'] ) ? stripslashes( $list_values['summarytxt'] ) : stripslashes( $list_values['casting_description'] );
				$casting_dscp	= _getCheckNullorNot( $short_dscp) ? $short_dscp : _mb_strcut( stripslashes( $list_values['fulltxt'] ), 0, 250 );
				$formatedDate	= _getShortFormatDate( $list_values['created_date'] );
				$maxWidth		= 120;
				$eThumbnail  	= "";
				$nailthumb			= true;
				if ( _getCheckNullorNot( $list_values['thumbnail'] ) ){
						$location_src = $_SERVER['DOCUMENT_ROOT'] .UNDER_SUBFOLDER ."/". CONFIG_FILES_UPLOAD_ROOT.$list_values['thumbnail'];
						if ( file_exists( $location_src ) ){
							$image_size 			= getimagesize( $location_src );
							$percent_resizing	= round( ( $maxWidth / $image_size[0] ) * 100 );
							$new_height  		= round( ( $percent_resizing / 100 )  * $image_size[1] );
							$eThumbnail 			= getDisplayImg( $list_values['thumbnail'], $maxWidth, $nailthumb, stripslashes( $list_values['title'] ), false, "" );
						}
				}
				else {						
						if ( _getCheckNullorNot( $list_values['filename'] ) ){
								$listimgs				= explode( ";", $list_values['filename']);
								if ( count($listimgs) > 1 ){
									$filename			= explode( ".", $listimgs[0] );
									$imgfile_name	= $listimgs[0];
								}
								else {
									$filename		 	= explode( ".", $list_values['filename'] );
									$imgfile_name	= $list_values['filename'];
								}
								
								if ( in_array( $filename[sizeof( $filename )-1], $Config_allowed_image_extension ) ){
									$location_srcs			= $_SERVER['DOCUMENT_ROOT'] . UNDER_SUBFOLDER ."/". CONFIG_FILES_UPLOAD_ROOT.$imgfile_name;
									$image_sizes			= getimagesize( $location_srcs );
									$percent_resizing		= round( ( $maxWidth / $image_sizes[0] ) * 100 );
									$new_heights 			= round( ( $percent_resizing / 100 )  * $image_sizes[1] );
									$eThumbnail				= getDisplayImg( $imgfile_name, $maxWidth, $nailthumb, stripslashes( $list_values['title'] ), false, "" );
								}								
						}
				}

				if ( $list_values['linkOpt'] == "1" ){
					if ( $list_values['loginDownload'] == "1" ){
						if ( !_getCheckNullorNot( $list_values['downloadAlias'] ) ){
							$pURL = _getCheckNullorNot( $list_values['linkfile'] ) ? $this->basicURL ."/download/". urldecode( trim( $list_values['linkfile'], "/" ) ) : "";
						}
						else {
							$pURL = _getCheckNullorNot( $list_values['linkfile'] ) ? $this->basicURL ."/". urldecode( trim( $list_values['downloadAlias'], "/" ) ) : "";
						}
					}
					else {
						$pURL = _getCheckNullorNot( $list_values['linkfile'] ) ? $this->basicURL ."/download/". urldecode( trim( $list_values['linkfile'], "/" ) ) : "";
					}
				}
				else {
					$pURL = ( _getCheckNullorNot($noPage) && $noPage > 1 ) ? $this->basicURL.$list_values['alias'].$list_values['external']."&amp;".$noPage : $this->basicURL.$list_values['alias'].$list_values['external'];
				}

				$thumb_txt = _getCheckNullorNot( $list_values['summarytxt'] ) ? "<span class=\"txtOption\">". $casting_date . $list_values['summarytxt'] ."</span>" : "<span class=\"txtOption\">". $casting_date . _mb_strcut( stripslashes( $list_values['fulltxt'] ), 0, 250 ) ."</span>";

				if ( _getCheckNullorNot( $eThumbnail ) ){
					$_boardlists .= sprintf( $this->getListFormat( "list_thumb_txt" ), $bgClass, $pURL, stripslashes( $list_values['title'] ), $eThumbnail, $pURL, stripslashes( $list_values['title'] ), stripslashes( $list_values['title'] ), $thumb_txt, $pURL, stripslashes( $list_values['title'] ) );
				}
				else {
					$_boardlists .= sprintf( $this->getListFormat( "list_thumb_txt_merge" ), $bgClass, $pURL, stripslashes( $list_values['title'] ), stripslashes( $list_values['title'] ), $thumb_txt, $pURL, stripslashes( $list_values['title'] ) );
				}
				$count++;
			}
			$_boardlist 	.= "<table class=\"pageboardList\">". $_boardlists ."\n\t</table>";

			return $_boardlist;
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To pritnt out all kind of list
 *  ------------------------------------------------------------------------- */
	public function getBoardList( $pageid, $sectionid, $categoriesid = 0, $display_type, $page ){
			$_boardlist  = "\n\t";

			$section_items = $this->dbConnect->getAllContents( "sections", "id = '{$sectionid}' AND publish = '1' AND status = '1' AND access_level >= '".getAllElements::setAccesslevel("ulevel")."' AND group_level >= '".getAllElements::setAccesslevel("gid")."'" );

			$caarray = explode( ";", $categoriesid );
			if ( count( $caarray ) > 1 ){
				$list_tail = " AND ( ";
				for ( $w=0; $w < count( $caarray ); $w++ ){
					$divider = $w == count( $caarray )-1 ? "":"OR";
					$list_tail .= " categoriesid = '{$caarray[$w]}'" . $divider;
				}
				$list_tail .= " )";
			}
			else {
				$list_tail = ( $categoriesid != 0 ) ? " AND categoriesid = '{$categoriesid}'" : "";
			}

			$total_list = $this->dbConnect->getTotalNumber( $section_items['tbname'], "notice = '0' AND status = '1' AND publish = '1' AND access_level >= '".getAllElements::setAccesslevel("ulevel")."' AND group_level >= '".getAllElements::setAccesslevel("gid")."' {$list_tail}" );
			$total_notice = $this->dbConnect->getTotalNumber( $section_items['tbname'], "notice = '1' AND status = '1' AND publish = '1' AND access_level >= '".getAllElements::setAccesslevel("ulevel")."' AND group_level >= '".getAllElements::setAccesslevel("gid")."' {$list_tail}" );
			
			if ( $total_list + $total_notice > 0 ){
				if ( $display_type == "photo" ){
					$maxCols = CONFIG_NUMBER_IMG;
					$recIdx = 0;
				}
				$total_page = ceil( ( $total_list - $total_notice )/CONFIG_HOW_MANY_ARTICLES_PER_PAGE );
				$noPage = isset( $page['page_no'] ) ? $page['page_no'] : 1;

				$first = $total_list == 0 ? 1 : CONFIG_HOW_MANY_ARTICLES_PER_PAGE *( $noPage-1 );
				$last = $total_list == 0 ? 0 : CONFIG_HOW_MANY_ARTICLES_PER_PAGE * $noPage;
				$loop_number = $total_list - ( $noPage-1 ) * CONFIG_HOW_MANY_ARTICLES_PER_PAGE;
				$total_loop_num = $loop_number;

				if ( $total_notice > 0 ){
					$list_query = $this->dbConnect->getQuery( "SELECT b.id, b.title, b.title_alias, b.thumbnail, b.summarytxt, b.casting_description, b.qrcode, b.fulltxt, b.linkfile, b.filename, b.filesize, b.urls, b.host, b.casting_description, b.casting_date, b.embed_bigcode, b.embed_code, b.sectionid, b.categoriesid, b.ordering, b.metatitle, b.metakey, b.metadesc, b.access_level, b.publish, b.status, b.notice, b.linkOpt, b.loginDownload, b.downloadAlias, b.views, b.votes, b.password, b.user_ip, b.modified_date, b.modified_by, b.created_date, b.created_by, b.created_by_alias, b.publish_date, o.external, o.pid, m.alias FROM {$section_items['tbname']} b, opensef  o LEFT JOIN menu m ON o.pid = m.pid  WHERE b.id = o.tid AND o.tbname = '{$section_items['tbname']}' AND b.notice = '1' AND o.publish = '1' AND m.status	= '1' AND b.access_level >= '".getAllElements::setAccesslevel("ulevel")."' AND b.group_level >= '".getAllElements::setAccesslevel("gid")."' AND b.publish = '1' AND b.status = '1' {$list_tail} ORDER BY b.ordering DESC" );

					while ( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
						$member_alias		= $this->dbConnect->getMemberInfo( $list_values['created_by'] );
						$created_by 			= !_getCheckNullorNot( $list_values['created_by_alias'] ) ? stripslashes( $member_alias['members_alias'] ) : stripslashes( $list_values['created_by_alias'] );
						$pURL 					= ( $noPage !== 1 ) ? $this->basicURL.$list_values['alias'].$list_values['external']."&amp;".$noPage : $this->basicURL.$list_values['alias'].$list_values['external'];
						$shortDate_notice  = _getShortFormatDate( $list_values['created_date'] );
						$file_name 			= !_getCheckNullorNot( $list_values['filename'] ) ?  "-" : "<a href=\"{$this->basicURL}{$pURL}\" title=\"\" target=\"_self\" onfocus=\"this.blur()\" class=\"iconDownload\"><img src=\"". $this->basicURL ."/images/iconDownload.gif\" width=\"12\" height=\"12\"></a>";

						$_boardlists .= sprintf( $this->getListFormat( $display_type ), " class=\"list_notice\"", "n", $pURL, stripslashes( $list_values['title'] ), stripslashes( $list_values['title'] ), "-", $shortDate_notice, $list_values['views'], "" );
					}
				}

				$dBlist_query = $this->dbConnect->getQuery( "SELECT b.id, b.title, b.title_alias, b.thumbnail, b.summarytxt, b.casting_description, b.qrcode, b.fulltxt, b.linkfile, b.filename, b.filesize, b.urls, b.host, b.casting_description, b.casting_date, b.embed_bigcode, b.embed_code, b.sectionid, b.categoriesid, b.ordering, b.metatitle, b.metakey, b.metadesc, b.access_level, b.publish, b.status, b.notice, b.linkOpt, b.loginDownload, b.downloadAlias, b.views, b.votes, b.password, b.user_ip, b.modified_date, b.modified_by, b.created_date, b.created_by, b.created_by_alias, b.publish_date, o.external, o.pid, m.alias FROM {$section_items['tbname']} b, opensef  o LEFT JOIN menu m ON o.pid = m.pid  WHERE b.id = o.tid AND o.tbname = '{$section_items['tbname']}' AND b.notice = '0' AND o.publish = '1' AND m.status	= '1' AND b.access_level >= '".getAllElements::setAccesslevel("ulevel")."' AND b.group_level >= '".getAllElements::setAccesslevel("gid")."' AND b.publish = '1' AND b.status = '1' {$list_tail} ORDER BY b.ordering DESC LIMIT {$first}, ".CONFIG_HOW_MANY_ARTICLES_PER_PAGE."" );

				$count = 1;
				while ( $list_values = $this->dbConnect->getFetch_Array( $dBlist_query ) ){
							$recIdx++;
							$bgClass 					= ( $loop_number/2 ) == floor( $loop_number/2 ) ? " class=\"list_even" : " class=\"list_odd";
							$bgClassB 				= ( $count % 2 == 0 ) ? " even" : " odd";
							$member_alias			= $this->dbConnect->getMemberInfo( $list_values['created_by'] );
							$casting_host			= _getCheckNullorNot( $list_values['host'] ) ? stripslashes( $list_values['host'] ) : stripslashes( $member_alias['members_alias'] );
							$casting_date			= ( _getCheckNullorNot( $list_values['casting_date'] ) && $list_values['casting_date']  != "0000-00-00" ) ? "<span class=\"castingDate\">"._getLongFormatDate( stripslashes( $list_values['casting_date'] ) )."</span>" : "-";
							$shortDate_created	= _getShortFormatDate( $list_values['created_date'] );
							$short_dscp				= _getCheckNullorNot( $list_values['summarytxt'] ) ? stripslashes( $list_values['summarytxt'] ) : stripslashes( $list_values['casting_description'] );
							$casting_dscp			= _getCheckNullorNot( $short_dscp) ? $short_dscp : _mb_strcut( stripslashes( $list_values['fulltxt'] ), 0, 250 );
							$created_by 				= !_getCheckNullorNot( $list_values['created_by_alias'] ) ? stripslashes( $member_alias['members_alias'] ) : stripslashes( $list_values['created_by_alias'] );

							if ( $list_values['linkOpt'] == "1" ){
									if ( $list_values['loginDownload'] == "1" ){
											if ( !_getCheckNullorNot( $list_values['downloadAlias'] ) ){
												$pURL = _getCheckNullorNot( $list_values['linkfile'] ) ? $this->basicURL ."/download/". urldecode( trim( $list_values['linkfile'], "/" ) ) : "";
											}
											else {
												$pURL = _getCheckNullorNot( $list_values['linkfile'] ) ? $this->basicURL ."/". urldecode( trim( $list_values['downloadAlias'], "/" ) ) : "";
											}
									}
									else {
											$pURL = _getCheckNullorNot( $list_values['linkfile'] ) ? $this->basicURL ."/download/". urldecode( trim( $list_values['linkfile'], "/" ) ) : "";
									}
							}
							else {
									$pURL = ( $noPage !== 1 ) ? $this->basicURL.$list_values['alias'].$list_values['external']."&amp;".$noPage : $this->basicURL.$list_values['alias'].$list_values['external'];
							}

							$file_name = !_getCheckNullorNot( $list_values['filename'] ) ?  "-" : "<a href=\"{$pURL}\" title=\"\" target=\"_self\" onfocus=\"this.blur()\" class=\"iconDownload\"><img src=\"". $this->basicURL ."/images/iconDownload.gif\" width=\"12\" height=\"12\"></a>";

							$eThumbnail  = "";
							if ( _getCheckNullorNot( $list_values['thumbnail'] ) ){
								$location_src = $_SERVER['DOCUMENT_ROOT'] .UNDER_SUBFOLDER ."/". CONFIG_FILES_UPLOAD_ROOT.$list_values['thumbnail'];
								if ( file_exists( $location_src ) ){
									$maxWidth			= 120;
									$nailthumb				= true;
									$image_size 			= getimagesize( $location_src );
									$percent_resizing	= round( ( $maxWidth / $image_size[0] ) * 100 );
									$new_height  		= round( ( $percent_resizing / 100 )  * $image_size[1] );
									$eThumbnail 			= getDisplayImg( $list_values['thumbnail'], $maxWidth, $nailthumb, stripslashes( $list_values['title'] ), false, "" );
								}
							}

							if ( $display_type == "board" ){
								$_boardlists .= sprintf( $this->getListFormat( $display_type ), $bgClass."\"", $loop_number, $pURL, stripslashes( $list_values['title'] ), stripslashes( $list_values['title'] ), $file_name, $shortDate_created,  $list_values['views'] );
							}
							elseif ( $display_type == "list_thumb_txt" ){
									$thumb_txt = _getCheckNullorNot( $list_values['summarytxt'] ) ? "<span class=\"txtOption\">". $casting_date . $list_values['summarytxt'] ."</span>" : "<span class=\"txtOption\">". $casting_date . _mb_strcut( stripslashes( $list_values['fulltxt'] ), 0, 250 ) ."</span>";

									if ( _getCheckNullorNot( $eThumbnail ) ){
										$_boardlists .= sprintf( $this->getListFormat( $display_type ), $bgClass, $pURL, stripslashes( $list_values['title'] ), $eThumbnail, $pURL, stripslashes( $list_values['title'] ), stripslashes( $list_values['title'] ), $thumb_txt, $pURL, stripslashes( $list_values['title'] ) );
									}
									else {
										$_boardlists .= sprintf( $this->getListFormat( "list_thumb_txt_merge" ), $bgClass, $pURL, stripslashes( $list_values['title'] ), stripslashes( $list_values['title'] ), $thumb_txt, $pURL, stripslashes( $list_values['title'] ) );
									}
							}
							elseif ( $display_type == "onlythumb" ){
								$_boardlists .= sprintf( $this->getListFormat( $display_type ), $bgClass."\"", $loop_number, $pURL, stripslashes( $list_values['title'] ), stripslashes( $list_values['title'] ), $created_by, $file_name, $shortDate_created,  $list_values['views'] );
							}
							elseif ( $display_type == "casting" ){
								$_boardlists .= sprintf( $this->getListFormat( $display_type ), $bgClass. " vodlistup\"", $list_values['embed_bigcode'], $pURL, stripslashes( $list_values['title'] ), $loop_number, stripslashes( $list_values['title'] ), $casting_host, $casting_date, $casting_dscp );
							}
							elseif ( $display_type == "castingthumblist" ){
								$getThumb = _getCheckNullorNot( $eThumbnail ) ? $eThumbnail : "<div class=\"vod_wrap-".$count."\">". $list_values['embed_bigcode'] ."</div>";

								// $first = 0 means that current page is first
								if ( $first == 0 ){
									$_boardlists .= $count == 1 ? "<ul class=\"castingthumblist\">" : "";
								}
								else {
									$_boardlists .= $count == 1 ? "<ul class=\"castingthumblist_all\">" : "";
								}
								// ONE(1) content in the first page
								if ( $total_list == 1 && $first == 0 ){
										if ( $count != 1 ){
											$_boardlists .= sprintf( $this->getListFormat( $display_type ), " class=\"list_".$count. $bgClassB."\"", "vod_wrap-".$count, $list_values['embed_bigcode'], $casting_dscp_, $pURL, stripslashes( $list_values['title'] ), stripslashes( $list_values['title'] ) );
										}
										else {
											$casting_dscp_ 	= $casting_date . $casting_dscp;
										    $_boardlists .= sprintf( $this->getListFormat( "castingthumblistOne" ), " class=\"list_". $count . $bgClassB."\"", "vod_wrap-".$count, $list_values['embed_bigcode'], $pURL, stripslashes( $list_values['title'] ), stripslashes( $list_values['title'] ), $casting_dscp_ );
										}
								}
								// FIVE(5) contents in the first page
								elseif ( $count < 5 && $first == 0 ){
									if ( _getCheckNullorNot( $extra_odd ) ){
										$_boardlists .= sprintf( $this->getListFormat( "castinglist" ), " class=\"list". $bgClassB.$extra_odd."\"", "listvod_wrap".$extra_odd, $getThumb, $pURL, stripslashes( $list_values['title'] ), stripslashes( $list_values['title'] ), $casting_date );
									}
									else {
										if ( $total_page == 1 && $total_loop_num == 2 ){
											$casting_dscp_ 	= $casting_date . $casting_dscp;
											$_boardlists .= sprintf( $this->getListFormat( "castingthumblistOne" ), " class=\"list". $count . $bgClassB."\"", "vod_wrap-".$count, $list_values['embed_bigcode'], $pURL, stripslashes( $list_values['title'] ), stripslashes( $list_values['title'] ), $casting_dscp_ );
										}
										else {
											$displayopt  		= $count == 1 ? "castingthumblistOne" : $display_type;
											$casting_dscp_ 	= $count == 1 ? $casting_date . $casting_dscp : $casting_date;
											if ( _getCheckNullorNot($list_values['embed_bigcode']) ){
													$_boardlists .= sprintf( $this->getListFormat( $displayopt ), " class=\"list_". $count . $bgClassB."\"", "vod_wrap-".$count, $list_values['embed_bigcode'], $pURL, stripslashes( $list_values['title'] ), stripslashes( $list_values['title'] ), $casting_dscp_ );
											}
											else {
													$_boardlists .= sprintf( $this->getListFormat( $displayopt."_a" ), " class=\"list_". $count . $bgClassB."\"", "img_wrap-".$count, $getThumb, $pURL, stripslashes( $list_values['title'] ), stripslashes( $list_values['title'] ), $casting_dscp_ );
											}
										}
									}
								}
								else {
									if ( $total_page == 1 && $total_loop_num < CONFIG_HOW_MANY_ARTICLES_PER_PAGE ){
										$extra_odd = ( $bgClassB == " odd" && $count == 5 && $total_loop_num == 5 ) ? "_full" : "";
										$_boardlists .= ( $count == 5 && ( $loop_number == 2 || $total_loop_num == 5 ) ) ? "</ul>\n<ul class=\"castingthumblist5\">" : "";
									}
									$_boardlists .= sprintf( $this->getListFormat( "castinglist" ), " class=\"list".$count . $bgClassB.$extra_odd."\"", "listvod_wrap".$extra_odd, $getThumb, $pURL, stripslashes( $list_values['title'] ), stripslashes( $list_values['title'] ), $casting_date );
								}

								if ( $loop_number >= 2 && $loop_number <= 3 && $first == 0 ){
									$addvalue	= ( $total_page == 1 && $total_loop_num >= 4 ) ? 1 : 2;
									$_boardlists .= $count == 1 ? "</ul>\n<ul class=\"castingthumblist". ($count+$addvalue) ."\">" : "";
								}
								elseif ( $loop_number > 3 && $first == 0 ){
									$_boardlists .= $count == 1 ? "</ul>\n<ul class=\"castingthumblist2\">" : "";
									$_boardlists .= $count == 4 ? "</ul>\n<ul class=\"castingthumblist5\">" : "";
								}
							}
							elseif ( $display_type == "blog" ){
								$_boardlists .= sprintf( $this->getListFormat( $display_type ), "class =\"bloglist\"", $pURL, stripslashes( $list_values['title'] ), sprintf("%02d",$loop_number).". ".stripslashes( $list_values['title'] ), $created_by, $list_values['views'], $shortDate_created, $list_values['fulltxt'] );
							}
							elseif ( $display_type == "photo" ){
								if ($count % $maxCols == 1){
									$_boardlists .= "<div class=\"photoset-row\">";
								}

								$newTitle		= _mb_strcut( stripslashes( $list_values['title'] ), 0, 35 );
								$shtmlTitle	= $list_values['title'];

								if ( _getCheckNullorNot( $list_values['thumbnail'] ) ){
									$location_src = $_SERVER['DOCUMENT_ROOT'] .UNDER_SUBFOLDER ."/". CONFIG_FILES_UPLOAD_ROOT.$list_values['thumbnail'];
									if ( file_exists( $location_src ) ){
										$nailthumb		= true;
										$image_size	= getimagesize( $location_src );
										$eThumbnail	= getDisplayImg( $list_values['thumbnail'], $image_size[0] , $nailthumb, stripslashes( $list_values['title'] ), false, "" );
									}
								}
								else {
									$listimg = explode( ";", $list_values['filename']);
									$location_src	= $_SERVER['DOCUMENT_ROOT'] .UNDER_SUBFOLDER ."/". CONFIG_FILES_UPLOAD_ROOT.$listimg[0];
									$image_size		= getimagesize( $location_src );
									$eThumbnail		= getDisplayImg( $listimg[0], $image_size[0] , $nailthumb, stripslashes( $list_values['title'] ), false, "" );
								}

								$_boardlists		.= "\n\t<figure class=\"photoset-item\">\n\t<a href=\"".$this->basicURL . $list_values['alias'] . $list_values['external']."\" class=\"item{$count}{$eclass}\" title=\"{$shtmlTitle}\" target=\"_self\" onfocus=\"this.blur()\">\n\t{$eThumbnail}</a><figcaption>{$shtmlTitle}</figcaption>\n\t</figure>";

								$eThumbnail = "";
								if ($count % $maxCols == 0){
									$_boardlists .= "\n</div>\n";
								}
							}
							$count++;
							$loop_number--;
				}

				if ( $display_type == "board" ){
					$_boardli_title .= sprintf( $this->getListFormat( $display_type."title" ), "No.", "Subject", "File", "Date", "Views" );
					$_boardlist 	.= "<table class=\"boardList\">". $_boardli_title . $_boardlists ."\n\t</table>";
				}
				elseif ( $display_type == "photo" ){
					//Close last row if needed
					if ($recIdx % $maxCols != 0){
						$_boardlists .= "\n</div>\n";
					}
					$_boardlist = "<!-- Bof Articles List -->\n<div class=\"photoset\">".$_boardlists."</div><!-- Eof Articles List -->" ;
				}
				elseif ( $display_type == "castingthumb" ){
					$_boardlist 	.= "<ul>". $_boardlists ."\n\t</ul>";
				}
				elseif ( $display_type == "castingthumblist" ){
					$_boardlist 	.= $_boardlists ."\n\t</ul>";
				}
				else {
					$_boardlist 	.= "<table class=\"boardList\">". $_boardlists ."\n\t</table>";
				}
			}
			else {
				$_boardlist .= "<table class=\"no-data-yet\">\n\t<tr><td colspan=\"6\" style=\"padding-top:20px;text-align:center;\">".NO_ARTICLE_YET."</td></tr>\n\t</table>";
			}

			if ( CONFIG_DISPLAY_PAGE_NUM_BLOCK == "true" ){
				$_boardlist .= $this->getPagelistblock( $pageid, $total_page, $noPage, $page['frontpost'], $categoriesid );
			}

			if ( $nailthumb === "true" ){
				$_boardlist  .= "<script type=\"text/javascript\">
				jQuery( document ).ready( function(){
					jQuery( '.frontpiclist' ).nailthumb( {width:90,height:70} );
				} );
				</script>";
			}

			return $_boardlist;
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 *  ------------------------------------------------------------------------- */
	public function getPagelistblock( $pageid, $total_page, $noPage, $writebt, $cId ){
		$_pagelist = "\n<div class=\"page_numbers_area\">";

		//To put links to lead the page at the bottom of articles
		$sef_href 		= $this->GeneralItems->getMenuURL( $pageid );
		$total_block	= ceil( $total_page/CONFIG_HOW_MANY_PAGES_PER_BLOCK );
		$block 			= ceil( $noPage/CONFIG_HOW_MANY_PAGES_PER_BLOCK );
		$first_page 	= ( $block-1 )*CONFIG_HOW_MANY_PAGES_PER_BLOCK;
		$last_page	 	= $block*CONFIG_HOW_MANY_PAGES_PER_BLOCK;

		if ( $total_block <= $block ){$last_page = $total_page;}

		//Link to preview page block
		if ( $block > 1 ){
			$my_page = $first_page;
			$_pagelist .= " <a href=\"{$this->basicURL}".UNDER_SUBFOLDER."{$sef_href['alias']}&amp;{$my_page}\" onfocus=\"this.blur()\">[previews]</a> ";
		}

		//Link to the page directly
		for ( $direct_page = $first_page+1; $direct_page <= $last_page; $direct_page++ ){
			$_pagelist .= $noPage == $direct_page ? " <strong class=\"page_numbers current \">{$direct_page}</strong> " : " <a href=\"".$this->basicURL.UNDER_SUBFOLDER."{$sef_href['alias']}&amp;{$direct_page}\" onfocus=\"this.blur()\" class=\"page_numbers\">{$direct_page}</a> ";
		}

		//Link to next page block
		if ( $block < $total_block ){
			$my_page = $last_page+1;
			$_pagelist .= " <a href=\"{$this->basicURL}".UNDER_SUBFOLDER."{$sef_href['alias']}&amp;{$my_page}\" onfocus=\"this.blur()\">[next]</a> ";
		}

		if ( isset( $_SESSION ) && $_SESSION['session_user_id'] && $_SESSION['guest'] == 1 ){
			$returnURL = $this->GeneralItems->getMenuURL( $pageid );
			$valueObject = $this->dbConnect->getAllContents( "members", "id = '{$_SESSION['session_user_id']}' AND publish='1'" );
			$caValue = explode( ";", $valueObject->categoriesid );

			if ( in_array( $cId, $caValue ) ){
				$_pagelist .= "&nbsp;<a href=\"".$this->basicURL.UNDER_SUBFOLDER.$returnURL['alias']."/post\" targer=\"_self\" title=\"{$returnURL['name']}\" class=\"wirteBTN page_numbers\" onfocus=\"this.blur()\">".TEXT_WRITE."</a>";
			}
			elseif ( $_SESSION['userlevel'] < 3 ){
				$_pagelist .= "&nbsp;<a href=\"".$this->basicURL.UNDER_SUBFOLDER.$returnURL['alias']."/post\" targer=\"_self\" title=\"{$returnURL['name']}\" class=\"wirteBTN page_numbers\" onfocus=\"this.blur()\">".TEXT_WRITE."</a>";
			}
			elseif ( $writebt == 1 ){
				$_pagelist .= "&nbsp;<a href=\"".$this->basicURL.UNDER_SUBFOLDER.$returnURL['alias']."/post\" targer=\"_self\" title=\"{$returnURL['name']}\" class=\"wirteBTN page_numbers\" onfocus=\"this.blur()\">".TEXT_WRITE."</a>";
			}
		}
		$_pagelist .= "</div>";

		return $_pagelist;
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]::  @return text html
 *  ------------------------------------------------------------------------- */
	public function getListFormat( $opt = "" ){

		if ( $opt === "boardtitle" ){
			$listtxt = "\n\t<tr id=\"board_bar\" height=\"25\">"
                          ."\n\t<th width=\"35\" align=\"center\" class=\"board_line lineTitle\">%s</th>"
                          ."\n\t<th align=\"center\" class=\"board_line lineTitle\">%s</th>"
                          ."\n\t<th width=\"40\" align=\"center\" class=\"board_line lineTitle\">%s</th>"
                          ."\n\t<th width=\"60\" align=\"center\" class=\"board_line lineTitle\">%s</th>"
                          ."\n\t<th width=\"55\" align=\"center\" class=\"lineTitle\">%s</th>"
                          ."\n\t</tr>";

		}
		elseif ( $opt === "castingtitle" ){
			$listtxt = "\n\t<tr id=\"board_bar\" height=\"25\">"
                          ."\n\t<th width=\"35\" align=\"center\" class=\"board_line\">No.</th>"
                          ."\n\t<th width=\"60\" align=\"center\" class=\"board_line\">Date</th>"
                          ."\n\t<th align=\"center\" class=\"board_line\">Title</th>"
                          ."\n\t<th class=\"tdprecher board_line\" align=\"center\">Host</th>"
                          ."\n\t<th class=\"tdvers\" align=\"center\"></th>"
                          ."\n\t</tr>";

		}
		elseif ( $opt === "list_thumb" ){
			$listtxt =  "\n\t<tr height=\"25\" %s>"
                           ."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
                           ."\n\t<td class=\"td_bot padding_both\" align=\"left\" ><a href=\"%s\" target=\"_self\" title=\"%s\" onfocus=\"this.blur()\">%s</a></td>"
                           ."\n\t<td class=\"td_bot fs10\" align=\"center\">%s</td>"
                           ."\n\t<td class=\"td_bot fs10\" align=\"center\">%s</td>"
                           ."\n\t<td class=\"td_bot fs10\" align=\"center\">%s</td>"
                           ."\n\t<td class=\"td_bot fs10\" align=\"center\">%s</td>"
                           ."\n\t</tr>";

		}
		elseif ( $opt === "list_thumb_txt" ){
			$listtxt =  "\n\t<tr%s\"><td class=\"thumbtxt\"><a href=\"%s\" title=\"%s\" onfocus=\"this.blur()\"  class=\"ScaleImg\">%s</a>"
						."\n\t<a href=\"%s\" title=\"%s\" onfocus=\"this.blur()\"  class=\"listupTitle\">%s</a>%s<a href=\"%s\" title=\"%s\" onfocus=\"this.blur()\" class=\"readmorebtn\">Read more</a></td>"
						."\n\t</tr>";

		}
		elseif ( $opt === "list_thumb_txt_merge" ){
			$listtxt =  "\n\t<tr%s\"><td class=\"thumbtxt\"><a href=\"%s\" title=\"%s\" onfocus=\"this.blur()\" class=\"listupTitle\">%s</a>%s <a href=\"%s\" title=\"%s\" onfocus=\"this.blur()\" class=\"readmorebtn\">Read more</a></td>"
						."\n\t</tr>";

		}
		elseif ( $opt === "onlythumb" ){
			$listtxt =  "\n\t<tr height=\"25\" %s>"
                           ."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
                           ."\n\t<td class=\"td_bot padding_both\" align=\"left\" ><a href=\"%s\" target=\"_self\" title=\"%s\" onfocus=\"this.blur()\">%s</a></td>"
                           ."\n\t<td class=\"td_bot fs10\" align=\"center\">%s</td>"
                           ."\n\t<td class=\"td_bot fs10\" align=\"center\">%s</td>"
                           ."\n\t<td class=\"td_bot fs10\" align=\"center\">%s</td>"
                           ."\n\t<td class=\"td_bot fs10\" align=\"center\">%s</td>"
                           ."\n\t</tr>";

		}
		elseif ( $opt === "board" ){
			$listtxt =  "\n\t<tr height=\"25\" %s>"
                           ."\n\t<td class=\"td_bot noLine\" align=\"center\">%s</td>"
                           ."\n\t<td class=\"td_bot padding_both\" align=\"left\" ><a href=\"%s\" target=\"_self\" title=\"%s\" onfocus=\"this.blur()\">%s</a></td>"
                           ."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
                           ."\n\t<td class=\"td_bot_date\" align=\"center\">%s</td>"
                           ."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
                           ."\n\t</tr>";

		}
		elseif ( $opt === "casting" ){
			$listtxt =  "\n\t<tr%s>"
								 ."\n\t<td><span class=\"listvod_wrap\"><div class=\"embed_vod\">%s</div></span><a href=\"%s\" title=\"%s\" target=\"_self\"><strong>[%s] %s</strong></a><span>By <strong>%s</strong>&nbsp;&nbsp;[  %s ]</span>%s</td>"
								 ."\n\t</tr>";
		}
		elseif ( $opt === "castingthumb" ){
			$listtxt =  "\n\t<li%s>"
								 ."\n\t<dl><span class=\"listvod_wrap\">%s</span><a href=\"%s\" title=\"%s\" target=\"_self\"><strong>[%s] %s</strong> [  %s ]</a>%s</dl>"
								 ."\n\t</li>";
		}
		elseif ( $opt === "castinglist" ){
			$listtxt =  "\n\t<li%s>"
											 ."\n\t<dl><dd class=\"%s\">%s</dd><dt><a href=\"%s\" title=\"%s\" target=\"_self\"><span class=\"vodTitle\">%s</span>%s</a></dt></dl>"
											 ."\n\t</li>";
		}
		elseif ( $opt === "castingthumblistOne" ){
			$listtxt =  "\n\t<li%s>"
						 ."\n\t<dl><dd><span class=\"%s\">%s</span></dd><dt class=\"sidedt\"><a href=\"%s\" title=\"%s\" target=\"_self\"><span class=\"vodTitle\">%s</span>%s</a></dt></dl>"
						 ."\n\t</li>";
		}
		elseif ( $opt === "castingthumblistOne_a" ){
			$listtxt =  "\n\t<li%s>"
						 ."\n\t<dl><dd><span class=\"%s\">%s</span></dd><dt class=\"sidedt\"><a href=\"%s\" title=\"%s\" target=\"_self\"><span class=\"vodTitle\">%s</span>%s</a></dt></dl>"
						 ."\n\t</li>";
		}
		elseif ( $opt === "castingthumblist" ){
			$listtxt =  "\n\t<li%s>"
						 ."\n\t<dl><dd><span class=\"%s\">%s</span></dd><dt class=\"gradient_bg\"><a href=\"%s\" title=\"%s\" target=\"_self\"><span class=\"vodTitle\">%s</span> %s</a></dt></dl>"
						 ."\n\t</li>";
		}
		elseif ( $opt === "castingthumblist_a" ){
			$listtxt =  "\n\t<li%s>"
						 ."\n\t<dl><dd><span class=\"%s\">%s</span></dd><dt class=\"gradient_bg\"><a href=\"%s\" title=\"%s\" target=\"_self\"><span class=\"vodTitle\">%s</span> %s</a></dt></dl>"
						 ."\n\t</li>";
		}
		elseif ( $opt === "blog" ){
			$listtxt = 	 "\n<ul %s>\n"
							."	<li class=\"list_subject\"><a href=\"%s\" target=\"_self\" title=\"%s\" onfocus=\"this.blur()\">%s</a></li>\n"
							."	<li class=\"list_author\">
									<span class=\"author\">%s</span>
									<span class=\"counts\">
										<span class=\"read\">view: <span class=\"num\">%s</span></span>
										<span class=\"wdate\">%s</span>
									</span></li>\n"
							."	<li class=\"list_txt\">%s</li>\n"
							."</ul>\n";
		}
		elseif ( $opt === "castingitem" ){
			$listtxt =  "\n<div class=\"boardHeader\">
								<div class=\"titleArea\">
									<h3 class=\"title\">%s</h3>
								</div>
								%s
							</div>
							<div class=\"pageentry\">%s</div>";
		}
		elseif ( $opt === "boarditem" ){
			$listtxt =  "\n<div class=\"boardHeader\">
								<div class=\"titleArea\">
									<h3 class=\"title\">%s</h3>
								</div>
								<div class=\"authorArea\">
									<span class=\"author\">%s %s</span>
									<span class=\"counts\">
										<span class=\"read\">view <span class=\"num\">%s</span></span>
										<span class=\"wdate\">%s</span>
									</span>
								</div>
								%s
							</div>
							<div class=\"pageentry\">%s</div>";
		}
		else {
			$listtxt =  "No Frame";
		}

		return $listtxt;
	}	
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!