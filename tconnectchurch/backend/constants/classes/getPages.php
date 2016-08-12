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
 * [v02-02/07/2011]:: Set flag, not allow to direct access
 *  ------------------------------------------------------------------------- */
defined( "_VALID_MOS" ) or die( "Your system is not working properly." );

class getPages {

	public $dbConnect;
	public $basicURL;
	public $static_url;
	public $setJS;
	public $setAllelements;
	public $error_flag;
	public $error_message;

	public $tbname;
	public $nodeValue;
	public $toolbarTitle;
	public $defaultSection;
	public $form;

	public function __construct( $baseURL ){
		global $Bon_db, $allElements;

		$this->dbConnect	 	= $Bon_db;
		$this->basicURL			= $baseURL;
		$this->static_url 		= rtrim( str_replace( "www", CONFIG_STATIC_SUBDOMAIN, str_replace( "/backend", "", $this->basicURL ) ), "/" );
		$this->setJS			= new getJavascripts($this->basicURL);
		$this->setAllelements	= $allElements;
		$this->error_flag		= false;
		$this->error_message	= array();

		$this->tbname			= "pages";
		$this->nodeValue		= "page";
		$this->toolbarTitle		= "Pages Manager";
		$this->defaultSection	= "1";
		$this->form				= $this->setJS->setDeleteJScript() . "\n<form id=\"stylizedForm\" name=\"anybovard\" method=\"post\" action=\"%s\" enctype=\"multipart/form-data\">%s\n</form>";
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get contents
 *  ------------------------------------------------------------------------- */
	public function getContents( $gvalue ){

		$_contents  = "\n<!--// Bof Main Contents //-->\n\n". $this->setJS->setExtraJScript() ."\n<dl>";
		$_contents .= "\n<dd class=\"pages_boardlist\">";
		$_contents .= $this->getBoardList( $gvalue );
		$_contents .= "\n</dd>";
		$_contents .= "\n</dl>\n<!--// Eof Main Contents //-->\n";

		return $_contents;
	}

/** -------------------------------------------------------------------------
 * [03/01/2014]::To get board list
 *  ------------------------------------------------------------------------- */
	public function  getBoardList( $gvalue ){

			$sxtra	 		= "";
			$cxtra	 		= ( isset( $gvalue['cid'] ) && _getCheckNullorNot( $gvalue['cid'] ) ) ? "&cid=".$gvalue['cid'] : "";
			$pxtra			= ( isset( $gvalue['p'] ) && _getCheckNullorNot( $gvalue['p'] ) ) ? "&p=".$gvalue['p'] : "";
			$gvalue_cid	= ( isset( $gvalue['cid'] ) && _getCheckNullorNot( $gvalue['cid'] ) ) ? " AND categories = '".$gvalue['cid']."'" : "";

			$_action_url	= $this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$cxtra}{$pxtra}";

			if( $gvalue['m'] === "update" ){
				$this->dbConnect->getQuery( "UPDATE {$this->tbname} SET status = {$gvalue['st']}, modified_date = 'now()'  WHERE id = '{$gvalue['id']}'" );
				echo $this->setJS->setRedirectJScript( $this->nodeValue, $sxtra, $cxtra, $pxtra );
			}

			if( _getCheckNullorNot( $gvalue['txt'] ) && $gvalue['search'] === "Search" ){
				$searchtxt = " AND title LIKE  '%" . $gvalue['txt'] ."%'";
			}
			else{
				if( isset( $gvalue['catid'] ) ){
					$cxtra	 = _getCheckNullorNot( $gvalue['catid'] ) ? "&cid=".$gvalue['catid'] : "";
					echo $this->setJS->setRedirectJScript( $this->nodeValue, $sxtra, $cxtra );
				}
			}

			$formbtns	= "<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" onfocus=\"this.blur()\" value=\"Copy\" /><input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"Update\" /><input type=\"submit\" name=\"submitbutton\" class=\"deletebutton\" value=\"Delete\" /><a href=\"".$this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$cxtra}{$pxtra}&amp;m=view"."\" onfocus=\"this.blur()\" class=\"abtn\"><span class=\"icon-32-new\" title=\"New\"></span>New</a>&nbsp;&nbsp;";

			$cateroies_ 		= $this->dbConnect->getCatListSct( "( stype = 'page' OR stype ='forms' ) AND publish = '1' ORDER BY ordering" );
			$total_list			= $this->dbConnect->getTotalNumber( $this->tbname, "publish = '1'" . $gvalue_cid .$searchtxt );
			$maxItem 			= ( isset( $gvalue['p'] ) && $gvalue['p'] === "all" ) ? $total_list : CONFIG_HOW_MANY_ARTICLES_PER_PAGE;

