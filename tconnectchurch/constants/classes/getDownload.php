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

class getDownload {

	public static $dbConnect;
	public static $basicURL;

	public function __construct( $basicURL ){
		global $Bon_db, $base_url;

		self::$dbConnect	 	= $Bon_db;
		self::$basicURL			= !_getCheckNullorNot( $basicURL ) ? $base_url : $basicURL;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]:: To
 * ----------------------------------------------------------------------- */
	public function setProcess( $allValues ){
		$allValues = trim( $allValues, '/' );

		if ( isset( $allValues ) && !empty( $allValues ) && $allValues != "download" ){
			$haystack	= rtrim( urldecode( $allValues ), '/' );
		}

		if ( empty( $haystack ) ){
			echo "<script type='text/javascript'>window.alert( '".CONFIG_NOTICE_EXIST_NO_FILE."-01' );window.history.go( -1 );</script>";
			exit();
		}

		$check_array = array( ".php", ".html", ".htm", ".css", ".txt", ".htaccess", ".js" );
		foreach ( $check_array as $value ){
			if ( !stristr( $haystack, $value ) === "false" ){
				echo "<script type='text/javascript'>window.alert( '".CONFIG_NOTICE_EXIST_NO_FILE."-02' );window.history.go( -1 );</script>";
				exit();

			} else {
				$valueObject		= self::$dbConnect->getAllContents( "articles", "status = '1' AND publish = '1' AND ( linkfile LIKE '%".$haystack."%' OR filename LIKE '%".$haystack."%' )" );

				if ( $valueObject['loginDownload'] == "1" ){
					if ( get_cookie( "okayTo" ) != "downLoad" ){
						echo "<script type='text/javascript'>window.alert( '".CONFIG_NOTICE_REGISTER_DOWNLOAD."' );window.history.go( -1 );</script>";
						exit();

					}else {
						if ( isset( $_COOKIE[md5( "okayTo" )] ) ){
							unset_cookie( "okayTo", 1800 );
						}
					}
				}

				$filename			= $_SERVER['DOCUMENT_ROOT'] . UNDER_SUBFOLDER . "/". CONFIG_FILES_UPLOAD_ROOT . $haystack;
				$file_extension	= strtolower( substr( strrchr( $haystack ,"." ),1 ) );
				$file_values		= explode( "/", trim( $haystack ) );
				$just_file_name	= end( $file_values );

				if ( !file_exists( $filename ) ){
					echo "<script type='text/javascript'>window.alert( '".CONFIG_NOTICE_EXIST_NO_FILE."-03' );window.history.go( -1 );</script>";
					exit();
				};
			}
		}

		//Required for IE, otherwise Content-disposition is ignored
		if ( ini_get( 'zlib.output_compression' ) ){ ini_set( 'zlib.output_compression', 'Off' ); }
		switch( $file_extension ){
				case 'json': $ctype= 'application/json'; break;
				case 'xml': $ctype= 'application/xml'; break;
				case 'swf': $ctype= 'application/x-shockwave-flash'; break;
				case 'flv': $ctype= 'video/x-flv'; break;
        // images
				case 'png': $ctype= 'image/png'; break;
				case 'jpe': $ctype= 'image/jpeg'; break;
				case 'jpeg': $ctype= 'image/jpeg'; break;
				case 'jpg': $ctype= 'image/jpeg'; break;
				case 'gif': $ctype= 'image/gif'; break;
				case 'bmp': $ctype= 'image/bmp'; break;
				case 'ico': $ctype= 'image/vnd.microsoft.icon'; break;
				case 'tiff': $ctype= 'image/tiff'; break;
				case 'tif': $ctype= 'image/tiff'; break;
				case 'svg': $ctype= 'image/svg+xml'; break;
				case 'svgz': $ctype= 'image/svg+xml'; break;
        // archives
				case 'zip': $ctype= 'application/zip'; break;
				case 'rar': $ctype= 'application/x-rar-compressed'; break;
				case 'exe': $ctype= 'application/x-msdownload'; break;
				case 'msi': $ctype= 'application/x-msdownload'; break;
				case 'cab': $ctype= 'application/vnd.ms-cab-compressed'; break;
        // audio/video
				case 'mp3': $ctype= 'audio/mpeg'; break;
				case 'qt': $ctype= 'video/quicktime'; break;
				case 'mov': $ctype= 'video/quicktime'; break;
        // adobe
				case 'pdf': $ctype= 'application/pdf'; break;
				case 'psd': $ctype= 'image/vnd.adobe.photoshop'; break;
				case 'ai': $ctype= 'application/postscript'; break;
				case 'eps': $ctype= 'application/postscript'; break;
				case 'iso': $ctype= 'application/iso'; break;
        // ms office
				case 'doc': $ctype= 'application/msword'; break;
				case 'rtf': $ctype= 'application/rtf'; break;
				case 'xls': $ctype= 'application/vnd.ms-excel'; break;
				case 'ppt': $ctype= 'application/vnd.ms-powerpoint'; break;
				case 'xlsx': $ctype= 'application/xlsx'; break;
				case 'pptx': $ctype= 'application/pptx'; break;
				case 'docx': $ctype= 'application/docx'; break;
				case 'csv': $ctype= 'application/csv'; break;
       // open office
				case 'odt': $ctype= 'application/vnd.oasis.opendocument.text'; break;
				case 'ods': $ctype= 'application/vnd.oasis.opendocument.spreadsheet'; break;

				default: $ctype= "application/force-download";
		}

		header("Content-Type: $ctype; charset=UTF-8");
		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); // required for certain browsers

		if ( $file_extension == 'pdf' ){
			header("Content-Disposition: inline; filename=".$just_file_name);
		} else {
			header("Content-Disposition: attachment; filename=".$just_file_name);
		}

		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($filename));
		readfile( "".$filename."" );
		exit();
	}
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!