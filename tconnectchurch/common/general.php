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

/** -------------------------------------------------------------------------
 * [00/00/2014]::Required major files
 *  ----------------------------------------------------------------------- */
function allMajorFiles( $module_directory ){
	global $PHP_SELF;

	$file_extension		= substr( $PHP_SELF, strrpos( $PHP_SELF, '.' ) );
	$directory_array	= array();

	if ( $dir = @dir( $module_directory ) ){
		while ( $file = $dir->read() ){
			if ( !is_dir( $module_directory . $file ) ){
				if ( substr( $file, strrpos( $file, '.' ) ) == $file_extension ){
					$directory_array[] = $file;
				}
			}
		}
		sort( $directory_array );
		$dir->close();
	}

	for ( $i=0, $n=sizeof( $directory_array ); $i<$n; $i++ ){
		$file = $directory_array[$i];
		if( $file != "index.php" ){
			include( $module_directory . $file );
		}
	}

	include_once (CONFIG_DOC_ROOT.'/common/classes/getEmail.php');
	include_once (CONFIG_DOC_ROOT.'/common/classes/getMime.php');
}

/** -------------------------------------------------------------------------
 * [03/12/2013]: Required Design files
 *  ----------------------------------------------------------------------- */
function allDesign( $design_directory ){
	global $PHP_SELF;

	$file_extension		= substr( $PHP_SELF, strrpos( $PHP_SELF, '.' ) );
	$directory_array	= array();

	if( $dir = @dir( $design_directory ) ){
		while( $file = $dir->read() ){
			if ( !is_dir( $design_directory . $file ) && ( !preg_match("/^[_]/", $file) ) && ( $file != "index.php" && $file != "." && $file != ".." ) ){
					$directory_array[] = array( 'id' => $file, 'text' => strtoupper( $file ) );
			}
		}
		sort( $directory_array );
		$dir->close();
	}

	return $directory_array;
}

/** -------------------------------------------------------------------------
 * [02/14/2013]::Create Cookie
 *  ----------------------------------------------------------------------- */
function set_cookie( $cookie_name, $value, $expire ){
    setcookie( md5( $cookie_name ), base64_encode( $value ), time() + $expire, '/', CONFIG_COOKIE_DOMAIN );
}

/** -------------------------------------------------------------------------
 * [02/14/2013]::Delete Cookie
 *  ----------------------------------------------------------------------- */
function unset_cookie( $cookie_name, $expire ){
    setcookie( md5( $cookie_name ), "", time() - $expire, '/', CONFIG_COOKIE_DOMAIN );
}

/** -------------------------------------------------------------------------
 * [02/14/2013]:: get Cookie
 *  ----------------------------------------------------------------------- */
function get_cookie( $cookie_name ){
    return base64_decode( $_COOKIE[md5( $cookie_name )] );
}

/** -------------------------------------------------------------------------
 * [00/00/2009]::Check IP address to allow access
 *  ----------------------------------------------------------------------- */
function _getAccessbyIP( $user_ip ){
	global $Config_allowedIPs;

	$kw = 0;
	$kw_ip_count = 0;
	while( $kw  < count( $Config_allowedIPs ) ){
		if ( preg_match( "/".$Config_allowedIPs[$kw]."/", $user_ip ) ){
			$kw_ip_count++;
		}
		$kw++;
	}

	if ( $kw_ip_count <= 0 ){
		return false;
	}
	else {
		return true;
	}
}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To
 *  ----------------------------------------------------------------------- */
function _getSanitize( $input, $type = "string" ){
    if ( is_array( $input ) ) {
        foreach ( $input as $var=>$val ) {
		  $output[$var] = _getSanitize( $val );
        }
		unset( $input );
    } else {
    	if ( $type === "none" ) {
    		$input = _getCleanInput_nojave( $input );
    	} elseif ( $type === "html" ) {
    		$input = _getKeepHtml( $input );
    		$input = _getCleanInput_nojave( $input );
    	} elseif ( $type === "quot" ) {
    		$input = _getKeepHtml( $input );
    		$input = _getMultiLineCleanInput( $input );
    		$input = str_replace( '"', "&quot;", trim( $input ) );
    		$input = str_replace( "=", "&#61;", trim( $input ) );
    		$input = str_replace( "?", "&#63;", trim( $input ) );
    	} elseif ( $type === "simple" ) {
    		$input = str_replace( "<p>&nbsp;</p>", "", trim( $input ) );
    		$input = _getKeepHtml( $input );
    		$input = _getCleanInput( $input );
    	} elseif ( $type === "simple_nojava" ) {
    		$input = _getKeepHtml( $input );
    		$input = _getCleanInput_nojave( $input );
    	} else {
    		$input = _getBasicClean( $input );
    	}
    	$output = ( $input );
    }

    return $output;
}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To
 *  ----------------------------------------------------------------------- */
function _getKeepHtml( $string ){
		$string = str_replace( array( "&lt;", "&gt;" ), array( "<", ">" ), htmlentities( $string, ENT_NOQUOTES, "UTF-8" ) );

		if ( strpos( $string, '&amp;#' ) !== false ){
			$string = preg_replace( '/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string );
		}
		$string = preg_replace( '/&amp;/', "&", $string );

		return $string;
}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To
 *  ----------------------------------------------------------------------- */
function _getJavaCleanInput( $string ){
	$search	= array( '@<script[^>]*?>.*?</script>@si' );
	$string	= preg_replace( $search, '', $string );

	return $string;
}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Remove Multi lin tags
 *  ----------------------------------------------------------------------- */
function _getMultiLineCleanInput( $string ){
    $in[] 	= '@<![\s\S]*?--[ \t\n\r]*>@i'; $out[] = "";	#Strip multi-line comments
    $string	= preg_replace( $in, $out, $string );

    return $string;
}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Remove Multi lin tags
 *  ----------------------------------------------------------------------- */
function _getHtml2txt($string){
	$search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
	               '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
	               '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
	               '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
	);
	$text = preg_replace($search, '', $string);

	return $text;
}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To
 *  ----------------------------------------------------------------------- */
function _getCleanInput( $string ){
    $in[] = "@<![\s\S]*?--[ \t\n\r]*>@i"; $out[] = "";		#Strip multi-line comments
																								# $in[] = "@\s[\s]+@i"; $out[] = " ";
	$in[] = "@&( – );@i"; 										$out[] = "-";
	$in[] = "/\®/"; 											$out[] = "&#174;";
	$in[] = "/\©/"; 											$out[] = "&#169;";
	$in[] = "/\™/"; 											$out[] = "&trade;";
	$in[] = "/\@/"; 											$out[] = "&#64;";

	$string = preg_replace( $in, $out, trim( $string ) );

	return $string;
}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To
 *  ----------------------------------------------------------------------- */
function _getCleanInput_nojave( $string ){
	$in[] = "@<script[^>]*?>.*?</script>@si";	$out[] = "";	# Strip out javascript
    $in[] = "@<![\s\S]*?--[ \t\n\r]*>@i"; $out[] = "";	# Strip multi-line comments
																							# $in[] = "@\s[\s]+@i"; $out[] = " ";
	$in[] = "@&( – );@i"; 										$out[] = "-";
	$in[] = "/\®/"; 											$out[] = "&#174;";
	$in[] = "/\©/"; 											$out[] = "&#169;";
	$in[] = "/\™/"; 											$out[] = "&trade;";
	$in[] = "/\@/"; 											$out[] = "&#64;";

	$string = preg_replace( $in, $out, trim( $string ) );

	return $string;
}