			$total_page		= ceil( $total_list/$maxItem );
			$noPage			= (isset( $gvalue['p'] ) && $gvalue['p'] !== "all") ?  $gvalue['p'] : 1;
			$first					= $total_list == 0 ? 1 : $maxItem *( $noPage-1 );
			$last					= $total_list == 0 ? 0 : $maxItem * $noPage;
			$loop_number	= $total_list - ( $noPage-1 ) * $maxItem;

			$_boardlist  = $this->setAllelements->setToolbar( $formbtns, "", $this->toolbarTitle );
			$_boardlist .= "\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tr><td valign=\"top\">";
			$_boardlist .= "\n\t<div class=\"totlaitemsright\">".$this->setAllelements->setPullDownMenuOnChange( "catid", $cateroies_ , $gvalue['cid'], "id=\"catid\" class=\"blue sfixedwidth\"" )."\t&nbsp;&nbsp;<input id=\"txt\" type=\"text\" name=\"txt\" value=\"\" class=\"extra180\"/>&nbsp;&nbsp;<input type=\"submit\" name=\"search\" class=\"submittxt\" value=\"Search\" /></div>";
			$_boardlist .= "\n\t<div class=\"totlaitemsleft\">Total item(s): <strong class=\"orangetxt\">{$total_list}</strong></div>";
			$_boardlist .= "\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">\n\t<tbody>";
			$_boardlist .= $this->getListFormat( "boardtitle" );

			$list_query = $this->dbConnect->getQuery( "SELECT * FROM {$this->tbname} WHERE publish = '1'{$searchtxt}". $gvalue_cid ." ORDER BY ordering LIMIT {$first}, ".$maxItem."" );
			while ( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
    			$bgClass 	= ( $loop_number/2 ) == floor( $loop_number/2 ) ? " class=\"list_odd\"" : " class=\"list_even\"";
				$pURL 		= $this->basicURL."indexx.php?node={$this->nodeValue}&amp;id={$list_values['id']}{$pxtra}{$cxtra}&amp;m=view";

				if( $list_values['sections'] == 0 && $list_values['categories'] == 0 && $list_values['frontpage'] == 0 ){
					$category_name = "page";
				}elseif( $list_values['frontpage'] == 1 ){
					$category_name = "<strong class=\"greentxt\">Front Page</strong>";
				}elseif( $list_values['frontpage'] == 0 ){
					$category_name = $this->dbConnect->getCategoriesName( $list_values['categories'] );
				}

				$created_date = _getShortFormatDate( $list_values['created_date'] );

				$_boardlist .= sprintf( $this->getListFormat( "boardlist" ), $bgClass, "<input type=\"checkbox\" value=\"{$list_values['id']}\" name=\"boardNum[]\">", $loop_number, $pURL, stripslashes( $list_values['title'] ), stripslashes( $list_values['title'] ), $category_name, "<input type=\"text\" size=\"3\" name=\"Short[{$list_values['id']}]\" value=\"{$list_values['ordering']}\" class=\"blue orderarea\">", $this->setAllelements->setPublishIcon( $this->nodeValue, $list_values['id'], $list_values['status'], $sxtra, $cxtra, $pxtra ), $list_values['views'], $list_values['id'], $created_date );

				$loop_number--;
			}

			$_boardlist .= "\n\t</tbody>\n\t</table>";
			$_boardlist .= $this->setAllelements->setPagelistblock( $this->nodeValue, $total_page, $noPage, $sxtra, $cxtra, $total_list, true );
			$_boardlist .= "\n</tr></table>";

			$_boardlistWithForm = sprintf( $this->form, $_action_url, $_boardlist );

