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

class getArticles {

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

		$this->dbConnect	 		= $Bon_db;
		$this->basicURL			= $baseURL;
		$this->static_url 			= rtrim( str_replace( "www", CONFIG_STATIC_SUBDOMAIN, str_replace( "/backend", "", $this->basicURL ) ), "/" );
		$this->setJS					= new getJavascripts($this->basicURL);
		$this->setAllelements	 	= $allElements;
		$this->error_flag				= false;
		$this->error_message		= array();

		$this->tbname				= "articles";
		$this->nodeValue			= "article";
		$this->toolbarTitle			= "Article Manager";
		$this->defaultSection		= "4";
		$this->form					= $this->setJS->setDeleteJScript() . "\n<form id=\"stylizedForm\" name=\"anybovard\" method=\"post\" action=\"%s\" enctype=\"multipart/form-data\">%s\n</form>";
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get Contents
 *  ------------------------------------------------------------------------- */
	public function getContents( $gvalue ){

		$_contents  = "\n<!--// Bof Main Contents //-->\n\n". $this->setJS->setExtraJScript() ."\n<dl>\n<dd class=\"pages_boardlist\">";
		$_contents .= $this->getBoardList( $gvalue );
		$_contents .= "\n</dd>\n</dl>\n<!--// Eof Main Contents //-->\n";

		return $_contents;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get Board List
 *  ------------------------------------------------------------------------- */
	public function  getBoardList( $gvalue ){

			$pxtra = isset( $gvalue['p'] ) ? "&p=".$gvalue['p'] : "";
			if( ( isset( $gvalue['sid'] ) && !empty( $gvalue['sid'] ) ) && ( !isset( $gvalue['cid'] ) && empty( $gvalue['cid'] ) )  ){
				$optail 						= " AND sectionid = '{$gvalue['sid']}'";
				$mtObject 				= $this->dbConnect->getObject( "SELECT * FROM sections WHERE publish = '1' AND status = '1' AND id = '{$gvalue['sid']}'" );
				$sxtra						= "&sid=".$gvalue['sid'];
				$hidden_sectionid  	=  "\n<input type=\"hidden\" name=\"sectionid\" value=\"{$gvalue['sid']}\" />";
				$g_sectid = $gvalue['sid'];
			}
			elseif( ( isset( $gvalue['sid'] ) && !empty( $gvalue['sid'] ) ) && ( isset( $gvalue['cid'] ) && !empty( $gvalue['cid'] ) ) ){
				$optail 						= " AND sectionid = '{$gvalue['sid']}' AND categoriesid = '{$gvalue['cid']}'";
				$mtObject 				= $this->dbConnect->getObject( "SELECT * FROM sections WHERE publish = '1' AND status = '1' AND id = '{$gvalue['sid']}'" );
				$cmtObject				= $this->dbConnect->getObject( "SELECT * FROM categories WHERE publish = '1' AND status = '1' AND id = '{$gvalue['cid']}'" );
				$extratitle 					= "-".$cmtObject->title;
				$sxtra 						= "&sid=".$gvalue['sid'];
				$cxtra 						= "&cid=".$gvalue['cid'];
				$g_sectid      = $gvalue['sid']."/".$gvalue['cid'];
				$hidden_sectionid		=  "\n<input type=\"hidden\" name=\"sectionid\" value=\"{$gvalue['sid']}\" />";
				$hidden_categoriesid	=  "\n<input type=\"hidden\" name=\"categoriesid\" value=\"{$gvalue['cid']}\" />";
			}
			else{
				$optail						= " AND sectionid = '".$this->defaultSection."'";
				$mtObject					= $this->dbConnect->getObject( "SELECT * FROM sections WHERE id = '{$this->defaultSection}'" );
				$hidden_sectionid	 	=  "\n<input type=\"hidden\" name=\"sectionid\" value=\"{$this->defaultSection}\" />";
				$g_sectid = $this->defaultSection;
			}

			$_action_url  = $this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$cxtra}{$pxtra}";

			if( $gvalue["m"] === "update" ){
				$this->dbConnect->getQuery( "UPDATE {$this->tbname} SET status = {$gvalue['st']}, modified_date = 'now()' WHERE id = '{$gvalue['id']}'" );
				$this->dbConnect->getQuery( "UPDATE opensef SET publish = {$gvalue['st']}, modified_date = 'now()'  WHERE tid = '{$gvalue['id']}' AND tbname = '{$this->tbname}'" );

				echo $this->setJS->setRedirectJScript( $this->nodeValue, $sxtra, $cxtra, $pxtra );
			}

			if( _getCheckNullorNot( $gvalue['txt'] ) && $gvalue['search'] === "Search" ){
				$searchtxt = " AND title LIKE  \"%" . $gvalue['txt'] ."%\"";
				$optail = "";
				$g_sectid = "";
			}
			else{
				if( isset( $gvalue['sectid'] ) ){
					if( count(explode( "/", $gvalue['sectid'] ) ) > 1){
						list( $section_id, $category_id ) = explode( "/", $gvalue['sectid'] );
						$sxtra = "&sid=".$section_id;
						$cxtra = "&cid=".$category_id;
					}
					else{
						$sxtra	 = _getCheckNullorNot( $gvalue['sectid'] ) ? "&sid=".$gvalue['sectid'] : "";
						$cxtra	 = "";
					}
					echo $this->setJS->setRedirectJScript( $this->nodeValue, $sxtra, $cxtra );
				}
			}

			$sectionCates_List 		= $this->dbConnect->getSecCatListSct( $this->tbname, " AND ( stype != 'page' AND stype != 'staff' AND stype != 'forms' )" );

			$formbtns	= "<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"Publish\" /><input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"Disable\" /><input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" onfocus=\"this.blur()\" value=\"Copy\" /><input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" onfocus=\"this.blur()\" value=\"Move\" /><input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" onfocus=\"this.blur()\" value=\"Update\" /><input type=\"submit\" name=\"submitbutton\" class=\"deletebutton\" onfocus=\"this.blur()\" value=\"Delete\" /><a href=\"".$this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$cxtra}{$pxtra}&amp;m=view"."\" class=\"abtn\" onfocus=\"this.blur()\"><span class=\"icon-32-new\" title=\"New\"></span>New</a>&nbsp;&nbsp;";

			$total_list			= $this->dbConnect->getTotalNumber( $this->tbname, "notice = '0' AND publish = '1'{$optail}".$searchtxt );
			$total_notice		= $this->dbConnect->getTotalNumber( $this->tbname, "notice = '1' AND publish = '1'{$optail}".$searchtxt );
			$total_items		= $total_list+$total_notice;
			$total_page		= ceil( $total_list/CONFIG_HOW_MANY_ARTICLES_PER_PAGE );
			$noPage			= isset( $gvalue['p'] ) ?  $gvalue['p'] : 1;
			$first 		 		= $total_list == 0 ? 1 : CONFIG_HOW_MANY_ARTICLES_PER_PAGE *( $noPage-1 );
			$last				 	= $total_list == 0 ? 0 : CONFIG_HOW_MANY_ARTICLES_PER_PfAGE * $noPage;
			$loop_number	= $total_list - ( $noPage-1 ) * CONFIG_HOW_MANY_ARTICLES_PER_PAGE;

