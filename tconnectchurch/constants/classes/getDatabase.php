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

class getDatabase {

	var $_DB_CONNECTION;
	var $_DB_HOST;
	var $_DB_USERNAME;
	var $_DB_PASSWORD;
	var $_DB_DATABASE_NAME;

	public function getDatabase( $host = '', $user = '', $password = '', $dbName = '' ){

		//Assign the host name if passed in
		if ( strlen( trim( $host ) ) > 0 ){
			$this->setHost( $host );
		}

		//Assign the user name if passed in
		if ( strlen( trim( $user ) ) > 0 ){
			$this->setUsername( $user );
		}

		//Assign the password if passed in
		if ( strlen( trim( $password ) ) > 0 ){
			$this->setPassword( $password );
		}

		//Assign the database name if passed in
		if ( strlen( trim( $dbName ) ) > 0 ){
			$this->setDatabaseName( $dbName );
		}

	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: Basic function
 *  ----------------------------------------------------------------------- */
	public function setHost( $host ){
		$this->_DB_HOST = $host;
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: Basic function
 *  ----------------------------------------------------------------------- */
	public function setUsername( $user ){
		$this->_DB_USERNAME = $user;
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: Basic function
 *  ----------------------------------------------------------------------- */
	public function setPassword( $password ){
		$this->_DB_PASSWORD = $password;
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: Basic function
 *  ----------------------------------------------------------------------- */
	public function setDatabaseName( $name ){
		$this->_DB_DATABASE_NAME = $name;
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: Basic function
 *  ----------------------------------------------------------------------- */
	public function getOpen(){
		//Check if the connection is not already set
		if ( isset( $this->_DB_CONNECTION ) ){
			return;
		}

		//Make sure that the host, the username, the password, and the database name are set
		if ( 	( !isset( $this->_DB_HOST ) ) || ( strlen( $this->_DB_HOST ) == 0 )
			|| ( !isset( $this->_DB_USERNAME ) ) || ( strlen( $this->_DB_USERNAME ) == 0 )
			|| ( !isset( $this->_DB_PASSWORD ) ) || ( strlen( $this->_DB_PASSWORD ) == 0 )
			|| ( !isset( $this->_DB_DATABASE_NAME ) ) || ( strlen( $this->_DB_DATABASE_NAME ) == 0 )
		  ){
			echo '<h1>DATABASE VARIABLES HAVE NOT BEEN SET</h1>';
			exit();
		}

		//Connect to the database using the variables already defined
		$this->_DB_CONNECTION = @mysql_connect( $this->_DB_HOST, $this->_DB_USERNAME, $this->_DB_PASSWORD ) or getDatabase::getDBError( "Could not connect to mysql server.", mysql_errno(), mysql_error() );

		//Select the database to use
		@mysql_select_db( $this->_DB_DATABASE_NAME ) or getDatabase::getDBError( "Could not use the database", mysql_errno(), mysql_error() );
		mysql_query( 'SET CHARACTER SET utf8' );
		mysql_query( 'SET SESSION collation_connection = "utf8_general_ci"' );
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: Basic function
 *  ------------------------------------------------------------------------- */
	public function getClose(){
		if ( isset( $this->_DB_CONNECTION ) ){
			mysql_close( $this->_DB_CONNECTION );
		}
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: Basic function
 *  ------------------------------------------------------------------------- */
	public function getCreation( $tdName ){
		$query = "	";
		$this->getQuery( $query );
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: Basic function
 *  ------------------------------------------------------------------------- */
	public function getQuery( $db_query ){
		$result = @mysql_query( $db_query ) or getDatabase::getDBError( $db_query, mysql_errno(), mysql_error() );
		if ( $result ){
			return $result;
		}
		else {
			echo "Sorry! ".$db_query;
			exit;
		}
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: Basic function
 *  ------------------------------------------------------------------------- */
	public function getNumberRows( $db_query ){
		return @mysql_num_rows( $db_query );
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: Basic function
 *  ------------------------------------------------------------------------- */
	public function getFetch_Array( $db_query ){
		return @mysql_fetch_array( $db_query, MYSQL_ASSOC );
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 *  ------------------------------------------------------------------------- */
	public function getFetchArray_Result( $db_query ){
		$result			= $this->getQuery( $db_query );
		$fetchArray	= @mysql_fetch_array( $result, MYSQL_ASSOC );

		return $fetchArray;
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: Basic function
 *  ------------------------------------------------------------------------- */
	public function getObject( &$db_query ){
		$result			=  $this->getQuery( $db_query );
		$objectResult	= @mysql_fetch_object( $result );

		return $objectResult ;
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 *  ------------------------------------------------------------------------- */
	public function getContentsInArray( $query ){
		$count_query		= $this->getQuery( $query );
		$count_values	= $this->getFetch_Array( $count_query );

		return $count_values;
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 *  ------------------------------------------------------------------------- */
	public function getTotalNumber( $tbname, $where = '' ){
		$where_tail		= ( !empty( $where ) ) ? " WHERE ".$where : "";
		$query				= "SELECT count( * ) AS total FROM ". $tbname . $where_tail;
		$count_values	= $this->getContentsInArray( $query );

		return $count_values['total'];
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 *  ------------------------------------------------------------------------- */
	public function getAllContents( $code, $where = '' ){
		$where_tail	= ( !empty( $where ) ) ? " WHERE ".$where : "";
		$query			= "SELECT * FROM ". $code . $where_tail;

		return $this->getContentsInArray( $query );
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 *  ------------------------------------------------------------------------- */
	public function getPageContent( $id, $where='' ){
		$where_tail	= ( !empty( $where ) ) ? " AND ".$where : "";
		$db_query		= "SELECT * FROM pages WHERE id = '". $id ."'". $where_tail ."";

		return $this->getContentsInArray( $db_query );
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 *  ------------------------------------------------------------------------- */
	public function getFrontPage( $where='' ){
		$where_tail 	= ( !empty( $where ) ) ? " AND ".$where : "";
		$db_query		= "SELECT * FROM pages WHERE  frontpage = '1' ". $where_tail ."";

		return $this->getContentsInArray( $db_query );
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 *  ------------------------------------------------------------------------- */
	public function getModules( $id ){
		$db_query = "SELECT * FROM modules WHERE id = '". $id ."'";

		return $this->getContentsInArray( $db_query );
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 *  ------------------------------------------------------------------------- */
	public function getBoardContent( $idarray, $where='' ){
		$where_tail	= ( !empty( $where ) ) ? " AND ".$where : "";
		$db_query		= "SELECT * FROM {$idarray['tbname']} b, opensef  o LEFT JOIN menu m ON o.pid = m.pid  WHERE b.id = o.tid AND o.tbname = '{$idarray['tbname']}' AND o.publish = '1' AND b.id = '". $idarray['tid'] ."'". $where_tail ."";

		return $this->getContentsInArray( $db_query );
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 *  ------------------------------------------------------------------------- */
	public function getMemberInfo( $id, $where='' ){
		$where_tail	= ( !empty( $where ) ) ? " AND ".$where : "";
		$db_query		= "SELECT * FROM members WHERE members_email_confirmed = '1' AND members_status = '1' AND id = '". $id ."'". $where_tail ."";

		return $this->getContentsInArray( $db_query );
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 *  ------------------------------------------------------------------------- */
	public function getMember( $username, $password, $extra_section = "" ){
		$query				= "SELECT count( * ) AS total FROM members WHERE members_email_confirmed = '1' AND members_status = '1' AND ( members_type = '1' OR members_type = '2' ) AND ( members_level > '0' OR members_level < '4' )". $extra_section;
		$count_values	= $this->getContentsInArray( $query );

		if ( $count_values['total'] == 1 ){
			$db_query			= "SELECT * FROM members WHERE members_email_confirmed = '1' AND members_status = '1'". $extra_section;
			$check_values	= $this->getContentsInArray( $db_query );

			if ( !_getValidatePassword( $password, $check_values['members_password'] ) ){
				return false;
			}
			else {
				return true;
			}
		}
		elseif ( intval( $count_values['total'] ) > 1 ){
			return false;
		}
		else {
			$num = $this -> getTotalNumber( "members", "members_email_confirmed = '1' AND members_status = '1' AND ( members_type = '1' OR members_type = '2' ) AND ( members_level > '0' OR members_level < '4' )". $extra_section );
			if ( $num == 1 ){
				$db_query			= "SELECT * FROM members WHERE members_email_confirmed = '1' AND members_status = '1' ". $extra_section;
				$check_values	= $this->getContentsInArray( $db_query );
				if ( !_getValidatePassword( $password, $check_values['members_password'] ) ){
					return false;
				}
				else {
					return true;
				}
			}
			else {
				return false;
			}

			return false;
		}
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 *  ----------------------------------------------------------------------- */
	public function getSectionsList( $where ){
		$sections_array	= array();
		$sections_query	= $this->getQuery( "SELECT * FROM sections WHERE ". $where ."" );

		while ( $sections_values = $this->getFetch_Array( $sections_query ) ){
			$sections_array[] = array( 'id' => $sections_values['id'], 'text' => $sections_values['title'] );
		}

		return $sections_array;
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 *  ----------------------------------------------------------------------- */
	public function getCatListSct( $where ){
		$sections_query = $this->getQuery( "SELECT * FROM sections WHERE ". $where ."" );

		while ( $sections_values = $this->getFetch_Array( $sections_query ) ){
			$categories_query = $this->getQuery( "SELECT * FROM categories WHERE parent = '0' AND section = '". $sections_values['id'] ."'" );

			while ( $categories_values = $this->getFetch_Array( $categories_query ) ){
				$categories_array[]		= array( 'id' => $categories_values['id'], 'text' => $categories_values['title'] );
				$child_categories_query	= $this->getQuery( "SELECT id, title, name FROM categories WHERE parent = '". $categories_values['id'] ."' AND section = '". $sections_values['id'] ."'" );

				while ( $child_categories_values = $this->getFetch_Array( $child_categories_query ) ){
					$categories_array[] = array( 'id' => $child_categories_values['id'], 'text' => '&nbsp;|-'.$child_categories_values['title'] );
				}
			}
		}

		return $categories_array;
	}

/** -------------------------------------------------------------------------
 * [05/28/2014]:: To get categories's list in array
 *  ----------------------------------------------------------------------- */
	public function getCategoriesList( $where ){
		$categories_array	= array();
		$categories_query	= $this->getQuery( "SELECT * FROM categories WHERE parent = '0' and  ". $where ."" );

		while ( $categories_values	= $this->getFetch_Array( $categories_query ) ){
			$categories_array[]		= array( 'id' => $categories_values['id'], 'text' => $categories_values['title'] );
			$child_categories_query	= $this->getQuery( "SELECT id, title, name FROM categories WHERE parent = '". $categories_values['id'] ."' and ". $where ."" );

			while ( $child_categories_values = $this->getFetch_Array( $child_categories_query ) ){
				$categories_array[] 	= array( 'id' => $child_categories_values['id'], 'text' => '&nbsp;|-'.$child_categories_values['title'] );
			}
		}

		return $categories_array;
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 *  ----------------------------------------------------------------------- */
	public function getCategoriesName( $cID, $type = 'text' ){
		global $base_url;

		$db_query				= "SELECT * FROM categories WHERE status = '1' AND publish = '1' AND id = '{$cID}'";
		$categories_values	= $this->getContentsInArray( $db_query );

		if ( $type === 'html' ){
			if ( !empty( $categories_values['categories_name_image'] ) ){
				$CategoriesName 	= '<img src="'.$base_url.'/templates/'.CONFIG_TEMPLATE.'/images/'.$categories_values['image'].'" alt="'.$categories_values['title'].'" border="0">';
			}
			else {
				$CategoriesName	= "<h1>{$categories_values['title']}</h1>";
			}
			$CategoriesName		= "<div id='page_heading'>{$CategoriesName}</div>";
		}
		else {
			$CategoriesName		= $categories_values['title'];
		}

		return $CategoriesName;
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 *  ----------------------------------------------------------------------- */
	public function getSectionsName( $sID,  $type = 'text' ){
		$db_query = "SELECT * FROM sections WHERE status = '1' AND id = '".$sID."'";
		$section_values = $this->getContentsInArray( $db_query );

		$SectionsName = ( $type === 'html' ) ? "<div id='page_heading'><h1>{$section_values['title']}</h1></div>" : $section_values['title'];

		return $SectionsName;
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 *  ------------------------------------------------------------------------ */
	public function getMembersList( $where = '' ){
		$members_array	= array();
		$members_query	= $this->getQuery( "SELECT * FROM members_type WHERE ". $where ."" );

		while ( $members_values = $this->getFetch_Array( $members_query ) ){
			$members_array[] = array( 'id' => $members_values['members_type_id'], 'text' => $members_values['members_type_name'] );
		}

		return $sections_array;
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 *  ----------------------------------------------------------------------- */
	public function getAccessList( $where = '' ){
		$access_array	= array();
		$access_query 	= $this->getQuery( "SELECT * FROM access_type WHERE ". $where ."" );

		while ( $access_values = $this->getFetch_Array( $access_query ) ){
			$access_array[] = array( 'id' => $access_values['access_type_id'], 'text' => $access_values['access_type_name'] );
		}

		return $access_array;
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 *  ----------------------------------------------------------------------- */
	public function getMenuList( $where ){
		$menu_array	= array();
		$menu_query = $this->getQuery( "SELECT * FROM menu WHERE parent = '0' AND ". $where );

		while ( $menu_values = $this->getFetch_Array( $menu_query ) ){
			$menu_array[] = array( 'id' => $menu_values['id'], 'text' => $menu_values['mtitle'] );
			$child_menu_query = $this->getQuery( "SELECT * FROM menu WHERE parent = '". $menu_values['id'] ."' and ". $where );

			while ( $child_menu_values = $this->getFetch_Array( $child_menu_query ) ){
				$menu_array[] = array( 'id' => $child_menu_values['id'], 'text' => '&nbsp;&#187;&nbsp;'.$child_menu_values['mtitle'] );
				$subchild_menu_query = $this->getQuery( "SELECT * FROM menu WHERE parent = '". $child_menu_values['id'] ."' and ". $where ."" );

				while ( $subchild_menu_values = $this->getFetch_Array( $subchild_menu_query ) ){
					$menu_array[] = array( 'id' => $subchild_menu_values['id'], 'text' => '&nbsp;&#187;&#187;&nbsp;'.$subchild_menu_values['mtitle'] );
				}
			}
		}

		return $menu_array;
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 *  ----------------------------------------------------------------------- */
	public function getTableName( $cID ){
		$db_query					= "SELECT * FROM categories WHERE status = '1' AND categories_id = '".$cID."'";
		$categories_values 	= $this->getContentsInArray( $db_query );

		return $categories_values['categories_name'];
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: To
 *  ------------------------------------------------------------------------- */
	public function getDBAction( $table, $data, $action = 'insert', $parameters = '' ){
		reset( $data );

		if ( $action == 'insert' ){
			$query = 'INSERT INTO ' . $table . ' ( ';

			while ( list( $columns, ) = each( $data ) ){
				$query .= $columns . ', ';
			}
			$query = substr( $query, 0, -2 ) . ' ) values ( ';
			reset( $data );

			while ( list( , $value ) = each( $data ) ){
				switch ( ( string )$value ){
					case 'now()':
						$query .= 'now(), ';
					break;
					case 'null':
						$query .= 'null, ';
					break;
					default:
						$query .= '\'' . $this->getInput( $value ) . '\', ';
					break;
				}
			}

			$query = substr( $query, 0, -2 ) . ' )';
		}
		elseif ( $action == 'update' ){
			$query = 'UPDATE ' . $table . ' SET ';

			while ( list( $columns, $value ) = each( $data ) ){
				switch ( ( string )$value ){
					case 'now()':
						$query	.= $columns . ' = now(), ';
					break;
					case 'null':
						$query	.= $columns .= ' = null, ';
					break;
					default:
						$query	.= $columns . ' = \'' . $this->getInput( $value ) . '\', ';
					break;
				}
			}
			$query = substr( $query, 0, -2 ) . ' WHERE ' . $parameters;
		} elseif ( $action == 'select' ){
			$query = 'SELECT ';

			foreach ( $data as $key => $kw ){
				$query .= $kw . ', ';
			}
			$query = substr( $query, 0, -2 ) . ' FROM ' . $table;

			if ( !empty( $parameters ) ){
				$query = substr( $query, 0 ) . ' WHERE ' . $parameters;
			}
		}

		return $this->getQuery( $query );
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: Basic Function
 *  ------------------------------------------------------------------------- */
	public function getInsertID(){
		return mysql_insert_id();
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: Basic Function
 *  ------------------------------------------------------------------------- */
	public function getInput( $string ){
		if ( function_exists( 'mysql_real_escape_string' ) ){
			return mysql_real_escape_string( $string );

		}
		elseif ( function_exists( 'mysql_escape_string' ) ){
			return mysql_escape_string( $string );
		}

		return addslashes( $string );
	}

/** -------------------------------------------------------------------------
 * [00/00/2012]:: Basic Function
 *  ------------------------------------------------------------------------- */
	public function getDBError( $query, $errno, $error ){
		die( '<div style="color:#000000;font-size:10px;font-family:Tahoma;"><b>' . $errno . ' - ' . $error . '<br /><br />' . $query . '<br /><br /><small><div style="color:#000000;font-size:10px;font-family:Tahoma;">[TEP STOP 0]</div></small><br /><br /></b></div>' );
	}
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!