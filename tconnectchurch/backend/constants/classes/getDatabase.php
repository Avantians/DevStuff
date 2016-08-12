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

class getDatabase {

	var $_DB_CONNECTION;
	var $_DB_HOST;
	var $_DB_USERNAME;
	var $_DB_PASSWORD;
	var $_DB_DATABASE_NAME;

	function getDatabase( $host = '', $user = '', $password = '', $dbName = '' ){

		// Assign the host name if passed in
		if ( strlen( trim( $host ) ) > 0 ){
			$this->setHost( $host );
		}

		// Assign the user name if passed in
		if ( strlen( trim( $user ) ) > 0 ){
			$this->setUsername( $user );
		}

		// Assign the password if passed in
		if ( strlen( trim( $password ) ) > 0 ){
			$this->setPassword( $password );
		}

		// Assign the database name if passed in
		if ( strlen( trim( $dbName ) ) > 0 ){
			$this->setDatabaseName( $dbName );
		}

	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function setHost( $host ){
		$this->_DB_HOST = $host;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function setUsername( $user ){
		$this->_DB_USERNAME = $user;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function setPassword( $password ){
		$this->_DB_PASSWORD = $password;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function setDatabaseName( $name ){
		$this->_DB_DATABASE_NAME = $name;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getOpen(){

		//Check if the connection is not already set
		if( isset( $this->_DB_CONNECTION ) ){
			return;
		}

		//Make sure that the host, the username, the password, and the database name are set
		if( ( !isset( $this->_DB_HOST ) ) || ( strlen( $this->_DB_HOST ) == 0 )
			|| ( !isset( $this->_DB_USERNAME ) ) || ( strlen( $this->_DB_USERNAME ) == 0 )
			|| ( !isset( $this->_DB_PASSWORD ) ) || ( strlen( $this->_DB_PASSWORD ) == 0 )
			|| ( !isset( $this->_DB_DATABASE_NAME ) ) || ( strlen( $this->_DB_DATABASE_NAME ) == 0 )
		  ){
			echo '<h1>DATABASE VARIABLES HAVE NOT BEEN SET</h1>';
			exit();
		}

		//Connect to the database using the variables already defined
		$this->_DB_CONNECTION = @mysql_connect( $this->_DB_HOST, $this->_DB_USERNAME, $this->_DB_PASSWORD ) or Bon_Database::getDBError( "Could not connect to mysql server.", mysql_errno(), mysql_error() );

		//Select the database to use
		mysql_select_db( $this->_DB_DATABASE_NAME ) or getDatabase::getDBError( "Could not use the database", mysql_errno(), mysql_error() );
		mysql_query( 'SET CHARACTER SET utf8' );
		mysql_query( 'SET SESSION collation_connection = "utf8_general_ci"' );
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getClose(){
		if( isset( $this->_DB_CONNECTION ) ){
			mysql_close( $this->_DB_CONNECTION );
		}
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getCreation( $tdName ){
		$query = "	";
		$this->getQuery( $query );
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getQuery( $db_query ){
		$result = @mysql_query( $db_query ) or getDatabase::getDBError( $db_query, mysql_errno(), mysql_error() );
		if( $result ){
			return $result;
		}
		else{
			echo "Sorry! ".$db_query;
			exit;
		}
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Get all of table name from database
 *  ------------------------------------------------------------------------- */
	function getTables( $noAllowed ){
		$result 	=  $this->getQuery( "SHOW TABLES;" );
		while( $list_values = @mysql_fetch_row( $result ) ){
			if( !in_array( $list_values[0], $noAllowed ) ){
				$tables[] = array( "id"=>$list_values[0],"text"=>$list_values[0] ) ;
			}
		}

		return $tables;
	}

/** -------------------------------------------------------------------------
 * [06/16/2014]::Optimize all of table
 *  ------------------------------------------------------------------------- */
	function getOptimize( $noAllowed = "" ){
		$result 	=  $this->getQuery( "SHOW TABLES;" );
		while( $list_values = @mysql_fetch_row( $result ) ){
			$this->getQuery( "OPTIMIZE TABLE `{$list_values[0] }`;" );
		}
	}

/** -------------------------------------------------------------------------
 * [06/16/2014]::Optimize all of table
 *  ------------------------------------------------------------------------- */
	function getRepair( $noAllowed = "" ){
		$result 	=  $this->getQuery( "SHOW TABLES;" );
		while( $list_values = @mysql_fetch_row( $result ) ){
			$this->getQuery( "REPAIR TABLE `{$list_values[0] }`;" );
		}
	}

/** -------------------------------------------------------------------------
 * [06/16/2014]::Get database backup by commends
 *  ------------------------------------------------------------------------- */
	function getBackupdb( $noAllowed = "" ){

		$destination_folder =  $_SERVER['DOCUMENT_ROOT'] . UNDER_SUBFOLDER . "/" . '_backup';
		if( !file_exists($destination_folder) && !is_dir($destination_folder) ){
			mkdir( $destination_folder, 0755 );
		}
		else {
			if( !is_writeable($destination_folder) ){
				chmod( $destination_folder, 0755 );
			}
		}

		$filename = $this->_DB_DATABASE_NAME ."_".date("mdY_Gi").".sql.gz";
		$backupcommand = "mysqldump --skip-extended-insert -u " . $this->_DB_USERNAME . " --password=" . $this->_DB_PASSWORD . " ". $this->_DB_DATABASE_NAME ." | gzip -9 > " . $destination_folder . "/" . $filename;
		exec($backupcommand);
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getNumberRows( $db_query ){
		return @mysql_num_rows( $db_query );
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getFetch_Array( $db_query ){
		return @mysql_fetch_array( $db_query, MYSQL_ASSOC );
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getFetchArray_Result( $db_query ){
		$result 			=  $this->getQuery( $db_query );
		$fetchArray	= @mysql_fetch_array( $result, MYSQL_ASSOC );

		return $fetchArray;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getObject( $db_query ){
		$result 				=  $this->getQuery( $db_query );
		$objectResult	= @mysql_fetch_object( $result );

		return $objectResult ;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getContentsInArray( $query ){
		$count_query	= $this->getQuery( $query );
		$count_values	= $this->getFetch_Array( $count_query );

		return $count_values;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getTotalNumber( $tbname, $where = '' ){
		$where_tail		= ( !empty( $where ) ) ? " WHERE ".$where : "";
		$query				= "SELECT count( * ) AS total FROM ". $tbname . $where_tail;
		$count_values	= $this->getContentsInArray( $query );

		return $count_values['total'];
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getContents( $code, $where = '' ){
		$where_tail	= ( !empty( $where ) ) ? " WHERE ".$where : "";
		$query			= "SELECT * FROM ". $code . $where_tail;

		return $this->getObject( $query );
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getAllContents( $code, $where = '' ){
		$where_tail			= ( !empty( $where ) ) ? " WHERE ".$where : "";
		$query					= "SELECT * FROM ". $code . $where_tail;

		return $this->getContentsInArray( $query );
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getPageContent( $id, $where='' ){
		$where_tail = ( !empty( $where ) ) ? " AND ".$where : "";
		$query		 = "SELECT * FROM pages WHERE id = '". $id ."'". $where_tail ."";

		return $this->getContentsInArray( $query );
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getModules( $id ){
		$query = "SELECT * FROM modules WHERE id = '". $id ."'";

		return $this->getContentsInArray( $query );
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getBoardContent( $idarray, $where='' ){
		$where_tail	= ( !empty( $where ) ) ? " AND ".$where : "";
		$query			= "SELECT * FROM ".$idarray['tbname']." WHERE id = '". $idarray['tid'] ."'". $where_tail ."";

		return $this->getContentsInArray( $query );
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getMemberInfo( $id, $where='' ){
		$where_tail	= ( !empty( $where ) ) ? " AND ".$where : "";
		$query			= "SELECT * FROM members WHERE members_email_confirmed = '1' AND members_status = '1' AND id = '". $id ."'". $where_tail ."";

		return $this->getContentsInArray( $query );
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getMember( $username, $password, $extra_section = "" ){
		$query = "SELECT count( * ) AS total FROM members WHERE members_email_confirmed = '1' AND members_status = '1' AND ( members_type = '1' OR members_type = '2' ) AND ( members_level > '0' OR members_level < '4' )". $extra_section;
		$count_values	= $this->getContentsInArray( $query );

		if( $count_values['total'] == 1 ){
			$db_query			= "SELECT * FROM members WHERE members_email_confirmed = '1' AND members_status = '1'". $extra_section;
			$check_values	= $this->getContentsInArray( $db_query );
			if( !_getValidatePassword( $password, $check_values['members_password'] ) ){
				return false;
			} else{
				return true;
			}

		} elseif( intval( $count_values['total'] ) > 1 ){
			return false;

		} else{
			$num = $this -> getTotalNumber( "members", "members_email_confirmed = '1' AND members_status = '1' AND ( members_type = '1' OR members_type = '2' ) AND ( members_level > '0' OR members_level < '4' )". $extra_section );
			if( $num == 1 ){
				$db_query			= "SELECT * FROM members WHERE members_email_confirmed = '1' AND members_status = '1' ". $extra_section;
				$check_values	= $this->getContentsInArray( $db_query );
				if( !_getValidatePassword( $password, $check_values['members_password'] ) ){
					return false;
				} else{
					return true;
				}

			} else{
				return false;
			}

			return false;
		}
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getMemberGroup( $where = '' ){
		$level_array	= array();
		$level_query	= $this->getQuery( "SELECT * FROM members_group WHERE ". $where ."" );

		while( $levelVvalues = $this->getFetch_Array( $level_query ) ){
			$level_array[] = array( 'id' => $levelVvalues['members_group_id'], 'text' => $levelVvalues['members_group_name'] );
		}

		return $level_array;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getAccessLevel( $where = '' ){
		$level_array	= array();
		$level_query	= $this->getQuery( "SELECT * FROM members_level WHERE ". $where ."" );

		while( $levelVvalues = $this->getFetch_Array( $level_query ) ){
			$level_array[] = array( 'id' => $levelVvalues['members_level_id'], 'text' => $levelVvalues['members_level_name'] );
		}

		return $level_array;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getMemberTypeList( $where = '' ){
		$access_array	= array();
		$access_query	= $this->getQuery( "SELECT * FROM members_type WHERE ". $where ."" );

		while( $access_values = $this->getFetch_Array( $access_query ) ){
			$access_array[] = array( 'id' => $access_values['members_type_id'], 'text' => $access_values['members_type_name'] );
		}

		return $access_array;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getMemberType( $id, $where='' ){
		$where_tail		= ( !empty( $where ) ) ? " AND ".$where : "";
		$db_query			= "SELECT * FROM members_type WHERE members_type_status = '1' AND members_type_id = '". $id ."'". $where_tail ."";
		$content_values	=  $this->getContentsInArray( $db_query );

		$typeName = $content_values['members_type_name'];

		return $typeName;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- 
	function getModulePage( $where ){
		$mpage_array	= array();
		$mpage_query	= $this->getQuery( "SELECT * FROM modules_pages WHERE ". $where ."" );

		while( $mpValues = $this->getFetch_Array( $mpage_query ) ){
			$mpage_array[] = $mpValues['pageid'];
		}

		return $mpage_array;
	}
*/
/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getBannerPage( $where ){
		$mpage_array = array();
		$mpage_query = $this->getQuery( "SELECT * FROM banners_pages WHERE ". $where ."" );

		while( $mpValues = $this->getFetch_Array( $mpage_query ) ){
			$mpage_array[] = $mpValues['pageid'];
		}

		return $mpage_array;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getModulePostionList( $where ){
		$position_array	= array();
		$position_query	= $this->getQuery( "SELECT * FROM modules_position ". $where ."" );

		while( $module_values = $this->getFetch_Array( $position_query ) ){
			$position_array[] = array( 'id' => $module_values['name'], 'text' => $module_values['title'] );
		}

		return $position_array;
	}

/** -------------------------------------------------------------------------
 * [00/00/2014]::Basic
 *  ------------------------------------------------------------------------- */
	function getAdGroupList( $where ){
		$position_array	= array();
		$position_query	= $this->getQuery( "SELECT * FROM advertises_group ". $where ."" );

		while( $module_values = $this->getFetch_Array( $position_query ) ){
			$position_array[] = array( 'id' => $module_values['name'], 'text' => $module_values['title'] );
		}

		return $position_array;
	}

/** -------------------------------------------------------------------------
 * [00/00/2014]::Basic
 *  ------------------------------------------------------------------------- */
	function getAdGroupList2Page( $where ){
		$position_array	= array();
		$position_query	= $this->getQuery( "SELECT * FROM advertises_group ". $where ."" );

		while( $module_values = $this->getFetch_Array( $position_query ) ){
			if ($this->getTotalNumber( "advertises", "position = '{$module_values['name']}'" ) > 0 ){
				$position_array[] = array( 'id' => $module_values['name'], 'text' => $module_values['title'] );			
			}
		}

		return $position_array;
	}
	
/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getPageList( $where ){
		$pages_array = array();
		$pages_categories_query = $this->getQuery( "SELECT categories FROM ( SELECT * FROM pages WHERE ". $where .") AS cp GROUP BY categories" );
		while( $pages_categories_values = $this->getFetch_Array( $pages_categories_query ) ){
				$pages_query = $this->getQuery( "SELECT * FROM pages WHERE categories = '". $pages_categories_values['categories'] ."' AND ". $where ."" );

				while( $pages_values = $this->getFetch_Array( $pages_query ) ){
					$pages_array[ ] = array( $this->getCategoriesName($pages_categories_values['categories']) => array( 'id' => $pages_values['id'], 'text' => $pages_values['title'] ) );
				}
		}

		return $pages_array;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getPageName( $pID,  $type = 'text' ){
		global $base_url;

		$db_query			= "SELECT * FROM pages WHERE publish = '1' AND status = '1' AND id = '{$pID}'";
		$page_values	= $this->getContentsInArray( $db_query );
		$pageName		= ( $type === 'html' ) ? "<div id='page_heading'><h1>{$page_values['title']}</h1></div>" : $page_values['title'];

		return $pageName;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getMenuTypeList( $where ){
		$menutype_array = array();
		$menutype_query = $this->getQuery( "SELECT * FROM menu_type WHERE ". $where ."" );

		while( $menutype_values = $this->getFetch_Array( $menutype_query ) ){
			$menutype_array[] = array( 'id' => $menutype_values['type'], 'text' => $menutype_values['title'] );
		}

		return $menutype_array;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getMenuList( $where ){
		$menu_array = array();
		$menu_query = $this->getQuery( "SELECT * FROM menu WHERE ". $where ." AND parent = '0' ORDER BY ordering, menutype DESC" );

		while( $menu_values = $this->getFetch_Array( $menu_query ) ){
			$menu_array[] = array( 'id' => $menu_values['id'], 'text' => $menu_values['mtitle'] );
			$child_menu_query = $this->getQuery( "SELECT * FROM menu WHERE parent = '". $menu_values['id'] ."' and ". $where ." ORDER BY ordering" );

			while( $child_menu_values = $this->getFetch_Array( $child_menu_query ) ){
				$menu_array[] = array( 'id' => $child_menu_values['id'], 'text' => '&nbsp;&#187;&nbsp;'.$child_menu_values['mtitle'] );
				$subchild_menu_query = $this->getQuery( "SELECT * FROM menu WHERE parent = '". $child_menu_values['id'] ."' and ". $where ."" );
				while( $subchild_menu_values = $this->getFetch_Array( $subchild_menu_query ) ){
					$menu_array[] = array( 'id' => $subchild_menu_values['id'], 'text' => '&nbsp;&#187;&#187;&nbsp;'.$subchild_menu_values['mtitle'] );
				}
			}
		}

		return $menu_array;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getMenuDesign( $where ){
		$mDesignarray = array();
		$mDesignquery = $this->getQuery( "SELECT * FROM menu_templates WHERE ". $where ."" );

		while( $mdValues = $this->getFetch_Array( $mDesignquery ) ){
			$mDesignarray[] = $mdValues['menuid'];
		}

		return $mDesignarray;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getSectionCategoryList( $where ){
		$sections_query = $this->getQuery( "SELECT * FROM sections WHERE ". $where ."" );

		while( $sections_values = $this->getFetch_Array( $sections_query ) ){
			$categories_query = $this->getQuery( "SELECT * FROM categories WHERE parent = '0' AND section = '". $sections_values['id'] ."'" );

			while( $categories_values = $this->getFetch_Array( $categories_query ) ){
				$categories_array[] = array( 'id' => $sections_values['id']."_".$categories_values['id'], 'text' => $sections_values['title']."/".$categories_values['title'] );

				$child_categories_query = $this->getQuery( "SELECT id, title, name FROM categories WHERE parent = '". $categories_values['id'] ."' AND section = '". $sections_values['id'] ."'" );

				while( $child_categories_values = $this->getFetch_Array( $child_categories_query ) ){
					$categories_array[] = array( 'id' => $sections_values['id']."_".$child_categories_values['id'], 'text' => $sections_values['title']."/".$categories_values['title']."-".$child_categories_values['title'] );
				}
			}
		}

		return $categories_array;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getSectionsList( $where ){
		$sections_array = array();
		$sections_query = $this->getQuery( "SELECT * FROM sections WHERE ". $where ."" );

		while( $sections_values = $this->getFetch_Array( $sections_query ) ){
			$sections_array[] = array( 'id' => $sections_values['id'], 'text' => $sections_values['title'] );
		}

		return $sections_array;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getSectionsTypeList( $where ){
		$sectionsTyep_array = array();
		$sectionsType_query = $this->getQuery( "SELECT * FROM sections WHERE ". $where ."" );

		while( $sectionsType_values = $this->getFetch_Array( $sectionsType_query ) ){
			$sectionsTyep_array[] = array( 'id' => $sectionsType_values['stype'].":".$sectionsType_values['id'] , 'text' => $sectionsType_values['title'] );
		}

		return $sectionsTyep_array;
	}

	/** -------------------------------------------------------------------------
 * [00/00/2011]::To bring menu Type to change
 *  ------------------------------------------------------------------------- */
	function getMenuSct( $where ){
		$menutype_query = $this->getQuery( "SELECT * FROM menu_type WHERE ". $where ."" );
		while ( $menutype_values = $this->getFetch_Array( $menutype_query ) ){
				$menus_array[] = array( 'id' => $menutype_values['type'], 'text' => $menutype_values['title'] );
		}

		return $menus_array;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To bring Sections and Categories to change
 *  ------------------------------------------------------------------------- */
	function getAdsGroupList( $where = "" ){
		$adsgroup_query = $this->getQuery( "SELECT * FROM advertises_group ORDER BY title" );

		while( $ads_values = $this->getFetch_Array( $adsgroup_query ) ){
				$adsgroup_array[] = array( 'id' => $ads_values['name'], 'text' => $ads_values['title'] );
		}

		return $adsgroup_array;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To bring Sections and Categories to change
 *  ------------------------------------------------------------------------- */
	function getSecCatListSct( $tbname, $where ){

			$menutype_query = $this->getQuery( "SELECT * FROM sections WHERE publish = '1' AND status = '1'".$where." ORDER BY ordering" );
			
			while( $menutype_values = $this->getFetch_Array( $menutype_query ) ){
				$menutype_count = $this->getTotalNumber( $tbname, "publish = '1' AND sectionid = '{$menutype_values['id']}'" );
				$sclist_array[] = array( 'id' => $menutype_values['id'], 'text' => $menutype_values['title']." ( ".$menutype_count." )" );

				if( $this->getTotalNumber( 'categories', "publish = '1' AND status = '1' AND section = '{$menutype_values['id']}'" ) > 0 ){
					$cquery = $this->getQuery( "SELECT * FROM categories WHERE publish = '1' AND status = '1' AND parent = '0' AND section = '{$menutype_values['id']}' ORDER BY ordering" );
					while( $cvalues = $this->getFetch_Array( $cquery ) ){
						$cat_count	  	= $this->getTotalNumber( $tbname, "publish = '1' AND categoriesid = '{$cvalues['id']}'" );				
						$sclist_array[] = array( 'id' => $menutype_values['id']."/".$cvalues['id'], 'text' => "&nbsp;&raquo;&nbsp;".$cvalues['title']." ( ".$cat_count." )" );						

						if( $this->getTotalNumber( 'categories', "publish = '1' AND status = '1' AND section = '{$menutype_values['id']}' AND parent = '{$cvalues['id']}'" ) > 0 ){
							$subcquery = $this->getQuery( "SELECT * FROM categories WHERE status = '1' AND section = '{$menutype_values['id']}' AND parent = '{$cvalues['id']}' ORDER BY ordering" );
							while( $subcvalues = $this->getFetch_Array( $subcquery ) ){
								$subcat_count 	 	= $this->getTotalNumber( $tbname, "publish = '1' AND status = '1' AND categoriesid = '{$subcvalues['id']}'" );
								$sclist_array[] = array( 'id' => $menutype_values['id']."/".$subcvalues['id'], 'text' => "&nbsp;&raquo;&raquo;&nbsp;".$subcvalues['title']." ( ".$subcat_count." )" );		
							}
						}
					}
				}
			}

		return $sclist_array;
	}
	
/** -------------------------------------------------------------------------
 * [00/00/2011]::To bring Sections and Categories to change
 *  ------------------------------------------------------------------------- */
	function getCatListSct( $where ){
		$sections_query = $this->getQuery( "SELECT * FROM sections WHERE ". $where ."" );

		while( $sections_values = $this->getFetch_Array( $sections_query ) ){
			$categories_query = $this->getQuery( "SELECT * FROM categories WHERE parent = '0' AND section = '". $sections_values['id'] ."'" );

			while( $categories_values = $this->getFetch_Array( $categories_query ) ){
				$categories_array[] = array( 'id' => $categories_values['id'], 'text' => $categories_values['title'] );

				$child_categories_query = $this->getQuery( "SELECT id, title, name FROM categories WHERE parent = '". $categories_values['id'] ."' AND section = '". $sections_values['id'] ."'" );
				while( $child_categories_values = $this->getFetch_Array( $child_categories_query ) ){
					$categories_array[] = array( 'id' => $child_categories_values['id'], 'text' => '&nbsp;|-'.$child_categories_values['title'] );
				}
			}
		}

		return $categories_array;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getCategoriesList( $where ){
		$categories_array = array();
		$categories_query = $this->getQuery( "SELECT * FROM categories WHERE parent = '0' and  ". $where ."" );

		while( $categories_values = $this->getFetch_Array( $categories_query ) ){
			$categories_array[] = array( 'id' => $categories_values['id'], 'text' => $categories_values['title'] );
			$child_categories_query = $this->getQuery( "SELECT id, title, name FROM categories WHERE parent = '". $categories_values['id'] ."' and ". $where ."" );

			while( $child_categories_values = $this->getFetch_Array( $child_categories_query ) ){
				$categories_array[] = array( 'id' => $child_categories_values['id'], 'text' => '&nbsp;|-'.$child_categories_values['title'] );
			}
		}

		return $categories_array;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getCategoriesName( $cID, $type = 'text' ){
		global $base_url;

		$db_query = "SELECT * FROM categories WHERE status = '1' AND publish = '1' AND id = '{$cID}'";
		$categories_values = $this->getContentsInArray( $db_query );

		if( $type === 'html' ){
			if( !empty( $categories_values['categories_name_image'] ) ){
				$CategoriesName = '<img src="'.$base_url.'/templates/'.CONFIG_TEMPLATE.'/images/'.$categories_values['image'].'" alt="'.$categories_values['title'].'" border="0">';
			}
			else{
				$CategoriesName = "<h1>{$categories_values['title']}</h1>";
			}
			$CategoriesName = "<div id='page_heading'>{$CategoriesName}</div>";
		}
		else{
			$CategoriesName = $categories_values['title'];
		}

		return $CategoriesName;
	}

/** -------------------------------------------------------------------------
 * [11/29/2014]::Basic
 *  ------------------------------------------------------------------------- */
	function getColoumn($table) {
		$fieldnames = array();
		$fieldnames_query = $this->getQuery( "SHOW COLUMNS FROM ". $table );
	    if ($this->getNumberRows($fieldnames_query) > 0) {
			while( $fieldnames_values = $this->getFetch_Array( $fieldnames_query ) ){
				$fieldnames[] = $fieldnames_values['Field'];
			}
		}
	      return $fieldnames;
	} 
	
/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getSectionsName( $sID,  $type = 'text' ){
		global $base_url;

		$db_query = "SELECT * FROM sections WHERE publish = '1' AND status = '1' AND id = '{$sID}'";
		$section_values = $this->getContentsInArray( $db_query );
		$SectionsName = ( $type === 'html' ) ? "<div id='page_heading'><h1>{$section_values['title']}</h1></div>" : $section_values['title'];

		return $SectionsName;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getTableName( $cID ){
		global $base_url;

		$db_query			= "SELECT * FROM categories WHERE status = '1' AND categories_id = '".$cID."'";
		$categories_values 	= $this->getContentsInArray( $db_query );
		$CategoriesName 	= $categories_values['categories_name'];

		return $CategoriesName;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getDBError( $query, $errno, $error ){
		die( '<div style="color:#000000;font-size:10px;font-family:Tahoma;"><b>' . $errno . ' - ' . $error . '<br /><br />' . $query . '<br /><br /><small><div style="color:#000000;font-size:10px;font-family:Tahoma;">[TEP STOP]</div></small><br /><br /></b></div>' );
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getDBAction( $table, $data, $action = 'insert', $parameters = '' ){
		reset( $data );
		if ( $action == 'insert' ){
			$query = 'INSERT INTO ' . $table . ' ( ';
				while( list( $columns, ) = each( $data ) ){
					$query .= $columns . ', ';
				}
			$query = substr( $query, 0, -2 ) . ' ) values ( ';
			reset( $data );
			while( list( , $value ) = each( $data ) ){
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
		elseif( $action == 'update' ){
			$query = 'UPDATE ' . $table . ' SET ';
			while( list( $columns, $value ) = each( $data ) ){
				switch ( ( string )$value ){
					case 'now()':
				$query .= $columns . ' = now(), ';
				break;
				case 'null':
					$query .= $columns .= ' = null, ';
				break;
				default:
					$query .= $columns . ' = \'' . $this->getInput( $value ) . '\', ';
				break;
				}
			}
			$query = substr( $query, 0, -2 ) . ' WHERE ' . $parameters;

		}
		elseif( $action == 'select' ){
			$query = 'SELECT ';

			foreach ( $data as $key => $kw ){
				$query .= $kw . ', ';
			}
			$query = substr( $query, 0, -2 ) . ' FROM ' . $table;
			if( !empty( $parameters ) ){
				$query = substr( $query, 0 ) . ' WHERE ' . $parameters;
			}
		}

		return $this->getQuery( $query );
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getInsertID(){
		return mysql_insert_id();
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Basic
 *  ------------------------------------------------------------------------- */
	function getInput( $string ){
		if ( function_exists( 'mysql_real_escape_string' ) ){
			if ( is_array( $string ) ){
				foreach( $string as $var=>$val ){
					$output[$var] = mysql_real_escape_string( $val );
					return $output;
				}
			}
			else{
				return mysql_real_escape_string( $string );
			}
		}
		elseif( function_exists( 'mysql_escape_string' ) ){
			if ( is_array( $string ) ){
				foreach( $string as $var=>$val ){
					$output[$var] = mysql_escape_string( $val );
					return $output;
				}
			}
			else{
				return mysql_escape_string( $string );
			}
		}

		return addslashes( $string );
	}
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!