/** -------------------------------------------------------------------------
 * [00/00/2011]::From http://svn.bitflux.ch/repos/public/popoon/trunk/classes/externalinput.php
 * | Author: Christian Stocker <christian.stocker@liip.ch>                |
 *  ----------------------------------------------------------------------- */
	function _getBasicClean( $string ){
		if ( get_magic_quotes_gpc() ){
			$string = stripslashes( $string );
		}

        $string = str_replace( array( "&amp;", "&lt;", "&gt;" ), array( "&amp;amp;", "&amp;lt;", "&amp;gt;" ), $string );
        // Fix &entitiy\n;
        $string = preg_replace( '#(&\#*\w+)[\x00-\x20]+;#u',"$1;",$string );
        $string = preg_replace( '#(&\#x*)([0-9A-F]+);*#iu',"$1$2;",$string );
        $string = html_entity_decode( $string, ENT_COMPAT, "UTF-8" );

        // Remove any attribute starting with "on" or xmlns
        $string = preg_replace( '#( <[^>]+[\x00-\x20\"\'\/] )( on|xmlns )[^>]*>#iUu', "$1>", $string );

        // Remove javascript: and vbscript: protocol
        $string = preg_replace( '#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2nojavascript...', $string );
        $string = preg_replace( '#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2novbscript...', $string );
        $string = preg_replace( '#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/]*-moz-binding[\x00-\x20]*:#Uu', '$1=$2nomozbinding...', $string );
        $string = preg_replace( '#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/]*data[\x00-\x20]*:#Uu', '$1=$2nodata...', $string );
        // Remove any style attributes, IE allows too much stupid things in them, eg.
        // <span style="width: expression( alert( 'Ping!' ) );"></span>
        // and in general you really don't want style declarations in your UGC
        $string = preg_replace( '#(<[^>]+[\x00-\x20\"\'\/])style[^>]*>#iUu', "$1>", $string );
        // Remove namespaced elements ( we do not need them... )
        $string = preg_replace( '#</*\w+:\w[^>]*>#i', "", $string );
        // Remove really unwanted tags
        do {
            $oldstring = $string;
            $string = preg_replace( '#</*(applet|meta|xml|blink|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>#i',"",$string );
        } while ( $oldstring != $string );

        return $string;
    }

/** -------------------------------------------------------------------------
* [06/05/2013]::Moved
 *  ----------------------------------------------------------------------- */
function _getXmlEntities( $string ){
	$string = html_entity_decode(stripslashes($string), ENT_QUOTES, 'UTF-8');
	//replace numeric entities
	$string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
	$string = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $string);

	return $string;
}

/** -------------------------------------------------------------------------
 * [03/30/2009]::Moved
 *  ----------------------------------------------------------------------- */
function _getIspublicEmail( $email ){
	global $freeEmailList;

	$email_com = explode( '@', $email );
	if ( in_array( $email_com[1], $freeEmailList ) ){
		return false;
	}
	else {
		$email_com_name = explode( '.', $email_com[1] );
		if ( in_array( $email_com_name[0], $freeEmailList ) ){
			return false;
		}
		else {
			return true;
		}
	}
}

/** -------------------------------------------------------------------------
 * [00/00/2013]::To change special code to symbol
 *  ----------------------------------------------------------------------- */
function _getActualEmail( $emailaddress ){
	$ActualEmail = str_replace( "&Dagger;", ".", trim( $emailaddress ) );
	$ActualEmail = str_replace( "&#64;", "@", trim( $ActualEmail ) );

	return $ActualEmail;
}

/** -------------------------------------------------------------------------
 * [00/00/2013]::To check alive email by domain
 *  ----------------------------------------------------------------------- */
function _getCheckDNSRR( $email ){
	$recType = '';

	list( $userName, $hostName ) = explode( "@", _getActualEmail( $email ) );
	if( !empty( $hostName ) ){

		if (checkdnsrr($hostName , "MX")){
			return true;
		}
		else {
			return false;
		}
	}

	return false;
}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Email Address Validation
 * From http://www.ip2location.net/faqs-ip-country-simple.aspx
 *  ----------------------------------------------------------------------- */
function _getCheckEmail( $email, $publicemail = "no", $dnsrremail = "no" ){

	if( $publicemail === "yes" ){
		return _getIspublicEmail( $email );
	}

	//Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
	if( !preg_match( "/^[^@]{1,64}@[^@]{1,255}$/", $email ) ){
		return false;
	}

	//Split it into sections to make life easier
	$email_array	= explode( "@", $email );
	$local_array		= explode( ".", $email_array[0] );
	for ( $i = 0; $i < sizeof( $local_array ); $i++ ){
		if ( !preg_match( "/^(([A-Za-z0-9!#$%&'*+\/=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[$i] ) ){
			return false;
		}
	}

	//Check if domain is IP. If not, it should be valid domain name
	if ( !preg_match( "/^\[?[0-9\.]+\]?$/", $email_array[1] ) ){
		$domain_array = explode( ".", $email_array[1] );

		//Not enough parts to domain
		if ( sizeof( $domain_array ) < 2 ){
			return false;
		}

		for ( $i = 0; $i < sizeof( $domain_array ); $i++ ){
			if ( !preg_match( "/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/", $domain_array[$i] ) ){
				return false;
			}
		}
	}

	if ( $dnsrremail === "yes" ){
		return _getCheckDNSRR( $email );
	}

	return true;
}

/** -------------------------------------------------------------------------
 * [00/00/2009]::nl2br() prior PHP 4.2.0 did not convert linefeeds on all OSs ( it only converted \n )
 *  ----------------------------------------------------------------------- */
function _getConvertLinefeeds( $from, $to, $string ){
	if ( ( PHP_VERSION < "4.0.5" ) && is_array( $from ) ){
		return preg_replace( '/( ' . implode( '|', $from ) . ' )/', $to, $string );
	}
	else {
		return str_replace( $from, $to, $string );
	}
}

/** -------------------------------------------------------------------------
 * [00/00/2009]::Checking the value is null or not
 *  ----------------------------------------------------------------------- */
function _getCheckNullorNot( $value ){
	if ( is_array( $value ) ){
		if ( sizeof( $value ) > 0 ){
			return true;
		}
		else {
			return false;
		}
	}
	else {
		if ( ( is_string( $value ) || is_int( $value ) ) && ( $value != '' ) && ( $value != 'NULL' ) && ( strlen( trim( $value ) ) > 0 ) ){
			return true;
		}
		else {
			return false;
		}
	}
}

/** -------------------------------------------------------------------------
 * [00/00/2009]::Changed getValidatePassword to getValidatePW
 * This function validates a plain text password with an encrpyted password
 * @param string $plain
 * @param stirng $encrypted
 * @return  bloon
 *  ----------------------------------------------------------------------- */
function _getValidatePassword( $plain, $encrypted ){
	if ( _getCheckNullorNot( $plain ) && _getCheckNullorNot( $encrypted ) ){
		// split apart the hash / salt
		$stack = explode( ':', $encrypted );
		if( sizeof( $stack ) != 2 ) return false;

		if ( md5( $stack[1] . $plain ) == $stack[0] ){
			return true;
		}
	}

	return false;
}

/** -------------------------------------------------------------------------
* [03/30/2009]::Check password - 7 to 12  /  at least one CAPS / one letter / one number
 *  ------------------------------------------------------------------------- */
function _getCheckPassword( $password ){
	if ( !preg_match( "#.*^(?=.{7,12})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$#", $password ) ){
		return false;
	}
	else {
		return true;
	}
}

/** -------------------------------------------------------------------------
 * [00/00/2009]::To make Password / Encrypt Password
 *  ----------------------------------------------------------------------- */
function _getMakePassword( $length=12 ){
	$salt 			= "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$makepass	= '';
	mt_srand( 10000000*( double )microtime() );
	for ( $i = 0; $i < $length; $i++ ){
		$makepass .= $salt[mt_rand( 0,61 )];
	}

	return $makepass;
}

/** -------------------------------------------------------------------------
* [03/30/2009]::
 *  ----------------------------------------------------------------------- */
function _getEncryptPassword( $plain ){
	$password = '';
	for ( $i=0; $i<10; $i++ ){
		$password .= _getRand();
	}
	$salt = substr( md5( $password ), 0, 2 );
	$password = md5( $salt . $plain ) . ':' . $salt;

	return $password;
}

/** -------------------------------------------------------------------------
 * [00/00/2009]::Return a random value
 *  ----------------------------------------------------------------------- */
function _getRand( $min = null, $max = null ){
	static $seeded;

	if ( !isset( $seeded ) ){
		mt_srand( ( double )microtime()*1000000 );
		$seeded = true;
	}

	if ( isset( $min ) && isset( $max ) ){
		if ( $min >= $max ){
			return $min;
		}
		else {
			return mt_rand( $min, $max );
		}
	}
	else {
		return mt_rand();
	}
}

/** -------------------------------------------------------------------------
 * [00/00/2009]::Redirect to another page or site
 * @param string $url
 * @param string $msg
 *  ----------------------------------------------------------------------- */
function _getRedirect( $url, $msg='' ){
	if ( ( CONFIG_ENABLE_SSL === "true" ) && ( getenv( 'HTTPS' ) == 'on' ) ){ 			// We are loading an SSL page
		if ( substr( $url, 0, strlen( CONFIG_SITE_URL ) ) == CONFIG_SITE_URL ){ 			// NONSSL url
			$url = CONFIG_SITE_URL_SSL . substr( $url, strlen( CONFIG_SITE_URL ) );	// Change it to SSL
		}
	}

	if ( trim( $msg ) ){
		$url .= '&mg='.urlencode( $msg );
	}

	if ( headers_sent() ){
		echo "<script>document.location.href='{$url}';</script>\n";
	}
	else {
		@ob_end_clean(); 	//Clear output buffer
		header( 'HTTP/1.1 301 Moved Permanently' );
		header( "Location: ". $url );
	}
	// Set a 400 (bad request) response code and exit.
	http_response_code(400);
	exit();
}

/** -------------------------------------------------------------------------
 * [00/00/2009]:: Getting IP
 * @package		CodeIgniter
 * @link		http://codeigniter.com
 *  ----------------------------------------------------------------------- */
function _getIP(){
	$_IP = FALSE;
	if ( $_IP !== FALSE ){
		return $_IP;
	}

	$cip	= ( isset( $_SERVER['HTTP_CLIENT_IP'] ) AND $_SERVER['HTTP_CLIENT_IP'] != "" ) ? $_SERVER['HTTP_CLIENT_IP'] : FALSE;
	$rip	= ( isset( $_SERVER['REMOTE_ADDR'] ) AND $_SERVER['REMOTE_ADDR'] != "" ) ? $_SERVER['REMOTE_ADDR'] : FALSE;
	$fip	= ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) AND $_SERVER['HTTP_X_FORWARDED_FOR'] != "" ) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : FALSE;

	if ( $cip && $rip )	$_IP = $cip;
	elseif ( $rip )		$_IP = $rip;
	elseif ( $cip )		$_IP = $cip;
	elseif ( $fip )			$_IP = $fip;

	if ( strstr( $_IP, ',' ) ){
		$x	= explode( ',', $_IP );
		$_IP	= end( $x );
	}

	if ( ! preg_match( "/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $_IP ) ){
		$_IP = '0.0.0.0';
	}
	unset( $cip );
	unset( $rip );
	unset( $fip );

	return $_IP;
}

