<?php
/** -------------------------------------------------------------------------
 * @author      Kenwoo - iweb@kenwoo.ca
 *  ------------------------------------------------------------------------- */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); ?>
<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]--><meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"><meta id="extViewportMeta" name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no"><!-- START: Meta Informations-->
<?php
foreach ( $all_contents['metainfo'] as $key => $values ) {
	echo $all_contents['metainfo'][$key];
}
?>
<meta name="google-site-verification" content="G_2UIG41vUzqYE_CMPuIn3eyjVjUYxr5ZlMWHYFPsIM" />
<meta name="robots" content="index,follow"/>
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Roboto:400,300,500,700,900">
<!--html5.js for IE less than 9--><!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]--><!-- css3-mediaqueries.js for IE less than 9 --><!--[if lt IE 9]><script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script><![endif]-->
<link rel="stylesheet" href="http://f.vimeocdn.com/styles/css_opt/global/icon_fonts.min.css?87f7bca">
<link rel="stylesheet" href="<?php echo $base_url;?>/minifycss?tp=<?php echo $Config_template_name;?>&fn=normalize|mtst01">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
</head>
<body><!-- START:  Main -->
<main><!-- START:  Header -->
<header>
<div class="header">
<?php echo $all_contents['modules']['top_menu'];?>
</div>
</header><!-- START:  Section -->
<section>
<?php
echo $all_contents['modules']['top_banner'];
echo "<div class=\"breadcrumb\">\n".$all_contents['modules']['breadcrumb']."</div>\n";
echo $all_contents['modules']['user1'];

if( _getCheckNullorNot( $all_contents['modules']['left'] ) ){
	echo "\n<aside class=\"aside_left\">\n".$all_contents['modules']['left']."\n</aside>\n\n"
			."<article class=\"article_right\">\n".$all_contents['contents']."\n</article>\n"
			."\n<br class=\"clearfix\" />\n";
}elseif( _getCheckNullorNot( $all_contents['modules']['left'] ) && _getCheckNullorNot( $all_contents['modules']['right'] ) ){
	echo "\n<aside class=\"main_left\">\n".$all_contents['modules']['left']."\n</aside>\n\n"
			."<article class=\"main_center\">\n".$all_contents['contents']."\n</article>\n\n"
			."<aside class=\"main_right\">\n".$all_contents['modules']['right']."\n</aside>\n"
			."\n<br class=\"clearfix\" />\n";
}elseif( _getCheckNullorNot( $all_contents['modules']['right'] ) ){
	echo "\n<article class=\"article_left\">\n".$all_contents['contents']."\n</article>\n\n"
			."<aside class=\"aside_right\">\n".$all_contents['modules']['right']."\n</aside>\n"
			."\n<br class=\"clearfix\" />\n";
}else{
	echo $all_contents['contents']."\n";
}
?>
</section>
</main><!-- START: Footer Area-->
<footer>
<?php echo "<div class=\"footer\">". $all_contents['modules']['footer']."</div>\n"; ?>
</footer><!-- START: JavaScript Area -->
<script type="text/javascript" src="<?php echo $base_url;?>/minifyjs?tp=<?php echo $Config_template_name;?>&fn=jquery.Imgblock|unslider|doubletaptogo|jquery.sidr.min|jquery.Options"></script>
<!--<script type="text/javascript" src="<?php echo $base_url;?>/design/<?php echo $Config_template_name;?>/js/jquery.nailthumb.1.1.min.js"></script>-->
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-53a48c805faa5e1c"></script><!-- START: Footer tracking Area --><?php echo $all_contents['modules']['foot_tracking'];?><!-- START: Top of Page --><a href="#" class="back-to-top">TOP</a></body></html>