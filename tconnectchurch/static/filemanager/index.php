<?php
/** -------------------------------------------------------------------------
 * @package  CMS
 * @author      Kenwoo - iweb@kenwoo.ca
 * @license    http://creativecommons.org/licenses/by/4.0/ Creative Commons
 *
 * [v02-02/07/2011]:: Redirect Browser  to HTTP_HOST
 * In most cases you need a method of follow the header() function of the exit() function,
 * in order to decrease the load of the server.
 *  ------------------------------------------------------------------------- */
$url = getenv('HTTP_HOST');
$pharse_url = explode(".", $url);

if ($pharse_url[0] == 'www' && sizeof($pharse_url)== 3){
	$link = "http://". $url ."" ;
} elseif ($pharse_url[0] == 'http://www'){
	   $link = $url ;
} elseif ($pharse_url[0] != 'www'){
	$second_pharse_url = explode("/", $pharse_url[0]);
	if(sizeof($pharse_url) == 3){
		$url = str_replace( $pharse_url[0], "www", $url );
		$link = "http://". $url ."";
	} else {
		if(end($second_pharse_url) != 'www' && $second_pharse_url[0] == 'http:'){
			$link = $url;
		} else {
			$link = "http://www.". $url ."";
		}
	}
}
header("Location:". $link ."");
exit;
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!