/** -------------------------------------------------------------------------
 * [00/00/2009]::To define a language from browser language
 * Specified by the user via the browser's Accept Language setting
 * Samples: "hu, en-us;q=0.66, en;q=0.33", "hu,en-us;q=0.5" 	UD.02132014
 *  ----------------------------------------------------------------------- */
function _getBrowserLanguage(){
	$browser_langs = array();

	if ( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ){
		$browser_accept = explode( ",", $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
		for ( $i = 0; $i < count( $browser_accept ); $i++ ){
			//The language part is either a code or a code with a quality.
			//We cannot do anything with a * code, so it is skipped.
			//If the quality is missing, it is assumed to be 1 according to the RFC.
			if ( preg_match( "!([a-z-]+)(;q=([0-9\\.]+))?!", trim($browser_accept[$i]), $found ) ){
				$browser_langs[$i] = $found[1];
				#$browser_langs[$found[1]] = ( isset( $found[3] ) ? ( float ) $found[3] : 1.0 );
			}
		}
	}
	//Order the codes by quality
	arsort( $browser_langs );

	return $browser_langs;
}

/** -------------------------------------------------------------------------
 * [02/14/2011]::Getting Browser
 *  ----------------------------------------------------------------------- */
function _getBrowserType( $agent ){
		$agent = strtolower( $agent );

		if ( preg_match( "/msie 5.0[0-9]*/", $agent ) )				{ $solo = "MSIE 5.0"; }
		elseif( preg_match( "/msie 5.5[0-9]*/", $agent ) )		{ $solo = "MSIE 5.5"; }
		elseif( preg_match( "/msie 6.0[0-9]*/", $agent ) )		{ $solo = "MSIE 6.0"; }
		elseif( preg_match( "/msie 7.0[0-9]*/", $agent ) )		{ $solo = "MSIE 7.0"; }
		elseif( preg_match( "/msie 8.0[0-9]*/", $agent ) )		{ $solo = "MSIE 8.0"; }
		elseif( preg_match( "/msie 4.[0-9]*/", $agent ) )		{ $solo = "MSIE 4.x"; }
		elseif( preg_match( "/firefox/", $agent ) )					{ $solo = "FireFox"; }
		elseif( preg_match( "/chrome/", $agent ) )					{ $solo = "Chrome"; }
		elseif( preg_match( "/x11/", $agent ) )						{ $solo = "Netscape"; }
		elseif( preg_match( "/opera/", $agent ) )					{ $solo = "Opera"; }
		elseif( preg_match( "/gec/", $agent ) )						{ $solo = "Gecko"; }
		elseif( preg_match( "/bot|slurp/", $agent ) )				{ $solo = "Robot"; }
		elseif( preg_match( "/internet explorer/", $agent ) )	{ $solo = "IE"; }
		elseif( preg_match( "/mozilla/", $agent ) )					{ $solo = "Mozilla"; }
		else 																			{ $solo = "etc - Unknown"; }

		return $solo;
}

/** -------------------------------------------------------------------------
 * [02/14/2011]::Getting OS
 *  ------------------------------------------------------------------------- */
 function _getOSType( $agent ){
    $agent = strtolower( $agent );

    if ( preg_match( "/windows 98/", $agent ) )							{ $solo = "98"; }
    elseif( preg_match( "/windows 95/", $agent ) )					{ $solo = "95"; }
    elseif( preg_match( "/windows nt 4\.[0-9]*/", $agent ) )	{ $solo = "NT"; }
    elseif( preg_match( "/windows nt 5\.0/", $agent ) )			{ $solo = "2000"; }
    elseif( preg_match( "/windows nt 5\.1/", $agent ) )			{ $solo = "XP"; }
    elseif( preg_match( "/windows nt 5\.2/", $agent ) )			{ $solo = "2003"; }
    elseif( preg_match( "/windows nt 6\.0/", $agent ) )			{ $solo = "Vista"; }
    elseif( preg_match( "/windows nt 6\.1/", $agent ) )			{ $solo = "Windows7"; }
    elseif( preg_match( "/windows 9x/", $agent ) )					{ $solo = "ME"; }
    elseif( preg_match( "/windows ce/", $agent ) )					{ $solo = "CE"; }
    elseif( preg_match( "/mac/", $agent ) )								{ $solo = "MAC"; }
    elseif( preg_match( "/linux/", $agent ) )								{ $solo = "Linux"; }
    elseif( preg_match( "/sunos/", $agent ) )							{ $solo = "sunOS"; }
    elseif( preg_match( "/irix/", $agent ) )									{ $solo = "IRIX"; }
    elseif( preg_match( "/phone/", $agent ) )							{ $solo = "Phone"; }
    elseif( preg_match( "/bot|slurp/", $agent ) )						{ $solo = "Robot"; }
    elseif( preg_match( "/internet explorer/", $agent ) )			{ $solo = "IE"; }
    elseif( preg_match( "/mozilla/", $agent ) )							{ $solo = "Mozilla"; }
    else 																					{ $solo = "etc - Unknown"; }

    return $solo;
}

/** -------------------------------------------------------------------------
 * [02/14/2011]::Getting OS
 *  ----------------------------------------------------------------------- */
function _getISmobile(){
	static $is_mobile;

	if ( isset( $is_mobile ) ){
		return $is_mobile;
	}

	if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ){
		$is_mobile = false;
	}
	elseif ( strpos( $_SERVER['HTTP_USER_AGENT'], 'Mobile' ) !== false // many mobile devices ( all iPhone, iPad, etc. )
		|| strpos( $_SERVER['HTTP_USER_AGENT'], 'Android' ) !== false
		|| strpos( $_SERVER['HTTP_USER_AGENT'], 'Silk/' ) !== false
		|| strpos( $_SERVER['HTTP_USER_AGENT'], 'Kindle' ) !== false
		|| strpos( $_SERVER['HTTP_USER_AGENT'], 'BlackBerry' ) !== false
		|| strpos( $_SERVER['HTTP_USER_AGENT'], 'Opera Mini' ) !== false ){

		$is_mobile = true;
	}
	else {
		$is_mobile = false;
	}

	return $is_mobile;
}

