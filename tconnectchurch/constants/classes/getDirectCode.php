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
 * Modified from DirectPHP by kksou
 * http://www.kksou.com/php-gtk2/Joomla/DirectPHP-plugin.php
 *  ------------------------------------------------------------------------- */
defined( "_VALID_MOS" ) or die( "Your system is not working properly." );

class getDirectCode {

	public static 	$php_start;
	public static	$php_end;

	public function __construct(){
		self::$php_start = "<?php";
		self::$php_end = "?>";
	}

/** -------------------------------------------------------------------------
 * [03/03/2014]:: To
 *  ------------------------------------------------------------------------- */
	public static function getDirectPHPS( $contents, $using_no_editor, $pageid = "" ){
		global $Bon_db, $base_url;

		$count			= "SELECT count(*) as total FROM apps_pages ap LEFT JOIN modules m ON m.id = ap.app_id  WHERE  m.inpage = '1' AND ap.pageid LIKE '%[".$pageid."]%' AND m.publish = '1' AND m.status = '1' AND ap.publish = '1'";
		$countObject	= $Bon_db->getObject( $count );
		if ($countObject->total > 0){
			$modules_query = $Bon_db->getQuery("SELECT app_id, app_position FROM apps_pages ap LEFT JOIN modules m ON m.id = ap.app_id  WHERE  m.inpage = '1' AND ap.pageid LIKE '%[".$pageid."]%' AND m.publish = '1' AND m.status = '1' AND ap.publish = '1' ORDER BY m.ordering DESC");
			while( $modules_items = $Bon_db->getFetch_Array($modules_query) ){
				$item_query = $Bon_db->getQuery("SELECT m.id, title, fulltxt, showtitle, position, filename FROM modules m LEFT JOIN apps_pages ap ON m.id = ap.app_id  WHERE  m.inpage = '1' AND m.id = '".$modules_items['app_id']."' AND ap.pageid LIKE '%[".$pageid."]%' AND m.publish = '1' AND m.status = '1' AND ap.publish = '1' AND m.access_level >= '0' ORDER BY m.ordering DESC");
				
				while( $_modules = $Bon_db->getFetch_Array($item_query) ){
						if ( _getCheckNullorNot( $_modules['filename'] ) ){
								$module_file	 			 = CONFIG_DOC_ROOT . "/modules/".$_modules['filename'].".inc";
								$$_modules['position']	.= file_get_contents($module_file, FILE_USE_INCLUDE_PATH);;
						}
						else {
								$$_modules['position']	.= $_modules['fulltxt'];
						}					
				}
			}
			$contents = $toppage . $contents . $bottompage;
		}

		$contents = self::fix_str( $contents );
		$output = "";
		$regexp = '/(.*?)'.self::fix_reg(self::$php_start).'\s+(.*?)'.self::fix_reg(self::$php_end).'(.*)/s';

		$found = preg_match( $regexp, $contents, $matches );
		while ( $found ){
			$output .= $matches[1];
			$phpcode = $matches[2];

			if ( self::check_php($phpcode) ){
				ob_start();
				if ( $using_no_editor ){
					eval( $phpcode );
				} else {
					eval( self::fix_str2( $phpcode ) );
				}
				$output .= ob_get_contents();
				ob_end_clean();
			} else {
				$output .= "The following command is not allowed: <strong>$errmsg</strong>";
			}

			$contents = $matches[3];
			$found = preg_match( $regexp, $contents, $matches );
		}
		$output .= $contents;

		return	$output;
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 *  ------------------------------------------------------------------------- */
	public static function fix_str( $str ){
		$str = str_replace( '{?php', '<?php', $str );
		$str = str_replace( '?}', '?>', $str );
		$str = preg_replace( array( '%&lt;\?php( \s|&nbsp;|<br\s/>|<br>|<p>|</p> )%s', '/\?&gt;/s', '/-&gt;/' ), array( '<?php ', '?>', '->' ), $str );

		return $str;
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 *  ------------------------------------------------------------------------- */
	public static function fix_str2( $str ){
		# $str = str_replace( '<br>', "\n", $str );
		# $str = str_replace( '<br />', "\n", $str );
		# $str = str_replace( '<p>', "\n", $str );
		# $str = str_replace( '</p>', "\n", $str );
		$str = str_replace( '&#39;', "'", $str );
		$str = str_replace( '&quot;', '"', $str );
		$str = str_replace( '&lt;', '<', $str );
		$str = str_replace( '&gt;', '>', $str );
		$str = str_replace( '&amp;', '&', $str );
		$str = str_replace( '&nbsp;', ' ', $str );
		$str = str_replace( '&#160;', "\t", $str );
		$str = str_replace( chr( hexdec( 'C2' ) ).chr( hexdec( 'A0' ) ), '', $str );
		$str = str_replace( html_entity_decode( "&Acirc;&nbsp;" ), '', $str );

		return $str;
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 *  ------------------------------------------------------------------------- */
	public static function fix_reg( $str ){
		$str = str_replace( '?', '\?', $str );
		$str = str_replace( '{', '\{', $str );
		$str = str_replace( '}', '\}', $str );

		return $str;
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 *  ------------------------------------------------------------------------- */
	public static function check_php( $code ){
		global $Config_enable_command_block, $Config_block_list, $errmsg;

		$status = 1;
		if ( !$Config_enable_command_block ) return $status;

		$function_list = array();

		if ( preg_match_all( '/([a-zA-Z0-9_]+)\s*[(|"|\']/s', $code, $matches ) ){
			$function_list = $matches[1];
		}

		if ( preg_match( '/`(.*?)`/s', $code ) ){
			$status		= 0;
			$errmsg	= 'backticks ( `` )';

			return $status;
		}

		if ( preg_match( '/\$database\s*->\s*([a-zA-Z0-9_]+)\s*[(|"|\']/s', $code, $matches ) ){
			$status		= 0;
			$errmsg	= 'database->'.$matches[1];

			return $status;
		}

		foreach( $function_list as $command ){
			if ( in_array( $command, $Config_block_list ) ){
				$status 		= 0;
				$errmsg	= $command;
				break;
			}
		}

		return $status;
	}

}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!