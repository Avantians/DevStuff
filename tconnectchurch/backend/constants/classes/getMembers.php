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

class getMembers {

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

		$this->tbname				= "members";
		$this->nodeValue			= "member";
		$this->toolbarTitle			= "Member Manager";
		$this->form					= $this->setJS->setDeleteJScript() . "\n<form id=\"stylizedForm\" name=\"anybovard\" method=\"post\" action=\"%s\" enctype=\"multipart/form-data\">%s\n</form>";
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get contents
 *  ------------------------------------------------------------------------- */
	public function getContents( $gvalue ){

		$_contents  = "\n<!--// Bof Main Contents //-->\n\n<dl>";
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

			$pxtra 			= isset( $gvalue['p'] ) ? "&p=".$gvalue['p'] : "";
			$_action_url	= $this->basicURL."indexx.php?node={$this->nodeValue}{$pxtra}";

			if( $gvalue['m'] === "update" ){
				$this->dbConnect->getQuery( "UPDATE {$this->tbname} SET members_status = {$gvalue['st']}, members_modified_date = 'now()' WHERE id = '{$gvalue['id']}'" );
				echo $this->setJS->setRedirectJScript( $this->nodeValue, $sxtra, $cxtra, $pxtra );
			}

			$formbtns	= "<!--<input type=\"submit\" name=\"submitbutton\" class=\"deletebutton\" value=\"Delete\" onfocus=\"this.blur()\"/>--><a href=\"".$this->basicURL."indexx.php?node={$this->nodeValue}{$pxtra}&amp;m=view"."\" class=\"abtn\" onfocus=\"this.blur()\"><span class=\"icon-32-new\" title=\"New\"></span>New</a>&nbsp;&nbsp;";

			$total_list			= $this->dbConnect->getTotalNumber( $this->tbname, "members_status = '1' AND members_option = '1'{$optail}" );
			$total_items		= $total_list;
			$total_page		= ceil( $total_list/CONFIG_HOW_MANY_ARTICLES_PER_PAGE );
			$noPage 			= isset( $gvalue['p'] ) ?  $gvalue['p'] : 1;
			$first		 			= $total_list == 0 ? 1 : CONFIG_HOW_MANY_ARTICLES_PER_PAGE *( $noPage-1 );
			$last					= $total_list == 0 ? 0 : CONFIG_HOW_MANY_ARTICLES_PER_PAGE * $noPage;
			$loop_number	= $total_list - ( $noPage-1 ) * CONFIG_HOW_MANY_ARTICLES_PER_PAGE;

			$_boardlist  = $this->setAllelements->setToolbar( $formbtns, "", $this->toolbarTitle );
			$_boardlist .= "\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tr><td valign=\"top\">";
			$_boardlist .= "\n\t<div class=\"totlaitems\">Total of members: {$total_items}</div>";
			$_boardlist .= "\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">";
			$_boardlist .= "\n\t<tbody>";
			$_boardlist .= $this->getListFormat( "boardtitle" );

			$list_query = $this->dbConnect->getQuery( "SELECT * FROM {$this->tbname} WHERE publish = '1' AND members_option = '1' ORDER BY members_firstname DESC LIMIT {$first}, ".CONFIG_HOW_MANY_ARTICLES_PER_PAGE."" );
			while ( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
    			$bgClass			= ( $loop_number/2 ) == floor( $loop_number/2 ) ? " class=\"list_odd\"" : " class=\"list_even\"";
				$pURL				= $this->basicURL."indexx.php?node={$this->nodeValue}&amp;id={$list_values['id']}{$pxtra}&amp;m=view";
				$type_name		= $this->dbConnect->getMemberType( $list_values['members_type'] );
				$created_date	= _getShortFormatDate( $list_values['members_registered_date'] );
				$last_date			= _getShortFormatDate( $list_values['members_date_of_last_login'] );
				$full_name 		= stripslashes( $list_values['members_lastname'] ) ." ". stripslashes( $list_values['members_firstname'] );

				$_boardlist .= sprintf( $this->getListFormat( "boardlist" ), $bgClass, $loop_number, $pURL, $full_name, $full_name, $list_values['members_email'],$list_values['members_username'], $type_name,  $this->setAllelements->setPublishIcon( $this->nodeValue, $list_values['id'], $list_values['members_status'], "", "", $pxtra ), $list_values['id'], $created_date, $last_date );
				$loop_number--;
			}
			$_boardlist .= "\n\t</tbody>";
			$_boardlist .= "\n\t</table>";
			$_boardlist .= $this->setAllelements->setPagelistblock( $this->nodeValue, $total_page, $noPage, $sxtra, $cxtra );
			$_boardlist .= "\n\t</td>";
			$_boardlist .= "\n</tr></table>";

			$_boardlistWithForm = sprintf( $this->form, $_action_url, $_boardlist );

			return $_boardlistWithForm;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get display
 *  ------------------------------------------------------------------------- */
	public function getDisplay( $gvalue ){

		$sxtra = isset( $gvalue['sid'] ) ? "&sid=".$gvalue['sid'] : "";
		$cxtra = isset( $gvalue['cid'] ) ? "&cid=".$gvalue['cid'] : "";
		$pxtra = isset( $gvalue['p'] ) ? "&p=".$gvalue['p'] : "";

		$_action_url  = $this->basicURL."indexx.php?node={$this->nodeValue}{$pxtra}";

		if( !empty( $gvalue['id'] ) ){
			$valueObject			= $this->dbConnect->getContents( $this->tbname, "id = '{$gvalue['id']}' AND publish='1'" );
			$buttontxt				= "Update";
			$buttonlink				="parent.location='".$this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$cxtra}{$pxtra}'";
			$presstxt				= "update";
			$extrabtn				= "<input type=\"submit\" name=\"submitbutton\" class=\"deletebutton\" value=\"Delete\" />";
			$members_type		= $valueObject->members_type;
			$members_level		= $valueObject->members_level;
			$caValue				= explode( ";", $valueObject->categoriesid );
		} else {
			$buttontxt				= "Create";
			$presstxt				= "create";
			$members_type		= 6;
			$members_level 	= 7;
			$buttonlink				="parent.location='".$this->basicURL."indexx.php?node={$this->nodeValue}{$sxtra}{$cxtra}{$pxtra}'";
		}

		$memberLevel_array	= $this->dbConnect->getAccessLevel( "members_level_status = '1'" );
		$memberType_array	= $this->dbConnect->getMemberTypeList( "members_type_status = '1'" );
		$yesno_array				= array( array( "id"=>"0","text"=>"NO" ), array( "id"=>"1","text"=>"YES" ) );
		$unlock_array			= array( array( "id"=>"0","text"=>"YES" ), array( "id"=>"1","text"=>"NO" ) );
		$cateroies_array			= $this->dbConnect->getCategoriesList( "publish = '1' AND ctype != 'page' ORDER BY ordering" );
		$formbtns					= $extrabtn . "<input type=\"submit\" name=\"submitbutton\" class=\"updatebutton\" value=\"{$buttontxt}\" /> <a onClick=\"{$buttonlink}\" class=\"cancelbutton\">Cancel</a>&nbsp;&nbsp;";

		$_boarditem .= $this->setAllelements->setToolbar( $formbtns, "", $this->toolbarTitle );
		$_boarditem .=
								"\n<dl class=\"pages_component\">\n\t<dd>"
								."\n<table border=\"0\"><tr><td width=\"150\" valign=\"top\"  align=\"right\"><lable for=\"sectionid\" class=\"darkOrange_txt txt100\">Type:</lable></td><td width=\"200\">".$this->setAllelements->setPullDownMenu( "members_type", $memberType_array ,$members_type, "id=\"members_type\" class=\"blue\"" )."</td><td  width=\"150\" valign=\"top\" align=\"right\"><lable for=\"categoriesid\" class=\"darkOrange_txt txt100\">Level:</lable></td><td width=\"180\">".$this->setAllelements->setPullDownMenu( "members_level", $memberLevel_array ,$members_level, "id=\"members_level\" class=\"blue\"" )."</td></tr></table>"

								."\n<table border=\"0\" width=\"695\"><tr><td  align=\"left\" valign=\"top\" width=\"395\">\n<lable for=\"members_firstname\" class=\"darkOrange_txt\">First Name:</lable>\n<input id=\"members_firstname\" type=\"text\" name=\"members_firstname\" value=\"".$valueObject->members_firstname."\" class=\"extra26\"  /></td><td align=\"left\" valign=\"top\">\n<lable for=\"members_lastname\" class=\"darkOrange_txt\">Last Name:</lable>\n<input id=\"members_lastname\" type=\"text\" name=\"members_lastname\" value=\"".$valueObject->members_lastname."\" class=\"extra26\"  /></td></tr>"
								."\n<tr><td  align=\"left\" valign=\"top\" width=\"395\">\n<lable for=\"members_email\" class=\"darkOrange_txt\">User Name:</lable>\n<input id=\"members_username\" type=\"text\" name=\"members_username\" value=\"".$valueObject->members_username."\" class=\"extra26\"  /></td><td align=\"left\" valign=\"top\">&nbsp;</td></tr>"

								."\n<tr><td  align=\"left\" valign=\"top\" width=\"395\">\n<lable for=\"members_email\" class=\"darkOrange_txt\">Email:</lable>\n<input id=\"members_email\" type=\"text\" name=\"members_email\" value=\"".$valueObject->members_email."\" class=\"extra26\"  /></td><td align=\"left\" valign=\"top\">\n<lable for=\"members_alias\" class=\"darkOrange_txt\">Alias:</lable>\n<input id=\"members_alias\" type=\"text\" name=\"members_alias\" value=\"".$valueObject->members_alias."\" class=\"extra26\"  /></td></tr>"

								."\n<tr><td  align=\"left\" valign=\"top\" width=\"395\">\n<lable for=\"members_date_of_last_login\" class=\"darkOrange_txt\">Last visit:</lable>\n<input id=\"members_date_of_last_login\" type=\"text\" name=\"members_date_of_last_login\" value=\"".$valueObject->members_date_of_last_login."\" class=\"extra26 readyonly\"  readonly=\"readonly\"/></td><td align=\"left\" valign=\"top\">\n<lable for=\"members_registered_date\" class=\"darkOrange_txt\">Registered date:</lable>\n<input id=\"members_registered_date\" type=\"text\" name=\"members_registered_date\" value=\"".$valueObject->members_registered_date."\" class=\"extra26 readyonly\"  readonly=\"readonly\" /></td></tr>"

								."\n<tr><td  align=\"left\" valign=\"top\" width=\"395\">\n<lable for=\"members_password\" class=\"darkOrange_txt\">Password:</lable>\n<input id=\"members_password\" type=\"password\" name=\"members_password\" class=\"extra26\"  /><small>Must contain at least ONE upper case, ONE lower case letter and ONE digit</small></td><td align=\"left\" valign=\"top\">\n<lable for=\"members_status\" class=\"darkOrange_txt\">Block?</lable>\n".$this->setAllelements->setPullDownMenu( "members_status", $unlock_array ,$valueObject->members_status, "id=\"members_status\" class=\"blue\"" )."</td></tr>"

								."\n<tr><td  align=\"left\" valign=\"top\" width=\"395\" >\n<!--<lable for=\"categoriesid\" class=\"darkOrange_txt\">Category:</lable>\n". $this->setAllelements->setPullDownMenu( "categoriesid[]", $cateroies_array , $caValue, "multiple=\"multiple\" size=\"7\" id=\"categoriesid\" class=\"blue multiselect fixedwidth\"" ) ."--></td><td align=\"left\" valign=\"top\"></td></tr>"

								."<tr><td align=\"left\" valign=\"top\">\n<lable for=\"members_password2\" class=\"darkOrange_txt\">Re-password:</lable>\n<input id=\"members_password2\" type=\"password\" name=\"members_password2\" class=\"extra26\"  /></td><td align=\"left\" valign=\"top\">\n<lable for=\"members_email_confirmed\" class=\"darkOrange_txt\">Email confirmed?</lable>\n".$this->setAllelements->setPullDownMenu( "members_email_confirmed", $yesno_array ,$valueObject->members_email_confirmed, "id=\"members_email_confirmed\" class=\"blue\"" )."</td></tr></table>"

								."\n<lable for=\"filename\" class=\"darkOrange_txt\">Avatar:</lable><small>To upload file. Please <strong>DO NOT</strong> use KOREAN file name and white space( s ). <strong>ex ) filename_date-012.jpg</strong></small>"
								."\n<input id=\"filename\" type=\"file\" name=\"filename\" size=\"30\" class=\"\" /><small>".$valueObject->members_image."</small><br/>"

								. getDisplayImg( $valueObject->members_image, 100, false, $valueObject->members_firstname );

		$_boarditem .=  "\n<input type=\"hidden\" name=\"id\" value=\"{$valueObject->id}\" />"
								."\n<input type=\"hidden\" name=\"xfile\" value=\"{$valueObject->members_image}\" />"
								."\n<input type=\"hidden\" name=\"press\" value=\"{$presstxt}\" />"
								."</dd>\n</dl>";

		$_boardlistWithForm = sprintf( $this->form, $_action_url, "\n<script type=\"text/javascript\" src=\"". $this->static_url . CONFIG_STATIC_SUBFOLDER ."/js/back-members.js\"></script>".$_boarditem );

		$bItems  = "\n<!--// Bof Contents //-->";
		$bItems .= $_boardlistWithForm;
		$bItems .= "\n<!--// Eof Contents //-->\n\n";

		return $bItems;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get create
 *  ------------------------------------------------------------------------- */
	public function getCreate( $allValues ){
		global $user_ip;

		$sxtra = isset( $allValues['sid'] ) ? "&sid=".$allValues['sid'] : "";
		$cxtra = isset( $allValues['cid'] ) ? "&cid=".$allValues['cid'] : "";
		$pxtra = isset( $allValues['p'] ) ? "&p=".$allValues['p'] : "";
		$node = $allValues['node'];
		if ( $_FILES[filename][name] && $error_flag == false && empty( $file ) ){
			$uploading_file	= getUploadingFile( $_FILES[filename] );
			$filename			= $uploading_file['name'];
			$filetype 			= $uploading_file['type'];
			$filesize 			= $uploading_file['size'];
			unset( $uploading_file );
		}

		if( $allValues['submitbutton'] === "Update" && $allValues['press'] === "update" ){
			$query								= "SELECT * FROM {$this->tbname} WHERE id = '{$allValues['id']}'";
			$valueObject 						= $this->dbConnect->getObject( $query );
			list( $year, $month, $date )	= explode( '-' , substr( $valueObject->members_registered_date , 0 , 10 ) );
			$cdate 								= $valueObject->members_registered_date;

			if( empty( $filename ) && !empty( $allValues['xfile'] ) ){
				$filename_array	= explode( ":", $allValues['xfile'] );
				$filename			= $filename_array[0];
				$filesize			= $filename_array[1];
			}

		}else{
			$cdate = "now()";
		}

		$username			= stripslashes( str_replace( "@", "&#64;;", trim( $allValues['members_email'] ) ) );

		$sql_data_array = array(
												'members_firstname'				=> $allValues['members_firstname'],
												'members_lastname'				=> $allValues['members_lastname'],
												'members_username'				=> $allValues['members_username'],
												'members_image'					=> $filename,
												'members_alias'					=> $allValues['members_alias'],
												'members_email'					=> $username,
												'members_email_confirmed'	=> $allValues['members_email_confirmed'],
												'members_type'						=> $allValues['members_type'],
												'members_level'					=> $allValues['members_level'],
												'members_status'					=> $allValues['members_status'],
												'members_modified_date' 		=> "now()",
												'members_registered_date'  	=> $cdate
								 );
		if( !empty( $allValues['members_password'] ) ){
			$pw_array['members_password'] = _getEncryptPassword( stripslashes( $allValues['members_password'] ) );
			$sql_data_array = array_merge( $pw_array, $sql_data_array );
		}

			if( $allValues['submitbutton'] === "Create" && $allValues['press'] === "create" ){
				if( !empty( $allValues['members_email'] ) ){
					$this->dbConnect->getDBAction( $this->tbname, $sql_data_array );
				}
				unset( $sql_data_array );
				echo $this->setJS->setRedirectJScript( $node, $sxtra, $cxtra, $pxtra );

			} elseif( $allValues['submitbutton'] === "Update" && $allValues['press'] === "update" ){
				if( !empty( $allValues['members_email'] ) ){
					$this->dbConnect->getDBAction( $this->tbname, $sql_data_array, "update", "id = {$allValues['id']}" );
				}
				unset( $sql_data_array );
				echo $this->setJS->setRedirectJScript( $node, $sxtra, $cxtra, $pxtra );

			} elseif( $allValues['submitbutton'] === "Delete" ){
				if( count( $allValues['boardnum'] ) > 0 ){
					foreach ( $allValues['boardnum'] as $key => $kw ){
						$this->dbConnect->getQuery( "DELETE FROM {$this->tbname} WHERE id = '{$kw}'" );
						#$this->dbConnect->getQuery( "UPDATE {$this->tbname} SET publish = '0', members_status = '0' WHERE id = '{$kw}'" );
					}

				}else{
					if( !empty( $allValues['id'] ) ){
						$this->dbConnect->getQuery( "DELETE FROM {$this->tbname} WHERE id = '{$allValues['id']}'" );
						#$this->dbConnect->getQuery( "UPDATE {$this->tbname} SET publish = '0', members_status = '0' WHERE id = '{$allValues['id']}'" );
					}else{
						echo "<script type='text/javascript'>window.alert( 'Please select an Article from the list to delete.' );</script>";
					}
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
										."\n\t<th width=\"25\" align=\"center\" class=\"board_line\"><small>#</small></th>"
										."\n\t<th align=\"center\" class=\"board_line\">Full Name</th>"
										."\n\t<th align=\"center\" class=\"board_line\">Email</th>"
										."\n\t<th width=\"80\" align=\"center\" class=\"board_line\">Username</th>"
										."\n\t<th width=\"90\" align=\"center\" class=\"board_line\">Level</th>"
										."\n\t<th width=\"50\" align=\"center\" class=\"board_line\">Status</th>"
										."\n\t<th width=\"25\"align=\"center\" class=\"board_line\"><small>ID</small></th>"
										."\n\t<th width=\"80\"align=\"center\" class=\"board_line\"><small>Created Date</small></th>"
										."\n\t<th width=\"60\"align=\"center\"><small>Last Visit</small></th>"
										."\n\t</tr>";

		} elseif( $opt === "boardlist" ){
			$listtxt =  "\n\t<tr height=\"25\" %s>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
										."\n\t<td class=\"td_bot left_space\" align=\"left\" ><a href=\"%s\" target=\"_self\" title=\"%s\" onfocus=\"this.blur()\"><small>%s</small></a></td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
										."\n\t<td class=\"td_bot\" align=\"center\"><small>%s</small></td>"
										."\n\t<td class=\"td_bot engtxt\" align=\"center\"><small>%s</small></td>"
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