/** -------------------------------------------------------------------------
 * [02/11/2009]::Added to convert a IP Address to a IP Number
 * From http://www.ip2location.net/faqs-ip-country-simple.aspx
 *  ----------------------------------------------------------------------- */
function _getDot2LongIP ( $IPaddr ){
    if ( $IPaddr == "" ){
        return 0;
    }
    else {
        $ips = explode ( "\.", $IPaddr );
        return ( $ips[3] + $ips[2] * 256 + $ips[1] * 256 * 256 + $ips[0] * 256 * 256 * 256 );
    }
}

/** -------------------------------------------------------------------------
 * [00/00/2009]::Parse the data used in the html tags to ensure the tags will not break
 *  ----------------------------------------------------------------------- */
function _getParseInputFieldData( $data, $parse ){
	return strtr( trim( $data ), $parse );
}

/** -------------------------------------------------------------------------
 * [00/00/2009]::To cut string
 *  ----------------------------------------------------------------------- */
function _getOutputString( $string, $translate = false, $protected = false ){
	if ( $protected === true ){
			return htmlspecialchars( $string );
	}
	else {
	  if ( $translate ===  false ){
			return _getParseInputFieldData( $string, array( '"' => '&quot;' ) );
	  }
	  else {
			return _getParseInputFieldData( $string, array( '"' => '"' ) );
	  }
	}
}

function _getParseInputFieldData_protected( $string ){

	return _getOutputString( $string, false, true );
}

/** -------------------------------------------------------------------------
 * [00/00/2009]:: $eStrings is array
 *  ----------------------------------------------------------------------- */
function _getErrorOutput( $eStrings ){
	if ( count( $eStrings ) > 0 ){
		foreach ( $eStrings as $key => $kw ){
			$va					 = strtolower( $key );
			$output[$va]	.= _getSanitize( $kw );
		}
		unset( $eStrings );

		$i = 1;
		foreach ( $output as $keys => $val ){
			$extra_tail = $i == count( $output ) ? "<br />" : "";
			$error_output .= "<strong>".strtoupper( $keys ) .":</strong> ". $val . $extra_tail;
			$i++;
		}

		$error_output = "<small>". $error_output ."</small>";
	}

	return $error_output;
}

/** -------------------------------------------------------------------------
 * [00/00/2009]::Wordwrap without unnecessary word splitting using multibyte string functions
 * mb_stripos and mb_substr are PHP5 function
 * @param string $str
 * @param int $width
 * @param string $break
 * @return string
 * @author Golan Zakai <golanzakaiATpatternDOTcoDOTil>
 * _wordwrap to _getWordWrap
 *  ----------------------------------------------------------------------- */
function _getWordWrap( $str, $width, $break ){
	$formatted = '';
	$position = -1;
	$prev_position = 0;
	$last_line = -1;

	//Looping the string stop at each space
	while ( $position = mb_stripos( $str, " ", ++$position, 'utf-8' ) ){
		if ( $position > $last_line + $width + 1 ){
			$formatted.= mb_substr( $str, $last_line + 1, $prev_position - $last_line - 1, 'utf-8' ).$break;
			$last_line = $prev_position;
		}
		$prev_position = $position;
	}

	//Adding last line without the break
	$formatted.= mb_substr( $str, $last_line + 1, mb_strlen( $str ), 'utf-8' );

	return $formatted;
}

/** -------------------------------------------------------------------------
* [03/30/2009]::To cut string by certain length
 *  ------------------------------------------------------------------------- */
function _mb_strcut( $str, $start = 0, $length ){

 	$string = trim( strip_tags( $str ) );
	$string = str_replace( array( "<p>", "</p>" ), array( "<br>", "<br>" ),  $string );	  // Replace <p> with <br> tag
	$string = preg_replace('/^<br\s?\>/', '', $string); 	  											  // Remove first <br> tag
 	$str_len = mb_strlen($string,'UTF-8');

   if ( $str_len > $length ){
	   /* mb_substr  PHP 4.0 이상, iconv_substr PHP 5.0 이상 */
	   $cutted_str  = mb_substr( $string, $start, $length,'UTF-8') . "..";
    }
	else {
		$cutted_str = $string;
	}

	return $cutted_str;
}

/** -------------------------------------------------------------------------
 * [00/00/2009]::
 *  ----------------------------------------------------------------------- */
function _getCheckingSession( $base_url="" ){
	global $Bon_db, $_ipno, $base_url;

		$session_life_admin	= CONFIG_LIFETIME_ADMIN; //30mins in second
		$logintime					= $_SESSION['session_time'];
		$session_id					= $_SESSION['session_id'];
		$session_life 				= time() - $logintime;

		if ( $Bon_db->getTotalNumber( "members_session", "session_id != ''" ) > 0 ){
			$t_time 	= time() - $session_life_admin;
			$dquery	= "DELETE FROM members_session WHERE time < '". $t_time ."'";
			$Bon_db->getQuery( $dquery );
		}

		if ( CONFIG_CHECK_IP_STATUS_FOR_SESSION === "true" ){
			if ( $_ipno != $_SESSION['ipno'] ){
				$extre = $_SESSION['section'] === "b" ? " AND section = 'b'" : "" ;

				$query = "DELETE FROM members_session WHERE session_id = '". $session_id ."'". $extre. " AND userid = '".( int )$_SESSION['session_user_id'] ."'";
				$Bon_db->getQuery( $query );
				session_destroy();
				clearstatcache();

				echo "<script type=\"text/javascript\"> alert( 'Your IP address has been changed. Please login again.' ); window.location.reload( '". $base_url ."' ); </script>\n";
				exit();
			}
		}

		$members	= $Bon_db->getMemberInfo( ( int )$_SESSION['session_user_id'], "members_email = '". $_SESSION['session_username']  ."'" );

		if ( $session_life < $session_life_admin ){
			#Calculation of past date
			$past = date( 'Y-m-d H:i:s', time() - 7 * 60 * 60 * 24 );
			if ( $session_id == md5( $members['id'] . $members['members_email'] . $members['members_type'] . $logintime ) ){

				if ( $Bon_db->getTotalNumber( "members_session", "session_id = '". $session_id ."'" ) == 0 ){
					session_start();
					unset($_SESSION);
					session_destroy();

					echo "<script type=\"text/javascript\">parent.location=\"". $base_url ."\"; </script>\n";
				}
				else {
					$current_time	= time();
					$current_session_id  = md5( $members['id'] . $members['members_email'] . $members['members_type'] . $current_time );
					$query = "UPDATE members_session SET time = ". $current_time . ", session_id = '". $current_session_id ."' WHERE session_id = '". $session_id ."'";
				}

				if( $Bon_db->getQuery( $query ) ){
					$_SESSION['session_id'] 		= $current_session_id;
					$_SESSION['session_time']	= $current_time;
				}
				else{
					$query = "DELETE FROM members_session WHERE session_id = '". $session_id ."'";
					$Bon_db->getQuery( $query );
					unset($_SESSION);
					session_destroy();
					echo "<script type=\"text/javascript\"> alert( 'Please try to login again.' ); window.location.reload( '". $base_url ."' ); </script>\n";
					exit();
				}
			}
			else {
				$query = "DELETE FROM members_session WHERE session_id = '". $session_id ."'";
				$Bon_db->getQuery( $query );
				unset($_SESSION);
				session_destroy();
				echo "<script type=\"text/javascript\"> alert( 'Your seesion ID was not assigned.' ); window.location.reload( '". $base_url ."' ); </script>\n";
				exit();
			}
		}
		else {
			$query = "DELETE FROM members_session WHERE session_id = '". $session_id ."' AND userid = '".( int )$_SESSION['session_user_id'] ."'";
			$Bon_db->getQuery( $query );
			unset($_SESSION);
			session_destroy();
			clearstatcache();

			echo "<script type=\"text/javascript\"> alert( 'Your seesion has expired. Please reconnect.' ); window.location.reload( '". $base_url ."' ); </script>\n";
			exit();
		}
}

