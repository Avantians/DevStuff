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

class getAllElements{

/** -------------------------------------------------------------------------
 * [00/00/2011]::To display greeting based on local time from browser
 *  ----------------------------------------------------------------------- */
	public static function setGreeding(){
		if ( date( "H" ) <= 11 ){
			$greeting = "<strong>Good Morning!</strong>\n";
		}
		else {
			if ( date( "H" ) > 11 and date( "H" ) < 18 ){
				$greeting = "<strong>Good Afternoon!</strong>\n";
			}
			else {
				$greeting = "<strong>Good Evening!</strong>\n";
			}
		}

		return $greeting;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]:: To display Error message
 *  --------------------------------------------------------------------- */
	public static function setMessage( $error_message ){
		$message_size	= sizeof( $error_message );

		$msg	  = "<div class='warning'>\n";
		for( $i=0; $i < $message_size; $i++ ){
			$msg .= $error_message[$i]."<br />\n";
		}
		$msg 	 .= "</div>\n";

		return $msg;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To get files from the folder
 *  ----------------------------------------------------------------------- */
	public static function setRandomImage(){
		$NUM_PIC 			= 9; // add more when there's more pictures
		$ALLOWED_PIC 	= 5;
		$PIC_PREFIX		= "/images/random/";
		$PIC_SUFFIX 		= ".gif";
		$PIC_WIDTH 		= "108";
		$PIC_HEIGHT 		= "72";
		unset( $pic_array );

		for( $i = 1; $i <= $NUM_PIC; $i++ ){
			$pic_array[$i] = $PIC_PREFIX . $i . $PIC_SUFFIX;
		}

		while( sizeOf( $pic_array ) > $NUM_PIC-$ALLOWED_PIC ){
			$rand_num = rand( 1, $NUM_PIC );
			if ( array_key_exists( $rand_num, $pic_array ) ){
				echo "<img src = '" . $pic_array[$rand_num] . "' border = 0 width = " . $PIC_WIDTH . " height = " . $PIC_HEIGHT . ">";
				unset( $pic_array[$rand_num] );
			}
		}
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To display counter
 *  ------------------------------------------------------------------------- */
	public static function setCounter( $titleEnable="disable" ){
		global $Bon_db;

		$display_title 						= "";
		$time 								= date( 'Y-m-d', time() );
		$yesterday 						= date( 'Y-m-d', time()-60*60*24 );

		$total_counter_values 		= $Bon_db->getContentsInArray( "SELECT count FROM kw_count WHERE count_id = 1" );
		$total_counter 					= $total_counter_values['count'];

		$yesterday_counter_values	= $Bon_db->getContentsInArray( "SELECT count FROM kw_count WHERE count_date = '{$yesterday}'" );
		$yesterday_counter			= $yesterday_counter_values['count'] ? $yesterday_counter_values['count'] : '0';

		$today_counter_values		= $Bon_db->getContentsInArray( "SELECT count FROM kw_count WHERE count_date = '{$time}'" );
		$today_counter 					=  $today_counter_values['count'];

		$display_count 					= _getIP()."<br/>\n"."<strong>A</strong>".$total_counter.".<strong>Y</strong>".$yesterday_counter.".<strong>T</strong>".$today_counter."\n";

		if ( $titleEnable == "enable" ){ $display_title = "\n<div class='side_head_title'>Counter</div>\n"; }

		return $display_title."\n<div class='side_counter'>".$display_count."</div>\n";
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]:: PullDown Menu - Select
 *  ------------------------------------------------------------------------- */
	public static function setPullDownMenu( $name, $values, $default = '', $parameters = '', $disabled = "" , $defaultset = false, $required = false ){
		$field = "<select name=\"" . _getOutputString( $name ) . '"';

		if ( _getCheckNullorNot( $parameters ) ) $field .= ' ' . $parameters;

		$field .= $disabled.">\n";
		$field .= $defaultset ? "<option value=\"\">".TEXT_PLEASE_SELECT_ONE."</option>\n" : "";
		if ( !_getCheckNullorNot( $default ) && isset( $GLOBALS[$name] ) ) $default = stripslashes( $GLOBALS[$name] );

		for ( $i=0, $n=sizeof( $values ); $i<$n; $i++ ){
			$field .= "<option value=\"" . _getOutputString( $values[$i]['id'] ) . "\"";
			if ( $default == $values[$i]['id'] ){
				$field .= " SELECTED";
			}

			$field .= ">" . _getOutputString( $values[$i]['text'], array( '"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;' ) ) . "</option>\n";
		}
		$field .= "</select>\n";
		$field .= $required ? TEXT_FIELD_REQUIRED : "";

		return $field;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]:: PullDown Menu with onchange - Select
 *  ------------------------------------------------------------------------- */
	public static function setPullDownMenuOnChange( $name, $values, $default = '', $parameters = '', $required = false ){
		$field = '<select name="' . _getOutputString( $name ) . '"';

		if ( _getCheckNullorNot( $parameters ) ){ $field .= ' ' . $parameters; }

		$field .= ' onchange="this.form.submit();">'."\n";
		$field .= '<option value="">'.CONFIG_PLEASE_SELECT_ONE."</option>\n";
		if ( !_getCheckNullorNot( $default ) && isset( $GLOBALS[$name] ) ) $default = stripslashes( $GLOBALS[$name] );

		for ( $i=0, $n=sizeof( $values ); $i<$n; $i++ ){
			$field .= '<option value="' . _getOutputString( $values[$i]['id'] ) . '"';
			if ( $default == $values[$i]['id'] ){ $field .= ' SELECTED'; }

			$field .= '>' . _getOutputString( $values[$i]['text'], array( '"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;' ) ) . "</option>\n";
		}
		$field .= "</select>\n";
		$field .= $required ? TEXT_FIELD_REQUIRED : "";

		return $field;
	}

/** -------------------------------------------------------------------------
 * [01/31/2011]:: Input field
 *  ------------------------------------------------------------------------- */
	public static function setInputField( $type = "text", $name, $values, $parameters = "", $required = false ){

		$field  = '<input type="'.$type.'" name="'._getOutputString( $name ).'" value="'._getOutputString( $values ).'"';
		$field .=  _getCheckNullorNot( $parameters )? ' ' . $parameters : "";
		$field .= "/>\n";
		$field .= $required ? TEXT_FIELD_REQUIRED : "";

		return $field;
	}

/** -------------------------------------------------------------------------
 * [02/13/2014]:: Input field
 *  ------------------------------------------------------------------------- */
	public static function setAccesslevel( $type = "gid" ){
		if ( $type === "gid" ){
			$accesslevel = ( isset( $_SESSION['session_gid'] ) && _getCheckNullorNot( $_SESSION['session_gid'] ) ) ? $_SESSION['session_gid'] : 1;
		} 
		elseif ( $type === "ulevel" ){
			$accesslevel = ( isset( $_SESSION['session_userlevel'] ) && _getCheckNullorNot( $_SESSION['session_userlevel'] ) ) ? $_SESSION['session_userlevel'] : 7;
		}

		return $accesslevel;
	}
	

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 *  ------------------------------------------------------------------------- */
	public static function setJustci_path( $temp_ci_path ){
		$basic_action	= array( "post","edit","delete","reply","register","activation","login","logout" );

		$content_path			= preg_replace( "/\/$/", "", $temp_ci_path );
		$content_path			= preg_replace( "/\&$/", "", $content_path );
		$pageInfo_array		= explode( "/", $content_path );
		if ( in_array( end( $pageInfo_array ), $basic_action ) ){
			$do 				= end( $pageInfo_array );
			$content_path 		= rtrim( str_replace( $do, "", $content_path ), "/" );
		}

		return $content_path;
	}	
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!