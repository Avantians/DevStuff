<?php
/** -------------------------------------------------------------------------
 * @package  CMS
 * @author      Kenwoo - iweb@kenwoo.ca
 * @license    http://creativecommons.org/licenses/by/4.0/ Creative Commons
 *
 * [v02-02/07/2011]:: Redirect Browser  to HTTP_HOST
 *  ------------------------------------------------------------------------- */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Bon CMS Administrator</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<link rel="shortcut icon" href="<?php echo $base_url;?>/images/<?php echo CONFIG_SITE_FAVICON;?>" type="image/x-icon"/>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta id="extViewportMeta" name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="robots" content="index,follow"/>
<?php
if( isset($all_contents) && _getCheckNullorNot($all_contents['metainfo']) ){
		foreach ($all_contents['metainfo'] as $key => $values) {
			echo $all_contents['metainfo'][$key];
		}
}
?>
<link rel="stylesheet" href="<?php echo $base_url;?>/design/mobile/css/normalize.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo $base_url;?>/design/mobile/css/template.css" type="text/css" media="screen" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
</head>
<body>
<a name="top" id="top"></a>

<div id="top_header">
	<div class="inside_title"><?php echo CONFIG_SITE; ?> :: Administrator&nbsp;&nbsp;</div>
</div>

<div id="container">
		<?php echo $all_contents['contents'];?>
<!--Need to clear both float for IE to remove white space-->
	<br class="clearfix"/>
</div>

<div id="footer">
	<div class="footer_content">
		<p class="copyright">&copy; 2001 - <?php echo date( "Y" ) . " "; ?> Development &#8734; <a href="http://www.mosaicOne.net" title="easiest Bon CMS" target="_blank">MosaicOne</a>, All Rights Reserved. | <span style="color:#950004;">&theta;.<span style="font-size:11pt;">&alpha;</span>&Omega;</span></p>
	</div>
</div>

<a href="/vo1#top" id="top-link"><span>Top of Page</span></a>
<!-- START: JavaScript Area -->
<script type="text/javascript" src="<?php echo str_replace("www", "www",rtrim(str_replace("/backend", "", $base_url), "/"));?>/static/js/jquery.scrollTo1.4.2-min.TopLink.js"></script>
<script type="text/javascript" src="<?php echo str_replace("www", "www",rtrim(str_replace("/backend", "", $base_url), "/"));?>/static/js/jquery.floatingbanner.js"></script>
<!-- END: JavaScript Area -->
</body>
</html>