/** -------------------------------------------------------------------------
 * [00/00/2009]::Sending Email
 *  ----------------------------------------------------------------------- */
function _getSendEmail( $to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address ){
	if ( SEND_EMAILS !== true ) return false;

	//Instantiate a new mail object
	$message = new getEmail( array( 'X-Mailer: Bon Mailer' ) );

	//Build the text version
	$text = strip_tags( $email_text );
	if ( EMAIL_USE_HTML == "true" ){
	  $message->add_html( $email_text, $text );
	}
	else {
	  $message->add_text( $text );
	}

	//Send message
	$message->build_message();
	$message->send( $to_name, $to_email_address, $from_email_name, $from_email_address, $email_subject );
}

/**
/** -------------------------------------------------------------------------
 * [00/00/2009]::
 *  ----------------------------------------------------------------------- */
function _getPostDate( $raw_date ){

    if ( $raw_date == '' ) return false;

    $year 	= ( int )substr( $raw_date, 0, 4 );
    $month	= ( int )substr( $raw_date, 5, 2 );
    $day 	= ( int )substr( $raw_date, 8, 2 );

    return date( "M", mktime( 0, 0, 0, $month, 1, $year ) )."<span>".$day."</span><span class='year'>".$year."</span>";
}

/** -------------------------------------------------------------------------
 * [00/00/2009]::Output a raw date string in the selected locale date format
 * $raw_date needs to be in this format: YYYY-MM-DD HH:MM:SS
 * NOTE: Includes a workaround for dates before 01/01/1970 that fail on windows servers
 *  ----------------------------------------------------------------------- */
function _getShortDateTime( $raw_date ){

	if ( $raw_date == '' ) return false;

	$year	= substr( $raw_date, 0, 4 );
	$month	= ( int )substr( $raw_date, 5, 2 );
	$day	= ( int )substr( $raw_date, 8, 2 );

	$hrs		= ( int )substr( $raw_date, 11, 2 );
	$mins	= ( int )substr( $raw_date, 14, 2 );
	$secs	= ( int )substr( $raw_date, 17, 2 );

	//return date( "Y.m.d", mktime( 0, 0, 0, $month, $day, $year ) );
	return date( "Y.m.d G:i:s", mktime( $hrs, $mins, $secs, $month, $day, $year ) );
}

/** -------------------------------------------------------------------------
 * [00/00/2009]::Output a raw date string in the selected locale date format
 * $raw_date needs to be in this format: YYYY-MM-DD HH:MM:SS
 * NOTE: Includes a workaround for dates before 01/01/1970 that fail on windows servers
 *  ----------------------------------------------------------------------- */
function _getShortFormatDate( $raw_date ){

	if ( $raw_date == '' ) return false;

	$year	= substr( $raw_date, 0, 4 );
	$month	= ( int )substr( $raw_date, 5, 2 );
	$day	= ( int )substr( $raw_date, 8, 2 );

	return date( "m.d.y", mktime( 0, 0, 0, $month, $day, $year ) );
}

/** -------------------------------------------------------------------------
 * [00/00/2009]::
 *  ----------------------------------------------------------------------- */
function _getLongFormatDate( $raw_date ){

    if ( $raw_date == '' ) return false;

    $year	= ( int )substr( $raw_date, 0, 4 );
    $month	= ( int )substr( $raw_date, 5, 2 );
    $day 	= ( int )substr( $raw_date, 8, 2 );

    return date( "M d, Y", mktime( 0, 0, 0, $month, $day, $year ) );
}

/** -------------------------------------------------------------------------
 * [00/00/2009]::
 *  ----------------------------------------------------------------------- */
function _getEncodeEmailAddress( $email ){
	$return = '';
	for ( $i=0,$c=strlen( $email );$i<$c;$i++ ){
		$return .= '&#' . ( rand( 0,1 )==0 ? ord( $email[$i] ) : 'X'.dechex( ord( $email[$i] ) ) ) . ';';
	}

	return $return;
}

/** -------------------------------------------------------------------------
 * [00/00/2009]::
 *  ----------------------------------------------------------------------- */
function getThumb_image( $path, $src, $alt = '', $width = '', $height = '', $parameters = '' ){

  	$image_size = @getimagesize( $src );

    if ( ( ( $src == '' ) || ( $src == DIR_IMAGES ) ) ){
      return false;
    }

	//To get FULL FILE NAME only
    $file = substr( getParseInputFieldData( $src, array( '"' => '&quot;' ) ), strrpos( getParseInputFieldData( $src, array( '"' => '&quot;' ) ), "/" )+1 );

    //To get file extension ONLY >> strtoupper( substr( $src,strrpos( $src,"." ) ) );
	if( $image_size[0] > $width && file_exists( $path.$file ) ){

		if ( $width > 0 ) $filenamesize .= $width . '_';
		if ( $height > 0 ) $filenamesize .= $height . '_';

		if ( file_exists( $path.'temp/'.$filenamesize.$file ) ){
			return '<img src="' . $path.'temp/' .$filenamesize. $file . '" border="0" alt="' . getParseInputFieldData( $alt, array( '"' => '&quot;' ) ) . '" ' . $parameters . ' />';

		}
		else{
			$thumb = new thumbnail( getParseInputFieldData( $src, array( '"' => '&quot;' ) ) );

			if ( ! $thumb->img['src'] ){
				// return tep_image_OLD( $src, $alt, $width, $height, $params );
			}

			// $thumb->size_width( $width );
			// $thumb->size_height( $height );
			$thumb->size_auto( $height );
			$thumb->save( $path.'temp/'.$filenamesize.$file );

			return '<img src="' . $path.'temp/' . $filenamesize . $file . '" border="0" alt="' . getParseInputFieldData( $alt, array( '"' => '&quot;' ) ) . '" ' . $parameters . ' />';
		}
	}
	else{
	  //alt is added to the img tag even if it is null to prevent browsers from outputting
	  //the image filename as default
	  	$image = '<img src="' . getParseInputFieldData( $src, array( '"' => '&quot;' ) ) . '" border="0" alt="' . getParseInputFieldData( $alt, array( '"' => '&quot;' ) ) . '"';

		if ( getCheckNullorNot( $alt ) ){
			$image .= ' title="' . getParseInputFieldData( $alt, array( '"' => '&quot;' ) ) . ' "';
		}

		if ( getCheckNullorNot( $parameters ) ) $image .= ' ' . $parameters;

		$image .= ' />';

		return $image;
	}
}

/** -------------------------------------------------------------------------
 * [00/00/2009]::Display an image for Backend
 *  ----------------------------------------------------------------------- */
