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

class getBackLoad {

	public $dbConnect;
	public $basicURL;
	public $static_url;
	public $setAllelements;
	public $error_flag;
	
	public $error_message;
	public $returnValue;
	public $cmsgenerator;
	public $minifyHtml;	
	
	public function __construct( $baseURL ){
		global $Bon_db, $base_url, $allElements;

		$this->dbConnect	 		= $Bon_db;
		$this->basicURL			= !_getCheckNullorNot( $baseURL ) ? $base_url."/" : $baseURL."/" ;
		$this->static_url 			= rtrim( str_replace( "www", CONFIG_STATIC_SUBDOMAIN, str_replace( "/backend", "", $this->basicURL ) ), "/" );
		$this->setAllelements	 	= $allElements;
		$this->error_flag				= false;
		$this->error_message		= array();
		$this->returnValue			= array();
		$this->cmsgenerator 		= TEXT_GENERATOR;
		$this->minifyHtml						= new getHTMLOptimize();	
		
		if( !preg_match( "/$server_host/i", $_SERVER['SERVER_NAME'] ) ){
			$this->error_message	= PLEASE_WRITE_ARTICLE_PROPERLY;
			$this->error_flag			= true;
		}

		if( getenv( $_SERVER['REQUEST_METHOD'] ) == "GET" ){
			$this->error_message	= PLEASE_WRITE_ARTICLE_PROPERLY_2;
			$this->error_flag			= true;
		}
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To load page contents
 *  ----------------------------------------------------------------------- */
	public function setLoading( $ci_path ){
		global $gvalue;

		if( !empty( $ci_path ) && !$this->error_flag ){

			//To set redirect URL from previews page
//			if( isset( $_SESSION['reffer'] ) && !empty( $_SESSION['reffer'] ) ){
//				$reffer =  $_SESSION['reffer'];
//				unset( $_SESSION['reffer'] );
//			}
//			else{
//				$reffer =  $_alias;
//			}

			$kLogin = new getLogin( $this->basicURL );

//			$this->returnValue['metainfo']	= $this->setMetInfo( $all_ids );
			$press_array 							=  array( "create","update", "delete", "copy", "move" );
			switch ( $gvalue['node'] ){
				case section:
					$kSections = new getSections( $this->basicURL );
					if( $gvalue['m'] === "view" && !in_array( $gvalue['press'], $press_array ) ){
						$_contents = $kSections->getDisplay( $gvalue['id'] );
					}
					else{
						$kSections->getCreate( $gvalue );
						$_contents = $kSections->getContents( $gvalue );
					}
				break;
				case category:
					$kCategoriess = new getCategories( $this->basicURL );
					if( $gvalue['m'] === "view" && !in_array( $gvalue['press'], $press_array ) ){
						$_contents = $kCategoriess->getDisplay( $gvalue['id'] );
					}
					else{
						$kCategoriess->getCreate( $gvalue );
						$_contents = $kCategoriess->getContents( $gvalue );
					}
				break;
				case menu:
					$kMenus = new getMenus( $this->basicURL );
					if( $gvalue['m'] === "view" && !in_array( $gvalue['press'], $press_array ) ){
						$_contents = $kMenus->getDisplay( $gvalue );
					}
					else{
						$kMenus->getCreate( $gvalue );
						$_contents = $kMenus->getContents( $gvalue );
					}
				break;
				case menutype:
					$kMenutypes = new getMenutype( $this->basicURL );
					if( $gvalue['m'] === "view" && !in_array( $gvalue['press'], $press_array ) ){
						$_contents = $kMenutypes->getDisplay( $gvalue );
					}
					else{
						$kMenutypes->getCreate( $gvalue );
						echo "<script type='text/javascript'>parent.location='indexx.php?node=menu';</script>";
					}
				break;
				case templates:
					$Mtemplates = new getTemplates( $this->basicURL );
					if( $gvalue['m'] === "view" && !in_array( $gvalue['press'], $press_array ) ){
						$_contents = $Mtemplates->getDisplay( $gvalue );
					}
					else{
						$Mtemplates->getCreate( $gvalue );
						$_contents = $Mtemplates->getContents( $gvalue );
					}
				break;
				case page:
					$kPages = new getPages( $this->basicURL );
					if( $gvalue['m'] === "view" && !in_array( $gvalue['press'], $press_array ) ){
						$_contents = $kPages->getDisplay( $gvalue );
					}
					else{
						$kPages->getCreate( $gvalue );
						$_contents = $kPages->getContents( $gvalue );
					}
				break;
				case article:
					$kArticles = new getArticles( $this->basicURL );
					if( $gvalue['m'] === "view" && !in_array( $gvalue['press'], $press_array ) ){
						$_contents = $kArticles->getDisplay( $gvalue );
					}
					else{
						if( $gvalue['submitbutton'] === "Move" ){
							$_contents = $kArticles->getMoveform( $gvalue );
						}
						else{
							if (isset($gvalue['submitbutton'])){
								$kArticles->getCreate( $gvalue );	
							}						
							$_contents = $kArticles->getContents( $gvalue );
						}
					}
				break;
				case photo:
					$kPhotos = new getPhotos( $this->basicURL );
					if( $gvalue['m'] === "view" && !in_array( $gvalue['press'], $press_array ) ){
						$_contents = $kPhotos->getDisplay( $gvalue );
					}
					else{
						if( $gvalue['submitbutton'] === "Move" ){
							$_contents = $kPhotos->getMoveform( $gvalue );
						}
						else{
							if (isset($gvalue['submitbutton'])){						
								$kPhotos->getCreate( $gvalue );
							}
							$_contents = $kPhotos->getContents( $gvalue );
						}
					}
				break;
				case casting:
					$kCastings = new getCastings( $this->basicURL );
					if( $gvalue['m'] === "view" && !in_array( $gvalue['press'], $press_array ) ){
						$_contents = $kCastings->getDisplay( $gvalue );
					}
					else{
						if( $gvalue['submitbutton'] === "Move" ){
							$_contents = $kCastings->getMoveform( $gvalue );
						}
						else{
							if (isset($gvalue['submitbutton'])){						
								$kCastings->getCreate( $gvalue );
							}
							$_contents = $kCastings->getContents( $gvalue );
						}
					}
				break;
				case modules:
						$kModules = new getModules( $this->basicURL );
					if( $gvalue['m'] === "view" && !in_array( $gvalue['press'], $press_array ) ){
						$_contents = $kModules->getDisplay( $gvalue );
					}
					else{				
						$kModules->getCreate( $gvalue );
						$_contents = $kModules->getContents( $gvalue );
					}
				break;
				case member:
					$kMembers = new getMembers( $this->basicURL );
					if( $gvalue['m'] === "view" && !in_array( $gvalue['press'], $press_array ) ){
						$_contents = $kMembers->getDisplay( $gvalue );
					}
					else{
						$kMembers->getCreate( $gvalue );
						$_contents = $kMembers->getContents( $gvalue );
					}
				break;
				case staff:
					$kStaffs = new getStaffs( $this->basicURL );
					if( $gvalue['m'] === "view" && !in_array( $gvalue['press'], $press_array ) ){
						$_contents = $kStaffs->getDisplay( $gvalue );
					}
					else{
						$kStaffs->getCreate( $gvalue );
						$_contents = $kStaffs->getContents( $gvalue );
					}
				break;
				case banner:
					$kBanners = new getBanners( $this->basicURL );
					if( $gvalue['m'] === "view" && !in_array( $gvalue['press'], $press_array ) ){
						$_contents = $kBanners->getDisplay( $gvalue );
					}
					else{
						$kBanners->getCreate( $gvalue );
						$_contents = $kBanners->getContents( $gvalue );
					}
				break;
				case adv:
					$kBanners = new getAdvertises( $this->basicURL );
					if( $gvalue['m'] === "view" && !in_array( $gvalue['press'], $press_array ) ){
						$_contents = $kBanners->getDisplay( $gvalue );
					}
					else{
						$kBanners->getCreate( $gvalue );
						$_contents = $kBanners->getContents( $gvalue );
					}
				break;
				case advertises_pages:
					$kBanners = new getAds2Page( $this->basicURL );
					if( $gvalue['m'] === "view" && !in_array( $gvalue['press'], $press_array ) ){
						$_contents = $kBanners->getDisplay( $gvalue );
					}
					else{
						$kBanners->getCreate( $gvalue );
						$_contents = $kBanners->getContents( $gvalue );
					}
				break;				
				case sef:
					$kSEFs = new getSEF( $this->basicURL );
					if( $gvalue['m'] === "view" && !in_array( $gvalue['press'], $press_array ) ){
						$_contents = $kSEFs->getDisplay( $gvalue );
					}
					else{
						$kSEFs->getCreate( $gvalue );
						$_contents = $kSEFs->getContents( $gvalue );
					}
				break;
				case config:
					$kSEFs = new getConfigs( $this->basicURL );
					if( $gvalue['m'] === "view" && !in_array( $gvalue['press'], $press_array ) ){
						$_contents = $kSEFs->getDisplay( $gvalue );
					}
					else{
						$kSEFs->getCreate( $gvalue );
						$_contents = $kSEFs->getContents( $gvalue );
					}
				break;
				case forms:
					$kSEFs = new getForms( $this->basicURL );
					if( $gvalue['m'] === "view" && !in_array( $gvalue['press'], $press_array ) ){
						$_contents = $kSEFs->getDisplay( $gvalue );
					}
					else{
						$kSEFs->getCreate( $gvalue );
						$_contents = $kSEFs->getContents( $gvalue );
					}
				break;

				case login:
					if( $gvalue['press'] === "doing" && !empty( $gvalue['username'] ) && !empty( $gvalue['password'] ) ){
						$_contents = $kLogin->setLogin( $gvalue );
					}
					else{
						$_contents = $kLogin->setHTML( "login", $this->basicURL );
					}
				break;
				case forgot_password:
					if( $gvalue['press'] === "doing" && !empty( $gvalue['username'] ) ){
						$_contents = $kLogin->setLogin( $gvalue );
					}
					else{
						$_contents = $kLogin->setHTML( "forgot_password", $this->basicURL );
					}
				break;
				case logout:
						$_contents = $kLogin->setLogout();
				break;
				default:
						$_contents  = $this->setAllelements->getCurrentItems( "pages", 10, "pages", "1", "1" );
						$_contents .= $this->setAllelements->getCurrentItems( "articles", 10, "articles", "4", "" );
						$_contents .= "\n<table border=\"0\" >\n<tr>\n\t<td colspan=\"4\"><div class=\"currenttitle\"><strong>Quick Menus</strong></div></td>\n</tr>\n\t<tr>\n\t";
						$_contents .= "<td width=\"70\" align=\"center\" valign=\"top\"><a href=\"".$this->basicURL."/indexx.php?node=page\" title=\"\" target=\"_self\" onfocus=\"this.blur()\" class=\"sc-button\"><span>Pages</span></a></td>\n\t";
						$_contents .= "<td width=\"70\" align=\"center\" valign=\"top\"><a href=\"".$this->basicURL."/indexx.php?node=menu\" title=\"\" target=\"_self\" onfocus=\"this.blur()\" class=\"sc-button\"><span>Menus</span></a></td>\n\t";
						$_contents .= "<td width=\"70\" align=\"center\" valign=\"top\"><a href=\"".$this->basicURL."/indexx.php?node=banner\" title=\"\" target=\"_self\" onfocus=\"this.blur()\" class=\"sc-button\"><span>Banners</span></a></td>\n";
						$_contents .= "<td width=\"70\" align=\"center\" valign=\"top\"><a href=\"".$this->basicURL."/indexx.php?node=member\" title=\"\" target=\"_self\" onfocus=\"this.blur()\" class=\"sc-button\"><span>Members</span></a></td>\n";
						$_contents .= "\n</tr>\n</table>";
				break;
			}

			//To display No page existing
			$this->returnValue['contents']  = $this->minifyHtml->minifyHTML("\n<!--// Bof Contents //-->\n<div class=\"first_pages_component\">\n". $_contents."\n</div>\n<!--// Eof Contents //-->\n\n");
		}
		else{
			$this->returnValue['contents']  = $this->minifyHtml->minifyHTML("\n<!--// Bof Contents //-->\n<div class=\"first_pages_component\">\n<!---Bof Error Message-->\n". $this->setAllelements->setMessage( $this->error_message )."\n<!---Eof Error Message-->\n</div>\n<!--// Eof Contents //-->\n\n");
		}

		return $this->returnValue;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To set Contents
 *  ----------------------------------------------------------------------- */
	public function setContents( $kbNews, $allContents ){
		global $press_array;

		if( $allContents['m'] === "view" && !in_array( $allContents['press'], $press_array ) ){
			$_contents = $kbNews->getDisplay( $allContents );
		}
		else{
			$kSEFs->getCreate( $allContents );
			$_contents = $kbNews->getContents( $allContents );
		}

		return $_contents;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get page meta information
 *  ----------------------------------------------------------------------- */
	public function setMetInfo( $pid ){
			$divider = "&nbsp;-&nbsp;";

			if( !empty( $pid['pid'] ) ){
				$meta_query	= "SELECT metatitle, metakey, metadesc FROM pages WHERE id = '". $pid['pid'] ."' AND access_level = '0' AND group_level = '0' AND publish = '1' AND status = '1'";
				$meta_items	= $this->dbConnect->getContentsInArray( $meta_query );
				$_title			= !empty( $meta_items['metatitle'] ) ? $meta_items['metatitle']. $divider .CONFIG_DEFAULT_META_TITLE : CONFIG_DEFAULT_META_TITLE;
				$_keyword		= !empty( $meta_items['metakey'] ) ? $meta_items['metakey']. $divider .CONFIG_DEFAULT_META_KEYWORDS : CONFIG_DEFAULT_META_KEYWORDS;
				$_description = !empty( $meta_items['metadesc'] ) ? $meta_items['metadesc']. $divider  .CONFIG_DEFAULT_META_DESCRIPTIONS : CONFIG_DEFAULT_META_DESCRIPTIONS;

			}
			else{
				$_title 			= CONFIG_DEFAULT_META_TITLE;
				$_keyword		= CONFIG_DEFAULT_META_KEYWORDS;
				$_description	= CONFIG_DEFAULT_META_DESCRIPTIONS;
			}

			$metaInfo['pagetitle']		=  "<title>{$_title}</title>\n";
			$metaInfo['title']				=  "\t<meta name=\"title\" content=\"{$_title}\" />\n";
			$metaInfo['keyword']		=  "\t<meta name=\"keywords\" content=\"{$_keyword }\" />\n";
			$metaInfo['description']	=  "\t<meta name=\"description\" content=\"{$_description}\" />\n";
			$metaInfo['generator']	=  "\t<meta name=\"generator\" content=\"{$this->cmsgenerator}\" />\n";
			$metaInfo['owner']			=  "\t<meta name=\"owner\" content=\"{$this->cmsgenerator}\" />\n";
			$metaInfo['base_url']		=  "\t<base href=\"{$this->basicURL}\" />\n";

		return $metaInfo;
	}
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!