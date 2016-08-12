<?php
/** -------------------------------------------------------------------------
 * @author      Kenwoo - iweb@kenwoo.ca
 *  ------------------------------------------------------------------------- */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); ?>
<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"><meta id="extViewportMeta" name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
<!-- START: Meta Informations -->
<?php
foreach ( $all_contents['metainfo'] as $key => $values ) {
	echo $all_contents['metainfo'][$key];
}
?>
<meta name="google-site-verification" content="" />
<meta name="robots" content="index,follow"/>
<!--html5.js for IE less than 9-->
<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<!-- css3-mediaqueries.js for IE less than 9 -->
<!--[if lt IE 9]><script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script><![endif]-->
<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
<link rel="stylesheet" href="<?php echo $base_url;?>/minifycss?tp=<?php echo $Config_template_name;?>&fn=fonts|normalize|general|connect-front|responsive">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<script type="text/javascript" src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<?php echo $all_contents['modules']['head_tracking']."\n"; ?>
</head>
<body>
<!-- START:  Main -->
<!-- span id="debugid"></span -->
<main>
<!--span id="debugid"></span-->
	<!-- START:  Header -->
	<header>
<?php echo $all_contents['modules']['top_menu'];?>
	</header>
<?php
echo $all_contents['modules']['top_banner'];
if( _getCheckNullorNot( $all_contents['modules']['left'] ) ){
	$page_contents = "\n<aside class=\"aside_left\">\n".$all_contents['modules']['left']."\n</aside>\n\n"
								."<article class=\"article_right\">\n".$all_contents['contents']."\n</article>\n"
								."\n<br class=\"clearfix\" />\n";
}
elseif( _getCheckNullorNot( $all_contents['modules']['left'] ) && _getCheckNullorNot( $all_contents['modules']['right'] ) ){
	$page_contents = "\n<aside class=\"main_left\">\n".$all_contents['modules']['left']."\n</aside>\n\n"
								."<article class=\"main_center\">\n".$all_contents['contents']."\n</article>\n\n"
								."<aside class=\"main_right\">\n".$all_contents['modules']['right']."\n</aside>\n"
								."\n<br class=\"clearfix\" />\n";
}
elseif( _getCheckNullorNot( $all_contents['modules']['right'] ) ){
	$page_contents = "\n<article class=\"article_left\">\n".$all_contents['contents']."\n</article>\n\n"
								."<aside class=\"aside_right\">\n".$all_contents['modules']['right']."\n</aside>\n"
								."\n<br class=\"clearfix\" />\n";
}
else{
	$page_contents = $all_contents['contents']."\n";
}

$breadcrumb = "<div class=\"breadcrumb\">\n".$all_contents['modules']['breadcrumb']."</div>\n";
echo ( $_ci_path !== "/" ) ? "<section class=\"content\"><div class=\"container-for-page\">". $breadcrumb . $page_contents ."</div></section>". $all_contents['modules']['user3'] : $page_contents. $all_contents['modules']['user3'] ;
?>
</main>
<!-- START: Footer Area -->
<footer>
<?php echo "<div class=\"footer\">". $all_contents['modules']['footer']."</div>\n"; ?>
</footer>
<script type="text/javascript" src="<?php echo $base_url;?>/minifyjs?tp=<?php echo $Config_template_name;?>&fn=lean_slider|Imgblock|doubletaptogo|fixed-menu.sliding|Options"></script>
<script type="text/javascript">
    $(document).ready(function() {
			var slider = $('#slider-area').leanSlider({
            controlNav: '#slider-control-nav'
        });
    });
</script>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-53a48c805faa5e1c"></script>
<?php echo $all_contents['modules']['foot_tracking']."\n"; ?>
<a href="#" class="go-to-top">TOP</a>
</body>
</html>