function getDisplayImgBK( $filename, $maxWidth, $alttxt="", $displayTxt = true ){
		global $Config_allowed_image_extension;

		if( !empty( $filename ) ){
			$file_array 	= explode( ";", $filename );
			if( count( $file_array ) > 0 ){
				for( $k=0; $k < count( $file_array ); $k++ ){
					$extension_array[$k] = explode( ".", $file_array[$k] );
					$fileextension = end($extension_array[$k]);
					if(in_array($fileextension,$Config_allowed_image_extension)){

						$image_path[$k] = $_SERVER['DOCUMENT_ROOT'] . UNDER_SUBFOLDER ."/". CONFIG_FILES_UPLOAD_ROOT . $file_array[$k];
						if( file_exists( $image_path[$k] ) ){
							list( $width, $height )= getimagesize( $image_path[$k] );

							if( $width > $maxWidth ){
								$percent_resizing = round( ( $maxWidth / $width ) * 100 );
								$new_width = $maxWidth;
								$new_height  = round( ( $percent_resizing / 100 )  * $height );
								$displayimg .= "\n<img src=\"". CONFIG_SITE_URL ."/". CONFIG_FILES_UPLOAD_ROOT . $file_array[$k] ."\" width=\"{$new_width}\" height=\"{$new_height}\" alt=\"{$alttxt}_w{$width}_h{$height}\"/>\n<br/>";
								if( $displayTxt === true ){
									$displayimg .= "\n<small>This image has been resized to display.</small>\n<br/>\n";
								}

							}
							else{
								$displayimg .= "\n<img src=\"". CONFIG_SITE_URL ."/". CONFIG_FILES_UPLOAD_ROOT . $file_array[$k] ."\" width=\"{$width}\" height=\"{$height}\" alt=\"{$alttxt}\"/>\n<br/>\n";
							}

						}
						else{
							$displayimg .= "<small>\"".$file_array[$k]."\"  does not exist in the folder.</small>";
						}
					}
					else {
						$displayimg = "";
					}
				}
			}
		}
		else{
			$displayimg = "";
		}

	return $displayimg;
}

/** -------------------------------------------------------------------------
 * [00/00/2009]::Display an images for Frontend
 *  ----------------------------------------------------------------------- */
function getDisplayImg( $filename, $maxWidth, $nailthumb = false, $alttxt="", $useDiv = true, $extraClass="" ){
		global $Config_allowed_image_extension;

		if ( !empty( $filename ) ){
			$file_array 	= explode( ";", $filename );
			if ( count( $file_array ) > 0 ){
				for ( $k=0; $k < count( $file_array ); $k++ ){
					$extension_array[$k] = explode( ".", $file_array[$k] );
					$fileextension = end($extension_array[$k]);
					if ( in_array($fileextension,$Config_allowed_image_extension) ){
							$image_path[$k] = $_SERVER['DOCUMENT_ROOT'] . UNDER_SUBFOLDER ."/". CONFIG_FILES_UPLOAD_ROOT . $file_array[$k];
							if ( file_exists( $image_path[$k] ) ){
								list( $width, $height )= getimagesize( $image_path[$k] );
								if ( $width >= $maxWidth && 0 != $maxWidth ){
									//$widthsize	= ( $nailthumb == true ) ? round( ( $width / $maxWidth ) * 100 ) : $width;
									$widthsize	= $maxWidth;
								}
								else {
									$widthsize	= $width;
								}

								$heightsize	= floor( ( $height / $width ) * $widthsize );
								$extraClass = !empty( $extraClass ) ? " class=\"{$extraClass}\"" : "";
								if( $nailthumb == true ){
										$displayimg .= "<img src=\"". CONFIG_SITE_URL ."/". CONFIG_FILES_UPLOAD_ROOT . $file_array[$k] ."\" alt=\"{$alttxt}\"{$extraClass} data-width=\"".$width."\"/>\n";
								}
								else {
										if ( $useDiv == true ){
											$displayimg .= "<div{$extraClass} style=\"margin:0 auto; max-width:{$widthsize}px;\"><img src=\"". CONFIG_SITE_URL ."/". CONFIG_FILES_UPLOAD_ROOT . $file_array[$k] ."\" width=\"{$widthsize}\" height=\"{$heightsize}\" alt=\"{$alttxt}\" data-width=\"".$width."\"/></div>\n";
										}
										else {
											$displayimg .= "<img src=\"". CONFIG_SITE_URL ."/". CONFIG_FILES_UPLOAD_ROOT . $file_array[$k] ."\" alt=\"{$alttxt}\"{$extraClass} data-width=\"".$width."\" style=\"margin:0 auto; max-width:{$widthsize}px;\"/>\n";
										}
								}
							}
							else {
								$displayimg .= "<small>No file <br/>in the folder</small>";
							}
					}
					else {
						$displayimg = "";
					}
				}
			}
		}
		else {
			$displayimg = "";
		}

	return $displayimg;
}

/** -------------------------------------------------------------------------
 * [05/02/2013]::Display an banner for pages
 *  ----------------------------------------------------------------------- */
function getDisplayBanner( $filename, $fulltxt="", $alttxt="", $urltxt="", $shtmlTitle="", $target_window="", $extraClass="", $frontCheck, $asImg = false){
	global $base_url;

		if( !empty( $filename ) ){
			$image_path	= $_SERVER['DOCUMENT_ROOT'] . UNDER_SUBFOLDER ."/". CONFIG_FILES_UPLOAD_ROOT . $filename;
			$image_url		= $base_url."/". CONFIG_FILES_UPLOAD_ROOT . $filename;

			if( file_exists( $image_path ) ){
				list( $width, $height ) = getimagesize( $image_path );
				$imgs			= "<img src=\"$image_url\" alt=\"$alttxt\" width=\"$width\" height=\"$height\" data-width=\"$width\">";
				$extraClass	= !empty( $extraClass ) ? " class=\"". $extraClass ."\"" : "";

				//Front page banners
				if( $frontCheck == 1 ){
						$Afulltxt = _getCheckNullorNot( $fulltxt ) ? "<h3>". $fulltxt ."</h3>" : "";
						if( _getCheckNullorNot( $urltxt ) ){
							//$displayimg .= "\t<li".$extraClass ."><a href=\"". $this->getProperLink($urltxt) ."\" title=\"".$shtmlTitle."\" target=\"".$target_window."\">".$imgs . $Afulltxt ."</a></li>";
							$displayimg .= "<div class=\"slides\" style=\"background-image:url('". $image_url ."');\"><div class=\"container text-center\"><a href=\"". $this->getProperLink($urltxt) ."\" title=\"".$shtmlTitle."\" target=\"".$target_window."\">".$imgs . $Afulltxt ."</a></div></div>";
						}
						else {
							$displayimg .= "<div class=\"slides\" style=\"background-image:url('". $image_url ."');\"><div class=\"container text-center\">". $Afulltxt ."</div></div>";
						}
				}
				//Other pages' banners
				else {
						$Afulltxt = _getCheckNullorNot( $fulltxt ) ? "<h3 class=\"banner-heading\">". $fulltxt ."</h3>" : "";
					if( $asImg == true) {
						$displayimg .= "<div". $extraClass ." style=\"max-width: ".$width."px;\">".$imgs . $Afulltxt ."</div>\n";
					}
					else{
						//$displayimg .= "\t<li". $extraClass ." style=\"background-image: url('".$image_url."');\">". $Afulltxt ."</li>";
						$displayimg .= "<div class=\"slides4page\" style=\"background-image:url('". $image_url ."');\"><div class=\"container text-center\">". $Afulltxt ."</div></div>";
					}
				}
			}
			else{
				$displayimg .= "<small>No Image</small>";
			}
		}
		else{
			$displayimg = "";
		}

	return $displayimg;
}

/** -------------------------------------------------------------------------
 * [00/00/2009]::To assign a file into proper folder
 *  ----------------------------------------------------------------------- */
function getDestinationFolder ( $file_name ){
	global $Config_prohibited_extension, $Config_allowed_image_extension, $Config_allowed_docs_extension, $Config_allowed_vod_extension, $Config_allowed_music_extension;

	#To check the file is okay to upload or not base on the extension of file
	$filename 	= explode( ".", $file_name );
	$extension 	= $filename[sizeof( $filename )-1];

	if ( in_array( $extension,$Config_prohibited_extension ) ){
		$error_message[]	= "NOT_ALLOWED_FILE_FORMAT";
		$error_flag				= true;

		return $error_message;
	}
	elseif ( in_array( $extension,$Config_allowed_image_extension ) ){
		$file_folder = CONFIG_FILES_UPLOAD_IMAGES;
	}
	elseif ( in_array( $extension,$Config_allowed_docs_extension ) ){
		$file_folder = CONFIG_FILES_UPLOAD_DOCS;
	}
	elseif ( in_array( $extension,$Config_allowed_vod_extension ) ){
		$file_folder = CONFIG_FILES_UPLOAD_VOD;
	}
	elseif ( in_array( $extension,$Config_allowed_music_extension ) ){
		$file_folder = CONFIG_FILES_UPLOAD_MUSIC;
	}

	return $file_folder;
}

