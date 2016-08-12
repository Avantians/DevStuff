<?php 
/** -------------------------------------------------------------------------
 * [v01-09/01/2015]:: Set flag, not allow to direct access
 *  ------------------------------------------------------------------------- */
defined( "_VALID_MOS" ) or die( "Your system is not working properly." );

global $user_ip, $gvalue, $redirect_flag;

		if( !_getCheckNullorNot($gvalue['title']) && !_getCheckNullorNot($gvalue['captcha'])){
		   _getRedirect( $base_url ."/connecting/in-life");
		   exit();
		}
		else {
			if ( md5( $gvalue['captcha'] ).'a4xn' === $gvalue['verifiedid'] ){
				$rNumber = time();
		  	}
			else {
				echo "<script type=\"text/javascript\"> alert( 'Please Verificate Number!' ); window.history.go( -1 ); </script>\n";
				exit();
			}
		
		}
		if( isset($_FILES) && $_FILES[filename][error][0] == 0 ){
			$uploading_file = getUploadMultiFile( $_FILES[filename] );
		
			for( $j=0; $j < count( $uploading_file['name'] ); $j++ ){
				$divider = ( $j == count( $uploading_file['name'] )-1 ) ? "":";";
		
				$filename	.= $uploading_file['name'][$j].$divider;
				$filetype	.= $uploading_file['type'][$j].$divider;
				$filesize	.= $uploading_file['size'][$j].$divider;
			}
			unset( $uploading_file );
		}
		
		$ordering = $Bon_db->getTotalNumber( "articles", "sectionid = '4' AND categoriesid = '8' AND publish != '2' AND status != '2'" ) + 1;
		$sql_data_array = array(
											'title'							=> $gvalue['title'],
											'fulltxt'						=> $gvalue['fulltxt'],
											'filename'					=> $filename,
											'filesize'						=> $filesize,
											'urls'							=> $gvalue['urls'],
											'sectionid'					=> $gvalue['sectionid'],
											'categoriesid'				=> $gvalue['categoriesid'],
											'ordering'					=> $ordering,
											'host'							=> $gvalue['yname'],
											'metatitle'					=> $gvalue['title'],
											'metakey'					=> $gvalue['title'],
											'metadesc'					=> $gvalue['title'],
											'group_level'				=> 1,
											'access_level'			=> 7,
											'password'					=> $gvalue['pwd'],
											'user_ip'						=> $user_ip,
											'publish'						=> 1,
											'status'	 					=> 0,
											'notice'						=> 0,
											'loginDownload'			=> 0,
											'downloadAlias'			=> "",
											'linkOpt'						=> "",
											'views'						=> 0,
											'modified_date' 			=> "now()",
											'created_date'  			=> "now()",
											'created_by'    			=> 000,
											'publish_date'				=> "now()"
											);
		$Bon_db->getDBAction( "articles", $sql_data_array );
		$inserted_article_id = $Bon_db->getInsertID();
		
		$sef_sql_data_array = array(
												'tid'					=> $inserted_article_id,
												'tbname'				=> "articles",
												'external'			=> "/".date( 'md' ).$inserted_article_id,
												'pid'					=> 9,
												'direction'			=> "",
												'publish'				=> 1,
												'created_date'		=> "now()",
												'modified_date'	=> "now()",
												'hits'					=> 0,
												'notes'				=> ""
											 );
		$Bon_db->getDBAction( 'opensef', $sef_sql_data_array );

		$body = "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:600px; font-size:13px; font-family:Tahoma;\">";
		$body  .= "<tr><td valign=\"top\" style=\"width:100px;\"><strong>Title</strong>:&nbsp;&nbsp;</td><td valign=\"top\">". stripslashes($gvalue['title']) ."</td></tr>"
					."<tr><td valign=\"top\" style=\"width:100px;\"><strong>Full Name</strong>:&nbsp;&nbsp;</td><td valign=\"top\">". stripslashes($gvalue['yname']) ."</td></tr>"
					."<tr><td valign=\"top\" style=\"width:100px;\"><strong>Email</strong>:&nbsp;&nbsp;</td><td valign=\"top\">". stripslashes(_getActualEmail( $gvalue['yemail'])) ."</td></tr>";
					
		if( _getCheckNullorNot($gvalue['urls'])){
			$body .= "<tr><td valign=\"top\" style=\"width:100px;\"><strong>Attachment</strong>:&nbsp;&nbsp;</td><td valign=\"top\">". stripslashes($gvalue['urls']) ."</td></tr>";
		}
		if( isset($_FILES) && $_FILES[filename][error][0] == 0 ){
			$body .= "<tr><td valign=\"top\" style=\"width:100px;\"><strong>Attachment</strong>:&nbsp;&nbsp;</td><td valign=\"top\">". stripslashes($filename) ."</td></tr>";
		} 	
		
		$body .= "<tr><td valign=\"top\" style=\"width:100px;\"><strong>Contents</strong>:&nbsp;&nbsp;</td><td valign=\"top\"></td></tr>"
					."<tr><td valign=\"top\" colspan=\"2\">". stripslashes($gvalue['fulltxt']) ."</td></tr>"
					."<tr><td valign=\"top\" colspan=\"2\"><hr/><br/>위 글을 게시 승인하기 위해서는 관리자 모드로 접속 후에 아래 링크를 클릭하세요.<br/><br/><a href=\"".$base_url."/backend/indexx.php?node=article&sid=4&cid=8&id=".$inserted_article_id."&m=view\" target=\"_blank\">".$base_url."/backend/indexx.php?node=article&sid=4&cid=8&id=".$inserted_article_id."&m=view</a><br/><br/>마지막 탭 \"Parameters ( Advanced )\" 클릭 후 아래쪽에 있는 \"Status\"를 \"YES\"로 변경 후에 \"UPDATE\" 혹은 \"APPLY\" 누르시면 됩니다.</td></tr></table>";	
		
		
		$vEmail  = _getSendEmail( $gvalue['yname'], _getActualEmail( CONFIG_ADMIN_EMAIL ), "삶 속의 나눔 게시물", $body, CONFIG_SITE_NAME, _getActualEmail( CONFIG_SITE_EMAIL ));

		unset($sql_data_array);
		unset($gvalue);
				
//		echo "\n<script type=\"text/javascript\">parent.location=\"".$base_url."/connecting/in-life/thanx-sharing\"</script>";
//_getSendEmail( "TorontoConnectChurch", _getActualEmail( ( "iweb&#64;kenwoo.ca" ) ), "삶 속의 나눔 게시물", $body, CONFIG_SITE_NAME, _getActualEmail( CONFIG_SITE_EMAIL ));

header( "refresh:12;url=".$base_url."/connecting/in-life" );
?>