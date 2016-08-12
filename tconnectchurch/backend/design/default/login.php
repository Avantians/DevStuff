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
<title>Genial CMS Administrator</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<link rel="shortcut icon" href="<?php echo $base_url;?>/images/<?php echo CONFIG_SITE_FAVICON;?>" type="image/x-icon"/>
<meta name="viewport" content="width=1024" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="robots" content="index,follow"/>
<?php
if( isset($all_contents) && _getCheckNullorNot($all_contents['metainfo']) ){
		foreach ($all_contents['metainfo'] as $key => $values) {
			echo $all_contents['metainfo'][$key];
		}
}
?>
<link rel="stylesheet" href="<?php echo $base_url;?>/design/default/css/normalize.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo $base_url;?>/design/default/css/template.css" type="text/css" media="screen" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
</head>
<body>
<a name="top" id="top"></a>
<div id="top_header">
	<div class="inside_title"><?php echo CONFIG_SITE; ?> :: <strong>Administrator</strong></div>
</div>

<div id="container">
	<div id='login_main_area'>
		<?php echo $all_contents['contents'];?>
	</div>
<!--Need to clear both float for IE to remove white space-->
	<br class="clearfix"/>
</div>

<div id="footer">
	<div class="footer_content">
		<p class="copyright">Developed by KENWOO | <span style="color:#950004;">&theta;.<span style="font-size:11pt;">&alpha;</span>&Omega;</span></p>
	</div>
</div>
<a href="/vo1#top" id="top-link"><span>Top of Page</span></a>
<!-- START: JavaScript Area -->
<script type="text/javascript" src="<?php echo str_replace("www", "www",rtrim(str_replace("/backend", "", $base_url), "/"));?>/static/js/jquery.scrollTo1.4.2-min.TopLink.js"></script>
<script type="text/javascript" src="<?php echo str_replace("www", "www",rtrim(str_replace("/backend", "", $base_url), "/"));?>/static/js/jquery.floatingbanner.js"></script>
<!-- END: JavaScript Area -->
</body>
</html>