/** -------------------------------------------------------------------------
 * [00/00/2009]::Replace
 *  ----------------------------------------------------------------------- */
function getChangFilename( $filename ){
	$temp = strtolower( $filename );

	for ( $i=0; $i< strlen( $temp ); $i++ ){
		if ( !preg_match( '/[^0-9a-z\.\_\-]/i', $temp[$i] ) ){
			$result = $result . $temp[$i];
		}
		else {
			$result = $result . $temp[$i];
		}
	}

	return $result;
}

/** -------------------------------------------------------------------------
 * [00/00/2009]::
 *  ------------------------------------------------------------------------- */
function getUploadingFile ( $aFILES, $thumb = true ){
	global $Bon_db, $Config_prohibited_extension, $Config_allowed_image_extension, $Config_allowed_docs_extension, $Config_allowed_vod_extension, $Config_allowed_music_extension;

	$upload_file_name = strtolower( $aFILES[name] );
	$upload_file_name = str_replace( " ","_",$upload_file_name );
	$upload_file_name = str_replace( "-","_",$upload_file_name );

	$upload_file_type	= $aFILES[type];
	$upload_file 	  		= $aFILES[tmp_name];
	$upload_file_size 	= $aFILES[size];

	//To check the file is okay to upload or not base on the extension of file
	$filename 	= explode( ".", $upload_file_name );
	$extension = $filename[sizeof( $filename )-1];

	//To check Folder Permission
	$destination_folder = $_SERVER['DOCUMENT_ROOT'] . UNDER_SUBFOLDER . "/". CONFIG_FILES_UPLOAD_ROOT . getDestinationFolder( $upload_file_name );
	if ( is_dir( $destination_folder ) && $error_flag == false ){
		@chmod( $destination_folder,0777 );	//Force to change the folder's Permission

		$destination_subfolder = $_SERVER['DOCUMENT_ROOT'] . UNDER_SUBFOLDER ."/". CONFIG_FILES_UPLOAD_ROOT . getDestinationFolder($upload_file_name) . date("Ym");
		if(!is_dir($destination_subfolder)){
			@mkdir($destination_subfolder, 0777, true);
		}
	}
	else {
		$error_message[]	= "DIRECTORY_NOT_EXIST_OR_IMPROPER_FOLDER :: ".$destination_folder;
		$error_flag 			= true;
	}

	//To check there is same file name or not
	$destination = $destination_subfolder."/". $upload_file_name;
	if ( file_exists( $destination ) && $error_flag == false ){
		$destination = $destination_subfolder."/". $filename[0] ."_". time().".".$filename[1];
		$upload_file_name = $filename[0] ."_". time().".".$filename[1];
	}

	//To save a file into folder
	//echo $upload_file_name."<br/>";
	//echo $destination."<br/>";
	if ( !@move_uploaded_file( $upload_file,$destination ) && $error_flag == false ){
		$error_message[]	= "ACCESS_DENIED_TO_COPY";
		$error_message[]	= "Error moving uploaded file ".$upload_file." to the ". $destination;
		$error_message[]	= "Check the directory permissions for ". getDestinationFolder( $upload_file_name ) ."( must be 777 )!";
		$error_flag				= true;
	}

	//After saving into proper folder, delete the file from buffer.
	if( !@unlink( $upload_file ) && $error_flag == false ){
		$error_message[]	= "ACCESS_DENIED_TO_DELETE_TMP_FILE";
		$error_flag				= true;
	}

	if ( $thumb ===  true ){
		if ( in_array( $extension,$Config_allowed_image_extension ) ){
			//ob_end_flush();
			$src = $destination.$upload_file_name;
			filesize( $src );
			$image_size = @getimagesize( $src );
			if ( $image_size[0] > 700 ){
				$newName = $filename[0]."_700.".$filename[1];
				$thumb = new thumbnail( $src );
				$thumb->size_width( 700 );
				$thumb->save( $destination.$newName );
				!@unlink( $destination.$upload_file_name );

				$upload_file_name = $newName;
			}
		}
	}

	$uploading_file['name'] 	= getDestinationFolder( $upload_file_name ) . date("Ym") ."/". $upload_file_name;
	$uploading_file['type']	= $extension;	//$upload_file_type;
	$uploading_file['size'] 	= $upload_file_size;

	if ( $error_flag === "true" ){
		$uploading_file['error'] = $error_message;
	}

	return $uploading_file;
}

/** -------------------------------------------------------------------------
 * [00/00/2009]::
 *  ----------------------------------------------------------------------- */
function getUploadMultiFile ( $aFILES, $thumb = true ){
	global $Bon_db, $Config_prohibited_extension, $Config_allowed_image_extension, $Config_allowed_docs_extension, $Config_allowed_vod_extension, $Config_allowed_music_extension;

	for( $i=0; $i < count( $aFILES[name] ); $i++ ){
		$upload_file_name[$i] = strtolower( $aFILES[name][$i] );
		$upload_file_name[$i] = str_replace( " ","_",$upload_file_name[$i] );
		$upload_file_name[$i] = str_replace( "-","_",$upload_file_name[$i] );

		//To check the file is okay to upload or not base on the extension of file
		$filename[$i]	 			= explode( ".", $upload_file_name[$i] );
		$extension[$i] 			= end( $filename[$i] );

		$upload_file[$i]			= $aFILES[tmp_name][$i];
		$upload_file_size[$i] 	= $aFILES[size][$i];
		$upload_file_type[$i] 	= $aFILES[type][$i];

		//To check Floder Permission
		$destination_folder = $_SERVER['DOCUMENT_ROOT'] . UNDER_SUBFOLDER . "/". CONFIG_FILES_UPLOAD_ROOT . getDestinationFolder( $upload_file_name[$i] );
		if ( is_dir( $destination_folder ) && $error_flag == false ){
			@chmod( $destination_folder,0777 );	//Force to change the folder's Permission

			$destination_subfolder = $_SERVER['DOCUMENT_ROOT'] . UNDER_SUBFOLDER  ."/". CONFIG_FILES_UPLOAD_ROOT . getDestinationFolder($upload_file_name[$i]) . date("Ym");
			if(!is_dir($destination_subfolder)){
				@mkdir($destination_subfolder, 0777, true);
			}
		}
		else {
			$error_message[] = "DIRECTORY_NOT_EXIST_OR_IMPROPER_FOLDER :: ".$destination_folder;
			$error_flag = true;
		}

		//To check there is same file name or not
		$destination[$i] = $destination_subfolder ."/". $upload_file_name[$i];
		if ( file_exists( $destination[$i] ) && $error_flag == false ){
			$destination[$i] = $destination_subfolder ."/". $filename[$i][0] ."_". time().".".$filename[$i][1];
			$upload_file_name[$i] = $filename[$i][0] ."_". time().".".$filename[$i][1];
		}

		//To save a file into folder
		if ( !@move_uploaded_file( $upload_file[$i], $destination[$i] ) && $error_flag == false ){
			$error_message[] = "ACCESS_DENIED_TO_COPY";
			$error_message[] = "Error moving uploaded file ".$upload_file[$i]." to the ". $destination[$i];
			$error_message[] = "Check the directory permissions for ". getDestinationFolder( $upload_file_name[$i] ) ."( must be 777 )!";
			$error_flag		 = true;
		}

		//After saving into proper folder, delete the file from buffer.
		if( !@unlink( $upload_file[$i] ) && $error_flag == false ){
			$error_message[] = "ACCESS_DENIED_TO_DELETE_TMP_FILE";
			$error_flag = true;
		}

		if ( $thumb === true ){
			if (in_array($extension,$Config_allowed_image_extension)){
				//ob_end_flush();
				$src = $destination.$upload_file_name;
					filesize($src);
				$image_size = @getimagesize($src);
				if ( $image_size[0] > 700 ){
					$newName = $filename[0]."_700.".$filename[1];
					$thumb = new thumbnail($src);
					$thumb->size_width(700);
					$thumb->save($destination.$newName);
					!@unlink($destination.$upload_file_name);

					$upload_file_name = $newName;
				}
			}
		}

		$uploading_file['name'][$i] = getDestinationFolder( $upload_file_name[$i] ) . date("Ym") ."/". $upload_file_name[$i];
		$uploading_file['type'][$i] = $extension[$i];	//$upload_file_type;
		$uploading_file['size'][$i] = $upload_file_size[$i];
	}

	return $uploading_file;
}

