<?php
class getJson {

	public static $basicURL;
	public static $error_flag;
	public static $error_message;
	public static $ref_number;
	public static $contents;
	public static $groupid;
	public static $userlevel;

	public function __construct( $basicURL ){
		global $base_url;

		self::$basicURL						= !_getCheckNullorNot($basicURL) ? $base_url : $basicURL;
		self::$error_flag					= false;
		self::$error_message	= array();
		self::$ref_number 				= date('is');
		self::$contents						= array();
		self::$groupid 							= ( isset($_SESSION['session_gid']) && _getCheckNullorNot($_SESSION['session_gid']) ) ? $_SESSION['session_gid'] : 1;
		self::$userlevel 						= ( isset($_SESSION['session_userlevel']) && _getCheckNullorNot($_SESSION['session_userlevel']) ) ? $_SESSION['session_userlevel'] : 7;
	}

/**
 * --------------------------------------------------------------------------------------------------------
 * KENWOO::[00/00/2011]:: To
 * --------------------------------------------------------------------------------------------------------
 */
	public function setProcess( $allValues ){
		global $Bon_db;

		$allValues = trim($allValues, '/');
		if( isset($allValues) && _getCheckNullorNot($allValues) && $allValues != "josn"){
			$haystack = rtrim(($allValues), '/');
		}

		header( "Content-Type: application/json; charset=UTF-8" );
		header( "Content-Encoding: gzip" );
		header( "Pragma: no-cache" );
		header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT'" );
		header( "Last-Modified: " . gmdate( 'D, d M Y H:i:s' ) . " GMT" );
		header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
		header( "Cache-Control: private",false); // required for certain browsers
		header( "Cache-Control: post-check=0, pre-check=0, max-age=0", false );
		header( "Last-Modified: ". gmdate('D, d M Y H:i:s', time()) ." GMT" );

		$elements = explode('/', $haystack);

		if($elements[0] == "menu"){
				$mquery = $Bon_db->getQuery("SELECT m.id, m.link, m.alias, m.mtitle, m.parent, m.pid, p.sectionid, p.categoriesid FROM menu m LEFT JOIN pages p ON m.pid = p.id WHERE m.menutype = 'topmenu' AND m.publish = '1' AND m.status = '1' AND m.parent = '0' AND m.hideshow = '1' ORDER BY m.ordering");

				while( $_menus1 = $Bon_db->getFetch_Array($mquery) ){
						if( _getCheckNullorNot($_menus1['link']) ){
								$pharse_url1	= explode("//", $_menus1['link']);
								$plink1						= $pharse_url1[0] == "http:" ? $_menus1['link'] : self::$basicURL.$_menus1['link'];
						}
						else {
								$plink1						= self::$basicURL.$_menus1['alias'];
						}

						$aquery 														= $Bon_db->getAllContents("categories", "id = '".$_menus1['categoriesid']."'");
						$aquery_ctype 									= _getCheckNullorNot( $aquery['ctype'] ) ? $aquery['ctype'] :  "page" ;
						$aquery_ctype 									= ( $aquery_ctype == "board" ) ?  "articles" : $aquery_ctype;
						$_menus1['categoriesid'] 	= $aquery_ctype == "page" ? 1 : $_menus1['categoriesid'];

						$sermonList_array[] 					= array(
																																		"listId"							=> "". $_menus1['id'] ."",
																																		"title"								=> "". stripslashes($_menus1['mtitle']) ."",
																																		"parentId"				=> "". $_menus1['parent'] ."",
																																		"sermonURL"		=> "". $plink1 ."",
																																		"submenuUrl"	=> "-",
																																		"categoryId"		=> "". $_menus1['categoriesid'] ."",
																																		"trigger"					=> "". $aquery_ctype .""
																												);

						if($Bon_db->getTotalNumber( "menu", "menutype = 'topmenu' AND publish = '1' AND status = '1' AND parent = '".$_menus1['id']."' AND hideshow = '1'") > 0){
							$squery = $Bon_db->getQuery("SELECT m.id, m.link, m.alias, m.mtitle, m.parent, m.pid, p.sectionid, p.categoriesid FROM menu m LEFT JOIN pages p ON m.pid = p.id WHERE m.menutype = 'topmenu' AND m.publish = '1' AND m.status = '1' AND m.parent = '".$_menus1['id']."' AND m.hideshow = '1' ORDER BY m.ordering	");
							while( $submenus = $Bon_db->getFetch_Array($squery) ){
								if( _getCheckNullorNot($submenus['link']) ){
										$pharse_url	= explode("//", $submenus['link']);
										$plink2			= $pharse_url[0] == "http:" ? $submenus['link'] : self::$basicURL.$submenus['link'];
								}
								else {
										$plink2			= self::$basicURL.$submenus['alias'];
								}

								$bquery																= $Bon_db->getAllContents("categories", "id = '".$submenus['categoriesid']."'");
								$bquery_ctype										= _getCheckNullorNot( $bquery['ctype'] ) ? $bquery['ctype'] :  "page" ;
								$bquery_ctype										= ( $bquery_ctype == "board" ) ?  "articles" : $bquery_ctype;
								$submenus['categoriesid']	= $bquery_ctype == "page" ? 1 : $submenus['categoriesid'];

								$sermonList_array[] 						= array(
																																					"listId"							=> "". $submenus['id'] ."",
																																					"title"								=> "". stripslashes($submenus['mtitle']) ."",
																																					"parentId"				=> "". $submenus['parent'] ."",
																																					"sermonURL"		=> "-",
																																					"submenuUrl"	=> "". $plink2 ."",
																																					"categoryId"		=> "". $submenus['categoriesid'] ."",
																																					"trigger"					=> "". $bquery_ctype .""
																														);

								if($Bon_db->getTotalNumber( "menu", "menutype = 'topmenu' AND publish = '1' AND status = '1' AND parent = '".$submenus['id']."' AND hideshow = '1'") > 0){

									$squery2 = $Bon_db->getQuery("SELECT m.id, m.link, m.alias, m.mtitle, m.parent, m.pid, p.sectionid, p.categoriesid FROM menu m LEFT JOIN pages p ON m.pid = p.id WHERE m.menutype = 'topmenu' AND m.publish = '1' AND m.status = '1' AND m.parent = '".$submenus['id']."' AND m.hideshow = '1' ORDER BY m.ordering");

									while( $submenus2 = $Bon_db->getFetch_Array($squery2) ){
										if( _getCheckNullorNot($submenus2['link']) ){
												$pharse_url	= explode("//", $submenus2['link']);
												$plink3 		= $pharse_url[0] == "http:" ? $submenus2['link'] : self::$basicURL.$submenus2['link'];
										}
										else {
												$plink3			= self::$basicURL.$submenus2['alias'];
										}

										$cquery 																= $Bon_db->getAllContents("categories", "id = '".$submenus2['categoriesid']."'");
										$cquery_ctype 											= _getCheckNullorNot( $cquery['ctype'] ) ? $cquery['ctype'] :  "page";
										$cquery_ctype												= ( $cquery_ctype == "board" ) ?  "articles" : $cquery_ctype;
										$submenus2['categoriesid']	= $cquery_ctype == "page" ? 1 : $submenus2['categoriesid'];

										$sermonList_array[] 							= array(
																																								"listId"							=> "". $submenus2['id'] ."",
																																								"title"								=> "". stripslashes($submenus2['mtitle']) ."",
																																								"parentId"				=> "". $submenus2['parent'] ."",
																																								"sermonURL"		=> "-",
																																								"submenuUrl"	=> "". $plink3 ."",
																																								"categoryId"		=> "". $submenus2['categoriesid'] ."",
																																								"trigger"					=> "". $cquery_ctype .""
																																		);

									}
								}
							}
						}
				}

				$all_array = array(
																				"name" 							=> "Milal Menu - Milalchurch App",
																				"main" 							=> "json.php",
																				"linked time" 									=> "". time() ."",
																				"webMaster" 		=> "milalweb@gmail.com",
																				"language" 				=> "UTF-8",
																				"lastBuildDate"	=> "". gmdate("D, d M Y H:i:s", time())." GMT" ."",
																				"homepage" 			=> "milalchurch.com",
																				"authors" 					=> array("Milal Web Team <milalweb@gmail.com>"),
																				"description" 			=> "Milal church applicaiton for sermons and columns. Menu",
																				"license" 						=> "MIT",
																				"copyright" 				=> "Copyright 2013, MilAlChurch.com",
																				"sermonList" 			=> $sermonList_array
														);
				//echo str_replace( "\\", "", json_encode( $all_array, JSON_PRETTY_PRINT ) );
				//echo json_encode( $all_array, JSON_PRETTY_PRINT );
				echo json_encode( $all_array, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_SLASHES );
		}
		else {

				$tbname 					= !_getCheckNullorNot($elements[0]) ? "casting" : $elements[0];
				$categoriesid 	= !_getCheckNullorNot($elements[1]) ? 2 : $elements[1];
				$page_no 				= !_getCheckNullorNot($elements[2]) ? 1 : $elements[2];
				$contentType	= ($tbname == "casting") ? 1 : 2;

				$list_tail 					= ($categoriesid != 0) ? " AND categoriesid = '".$categoriesid."'" : "";
				$total_list 				= $Bon_db->getTotalNumber( $tbname, "notice = '0' AND status = '1' AND publish = '1' AND access_level >= '".self::$userlevel."' AND group_level >= '".self::$groupid."' {$list_tail}");
				$total_notice 	= $Bon_db->getTotalNumber( $tbname, "notice = '1' AND status = '1' AND publish = '1' AND access_level >= '".self::$userlevel."' AND group_level >= '".self::$groupid."' {$list_tail}");
				$total_page 		= ceil($total_list/CONFIG_HOW_MANY_ARTICLES_PER_PAGE);
				$page_no 				= ($page_no > $total_page) ? $total_page : $page_no;
				$noPage 					= isset($page_no) ? $page_no : 1;
				$first 								= $total_list == 0 ? 1 : CONFIG_HOW_MANY_ARTICLES_PER_PAGE *($noPage-1);
				$last 								= $total_list == 0 ? 0 : CONFIG_HOW_MANY_ARTICLES_PER_PAGE * $noPage;
				$loop_number	= $total_list - ($noPage-1) * CONFIG_HOW_MANY_ARTICLES_PER_PAGE;

				$sqlportion 			= ($tbname == "articles") ? "b.id, b.title, b.title_alias, b.fulltxt, b.linkfile, b.filename, b.urls, b.embed_code, b.created_date, b.created_by, o.external, o.pid, m.alias" : "b.id, b.title, b.host, b.verse,b.fulltxt, b.linkfile, b.filename, b.urls, b.embed_code, b.casting_date, b.views, b.votes,o.external, o.pid, m.alias";
				$list_query 			= $Bon_db->getQuery("SELECT {$sqlportion} FROM {$tbname} b, opensef  o LEFT JOIN menu m ON o.pid = m.pid  WHERE b.id = o.tid AND o.tbname = '".$tbname."' AND o.publish = '1' AND m.status	= '1' AND b.access_level >= '".self::$userlevel."' AND b.group_level >= '".self::$groupid."' AND b.publish = '1' AND b.status = '1' {$list_tail}ORDER BY b.created_date DESC LIMIT {$first}, ".CONFIG_HOW_MANY_ARTICLES_PER_PAGE."");

				if( $Bon_db->getNumberRows($list_query) >0 ){
							while ($list_values = $Bon_db->getFetch_Array($list_query)) {

								if( _getCheckNullorNot($list_values['host']) ){
									$hosted_by	= stripslashes($list_values['host']);
								}
								else {
									$telements 	= explode("-", $list_values['title']);
									$hosted_by	= trim($telements[1]);
									$hosted_by = _getCheckNullorNot($hosted_by) ? stripslashes($hosted_by) : "-";
								}

								if( _getCheckNullorNot($list_values['casting_date']) ){
									$shortDate 	= _getShortFormatDate($list_values['casting_date']);
								}
								else {
									$telements 	= explode("]", $list_values['title']);
									$shortDate 	= trim($telements[0], "[");
									$shortDate 	= _getCheckNullorNot($shortDate) ? stripslashes($shortDate) : "-";
								}

								$f 														= self::$basicURL."/upload/".trim(urldecode($list_values['linkfile']), "/");
								$casting_mps					= _getCheckNullorNot($list_values['linkfile']) ? self::$basicURL."/download.php?fn=".trim(urldecode($list_values['linkfile']), "/") : "-";
								$casting_mps_size	= _getCheckNullorNot($list_values['filesize']) ? $list_values['filesize'] : getSizeFile("http://www.milalchurch.com/upload/".trim(urldecode($list_values['linkfile']), "/"));
								$casting_mps_size	= _getCheckNullorNot($casting_mps_size) ? $casting_mps_size : "-";

								if( _getCheckNullorNot($list_values['embed_code']) ){
									//Vimeo embed_code ONLY
									$without_tails 			= substr( $list_values['embed_code'], 0, strpos( $list_values['embed_code'] , "?" ) );
									$without_garbage	= str_replace( "<iframe src=\"http://player.vimeo.com/video/", "", $without_tails );
									$video_urlno	 				= "http://player.vimeo.com/video/". $without_garbage;
								}
								else {
									$video_urlno					= "-";
								}

								$pURL 														= ($noPage !== 1) ? self::$basicURL.$list_values['alias'].$list_values['external']."&amp;".$noPage : self::$basicURL.$list_values['alias'].$list_values['external'];
								$verses													=	(_getCheckNullorNot($list_values['verse'])) ? stripslashes($list_values['verse']) : "-";

								$allSermonList_array[]	= array(
																																		"contentType"	=> "". $contentType ."",
																																		"sermonId"			=> "". $list_values['id'] ."",
																																		"listId"							=> "". $loop_number ."",
																																		"title"								=> "". jentities( $list_values['title'] )."",
																																		"body"							=> "<![CDATA[". jentities( $list_values['fulltxt'] )."]]>",
																																		"date"							=> "". $shortDate ."",
																																		"sermoner"			=> "". $hosted_by ."",
																																		"passage"				=> "". stripslashes($verses) ."",
																																		"audioURL"			=> "". $casting_mps ."",
																																		"fileSize"					=> "". $casting_mps_size ."",
																																		"videoURL"			=> "". $video_urlno ."",
																																		"page"							=> "". $page_no."of".$total_page ."",
																																		"pageURL"				=> "". $pURL ."",
																																		"trigger"					=> "". $tbname .""
																											);
								$loop_number--;
							}
				}
				else{
					$allSermonList_array[]	=array("body"	=> "No data available. Please try to visit lator.");
				}

				$allSermon_array = array(
																									"name" 							=> $Bon_db->getCategoriesName($categoriesid) ." - Milalchurch App",
																									"main" 							=> "json.php",
																									"linked time" 			=> "". time() ."",
																									"webMaster" 		=> "milalweb@gmail.com",
																									"language" 				=> "UTF-8",
																									"lastBuildDate"	=> "". gmdate("D, d M Y H:i:s", time())." GMT" ."",
																									"homepage" 			=> "milalchurch.com",
																									"authors" 					=> array("Milal Web Team <milalweb@gmail.com>"),
																									"description" 			=> "Milal church applicaiton for sermons and columns.". $Bon_db->getCategoriesName($categoriesid)  ."",
																									"license" 						=> "MIT",
																									"copyright" 				=> "Copyright 2013, MilAlChurch.com",
																									"sermonList" 			=> $allSermonList_array
																		);
				echo json_encode( $allSermon_array, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_SLASHES );
			}

			exit();
	}
}

function jentities($string){

    $string = html_entity_decode(stripslashes($string), ENT_QUOTES, 'UTF-8');

		//replace numeric entities
		//$string = preg_replace_callback('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
		//$string = preg_replace_callback('~&#([0-9]+);~e', 'chr("\\1")', $string);

   return $string;
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!