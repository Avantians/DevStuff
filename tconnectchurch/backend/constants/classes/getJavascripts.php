<?php
/** -------------------------------------------------------------------------
 * This program is Open Source; you can redistribute it
 * and/or modify it under the terms of the GNU General Public License
 * This program is coded in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY
 * @package  CMS
 * @author      Kenwoo - iweb@kenwoo.ca
 * @license    http://creativecommons.org/licenses/by/2.0/ Creative Commons
 *
 * [v02-02/07/2011]:: Set flag, not allow to direct access
 *  ------------------------------------------------------------------------- */
defined( "_VALID_MOS" ) or die( "Your system is not working properly." );

class getJavascripts {

	public $basicURL;
	public $static_url;

	public function __construct( $baseURL ){
		$this->basicURL	= $baseURL;
		$this->static_url 	= rtrim( str_replace( "www", CONFIG_STATIC_SUBDOMAIN, str_replace( "/backend", "", $this->basicURL ) ), "/" );
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To display Error message
 *  ------------------------------------------------------------------------- */
	public function  setMessage( $error_message ){
		$message_size	= sizeof( $error_message );
		$msg 				= "<div class='warning'>\n";
		for( $i=0; $i < $message_size; $i++ ){
			$msg .= $error_message[$i]."<br />";
		}
		$msg .= "</div>";

		return $msg;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To select multiple files
 *  ------------------------------------------------------------------------- */
	public function setMultiFile(){
		$jscript =	"\n<script type=\"text/javascript\" src=\"". $this->static_url . CONFIG_STATIC_SUBFOLDER ."/js/jquery.MultiFile.pack.js\"></script>";

		return $jscript;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Redirect JS
 *  ------------------------------------------------------------------------- */
	public function setRedirectJScript( $nodeValue, $sxtra = "", $cxtra = "", $pxtra ="" ){
		$jscript =	"\n<script type=\"text/javascript\">parent.location=\"".$this->basicURL."indexx.php?node={$nodeValue}{$sxtra}{$cxtra}{$pxtra}\"</script>";

		return $jscript;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Redirect JS with ID
 *  ------------------------------------------------------------------------- */
	public function setRedirectJScriptID( $nodeValue, $sxtra = "", $cxtra = "", $pxtra ="", $ixtra ="" ){
		$jscript =	"\n<script type=\"text/javascript\">parent.location=\"".$this->basicURL."indexx.php?node={$nodeValue}{$sxtra}{$cxtra}{$pxtra}{$ixtra}&m=view\"</script>";

		return $jscript;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Ajax to load categories list
 *  ------------------------------------------------------------------------- */
	public function setAjaxJScript( $opt="", $m="" ){
		global $Bon_db;

		$filename = _getCheckNullorNot( $m ) ?  $m : "categories" ;
		$typeame = _getCheckNullorNot( $m ) ?  "menutype" : "sectionid" ;

		$this->basicURL  = rtrim( str_replace( "backend", "static", $this->basicURL ), "/" );
		$jscript =
"\n<script type=\"text/javascript\">
	$( document ).ready( function(){
		$( \"#stylizedForm\" ).get( 0 ).reset();

			$( \"#{$typeame}\" ).change( function(){
				var id=$( this ).val();
				var dataString = \"{$typeame}=\"+ id;
				$.ajax( {
						type: \"GET\",
						url: \"".$this->basicURL."/get_{$filename}.php?op={$opt}\",
						data: dataString,
						cache: false,
						success: function( html ){
							$( \".categoriesid\" ).html( html );
						}
				} );
			} );
	} );
</script>";

		return $jscript;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Check all JS
 *  ------------------------------------------------------------------------- */
	public function setExtraJScript(){

		$jscript =
"<script type=\"text/javascript\">
	<!--
	function listCheckAll(){
		form = document.anybovard;
		listTotal = form.elements[\"boardNum[]\"].length;

		if( form.elements[\"master\"].checked== true ){
			for( var i=0; i < listTotal; i++ ){
				form.elements[\"boardNum[]\"][i].checked = true;
			}
		}else{
			for( var i=0; i < listTotal; i++ ){
				form.elements[\"boardNum[]\"][i].checked = false;
			}
		}
	}
	//-->
</script>";

		return $jscript;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Calender JS
 *  ------------------------------------------------------------------------- */
		public function setBootstraptDatepickJScript(){
		$jscript  = "<link rel=\"stylesheet\" href=\"".$this->static_url . CONFIG_STATIC_SUBFOLDER ."/css/bootstrap-datepicker.css\" type=\"text/css\" media=\"screen\"/>\n";
		$jscript .= "<script type=\"text/javascript\" src=\"".$this->static_url . CONFIG_STATIC_SUBFOLDER ."/js/bootstrap-datepicker.js\"></script>\n<script type=\"text/javascript\">\n$(document).ready(function () {\n\t$('#casting_date').datepicker({ format: 'yyyy-mm-dd' })\n\n\t$('#publishing_date').datepicker({ format: 'yyyy-mm-dd' })\n});\n</script>\n";

		return $jscript;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To delete the article JS
 *  ------------------------------------------------------------------------- */
	public function setDeleteJScript(){

		$jscript =
"\n<script type=\"text/javascript\">
	$( document ).ready( function(){
		$( \".deletebutton\" ).click( function(){
			  var result = confirm( \"Are you sure you want to delete?\" );
			  if( result == true ){
				   return true;
			  }else{
				   return false;
			  }
		  } ); // ends the delete.click
	} );
</script>";

		return $jscript;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To select menu item()s) JS
 *  ------------------------------------------------------------------------- */
	public function setMenuItemsJScript( $sId = "selections"){

		$jscript =
"\n<script type=\"text/javascript\">
					function allselections() {
						var e = document.getElementById('".$sId."');
							e.disabled = false;
						var i = 0;
						var n = e.options.length;
						for (i = 0; i < n; i++) {
							e.options[i].disabled = false;
							e.options[i].selected = true;
						}
					}
					function disableselections() {
						var e = document.getElementById('".$sId."');
							e.disabled = true;
						var i = 0;
						var n = e.options.length;
						for (i = 0; i < n; i++) {
							e.options[i].disabled = true;
							e.options[i].selected = false;
						}
					}
					function enableselections() {
						var e = document.getElementById('".$sId."');
							e.disabled = false;
						var i = 0;
						var n = e.options.length;
						for (i = 0; i < n; i++) {
							e.options[i].disabled = false;
						}
					}
</script>";

		return $jscript;
	}

/** -------------------------------------------------------------------------
 * [03/10/2014]::To put TinyMCE
 *  ------------------------------------------------------------------------- */
	public function setEditor( $selector = "tinymce", $editor_selector = "#fulltxt", $option = "advance" ){

		if( $selector == "tinymce" ){
			$editor = "\n<script type=\"text/javascript\" src=\"". $this->static_url . CONFIG_STATIC_SUBFOLDER ."/js/tinymce/tinymce.min.js\"></script>";
			if( $option === "simple" ){
				$editor .=
"\n<script type=\"text/javascript\">
	tinyMCE.init( {
    selector: '{$editor_selector}',
    plugins: \"link charmap code fullscreen\",
    toolbar: \"undo redo | styleselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | forecolor backcolor | bullist numlist outdent indent | charmap | preview code fullscreen\",
	setup :
		function(ed) {
			ed.on('init', function() {
				this.getDoc().body.style.fontSize = '13px';
			});
		},
	relative_urls: false,
	image_advtab: true,
    menubar: false,
    toolbar_items_size: \"small\",
    relative_urls: false
	} );
</script>";
				} else {
					$editor .=
"\n<script type=\"text/javascript\">
tinymce.init({
    selector: \"{$editor_selector}\",
	theme: \"modern\",
    plugins: [
        \"advlist autolink lists link image charmap preview hr anchor pagebreak\",
        \"searchreplace visualblocks visualchars code fullscreen\",
        \"insertdatetime media nonbreaking save table contextmenu directionality\",
        \"emoticons paste textcolor filemanager\"
    ],
    toolbar: \"undo redo | styleselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | forecolor backcolor | bullist numlist outdent indent | charmap emoticons | preview code | link unlink anchor image media | formatselect | fontselect | fontsizeselect | searchreplace | table | nonbreaking hr removeformat | subscript superscript | fullscreen\",
	setup :
		function(ed) {
			ed.on('init', function() {
				this.getDoc().body.style.fontSize = '13px';
			});
		},
	relative_urls: false,
	image_advtab: true,
    menubar: false,
    toolbar_items_size: \"small\",
	filemanager_title:\"File Manager\" ,
   external_filemanager_path:\"".UNDER_SUBFOLDER."/static/filemanager/\",
   external_plugins: { \"filemanager\" : \"".UNDER_SUBFOLDER."/static/js/tinymce/plugins/filemanager/plugin.min.js\"}
});
</script>";
			}
		}

		return $editor;
	}
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!