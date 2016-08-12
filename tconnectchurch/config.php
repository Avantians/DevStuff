<?php
/** -------------------------------------------------------------------------
 * This program is Open Source; you can redistribute it
 * and/or modify it under the terms of the GNU General Public License
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY
 * @package  CMS
 * @author      Kenwoo - iweb@kenwoo.ca
 * @license    http://creativecommons.org/licenses/by/4.0/ Creative Commons
 *
 * [v02-02/07/2011]:: Set flag which is not allow no direct access
 *  ----------------------------------------------------------------------- */
defined( "_VALID_MOS" ) or die( "Your system is not working properly." );

/** -------------------------------------------------------------------------
 * [00/00/2011]::Database configuration section
 *  ----------------------------------------------------------------------- */
define( "DB_SERVER", "localhost" );						// This is normally set to localhost
define( "DB_USERNAME", "mosaicon_dbuser" ); 	// MySQL Username
define( "DB_PASSWORD", "FioADO1kvuQo" );		// MySQL Password
define( "DB_NAME", "mosaicon_tcc" );						// MySQL Database name
define( "DB_CHARSET", "utf8" );							// MySQL Database Character set
define( "WHERE_STORE_SESSIONS", "mysql" );   // Leave empty "" for default handler or set to "mysql"

/** -------------------------------------------------------------------------
 * [03/01/2014]::Basic setup for SUB like /sub-foldername
 * NEED TO change
 *  
 * define( "UNDER_SUBFOLDER", "/sub-foldername" );
 * 
 * /static/filemanager/config/config.php
 * $upload_dir = '/sub-foldername/upload/images/'; 
 * $current_path = $_SERVER['DOCUMENT_ROOT'].'/sub-foldername/upload/images/'; 
 * 
 * .htaccess
 * RewriteRule ^(.*)$ sub-foldername/index.php [E=CI_PATH:/$1,L]
 *  ----------------------------------------------------------------------- */
define( "UNDER_SUBFOLDER", "/tconnectchurch" );
define( "CONFIG_DESIGN", "../design" );
define( "CONFIG_STATIC_SUBDOMAIN", "www" );
define( "CONFIG_STATIC_SUBFOLDER", "/static" );
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!
