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
 * [v02-11/25/2014]:: Set flag, not allow to direct access
 *  ------------------------------------------------------------------------- */
defined( "_VALID_MOS" ) or die( "Your system is not working properly." );

include_once (CONFIG_DOC_ROOT.'/constants/classes/getModules.php');

class getAdvertises extends getModules{
/*
    public function construct(){
        parent::__construct();
    } 
    */
/** -------------------------------------------------------------------------
 * [10/08/2014]:: To
 *  ------------------------------------------------------------------------- */
	public static function setAds( $pid, $pposition ){
		global $base_url;

		$pidquery			= self::$dbConnect->getQuery("SELECT * FROM advertises_pages WHERE id =  '".$pid."'  AND position = '".$pposition."' AND publish = '1' AND status = '1' ORDER BY ordering");
		while( $pg_items = self::$dbConnect->getFetch_Array($pidquery) ){
						$ads_query = self::$dbConnect->getQuery("SELECT * FROM advertises WHERE position = '{$pg_items['ads_group']}' AND publish = '1' AND status = '1' ORDER BY ordering");
						$count							= 1;
						$nailthumb			= false;
						$maxWidth 		= 60;
						while( $ads_items = self::$dbConnect->getFetch_Array($ads_query) ){
										$sdate = floor((time() - strtotime( $ads_items['starting_date'] )) / 86400);
										$edate = floor((strtotime( $ads_items['ending_date'] ) - time()) / 86400);
										$extra_class = _getCheckNullorNot( $ads_items['extra_class'] ) ? " class=\"".$ads_items['extra_class']."\"" : "";

										$src = $_SERVER['DOCUMENT_ROOT'] . UNDER_SUBFOLDER ."/". CONFIG_FILES_UPLOAD_ROOT . $ads_items['filename'];
										$image_url		= $base_url."/". CONFIG_FILES_UPLOAD_ROOT . $ads_items['filename'];
										if ( file_exists( $src ) ){
												list( $width, $height ) = getimagesize( $src );
												$widthsize  = ( $width >= $maxWidth ) ? round( ( $width / $maxWidth ) * 100 ) : $width;
												$heightsize = floor( ( $height / $width ) * $widthsize );
												$imagesource = " style=\"background-image: url('".$image_url."');\"";
										}

										if ( $sdate >= 0 && $edate >= 0){
												if ( _getCheckNullorNot( $ads_items['urls'] ) ){
														$advlist .= "\n<li".$extra_class.$imagesource.">". $pg_items['adsfulltxt'] ."<a href=\"". $this->getProperLink($ads_items['urls']) ."\" title=\"".stripslashes( $ads_items['title'] )."\" target=\"".$ads_items['target_window']."\">".$ads_items['fulltxt']."</a></li>";
												}
												else {
														$advlist .= "\n<li".$extra_class.$imagesource.">". $pg_items['adsfulltxt'] ."</li>";
												}
										}
										elseif ( $ads_items['starting_date'] == "0000-00-00" && $edate >= 0 ) {
												if ( _getCheckNullorNot( $ads_items['urls'] ) ){
														$advlist .= "\n<li".$extra_class.$imagesource.">". $pg_items['adsfulltxt'] ."<a href=\"". $this->getProperLink($ads_items['urls']) ."\" title=\"".stripslashes( $ads_items['title'] )."\" target=\"".$ads_items['target_window']."\">".$ads_items['fulltxt']."</a></li>";
												}
												else {
														$advlist .= "\n<li".$extra_class.$imagesource.">". $pg_items['adsfulltxt'] ."</li>";
												}
										}
										elseif ( $ads_items['starting_date'] == "0000-00-00" && $ads_items['ending_date']  == "0000-00-00" ) {
												if ( _getCheckNullorNot( $ads_items['urls'] ) ){
														$advlist .= "\n<li".$extra_class.$imagesource.">". $pg_items['adsfulltxt'] ."<a href=\"". $this->getProperLink($ads_items['urls']) ."\" title=\"".stripslashes( $ads_items['title'] )."\" target=\"".$ads_items['target_window']."\">".$ads_items['fulltxt']."</a></li>";
												}
												else {
														$advlist .= "\n<li".$extra_class.$imagesource.">". $pg_items['adsfulltxt'] ."</li>";
												}
										}
										$count++;
						}

						$inside_advlist = _getCheckNullorNot($pg_items['innerclass']) ? "\n<ul class=\"{$pg_items['innerclass']}\">". $advlist ."\n</ul>" : "\n<ul>". $advlist ."\n</ul>";
						$inside .= _getCheckNullorNot($pg_items['outsideclass']) ? "<!-- Bof Advertises List --><div class=\"{$pg_items['outsideclass']}\">". $inside_advlist ."\n</div><!-- Eof Advertises List -->" : "\n<!-- Bof Ads List -->". $inside_advlist ."\n<!--Eof Ads List -->";
		}

		return $inside;
	}
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!