			return $_boardlistWithForm;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get display
 *  ------------------------------------------------------------------------- */
	public function getDisplay( $gvalue ){

		$pxtra	= ( isset( $gvalue['p'] ) && _getCheckNullorNot( $gvalue['p'] ) ) ? "&p=".$gvalue['p'] : "";
		$cxtra	= ( isset( $gvalue['cid'] ) && _getCheckNullorNot( $gvalue['cid'] ) ) ? "&cid=".$gvalue['cid'] : "";
		$_action_url  = $this->basicURL."indexx.php?node={$this->nodeValue}{$pxtra}{$cxtra}";

		if( _getCheckNullorNot( $gvalue['id'] ) ){
			$valueObject	= $this->dbConnect->getContents( $this->tbname, "id = '{$gvalue['id']}' AND publish='1'" );
			$caValue		= explode( ";", $valueObject->categoriesid );

			$buttontxt		= "Update";
			$buttonlink		= "parent.location='".$this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$cxtra}{$pxtra}'";
			$presstxt 		= "update";
			$extrabtn 		= "<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"Copy\" /><input type=\"submit\" name=\"submitbutton\" class=\"deletebutton\" value=\"Delete\" />";
			$imgtitle 		= ( _getCheckNullorNot( $valueObject->title_images ) ) ? "\n".getDisplayImg( $valueObject->title_images, 700, false, $valueObject->title ) : "";

			if( !empty( $valueObject->filenames ) ){
				$file_array = explode( ";", $valueObject->filenames );
				for( $w=0; $w < count( $file_array ); $w++ ){
					$divider = $w == count( $file_array )-1 ? "":"<br>";
					$valueObject_filename .= "Delete <input type=\"checkbox\" value=\"{$file_array[$w]}\" name=\"dFile[]\">&nbsp;-&nbsp;&nbsp;".$file_array[$w].$divider;
				}
				$valueObject_filename = $valueObject_filename."<br />";
			}

			$cextra									= " AND section = '{$valueObject->sectionid}'";
			$valueObject_sectionid			= $valueObject->sections;
			$valueObject_categoriesid		= $valueObject->categories;
			$valueObject_frontpage			= $valueObject->frontpage;
			$valueObject_status				= $valueObject->status;
			$valueObject_access_level 		= $valueObject->access_level;
			$valueObject_group_level 		= $valueObject->group_level;
			$valueObject_ordering 			= $valueObject->ordering;
			$valueObject_readonly			= $valueObject->readonly_opt == 0 ? "" : "readonly";
		} else {
			$cextra									= " AND section = '0'";
			$buttontxt								= "Create";
			$buttonlink								= "parent.location='".$this->basicURL."indexx.php?node={$this->nodeValue}{$pxtra}{$cxtra}'";
			$presstxt 								= "create";
			$valueObject_frontpage			= 0;
			$valueObject_status				= 1;
			$valueObject_access_level 		= 7;
			$valueObject_group_level 		= 1;
			$valueObject_readonly			= "";
			$valueObject_ordering 			= $this->dbConnect->getTotalNumber( $this->tbname, "publish != '2' AND status != '2'" ) + 1;
			$valueObject_sectionid 			= _getCheckNullorNot( $gvalue['sid'] ) ? $gvalue['sid'] : $this->defaultSection;
			$valueObject_categoriesid 		= _getCheckNullorNot( $gvalue['cid'] ) ? $gvalue['cid'] : "1";
		}

		$sections_array0		= $this->dbConnect->getSectionsList( "publish = '1' AND ( stype = 'page' OR stype = 'forms' ) ORDER BY ordering" );
		$sections_array1		= $this->dbConnect->getSectionsList( "publish = '1' AND ( stype != 'page' AND stype != 'forms' ) ORDER BY ordering" );
		$cateroies_array0		= $this->dbConnect->getCategoriesList( "publish = '1' AND ( ctype = 'forms' OR ctype = 'page' ) ORDER BY ordering" );
		$cateroies_array1		= $this->dbConnect->getCategoriesList( "publish = '1' AND ctype != 'forms' AND ctype != 'page'{$cextra} ORDER BY ordering" );
		$slectclass				= $this->dbConnect->getTotalNumber( "categories", "publish = '1' AND ctype != 'forms' AND ctype != 'page'{$cextra} ORDER BY ordering" ) > 2 ? "multiselect " : "blue ";
		$memberGroup_array		= $this->dbConnect->getMemberGroup( "members_group_status = '1' ORDER BY members_group_id" );
		$accessLevel_array		= $this->dbConnect->getAccessLevel( "members_level_status = '1' ORDER BY members_level_id" );
		$yesno_array			= array( array( "id"=>"0","text"=>"NO" ), array( "id"=>"1","text"=>"YES" ) );
		$formbtns				= $extrabtn . "<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"{$buttontxt}\" /> <input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"Apply\" /> <a onClick=\"{$buttonlink}\" class=\"cancelbutton\">Cancel</a>&nbsp;&nbsp;";

		$_boarditem	 = $this->setAllelements->setToolbar( $formbtns, "", $this->toolbarTitle );
		$_boarditem	.= $this->setJS->setAjaxJScript( "multi" );
		$_boarditem	.= $this->setJS->setBootstraptDatepickJScript() . $this->setJS->setMultiFile();

		if( CONFIG_USE_TXT_EDITOR === "true"  ){
			$_boarditem .= ( $valueObject->useeditor == 1 ) ? $this->setJS->setEditor( "tinymce", "#fulltxt" ) : "";
		} else {
			$_boarditem .= "";
		}
		$_boarditem .=  "\n<dl class=\"pages_component\">\n\t<dd>";