			$_boardlist  = $this->setAllelements->setToolbar( $formbtns, "", $this->toolbarTitle );
			$_boardlist .= $hidden_sectionid . $hidden_categoriesid;
			$_boardlist .= "\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tr><td valign=\"top\">";

			$_boardlist .= "\n\t<div class=\"totlaitemsright\">".$this->setAllelements->setPullDownMenuOnChange( "sectid", $sectionCates_List , $g_sectid, "id=\"sectid\" class=\"blue sfixedwidth\"" )."\t&nbsp;&nbsp;<input id=\"txt\" type=\"text\" name=\"txt\" value=\"\" class=\"extra180\"/>&nbsp;&nbsp;<input type=\"submit\" name=\"search\" class=\"submittxt\" value=\"Search\" /></div>";

			$_boardlist .= "\n\t<div class=\"totlaitemsleft\">Total for <strong>{$mtObject->title}{$extratitle}</strong>: <strong class=\"orangetxt\">{$total_items}</strong></div>";
			$_boardlist .= "\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">\n\t<tbody>";
			$_boardlist .= $this->getListFormat( "boardtitle" );

			if( $total_notice > 0 ){
				$list_query =  $this->dbConnect->getQuery( "SELECT * FROM {$this->tbname} WHERE publish = '1' AND notice = '1'{$optail}{$searchtxt} ORDER BY ordering DESC" );
				while( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
					$pURL					= $this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$cxtra}&amp;id={$list_values['id']}{$pxtra}&amp;m=view";
					$category_name	= $this->dbConnect->getCategoriesName( $list_values['categoriesid'] );
					$created_date		= _getShortFormatDate( $list_values['created_date'] );

					$_boardlist		.= sprintf( $this->getListFormat( "boardlist" ), " class=\"list_notice\"", "<input type=\"checkbox\" value=\"{$list_values['id']}\" name=\"boardNum[]\">", "n", $pURL, stripslashes( $list_values['title'] ), stripslashes( $list_values['title'] ), $category_name, "<input type=\"text\" size=\"3\" name=\"Short[{$list_values['id']}]\" value=\"{$list_values['ordering']}\" class=\"blue orderarea\">", $this->setAllelements->setPublishIcon( $this->nodeValue, $list_values['id'], $list_values['status'], $sxtra, $cxtra, $pxtra ), $list_values['views'], $list_values['id'], $created_date );
				}
			}

			$list_query = $this->dbConnect->getQuery( "SELECT * FROM {$this->tbname} WHERE publish = '1' AND notice = '0'{$optail}{$searchtxt} ORDER BY ordering DESC LIMIT {$first}, ".CONFIG_HOW_MANY_ARTICLES_PER_PAGE."" );
			while( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
    		$bgClass				= ( $loop_number/2 ) == floor( $loop_number/2 ) ? " class=\"list_odd\"" : " class=\"list_even\"";
				$pURL					= $this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$cxtra}&amp;id={$list_values['id']}{$pxtra}&amp;m=view";
				$category_name	= $this->dbConnect->getCategoriesName( $list_values['categoriesid'] );
				$created_date		= _getShortFormatDate( $list_values['created_date'] );

				$_boardlist		.= sprintf( $this->getListFormat( "boardlist" ), $bgClass, "<input type=\"checkbox\" value=\"{$list_values['id']}\" name=\"boardNum[]\">", $loop_number, $pURL, stripslashes( $list_values['title'] ), stripslashes( $list_values['title'] ), $category_name, "<input type=\"text\" size=\"3\" name=\"Short[{$list_values['id']}]\" value=\"{$list_values['ordering']}\" class=\"blue orderarea\">", $this->setAllelements->setPublishIcon( $this->nodeValue, $list_values['id'], $list_values['status'], $sxtra, $cxtra, $pxtra ), $list_values['views'], $list_values['id'], $created_date );
				$loop_number--;
			}
			$_boardlist .= "\n\t</tbody>\n\t</table>";
			$_boardlist .= $this->setAllelements->setPagelistblock( $this->nodeValue, $total_page, $noPage, $sxtra, $cxtra );
			$_boardlist .= "\n\t</td>\n</tr></table>";

			$_boardlistWithForm = sprintf( $this->form, $_action_url, $_boardlist );

			return $_boardlistWithForm;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To Move
 *  ------------------------------------------------------------------------- */
	public function getMoveform( $gvalue ){
		$secCate_array	= $this->dbConnect->getSectionCategoryList( "publish = '1' AND stype != 'page' ORDER BY ordering" );
		$buttonlink			="history.go( -1 )";
		$_action_url  		= $this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$cxtra}{$pxtra}";
		$formbtns			= $extrabtn . "<input type=\"submit\" name=\"movebutton\" class=\"updatebutton\" value=\"Confirm\" /> <a onClick=\"{$buttonlink}\" class=\"cancelbutton\">Cancel</a>&nbsp;&nbsp;";
		$_moveform  	= $this->setAllelements->setToolbar( $formbtns, "", $this->toolbarTitle );
		$_moveform 	   .=
								"\n<dl class=\"pages_component\">\n\t<dd>"
								."\n<table border=\"0\"><tr><td valign=\"top\"  align=\"right\"><lable for=\"seccateid\" class=\"darkOrange_txt txt171\">Section / Category:</lable><small>Select a section / category to move</small></td><td>".$this->setAllelements->setPullDownMenu( "seccateid", $secCate_array, $valueObject_sectionid, "size=\"20\" id=\"seccateid\" class=\"widthx\"" )."</td></tr></table><br/>";
		for( $i = 0; $i< count( $gvalue['boardnum'] ); $i++ ){
			$_moveform .=  "\n<input type=\"hidden\" name=\"boardnum[".$i."]\" value=\"".$gvalue['boardnum'][$i]."\" />";
		}
		$_moveform .=  "\n</dd>\n</dl>";

		$_boardlistWithForm = sprintf( $this->form, $_action_url, $_moveform );
		$bItems  = "\n<!--// Bof Contents //-->";
		$bItems .= $_boardlistWithForm;
		$bItems .= "\n<!--// Eof Contents //-->\n\n";

		return $bItems;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To Display
 *  ------------------------------------------------------------------------- */
	public function getDisplay( $gvalue ){

		$sxtra	= isset( $gvalue['sid'] ) ? "&sid=".$gvalue['sid'] : "";
		$cxtra	= isset( $gvalue['cid'] ) ? "&cid=".$gvalue['cid'] : "";
		$pxtra	= isset( $gvalue['p'] ) ? "&p=".$gvalue['p'] : "";

		$_action_url	= $this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$cxtra}{$pxtra}";
		$photoOpt		= "4";

		if( !empty( $gvalue['id'] ) ){
			$valueObject		= $this->dbConnect->getContents( $this->tbname, "id = '{$gvalue['id']}' AND publish='1'" );
			if( !empty( $valueObject->filename ) ){
				$file_array = explode( ";", $valueObject->filename );
				for( $w=0; $w < count( $file_array ); $w++ ){
					$divider = $w == count( $file_array )-1 ? "":"<br>";
					$valueObject_filename .= "Delete <input type=\"checkbox\" value=\"{$file_array[$w]}\" name=\"dFile[]\">&nbsp;-&nbsp;&nbsp;<a href=\"/download/".urldecode( stripslashes( $file_array[$w] ) )."\" title=\"\" target=\"_self\">".$file_array[$w]."</a>".$divider;
				}
				$valueObject_filename = $valueObject_filename ."<hr class=\"divimg\">";
			}

			$buttontxt								= "Update";
			$buttonlink								= "parent.location='".$this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$cxtra}{$pxtra}'";
			$presstxt								= "update";
			$extrabtn								= "<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"Copy\" /><input type=\"submit\" name=\"submitbutton\" class=\"deletebutton\" value=\"Delete\" />";
			$sid_array								= explode( ":", $valueObject->sectionid );
			$valueObject_ordering			= $valueObject->ordering == 0 ? $valueObject->id  : $valueObject->ordering;
			$valueObject_sectionid			= end( $sid_array );
			$valueObject_categoriesid		= $valueObject->categoriesid;

			$valueObject_status				= $valueObject->status;
			$valueObject_usable 				= $valueObject->usable;
			$valueObject_access_level		= $valueObject->access_level;
			$valueObject_group_level		= $valueObject->group_level;

			$sectionObject						= $this->dbConnect->getContents( "sections", "id = '{$valueObject->sectionid}' AND publish='1'" );
			$photoOpt								= $sectionObject->stype === "photo" ? "10" : "5";
		} else {
			$buttontxt								= "Create";
			$presstxt								= "create";
			$valueObject_sectionid			= !empty( $gvalue['sid'] ) ? $gvalue['sid'] : $this->defaultSection;
			$valueObject_categoriesid 		= !empty( $gvalue['cid'] ) ? $gvalue['cid'] : "";
			$valueObject_status				= 1;
			$valueObject_usable 				= 1;
			$valueObject_access_level		=	7;
			$valueObject_group_level		= 1;
			$valueObject_ordering 			= $this->dbConnect->getTotalNumber( $this->tbname, "publish != '2' AND status != '2'" ) + 1;
			$buttonlink								="parent.location='".$this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$cxtra}{$pxtra}'";
			
			$nsectionObject						= $this->dbConnect->getContents( "sections", "id = '{$gvalue['sid']}' AND publish='1'" );
			$photoOpt								= $nsectionObject->stype === "photo" ? "10" : "5";			
		}

		$sections_array			= $this->dbConnect->getSectionsTypeList( "publish = '1' AND ( stype = 'board' OR stype = 'blog' OR stype = 'casting' OR stype = 'photo' ) ORDER BY ordering" );
		$cateroies_array		= $this->dbConnect->getCategoriesList( "publish = '1' AND status = '1' AND section = '{$valueObject_sectionid}' ORDER BY ordering" );
		$notice_array				= array( array( "id"=>"0","text"=>"NO" ), array( "id"=>"1","text"=>"YES" ) );
		$memberGroup_array	= $this->dbConnect->getMemberGroup( "members_group_status = '1' ORDER BY members_group_id" );
		$accessLevel_array	= $this->dbConnect->getAccessLevel( "members_level_status = '1' ORDER BY members_level_id" );

		$formbtns	= $extrabtn . "<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"{$buttontxt}\" /> <input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"Apply\" /> <a onClick=\"{$buttonlink}\" class=\"cancelbutton\">Cancel</a>&nbsp;&nbsp;";

		$_boarditem .= ( CONFIG_USE_TXT_EDITOR === "true" ) ? $this->setJS->setEditor( "tinymce", "#fulltxt" ) : "";

		$_boarditem .= $this->setJS->setAjaxJScript();
		$_boarditem .= $this->setJS->setBootstraptDatepickJScript();
		$_boarditem .= $this->setJS->setMultiFile();
		$_boarditem .= $this->setAllelements->setToolbar( $formbtns, "", $this->toolbarTitle );
		$_boarditem .= "\n<dl class=\"pages_component\">\n\t<dd>";

		$_boarditem .= "\n\t<ul class=\"tabs\">";
		$_boarditem .= "\n\t\t<li><a href=\"#tcontentsa\" onfocus=\"this.blur()\"><span>Article</span></a></li>";
		$_boarditem .= "\n\t\t<li><a href=\"#tcontentsb\" onfocus=\"this.blur()\"><span>Attachment Info</span></a></li>";
		$_boarditem .= "\n\t\t<li><a href=\"#tcontentsc\" onfocus=\"this.blur()\"><span>Casting Info</span></a></li>";
		$_boarditem .= "\n\t\t<li><a href=\"#tcontentsd\" onfocus=\"this.blur()\"><span>Parameters ( Advanced )</span></a></li>";
		$_boarditem .= "\n\t</ul>";

		$_boarditem .= "\n\t<div id=\"tcontentsa\" class=\"tab_content\"><br />";
		$_boarditem .= "\n<table border=\"0\"><tr><td valign=\"top\" align=\"right\"><lable for=\"sectionid\" class=\"darkOrange_txt txt55\">Section:</lable><small>Section</small></td><td>".$this->setAllelements->setPullDownMenu( "sectionid", $sections_array ,$valueObject_sectionid, "id=\"sectionid\" class=\"blue fixedwidth\"", "", true )."</td><td valign=\"top\" align=\"right\"><lable for=\"categoriesid\" class=\"darkOrange_txt txt95\">Category:</lable><small>Category</small></td><td><div class=\"categoriesid\">".$this->setAllelements->setPullDownMenu( "categoriesid", $cateroies_array ,$valueObject_categoriesid, "id=\"categoriesid\" class=\"blue fixedwidth\"", "", true )."</div></td></tr></table>"

								."\n<lable for=\"title\" class=\"darkOrange_txt\">Title:</lable>"
								."\n<input id=\"title\" type=\"text\" name=\"title\" value=\"".$valueObject->title."\" class=\"extra53\"/><br />"

								."\n<lable for=\"urls\" class=\"darkOrange_txt\">Link:</lable><small>Start with <strong>http://</strong> or <strong>https://</strong> and corrent URL ( http://www.domain.com/testone )</small>"
								."\n<input id=\"urls\" type=\"text\" name=\"urls\" value=\"".$valueObject->urls."\" class=\"extra53\"  /><br />"

								."\n<lable for=\"fulltxt\" class=\"darkOrange_txt\" >Contents:</lable>"
								."\n<textarea id=\"fulltxt\" name=\"fulltxt\" class=\"mceEditor sizeset\">". htmlspecialchars( $valueObject->fulltxt ) ."</textarea><br />"

								."\n<lable for=\"summarytxt\" class=\"darkOrange_txt\">Summary:</lable><small>To put summary of full text."
								."\n<strong><span name=\"countchars\" id=\"countchars\" class=\"greentxt\"></span> Characters Remaining of ".MAX_CHART."</strong>"
								."<textarea id=\"summarytxt\" name=\"summarytxt\" class=\"Threeline\">".$valueObject->summarytxt."</textarea>"

								."\n\t</div>";

		$_boarditem .= "\n\t<div id=\"tcontentsb\" class=\"tab_content\"><br />";
		$_boarditem .= "\n<lable for=\"thumbnail\" class=\"darkOrange_txt\">Thumbnail Image:</lable><small>Thumbnail as a representative for this article. Please <strong>DO NOT</strong> use white space( s ). <strong>&nbsp;&nbsp;ex ) /folder_name/thumbnail.png</strong></small>"
								."\n<input id=\"thumbnail\" type=\"text\" name=\"thumbnail\" value=\"".$valueObject->thumbnail."\" class=\"extra26 floatleft\"/><input id=\"thumimage\" type=\"file\" name=\"thumimage\" size=\"20\" class=\"multi mspace2 \" maxlength=\"1\"  />
								<br class=\"clearfix\">"

								."\n<lable for=\"qrcode\" class=\"darkOrange_txt\">QR Code:</lable><small>Matrix barcode, readable by QR scanners <strong>&nbsp;&nbsp;ex ) /folder_name/qrcode.png</strong></small>"
								."\n<input id=\"qrcode\" type=\"text\" name=\"qrcode\" value=\"".$valueObject->qrcode."\" class=\"extra26 floatleft\"/><input id=\"qrcodeimage\" type=\"file\" name=\"qrcodeimage\" size=\"20\" class=\"multi mspace2 \" maxlength=\"1\"  />
								<br class=\"clearfix\">"

								."\n<lable for=\"linkfile\" class=\"darkOrange_txt\">File to download:</lable><small>If you know the location of a file. <strong>&nbsp;&nbsp;ex ) /folder_name/filename</strong></small>"
								."\n<input id=\"linkfile\" type=\"text\" name=\"linkfile\" value=\"".$valueObject->linkfile."\" class=\"extra53\"  /><br />"

								."\n<lable for=\"filename\" class=\"darkOrange_txt\">Attachment:</lable><small>To upload Max. {$photoOpt} files. Please <strong>DO NOT</strong> use white space( s ). <strong>&nbsp;&nbsp;ex ) filename_date-012.jpg</strong></small>"
								."\n<input id=\"filename\" type=\"file\" name=\"filename[]\" size=\"20\" class=\"multi mspace\" maxlength=\"{$photoOpt}\"  /><small>".$valueObject_filename."</small>"

								.getDisplayImgBK($valueObject->filename, 300, false, $valueObject->title)

								."\n\t</div>";

		$_boarditem .= "\n\t<div id=\"tcontentsc\" class=\"tab_content\"><br />";
		$_boarditem .= "\n<lable for=\"casting_description\" class=\"darkOrange_txt\">Summary for video:</lable><small>One or Two sentence.</small>"
													."\n<input id=\"casting_description\" type=\"text\" name=\"casting_description\" value=\"".$valueObject->casting_description."\" class=\"extra53\"/>"
													."\n<table border=\"0\" width=\"695\"><tr><td align=\"left\" valign=\"top\" width=\"395\">\n<lable for=\"host\" class=\"darkOrange_txt\">Host:</lable><small>Name of main speaker</small>\n<input id=\"host\" type=\"text\" name=\"host\" value=\"".$valueObject->host."\" class=\"extra26\"/></td><td align=\"left\" valign=\"top\">\n<lable for=\"casting_date\" class=\"darkOrange_txt\">Casting Date:</lable><small>YYYY-MM-DD</small>\n<input id=\"casting_date\" type=\"text\" name=\"casting_date\" value=\"".$valueObject->casting_date."\" class=\"extra26\"/></td></tr></table>"

													."\n<lable for=\"embed_bigcode\" class=\"darkOrange_txt\">Full Embed code:</lable><small>To put full embed code here."
													."<textarea id=\"embed_bigcode\" name=\"embed_bigcode\" class=\"Threeline\">".$valueObject->embed_bigcode."</textarea>\n\t</div>";

		$_boarditem .= "\n\t<div id=\"tcontentsd\" class=\"tab_content\"><br /><br />";
		$_boarditem .= "\n<table border=\"0\"><tr><td valign=\"top\" align=\"right\"><lable for=\"notice\" class=\"darkOrange_txt txt90\">Is it NOTICE?</lable></td><td>".$this->setAllelements->setPullDownMenu( "notice", $notice_array ,$valueObject->notice, "id=\"notice\" class=\"blue\"" )."</td><td valign=\"top\" align=\"right\"><lable for=\"publishing_date\" class=\"darkOrange_txt txt95\">Publish Date:</lable><small>YYYY-MM-DD</small></td><td><input id=\"publishing_date\" type=\"text\" name=\"publishing_date\" value=\"".$valueObject->publish_date."\" class=\"extra20\"/></td></tr><tr><td valign=\"top\"><lable for=\"loginDownload\" class=\"darkOrange_txt txt120\">Login to download?</lable></td><td>".$this->setAllelements->setPullDownMenu( "loginDownload", $notice_array ,$valueObject->loginDownload, "id=\"loginDownload\" class=\"blue\"" )."</td><td valign=\"top\"><lable for=\"linkOpt\" class=\"darkOrange_txt txt120\">Click to download?</lable></td><td>".$this->setAllelements->setPullDownMenu( "linkOpt", $notice_array ,$valueObject->linkOpt, "id=\"linkOpt\" class=\"blue\"" )."</td></tr>";

		$_boarditem .= " <tr><td colspan=\"4\">\n<lable for=\"downloadAlias\" class=\"darkOrange_txt\">Download Alias:</lable><small>Download Alias will be appeared as the like.</small>"
								."\n<input id=\"downloadAlias\" type=\"text\" name=\"downloadAlias\" value=\"".$valueObject->downloadAlias."\" class=\"extra53\"/></td></tr>";

		$_boarditem .= "<tr><td valign=\"top\" align=\"right\"><lable for=\"access_level\" class=\"darkOrange_txt txt90\">Access Level:</lable></td><td>".$this->setAllelements->setPullDownMenu( "access_level", $accessLevel_array , $valueObject_access_level, "id=\"members_type\" class=\"blue sfixedwidth\"" )."</td><td valign=\"top\" align=\"right\"><lable for=\"group_level\" class=\"darkOrange_txt txt55\">Group:</lable></td><td>".$this->setAllelements->setPullDownMenu( "group_level", $memberGroup_array , $valueObject_group_level, "id=\"group_level\" class=\"blue fixedwidth\"" )."</td></tr></table>\n<table border=\"0\"><tr><td valign=\"top\"><lable for=\"order\" class=\"darkOrange_txt ordertxt\">Order:</lable></td><td><input id=\"order\" type=\"text\" name=\"order\" value=\"".$valueObject_ordering."\" class=\"extra3\"  /></td><td valign=\"top\"><lable for=\"views\" class=\"darkOrange_txt stypetxt\">Views:</lable></td><td><input id=\"views\" type=\"text\" name=\"views\" value=\"".$valueObject->views."\" class=\"extra3\" /></td><td valign=\"top\"><lable for=\"status\" class=\"darkOrange_txt stypetxt\">Status:</lable></td><td>".$this->setAllelements->setPullDownMenu( "status", $notice_array ,$valueObject_status, "id=\"status\" class=\"blue\"" )."</td><td valign=\"top\"><lable for=\"usable\" class=\"darkOrange_txt txt120\">Use embed code:</lable></td><td>".$this->setAllelements->setPullDownMenu( "usable", $notice_array ,$valueObject->usable, "id=\"usable\" class=\"blue\"" )."</td></tr></table><br />\n\t</div>";

		$_boarditem .=  "\n<input type=\"hidden\" name=\"id\" value=\"{$valueObject->id}\" />"
								."\n<input type=\"hidden\" name=\"xfile\" value=\"{$valueObject->filename}:{$valueObject->filesize}\" />"
								."\n<input type=\"hidden\" name=\"press\" value=\"{$presstxt}\" />"
								."</dd>\n</dl>";

		$_boardlistWithForm = sprintf( $this->form, $_action_url, "\n<script type=\"text/javascript\" src=\"". $this->static_url . CONFIG_STATIC_SUBFOLDER ."/js/back-article.js\"></script>".$_boarditem );

		$bItems  = "\n<!--// Bof Contents //-->";
		$bItems .= $_boardlistWithForm;
		$bItems .= "\n<!--// Eof Contents //-->\n\n";

		return $bItems;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To Create
 *  ------------------------------------------------------------------------- */
	public function getCreate( $allValues ){
		global $user_ip;

		$sxtra	= isset( $allValues['sid'] ) ? "&sid=".$allValues['sid'] : "";
		$cxtra	= isset( $allValues['cid'] ) ? "&cid=".$allValues['cid'] : "";
		$pxtra	= isset( $allValues['p'] ) ? "&p=".$allValues['p'] : "";
		$node	= $allValues['node'];

		if( isset($_FILES) ){
			if( $_FILES[thumimage][error] == 0 ){
				$thum_file = getUploadingFile( $_FILES[thumimage] );
				$thumimage_filename = $thum_file['name'];
				unset( $thum_file );
	
				$allValues['thumbnail'] = $thumimage_filename;
			}
	
			if( $_FILES[qrcodeimage][error] == 0 ){
				$qrcode_file = getUploadingFile( $_FILES[qrcodeimage] );
				$qrimage_filename = $qrcode_file['name'];
				unset( $qrcode_file );
	
				$allValues['qrcode'] = $qrimage_filename;
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

		if( isset( $allValues['seccateid'] ) && !empty( $allValues['seccateid'] ) ){
			$scid_array						= explode( "_", $allValues['seccateid'] );
			$allValues['sectionid']			= $scid_array[0];
			$allValues['categoriesid']	= $scid_array[1];
		} else {
			$sid_array= explode( ":", $allValues['sectionid'] );
			$allValues["sectionid"] = end( $sid_array );
		}

		$sxtras			= !empty( $allValues['sectionid'] ) ? " AND sectionid = ".$allValues['sectionid'] : "";
		$cxtras			= !empty( $allValues['categoriesid'] ) ? " AND categoriesid = ".$allValues['categoriesid'] : "";
		$pageObject	= $this->dbConnect->getObject( "SELECT * FROM pages WHERE publish = '1' AND status = '1'{$sxtras}{$cxtras}" );

		if( $allValues['submitbutton'] === "Copy" && $allValues['press'] === "update" ){
			$cdate					= "now()";
			$extra_title			= "Copy - ";
			$allValues['views']	= 0;

			if( empty( $filename ) && !empty( $allValues['xfile'] ) ){
				$filename_array	= explode( ":", $allValues['xfile'] );
				$filename				= $filename_array[0];
				$filesize				= $filename_array[1];
			}

		}elseif( ( $allValues['submitbutton'] === "Update" || $allValues['submitbutton'] === "Apply" ) && $allValues['press'] === "update" ){
			$valueObject = $this->dbConnect->getObject( "SELECT * FROM {$this->tbname} WHERE id = '{$allValues['id']}' AND publish='1'" );
			list( $year, $month, $date ) = explode( '-' , substr( $valueObject->created_date , 0 , 10 ) );
			$cdate = $valueObject->created_date;

			if( !empty( $allValues['xfile'] ) ){
				$filename_array	= explode( ":", $allValues['xfile'] );
				$xfilename			= $filename_array[0];
				$xfilesize				= $filename_array[1];
			}

			if( count( $allValues['dfile'] ) > 0 ){
				$xfiles = substr( $allValues['xfile'], 0, strpos( $allValues['xfile'], ":" ) );
				$farray = explode( ";", $xfiles );
				for( $no = 0; $no < count( $allValues['dfile'] ); $no++ ){
					for( $noz=0; $noz < count( $farray ); $noz++ ){
						if( $farray[$noz] == $allValues['dfile'][$no] ){
							$xfiles = str_replace( $farray[$noz], "", rtrim( $xfiles, ";" ) );
							if( $allValues['views']!= 0 ){
								!@unlink( $_SERVER['DOCUMENT_ROOT'] . "/" . CONFIG_FILES_UPLOAD_ROOT . $farray[$noz] );
								clearstatcache();
							}
						}
					}
				}
				$xfilename = preg_replace( '/;;/', ";", $xfiles );
				$xfilename = trim( $xfilename, ";" );
			}

			$filename	= trim( empty( $filename ) ? $xfilename : $xfilename .";".$filename, ";" );
			$re_status	= $allValues['status'];
		}else{
			$cdate		= "now()";
			$re_status	= _getCheckNullorNot( $allValues['status'] ) ? $allValues['status'] : 1;
		}

		$total_pages			= $this->dbConnect->getTotalNumber( $this->tbname, "publish != '2' AND status != '2'" );
		$allValues['order']	= empty( $allValues['order'] ) ? $total_pages + 1 : $allValues['order'];

		$sql_data_array = array(
												'title'							=> $extra_title . $allValues['title'],
												'fulltxt'						=> $allValues['fulltxt'],
												'summarytxt'				=> $allValues['summarytxt'],
												'thumbnail'					=> $allValues['thumbnail'],
												'qrcode'						=> $allValues['qrcode'],
												'embed_bigcode'			=> $allValues['embed_bigcode'],
												'linkfile'						=> $allValues['linkfile'],
												'filename'					=> $filename,
												'filesize'						=> $filesize,
												'urls'							=> $allValues['urls'],
												'embed_code'				=> $allValues['embed_code'],
												'usable'						=> $allValues['usable'],
												'sectionid'					=> $allValues['sectionid'],
												'categoriesid'				=> $allValues['categoriesid'],
												'ordering'					=> $allValues['order'],
												'host'							=> $allValues['host'],
												'casting_description'	=> $allValues['casting_description'],
												'casting_date'				=> $allValues['casting_date'],
												'metatitle'					=> $allValues['title'],
												'metakey'					=> $allValues['metakey'],
												'metadesc'					=> $allValues['summarytxt'],
												'group_level'				=> $allValues['group_level'],
												'access_level'			=> $allValues['access_level'],
												'password'					=> '7277',
												'user_ip'						=> $user_ip,
												'publish'						=> 1,
												'status'	 					=> $re_status,
												'notice'						=> $allValues['notice'],
												'loginDownload'			=> $allValues['logindownload'],
												'downloadAlias'			=> $allValues['downloadalias'],
												'linkOpt'						=> $allValues['linkopt'],
												'views'						=> $allValues['views'],
												'modified_date' 			=> "now()",
												'created_date'  			=> $cdate,
												'created_by'    			=> $_SESSION['session_user_id'],
												'publish_date'				=> _getCheckNullorNot( $allValues['publish_date']) ? $allValues['publish_date'] : "now()"
											 );

			if( ( $allValues['submitbutton'] === "Create" || $allValues['submitbutton'] === "Apply" ) && $allValues['press'] === "create" ){
				if( !empty( $allValues['title'] ) ){
					$this->dbConnect->getDBAction( $this->tbname, $sql_data_array );
					$inserted_article_id = $this->dbConnect->getInsertID();

					$sef_sql_data_array = array(
															'tid'					=> $inserted_article_id,
															'tbname'				=> $this->tbname,
															'external'			=> "/".date( 'md' ).$inserted_article_id,
															'pid'					=> $pageObject->id,
															'direction'			=> "",
															'publish'				=> 1,
															'created_date'		=> "now()",
															'modified_date'	=> "now()",
															'hits'					=> 0,
															'notes'				=> ""
														 );

					$this->dbConnect->getDBAction( 'opensef', $sef_sql_data_array );
				}
				unset( $sef_sql_data_array );
				unset( $sql_data_array );

				if( CONFIG_EMAIL_NOTICATION_NEW_ARTICLE === "true" ){
					$email_subject = "New article has been added - ".date( "F j, Y, g:i a" );
					$email_text  = $allValues['title']."<hr/><br/><br/>ID: ".$inserted_article_id."<br/>IN ".$this->tbname;
					_getSendEmail( "Admin of ".CONFIG_SITE_NAME, CONFIG_ADMIN_EMAIL, $email_subject, $email_text, CONFIG_SITE_NAME, CONFIG_SITE_EMAIL );
				}

				if( $allValues['submitbutton'] === "Create" ){
					echo $this->setJS->setRedirectJScript( $node, $sxtra, $cxtra, $pxtra );
				} elseif ( $allValues['submitbutton'] === "Apply" ){
					echo $this->setJS->setRedirectJScriptID( $node, $sxtra, $cxtra, $pxtra, "&id={$inserted_article_id}" );
				}

			} elseif( $allValues['submitbutton'] === "Copy" && $allValues['press'] === "update" ){
				if( !empty( $allValues['title'] ) ){
					$this->dbConnect->getDBAction( $this->tbname, $sql_data_array );
					$inserted_article_id = $this->dbConnect->getInsertID();

					$sef_sql_data_array = array(
															'tid'						=> $inserted_article_id,
															'tbname'				=> $this->tbname,
															'external'				=> "/".date( 'md' ).$inserted_article_id,
															'pid'						=> $pageObject->id,
															'direction'			=> "",
															'publish'				=> 1,
															'created_date'		=> "now()",
															'modified_date'	=> "now()",
															'hits'					=> 0,
															'notes'					=> "copied"
														 );

					$this->dbConnect->getDBAction( 'opensef', $sef_sql_data_array );
				}
				unset( $sef_sql_data_array );
				unset( $sql_data_array );
				echo $this->setJS->setRedirectJScript( $node, $sxtra, $cxtra, $pxtra );

			} elseif( ( $allValues['submitbutton'] === "Update" || $allValues['submitbutton'] === "Apply" ) && $allValues['press'] === "update" ){
				if( !empty( $allValues['title'] ) ){
					$this->dbConnect->getDBAction( $this->tbname, $sql_data_array, "update", "id = {$allValues['id']}" );

					if( $this->dbConnect->getTotalNumber( "opensef", "tbname = '{$this->tbname}' AND tid = '{$allValues['id']}'" )>0 ){
						$sef_sql_data_array = array(
																'tbname'				=> $this->tbname,
																'external'			=> "/".$month.$date.$allValues['id'],
																'pid'					=> $pageObject->id,
																'direction'			=> "",
																'publish'				=> 1,
																'modified_date'	=> "now()"
															 );
						$this->dbConnect->getDBAction( 'opensef', $sef_sql_data_array, "update", "tbname = '{$this->tbname}' AND tid = '{$allValues['id']}'" );

					}else{
						$sef_sql_data_array = array(
																'tid'					=> $allValues['id'],
																'tbname'				=> $this->tbname,
																'external'			=> "/".date( 'md' ).$allValues['id'],
																'pid'					=> $pageObject->id,
																'direction'			=> "",
																'publish'				=> 1,
																'created_date'		=> "now()",
																'modified_date'	=> "now()",
																'hits'					=> 0,
																'notes'				=> "updated"
															 );
						$this->dbConnect->getDBAction( 'opensef', $sef_sql_data_array );
					}
				}
				unset( $sef_sql_data_array );
				unset( $sql_data_array );
				if( $allValues['submitbutton'] === "Update" ){
					echo $this->setJS->setRedirectJScript( $node, $sxtra, $cxtra, $pxtra );
				} elseif ( $allValues['submitbutton'] === "Apply" ){
					echo $this->setJS->setRedirectJScriptID( $node, $sxtra, $cxtra, $pxtra, "&id={$allValues['id']}" );
				}

			} elseif( $allValues['submitbutton'] === "Publish" ){
				if(count($allValues['boardnum'])>0){
					foreach ($allValues['boardnum'] as $key => $kw) {
						$this->dbConnect->getQuery( "UPDATE ".$this->tbname." SET status = 1 WHERE id = '{$kw}'" );
					}
				} else {
					echo "<script type='text/javascript'>window.alert('Please select an Article from the list to update.');</script>";
				}
				unset($allValues);
				echo $this->setJS->setRedirectJScript( $node, $sxtra, $cxtra, $pxtra);

			} elseif( $allValues['submitbutton'] === "Disable" ){
				if(count($allValues['boardnum'])>0){
					foreach ($allValues['boardnum'] as $key => $kw) {
						$this->dbConnect->getQuery( "UPDATE ".$this->tbname." SET status = 0 WHERE id = '{$kw}'" );
					}
				} else {
					echo "<script type='text/javascript'>window.alert('Please select an Article from the list to update.');</script>";
				}
				unset($allValues);
				echo $this->setJS->setRedirectJScript( $node, $sxtra, $cxtra, $pxtra);

			} elseif( $allValues['submitbutton'] === "Delete" ){
				if( count( $allValues['boardnum'] ) > 0 ){
					foreach ( $allValues['boardnum'] as $key => $kw ){
						//To delete each of  attachments
						$attachmentObject = $this->dbConnect->getContents($this->tbname, "id = '{$kw}' AND publish='1'");
						if( !empty($attachmentObject->filename) ){
									$attachment_array = explode(";", $attachmentObject->filename);
									for($w=0; $w < count($attachment_array); $w++){
											if( $this->dbConnect->getTotalNumber( $this->tbname, "filename LIKE '%{$attachment_array[$w]}%'") == 1 ){													
								        @unlink($_SERVER['DOCUMENT_ROOT'] . "/" . CONFIG_FILES_UPLOAD_ROOT . $attachment_array[$w]);
											}	
									}
						}
						//To delete Linked image
						//But If the image is used in more than ONE place, the image will be kept
						if( !empty($attachmentObject->linkfile) && $this->dbConnect->getTotalNumber( $this->tbname, "linkfile = '{$attachmentObject->linkfile}'") == 1 ){
					        @unlink($_SERVER['DOCUMENT_ROOT'] . "/" . CONFIG_FILES_UPLOAD_ROOT . $attachmentObject->linkfile);
						}
						//To delete Thumbnail image
						//But If the Thumbnail is used in more than ONE place, the Thumbnail will be kept
						if( !empty($attachmentObject->thumbnail) && $this->dbConnect->getTotalNumber( $this->tbname, "thumbnail = '{$attachmentObject->thumbnail}'") == 1 ){
								@unlink($_SERVER['DOCUMENT_ROOT'] . "/" . CONFIG_FILES_UPLOAD_ROOT . $attachmentObject->thumbnail);
						}
						//To delete QR Code image
						if( !empty($attachmentObject->qrcode) ){
								@unlink($_SERVER['DOCUMENT_ROOT'] . "/" . CONFIG_FILES_UPLOAD_ROOT . $attachmentObject->qrcode);
						}

						//Just in case, we don't want to update a status instead of deleting it.
						//$this->dbConnect->getQuery( "DELETE FROM ".$this->tbname." WHERE id = '{$kw}'" );
						//$this->dbConnect->getQuery( "DELETE FROM opensef WHERE tid = '{$kw}' AND tbname = '".$this->tbname."'" );
						$this->dbConnect->getQuery( "UPDATE ".$this->tbname." SET publish = '0' WHERE id = '{$kw}'" );
						$this->dbConnect->getQuery( "UPDATE opensef SET publish = '0' WHERE tid = '{$kw}' AND tbname = '".$this->tbname."'" );
					}

				}else{
					if( !empty( $allValues['id'] ) ){
						//To delete each of  attachments
						$attachmentObject_with_id = $this->dbConnect->getContents($this->tbname, "id = '{$allValues['id']}' AND publish='1'");
						if( !empty($attachmentObject_with_id->filename) ){
									$attachments_array = explode(";", $attachmentObject_with_id->filename);
									for($ws=0; $ws < count($attachments_array); $ws++){
											if( $this->dbConnect->getTotalNumber( $this->tbname, "filename LIKE '%{$attachments_array[$ws]}%'") == 1 ){										
								        @unlink($_SERVER['DOCUMENT_ROOT'] . "/" . CONFIG_FILES_UPLOAD_ROOT . $attachments_array[$ws]);											
											}
									}
						}
						//To delete Linked image
						//But If the image is used in more than ONE place, the image will be kept
						if( !empty($attachmentObject_with_id->linkfile) && $this->dbConnect->getTotalNumber( $this->tbname, "linkfile = '{$attachmentObject_with_id->linkfile}'") == 1 ){
					        @unlink($_SERVER['DOCUMENT_ROOT'] . "/" . CONFIG_FILES_UPLOAD_ROOT . $attachmentObject_with_id->linkfile);
						}
						//To delete Thumbnail image
						//But If the Thumbnail is used in more than ONE place, the Thumbnail will be kept
						if( !empty($attachmentObject_with_id->thumbnail) && $this->dbConnect->getTotalNumber( $this->tbname, "thumbnail = '{$attachmentObject_with_id->thumbnail}'") == 1 ){
								@unlink($_SERVER['DOCUMENT_ROOT'] . "/" . CONFIG_FILES_UPLOAD_ROOT . $attachmentObject_with_id->thumbnail);
						}
						//To delete QR Code image
						if( !empty($attachmentObject_with_id->qrcode) ){
								@unlink($_SERVER['DOCUMENT_ROOT'] . "/" . CONFIG_FILES_UPLOAD_ROOT . $attachmentObject_with_id->qrcode);
						}
						//Just in case, we don't want to update a status instead of deleting it.
						//$this->dbConnect->getQuery( "DELETE FROM ".$this->tbname." WHERE id = '{$allValues['id']}'" );
						//$this->dbConnect->getQuery( "DELETE FROM opensef WHERE itd = '{$allValues['id']}' AND tbname = '".$this->tbname."'");
						$this->dbConnect->getQuery( "UPDATE ".$this->tbname." SET publish = '0', status = '0' WHERE id = '{$allValues['id']}'" );
						$this->dbConnect->getQuery( "UPDATE opensef SET publish = '0' WHERE tid = '{$allValues['id']}' AND tbname = '".$this->tbname."'" );
					}else{
						echo "<script type='text/javascript'>window.alert( 'Please select an Article from the list to delete.' );</script>";
					}
				}
				unset( $allValues );
				echo $this->setJS->setRedirectJScript( $node, $sxtra, $cxtra, $pxtra );

			} elseif( $allValues['submitbutton'] === "Update" ){
				if( count( $allValues['boardnum'] )>0 ){
					foreach ( $allValues['boardnum'] as $key => $kw ){
						$this->dbConnect->getQuery( "UPDATE {$this->tbname} SET ordering = {$allValues['short'][$kw]} WHERE id = '{$kw}'" );
					}
				} else {
					echo "<script type='text/javascript'>window.alert( 'Please select an Article from the list to update.' );</script>";
				}
				unset( $allValues );
				echo $this->setJS->setRedirectJScript( $node, $sxtra, $cxtra, $pxtra );

			} elseif( $allValues['submitbutton'] === "Copy" ){
				if( count( $allValues['boardnum'] ) > 0 ){
					foreach ( $allValues['boardnum'] as $key => $kw ){
						$apObject = $this->dbConnect->getObject( "SELECT * FROM {$this->tbname} WHERE id = '{$kw}'" );
						$copy_sql_data_array = array(
																	'title'							=> "Copy - ".$apObject->title,
																	'fulltxt'						=> $apObject->fulltxt,
																	'thumbnail'					=> $apObject->thumbnail,
																	'qrcode'						=> $apObject->qrcode,
																	'embed_bigcode'			=> $apObject->embed_bigcode,
																	'linkfile'						=> $apObject->linkfile,
																	'filename'					=> $apObject->filename,
																	'filesize'						=> $apObject->filesize,
																	'urls'							=> $apObject->urls,
																	'embed_code'				=> $apObject->embed_code,
																	'usable'						=> $apObject->usable,
																	'sectionid'					=> $apObject->sectionid,
																	'categoriesid'				=> $apObject->categoriesid,
																	'ordering'					=> $apObject->ordering +1,
																	'host'							=> $apObject->host,
																	'casting_description'	=> $apObject->casting_description,
																	'casting_date'				=> $apObject->casting_date,
																	'metatitle'					=> $apObject->title,
																	'metakey'					=> $apObject->metakey,
																	'metadesc'					=> $apObject->summarytxt,
																	'access_level'			=> 7,
																	'group_level'				=> 1,
																	'password'					=> 'milalweb',
																	'user_ip'						=> $user_ip,
																	'publish'						=> 1,
																	'status'	 					=> 0,
																	'notice'						=> $apObject->notice,
																	'loginDownload'			=> $apObject->loginDownload,
																	'downloadAlias'			=> $apObject->downloadAlias,
																	'linkOpt'						=> $apObject->linkOpt,
																	'views'						=> 0,
																	'modified_date' 			=> "now()",
																	'created_date'  			=> $cdate,
																	'created_by'    			=> $_SESSION['session_user_id'],
																	'publish_date'				=> "now()"
																	 );
						$this->dbConnect->getDBAction( $this->tbname, $copy_sql_data_array );
						$inserted_article_id = $this->dbConnect->getInsertID();
						unset( $copy_sql_data_array );

						$copy_sef_sql_data_array = array(
																		'tid'					=> $inserted_article_id,
																		'tbname'				=> $this->tbname,
																		'external'			=> "/".date( 'md' ).$inserted_article_id,
																		'pid'					=> $pageObject->id,
																		'direction'			=> "",
																		'publish'				=> 1,
																		'created_date'		=> "now()",
																		'modified_date'	=> "now()",
																		'hits'					=> 0,
																		'notes'				=> "copied"
																		 );

						$this->dbConnect->getDBAction( 'opensef', $copy_sef_sql_data_array );
						unset( $copy_sef_sql_data_array );
					}
				}
				else {
					echo "<script type='text/javascript'>window.alert( 'Please select an Article from the list to copy.' );</script>";
				}

				unset( $allValues );
				echo $this->setJS->setRedirectJScript( $node, $sxtra, $cxtra, $pxtra );
			} elseif( $allValues['movebutton'] === "Confirm" ){
				if( count( $allValues['boardnum'] ) > 0 ){
					foreach ( $allValues['boardnum'] as $key => $kw ){
						$apObject = $this->dbConnect->getObject( "SELECT * FROM {$this->tbname} WHERE id = '{$kw}'" );
						$this->dbConnect->getQuery( "UPDATE {$this->tbname} SET sectionid = '{$scid_array[0]}',  categoriesid = '{$scid_array[1]}', status = '0' WHERE id = '{$kw}'" );
						$this->dbConnect->getQuery( "DELETE FROM opensef WHERE tid = '{$kw}' AND tbname = '{$this->tbname}'" );

						$move_sef_sql_data_array = array(
																		'tid'					=> $kw,
																		'tbname'				=> $this->tbname,
																		'external'			=> "/".date( 'md' ).$kw,
																		'pid'					=> $pageObject->id,
																		'direction'			=> "",
																		'publish'				=> 1,
																		'created_date'		=> "now()",
																		'modified_date'	=> "now()",
																		'hits'					=> 0,
																		'notes'				=> "moved"
																		 );

						$this->dbConnect->getDBAction( 'opensef', $move_sef_sql_data_array );
						unset( $copy_sef_sql_data_array );
					}

				} else {
					echo "<script type='text/javascript'>window.alert( 'Please select an Article from the list to update.' );</script>";
				}

				unset( $allValues );
				echo $this->setJS->setRedirectJScript( $node, $sxtra, $cxtra, $pxtra );
			}
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get List Format
 *  ------------------------------------------------------------------------- */
	public function getListFormat( $opt = "" ){
		if( $opt === "boardtitle" ){
			$listtxt = "\n\t<tr id=\"board_bar\" height=\"25\">"
										."\n\t<th width=\"35\" align=\"center\" class=\"board_line\"><input type=\"checkbox\" value=\"\" name=\"master\" onClick=\"listCheckAll();\"></th>"
										."\n\t<th width=\"15\" align=\"center\" class=\"board_line\"><small>#</small></th>"
										."\n\t<th align=\"center\" class=\"board_line\">Title</th>"
										."\n\t<th width=\"95\" align=\"center\" class=\"board_line\">Categories</th>"
										."\n\t<th width=\"50\" align=\"center\" class=\"board_line\">Order</th>"
										."\n\t<th width=\"50\" align=\"center\" class=\"board_line\">Status</th>"
										."\n\t<th width=\"50\" align=\"center\" class=\"board_line\"><small>Views</small></th>"
										."\n\t<th width=\"50\" align=\"center\" class=\"board_line\"><small>ID</small></th>"
										."\n\t<th width=\"60\" align=\"center\"><small>Date</small></th>"
										."\n\t</tr>";
		} elseif( $opt === "boardlist" ){
			$listtxt =  "\n\t<tr height=\"25\" %s>"
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
		} else {
			$listtxt =  "No Frame";
		}

		return $listtxt;
	}
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!