<?php
/** -------------------------------------------------------------------------
 * This program is Open Source; you can redistribute it
 * and/or modify it under the terms of the GNU General Public License
 * This program is coded in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY
 * @package CMS
 * @author  Kenwoo - iweb@kenwoo.ca
 * @license http://creativecommons.org/licenses/by/4.0/ Creative Commons
 *
 * [v02-02/07/2011]:: Set flag, not allow to direct access
 * ----------------------------------------------------------------------- */
defined( "_VALID_MOS" ) or die( "Your system is not working properly." );

class getArticles {

	public $dbConnect;
	public $basicURL;
	public $static_url;
	public $error_flag;
	public $error_message;
	public $contents;
	public $editor;

	public function __construct( $basicURL ){
		global $Bon_db;

		$this->dbConnect	 				= $Bon_db;
		$this->basicURL						= $basicURL;
		$this->static_url 						= rtrim( str_replace( "www", CONFIG_STATIC_SUBDOMAIN, str_replace( "/backend", "", $this->basicURL ) ), "/" );
		$this->error_flag						= false;
		$this->error_message	= array();
		$this->contents							= array();
		$this->editor									=
"\n<script type=\"text/javascript\" src=\"". $this->static_url . CONFIG_STATIC_SUBFOLDER ."/js/tinymce/tinymce.min.js\"></script>
<script type=\"text/javascript\">
	tinyMCE.init( {
    selector: \"#article\",
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
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 * ------------------------------------------------------------------------- */
	public function postForm( $pID, $backtourl = "", $mode = "" ){
		global $_ci_path;

		//To check session / login required to post article.
		//If there is no session, it will redirect to login page - !_getCheckNullorNot($_SESSION) means empty($_SESSION)
		if ( !_getCheckNullorNot($_SESSION) && !_getCheckNullorNot($_SESSION['session_user_id']) ){
			$_SESSION['reffer'] = $backtourl;
			_getRedirect( $this->basicURL.$backtourl."/login" );
			die();
		}
		else {
			$accessLevel				= getAllElements::setAccesslevel("ulevel");
			$groupLevel 					= getAllElements::setAccesslevel("gid");
			$buttonlink							="parent.location='{$this->basicURL}{$backtourl}'";
			//To set Captcha Number randomly
			$captchanumber	= rand( 0,9999 );
			$captch2	= md5( $captchanumber ).'a4xn';
			//To set JavaScript for editor - TinyMCE
			$formPrint							= "\n<script type=\"text/javascript\" src=\"". $this->static_url ."/static/js/jquery.MultiFile.pack.js\"></script>\n<!--//Bof Contents //-->\n";
			$formPrint			   		 .= CONFIG_USE_TXT_EDITOR ? $this->editor : "";
		}

		if ( $pID['pid'] != Null && $pID['pid'] != 0 ){
				//Get all page information by ID
				$page_items		= $this->dbConnect->getAllContents( "pages", "id = '{$pID['pid']}' AND publish = '1' AND status = '1' AND access_level >= '". $accessLevel ."' AND group_level >= '". $groupLevel ."'" );
				//It will get categories list based on SESSION ID
				$SectionID					= _getCheckNullorNot( $page_items['sectionid'] ) ? $page_items['sectionid'] : "1";
				$sections_array	= $this->dbConnect->getSectionsList( "publish = '1' ORDER BY ordering" );
				//Get category list in array
				if ( $SectionID == 1 ){
					$categories_array = $this->dbConnect->getCategoriesList( "publish = '1' AND section	!= '1' AND access_level >= '". $accessLevel ."' AND group_level >= '". $groupLevel  ."' ORDER BY ordering" );
				}
				else {
					$categories_array = $this->dbConnect->getCategoriesList( "publish = '1' AND section = '{$SectionID}' AND access_level >= '". $accessLevel ."' AND group_level >= '". $groupLevel  ."' ORDER BY ordering" );
				}
				//Display page text or image TITLE with or without link
				if ( CONFIG_SHOW_TITLE === "true" ){
						if ( $page_items['showtitle'] == 1 ){
								if ( _getCheckNullorNot( $page_items['title_images'] ) && CONFIG_USE_IMAGE_PAGE_TITLE === "true" ){
									$formPrint .= "\n<h1 class=\"pages_img_title\">". getDisplayImg( $page_items['title_images'], 500, false, $page_items['title'], false ) ."</h1>";
								}
								else {
									$formPrint .= ( CONFIG_USE_TXT_LINK === "true" ) ? "\n<h1><a href=\"".$this->basicURL.getAllElements::setJustci_path( $_ci_path )."\" target=\"_self\" title=\"". stripslashes( $page_items['title'] ) ."\" onfocus=\"this.blur()\">". stripslashes( $page_items['title'] ) ."</a></h1>" : "\n<h1>".stripslashes( $page_items['title'] )."</h1>";
								}
						}
				}

				if ( _getCheckNullorNot( $mode ) && $mode === "edit" ){
					$sqry					= "SELECT tbname FROM sections WHERE publish = '1' AND status = '1' AND id = '". $page_items['sectionid'] ."'";
					$sObject 	= $this->dbConnect->getObject( $sqry );
					$count 			= $this->dbConnect->getTotalNumber( $sObject->tbname, "id = '{$pID['tid']}' AND publish='1' AND status='1' AND access_level >= '". $accessLevel ."' AND group_level >= '". $groupLevel  ."' AND created_by = '". $_SESSION['session_user_id'] ."'" );

					if ( $count == 1 ){
						$query 					= "SELECT * FROM ". $sObject->tbname ." WHERE id = '". $pID['tid'] ."' AND publish='1' AND status='1' AND access_level >= '". $accessLevel ."' AND group_level >= '". $groupLevel  ."' AND created_by = '". $_SESSION['session_user_id'] ."'";
						$valueObject = $this->dbConnect->getObject( $query );
						$extraform		= "\n<input type=\"hidden\" name=\"mode\" value=\"edit\" />";

						if ( _getCheckNullorNot( $valueObject->filename ) ){
							$extraform_file	= "\n<input type=\"hidden\" name=\"xfile\" value=\"". $valueObject->filename .":". $valueObject->filesize ."\" />";
							$file_array 				= explode( ";", $valueObject->filename );
							for ( $w=0; $w < count( $file_array ); $w++ ){
								$divider = $w == count( $file_array )-1 ? "":"<br />";
								$valueObject_filename .= "Del <input type=\"checkbox\" value=\"{$file_array[$w]}\" name=\"dFile[]\">&nbsp;-&nbsp;&nbsp;". $file_array[$w] . $divider;
							}
						}
						else {
								$valueObject_filename = "";
						}
						$optInfo 			= $valueObject->id;
						$submitTxt	= "Update";
					}
					else {
						echo "<script type=\"text/javascript\"> alert( 'You cannot edit this article. This has been written by someone else.' ); window.history.go( -1 ); </script>\n";
					}
				}
				else {
					$submitTxt = "Submit";
				}

		}
		else {
			//Display POST page TITLE
			$formPrint .= "\n<h1><a href=\"". $this->basicURL.getAllElements::setJustci_path( $_ci_path ) ."#\" title=\"Post an article\" target=\"_self\">Post an article</a></h1>";
		}

		$formPrint .= "\n<form action=\"\" id=\"stylizedForm\" name=\"postform\" method=\"post\" enctype=\"multipart/form-data\">"
							."\n<lable for=\"cetegories\" class=\"category_lable darkOrange_txt\">Category:</lable>"
							."\n".getAllElements::setPullDownMenu( "categories", $categories_array ,$page_items['categoriesid'], "class=\"blue\"" )

							."\n<lable for=\"title\" class=\"darkOrange_txt\">Title:</lable><span id=\"titleInfo\" class=\"formtxt\"></span>"
							."\n<input id=\"title\" type=\"text\" name=\"title\" class=\"extra53\" value=\"{$valueObject->title}\" />"

							."\n<lable for=\"article\">Contents:</lable>"
							."\n<textarea id=\"article\" name=\"article\" class=\"mceEditor\">".$valueObject->fulltxt."</textarea><br />"

							."\n<lable for=\"urls\">Link:</lable><small>&nbsp;&nbsp;<strong>http://</strong>www.domain.com/URL</small>"
							."\n<input id=\"urls\" type=\"text\" name=\"urls\" class=\"extra53\" value=\"{$valueObject->urls}\" />"

							."\n<lable for=\"embed_code\">Embed:</lable>&nbsp;&nbsp;<small>Embed Code from YouTube or Vimeo</small>"
							."<textarea id=\"embed_code\" name=\"embed_code\" class=\"Threeline\">".$valueObject->embed_code."</textarea>"

							."\n<lable for=\"filename\">File:</lable>&nbsp;&nbsp;<small>( Maximum : 5M )</small>"
							."\n<input id=\"filename\" type=\"file\" name=\"filename[]\" size=\"20\" class=\"multi\" maxlength=\"3\" />";

		if ( _getCheckNullorNot($valueObject_filename)){
			$formPrint .= "<br/><small>".$valueObject_filename."</small>";
		}

		$formPrint .= "\n<br/><lable for=\"captcha\" class=\"darkOrange_txt\">Verification Number is <strong class=\"orange\">".$captchanumber."</strong></lable> &nbsp;&nbsp;(Please type the number below.)"
							."\n<input id=\"captcha\" type=\"text\" name=\"captcha\" class=\"extra100\" />"

							."\n<br/>\n<input type=\"hidden\" name=\"returnurl\" value=\"".$backtourl."\" />"
							."\n<input type=\"hidden\" name=\"verifiedid\" value=\"".$captch2."\" />"
							."\n<input type=\"hidden\" name=\"option\" value=\"".$optInfo."\" />"
							."\n<input type=\"hidden\" name=\"sections\" value=\"".$SectionID."\" />"
							."\n<input type=\"hidden\" name=\"pid\" value=\"".$pID['pid']."\" />"
							."\n<input type=\"hidden\" name=\"press\" value=\"doing\" />". $extraform . $extraform_file
							."\n<a onClick=\"{$buttonlink}\" class=\"btn-base graybtn curs floatright expadding\">Cancel</a>&nbsp;&nbsp;<input type=\"submit\" name=\"submitbutton\" class=\"btn-base skybluebtn floatright\" value=\"{$submitTxt}\" />"
							."\n</form>"
							."\n<script type=\"text/javascript\" src=\"".$this->static_url."/static/js/article-validation.js\"></script>";
		$formPrint .= "\n<!--//Eof Contents //-->\n";

		return $formPrint;
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 * ------------------------------------------------------------------------- */
	public function deleteProcess( $pID, $backtourl="", $mode="" ){
		#To check session / login required to post article. If there is no sesion, it will redirect to login page
		if ( !_getCheckNullorNot( $_SESSION['session_user_id'] ) && $_SESSION['guest'] == 1 ){
			$_SESSION['reffer'] = $backtourl;
			_getRedirect( $this->basicURL.$backtourl."/login" );
		}

		$buttonlink		="parent.location='{$this->basicURL}{$backtourl}'";
		$idcheck		= $this->dbConnect->getAllContents( $pID['tbname'], "id = '{$pID['tid']}' AND publish = '1' AND status = '1' AND access_level >= '".getAllElements::setAccesslevel("ulevel")."' AND group_level >= '".getAllElements::setAccesslevel("gid")."'" );

				if ( _getCheckNullorNot( $pID['password'] ) ){
					if ( $pID['password'] === $idcheck['password'] ){
							if ( $idcheck['created_by'] == $_SESSION['session_user_id'] ){
								 $this->dbConnect->getQuery( "DELETE FROM {$pID['tbname']} WHERE id = '{$pID['tid']}'" );
								 $this->dbConnect->getQuery( "DELETE FROM opensef WHERE tid = '{$pID['id']}' AND tbname = '{$pID['tbname']}' AND external = '{$pID['external']}'" );
								//$GeneralItems = new getGeneral( $this->basicURL );
								//$returnURL = $GeneralItems->getMenuURL( $pID['pid'] );
								//$returnURL_tail = ( _getCheckNullorNot( $pID['page_no'] ) && $pID['page_no'] > 1 ) ? "&".$pID['page_no'] : "";
								//$reffer = $returnURL['alias'].$returnURL_tail;
								unset( $pID );
								_getRedirect( $backtourl );

							}
							else {
								unset( $pID );
								echo "<script type=\"text/javascript\"> alert( 'You cannot delete the article because this was written by someone else!' ); window.history.go( -1 ); </script>\n";
								exit();
							}
					}
					else {
						unset( $pID );
						echo "<script type=\"text/javascript\"> alert( 'You cannot delete the article due to wrong password!' ); window.history.go( -1 ); </script>\n";
						exit();
					}
				}
				else {
					$formPrint = '<script type="text/javascript">
												$( document ).ready( function(){
													$( ".submitbutton" ).click( function(){
														 //this will display a confirm ( ok/cancel ) pop-up box
														 var result = confirm( "Are you sure you want to delete?" );
														 if ( result == true ){
															 //this will allow the form to submit
															 return true;
														 }else {
															 //this will stop form from submitting
															 return false;
														 }
													 } ); //ends the delete.click
												} );
												</script>';
					$formPrint .= "\n<!--//Bof Contents //-->\n<dl class=\"pages_component\">"
										."\n<dd class=\"pages_txt_title\">Delete an article</dd>"
										."\n<dd class=\"pages_article_title\">You are going to delete \"<strong>{$idcheck['title']}</strong>\".</dd>"
										."\n<dd class=\"pages_formarea\">"
										."\n<form action=\"\" id=\"stylizedForm\" name=\"postform\" method=\"post\" enctype=\"multipart/form-data\" style=\"width:400px;margin:50px 0 0 150px;\">"
										."\n<lable for=\"password\" class=\"darkOrange_txt extra400\">Please enter the password:</lable>\n<input id=\"password\" type=\"password\" name=\"password\" class=\"\" />"

										."\n<input type=\"submit\" name=\"submitbutton\" class=\"submitbutton\" value=\"submit\" />&nbsp;&nbsp;<a onClick=\"{$buttonlink}\" class=\"cancelbutton\">Cancel</a>"
										."\n</form>"
										."\n</dd>"
										."\n</dl>\n<!--//Eof Contents //-->\n";
				}

		unset( $pID );
		return $formPrint;
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 * ------------------------------------------------------------------------- */
	public function postProcess( $allValues ){
		global $user_ip;

		if ( $allValues['error_flag'] == false ){
			if ( !_getCheckNullorNot( $allValues['categories'] ) OR $allValues['categories'] == 0 ){
				echo "<script type=\"text/javascript\"> alert( 'Please select a category!' ); window.history.go( -1 ); </script>\n";
				exit();
			}

			if ( $_FILES[filename][error][0] == 0 ){
				$uploading_file = getUploadMultiFile( $_FILES[filename] );

				for ( $j=0; $j < count( $uploading_file['name'] ); $j++ ){
					$divider = ( $j == count( $uploading_file['name'] )-1 ) ? "":";";

					$filename .= $uploading_file['name'][$j].$divider;
					$filetype   .= $uploading_file['type'][$j].$divider;
					$filesize   .= $uploading_file['size'][$j].$divider;
				}

				unset( $uploading_file );
			}

			foreach ( $allValues as $key => $kw ){
				//CONFIG_FORBAD_LANAGUAGE
				$va	= strtolower( $key );
				$$va	= $kw;
				unset( $allValues[$key] );
			}

			$section_qry		= "SELECT tbname FROM sections WHERE publish = '1' AND status = '1' AND id = '{$sections}'";
			$sectionObject 	= $this->dbConnect->getObject( $section_qry );

			if ( $submitbutton === "Update" && $mode === "edit" ){
				$query = "SELECT * FROM {$sectionObject->tbname} WHERE id = '{$option}' AND publish='1' AND status='1'";
				$valueObject = $this->dbConnect->getObject( $query );

				list( $year, $month, $date ) = explode( '-' , substr( $valueObject->created_date , 0 , 10 ) );
				$cviews	= $valueObject->views;
				$cvotes	= $valueObject->votes;
				$cdate	= $valueObject->created_date;

				if ( !_getCheckNullorNot( $filename ) && _getCheckNullorNot( $xfile ) ){
					$filename_array = explode( ":", $xfile );
					$filename			= $filename_array[0];
					$filesize			= $filename_array[1];
				}

				if ( count( $allValues['dfile'] ) > 0 ){
					$xfiles = substr( $allValues['xfile'], 0, strpos( $allValues['xfile'], ":" ) );
					$farray = explode( ";", $xfiles );
					for ( $no=0; $no < count( $allValues['dfile'] ); $no++ ){
						for ( $noz=0; $noz < count( $farray ); $noz++ ){
							if ( $farray[$noz] == $allValues['dfile'][$no] ){
								$xfiles = str_replace( $farray[$noz], "", rtrim( $xfiles, ";" ) );
								!@unlink( $_SERVER['DOCUMENT_ROOT'] . "/" . CONFIG_FILES_UPLOAD_ROOT . $farray[$noz] );
								clearstatcache();
							}
						}
					}
					$filename = trim( $xfiles, ";" );
				}
			}
			else {
				$cviews	= 0;
				$cvotes	= 0;
				$cdate	= "now()";
			}

			$sql_data_array = array(
 													'title'						=> $title,
													'fulltxt'					=> $article,
													//'linkfile'				=> $images,
													'filename'				=> $filename,
													'filesize'					=> $filesize,
													'urls'						=> $urls,
													'embed_code'			=> $embed_code,
													'sectionid'				=> $sections,
													'categoriesid'			=> $categories,
													'ordering'				=> "",
													'metatitle'				=> $title,
													'metakey'				=> $title,
													'metadesc'				=> $title,
													'access_level'		=> 7,
													'group_level	'		=> 1,
													'publish'					=> 1,
													'status'					=> 1,
													'notice'					=> 0,
													'views'					=> $cviews,
													'votes'					=> $cvotes,
													'password'				=> $password,
													'user_ip'					=> $user_ip,
													'modified_date'		=> "now()",
													'created_date'			=> $cdate,
													'created_by'			=> $_SESSION['session_user_id']
												 );

				if ( $submitbutton === "Update" && $mode === "edit" ){
					if ( _getCheckNullorNot( $title ) ){
						 $this->dbConnect->getDBAction( $sectionObject->tbname, $sql_data_array, "update", "id = {$option}" );
						$sef_sql_data_array = array(
																'tbname'				=> $sectionObject->tbname,
																'external'			=> "/".$month.$date.$option,
																'pid'					=> $pid,
																'direction'			=> "",
																'publish'				=> 1,
																'modified_date'	=> "now()"
															 );

						 $this->dbConnect->getDBAction( 'opensef', $sef_sql_data_array, "update", "tid = '{$option}' AND tbname = '{$sectionObject->tbname}'" );
						unset( $sql_data_array );
						unset( $sef_sql_data_array );

						$pageneo	= isset( $page_no ) ? "&".$page_no : "";
						$returnurl	= "/".$month.$date.$option.$pageneo;
					}
				}
				else {
					 $this->dbConnect->getDBAction( $sectionObject->tbname, $sql_data_array );
					$inserted_article_id = $this->dbConnect->getInsertID();

					$sef_sql_data_array = array(
															'tid'					=> $inserted_article_id,
															'tbname'				=> $sectionObject->tbname,
															'external'			=> "/".date( 'md' ).$inserted_article_id,
															'pid'					=> $pid,
															'direction'			=> "",
															'publish'				=> 1,
															'created_date'		=> "now()",
															'modified_date'	=> "now()",
															'hits'					=> 0,
															'notes'				=> ""
														 );

					 $this->dbConnect->getDBAction( 'opensef', $sef_sql_data_array );
					unset( $sql_data_array );
					unset( $sef_sql_data_array );
				}

			if ( CONFIG_EMAIL_NOTICATION_NEW_ARTICLE === "true" ){
				$email_subject	= "New article has been added - ".date( "F j, Y, g:i a" );
				$email_text		= $title." - SECTION: ".$sectionObject->tbname."<hr/><br/><br/>".$embed_code."<br/>".$filename."<br/>".$article."<br/><hr/>".$urls;
				_getSendEmail( CONFIG_SITE_NAME, CONFIG_SITE_EMAIL, $email_subject, $email_text, CONFIG_SITE_NAME, CONFIG_SITE_EMAIL );
			}
			_getRedirect( $this->basicURL.$returnurl );

		}
		else {
			_getRedirect( $this->basicURL.$returnurl );
		}
	}

}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!