		$_boarditem .= "\n\t<ul class=\"tabs\">";
		$_boarditem .= "\n\t\t<li><a href=\"#tcontentsa\" onfocus=\"this.blur()\"><span>Article</span></a></li>";
		$_boarditem .= "\n\t\t<li><a href=\"#tcontentsb\" onfocus=\"this.blur()\"><span>Attachment(s)</span></a></li>";
		$_boarditem .= "\n\t\t<li><a href=\"#tcontentsc\" onfocus=\"this.blur()\"><span>Parameters ( Advanced )</span></a></li>";
		$_boarditem .= "\n\t</ul>";

		$_boarditem .= "\n\t<div id=\"tcontentsa\" class=\"tab_content\"><br />";
		$_boarditem .=  $imgtitle
									."\n<table border=\"0\"><tr><td valign=\"top\"  align=\"right\"><lable for=\"sections\" class=\"darkOrange_txt txt80\">Section:</lable><small>Section</small>"
									."</td><td valign=\"top\">".$this->setAllelements->setPullDownMenu( "sections", $sections_array0, $valueObject_sectionid, "id=\"sections\" class=\"blue fixedwidth\"", "", true )."</td><td valign=\"top\" align=\"right\">"
									."<lable for=\"categories\" class=\"darkOrange_txt txt110\">Category:</lable><small>Category</small></td><td>".$this->setAllelements->setPullDownMenu( "categories", $cateroies_array0, $valueObject_categoriesid, "id=\"categories\" class=\"blue fixedwidth\"", "", true )."</td></tr></table>"
									."\n<lable for=\"title\" class=\"darkOrange_txt\">Title:</lable>"
									."\n<input id=\"title\" type=\"text\" name=\"title\" value=\"".$valueObject->title."\" class=\"extra53\"/><br />"

									."\n<lable for=\"fulltxt\" class=\"darkOrange_txt\" >Contents:</lable>"
									."\n<textarea id=\"fulltxt\" name=\"fulltxt\" class=\"mceEditor sizeset\"".$valueObject_readonly.">". htmlspecialchars( $valueObject->fulltxt ) ."</textarea><br />"

									."\n<table border=\"0\" align=\"left\"><tr><td valign=\"top\" align=\"right\" width=\"150\"><lable for=\"frontpage\" class=\"darkOrange_txt txt100\">Front page?</lable></td><td>".$this->setAllelements->setPullDownMenu( "frontpage", $yesno_array ,$valueObject_frontpage, "id=\"frontpage\" class=\"blue\"" )."</td><td valign=\"top\" width=\"50%\" align=\"right\" ><lable for=\"useeditor\" class=\"darkOrange_txt txt85\">Use editor?</lable></td><td>".$this->setAllelements->setPullDownMenu( "useeditor", $yesno_array ,$valueObject->useeditor, "id=\"useeditor\" class=\"blue\"" )."</td><td valign=\"top\"><lable for=\"status\" class=\"darkOrange_txt stypetxt\">Status:</lable></td><td>".$this->setAllelements->setPullDownMenu( "status", $yesno_array ,$valueObject_status, "id=\"status\" class=\"blue\"" )."</td></tr></table>\n\t</div>";

		$_boarditem .= "\n\t<div id=\"tcontentsb\" class=\"tab_content\"><br />";

		$_boarditem .= "\n<lable class=\"darkOrange_txt txt80p\" >If you want to put articles into this page, select setion  and category.</lable><table border=\"0\"><tr><td valign=\"top\"  align=\"right\"><lable for=\"sectionid\" class=\"darkOrange_txt txt80\">Section:</lable><small>Section to page</small></td><td valign=\"top\">".$this->setAllelements->setPullDownMenu( "sectionid", $sections_array1 ,$valueObject->sectionid, "id=\"sectionid\" class=\"blue fixedwidth\"", "", true )."</td><td valign=\"top\" align=\"right\">
		<lable for=\"categoriesid\" class=\"darkOrange_txt txt110\">Category:</lable><small>Category to page</small></td><td><div class=\"categoriesid\">".$this->setAllelements->setPullDownMenu( "categoriesid[]", $cateroies_array1 ,$caValue, "multiple=\"multiple\" size=\"7\" id=\"categoriesid\" class=\"{$slectclass}fixedwidth\"", "", true )."</div></td></tr></table><br/>"
											."\n<lable for=\"title_images\" class=\"darkOrange_txt\">Using title image?</lable><small>To use an image title instead of text title. Please <strong>DO NOT</strong> use white space( s ). <strong>ex ) filename_date-012.jpg</strong></small>"
									."\n<input id=\"titleimages\" type=\"text\" name=\"titleimages\" value=\"".$valueObject->title_images."\" class=\"extra26 floatleft\"/><input id=\"title_images\" type=\"file\" name=\"title_images\" size=\"30\" class=\"mspace2\" /><br class=\"clearfix\">"

