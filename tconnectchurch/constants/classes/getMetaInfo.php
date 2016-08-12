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

class getMetaInfo {

	public static $dbConnect;
	public static $basicURL;
	public static $cmsgenerator;
	public static $owner;
	public static $metaInfo;

	public function __construct( $baseURL ){
		global $Bon_db;

		self::$dbConnect	 	= $Bon_db;
		self::$basicURL			= $baseURL;
		self::$cmsgenerator 	= TEXT_GENERATOR;
		self::$owner 				= TEXT_OWNER;
		self::$metaInfo 			= array();
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]:: To
 *  ------------------------------------------------------------------------- */
	public function setMetInfos( $pid ){
			global $_ci_path;

			$divider = META_DIVIDER;
			$_sitename	= CONFIG_SITE_NAME;
			$_twitterid	= CONFIG_TWITTER_ID;
			if ( $pid === "404"){
				$_title 			= "404 Error " . $divider . CONFIG_DEFAULT_META_TITLE;
				$_keyword 	= CONFIG_DEFAULT_META_KEYWORDS;
				$_description = CONFIG_DEFAULT_META_DESCRIPTIONS;

			}
			elseif ( _getCheckNullorNot( $pid['pid'] ) && !_getCheckNullorNot( $pid['tid'] )){
				$meta_query = "SELECT metatitle, metakey, metadesc FROM pages WHERE id = '". $pid['pid'] ."' AND access_level >= '{$this->_userlevel}' AND group_level >= '{$this->_gid}' AND publish = '1' AND status = '1'";
				$meta_items = self::$dbConnect->getContentsInArray( $meta_query );

				$_title 			= _getCheckNullorNot( $meta_items['metatitle'] ) ? $meta_items['metatitle']. $divider .CONFIG_DEFAULT_META_TITLE : CONFIG_DEFAULT_META_TITLE;
				$_keyword 	= _getCheckNullorNot( $meta_items['metakey'] ) ? $meta_items['metakey']. $divider .CONFIG_DEFAULT_META_KEYWORDS : CONFIG_DEFAULT_META_KEYWORDS;
				$_description = _getCheckNullorNot( $meta_items['metadesc'] ) ? $meta_items['metadesc']. $divider  .CONFIG_DEFAULT_META_DESCRIPTIONS : CONFIG_DEFAULT_META_DESCRIPTIONS;
				$_fbImage		= "/static/images/og.png";
				$_twImage		= "/static/images/responsive.jpg";
			}
			elseif ( _getCheckNullorNot( $pid['pid'] ) && _getCheckNullorNot( $pid['tid'] )){
				$metaItem_q = "SELECT b.id, b.title, b.title_alias, b.thumbnail, b.filename, b.fulltxt, b.summarytxt, b.metatitle, b.metakey, b.metadesc, b.casting_description FROM {$pid['tbname']} b, opensef  o LEFT JOIN menu m ON o.pid = m.pid  WHERE b.id = o.tid AND o.tbname = '{$pid['tbname']}' AND b.id = '{$pid['tid']}' AND b.notice = '0' AND o.publish = '1' AND m.status	= '1' AND b.access_level >= '{$this->_userlevel}' AND b.group_level >= '{$this->_gid}' AND b.publish = '1' AND b.status = '1'";
				$meta_article = self::$dbConnect->getContentsInArray( $metaItem_q );

				$meta_article['summarytxt']	= _getCheckNullorNot( $meta_article['summarytxt'] ) ? $meta_article['summarytxt'] : $meta_article['casting_description'];
				$meta_article['metatitle'] 	= _getCheckNullorNot( $meta_article['metatitle'] ) ? $meta_article['metatitle'] : $meta_article['title'];
				$meta_article['metadesc']	= _getCheckNullorNot( $meta_article['metadesc'] ) ? $meta_article['metadesc'] : _mb_strcut( strip_tags($meta_article['summarytxt']), 0, 200 );

				$_title 			= _getCheckNullorNot( $meta_article['metatitle'] ) ? $meta_article['metatitle']. $divider .CONFIG_DEFAULT_META_TITLE : CONFIG_DEFAULT_META_TITLE;
				$_keyword 	= _getCheckNullorNot( $meta_article['metakey'] ) ? $meta_article['metakey']. $divider .CONFIG_DEFAULT_META_KEYWORDS : CONFIG_DEFAULT_META_KEYWORDS;
				$_description	= _getCheckNullorNot( $meta_article['metadesc'] ) ? strip_tags($meta_article['metadesc']). $divider .CONFIG_DEFAULT_META_DESCRIPTIONS : _mb_strcut( strip_tags($meta_article['fulltxt']), 0, 200 ). $divider.CONFIG_DEFAULT_META_DESCRIPTIONS;
				$_fbImage		= _getCheckNullorNot( $meta_article['thumbnail'] ) ? "/upload/". trim( $meta_article['thumbnail'] ) : "/static/images/og.png";
				$_twImage		= _getCheckNullorNot( $meta_article['thumbnail'] ) ? "/upload/". trim( $meta_article['thumbnail'] ) : "/static/images/responsive.jpg";
			} else {
				$_title 			= CONFIG_DEFAULT_META_TITLE;
				$_keyword		= CONFIG_DEFAULT_META_KEYWORDS;
				$_description	= CONFIG_DEFAULT_META_DESCRIPTIONS;
				$_fbImage		= "/static/images/og.png";
				$_twImage		= "/static/images/responsive.jpg";
			}

			self::$metaInfo['favpng']				= "<link rel=\"icon\" href=\"".self::$basicURL."/static/images/favicon.png\" type=\"image/png\" />\n";
			self::$metaInfo['pagetitle']								=  "<title>{$_title}</title>\n";
			self::$metaInfo['keyword']								=  "<meta name=\"keywords\" content=\"{$_keyword }\" />\n";
			self::$metaInfo['description']						=  "<meta name=\"description\" content=\"{$_description}\" />\n";
			self::$metaInfo['generator']							=  "<meta name=\"generator\" content=\"". self::$cmsgenerator ."\" />\n";
			self::$metaInfo['base_url']								=  "<base href=\"".self::$basicURL."\" />\n";
			self::$metaInfo['canonical']				= "<link rel=\"canonical\" href=\"".self::$basicURL.$_ci_path."\" />\n";
			self::$metaInfo['image_src']				=  "<link rel=\"image_src\" href=\"".self::$basicURL."{$_fbImage}\" />\n";
//			self::$metaInfo['favicon']				= "<link rel=\"shortcut icon\" href=\"".self::$basicURL."/static/images/favicon.ico\" type=\"image/x-icon\" />\n";
//			self::$metaInfo['itemprop']				= "<meta itemprop=\"url\" content=\"".self::$basicURL.$_ci_path."\" />\n";
//			self::$metaInfo['image']						=  "<meta name=\"image\" content=\"".self::$basicURL."{$_fbImage}\" />\n";
//			self::$metaInfo['thumbnail']						=  "<meta name=\"thumbnail\" content=\"".self::$basicURL."{$_fbImage}\" />\n";

			if ( CONFIG_USE_FB_META === "true" ){
/*
268871339946116
<!-- Apple -->
<link rel="apple-touch-icon" sizes="144x144" href="<?php echo $base_url;?>/static/images/apple-touch-icon-144.png">
<link rel="apple-touch-icon" sizes="114x114" href="<?php echo $base_url;?>/static/images/apple-touch-icon-114.png">
<link rel="apple-touch-icon" sizes="72x72" href="<?php echo $base_url;?>/static/images/apple-touch-icon-72.png">
<link rel="apple-touch-icon" sizes="57x57" href="<?php echo $base_url;?>/static/images/apple-touch-icon-57.png">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="green">
268002589922519
					self::$metaInfo['ogfb:app_id']									=  "<meta property=\"fb:app_id\" content=\"268002589922519\" />\n";
					self::$metaInfo['og:locale']										=  "<meta property=\"og:locale\" content=\"en_US\"/>\n";
*/
  				self::$metaInfo['og:site_name']				=  "<meta property=\"og:site_name\" content=\"{$_sitename}\" />\n";
					self::$metaInfo['og:title']									=  "<meta property=\"og:title\" content=\"{$_title}\" />\n";
					self::$metaInfo['og:type']									=  "<meta property=\"og:type\" content=\"article\" />\n";
					self::$metaInfo['og:url']										=  "<meta property=\"og:url\" content=\"".self::$basicURL.$_ci_path."\" />\n";
					self::$metaInfo['og:image']								=  "<meta property=\"og:image\" content=\"".self::$basicURL."{$_fbImage}\" />\n";
					self::$metaInfo['og:description']				=  "<meta property=\"og:description\" content=\"{$_description}\" />\n";
			}
			if ( CONFIG_USE_TWITTER_META === "true" ){
					self::$metaInfo['twitter:card']						=  "<meta name=\"twitter:card\" content=\"summary_large_image\" />\n";
					self::$metaInfo['twitter:site']							=  "<meta name=\"twitter:site\" content=\"@{$_twitterid}\" />\n";
					self::$metaInfo['twitter:creator']				=  "<meta name=\"twitter:creator\" content=\"@{$_twitterid}\" />\n";
					self::$metaInfo['twitter:title']							=  "<meta name=\"twitter:title\" content=\"{$_title}\" />\n";
					self::$metaInfo['twitter:description']	=  "<meta name=\"twitter:description\" content=\"{$_description}\" />\n";
					self::$metaInfo['twitter:image']					=  "<meta name=\"twitter:image\" content=\"".self::$basicURL."{$_twImage}\" />\n";
			}

			unset( $pid );

		return self::$metaInfo;
	}
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!