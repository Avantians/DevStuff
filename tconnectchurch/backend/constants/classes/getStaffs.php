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

class getStaffs {

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

		$this->tbname				= "staff";
		$this->nodeValue			= "staff";
		$this->toolbarTitle			= "Staff Manager";
		$this->form					= $this->setJS->setDeleteJScript() . "\n<form id=\"stylizedForm\" name=\"anybovard\" method=\"post\" action=\"%s\" enctype=\"multipart/form-data\">%s\n</form>";
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
 * [00/00/2011]::To get board list
 *  ------------------------------------------------------------------------- */
	public function  getBoardList( $gvalue ){

			$pxtra = isset($gvalue['p']) ? "&p=".$gvalue['p'] : "";
			$_action_url  = $this->basicURL."indexx.php?node={$this->nodeValue}{$pxtra}";

			if($gvalue['m'] === "update"){
				$this->dbConnect->getQuery( "UPDATE {$this->tbname} SET status = {$gvalue['st']}, modified_date = 'now()' WHERE id = '{$gvalue['id']}'" );
				echo $this->setJS->setRedirectJScript( $this->nodeValue, $sxtra, $cxtra, $pxtra);
			}

			$formbtns	= "<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"Update\" /><input type=\"submit\" name=\"submitbutton\" class=\"deletebutton\" value=\"Delete\" /><a href=\"".$this->basicURL."indexx.php?node={$this->nodeValue}{$pxtra}&amp;m=view"."\" class=\"abtn\" onfocus=\"this.blur()\"><span class=\"icon-32-new\" title=\"New\"></span>New</a>&nbsp;&nbsp;";

			$total_list			= $this->dbConnect->getTotalNumber( $this->tbname, "publish = '1'");
			$total_items		= $total_list;
			$total_page		= ceil($total_list/CONFIG_HOW_MANY_ARTICLES_PER_PAGE);
			$noPage 			= isset($gvalue['p']) ?  $gvalue['p'] : 1;
			$first					= $total_list == 0 ? 1 : CONFIG_HOW_MANY_ARTICLES_PER_PAGE *($noPage-1);
			$last					= $total_list == 0 ? 0 : CONFIG_HOW_MANY_ARTICLES_PER_PAGE * $noPage;
			$loop_number	= $total_list - ($noPage-1) * CONFIG_HOW_MANY_ARTICLES_PER_PAGE;

			$_boardlist  = $this->setAllelements->setToolbar($formbtns, "", $this->toolbarTitle);
			$_boardlist .= "\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tr><td valign=\"top\">";
			$_boardlist .= "\n\t<div class=\"totlaitems\">Total of menu items <strong></strong>: {$total_items}</div>";
			$_boardlist .= "\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">";
			$_boardlist .= "\n\t<tbody>";
			$_boardlist .= $this->getListFormat("boardtitle");

			$list_query = $this->dbConnect->getQuery("SELECT * FROM {$this->tbname} WHERE publish = '1' ORDER BY ordering, created_date DESC LIMIT {$first}, ".CONFIG_HOW_MANY_ARTICLES_PER_PAGE."");
			while ($list_values = $this->dbConnect->getFetch_Array($list_query)) {
    			$bgClass			= ($loop_number/2) == floor($loop_number/2) ? " class=\"list_odd\"" : " class=\"list_even\"";
				$pURL				= $this->basicURL."indexx.php?node={$this->nodeValue}&amp;id={$list_values['id']}{$pxtra}&amp;m=view";
				$category_name	= $this->dbConnect->getCategoriesName( $list_values['categoriesid'] );
				$created_date	= _getShortFormatDate( $list_values['created_date'] );

				$_boardlist .= sprintf($this->getListFormat("boardlist"), $bgClass, "<input type=\"checkbox\" value=\"{$list_values['id']}\" name=\"boardNum[]\">", $loop_number, getDisplayImgBK($list_values['filename'], 90, false, $list_values['name'], false), $pURL, stripslashes($list_values['jobtitle']."-".$list_values['name']), stripslashes($list_values['name']), $list_values['jobtitle'], "<input type=\"text\" size=\"3\" name=\"Short[{$list_values['id']}]\" value=\"{$list_values['ordering']}\" style=\"width:40px;font-size:11px;text-align:center\">", $this->setAllelements->setPublishIcon( $this->nodeValue, $list_values['id'], $list_values['status'] ), $list_values['views'], $list_values['id'], $created_date );
				$loop_number--;
			}

			$_boardlist .= "\n\t</tbody>";
			$_boardlist .= "\n\t</table>";
			$_boardlist .= $this->setAllelements->setPagelistblock( $this->nodeValue, $total_page, $noPage );
			$_boardlist .= "\n\t</td></tr></table>";

			$_boardlistWithForm = sprintf($this->form, $_action_url, $_boardlist);

			return $_boardlistWithForm;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get display
 *  ------------------------------------------------------------------------- */
	public function getDisplay( $gvalue ){

		$pxtra 			= isset($gvalue['p']) ? "&p=".$gvalue['p'] : "";
		$_action_url 	= $this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$cxtra}{$pxtra}";

		if( !empty($gvalue['id']) ){
			$valueObject	= $this->dbConnect->getContents($this->tbname, "id = '{$gvalue['id']}' AND publish='1'");
			$buttontxt		= "Update";
			$buttonlink		= "parent.location='".$this->basicURL ."indexx.php?node={$this->nodeValue}{$pxtra}'";
			$presstxt		= "update";
			$extrabtn		= "<input type=\"submit\" name=\"submitbutton\" class=\"deletebutton\" value=\"Delete\" />";
		} else {
			$buttontxt		= "Create";
			$presstxt 		= "create";
			$buttonlink		= "parent.location='".$this->basicURL ."indexx.php?node={$this->nodeValue}{$pxtra}'";
		}

		$notice_array 	= array(array("id"=>"0","text"=>"NO"), array("id"=>"1","text"=>"YES"));
		$yesno_array 	= array(array("id"=>"no","text"=>"NO"), array("id"=>"yes","text"=>"YES"));
		$formbtns			= $extrabtn . "<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"{$buttontxt}\" /> <a onClick=\"{$buttonlink}\" class=\"cancelbutton\">Cancel</a>&nbsp;&nbsp;";

		$_boarditem  = $this->setJS->setEditor( "tinymce", "#fulltxt", "simple" );
		$_boarditem .= $this->setAllelements->setToolbar($formbtns, "", $this->toolbarTitle);
		$_boarditem .=
								"\n<dl class=\"pages_component\">\n\t<dd>"
								."\n<lable for=\"name\" class=\"darkOrange_txt\">Full Name:</lable>"
								."\n<input id=\"name\" type=\"text\" name=\"name\" value=\"".$valueObject->name."\" class=\"extra53\"/><br />"

								."\n<lable for=\"jobtitle\" class=\"darkOrange_txt\">Job Title:</lable>"
								."\n<input id=\"jobtitle\" type=\"text\" name=\"jobtitle\" value=\"".$valueObject->jobtitle."\" class=\"extra53\"/><br />"

								."\n<table border=\"0\" width=\"695\"><tr><td  align=\"left\" valign=\"top\" width=\"395\">\n<lable for=\"email\" class=\"darkOrange_txt\">Email:</lable><small>alias@test.com</small>\n<input id=\"email\" type=\"text\" name=\"email\" value=\"".$valueObject->email."\" class=\"extra26\"  /></td><td align=\"left\" valign=\"top\">\n<lable for=\"phone\" class=\"darkOrange_txt\">Phone:</lable><small>416-123-4567</small>\n<input id=\"phone\" type=\"text\" name=\"phone\" value=\"".$valueObject->phone."\" class=\"extra26\"  /></td></tr></table>"

								."\n<lable for=\"urls\" class=\"darkOrange_txt\">Personal site / Blog:</lable>"
								."\n<input id=\"urls\" type=\"text\" name=\"urls\" value=\"".$valueObject->urls."\" class=\"extra53\"/><br />"

								."\n<lable for=\"filename\" class=\"darkOrange_txt\">Photo:</lable><small>To upload file. Please <strong>DO NOT</strong> use KOREAN file name and white space(s). <strong>ex) filename_date-012.jpg</strong></small>"
								."\n<input id=\"filename\" type=\"file\" name=\"filename\" size=\"30\" class=\"\" /><small>".$valueObject->filename."</small><br/>"

								. getDisplayImg($valueObject->filename, 100, false, $valueObject->title)

								."\n<lable for=\"fulltxt\" class=\"darkOrange_txt\" >Contents:</lable>"
								."\n<textarea id=\"fulltxt\" name=\"fulltxt\" class=\"mceEditor\">".$valueObject->fulltxt."</textarea><br />"

								."\n<table border=\"0\"><tr><td width=\"40\" valign=\"top\"><lable for=\"order\" class=\"darkOrange_txt\" style=\"margin:3px 5px 0 0;text-align:left;width:45px;\">Order:</lable></td><td width=\"50\"><input id=\"order\" type=\"text\" name=\"order\" value=\"".$valueObject->ordering."\"class=\"extra3\"  /></td></tr></table><br />";

		$_boarditem .=  "\n<input type=\"hidden\" name=\"id\" value=\"{$valueObject->id}\" />"
								."\n<input type=\"hidden\" name=\"xfile\" value=\"{$valueObject->filename}:{$valueObject->filesize}\" />"
								."\n<input type=\"hidden\" name=\"press\" value=\"{$presstxt}\" />"
								."</dd>\n</dl>";

		$_boardlistWithForm = sprintf($this->form, $_action_url, "\n<script type=\"text/javascript\" src=\"". $this->static_url . CONFIG_STATIC_SUBFOLDER ."/js/back-staff.js\"></script>".$_boarditem);

		$bItems  = "\n<!--// Bof Contents //-->";
		$bItems .= $_boardlistWithForm;
		$bItems .= "\n<!--// Eof Contents //-->\n\n";

		return $bItems;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get create
 *  ------------------------------------------------------------------------- */
	public function getCreate( $allValues ){

		$pxtra = isset($allValues['p']) ? "&p=".$allValues['p'] : "";
		$node = $allValues['node'];
		if ($_FILES[filename][name] && $error_flag == false && empty($file)){
			$uploading_file = getUploadingFile($_FILES[filename]);
			$fileerror	= $uploading_file['error'];
			$filename	= $uploading_file['name'];
			$filetype	= $uploading_file['type'];
			$filesize	= $uploading_file['size'];
			unset($uploading_file);
		}

  	    $page_qry		= "SELECT * FROM pages WHERE publish = '1' AND status = '1'{$sxtras}{$cxtras}";
		$pageObject = $this->dbConnect->getObject( $page_qry );

		if( $allValues['submitbutton'] === "Update" && $allValues['press'] === "update" ){
			$query = "SELECT * FROM {$this->tbname} WHERE id = '{$allValues['id']}' AND publish='1' AND status='1'";
			$valueObject = $this->dbConnect->getObject( $query );
			list($year, $month, $date) = explode( '-' , substr( $valueObject->created_date , 0 , 10 ) );
			$cdate = $valueObject->created_date;
			$cby = $valueObject->created_by;

			if( empty($filename) && !empty($allValues['xfile']) ){
				$filename_array = explode(":", $allValues['xfile']);
				$filename = $filename_array[0];
				$filesize = $filename_array[1];
			}
		}else{
			$cdate = "now()";
			$cby = $_SESSION['session_user_id'];
		}

		$total_pages = $this->dbConnect->getTotalNumber( $this->tbname, "publish = '1' AND status = '1'");
		$allValues['order'] = empty($allValues['order']) ? $total_pages + 1 : $allValues['order'];

		$sql_data_array = array(
												'name'				=> $allValues['name'],
												'jobtitle'				=> $allValues['jobtitle'],
												'fulltxt'				=> $allValues['fulltxt'],
												'linkfile'				=> $allValues['linkfile'],
												'filename'			=> $filename,
												'filesize'			=> $filesize,
												'urls'					=> $allValues['urls'],
												'phone'				=> $allValues['phone'],
												'email'				=> $allValues['email'],
												'ordering'			=> $allValues['order'],
												'publish'			=> 1,
												'status'	 			=> 1,
												'modified_date' 	=> "now()",
												'modified_by' 	=> $_SESSION['session_user_id'],
												'created_date'  	=> $cdate,
												'created_by'  	=> $cby
											);

			if( $allValues['submitbutton'] === "Create" && $allValues['press'] === "create" ){
				if( !empty($allValues['name']) ){
					$this->dbConnect->getDBAction($this->tbname, $sql_data_array);
				}
				unset($sql_data_array);
				echo $this->setJS->setRedirectJScript( $node, $sxtra, $cxtra, $pxtra);
			} elseif( $allValues['submitbutton'] === "Update" && $allValues['press'] === "update" ){
				if( !empty($allValues['name']) ){
					$this->dbConnect->getDBAction($this->tbname, $sql_data_array, "update", "id = {$allValues['id']}");
				}
				unset($sql_data_array);
				echo $this->setJS->setRedirectJScript( $node, $sxtra, $cxtra, $pxtra);
			} elseif( $allValues['submitbutton'] === "Delete" ){
				if(count($allValues['boardnum']) > 0){
					foreach ($allValues['boardnum'] as $key => $kw) {
						# $this->dbConnect->getQuery( "DELETE FROM {$this->tbname} WHERE id = '{$kw}'" );
						$this->dbConnect->getQuery( "UPDATE {$this->tbname} SET publish = '0' WHERE id = '{$kw}'" );
					}
				}else{
					if( !empty($allValues['id']) ){
						# $this->dbConnect->getQuery( "DELETE FROM {$this->tbname} WHERE id = '{$allValues['id']}'" );
						$this->dbConnect->getQuery( "UPDATE {$this->tbname} SET publish = '0' WHERE id = '{$allValues['id']}'" );
					}else{
						echo "<script type='text/javascript'>window.alert('Please select an Article from the list to delete.');</script>";
					}
				}
				unset($allValues);
				echo $this->setJS->setRedirectJScript( $node, $sxtra, $cxtra, $pxtra);
			} elseif( $allValues['submitbutton'] === "Update" ){
				if(count($allValues['boardnum'])>0){
					foreach ($allValues['boardnum'] as $key => $kw) {
						$this->dbConnect->getQuery( "UPDATE {$this->tbname} SET ordering = {$allValues['short'][$kw]} WHERE id = '{$kw}'" );
					}
				} else {
					echo "<script type='text/javascript'>window.alert('Please select an Article from the list to update.');</script>";
				}
				unset($allValues);
				echo $this->setJS->setRedirectJScript( $node, $sxtra, $cxtra, $pxtra);
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
										."\n\t<th width=\"15%\" align=\"center\" class=\"board_line\">Photo</th>"
										."\n\t<th align=\"center\" class=\"board_line\">Full Name</th>"
										."\n\t<th width=\"20%\" align=\"center\" class=\"board_line\">Job Title</th>"
										."\n\t<th width=\"70\" align=\"center\" class=\"board_line\">Order</th>"
										."\n\t<th width=\"50\" align=\"center\" class=\"board_line\">Status</th>"
										."\n\t<th width=\"70\"align=\"center\" class=\"board_line\"><small>Views</small></th>"
										."\n\t<th width=\"70\"align=\"center\"><small>ID</small></th>"
										."\n\t</tr>";
		} elseif( $opt === "boardlist") {
			$listtxt =  "\n\t<tr height=\"25\" %s>"
										."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\" style=\"padding-top:7px;\"><small>%s</small></td>"
										."\n\t<td class=\"td_bot left_space\" align=\"left\" >&nbsp;&nbsp;<a href=\"%s\" target=\"_self\" title=\"%s\" onfocus=\"this.blur()\">%s</a></td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\">%s</td>"
										."\n\t<td class=\"td_bot\" align=\"center\">%s</td>"
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