								."\n<lable for=\"filename\" class=\"darkOrange_txt\">Attachment:</lable><small>To upload Max. 2 files. Please <strong>DO NOT</strong> use white space( s ). <strong>&nbsp;&nbsp;ex ) filename_date-012.jpg</strong></small>"
								."\n<input id=\"filename\" type=\"file\" name=\"filename[]\" size=\"20\" class=\"multi mspace\" maxlength=\"2\"  /><small>".$valueObject_filename."</small>"

									."\n<br><lable for=\"embed_code\" class=\"darkOrange_txt\">Embed code:</lable><small>To insert YOUTUBE Video in a page, please place embed code here.</small>"
									."<textarea id=\"embed_code\" name=\"embed_code\" class=\"Threeline\">".$valueObject->embed_code."</textarea><br />\n\t</div>";
									
		$_boarditem .= "\n\t<div id=\"tcontentsc\" class=\"tab_content\"><br />";
		$_boarditem .= "\n<table border=\"0\"><tr><td valign=\"top\" align=\"right\" width=\"19%\"><lable for=\"showtitle\" class=\"darkOrange_txt txt100 darkorange\">Display title? </lable></td><td>".$this->setAllelements->setPullDownMenu( "showtitle", $yesno_array ,$valueObject->showtitle, "id=\"showtitle\" class=\"blue\"" )."</td><td valign=\"top\" align=\"right\" colspan=\"2\">
									<table border=\"0\"><tr><td valign=\"top\" align=\"right\"><lable for=\"order\" class=\"darkOrange_txt ordertxt\">Order:</lable></td><td><input id=\"order\" type=\"text\" name=\"order\" value=\"".$valueObject_ordering."\" class=\"extra3\"  /></td><td valign=\"top\"><lable for=\"views\" class=\"darkOrange_txt viewtxt\">Views:</lable></td><td><input id=\"views\" type=\"text\" name=\"views\" value=\"".$valueObject->views."\"class=\"extra3\"  /></td></tr></table></td></tr><tr><td valign=\"top\" align=\"right\"><lable for=\"access_level\" class=\"darkOrange_txt txt90\">Access Level:</lable></td><td>".$this->setAllelements->setPullDownMenu( "access_level", $accessLevel_array ,$valueObject_access_level, "id=\"members_type\" class=\"blue sfixedwidth\"" )."</td><td valign=\"top\" align=\"right\" width=\"19%\"><lable for=\"group_level\" class=\"darkOrange_txt txt55\">Group:</lable></td><td>".$this->setAllelements->setPullDownMenu( "group_level", $memberGroup_array ,$valueObject_group_level, "id=\"group_level\" class=\"blue fixedwidth\"" )."</td></tr></table>\n<lable for=\"metatitle\" class=\"darkOrange_txt\" >Meta Title:</lable>"
									."<input id=\"metatitle\" type=\"text\" name=\"metatitle\" value=\"".$valueObject->metatitle."\" class=\"extra53\" /><br />"
									."\n<lable for=\"metakey\" class=\"darkOrange_txt\" >Meta Keyword:</lable>"
									."\n<input id=\"metakey\" type=\"text\" name=\"metakey\" value=\"".$valueObject->metakey."\" class=\"extra53\" /><br />"
									."\n<lable for=\"metadesc\" class=\"darkOrange_txt\" >Meta Description:</lable>"
									."\n<textarea id=\"metadesc\" name=\"metadesc\" class=\"Threeline\">".$valueObject->metadesc."</textarea>\t</div>";

		$_boarditem .=  "\n<input type=\"hidden\" name=\"id\" value=\"{$valueObject->id}\" />"
									."\n<input type=\"hidden\" name=\"xfile\" value=\"{$valueObject->filenames}:0\" />"
									."\n<input type=\"hidden\" name=\"press\" value=\"{$presstxt}\" />"
									."</dd>\n</dl>";

		$_boardlistWithForm = sprintf( $this->form, $_action_url, "\n<script type=\"text/javascript\" src=\"". $this->static_url . CONFIG_STATIC_SUBFOLDER ."/js/back-pages.js\"></script>".$_boarditem );

		$bItems  = "\n<!--// Bof Contents //-->";
		$bItems .= $_boardlistWithForm;
		$bItems .= "\n<!--// Eof Contents //-->\n\n";

