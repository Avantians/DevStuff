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

class getGeneral {

	public $dbConnect;
	public $basicURL;
	public $error_flag;

	public function __construct( $baseURL ){
		global $Bon_db, $base_url;

		$this->dbConnect	= $Bon_db;
		$this->basicURL	= $baseURL;
		$this->error_flag		= false;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]:: To
 *  ----------------------------------------------------------------------- */
	public function getMenuURL( $value ){
		$query = "SELECT * FROM menu WHERE pid = '{$value}' AND publish = '1' AND status = '1' AND group_level >= '".getAllElements::setAccesslevel("gid")."' AND access_level >= '".getAllElements::setAccesslevel("ulevel")."' AND utaccess_level >= '1' ";
		$menuURL = $this->dbConnect->getContentsInArray( $query );

		return $menuURL;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]:: To
 *  ----------------------------------------------------------------------- */
	public function getExternalItemURL( $eItems ){
		$eURL_query = "SELECT * FROM opensef WHERE tid = '". $eItems['tid'] ."' AND tbname = '".$eItems['tbname']."' AND publish =  '1'";
		$eURL_items = $this->dbConnect->getContentsInArray( $eURL_query );

		$ePageURL = $this->basicURL.$eURL_items['external'];

		return $ePageURL;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]:: To
 *  ----------------------------------------------------------------------- */
	public function getAddthis( $url, $title ){

	  $SharingIcons  = "\n<div class=\"addthis_sharing_toolbox\">";
	  $SharingIcons	.= "</div>";

		return $SharingIcons;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]:: To
 *  ----------------------------------------------------------------------- */
	public function getSharingIcons( $url, $title ){

	  $SharingIcons    = "\n<div class=\"sharingIcons\">";
		#$SharingIcons	.= "\n".'<a href="https://www.facebook.com" title="Share this page with Facebook!" target="_blank" class="facebook_share_bt" onfocus="this.blur()" onclick="window.open( \'http://www.facebook.com/sharer.php?u=\' + encodeURIComponent( \''. $url .'\' ) + \'&amp;t=\' + encodeURIComponent( \''.$title.'\' ) + \'\' ); return false;"><span class="nodisplay">Facebook</span></a>';
		$SharingIcons	.= "\n".'<a href="https://www.facebook.com/sharer.php?u='.rawurldecode($url).'&amp;t='.rawurldecode($title).'&amp;display=popup" title="Share this page with Facebook!" target="_blank" class="facebook_share_bt"><span class="nodisplay">Facebook</span></a>';

		$SharingIcons	.= "\n".'<a href="https://twitter.com/share" title="Share this page with Twitter!" target="_blank" class="twitter_share_bt" data-count="none" onfocus="this.blur()"><span class="nodisplay">Tweet</span></a>
<script>!function( d,s,id ){var js,fjs=d.getElementsByTagName( s )[0];if ( !d.getElementById( id ) ){js=d.createElement( s );js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore( js,fjs );}}( document,"script","twitter-wjs" );</script>';

		#$SharingIcons .= "\n".'<a rel="nofollow" href="'. $this->basicURL.$url .'" title="Permanent Link: '.$title.'" target="_self" class="digg_text" onfocus=\"this.blur()\">Permalink</a>'."\n";
	    $SharingIcons    .= "</div>";

		return $SharingIcons;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]:: To create proper URL
 *  http://www.kenwoo.ca OR subdoamin.kenwoo.ca
 *  ----------------------------------------------------------------------- */
	public function getProperLink( $url ){
		//if ( !preg_match( "/^( http|https|ftp|telnet|news|mms )\:\/\//i", $url ) ){$url = "http://" . $url;}
		$pharse_url = explode( ".", $url );
		if ( $pharse_url[0] == "www" && sizeof( $pharse_url ) == 3 ){
			$link = "http://". $url ."";
	  }
			elseif ( $pharse_url[0] != "www" && sizeof( $pharse_url ) == 1 ){
		   $link =  $this->basicURL . $url ."";
		}
		elseif ( $pharse_url[0] == "http://www" ){
		    $link = $url;
		}
		elseif ( $pharse_url[0] == "https://www" ){
		    $link = $url;
    }
		elseif ( $pharse_url[0] != "www" ){
	    	$second_pharse_url = explode( "/", $pharse_url[0] );
			if ( sizeof( $pharse_url ) == 3 ){
		        $link = "http://". $url ."";
		  }
			else {
					if ( end( $second_pharse_url ) != "www" && $second_pharse_url[0] == "http:" ){
						$link = $url;
					}
					else {
						$link = "http://www.". $url ."";
					}
			}
		}

		return $link;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]:: @param string $pInfo
 *  ----------------------------------------------------------------------- */
	public function getValidation( $val, $max=2, $type="string" ){
		if ( _getCheckNullorNot( $val ) ){
			$val = trim( $val );

			if ( strlen( $val ) <= ( int )$max ){
				if ( $type === "email" ){
					$this->error_flag = ( bool )preg_match( "/^[a-z0-9][a-z0-9_\.-]{0,}[a-z0-9]@[a-z0-9][a-z0-9_\.-]{0,}[a-z0-9][\.][a-z0-9]{2,4}$/ix", $val );

				} elseif ( $type === "int" ){
					$this->error_flag = ( bool )is_int( $val );

				} else {
					$this->error_flag = ( bool )preg_match( "/^( [a-zA-Z0-9] )+$/i", $val );
				}
			}

		} else {
			$this->error_flag = false;
		}

 		return $this;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::  $type = articles / photos / items
 * $thumbtxt will be the number of text to display.
 * $eclass is to use <ul> tag with extra class element.
 * $GeneralItems->getFrontItems( "articles", 12, "imgboard#4", "#4#4OR5OR7" ); 
 * $GeneralItems->getFrontItems( "articles", 6, "imgboard", "#6#6" ); 
 *  ----------------------------------------------------------------------- */
	public function getFrontItems( $tbname, $max = 1, $type = "articles", $list_tail = "", $thumbtxt = "", $eclass = "" ){

		$type_count = explode( '#' , $type );
		if ( count($type_count) > 1) {
			list( $type, $Temp_max ) = explode( '#' , $type );
		}

		if ( _getCheckNullorNot($list_tail) ){
			list( $indication, $sectionid, $categoryid ) = explode( '#' , $list_tail );

			if ( $indication == "adbanner" ){
				$list_tail = " AND " . $sectionid;
			}
			elseif (  $indication == "mostrecent" ){
				$list_tail	= explode( "OR", $categoryid );
			}
			else {
				$sectionid_array	= explode( "OR", $sectionid );
				if ( count($sectionid_array) > 1 ){
						$section_area = "( ";
						for( $z=0; $z < count( $sectionid_array ); $z++ ){
								$section_area .= "b.sectionid = '{$sectionid_array[$z]}'";
								$section_area .= $z +1 != count( $sectionid_array ) ? " OR " : "";
						}
						$section_area .= " )";
				}
				else {
						$section_area = "b.sectionid = '{$sectionid}'";
				}
				$section_area = " AND " . $section_area;

				$categoryid_array	= explode( "OR", $categoryid );
				if ( count($categoryid_array) > 1 ){
						$category_area = "( ";
						for( $y=0; $y < count( $categoryid_array ); $y++ ){
								$category_area .= "b.categoriesid = '{$categoryid_array[$y]}'";
								$category_area .= $y +1 != count( $categoryid_array ) ? " OR " : "";
						}
						$category_area .= " )";
				}
				else {
						$category_area = "b.categoriesid = '{$categoryid}'";
				}
				$category_area = " AND " . $category_area;

				$list_tail = $section_area . $category_area;
			}
		}

		if ( $type === "articles" ){
				$count = 1;
				$list_query = $this->dbConnect->getQuery( "SELECT b.title, b.title_alias, b.embed_bigcode, b.ordering, b.created_date, b.created_by, b.created_by_alias, o.external, o.pid, m.alias FROM {$tbname} b, opensef  o LEFT JOIN menu m ON o.pid = m.pid WHERE b.id = o.tid AND o.tbname = '{$tbname}' AND o.publish = '1' AND m.status = '1' AND b.publish = '1' AND b.status = '1'{$list_tail} ORDER BY b.created_date DESC LIMIT 0, {$max}" );

				while ( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
						$newTitle		= _mb_strcut( stripslashes( $list_values['title'] ), 0, 30 );
						$shtmlTitle	= $list_values['title'];
						//$articlelist		.= "\n\t<li class=\"frontlist".$count."\">\n\t<a href=\"".$this->basicURL . $list_values['alias'] . $list_values['external']."\" class=\"item{$count}{$eclass}\" title=\"{$shtmlTitle}\" target=\"_self\" onfocus=\"this.blur()\">\n\t{$newTitle}</a>\n\t</li>";
						
						$articlelist	.=	"<li class=\"frontlist{$count}\">
										<a href=\"". $this->basicURL . $list_values['alias'] . $list_values['external'] ."\" class=\"item{$count}{$eclass}\" title=\"{$shtmlTitle}\" target=\"_self\" onfocus=\"this.blur()\">
										<span class=\"list-no\">". sprintf("%02d", $count) ."</span>{$newTitle}</a>
										</li>";
						$count++;
				}
				$inside = _getCheckNullorNot($eclass) ? "<!-- Bof Articles List --><ul class=\"{$eclass}\">".$articlelist."</ul><!-- Eof Articles List -->" : "\n\t<!-- Bof List -->".$articlelist."\n\t<!--Eof List -->\n";

		 }
		elseif ( $type === "qt" ){
				$count = 1;
				$list_query = $this->dbConnect->getQuery( "SELECT b.title, b.title_alias, b.summarytxt, b.ordering, b.created_date, b.casting_date, o.external, o.pid, m.alias FROM {$tbname} b, opensef  o LEFT JOIN menu m ON o.pid = m.pid WHERE b.id = o.tid AND o.tbname = '{$tbname}' AND o.publish = '1' AND m.status = '1' AND b.publish = '1' AND b.status = '1'{$list_tail} ORDER BY b.created_date DESC LIMIT 0, {$max}" );
				while ( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
						$newTitle		= _mb_strcut( stripslashes( $list_values['title'] ), 0, 32 );
						$shtmlTitle	= $list_values['title'];
						$newInfo		=  stripslashes( $list_values['summarytxt'] );
						list( $year, $month, $day ) = explode( '-' , $list_values['casting_date'] );
						
							$articlelist	.=	"<li>
								<div class=\"date\"><a href=\"". $this->basicURL . $list_values['alias'] . $list_values['external'] ."\" class=\"item{$count}{$eclass}\" title=\"{$shtmlTitle}\" target=\"_self\" onfocus=\"this.blur()\"><span class=\"day\">". $day ."</span><span class=\"month\">". $month ."</span></a></div>
								<div class=\"bd\">
									<a href=\"". $this->basicURL . $list_values['alias'] . $list_values['external'] ."\" class=\"item{$count}{$eclass}\" title=\"{$shtmlTitle}\" target=\"_self\" onfocus=\"this.blur()\">{$newInfo}</a>
									
								</div>
							</li>";
						$count++;
				}
				$inside = _getCheckNullorNot($eclass) ? "<!-- Bof Articles List --><ul class=\"{$eclass}\">".$articlelist."</ul><!-- Eof Articles List -->" : "\n\t<!-- Bof List -->".$articlelist."\n\t<!--Eof List -->\n";

		 }		 
		 elseif ( $type === "castpic" ){
				$count = 1;
				if ( $indication == "percategory" ){
						$categories_query	= $this->dbConnect->getQuery( "SELECT id, ctype, title, section FROM categories WHERE parent = '0' AND section = 4 ORDER BY ordering DESC" );
						while ( $categories_values	= $this->dbConnect->getFetch_Array( $categories_query ) ){
								$list_query = $this->dbConnect->getQuery( "SELECT b.title, b.title_alias, b.embed_bigcode, b.thumbnail, b.summarytxt, b.casting_description, b.ordering, b.urls, b.host, b.casting_date, b.embed_code, b.sectionid, b.categoriesid, b.created_date, b.created_by, b.created_by_alias, o.external, o.pid, m.alias FROM {$tbname} b, opensef  o LEFT JOIN menu m ON o.pid = m.pid WHERE b.id = o.tid AND o.tbname = '{$tbname}' AND o.publish = '1' AND m.status = '1' AND b.publish = '1' AND b.status = '1' AND sectionid = '{$categories_values['section']}' AND categoriesid = '{$categories_values['id']}' ORDER BY b.ordering DESC LIMIT 0, {$max}" );

								while ( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
										$newTitle		= _mb_strcut( stripslashes( $list_values['title'] ), 0, 35 );
										$shtmlTitle	= $list_values['title'];
										if ( _getCheckNullorNot( $list_values['thumbnail'] ) ){
											$location_src = $_SERVER['DOCUMENT_ROOT'] .UNDER_SUBFOLDER ."/". CONFIG_FILES_UPLOAD_ROOT.$list_values['thumbnail'];
											if ( file_exists( $location_src ) ){
												$nailthumb				= true;
												$image_size 			= getimagesize( $location_src );
												$eThumbnail 			= getDisplayImg( $list_values['thumbnail'], $image_size[0] , $nailthumb, stripslashes( $list_values['title'] ), false, "" );
											}
										}
										$articlelist		.= "\n\t<li>\n\t<a href=\"".$this->basicURL . $list_values['alias'] . $list_values['external']."\" class=\"item{$count}{$eclass}\" title=\"{$shtmlTitle}\" target=\"_self\" onfocus=\"this.blur()\">\n\t{$eThumbnail}</a>\n\t</li>";
										$count++;
								}
						}
				}
				/* To get most recent casting with thumbnail image from selected section and category.
				 * Using general function getDisplayImg( $filename, $maxWidth, $nailthumb = true OR false, $alttxt, $useDiv = true OR false, $extraClass )
				**/
				elseif ( $indication == "mostrecent" ){
						$count = 1;
						foreach ( $list_tail as &$value ){
							$list_query = $this->dbConnect->getQuery( "SELECT b.title, b.title_alias, b.embed_bigcode, b.thumbnail, b.filename, b.summarytxt, b.casting_description, b.ordering, b.urls, b.host, b.casting_date, b.embed_code, b.sectionid, b.categoriesid, b.created_date, b.created_by, b.created_by_alias, o.external, o.pid, m.alias FROM {$tbname} b, opensef  o LEFT JOIN menu m ON o.pid = m.pid WHERE b.id = o.tid AND o.tbname = '{$tbname}' AND o.publish = '1' AND m.status = '1' AND b.publish = '1' AND b.status = '1' AND sectionid = '{$sectionid}' AND categoriesid = '{$value}' ORDER BY b.ordering DESC LIMIT 0, {$max}" );

							while ( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
									$categoryTitle = $this->dbConnect->getCategoriesName( $list_values['categoriesid'] );
									$shtmlTitle	= $list_values['title'];
									$host	= $list_values['host'];
									$casting_description	= $list_values['casting_description'];
									$casting_date			= ( _getCheckNullorNot( $list_values['casting_date'] ) && $list_values['casting_date']  != "0000-00-00" ) ? _getLongFormatDate( stripslashes( $list_values['casting_date'] ) ) : "-";
									
									if ( _getCheckNullorNot( $list_values['thumbnail'] ) ){
										$location_src = $_SERVER['DOCUMENT_ROOT'] .UNDER_SUBFOLDER ."/". CONFIG_FILES_UPLOAD_ROOT.$list_values['thumbnail'];
										if ( file_exists( $location_src ) ){
											$image_size 			= getimagesize( $location_src );
											$eThumbnail 			= "<span class=\"fornt_img_wrap-1\">".getDisplayImg( $list_values['thumbnail'], $image_size[0] , true, stripslashes( $list_values['title'] ), false, "" )."</span>";
										}
									}
									else {
											if(_getCheckNullorNot($list_values['embed_bigcode'])){
												$eThumbnail ="<span class=\"vod_wrap-1\">". $list_values['embed_bigcode'] ."</span>" ;												
											}
									}

									$articlelist		.= "<dd>". $eThumbnail ."</dd>
											<dt class=\"sidedt\">
															<a href=\"". $this->basicURL . $list_values['alias'] . $list_values['external'] ."\" class=\"item{$count}{$eclass}\" title=\"{$shtmlTitle}\" target=\"_self\" onfocus=\"this.blur()\">
															<span class=\"vodTitle\">". $shtmlTitle ."</span>
															<span class=\"castingDate\">". $casting_date . "&nbsp;&nbsp;[". $host ."]" ."</span>
															<strong>". $casting_description ."</strong></a>
											</dt>";
									$count++;
							}
							$value = $value * 2;
						}
				}
				$inside = _getCheckNullorNot($eclass) ? "<!-- Bof Articles List --><dl class=\"{$eclass}\">".$articlelist."</dl><!-- Eof Articles List -->" : "\n\t<!-- Bof List --><dl>".$articlelist."\n\t</dl><!--Eof List -->\n";

		}
		 elseif ( $type === "front_imgboard" ){
				$count = 0;
				$maxCols =  _getCheckNullorNot($Temp_max)  ? $Temp_max : 3;
				$list_query = $this->dbConnect->getQuery( "SELECT b.title, b.title_alias, b.embed_bigcode, b.thumbnail, b.filename, b.summarytxt, b.casting_description, b.ordering, b.urls, b.host, b.casting_date, b.embed_code, b.sectionid, b.categoriesid, b.created_date, b.created_by, b.created_by_alias, o.external, o.pid, m.alias FROM {$tbname} b, opensef  o LEFT JOIN menu m ON o.pid = m.pid WHERE b.id = o.tid AND o.tbname = '{$tbname}' AND o.publish = '1' AND m.status = '1' AND b.publish = '1' AND b.status = '1' {$list_tail} ORDER BY b.ordering DESC LIMIT 0, {$max}" );
				
				while ( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
						$count++;
						$newTitle		= _mb_strcut( stripslashes( $list_values['title'] ), 0, 35 );
						$shtmlTitle	= $list_values['title'];

						if ( _getCheckNullorNot( $list_values['thumbnail'] ) ){
								$location_src = $_SERVER['DOCUMENT_ROOT'] .UNDER_SUBFOLDER ."/". CONFIG_FILES_UPLOAD_ROOT.$list_values['thumbnail'];
								if ( file_exists( $location_src ) ){
									$image_size 			= getimagesize( $location_src );
									$eThumbnail 			= getDisplayImg( $list_values['thumbnail'], $image_size[0] , true, stripslashes( $list_values['title'] ), false, "" );
								}
						}
						else {
								$listimg 			 = explode( ";", $list_values['filename']);
								$location_src		= $_SERVER['DOCUMENT_ROOT'] .UNDER_SUBFOLDER ."/". CONFIG_FILES_UPLOAD_ROOT.$listimg[0];
								$image_size		= getimagesize( $location_src );
								$eThumbnail  	= getDisplayImg( $listimg[0], $image_size[0] , true, stripslashes( $list_values['title'] ), false, "" );					
						}

						if ( $count == 1 ){
							$articlelist .= "<ul class=\"oneimgtile\">";
						}
						
						if ( $count == 2 ){
							$articlelist .= "</ul><div class=\"tile-section\"><ul class=\"twoimgtile\">";
						}
						if ( $count == 4 ){
						   $articlelist .= "</ul><ul class=\"threeimgtile extra-top\">";
						}
						   $articlelist .= "<li class=\"tile\"><a href=\"". $this->basicURL . $list_values['alias'] . $list_values['external'] ."\" title=\"". $shtmlTitle ."\" target=\"_self\">
								<div class=\"txtline\">". $shtmlTitle ."</div>
								<div class=\"mask\"></div>
								</a>". $eThumbnail ."</li>";
				}
				//Close last row if needed
				if ( $count == $max ){
					$articlelist .= "</ul>\n</div>\n";
				}

				$inside = "<!-- Bof Articles List -->\n".$articlelist."<!-- Eof Articles List -->" ;

		}		
		 elseif ( $type === "imgboard" ){
				$count = 0;
				$maxCols =  _getCheckNullorNot($Temp_max)  ? $Temp_max : 3;
				$list_query = $this->dbConnect->getQuery( "SELECT b.title, b.title_alias, b.embed_bigcode, b.thumbnail, b.filename, b.summarytxt, b.casting_description, b.ordering, b.urls, b.host, b.casting_date, b.embed_code, b.sectionid, b.categoriesid, b.created_date, b.created_by, b.created_by_alias, o.external, o.pid, m.alias FROM {$tbname} b, opensef  o LEFT JOIN menu m ON o.pid = m.pid WHERE b.id = o.tid AND o.tbname = '{$tbname}' AND o.publish = '1' AND m.status = '1' AND b.publish = '1' AND b.status = '1' {$list_tail} ORDER BY b.ordering DESC LIMIT 0, {$max}" );		
				while ( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
						$count++;
						if ($count % $maxCols == 1){
							$articlelist .= "<div class=\"photoset-row\">";
						}

						$newTitle		= _mb_strcut( stripslashes( $list_values['title'] ), 0, 35 );
						$shtmlTitle	= $list_values['title'];

						if ( _getCheckNullorNot( $list_values['thumbnail'] ) ){
								$location_src = $_SERVER['DOCUMENT_ROOT'] .UNDER_SUBFOLDER ."/". CONFIG_FILES_UPLOAD_ROOT.$list_values['thumbnail'];
								if ( file_exists( $location_src ) ){
									$image_size 			= getimagesize( $location_src );
									$eThumbnail 			= getDisplayImg( $list_values['thumbnail'], $image_size[0] , true, stripslashes( $list_values['title'] ), false, "" );
								}
						}
						else {
								$listimg 			= explode( ";", $list_values['filename']);
								$location_src		= $_SERVER['DOCUMENT_ROOT'] .UNDER_SUBFOLDER ."/". CONFIG_FILES_UPLOAD_ROOT.$listimg[0];
								$image_size		= getimagesize( $location_src );
								$eThumbnail  	= getDisplayImg( $listimg[0], $image_size[0] , true, stripslashes( $list_values['title'] ), false, "" );					
						}

						$articlelist		.= "\n\t<figure class=\"photoset-item\">\n\t<a href=\"".$this->basicURL . $list_values['alias'] . $list_values['external']."\" class=\"item{$count}{$eclass}\" title=\"{$shtmlTitle}\" target=\"_self\" onfocus=\"this.blur()\">\n\t{$eThumbnail}</a><figcaption>{$shtmlTitle}</figcaption>\n\t</figure>";

					  $eThumbnail = "";
						if ($count % $maxCols == 0){
							$articlelist .= "\n</div>\n";
						}
				}
				//Close last row if needed
				if ($count % $maxCols != 0){
					$articlelist .= "\n</div>\n";
				}

				$inside = "<!-- Bof Articles List -->\n<div class=\"photoset\">".$articlelist."</div><!-- Eof Articles List -->" ;

		}
		/* To get an image as advertisement
		 * Using general function getDisplayImg( $filename, $maxWidth, $nailthumb = true OR false, $alttxt, $useDiv = true OR false, $extraClass )
		**/
		elseif ( $type === "advertises" ){
			$count							= 1;
			$nailthumb			= false;
			$maxWidth 		= 60;
			$subquedate_ = " TO_DAYS( starting_date ) >= TO_DAYS('". date("Y-m-d") ."') AND TO_DAYS( ending_date ) >= TO_DAYS('". date("Y-m-d") ."') ";
			$list_query			= $this->dbConnect->getQuery( "SELECT * FROM {$tbname} WHERE publish = '1' AND status = '1' {$list_tail} ORDER BY position, ordering" );

			while ( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
					/** -------------------------------------------------------------------------
					 * 86400 = 1 Day //
					 * if $sdate >= 0, it means advertises will be display / if $edate < 0, it means advertises will not be display
					 * Calculation for starting & ending date of advertises
					 *  ----------------------------------------------------------------------- */
					$sdate = floor((time() - strtotime( $list_values['starting_date'] )) / 86400);
					$edate = floor((strtotime( $list_values['ending_date'] ) - time()) / 86400);

					$src = $_SERVER['DOCUMENT_ROOT'] . UNDER_SUBFOLDER ."/". CONFIG_FILES_UPLOAD_ROOT . $list_values['filename'];
					if ( file_exists( $src ) ){
							list( $width, $height ) = getimagesize( $src );
							$widthsize	= ( $width >= $maxWidth ) ? round( ( $width / $maxWidth ) * 100 ) : $width;
							$heightsize	= floor( ( $height / $width ) * $widthsize );
					}
					$shtmlTitle	= stripslashes( $list_values['title'] );
					$lisextra			= _getCheckNullorNot( $list_values['extra_class'] ) ? " class=\"".$list_values['extra_class'] .$count."\"" : "";

					if ( $sdate >= 0 && $edate >= 0){
							if ( _getCheckNullorNot( $list_values['urls'] ) ){
								$advlist .= "\n<li".$lisextra ."><a href=\"". $this->getProperLink($list_values['urls']) ."\" title=\"".$shtmlTitle."\" target=\"".$list_values['target_window']."\">".getDisplayImg( $list_values['filename'], $widthsize, true, $shtmlTitle, false, "advlist" )."</a></li>";
							}
							else {
								$advlist .=  "\n<li".$lisextra .">". getDisplayImg( $list_values['filename'], $maxWidth, true, $shtmlTitle, false, "advlist" ) ."</li>";
							}
					}
					elseif ( $list_values['starting_date'] == "0000-00-00" && $edate >= 0 ) {
							if ( _getCheckNullorNot( $list_values['urls'] ) ){
								$advlist .= "\n<li".$lisextra ."><a href=\"". $this->getProperLink($list_values['urls']) ."\" title=\"".$shtmlTitle."\" target=\"".$list_values['target_window']."\">".getDisplayImg( $list_values['filename'], $widthsize, true, $shtmlTitle, false, "advlist" )."</a></li>";
							}
							else {
								$advlist .=  "\n<li".$lisextra .">". getDisplayImg( $list_values['filename'], $maxWidth, true, $shtmlTitle, false, "advlist" ) ."</li>";
							}
					}
					elseif ( $list_values['starting_date'] == "0000-00-00" && $list_values['ending_date']  == "0000-00-00" ) {
							if ( _getCheckNullorNot( $list_values['urls'] ) ){
								$advlist .= "\n<li".$lisextra ."><a href=\"". $this->getProperLink($list_values['urls']) ."\" title=\"".$shtmlTitle."\" target=\"".$list_values['target_window']."\">".getDisplayImg( $list_values['filename'], $widthsize, true, $shtmlTitle, false, "advlist" )."</a></li>";
							}
							else {
								$advlist .=  "\n<li".$lisextra .">\n". getDisplayImg( $list_values['filename'], $maxWidth, true, $shtmlTitle, false, "advlist" ) ."</li>";
							}
					}
					$count++;
			}
			$inside = _getCheckNullorNot($eclass) ? "<!-- Bof Advertises List --><ul class=\"{$eclass}\">". $advlist ."\n</ul><!-- Eof Advertises List -->" : "\n\t<!-- Bof Ads List -->". $advlist ."\n\t<!--Eof Ads List -->\n";
			if ( $nailthumb == true ){
				$inside  .= "<script type=\"text/javascript\">
									jQuery( document ).ready( function(){
										jQuery( '.advlist' ).nailthumb( {} );
									} );
								</script>";
			}
		}
		/* To get one thumbnail image to represent of the photo page
		 * Using general function getDisplayImg( $filename, $maxWidth, $nailthumb = true OR false, $alttxt, $useDiv = true OR false, $extraClass )
		**/
		elseif ( $type === "photos" ){
			$list_query = $this->dbConnect->getQuery( "SELECT b.id, b.title, b.title_alias, b.filename, b.thumbnail, b.created_date, o.external, o.pid, m.alias FROM {$tbname} b, opensef  o LEFT JOIN menu m ON o.pid = m.pid  WHERE b.id = o.tid AND o.tbname = '{$tbname}' AND b.notice = '0' AND o.publish = '1' AND m.status	= '1' AND b.access_level >= '1' AND b.group_level >= '1' AND b.publish = '1' AND b.status = '1' {$list_tail} ORDER BY b.ordering DESC LIMIT 0, {$max}" );

			$nailthumb = false;
			$maxCols = 4;
			$recIdx = 0;

			while($list_values = $this->dbConnect->getFetch_Array($list_query)){
				$recIdx++;
				$maxWidth = 445;
				if ( _getCheckNullorNot($list_values['thumbnail']) ){
						$image_name[$recIdx] = $list_values['thumbnail'];
						$src = $_SERVER['DOCUMENT_ROOT'] . UNDER_SUBFOLDER ."/". CONFIG_FILES_UPLOAD_ROOT.$image_name[$recIdx];
						list( $width, $height ) = getimagesize( $src );
						$widthsize	= ( $width >= $maxWidth && 0 != $maxWidth ) ? round( ( $width / $maxWidth ) * 100 ) : $width;
						$heightsize	= floor( ( $height / $width ) * $widthsize );
				}
				else {
					$file_array = explode(";", $list_values['filename']);
					for($i=0; $i < count($file_array); $i++){
						$src[$i] = $_SERVER['DOCUMENT_ROOT'] . UNDER_SUBFOLDER ."/". CONFIG_FILES_UPLOAD_ROOT.$file_array[$i];
						if ( file_exists($src[$i]) ){
							$image_size[$i] = getimagesize($src[$i]);
							$widthsize	= ( $image_size[$i][0] >= $maxWidth ) ? round( ( $image_size[$i][0] / $maxWidth ) * 100 ) : $image_size[$i][0];
							$heightsize	= floor( ( $image_size[$i][1] / $image_size[$i][0] ) * $widthsize );
							if ( $heightsize < 70 ){
								$image_name[$recIdx] = $file_array[$i];
								$i = count($file_array) + 1;
							}
						}
					}
				}

				if ( empty($image_name[$recIdx]) ){
					$image_name[$recIdx] = $file_array[0];
					$maxWidth = 40;
				}

				$image_location = $base_url. UNDER_SUBFOLDER .$list_values['alias'].$list_values['external'];

				if ($recIdx % $maxCols == 1){
					$_boardlist .= "\n\t<tr>";
				}
				$shtmlTitle			= stripslashes( $list_values['title'] );
				$_boardlist .= "\n\t<td><a href=\"".$image_location."\" title=\"".$shtmlTitle."\" target=\"_self\" style=\"max-width: ".$widthsize."px;\">". getDisplayImg( $image_name[$recIdx], $maxWidth, true, $shtmlTitle, false, "frontpiclist" ) ."</a></td>";
				//Close row if needed

				if ($recIdx % $maxCols == 0){
					$_boardlist .= "\n\t</tr>";
				}
			}
			//Close last row if needed
			if ($recIdx % $maxCols != 0){
				$_boardlist .= "\n\t</tr>";
			}
			$inside  = "\n\t<table class=\"front_photo_group\">".$_boardlist."\n\t</table>";
			if ( $nailthumb == true ){
				$inside  .= "<script type=\"text/javascript\">
									jQuery( document ).ready( function(){
										jQuery( '.frontpiclist' ).nailthumb( {} );
									} );
								</script>";
			}

		}
		elseif ( $type === "videos" ||  $type === "videos_txt" ){
			/** -------------------------------------------------------------------------
			 * videos_txt is to display articles with small video on left with the title on right.
			 * vides is to display ONLY video
			 * If it has extra class value, that means <ul> tag will be added with extra class.
			 * If it does not have a video, thumbnail image will be display at the same spot.
			 * If $thumbtxt is not empty or null, it will be added short description.
			 *  ----------------------------------------------------------------------- */
			$count = 1;
			$inside = "\n";
			$list_query = $this->dbConnect->getQuery( "SELECT b.title, b.fulltxt, b.summarytxt, b.thumbnail, b.embed_bigcode, b.ordering, b.host, b.casting_date, b.created_date, b.created_by, o.external, o.pid, m.alias FROM {$tbname} b, opensef  o LEFT JOIN menu m ON o.pid = m.pid WHERE b.id = o.tid AND o.tbname = '{$tbname}' AND o.publish = '1' AND m.status = '1' AND b.publish = '1' AND b.status = '1' {$list_tail} ORDER BY b.ordering DESC LIMIT 0, {$max}" );
			while ( $insideObject = $this->dbConnect->getFetch_Array( $list_query ) ){
					$descriptxt 	= _getCheckNullorNot( $insideObject['summarytxt'] ) ? stripslashes( $insideObject['summarytxt'] ) : _mb_strcut( stripslashes( $insideObject['fulltxt'] ), 0, 200 ) ;
					$short_des	= _getCheckNullorNot( $thumbtxt ) ? "<div class=\"shortdes\">". $descriptxt ."</div>" : "";
					$maxWidth	= 400;
				  $embed_codes = "";
					if ( _getCheckNullorNot( $insideObject['embed_code'] ) ){
							$video_array		= explode( ",", $insideObject['embed_code'] );
							if ( count($video_array) >0 && $video_array[0] === "youtube"){
								$embed_codes = '<span class="front_vod_wrap"><div class="embed_vod"><iframe width="'.$video_array[2].'" height="'.$video_array[3].'" src="//www.youtube.com/embed/'.$video_array[1].'?hd=1&amp;rel=0&amp;autohide=1&amp;showinfo=0&amp;modestbranding=0&amp;controls=1&amp;fs=1" frameborder="0" allowfullscreen></iframe></div></span>';
							}
							else {
								$embed_codes = "<span class=\"front_vod_wrap\"><div class=\"embed_vod\">". $insideObject['embed_code'] ."</div></span>";
							}
					}
					else {
							if ( _getCheckNullorNot( $insideObject['thumbnail'] ) ){
									$extra_opt = "thumb";
									$src = $_SERVER['DOCUMENT_ROOT'] . UNDER_SUBFOLDER ."/". CONFIG_FILES_UPLOAD_ROOT . $insideObject['thumbnail'];
									if ( file_exists( $src ) ){
										list($width, $height, $type, $attr) = getimagesize( $src );
										$widthsize	= round( ( $width / $maxWidth) * 100 );
										$heightsize	= floor( ( $height / $width ) * $widthsize );
										$widthsize = $widthsize < 150 ? "150" : $widthsize;
									}
									$embed_codes = "<span class=\"front_img_wrap\"><img src=\"". $this->basicURL ."/upload/". $insideObject['thumbnail'] ."\" alt=\"". stripslashes( $insideObject['title'] ) ."\" width=\"". $widthsize ."\" height=\"". $heightsize ."\"></span>";
							}
					}

				$inside .= $type == "videos" ? "\t<li class='frameVideo".$count."'>\n\t<div class=\"img_box see_big\">\n\t<div  onclick=\"location.href='".$this->basicURL . $insideObject['alias'] . $insideObject['external']."'\" style=\"cursor:pointer\">\n\t<div class=\"description\" onfocus=\"this.blur()\">\n\t<span>". stripslashes( $insideObject['title'] ) ."</span>\n\t</div>\n\t".$embed_codes."\t</div>\n\t</div>\n\t</li>\n" : "\t<li class='frameVideo".$count."'>\n\t<a href=\"". $this->basicURL . $insideObject['alias'] . $insideObject['external']."\" title=\"". stripslashes( $insideObject['title'] ) ."\" target=\"_self\">". $embed_codes . "<h3 class=\"sfrontTitle\">". stripslashes( $insideObject['title'] ) ."</h3>". $short_des ."</a>\n\t</li>\n";

					$count++;
			}
			$inside = _getCheckNullorNot($eclass) ? "<ul class=\"{$eclass}\">".$inside."</ul>" : $inside;
		}

 		return $inside;
	}
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!