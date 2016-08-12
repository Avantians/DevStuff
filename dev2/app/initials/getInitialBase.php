<?php
namespace ElasticActs\App\initials;

/**
* Base Initialzation
*/
abstract class getInitialBase
{
    protected function setInitialBase()
    {
        // Prevent Robot access for security
        if ( $_SERVER['SERVER_PROTOCOL'] != 'HTTP/1.1' && $_SERVER['SERVER_PROTOCOL'] != 'HTTP/1.0' ){
            // Set a 400 (Bad Request) response code and exit.
            header('HTTP/1.1 400 Bad Request');
        }

        ini_set('output_buffering', 'off');

        // Required for IE, otherwise Content-disposition is ignored
        if ( ini_get('zlib.output_compression') ){
            ini_set('zlib.output_compression', 0);
        }

		// Site With GZIP Compression
		if ( substr_count( getenv('HTTP_ACCEPT_ENCODING' ), 'gzip') ){
            if ( !in_array('ob_gzhandler', ob_list_handlers()) ){
                ob_start('ob_gzhandler');
                ob_implicit_flush(0);
            } else {
                ob_start();
            }
		} else {
		    ob_start();
        }

        $this->catchErrors( 'screen', CONFIG_DCOCUMENT_ROOT );
        $this->setTimeZone( DEFAULT_TIMEZONE );

        // If the short valuable not support
        if ( isset($HTTP_POST_VARS) && ! isset($_POST) ){
        	$_POST   	=& $HTTP_POST_VARS;
        	$_GET    	=& $HTTP_GET_VARS;
        	$_SERVER 	=& $HTTP_SERVER_VARS;
        	$_COOKIE	=& $HTTP_COOKIE_VARS;
        	$_ENV    	=& $HTTP_ENV_VARS;
        	$_FILES  	=& $HTTP_POST_FILES;

        	if ( !isset($_SESSION) ){
        	    $_SESSION 	=& $HTTP_SESSION_VARS;
        	}
        }

        if ( ! ini_get('register_globals') || ! get_magic_quotes_gpc() ){
            @extract($_GET);
            @extract($_POST);
            @extract($_SERVER);
        }

        /**
        * Referral from phpBB2
        * If the value of magic_quotes_gpc is FALSE in php.ini,
        * it will apply addslashes() to protect it from SQL Injection
        */
        if ( ! get_magic_quotes_gpc() ){
            if ( is_array( $_GET ) ){
                while ( list( $k, $v ) = each( $_GET ) ){
                    if ( is_array( $_GET[$k] ) ) {
                        while ( list( $k2, $v2 ) = each( $_GET[$k] ) ){
                            $_GET[$k][$k2] = addslashes( $v2 );
                        }
                        @reset( $_GET[$k] );
                    } else {
                        $_GET[$k] = addslashes( $v );
                    }
                }
                @reset($_GET);
            }

            if ( is_array( $_POST ) ){
                while ( list( $k, $v ) = each( $_POST ) ){
                    if ( is_array( $_POST[$k] ) ){
                        while ( list( $k2, $v2 ) = each( $_POST[$k] ) ){
                            $_POST[$k][$k2] = addslashes( $v2 );
                        }
                        @reset( $_POST[$k] );
                    } else{
                        $_POST[$k] = addslashes( $v );
                    }
                }
                @reset($_POST);
            }

            if ( is_array( $_COOKIE ) ){
                while ( list( $k, $v ) = each( $_COOKIE ) ){
                    if ( is_array( $_COOKIE[$k] ) ){
                        while( list( $k2, $v2 ) = each( $_COOKIE[$k] ) ){
                            $_COOKIE[$k][$k2] = addslashes( $v2 );
                        }
                        @reset( $_COOKIE[$k] );
                    } else {
                        $_COOKIE[$k] = addslashes( $v );
                    }
                }
                @reset($_COOKIE);
            }
        }

        /**
        * extract( $_GET );
        * Protect from page.php?_POST[var1]=data1&_POST[var2]=data2
        * extract( $_GET ); 명령으로 인해 page.php?_POST[var1]=data1&_POST[var2]=data2
        * 와 같은 코드가 _POST 변수로 사용되는 것을 막음
        */
        $ext_arr = array ( 'PHP_SELF', '_ENV', '_GET', '_POST', '_FILES', '_SERVER', '_COOKIE', '_SESSION', '_REQUEST', 'HTTP_ENV_VARS', 'HTTP_GET_VARS', 'HTTP_POST_VARS', 'HTTP_POST_FILES', 'HTTP_SERVER_VARS','HTTP_COOKIE_VARS', 'HTTP_SESSION_VARS', 'GLOBALS' );
        $ext_cnt = count( $ext_arr );
        for ( $i=0; $i<$ext_cnt; $i++ ){
            if ( isset( $_GET[$ext_arr[$i]] ) ){
                unset( $_GET[$ext_arr[$i]] );
            }
        }

        $rg = array_keys( $_REQUEST );
        foreach ( $rg as $var ) {
            if ( $_REQUEST[$var] === $$var ){
                unset( $$var );
            }
        }
    }

    abstract protected function catchErrors( $type, $location );
    abstract protected function setTimeZone( $zone );
}
