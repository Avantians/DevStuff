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
$static_url = rtrim(str_replace("www", "www", str_replace("/backend", "", $base_url)), "/");

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
<div id="header-box">
	<ul id="menu" >
		<li><a href="<?php echo CONFIG_SITE_URL;?>" target="_blank">Site</a></li>
		<li><a href="<?php echo $base_url;?>/indexx.php" target="_self">HOME</a></li>
		<?php
			if ($_SESSION['session_userlevel'] <= 2){
				echo "<li><a href=\"".$base_url."/indexx.php?node=section\" target=\"_self\">Sections</a></li>\n";
				echo "<li><a href=\"".$base_url."/indexx.php?node=category\" target=\"_self\">Categories</a></li>\n";
			}
		?>
		<li><a href="<?php echo $base_url;?>/indexx.php?node=page" target="_self">Pages</a></li>
		<?php
			if ($_SESSION['session_userlevel'] <= 2){
				echo "<li><a href=\"".$base_url."/indexx.php?node=menu\" target=\"_self\">Menus</a></li>\n";
				echo "<li><a href=\"".$base_url."/indexx.php?node=templates\" target=\"_self\">Templates</a></li>\n";
			}
		?>
		<li><a href="<?php echo $base_url;?>/indexx.php?node=article" target="_self">Articles</a></li>
		<?php
		if ($_SESSION['session_userlevel'] <= 2){
				echo "<li><a href=\"".$base_url."/indexx.php?node=member\" target=\"_self\">Members</a></li>\n";
		}
		if ( !empty($_SESSION['session_user_id']) ){
			if($_SESSION['session_userlevel'] <= 2){
				echo "\n<li><a href=\"{$base_url}/indexx.php?node=modules\" target=\"_self\">Modules</a></li>";
			}
			echo "\n<li class=\"end\"><a href=\"{$base_url}/indexx.php?node=logout\" target=\"_self\">LOG OUT</a></li>";
		} 
		else {
			if ($_SESSION['session_userlevel'] <= 2){
				echo "\n<li><a href=\"{$base_url}/indexx.php?node=module\" target=\"_self\">Modules</a></li>";
			}
		}
		?>

	</ul>
</div>

<div id="container">
	<div id='main_area'>
		<?php echo $all_contents['contents'];?>
	</div>
<!--Need to clear both float for IE to remove white space-->
	<br class="clearfix"/>
</div>

<div id="footer">
	<div class="footer_content">
		<p class="copyright">&copy; 2003 - <?php echo date( "Y" ) . " "; ?> Development &#8734;  <a href="http://www.mosaicOne.net" title="easiest Bon CMS" target="_blank">MosaicOne</a> , All Rights Reserved. | <span style="color:#950004;">&theta;.<span style="font-size:11pt;">&alpha;</span>&Omega;</span></p>
	</div>
	<br />
</div>
<a href="#top" id="top-link"><span>Top of Page</span></a>
<!-- START: JavaScript Area -->
<script type="text/javascript" src="<?php echo $static_url?>/static/js/jquery.scrollTo1.4.2-min.TopLink.js"></script>
<script type="text/javascript" src="<?php echo $static_url?>/static/js/jquery.floatingbanner.js"></script>
<script type="text/javascript">
$(document).ready(function () {
	$('.toolbar_box').floatingbanner();
    var totalChars      = <?php echo MAX_CHART; ?>;
    var countTextBox    = $('#summarytxt')
    var charsCountEl    = $('#countchars');
    CheckChars(totalChars,countTextBox,charsCountEl); //call function to check text already in textarea

    countTextBox.keyup(function() {  // call function on keyup
        CheckChars(totalChars,countTextBox,charsCountEl);
    }).mouseout(function() { // on mouseout
       CheckChars(totalChars,countTextBox,charsCountEl);
    });
});

function CheckChars(totalChars,countTextBox,charsCountEl){
    charsCountEl.text(totalChars);
    var thisChars = countTextBox.val().replace(/{.*}/g, '').length;
    if(thisChars > totalChars){
        var CharsToDel = (thisChars-totalChars);
        countTextBox.val(countTextBox.val().substring(0,countTextBox.val().length-CharsToDel));
        charsCountEl.text( totalChars - countTextBox.val().length );
    }else{
        charsCountEl.text( totalChars - thisChars );
    }
}
</script>
<!-- END: JavaScript Area -->
</body>
</html>
<?php
#print_r($_SESSION);
?>