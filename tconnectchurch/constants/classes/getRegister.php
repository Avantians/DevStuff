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

class getRegister {

	public  $basicURL;
	public  $error_flag;
	public  $error_message;

	public function __construct( $baseURL ){
		$this->basicURL		= $baseURL;
		$this->error_flag			= false;
		$this->error_message	= array();

		if ( !preg_match( "/".$_SERVER[HTTP_HOST]."/i", $_SERVER[SERVER_NAME] ) ){
			$this->error_message	= PLEASE_WRITE_ARTICLE_PROPERLY;
			$this->error_flag				= true;
		}
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]:: To
 *  ------------------------------------------------------------------------- */
	public function setHTML ( $optInfo, $backtourl="/", $eMessage="" ){
		if ( $this->error_flag ){
			return $this->error_message;
		} else {
		/*
			$valueHtmlSet  ="<script type=\"text/javascript\" src=\"../static/js/jquery.pstrength-min.1.2.js\"></script>";
			$valueHtmlSet .= "<script type=\"text/javascript\">
										$( function(){
										$( '.password' ).pstrength();
										} );
										</script>";
		*/
			$valueHtmlSet =  !empty( $eMessage ) ?  "<p class=\"error\">".$eMessage."</p>" : "";
			$valueHtmlSet .= "\n<p class=\"page_heading\">Registration</p>"
									.$message_tag
									."\n<div class=\"divborder\">"
									."\n<div class=\"divborder_inside\">"
									."\n<form action=\"".$this->basicURL."/register\" method=\"post\" name=\"login\" id=\"stylizedForm\">"
									."\n\t<lable for =\"first_name\">First Name</lable>"
									."\n\t<input type=\"text\" name=\"first_name\" id=\"first_name\" tabindex=\"1\"/><br/>"
									."\n\t<span id=\"first_nameInfo\" class=\"formtxt\">What's your first name?</span><br/>"

									."\n\t<lable for =\"last_name\">Last Name</lable>"
									."\n\t<input type=\"text\" name=\"last_name\" id=\"last_name\" tabindex=\"2\"/><br/>"
									."\n\t<span id=\"last_nameInfo\" class=\"formtxt\">What's your last name?</span><br/>"

									."\n\t<lable for =\"username\">User ID ( Email Address )</lable>"
									."\n\t<input type=\"text\" name=\"username\" id=\"username\" tabindex=\"3\"/><br/>"
									."\n\t<span id=\"usernameInfo\" class=\"formtxt\">Please use valid E-mail, you will need it to log in!</span><br/>"

									."\n\t<lable for =\"password\">Password</lable>"
									."\n\t<input type=\"password\" name=\"password\" id=\"password\" class=\"password\" tabindex=\"4\"/><br/>"
									."\n\t<span id=\"passwordInfo\" class=\"formtxt\">At least 5 characters: letters, numbers,  '#' and '!'</span><br/>"

									."\n\t<lable for =\"re_password\">Password Again</lable>"
									."\n\t<input type=\"password\" name=\"re_password\" id=\"re_password\" tabindex=\"5\"/><br/>"
									."\n\t<span id=\"re_passwordInfo\" class=\"formtxt\">Confirm password</span><br/>"

									."\n\t<input type=\"hidden\" name=\"returnurl\" value=\"".$backtourl."\" />"
									."\n\t<input type=\"hidden\" name=\"option\" value=\"".$optInfo."\" />"
									."\n\t<input type=\"hidden\" name=\"press\" value=\"doing\" />"
									."\n\t<input type=\"submit\" name=\"submitbutton\" value=\"Login\" id=\"submit-register\" tabindex=\"6\"/>"
									."\n</form>"
									."\n<script type=\"text/javascript\" src=\"../static/js/validation.js\"></script>"
									."\n</div>"
									."\n</div>";

			return $valueHtmlSet;
		}
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]:: To
 *  ------------------------------------------------------------------------- */
	public function setRegister( $allValues ){
		global $Bon_db, $_ipno;

		if ( $allValues['option'] === "register" ){
			$first_name			= stripslashes( $allValues['first_name'] );
			$last_name			= stripslashes( $allValues['last_name'] );
			$username			= stripslashes( str_replace( "@", "&#64;;", trim( $allValues['username'] ) ) );
			$password			= _getEncryptPassword( stripslashes( $allValues['password'] ) );

			$activation_code 	= md5( $username );
			$db_query				= "SELECT count( * ) AS total FROM members WHERE members_email_confirmed = '1' AND members_status = '1' AND members_email = '". $username ."'";
			$count_query		= $Bon_db->getQuery( $db_query );
			$count_values		= $Bon_db->getFetch_Array( $count_query );

			if ( $count_values['total'] >= 1 ){
					$RegisterValue = REGISTRATION_EXISTS_EMAIL;
			}else {
					$sql_data_array = array(
															'members_firstname'				=> $first_name,
															'members_lastname'				=> $last_name,
															'members_image'					=> "avatar/default-avatar.png",
															'members_alias'						=> strtolower( substr( $first_name, 0, 1 ).$last_name ),
															'members_dob'						=> 0000,
															'members_email'					=> $username,
															'members_password'				=> $password,
															'members_activation_code'		=> $activation_code,
															'members_city'						=> "",
															'members_province'				=> "",
															'members_country'					=> "",
															'members_phone'					=> "647-000-0000",
															'members_number_article'		=> 0,
															'members_email_confirmed'	=> 0,
															'members_visited_count'			=> 0,
															'members_type'						=> 6,
															'members_level'						=> 7,
															'members_group_id'				=> 1,
															'members_registered_date'		=> 'now()',
															'members_modified_date'		=> 'now()',
															'members_date_of_last_login'	=> 'now()',
															'members_status'					=> 0
														 );

					if ( $Bon_db->getDBAction( "members", $sql_data_array ) ){
							unset( $sql_data_array );

							$email_subject = "Registration Confirmation";
							$email_text = sprintf( REGISTRATION_ACTIVATION_EMAIL_TMP, $activation_code, $allValues['username'] );

							_getSendEmail( $first_name." " .$last_name, _getActualEmail( $username ), $email_subject, $email_text, CONFIG_SITE_NAME, _getActualEmail( CONFIG_SITE_EMAIL ) );

							if ( CONFIG_ADDITIONAL_REGISTER_EMAIL === "true" ){
								$email_subject = "Extra Registration Confirmation Email for admin";
								_getSendEmail( "Admin of ".CONFIG_SITE_NAME, CONFIG_ADMIN_EMAIL, $email_subject, $email_text, CONFIG_SITE_NAME, ( CONFIG_SITE_EMAIL ) );
							}

							$RegisterValue = REGISTRATION_ACTIVATION_TXT;
					}
			}
			unset( $gvalue );
		} else {
			$this->error_message	= WHAT_R_U_DOING." - R01";
			$RegisterValue = $this->setHTML( "register", $this->basicURL, $this->error_message );
		}
			return $RegisterValue;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]:: To
 *  ------------------------------------------------------------------------- */
	public function setActivation( $allValues ){
		global $Bon_db, $_ipno;

		$ActivationValue =  $allValues['op'] ."<br/>". $allValues['em'];

		$query	= "SELECT *"
					. "\n FROM members"
					. "\n WHERE members_email_confirmed = '0'"
					. "\n AND members_status = '0' AND members_activation_code = '". $allValues['op'] ."'";
		$activation_query	= $Bon_db->getQuery( $query );
		$activation		= $Bon_db->getFetch_Array( $activation_query );

		if ( @$activation['id'] ){
				$emailaddress = substr( $allValues['em'], 0, -11 );
				$emailaddress = str_replace( ".", "&Dagger;", trim( $emailaddress ) );

				if ( $emailaddress === $activation['members_email'] ){

					$update_query	= "UPDATE members "
											. "\n SET members_activated_date = now(),"
											. "\n members_modified_date = now(),"
											. "\n members_email_confirmed = 1,"
											. "\n members_status = 1"
											. "\n WHERE members_email_confirmed = '0'"
											. "\n AND members_status = '0' "
											. "\n AND members_activation_code = '". $allValues['op'] ."'"
											. "\n AND members_email = '". $emailaddress ."'";

					if ( $Bon_db->getQuery( $update_query ) ){
						$ActivationValue = REGISTRATION_ACTIVATION_SUCCESS;
					} else {
						$ActivationValue = REGISTRATION_ACTIVATION_FAILURE_C;
					}
				} else {
					$ActivationValue = REGISTRATION_ACTIVATION_FAILURE_B;
				}
		} else {
			$ActivationValue = REGISTRATION_ACTIVATION_FAILURE_A;
		}

		return $ActivationValue;
	}
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!