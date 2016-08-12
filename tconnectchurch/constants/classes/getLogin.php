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
 *  ----------------------------------------------------------------------- */
defined( "_VALID_MOS" ) or die( "Your system is not working properly." );

class getLogin {

	public $dbConnect;
	public $basicURL;
	public $error_flag;
	public $error_message;

	public function __construct( $baseURL ){
		global $Bon_db;

		$this->dbConnect	 				= $Bon_db;
		$this->basicURL						= $baseURL;
		$this->error_flag						= false;
		$this->error_message	= array(  );

		if ( !preg_match( "/".$_SERVER[HTTP_HOST]."/i", $_SERVER[SERVER_NAME] ) ){
			$this->error_message	= PLEASE_WRITE_ARTICLE_PROPERLY;
			$this->error_flag						= true;
		}
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]:: To
 *  ----------------------------------------------------------------------- */
	public function setHTML ( $optInfo, $back2URL, $eMessage="" ){
		global $_ci_path;
		
		if ( _getCheckNullorNot( $_SESSION['session_user_id'] ) && $_SESSION['guest'] == 0 ){
			$this->error_flag = true;
		}

		if ( $this->error_flag ){
			$this->error_message = LOGIN_ALREADY.$_SESSION['session_user_id'];

			return $this->error_message;
		} 
		else {
			$back2URL =  !_getCheckNullorNot( $back2URL ) ? $this->basicURL : $back2URL;

			if ( _getCheckNullorNot($eMessage) ){

			}
			$valueHtmlSet  = "\n<h1><a href=\"#\" title=\"".CONFIG_SITE_NAME." :: Login\" target=\"_self\">LogIn</a></h1>\n";
		//Share icons for facebook and twitter under page title			
			if ( CONFIG_SHARING_ICONS == "true" ){
				$sharing_icons = new getGeneral($baseURL);
				$valueHtmlSet .= $sharing_icons->getAddthis( $this->basicURL.getAllElements::setJustci_path( $_ci_path ), CONFIG_SITE_NAME." :: Login" );
			}
			
			$valueHtmlSet .= "\n<div class=\"pages_txt\">\n";
			$valueHtmlSet .= _getCheckNullorNot( $eMessage ) ?  "<p class=\"error\">". $eMessage ."</p>" : "";
			$valueHtmlSet .= "\n<form action=\"".$this->basicURL."/login\" method=\"post\" name=\"login\" id=\"stylizedLogin\">"
																	."\n\t<lable for =\"username\" class=\"logintxt\">User ID ( email address )</lable>"
																	."\n\t<input type=\"text\" name=\"username\" tabindex=\"1\"/>"
																	."\n\t<lable for =\"password\" class=\"logintxt\">Password</lable>"
																	."\n\t<input type=\"password\" name=\"password\" tabindex=\"2\"/>"
																	."\n\t<input type=\"hidden\" name=\"returnurl\" value=\"".$back2URL."\" />"
																	."\n\t<input type=\"hidden\" name=\"option\" value=\"".$optInfo."\" />"
																	."\n\t<input type=\"hidden\" name=\"press\" value=\"doing\" />"
																	."\n\t<input type=\"submit\" name=\"submitbutton\" value=\"Login\" class=\"btn-base greenbtn\" tabindex=\"3\"/>"
																	."\n</form>"
																	."\n</div>";

			return $valueHtmlSet;
		}
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]:: To
 *  ------------------------------------------------------------------------- */
	public function setLogin( $allValues ){
		global $_ipno;

		if ( $allValues['option'] === "login" ){
			if ( _getCheckEmail( $allValues['username'] ) ){
				$username				= stripslashes( strtolower( $allValues['username'] ) );
				$username				= str_replace( "@", "&#64;", trim( $username ) );
				$extra_section	= "\n AND members_email = '". $username ."'";
			}
			else {
				$username				= stripslashes( strtolower( $allValues['username'] ) );
				$extra_section	= "\n AND members_username = '". $username ."'";
			}

			$password		= stripslashes( $allValues['password'] );
			$returnURL  	= _getCheckNullorNot( $allValues['returnurl'] ) ? $allValues['returnurl'] : $this->basicURL;
			unset( $allValues );

			if ( !_getCheckNullorNot( $password ) ){
				$this->error_message	= WRONG_LOGIN_INFORMATION."-01";
				$LoginValue									= $this->setHTML( "login", $this->basicURL, $this->error_message );
			}
			elseif ( !$this->dbConnect->getMember( $username, $password, $extra_section ) ){
				$this->error_message	= WRONG_LOGIN_INFORMATION."-02";
				$LoginValue									= $this->setHTML( "login", $this->basicURL, $this->error_message );
			}
			else {
				$query	= "SELECT *"
											. "\n FROM members"
											. "\n WHERE members_email_confirmed = '1'"
											. "\n AND members_status = '1'"
											. $extra_section;
				$mem_query	= $this->dbConnect->getQuery( $query );
				$members			= $this->dbConnect->getFetch_Array( $mem_query );

				if ( @$members['id'] ){
					if ( _getValidatePassword( $password, $members['members_password'] ) ){
							//Construct Session ID
							$logintime		= time();
							$session_id	= md5( $members['id'] . $members['members_email'] . $members['members_type'] . $logintime );

							session_name( md5( $this->basicURL ) );

							//Add Session ID entry to DB
							$query = "INSERT INTO members_session"
							. "\n SET session_id = \"".$session_id."\", username = \"". $members['members_email']."\", time = \"".$logintime."\", guest = \"1\", userid = \"".$members['id']."\", usertype = \"".$members['members_type']."\", userlevel = \"".$members['members_level']."\", groupid = \"".$members['members_group_id']."\", ipno = \"".$_ipno."\"";

							if ( $this->dbConnect->getQuery( $query ) ){
								$_SESSION['session_id'] 							= $session_id;
								$_SESSION['session_user_id'] 		= $members['id'];
								$_SESSION['session_username'] = $members['members_email'];
								$_SESSION['guest'] 											= 1;
								$_SESSION['session_usertype']		= $members['members_type'];
								$_SESSION['session_userlevel']		= $members['members_level'];
								$_SESSION['session_gid'] 						= $members['members_group_id'];
								$_SESSION['session_time'] 					= $logintime;
								$_SESSION['ipno'] 												= $_ipno;
							}
							else {
								$this->error_message	= WRONG_LOGIN_INFORMATION."-03";
								return $this->setHTML( "login", $this->basicURL, $this->error_message );
							}
							session_write_close(  );

							$update_query	= "UPDATE members "
													. "\n SET members_date_of_last_login = now(  ),"
													. "\n members_visited_count = members_visited_count + 1"
													. "\n WHERE members_email_confirmed = '1'"
													. "\n AND members_status = '1' "
													. "\n AND id = '".( int )$_SESSION['session_user_id'] ."'"
													. $extra_section;

							if ( $this->dbConnect->getQuery( $update_query ) ){
								unset( $gvalue );
								_getRedirect( $returnURL );
							}
							else {
								$this->error_message	= WRONG_LOGIN_INFORMATION2." -04";
								$LoginValue = $this->error_message;
							}
					}
					else {
							$this->error_message	= WRONG_LOGIN_INFORMATION." -05";
							$LoginValue = $this->setHTML( "login", $this->basicURL, $this->error_message );
					}
				}
				else {
					$this->error_message	= WRONG_LOGIN_INFORMATION." -06";
					$LoginValue = $this->setHTML( "login", $this->basicURL, $this->error_message );
				}
			}
		}
		else {
			$this->error_message	= WHAT_R_U_DOING." -07";
			$LoginValue = $this->setHTML( "login", $this->basicURL, $this->error_message );
		}

		return $LoginValue;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]:: To logout with delete all of sessions
 * If it's desired to kill the session, also delete the session cookie.
 * Note: This will destroy the session, and not just the session data!
 *  ----------------------------------------------------------------------- */
	public function setLogout ( ){
		if ( _getCheckNullorNot( $_SESSION['session_username'] ) && $_SESSION['guest'] == 1 ){
				if ( isset( $_COOKIE[session_name(  )] ) ){
				   setcookie( session_name(  ), '', time(  )-42000, '/' );
				}

				$delete_query	= "DELETE FROM members_session"
										. "\n WHERE session_id = '".$_SESSION['session_id'] ."'"
										. "\n AND guest = '1'"
										. "\n AND username = '". $_SESSION['session_username']  ."'";
																	
				$LogOutText  = "\n<h1><a href=\"#\" title=\"".CONFIG_SITE_NAME." :: Logout\" target=\"_self\">LogOut</a></h1>\n";				
				$LogOutText .= "\n<div class=\"pages_txt\"><br/><br/><br/>";
					//Unset all of the session variables.
				$_SESSION = array();				
				if ( $this->dbConnect->getQuery( $delete_query ) ){
					session_destroy();
					$LogOutText .= LOGOUT_SUCCESSFULLY;
				}
				else {
					session_destroy();				
					$LogOutText .= LOGOUT_SUCCESSFULLY;
				}
				$LogOutText .= "\n</div>";
				unset($_SESSION);				
		}
		else {
			_getRedirect( $this->basicURL );
		}

		return $LogOutText;
	}
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!