		return $bItems;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get create
 *  ------------------------------------------------------------------------- */
	public function getCreate( $allValues ){

		$pxtra						= ( isset( $allValues['p'] ) && _getCheckNullorNot( $allValues['p'] ) ) ? "&p=".$allValues['p'] : "";
		$cxtra						= ( isset( $allValues['cid'] ) && _getCheckNullorNot( $allValues['cid'] ) ) ? "&cid=".$allValues['cid'] : "";
		$node						= $allValues['node'];
		$total_pages				= $this->dbConnect->getTotalNumber( $this->tbname, "publish = '1' AND status = '1'" );
		$allValues['order']		= !_getCheckNullorNot( $allValues['order'] ) ? $total_pages + 1 : $allValues['order'];
		$allValues['showtitle']	= !_getCheckNullorNot( $allValues['showtitle'] ) ? 1 : $allValues['showtitle'];

		for( $j=0; $j < count( $allValues['categoriesid'] ); $j++ ){
			$divider = ( $j == count( $allValues['categoriesid'] )-1 ) ? "":";";
			$categoriesids .= $allValues['categoriesid'][$j].$divider;
		}
		
		if( isset($_FILES) ){
			if( $_FILES[title_images][name] && $this->error_flag	 == false && $_FILES[title_images][error] == 0 ){
				$uploading_file	= getUploadingFile( $_FILES[title_images] );
				$title_imagesfile			= $uploading_file['name'];
				unset( $uploading_file );
	
				$allValues['titleimages'] = $title_imagesfile;
			}
	
			if( $_FILES[filename][error][0] == 0 ){
				$uploading_file = getUploadMultiFile( $_FILES[filename] );
	
				for( $j=0; $j < count( $uploading_file['name'] ); $j++ ){
						$divider = ( $j == count( $uploading_file['name'] )-1 ) ? "":";";
						$filename	.= $uploading_file['name'][$j].$divider;
						$filetype	.= $uploading_file['type'][$j].$divider;
						$filesize	.= $uploading_file['size'][$j].$divider;
				}
				unset( $uploading_file );
			}
		}

		if( $allValues['submitbutton'] === "Copy" && $allValues['press'] === "update" ){
				$cdate					= "now()";
				$cby = $_SESSION['session_user_id'];
				$extra_title			= "Copy - ";
				$allValues['views']	= 0;
				$re_status	= 0;

				if( !_getCheckNullorNot( $filename ) && _getCheckNullorNot( $allValues['xfile'] ) ){
						$filename_array	= explode( ":", $allValues['xfile'] );
						$filename 			= $filename_array[0];
						$filesize 			= $filename_array[1];
				}
		}
		elseif( ( $allValues['submitbutton'] === "Update" || $allValues['submitbutton'] === "Apply" ) && $allValues['press'] === "update" ){
			$query = "SELECT * FROM {$this->tbname} WHERE id = '{$allValues['id']}' AND publish='1'";
			$valueObject = $this->dbConnect->getObject( $query );
			list( $year, $month, $date ) = explode( '-' , substr( $valueObject->created_date , 0 , 10 ) );
			$cdate = $valueObject->created_date;
			$cby = $valueObject->created_by;

			if( _getCheckNullorNot( $allValues['xfile'] ) ){
				$filename_array	= explode( ":", $allValues['xfile'] );
				$xfilename		= $filename_array[0];
				$xfilesize		= $filename_array[1];
			}

			if( count( $allValues['dfile'] ) > 0 ){
					$xfiles = substr( $allValues['xfile'], 0, strpos( $allValues['xfile'], ":" ) );
					$farray = explode( ";", $xfiles );
					for( $no = 0; $no < count( $allValues['dfile'] ); $no++ ){
							for( $noz=0; $noz < count( $farray ); $noz++ ){
									if( $farray[$noz] == $allValues['dfile'][$no] ){
											$xfiles = str_replace( $farray[$noz], "", rtrim( $xfiles, ";" ) );
											!@unlink( $_SERVER['DOCUMENT_ROOT'] . "/" . CONFIG_FILES_UPLOAD_ROOT . $farray[$noz] );
											clearstatcache();
									}
							}
					}
					$xfilename = preg_replace( '/;;/', ";", $xfiles );
					$xfilename = trim( $xfilename, ";" );
				}

				$filename 	= trim( empty( $filename ) ? $xfilename : $xfilename .";".$filename, ";" );
				$re_status	= _getCheckNullorNot( $allValues['status'] ) ? $allValues['status'] : 1;
				$useeditor = $allValues['useeditor'];
				$metatitle	= _getCheckNullorNot( $allValues['metatitle'] ) ? $allValues['metatitle'] : $allValues['title'];
		}
		else{
				$re_status	= _getCheckNullorNot( $allValues['status'] ) ? $allValues['status'] : 1;
				$useeditor	= $allValues['useeditor'];
				$metatitle	= _getCheckNullorNot( $allValues['metatitle'] ) ? $allValues['metatitle'] : $allValues['title'];
				$cdate 		= "now()";
				$cby = $_SESSION['session_user_id'];
		}
		$filename = _getCheckNullorNot( $allValues['linkfile'] ) ? rtrim( $allValues['linkfile'].$filename, " ;" ) : rtrim( $filename, " ;" );

		$sql_data_array = array(
												'title'					=> $extra_title . $allValues['title'],
												'fulltxt'				=> $allValues['fulltxt'],
												'frontpage'  		=> $allValues['frontpage'],
												'showtitle' 			=> $allValues['showtitle'],
												'title_images'		=> $allValues['titleimages'],
												'filenames'			=> $filename,
												'embed_code'		=> $allValues['embed_code'],
												'sectionid'			=> $allValues['sectionid'],
												'categoriesid'		=> $categoriesids,
												'sections'			=> $allValues['sections'],
												'categories'		=> $allValues['categories'],
												'ordering'			=> $allValues['order'],
												'metatitle'			=> $metatitle,
												'metakey'			=> $allValues['metakey'],
												'metadesc'			=> $allValues['metadesc'],
												'access_level'	=> $allValues['access_level'],
												'group_level'		=> $allValues['group_level'],
												'publish'				=> 1,
												'status'	 			=> $re_status,
												'useeditor'			=> $useeditor,
												'views'				=> $allValues['views'],
												'modified_date'	=> "now()",
												'modified_by' 		=> $_SESSION['session_user_id'],
												'created_date'  	=> $cdate,
												'created_by'    	=> $cby,
												'publish_date'		=> "now()"
											 );

			if( ( $allValues['submitbutton'] === "Create" || $allValues['submitbutton'] === "Apply" ) && $allValues['press'] === "create" ){
					if( _getCheckNullorNot( $allValues['title'] ) ){
						$this->dbConnect->getDBAction( $this->tbname, $sql_data_array );
						$inserted_article_id = $this->dbConnect->getInsertID();
					}
					unset( $sql_data_array );

					$total_module = $this->dbConnect->getTotalNumber( "modules", "publish = '1' AND status = '1' AND select_opt LIKE '%all%'" );
					if( count( $total_module ) > 0 ){
						$module_query = $this->dbConnect->getQuery( "SELECT * FROM apps_pages WHERE publish = '1' AND params LIKE '%all#%'" );
						while ( $module_values = $this->dbConnect->getFetch_Array( $module_query ) ){
							$this->dbConnect->getQuery( "UPDATE apps_pages SET pageid = '".$module_values['pageid'] . ";[".$inserted_article_id."]"."' WHERE id = ".$module_values['id']."" );
						}
					}

					if( $allValues['submitbutton'] === "Create" ){
						echo $this->setJS->setRedirectJScript( $node, $sxtra, $cxtra, $pxtra );
					}
					elseif ( $allValues['submitbutton'] === "Apply" ){
						echo $this->setJS->setRedirectJScriptID( $node, $sxtra, $cxtra, $pxtra, "&id={$inserted_article_id}" );
					}
			}
			elseif( $allValues['submitbutton'] === "Copy" && $allValues['press'] === "update" ){
					if( _getCheckNullorNot( $allValues['title'] ) ){
						$this->dbConnect->getDBAction( $this->tbname, $sql_data_array );
					}
					unset( $sql_data_array );
					echo $this->setJS->setRedirectJScript( $node, $sxtra, $cxtra, $pxtra );
			}
			elseif( ( $allValues['submitbutton'] === "Update" || $allValues['submitbutton'] === "Apply" ) && $allValues['press'] === "update" ){
					if( _getCheckNullorNot( $allValues['title'] ) ){
						$this->dbConnect->getDBAction( $this->tbname, $sql_data_array, "update", "id = {$allValues['id']}" );
					}
					unset( $sql_data_array );

					if( $allValues['submitbutton'] === "Update" ){
						echo $this->setJS->setRedirectJScript( $node, $sxtra, $cxtra, $pxtra );
					}
					elseif ( $allValues['submitbutton'] === "Apply" ){
						echo $this->setJS->setRedirectJScriptID( $node, $sxtra, $cxtra, $pxtra, "&id={$allValues['id']}" );
					}
			}
			elseif( $allValues['submitbutton'] === "Delete" ){
					if( count( $allValues['boardnum'] ) > 0 ){
						foreach ( $allValues['boardnum'] as $key => $kw ){
							$this->dbConnect->getQuery( "UPDATE {$this->tbname} SET publish = '0' WHERE id = '{$kw}'" );
						}
					}
					else{
						if( _getCheckNullorNot( $allValues['id'] ) ){
							$this->dbConnect->getQuery( "UPDATE {$this->tbname} SET publish = '0' WHERE id = '{$allValues['id']}'" );
						}else{
							echo "<script type='text/javascript'>window.alert( 'Please select a PAGE from the list to delete.' );</script>";
						}
					}
					unset( $allValues );
					echo $this->setJS->setRedirectJScript( $node, $sxtra, $cxtra, $pxtra );
			}
			elseif( $allValues['submitbutton'] === "Update" ){
					if( count( $allValues['boardnum'] )>0 ){
						foreach( $allValues['boardnum'] as $key => $kw ){
							$this->dbConnect->getQuery( "UPDATE {$this->tbname} SET ordering = {$allValues['short'][$kw]} WHERE id = '{$kw}'" );
						}
					}
					else {
						echo "<script type='text/javascript'>window.alert( 'Please select a PAGE from the list to update.' );</script>";
					}
					unset( $allValues );
					echo $this->setJS->setRedirectJScript( $node, $sxtra, $cxtra, $pxtra );
			}
			elseif( $allValues['submitbutton'] === "Copy" ){
				if( count( $allValues['boardnum'] ) > 0 ){
					foreach ( $allValues['boardnum'] as $key => $kw ){
						$apObject = $this->dbConnect->getObject( "SELECT * FROM {$this->tbname} WHERE id = '{$kw}'" );
						$copy_sql_data_array = array(
																	'title'					=> "Copy - ".$apObject->title,
																	'fulltxt'				=> $apObject->fulltxt,
																	'frontpage'  		=> $apObject->frontpage,
																	'showtitle' 			=> $apObject->showtitle,
																	'title_images'		=> $apObject->filename,
																	'embed_code'		=> $apObject->embed_code,
																	'sectionid'			=> $apObject->sectionid,
																	'categoriesid'		=> $apObject->categoriesids,
																	'sections'			=> $apObject->sections,
																	'categories'		=> $apObject->categories,
																	'ordering'			=> 0,
																	'metatitle'			=> $apObject->metatitle,
																	'metakey'			=> $apObject->metakey,
																	'metadesc'			=> $apObject->metadesc,
																	'access_level'	=> $apObject->access_level,
																	'group_level'		=> $apObject->group_level,
																	'publish'				=> 1,
																	'status'	 			=> 0,
																	'useeditor'	 		=> 0,
																	'views'				=> 0,
																	'modified_date'	=> "now()",
																	'modified_by' 		=> $_SESSION['session_user_id'],
																	'created_date'  	=> "now()",
																	'created_by'    	=> $_SESSION['session_user_id'],
																	'publish_date'		=> "now()"
																	 );
						$this->dbConnect->getDBAction( $this->tbname, $copy_sql_data_array );
						$inserted_article_id = $this->dbConnect->getInsertID();
						unset( $copy_sql_data_array );
					}
				}
				else{
					echo "<script type='text/javascript'>window.alert( 'Please select a PAGE from the list to copy.' );</script>";
				}
			}
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get List Format
 *  ------------------------------------------------------------------------- */
	public function getListFormat( $opt = "" ){
		if( $opt === "boardtitle" ){
			$listtxt = "\n\t<tr id=\"board_bar\">"
										."\n\t<th width=\"35\" align=\"center\" class=\"board_line\"><input type=\"checkbox\" value=\"\" name=\"master\" onClick=\"listCheckAll();\"></th>"
										."\n\t<th width=\"15\" align=\"center\" class=\"board_line\"><small>#</small></th>"
										."\n\t<th align=\"center\" class=\"board_line\">Title</th>"
										."\n\t<th width=\"80\" align=\"center\" class=\"board_line\">Categories</th>"
										."\n\t<th width=\"50\" align=\"center\" class=\"board_line\">Order</th>"
										."\n\t<th width=\"50\" align=\"center\" class=\"board_line\">Status</th>"
										."\n\t<th width=\"60\" align=\"center\" class=\"board_line\"><small>Views</small></th>"
										."\n\t<th width=\"15\" align=\"center\" class=\"board_line\"><small>ID</small></th>"
										."\n\t<th width=\"60\" align=\"center\"><small>Date</small></th>"
										."\n\t</tr>";
		}
		elseif( $opt === "boardlist" ){
			$listtxt =  "\n\t<tr %s>"
										."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
										."\n\t<td class=\"td_bot left_space\" align=\"left\" ><a href=\"%s\" target=\"_self\" title=\"%s\" onfocus=\"this.blur()\">%s</a></td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\">%s</td>"
										."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
										."\n\t</tr>";
		}
		else {
			$listtxt =  "No Frame";
		}

		return $listtxt;
	}
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!