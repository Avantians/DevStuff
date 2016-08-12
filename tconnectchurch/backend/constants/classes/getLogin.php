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

class getLogin {

	public $dbConnect;
	public $basicURL;
	public $static_url;
	public $setAllelements;
	public $error_flag;
	public $error_message;

	public function __construct( $baseURL ){
		global $Bon_db, $allElements;

		$this->dbConnect	 			= $Bon_db;
		$this->basicURL					= $baseURL;
		$this->static_url 					= rtrim( str_replace( "www", CONFIG_STATIC_SUBDOMAIN, str_replace( "/backend", "", $this->basicURL ) ), "/" );
		$this->setAllelements	= $allElements;
		$this->error_flag						= false;
		$this->error_message	= array();

		if ( !preg_match( "/".$_SERVER[HTTP_HOST]."/i", $_SERVER[SERVER_NAME] ) ){
			$this->error_message	= PLEASE_WRITE_ARTICLE_PROPERLY;
			$this->error_flag			= true;
		}
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To display HTML
 *  ------------------------------------------------------------------------- */
	public function setHTML ( $optInfo, $back2URL, $eMessage="" ){

		if( _getCheckNullorNot( $_SESSION['session_user_id'] ) && $_SESSION['guest'] == 9 ){
			$this->error_flag = true;
		}

		$back2URL		=  !_getCheckNullorNot( $back2URL ) ? $this->basicURL : $back2URL."index.php?node=login";
		$valueHtmlSet	=  _getCheckNullorNot( $eMessage ) ?  "<p class=\"error\">".$eMessage."</p>" : "";

		if( $this->error_flag ){
			_getRedirect( $this->basicURL."indexx.php" );
		}
		elseif( $optInfo === "forgot_password" ){
			$valueHtmlSet  .= $this->error_flag ? "\n".$this->error_message	: "";
			$valueHtmlSet  .= "\n<div class=\"admin_login\"><h2>Forgot your password?</h2>"
									."\n<form action=\"".$this->basicURL."index.php?node=forgot_password\" method=\"post\" name=\"login\" id=\"stylizedForm\">"

									."\n<br /><br /><table border=\"0\" class=\"login_table\"><tr><td  align=\"left\" valign=\"top\">\n\t<lable for =\"username\">Your email address</lable>\n\t<input type=\"text\" name=\"username\" tabindex=\"1\" style=\"width:97%;\"/></td></tr></table>"

									."\n\t<input type=\"hidden\" name=\"returnurl\" value=\"".$back2URL."\" />"
									."\n\t<input type=\"hidden\" name=\"option\" value=\"".$optInfo."\" />"
									."\n\t<input type=\"hidden\" name=\"press\" value=\"doing\" />"
									."\n\t<input type=\"submit\" name=\"submitbutton\" value=\"Reset\" id=\"submit-login\" tabindex=\"3\" class=\"submitbutton\"/>"
									."\n</form>"
									."\n</div>";

			return $valueHtmlSet;
		}
		else{
			$valueHtmlSet  .= $this->error_flag ? "\n".$this->error_message	: "";
			$valueHtmlSet  .= "\n<div class=\"admin_login\"><h2>Admin Login</h2>"
									."\n<form action=\"".$this->basicURL."index.php?node=login\" method=\"post\" name=\"login\" id=\"stylizedForm\">"

									."\n<table border=\"0\" class=\"login_table\"><tr><td  align=\"left\" valign=\"top\">\n\t<lable for =\"username\">User ID ( email address )</lable>\n\t<input type=\"text\" name=\"username\" tabindex=\"1\" style=\"width:97%;\"/></td></tr><tr><td align=\"left\" valign=\"top\">\n\t<lable for =\"password\">Password</lable>\n\t<input type=\"password\" name=\"password\" tabindex=\"2\" style=\"width:97%;\"/></td></tr></table>"

									."\n\t<input type=\"hidden\" name=\"returnurl\" value=\"".$back2URL."\" />"
									."\n\t<input type=\"hidden\" name=\"option\" value=\"".$optInfo."\" />"
									."\n\t<input type=\"hidden\" name=\"press\" value=\"doing\" />"
									."\n\t<input type=\"submit\" name=\"submitbutton\" value=\"Login\" id=\"submit-login\" tabindex=\"3\" class=\"submitbutton\"/>"
									."\n\t<br/><a href=\"{$this->basicURL}index.php?node=forgot_password\" title=\"Forgot your password?\" target=\"_self\" onfocus=\"this.blur()\">Forgot your password?</a>"
									."\n</form>"
									."\n</div>";

			return $valueHtmlSet;
		}
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To login
 *  ------------------------------------------------------------------------- */
	public function setLogin( $allValues ){
		global $_ipno;

		if( $allValues['option'] === "login" ){
			if( _getCheckEmail( $allValues['username'] ) ){
				$username		= stripslashes( strtolower( $allValues['username'] ) );
				$username		= str_replace( "@", "&#64;", trim( $username ) );
				$extra_section	= "\n AND members_email = '". $username ."'";
			}
			else{
				$username		= stripslashes( strtolower( $allValues['username'] ) );
				$extra_section	= "\n AND members_username = '". $username ."'";
			}

			$password		= stripslashes( $allValues['password'] );
			$returnURL	= empty( $allValues['returnurl'] ) ? $this->basicURL : $allValues['returnurl'];
			unset( $allValues );

			if( !_getCheckNullorNot( $password ) ){
				$this->error_message	= WRONG_LOGIN_INFORMATION."-01";
				$LoginValue				= $this->setHTML( "login", $this->basicURL, $this->error_message );
			}
			elseif( !$this->dbConnect->getMember( $username, $password, $extra_section ) ){
				$this->error_message	= WRONG_LOGIN_INFORMATION."-02";
				$LoginValue				= $this->setHTML( "login", $this->basicURL, $this->error_message );
			}
			else{
				$query	= "SELECT *"
							. "\n FROM members"
							. "\n WHERE members_email_confirmed = '1'"
							. "\n AND members_status = '1' AND members_level <= '3'"
							. $extra_section;

				$mem_query	= $this->dbConnect->getQuery( $query );
				$members		= $this->dbConnect->getFetch_Array( $mem_query );

				if ( @$members['id'] ){
					if ( _getValidatePassword( $password, $members['members_password'] ) ){
							//Construct Session ID
							$logintime		= time();
							$session_id	= md5( $members['id'] . $members['members_email'] . $members['members_type'] . $logintime );

							session_name( md5( $this->basicURL ) );

							//Add Session ID entry to DB
							$query = "INSERT INTO members_session"
							. "\n SET session_id = \"".$session_id."\", username = \"". $members['members_email']."\", time = \"".$logintime."\", guest = \"9\", userid = \"".$members['id']."\", usertype = \"".$members['members_type']."\", userlevel = \"".$members['members_level']."\", groupid = \"".$members['members_group_id']."\", section = \"b\", ipno = \"".$_ipno."\"";
							if ( $this->dbConnect->getQuery( $query ) ){
								$_SESSION['session_id'] 				= $session_id;
								$_SESSION['session_user_id'] 		= $members['id'];
								$_SESSION['session_username'] 	= $members['members_email'];
								$_SESSION['guest'] 						= 9;
								$_SESSION['section'] 					= "b";
								$_SESSION['session_usertype'] 		= $members['members_type'];
								$_SESSION['session_userlevel'] 	= $members['members_level'];
								$_SESSION['session_gid'] 				= $members['members_group_id'];
								$_SESSION['session_time'] 			= $logintime;
								$_SESSION['ipno'] 						= $_ipno;
							}
							else{
								$this->error_message	= WRONG_LOGIN_INFORMATION."-03";
								return $this->setHTML( "login", $this->basicURL, $this->error_message );
							}
							session_write_close();

							$update_query	= "UPDATE members "
													. "\n SET members_date_of_last_login = now(),"
													. "\n members_visited_count = members_visited_count + 1"
													. "\n WHERE members_email_confirmed = '1'"
													. "\n AND members_status = '1' "
													. "\n AND id = '".( int )$_SESSION['session_user_id'] ."'"
													. "\n AND members_email = '". $_SESSION['session_username']  ."'";

							if( $this->dbConnect->getQuery( $update_query ) ){
								unset( $gvalue );
								_getRedirect( $returnURL );
							}
							else{
								$this->error_message	= WRONG_LOGIN_INFORMATION2." -04";
								$LoginValue				= $this->error_message;
							}

					}
					else{
							$this->error_message	= WRONG_LOGIN_INFORMATION." -05";
							$LoginValue				= $this->setHTML( "login", $this->basicURL, $this->error_message );
					}

				}
				else{
					$this->error_message	= WRONG_LOGIN_INFORMATION." -06";
					$LoginValue				= $this->setHTML( "login", $this->basicURL, $this->error_message );
				}
			}

		}
		elseif( $allValues['option'] === "forgot_password" ){
			$username	= stripslashes( strtolower( $allValues['username'] ) );
			$username	= str_replace( "@", "&#64;", trim( $username ) );

			$total_no 		= $this->dbConnect->getTotalNumber( "members", "members_email_confirmed = '1' AND members_status = '1' AND ( members_type = '1' OR members_type = '2' ) AND ( members_level > '0' AND members_level < '4' ) AND members_email = '". $username ."'" );

			if( $total_no == 1 ){
				$temp_pw			= _getMakePassword( 7 );
				$encrypted_pw	= _getEncryptPassword( $temp_pw );
				$email_subject	= "We reseted your password.";
				$email_text		= "We reseted your password.<br/><br/> Password: ".$temp_pw."<br/><br/><br/>Thank you.";

				$mquery			= "SELECT * FROM members"
										. "\n WHERE members_email_confirmed = '1' AND members_status = '1' AND ( members_type = '1' OR members_type = '2' ) AND ( members_level > '0' OR members_level < '4' )"
										. "\n AND members_email = '". $username ."'";
				$mObject 			= $this->dbConnect->getObject( $mquery );

				//echo ( $mObject->members_lastname.$mObject->members_firstname . _getActualEmail( $mObject->members_email ) . $email_subject . $email_text . CONFIG_SITE_NAME . CONFIG_SITE_EMAIL );
				//exit();

				$vEmail 			= _getSendEmail( $mObject->members_lastname.$mObject->members_firstname, _getActualEmail( $mObject->members_email ), $email_subject, $email_text, CONFIG_SITE_NAME, CONFIG_SITE_EMAIL );
				if( $vEmail ){
					$update_query	= "UPDATE members SET members_modified_date = now(), members_password = {$encrypted_pw} WHERE members_email = '". $mObject->members_email  ."'";
					if( $this->dbConnect->getQuery( $update_query ) ){
						_getRedirect( $returnURL );
					}
				}
			}
			else{
				$this->error_message	= WRONG_EMAIL_INFORMATION." -07";
				$LoginValue = $this->setHTML( "login", $this->basicURL, $this->error_message );
			}
		}
		else{
			$this->error_message	= WHAT_R_U_DOING." -07";
			$LoginValue = $this->setHTML( "forgot_password", $this->basicURL, $this->error_message );
		}

			return $LoginValue;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To logout
 *  ------------------------------------------------------------------------- */
	public function setLogout(){
		//If it's desired to kill the session, also delete the session cookie.
		//Note: This will destroy the session, and not just the session data!
		if( !empty( $_SESSION['session_username'] ) ){
				if( isset( $_COOKIE[session_name()] ) ){
				   setcookie( session_name(), "", time()-42000, '/' );
				}

				$delete_query	= "DELETE FROM members_session"
										. "\n WHERE session_id = '".$_SESSION['session_id'] ."'"
										. "\n AND section = 'b' "
										. "\n AND guest = '9'"
										. "\n AND username = '". $_SESSION['session_username']  ."'";

				if( $this->dbConnect->getQuery( $delete_query ) ){
					//Unset all of the session variables.
					$_SESSION = array();
					session_start();
					unset($_SESSION);
					session_destroy();
					$LogOutText  = "<p class=\"page_txt_heading\">LogOut</p>";
					$LogOutText .= "\n<div class=\"admin_login\">";
					$LogOutText .= LOGOUT_SUCCESSFULLY;
					$LogOutText .= "\n</div>";
					_getRedirect( $this->basicURL );

				}
				else{
					$LogOutText  = "<p class=\"page_txt_heading\">LogOut</p>";
					$LogOutText .= "\n<div class=\"admin_login\">";
					$LogOutText .= LOGOUT_UNSUCCESSFULLY;
					$LogOutText .= "\n</div>";
				}

		}
		else{
			_getRedirect( $this->basicURL );
		}

		return $LogOutText;
	}
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!