/** -------------------------------------------------------------------------
 * [00/00/2013]::Get proper file size
 *  ----------------------------------------------------------------------- */
function getSizeFile( $url ){
    if ( substr( $url,0,4 )=='http' ){
        $x = array_change_key_case( get_headers( $url, 1 ),CASE_LOWER );
        if ( strcasecmp( $x[0], 'HTTP/1.1 200 OK' ) != 0 ){
					$x = $x['content-length'][1];
				}
				else{
					$x = $x['content-length'];
				}
    }
    else {
		$x = @filesize( $url );
	}

    return $x;
}


/** -------------------------------------------------------------------------
 * [03/03/2014]:: To put Facebook timeline on website
 * Facebook Functions
 *  ----------------------------------------------------------------------- */
 function fbTimelineFeed($pageid, $access_token, $locale){

// Looking for your Facebook Profile / Group ID?  http://lookup-id.com/
// http://smashballoon.com/custom-facebook-feed/demo/
// http://johndoesdesign.com/blog/2011/php/adding-a-facebook-news-status-feed-to-a-website/

//$app_id = "268871339946116";
//$app_secret = "6512846d4738eada5163309d78bc323a";
// "https://graph.facebook.com/oauth/access_token?type=client_cred&client_id={$app_id}&client_secret={$app_secret}";
//$access_token = '268871339946116|8fQKlswiri3EoPKTmr_EwE85uDY';

// You can get Facebook PAGE or GROUP id from http://lookup-id.com/
//$page_id = '154163081372950'; // for smartphoneFilm
//$locale = 'en_US';

	$json_object = fetchUrl('https://graph.facebook.com/' . $pageid . '/posts?access_token='.$access_token.'&limit=3&locale=' . $locale);
	$fbdata = json_decode($json_object);

	foreach ($fbdata->data as $post ){

			$time = fb_getdate(strtotime($post->created_time));
			$message_array = explode('_', $post->id);
			$messageid = end($message_array);

			if ( !empty($post->message) ){
				if ( strlen( $post->message ) > 210 ){
					$message = mb_strcut( $post->message, 0, 210, 'utf-8' )."..";
				}
				else {
					$message = $post->message;
				}
				$post_text = fb_make_clickable( htmlspecialchars( $message ) ) ;
			}
			elseif (!empty($post->story)){
				if( strlen( $post->story ) > 210 ){
					$story = mb_strcut( $post->story, 0, 210, 'utf-8' )."..";
				}
				else {
					$story = $post->story;
				}
				$post_text = fb_make_clickable( htmlspecialchars( $story ) ) ;
			}

			$posts .= "<li class='fblist'><a class='cff-author' href='https://www.facebook.com/".$post->from->id."' target='_blank' title='".$post->name."'><img src='http://graph.facebook.com/".$post->from->id."/picture' width='50' height='50' class='fbnamepic'><span class='page-name'>".$post->from->name."<br/><span class='timetxt'>".$time."</span></span></a>";
			$posts .= "<p class='post-text'>". $post_text ."</p>";
			$posts .= "<a class='viewpost-facebook' href='https://www.facebook.com/".$post->from->id."/posts/".$messageid."' title='View on Facebook' target='_blank'>View on Facebook</a>";
			$posts .= "</li>\n";

			$postName = $post->name;
	}
	$feedingData = "<ul class='front_articles'>\n<li class='fbtitle'><a href='https://www.facebook.com/".$pageid."' target='_blank' title='".$postName."'>Facebook</a><div class=\"fb-like\" data-href=\"https://www.facebook.com/smartphonefilm\" data-layout=\"button_count\" data-action=\"like\" data-show-faces=\"false\" data-share=\"false\"></div></li>\n".$posts."</ul>";

    return $feedingData;
}

function fetchUrl($url){
    //Can we use cURL?
    if (is_callable('curl_init')){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $feedData = curl_exec($ch);
        curl_close($ch);
    //If not then use file_get_contents
    }
    elseif ( ini_get('allow_url_fopen') == 1 || ini_get('allow_url_fopen') === TRUE ) {
        $feedData = @file_get_contents($url);
    }

    return $feedData;
}
function fb_make_clickable($text) {
    $pattern  = '#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#';
    return preg_replace_callback($pattern, 'fb_auto_link_text_callback', $text);
}
function fb_auto_link_text_callback($matches) {
    $max_url_length = 50;
    $max_depth_if_over_length = 2;
    $ellipsis = '&hellip;';
    $target = 'target="_blank"';
    $url_full = $matches[0];
    $url_short = '';
    if (strlen($url_full) > $max_url_length) {
        $parts = parse_url($url_full);
        $url_short = $parts['scheme'] . '://' . preg_replace('/^www\./', '', $parts['host']) . '/';
        $path_components = explode('/', trim($parts['path'], '/'));
        foreach ($path_components as $dir) {
            $url_string_components[] = $dir . '/';
        }
        if (!empty($parts['query'])) {
            $url_string_components[] = '?' . $parts['query'];
        }
        if (!empty($parts['fragment'])) {
            $url_string_components[] = '#' . $parts['fragment'];
        }
        for ($k = 0; $k < count($url_string_components); $k++) {
            $curr_component = $url_string_components[$k];
            if ($k >= $max_depth_if_over_length || strlen($url_short) + strlen($curr_component) > $max_url_length) {
                if ($k == 0 && strlen($url_short) < $max_url_length) {
                    // Always show a portion of first directory
                    $url_short .= substr($curr_component, 0, $max_url_length - strlen($url_short));
                }
                $url_short .= $ellipsis;
                break;
            }
            $url_short .= $curr_component;
        }
    }
		else {
        $url_short = $url_full;
    }
    if( substr( $url_full, 0, 4 ) !== "http" ) $url_full = 'http://' . $url_full;

    return "<a class='break-word' rel='nofollow' href='".$url_full."'" . $target . ">".$url_full."</a>";
}
function fb_getdate($original) {

            $printT = date('M.d.y', $original);
            $periods = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', "decade");
            $periods_plural = array('seconds', 'minutes', 'hours', 'days', 'weeks', 'months', 'years', "decade");

            $lengths = array("60","60","24","7","4.35","12","10");
            $now = time();

            // is it future date or past date
            if ($now > $original) {
                $difference = $now - $original;
                $tense = 'ago';
            }
						else {
                $difference = $original - $now;
                $tense = 'ago';
            }

            for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
                $difference /= $lengths[$j];
            }

            $difference = round($difference);

            if($difference != 1) {
                $periods[$j] = $periods_plural[$j];
            }
            $print = "$difference $periods[$j] {$tense}";

    return $printT." / ".$print;
}

function _getRemovedir($dir) {
		$structure = glob(rtrim($dir, "/").'/*');
    if (is_array($structure)){
        foreach ($structure as $file){
            if (is_dir($file)) {
            	_getRemovedir($file);
            }
            elseif (is_file($file)){
             @unlink($file);
             }
        }
    }

		$folders = explode( "/", rtrim($dir, "/"));
		if( end($folders) != "thumbs" ){
				@rmdir($dir);
		}
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!
