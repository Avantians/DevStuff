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

$mem = abs(intval(@ini_get('memory_limit')));
if ($mem && $mem < 64) {
	@ini_set('memory_limit', '192M');
}

$time = abs(intval(@ini_get("max_execution_time")));
if ($time != 0 && $time < 120) {
	@set_time_limit(240);
}

ini_set( "output_buffering","on" );
ini_set( "zlib.output_compression", 0 );

if ( substr_count( $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip' ) ){
    ob_start( "ob_gzhandler" );
    ob_implicit_flush(0);
}
else{
    @ob_start();
}

if ( !ini_get( 'register_globals' ) || !get_magic_quotes_gpc() ){
    if( !ini_set( 'session.bug_compat_warn','0' ) )
    ini_set( 'session.bug_compat_42','1' );

    @extract( $_GET );
    @extract( $_POST );
    @extract( $_SERVER );
}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Setting  the level of error reporting // show all types but notices
 *  ----------------------------------------------------------------------- */
ini_set( "display_errors",1 );
ini_set( "log_errors", true );
ini_set( "error_log",  dirname( __FILE__ ) . "/dev_errors.txt" );
error_reporting( E_ALL ^ E_NOTICE );

/** -------------------------------------------------------------------------
 * [00/00/2011]::Check support for register_globals
 *  ----------------------------------------------------------------------- */
$wrongSettingsTexts = array();
if ( function_exists( 'ini_get' ) && ( ini_get( 'register_globals' ) == false ) && ( PHP_VERSION < 4.3 ) ){
    exit( 'Server Requirement Error: register_globals is disabled in your PHP configuration. This can be enabled in your php.ini configuration file or in the .htaccess file in your catalog directory. Please use PHP 4.3+ if register_globals cannot be enabled on the server.' );
}

if ( PHP_VERSION < 5.3 ){
    if ( ini_get( 'magic_quotes_gpc' ) != '1' ){
        $wrongSettingsTexts[] = 'PHP magic_quotes_gpc setting is `OFF` instead of `ON`';
    }
}

if ( ini_get( 'register_globals' ) == '1' ){
    $wrongSettingsTexts[] = 'PHP register_globals setting is `ON` instead of `OFF`';
}

if ( RG_EMULATION !=0 ){
    $wrongSettingsTexts[] = '&quot;Register Globals Emulation&quot; setting is `ON`. &nbsp; To disable Register Globals Emulation, navigate to Site -> Global Configuration -> Server, select `OFF`, and save.<br /><span style="font-weight: normal; font-style: italic; color: #666;">Register Globals Emulation is `ON` by default for backward compatibility.</span>';
}

if ( count( $wrongSettingsTexts ) ){
    echo '<div style="clear: both; margin: 10px auto 3px auto; padding: 5px 15px; display: block; float: left; border: 1px solid #cc0000; background: #ffffcc; text-align: left; width: 90%;">
							<p style="color: #CC0000;">Following PHP Server Settings are not optional for <strong>Security</strong> and it is recommended to change them:</p>
							<ul style="margin: 0px; padding: 0px; padding-left: 15px; list-style: none;" >';
		foreach ( $wrongSettingsTexts as $txt ){
				echo '<li style="min-height: 25px; padding-bottom: 5px; padding-left: 25px; color: red; font-weight: bold; background-image: url( ../includes/js/ThemeOffice/warning.png ); background-repeat: no-repeat; background-position: 0px 2px;" >';
				echo $txt;
				echo '</li>';
		}
    echo '</ul>';
    echo '</div>';
}

/** -------------------------------------------------------------------------
 * [00/00/2011]::If the short valuable not support
 *  ----------------------------------------------------------------------- */
if ( isset( $HTTP_POST_VARS ) && !isset( $_POST ) ){
    $_POST   		= &$HTTP_POST_VARS;
    $_GET    		= &$HTTP_GET_VARS;
    $_SERVER 	= &$HTTP_SERVER_VARS;
    $_COOKIE	= &$HTTP_COOKIE_VARS;
    $_ENV    		= &$HTTP_ENV_VARS;
    $_FILES  		= &$HTTP_POST_FILES;

  if ( !isset( $_SESSION ) ){
        $_SESSION 	= &$HTTP_SESSION_VARS;
    }
}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Set  Default Timezone
 *  ----------------------------------------------------------------------- */
if ( function_exists( "date_default_timezone_set" ) ){
    date_default_timezone_set( "America/Toronto" );
}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Referral from phpBB2
 * If the value of magic_quotes_gpc is FALSE in php.ini, it will apply addslashes() to protect it from SQL Injection
 *  ----------------------------------------------------------------------- */
if ( !get_magic_quotes_gpc() ){
    if ( is_array( $_GET ) ){
        while ( list( $k, $v ) = each( $_GET ) ){
            if ( is_array( $_GET[$k] ) ){
                while ( list( $k2, $v2 ) = each( $_GET[$k] ) ){
                    $_GET[$k][$k2] = addslashes( $v2 );
                }
                @reset( $_GET[$k] );
            }
            else {
                $_GET[$k] = addslashes( $v );
            }
        }
        @reset( $_GET );
    }

    if ( is_array( $_POST ) ){
        while ( list( $k, $v ) = each( $_POST ) ){
            if ( is_array( $_POST[$k] ) ){
                while ( list( $k2, $v2 ) = each( $_POST[$k] ) ){
                    $_POST[$k][$k2] = addslashes( $v2 );
                }
                @reset( $_POST[$k] );
            }
            else{
                $_POST[$k] = addslashes( $v );
            }
        }
        @reset( $_POST );
    }

    if ( is_array( $_COOKIE ) ){
        while ( list( $k, $v ) = each( $_COOKIE ) ){
            if ( is_array( $_COOKIE[$k] ) ){
                while( list( $k2, $v2 ) = each( $_COOKIE[$k] ) ){
                    $_COOKIE[$k][$k2] = addslashes( $v2 );
                }
                @reset( $_COOKIE[$k] );
            }
            else{
                $_COOKIE[$k] = addslashes( $v );
            }
        }
        @reset( $_COOKIE );
    }
}

/** -------------------------------------------------------------------------
 * [00/00/2011]::extract( $_GET );
 * Protect from page.php?_POST[var1]=data1&_POST[var2]=data2
 * extract( $_GET ); 명령으로 인해 page.php?_POST[var1]=data1&_POST[var2]=data2 와 같은 코드가 _POST 변수로 사용되는 것을 막음
 *  ------------------------------------------------------------------------- */
$ext_arr = array ( 'PHP_SELF', '_ENV', '_GET', '_POST', '_FILES', '_SERVER', '_COOKIE', '_SESSION', '_REQUEST', 'HTTP_ENV_VARS', 'HTTP_GET_VARS', 'HTTP_POST_VARS', 'HTTP_POST_FILES', 'HTTP_SERVER_VARS','HTTP_COOKIE_VARS', 'HTTP_SESSION_VARS', 'GLOBALS' );
$ext_cnt = count( $ext_arr );
for ( $i=0; $i<$ext_cnt; $i++ ){
    if ( isset( $_GET[$ext_arr[$i]] ) ) unset( $_GET[$ext_arr[$i]] );
}

$rg = array_keys( $_REQUEST );
foreach ( $rg as $var ){
    if ( $_REQUEST[$var] === $$var ){
        unset( $$var );
    }
}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Checking register_globlas
 *  ------------------------------------------------------------------------- */
if ( ini_get( 'register_globals' ) == 0 ){
    // php.ini has register_globals = off and emulate = on
    registerGlobals();
}
else {
    // php.ini has register_globals = on and emulate = on
    // just check for spoofing
    checkInputArray( $_FILES );
    checkInputArray( $_ENV );
    checkInputArray( $_GET );
    checkInputArray( $_POST );
    checkInputArray( $_COOKIE );
    checkInputArray( $_SERVER );

    if( isset( $_SESSION ) ){
        checkInputArray( $_SESSION );
    }
}

$_GET 					= array_map( 'stripslashes_deep', $_GET );
$_POST 				= array_map( 'stripslashes_deep', $_POST );
$_COOKIE 	= array_map( 'stripslashes_deep', $_COOKIE );
$_REQUEST	= array_map( 'stripslashes_deep', $_REQUEST );

/** -------------------------------------------------------------------------
 * [00/00/2011]::Current Domain
 *  ------------------------------------------------------------------------- */
$CURRENT_DOMAIN = getenv( "HTTP_HOST" );

/** -------------------------------------------------------------------------
 * [00/00/2011]::Getting php_self in the local scope - $PHP_SELF & Set Self Folder & PHP Version
 *  ------------------------------------------------------------------------- */
if (!isset( $PHP_SELF )){
 $PHP_SELF = setSelf();
}
$SELF_FOLDER	= rtrim( dirname( getenv( "PHP_SELF" ) ), '/\\' ) . '/';
$PHPVERSION		= phpversion();

/** -------------------------------------------------------------------------
 * [00/00/2011]::Reliably set PHP_SELF as a filename .. platform safe
 * PHP_SELF is unreliable therefore this does check it as such.
 *  ------------------------------------------------------------------------- */
function setSelf(){
    $base = ( array( 'SCRIPT_NAME', 'PHP_SELF' ) );
    foreach ( $base as $index => $key ){
        if ( array_key_exists(  $key, $_SERVER ) && !empty(  $_SERVER[$key] ) ){
            if ( false !== strpos( $_SERVER[$key], '.php' ) ){
                preg_match( '@[a-z0-9_]+\.php@i', $_SERVER[$key], $matches );
                if ( is_array( $matches ) && ( array_key_exists( 0, $matches ) ) && ( substr( $matches[0], -4, 4 ) == '.php' ) && ( is_readable( $matches[0] ) ) ){
                    return $matches[0];
                }
            }
        }
    }

    return 'index.php';
}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Adds an array to the GLOBALS array and checks that the GLOBALS variable is not being attacked
 *  ------------------------------------------------------------------------- */
function checkInputArray( &$array, $globalise=false ){
    static $banned = array( '_files', '_env', '_get', '_post', '_cookie', '_server', '_session', 'globals' );

    foreach ( $array as $key => $value ){
        $intval = intval( $key );
        // PHP GLOBALS injection bug
        $failed = in_array( strtolower( $key ), $banned );
        // PHP Zend_Hash_Del_Key_Or_Index bug
        $failed |= is_numeric( $key );
        if ( $failed ){
            die( 'Illegal variable <b>' . implode( '</b> or <b>', $banned ) . '</b> passed to script.' );
        }

        if ( $globalise ){
            $GLOBALS[$key] = $value;
        }
    }
}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Emulates register globals = off
 *  ------------------------------------------------------------------------- */
function unregisterGlobals (){
    checkInputArray( $_FILES );
    checkInputArray( $_ENV );
    checkInputArray( $_GET );
    checkInputArray( $_POST );
    checkInputArray( $_COOKIE );
    checkInputArray( $_SERVER );

    if ( isset( $_SESSION ) ){
        checkInputArray( $_SESSION );
    }

    $REQUEST	= $_REQUEST;
    $GET 		= $_GET;
    $POST 		= $_POST;
    $COOKIE 	= $_COOKIE;
    if ( isset ( $_SESSION ) ){
        $SESSION = $_SESSION;
    }

    $FILES 	= $_FILES;
    $ENV	= $_ENV;
    $SERVER	= $_SERVER;

    foreach ( $GLOBALS as $key => $value ){
        if ( $key != 'GLOBALS' ){
            unset ( $GLOBALS [ $key ] );
        }
    }

    $_REQUEST	= $REQUEST;
    $_GET 		= $GET;
    $_POST		= $POST;
    $_COOKIE	= $COOKIE;
    if ( isset ( $SESSION ) ){
        $_SESSION = $SESSION;
    }

    $_FILES		= $FILES;
    $_ENV		= $ENV;
    $_SERVER	= $SERVER;
}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Emulates register globals = on
 *  ------------------------------------------------------------------------- */
function registerGlobals(){
    checkInputArray( $_FILES, true );
    checkInputArray( $_ENV, true );
    checkInputArray( $_GET, true );
    checkInputArray( $_POST, true );
    checkInputArray( $_COOKIE, true );
    checkInputArray( $_SERVER, true );

    if ( isset( $_SESSION ) ){
        checkInputArray( $_SESSION, true );
    }

    foreach ( $_FILES as $key => $value ){
        $GLOBALS[$key] = $_FILES[$key]['tmp_name'];
        foreach ( $value as $ext => $value2 ){
            $key2 			= $key . '_' . $ext;
            $GLOBALS[$key2] = $value2;
        }
    }
}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Why does my HTML output include lots of \" like <a href=\"mylink.htm\">link</a>
 * This is probably because you are using PHP and it has a feature that's called magic quotes that is enabled by default.
 * Please add the code below to remove out \
 *  ------------------------------------------------------------------------- */
function stripslashes_deep( $value ){
    $value = is_array( $value ) ? array_map( 'stripslashes_deep', $value ) : stripslashes( $value );
    return $value;
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!
