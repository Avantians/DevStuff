<?php
/** -------------------------------------------------------------------------
 * @package  CMS
 * @author      Kenwoo - iweb@kenwoo.ca
 * @license    http://creativecommons.org/licenses/by/4.0/ Creative Commons
 *
 * [v02-02/07/2011]:: Set flag, not allow to direct access
 * ----------------------------------------------------------------------- */
defined( "_VALID_MOS" ) or die( "Your system is not working properly." );
class getSearch {

	public $dbConnect;
	public $basicURL;

	public function __construct( $basicURL ){
		global $Bon_db;

		$this->dbConnect			 		= $Bon_db;
		$this->basicURL						= $basicURL;
		$this->static_url						= rtrim( str_replace( "www", "static", $this->basicURL ), "/" );
		$this->error_flag						= false;
		$this->error_message	= array();
		$this->contents							= array();
		$this->tbname							= "articles";
	}

/** -------------------------------------------------------------------------
 * [06/132014]:: To
 * ----------------------------------------------------------------------- */
	public function setProcess( $allValues ){
			$allValues = trim( $allValues, '/' );

			if( !_getCheckNullorNot( $allValues ) ) {
					$_boardlist .= "<table  width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"1\">\n\t<tr><td colspan=\"6\" style=\"padding-top:20px;text-align:center;\">".NO_RELATED_ARTICLE."</td></tr>\n\t</table>";
			}
			else {
				$list_tail = " AND ( b.title LIKE '%" . $allValues ."%' OR b.fulltxt LIKE '%" . $allValues ."%' OR b.summarytxt LIKE '%" . $allValues ."%' )";
				$total_list = $this->dbConnect->getTotalNumber( $this->tbname, "notice = '0' AND status = '1' AND publish = '1' AND access_level >= '".getAllElements::setAccesslevel("ulevel")."' AND group_level >= '".getAllElements::setAccesslevel("gid")."' AND ( title LIKE '%" . $allValues ."%' OR fulltxt LIKE '%" . $allValues ."%' OR summarytxt LIKE '%" . $allValues ."%' )" );

				if( $total_list > 0 ){
					$total_page	= ceil( ( $total_list ) / CONFIG_HOW_MANY_ARTICLES_PER_PAGE );
					$noPage			= isset( $page['page_no'] ) ? $page['page_no'] : 1;

					$first = $total_list == 0 ? 1 : CONFIG_HOW_MANY_ARTICLES_PER_PAGE *( $noPage-1 );
					$last = $total_list == 0 ? 0 : CONFIG_HOW_MANY_ARTICLES_PER_PAGE * $noPage;
					$loop_number = $total_list - ( $noPage-1 ) * CONFIG_HOW_MANY_ARTICLES_PER_PAGE;
					$total_loop_num = $loop_number;

					$list_query = $this->dbConnect->getQuery( "SELECT b.id, b.title, b.title_alias, b.thumbnail, b.summarytxt, b.casting_description, b.qrcode, b.fulltxt, b.linkfile, b.filename, b.filesize, b.urls, b.host, b.casting_description, b.casting_date, b.embed_bigcode, b.embed_code, b.sectionid, b.categoriesid, b.ordering, b.metatitle, b.metakey, b.metadesc, b.access_level, b.publish, b.status, b.notice, b.linkOpt, b.loginDownload, b.downloadAlias, b.views, b.votes, b.password, b.user_ip, b.modified_date, b.modified_by, b.created_date, b.created_by, b.created_by_alias, b.publish_date, o.external, o.pid, m.alias FROM {$this->tbname} b, opensef  o LEFT JOIN menu m ON o.pid = m.pid  WHERE b.id = o.tid AND o.tbname = '{$this->tbname}' AND b.notice = '0' AND o.publish = '1' AND m.status	= '1' AND b.access_level >= '".getAllElements::setAccesslevel("ulevel")."' AND b.group_level >= '".getAllElements::setAccesslevel("gid")."' AND b.publish = '1' AND b.status = '1' {$list_tail} ORDER BY b.ordering DESC LIMIT {$first}, ".$total_list ."" );

					$count = 1;
					while ( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
								$recIdx++;
								$bgClass 					= ( $loop_number/2 ) == floor( $loop_number/2 ) ? " class=\"list_even" : " class=\"list_odd";
								$bgClassB 				= ( $count % 2 == 0 ) ? " even" : " odd";
								$shortDate  			= _getShortFormatDate( $list_values['created_date'] );

								if( $list_values['linkOpt'] == "1" ){
									if( $list_values['loginDownload'] == "1" ){
										if( !_getCheckNullorNot( $list_values['downloadAlias'] ) ){
											$pURL = _getCheckNullorNot( $list_values['linkfile'] ) ? $this->basicURL ."/download/". urldecode( trim( $list_values['linkfile'], "/" ) ) : "";
										}
										else{
											$pURL = _getCheckNullorNot( $list_values['linkfile'] ) ? $this->basicURL ."/". urldecode( trim( $list_values['downloadAlias'], "/" ) ) : "";
										}
									}
									else{
										$pURL = _getCheckNullorNot( $list_values['linkfile'] ) ? $this->basicURL ."/download/". urldecode( trim( $list_values['linkfile'], "/" ) ) : "";
									}
								}
								else{
									$pURL = ( $noPage !== 1 ) ? $this->basicURL.$list_values['alias'].$list_values['external']."&amp;".$noPage : $this->basicURL.$list_values['alias'].$list_values['external'];
								}

								$file_name = !_getCheckNullorNot( $list_values['filename'] ) ?  "-" : "<a href=\"{$pURL}\" title=\"\" target=\"_self\" onfocus=\"this.blur()\" class=\"iconDownload\"></a>";
								$_boardlists .= sprintf( $this->getListFormat( "board" ), $bgClass."\"", $loop_number, $pURL, stripslashes( $list_values['title'] ), stripslashes( $list_values['title'] ), $file_name, $shortDate,  $list_values['views'] );

								$count++;
								$loop_number--;
					}

					$_boardlist 	.= "<table border=\"0\" cellspacing=\"2\" cellpadding=\"1\" class=\"boardList\">". sprintf( $this->getListFormat( "boardtitle" ), "No.", "Subject", "File", "Date", "Views" ). $_boardlists ."\n\t</table>";

					if( CONFIG_DISPLAY_PAGE_NUM_BLOCK == "true" ){
						//$_boardlist .= $this->getPagelistblock( $pageid, $total_page, $noPage, $page['frontpost'], $categoriesid );
					}
				}
				else{
					$_boardlist .= "<table border=\"0\" cellspacing=\"2\" cellpadding=\"1\">\n\t<tr><td colspan=\"6\" style=\"padding-top:20px;text-align:center;\">".NO_RELATED_ARTICLE."</td></tr>\n\t</table>";
				}
			}

			return $_boardlist;
	}

/** -------------------------------------------------------------------------
 * [06/132014]:: To
 *  ------------------------------------------------------------------------- */
	public function getPagelistblock( $pageid, $total_page, $noPage, $writebt, $cId ){
		$_pagelist = "\n<div class=\"page_numbers_area\">";

		//To put links to lead the page at the bottom of articles
		$sef_href 		= $this->GeneralItems->getMenuURL( $pageid );
		$total_block	= ceil( $total_page/CONFIG_HOW_MANY_PAGES_PER_BLOCK );
		$block 				= ceil( $noPage/CONFIG_HOW_MANY_PAGES_PER_BLOCK );
		$first_page 	= ( $block-1 )*CONFIG_HOW_MANY_PAGES_PER_BLOCK;
		$last_page	= $block*CONFIG_HOW_MANY_PAGES_PER_BLOCK;

		if( $total_block <= $block ){$last_page = $total_page;}

		//Link to preview page block
		if( $block > 1 ){
			$my_page = $first_page;
			$_pagelist .= " <a href=\"{$this->basicURL}".UNDER_SUBFOLDER."{$sef_href['alias']}&amp;{$my_page}\" onfocus=\"this.blur()\">[previews]</a> ";
		}

		//Link to the page directly
		for( $direct_page = $first_page+1; $direct_page <= $last_page; $direct_page++ ){
			$_pagelist .= $noPage == $direct_page ? " <strong class=\"page_numbers current \">{$direct_page}</strong> " : " <a href=\"".$this->basicURL.UNDER_SUBFOLDER."{$sef_href['alias']}&amp;{$direct_page}\" onfocus=\"this.blur()\" class=\"page_numbers\">{$direct_page}</a> ";
		}

		//Link to next page block
		if( $block < $total_block ){
			$my_page = $last_page+1;
			$_pagelist .= " <a href=\"{$this->basicURL}".UNDER_SUBFOLDER."{$sef_href['alias']}&amp;{$my_page}\" onfocus=\"this.blur()\">[next]</a> ";
		}
		$_pagelist .= "</div>";

		return $_pagelist;
	}


/** -------------------------------------------------------------------------
 * [06/132014]:: @return text html
 *  ------------------------------------------------------------------------- */
	public function getListFormat( $opt = "" ){

		if( $opt === "boardtitle" ){
			$listtxt = "\n\t<tr id=\"board_bar\" height=\"25\">"
                          ."\n\t<th width=\"35\" align=\"center\" class=\"board_line lineTitle\">%s</th>"
                          ."\n\t<th align=\"center\" class=\"board_line lineTitle\">%s</th>"
                          ."\n\t<th width=\"40\" align=\"center\" class=\"board_line lineTitle\">%s</th>"
                          ."\n\t<th width=\"60\" align=\"center\" class=\"board_line lineTitle\">%s</th>"
                          ."\n\t<th width=\"55\" align=\"center\" class=\"lineTitle\">%s</th>"
                          ."\n\t</tr>";
		}
		elseif( $opt === "board" ){
			$listtxt =  "\n\t<tr height=\"25\" %s>"
                           ."\n\t<td class=\"td_bot noLine\" align=\"center\">%s</td>"
                           ."\n\t<td class=\"td_bot padding_both\" align=\"left\" ><a href=\"%s\" target=\"_self\" title=\"%s\" onfocus=\"this.blur()\">%s</a></td>"
                           ."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
                           ."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
                           ."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
                           ."\n\t</tr>";
		}
		else{
			$listtxt =  "No Frame";
		}

		return $listtxt;
	}
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!