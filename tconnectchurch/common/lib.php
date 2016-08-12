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

global $base_url;
/** -------------------------------------------------------------------------
 * [00/00/2011]:: Text Setting
 *  ----------------------------------------------------------------------- */
define( "META_DIVIDER", " || " );
define( "PLEASE_WRITE_ARTICLE_PROPERLY", "You did not approch to this system correctly." );
define( "PLEASE_WRITE_ARTICLE_PROPERLY2", "You did not send information properly." );
define( "WRONG_LOGIN_INFORMATION", "<strong>Please re-enter your username or password</strong><br/>username or password you entered is incorrect. Please try again (make sure your caps lock is off)." );
define( "WRONG_LOGIN_INFORMATION2", "Sorry we could not update your login information." );
define( "WRONG_EMAIL_INFORMATION", "<strong>Please re-enter your email address</strong><br/>We do not have the email address." );

define( "WHAT_R_U_DOING", "What are you trying to do?" );
define( "NO_MATCHED_EMAIL", "<p><strong>Sorry, that email address is not registered with us.</strong></p>" );

define( "NOT_FIND_PAGES", "<h2 class=\"no_page_heading\">File or Page Not found!</h2><strong style=\"font-size:1.3em;line-height:1.2em;\">404 Error - We're sorry the page you requested could not be found.</strong><br/><br/>
It's possible that the page has been moved, you typed the address incorrectly, or that the page no longer exists.<br/><br/><br/><strong>Now you have some options.</strong><br/><a href=\"".$base_url."\" title=\"Homepage\" target=\"_self\">&nbsp;&nbsp;&nbsp;&clubs; Visit the home page to view our main page.</a><br/><a href=\"".$base_url."/about-kenwoo\" title=\"About ".CONFIG_SITE."\" target=\"_self\">&nbsp;&nbsp;&nbsp;&clubs; Visit \"About ".CONFIG_SITE."\" page to view. </a><br/><br/>If difficulties persist, please contact the system administrator of this site - "  );

define( "LOGIN_ALREADY", "<h2 class=\"page_heading\">Log In</h2>You already logged in.");
define( "LOGOUT_SUCCESSFULLY", "<strong><h2>You have successfully signed out.</h2></strong>");
define( "LOGOUT_UNSUCCESSFULLY", "<p class=\"page_heading\">Signed out</p>You cannot sign out successfully.</strong>");
define( "DONOT_HAVE_LOGIN_INFO", "<p class=\"page_heading\">Signed out</p>You donot have login session.</strong>");

define( "REGISTRATION_EXISTS_EMAIL", "<p class=\"page_heading\">Registration</p>Your email address already exists.<br/>If you forgot your password, you can easily recover it by <a href=\"#\" title=\"recover your password\" target=\"_self\">clicking here</a>.<br/>or Please <a href=\"javascript: history.go(-1)\" title=\"Go Back to registration page\" target=\"_self\">go back</a> to registration page to try again. <br/><br/><a href=\"javascript: history.go(-1)\" title=\"Go Back to registration page\" target=\"_self\">previous</a>");

define( "REGISTRATION_ACTIVATION_TXT", "<p class=\"page_heading\">Registration</p>Thank you for registration! A confirmation email has been sent to your email address. Please click on the Activation Link to Activate your account.");

define( "REGISTRATION_ACTIVATION_EMAIL_TMP", "<p style=\"font:0.7em verdana;\">Thank you for registration! <br/><br/>To activate your account, pleas click on this link: <br/>".$base_url."/?op=%s&em=%s/activation<br/><br/><strong>Web Team</strong></p>");

define( "REGISTRATION_ACTIVATION_EMAIL_BACKEND_TMP", "<p style=\"font:0.7em verdana;\">Thank you for registration! <br/><br/>To activate your account, pleas click on this link: <br/>".rtrim(str_replace("/backend", "", $base_url), "/")."/?op=%s&em=%s/activation<br/><br/><strong>Web Team</strong></p>");

define( "REGISTRATION_ACTIVATION_SUCCESS", "<p class=\"page_heading\">Registration</p>Your account has been successfully activated now!<br/>You can now log in with the user id and password you provided when you signed up.<br/><br/><a href=\"/login\" title=\"Go to Login\" target=\"_self\">Login</a>");

define( "REGISTRATION_ACTIVATION_FAILURE_A", "<p class=\"page_heading\">Registration</p>Your account has been activated or we could not find your account any more.<br/>Please contact our support team for quick assistance at ".CONFIG_SITE_EMAIL);
define( "REGISTRATION_ACTIVATION_FAILURE_B", "<p class=\"page_heading\">Registration</p>Your account could not be activated at this time.<br/>Please contact our support team for quick assistance at ".CONFIG_SITE_EMAIL);
define( "REGISTRATION_ACTIVATION_FAILURE_C", "<p class=\"page_heading\">Registration</p>Your account could not be activated at this time due to database issue.<br/>Please contact our support team for quick assistance at ".CONFIG_SITE_EMAIL);

define( "TEXT_FIELD_REQUIRED", "*");
define( "TEXT_PLEASE_SELECT_ONE", "-Please select-");
define( "NO_ARTICLE_YET", "There is no article yet.<br/> No data!");
define( "NO_RELATED_ARTICLE", "There is no related article.");
define( "CONFIG_PLEASE_SELECT_ONE", "-- Select one --");
define( "CONFIG_NOTICE_REGISTER_DOWNLOAD", "Need to register to download");
define( "CONFIG_NOTICE_EXIST_NO_FILE", "Specified file does not exist.");

define( "NOT_ALLOWED_FILE_FORMAT", "This file format is not allowd to upload." );
define( "DIRECTORY_NOT_EXIST_OR_IMPROPER_FOLDER", "Please check the folder to upload the file.");
define( "TEXT_DELET", "Delete");
define( "TEXT_EDIT", "Modify");
define( "TEXT_LIST", "List");
define( "TEXT_WRITE", "POST");
define( "TEXT_AUTHOR", "설교자 ");
define( "TEXT_MP3", "MP3");
define( "TEXT_DELETE", "Delete ");
define( "TEXT_GENERATOR", "Bon CMS");
define( "TEXT_OWNER", "Bon CMS");
define( "MAX_CHART", "250");
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!