<?php
/** -------------------------------------------------------------------------
* @package  ElasticActs CMS
* @author   Kenwoo - iweb@kenwoo.ca
* @license  http://creativecommons.org/licenses/by/4.0/ Creative Commons
*
* Global Configuration
* Setting flag to prevent direct access
* ------------------------------------------------------------------------ */
defined( '_VALID_TAGS' ) or die( 'Your system is not working properly.' );

/** -------------------------------------------------------------------------
* Database configuration section
* ------------------------------------------------------------------------ */
// define( 'DB_SERVER', 'localhost' );		   // This is normally set to localhost
// define( 'DB_USERNAME', 'root' ); 	       // MySQL Username
// define( 'DB_PASSWORD', 'root' );		   // MySQL Password
// define( 'DB_NAME', 'mosaicon_tcc' );	   // MySQL Database name
define( 'DB_CHARSET', 'utf8' );			   // MySQL Database Character set
define( 'WHERE_STORE_SESSIONS', 'mysql' ); // Leave empty  for default handler or set to mysql

$dbParams = array(
  'DB_SERVER'   => 'localhost',   // This is normally set to localhost
  'DB_USERNAME' => 'root',        // MySQL Username
  'DB_PASSWORD' => 'root',        // MySQL Password
  'DB_NAME'     => 'mosaicon_tcc' // MySQL Database name
);
/** -------------------------------------------------------------------------
* Define static folder and static domain
*  ----------------------------------------------------------------------- */
define( 'CONFIG_DESIGN', '../design' );
define( 'CONFIG_STATIC_SUBDOMAIN', 'www' );
define( 'CONFIG_STATIC_SUBFOLDER', '/static' );

/** -------------------------------------------------------------------------
* Basic setup for SUB like /sub-foldername
*
* When you have the package under SUB-FOLDER-NAME, need to define.
* define( UNDER_SUBFOLDER, /sub-foldername );
*
* AND need to change TWO files
* IN /static/filemanager/config/config.php
* $upload_dir = '/sub-foldername/upload/images/';
* $current_path = $_SERVER['DOCUMENT_ROOT'].'/sub-foldername/upload/images/';
*
* IN .htaccess
* RewriteRule ^(.*)$ sub-foldername/index.php [E=CI_PATH:/$1,L]
*  ----------------------------------------------------------------------- */
define( 'UNDER_SUBFOLDER', '' );

define( 'CONFIG_PROTECT_IP_FRONT', false );

define( "DEFAULT_TIMEZONE", "America/Toronto" );
/** --------------------------------------------------------------------
* Basic Directories
* ------------------------------------------------------------------- */
define( 'CONFIG_DCOCUMENT_ROOT', realpath(getenv('DOCUMENT_ROOT')).'/' );
define( 'CONFIG_CONSTANTS', CONFIG_DCOCUMENT_ROOT . 'constants/' );
define( 'CONFIG_CLASSES', CONFIG_CONSTANTS . 'classes/' );
define( 'CONFIG_COMMON', CONFIG_DCOCUMENT_ROOT . 'common/' );
define( 'CONFIG_FILES_UPLOAD_IMAGES_TEMP', CONFIG_FILES_UPLOAD_ROOT . 'temp/' );
define( 'CONFIG_BACKEND', CONFIG_DCOCUMENT_ROOT . 'backend/' );
define( 'CONFIG_BACKEND_CONSTANTS', CONFIG_BACKEND . 'constants/' );
define( 'CONFIG_BACKEND_CLASSES', CONFIG_BACKEND_CONSTANTS . 'classes/' );
/** --------------------------------------------------------------------
* Upload Directories
* ------------------------------------------------------------------- */
define( 'CONFIG_FILES_UPLOAD_ROOT', 'upload/' );
define( 'CONFIG_FILES_UPLOAD_IMAGES', 'images/' );
define( 'CONFIG_FILES_UPLOAD_MUSIC', 'sounds/' );
define( 'CONFIG_FILES_UPLOAD_VOD', 'vods/' );
define( 'CONFIG_FILES_UPLOAD_DOCS', 'docs/' );
/** --------------------------------------------------------------------
* Setting for session
* ------------------------------------------------------------------- */
define( 'SESSION_SAVE_PATH', CONFIG_DCOCUMENT_ROOT . '_data/_session' );
define( 'CONFIG_COOKIE_DOMAIN', '.ea.dev' );
