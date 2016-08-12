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

class getAllElements {

	public $dbConnect;
	public $basicURL;
	public $static_url;
	public $error_flag;
	public $error_message;

	public function __construct( $basicURL ){
		global $Bon_db;

		$this->dbConnect	 		= $Bon_db;
		$this->basicURL			= $basicURL;
		$this->static_url 			= rtrim( str_replace( "www", CONFIG_STATIC_SUBDOMAIN, str_replace( "/backend", "", $this->basicURL ) ), "/" );
		$this->error_flag				= false;
		$this->error_message		= array();
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To display Error message
 *  ------------------------------------------------------------------------- */
	public function  setMessage( $error_message ){
		$message_size	= sizeof( $error_message );
		$msg 				= "<div class='warning'>\n";

		for( $i=0; $i < $message_size; $i++ ){
			$msg .= $error_message[$i]."<br />\n";
		}

		$msg .= "</div>\n";

		return $msg;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To display Error message
 *  ------------------------------------------------------------------------- */
	public function setArrayDepth(array $array) {
    $max_depth = 0;

    foreach ($array as $value) {
        if (is_array($value)) {
            $depth = $this->setArrayDepth($value) + 1;

            if ($depth > $max_depth) {
                $max_depth = $depth;
            }
        }
    }

    return $max_depth;
}

/** -------------------------------------------------------------------------
 * [00/00/2011]::PullDown Menu - Select
 * _getPull_down_menu to _getPullDownMenu
 *  ------------------------------------------------------------------------- */
	public function setPullDownMenu( $name, $values, $default = '', $parameters = '', $disabled = "" , $defaultset = false, $required = false ){
		$field = '<select name="' . _getOutputString( $name ) . '"';

		if ( _getCheckNullorNot( $parameters ) ) $field .= ' ' . $parameters;

		$field .= $disabled.">\n";
		$field .= $defaultset ? '<option value="0">'.TEXT_PLEASE_SELECT_ONE."</option>\n" : "";

		if ( empty( $default ) && isset( $GLOBALS[$name] ) ) $default = stripslashes( $GLOBALS[$name] );

			if( is_array( $default ) ){
				if( $this->setArrayDepth($values) > 1){
					$previous = "";
					for ( $i=0, $n=sizeof( $values ); $i<$n; $i++ ){
							foreach ( $values[$i] as $key => $kw ) {
									if( $previous !=  $key ){
										$field .= ( $i != 0 ) ? "</optgroup>\n" : "";
										$field .= "<optgroup label=\"". $key ."\">\n";
										$previous = $key;
									}

									$field .= "\t<option value=\"". _getOutputString( $kw['id'] ) ."\"";
									foreach ( $default as $key => $kww ){
										$sid_array			= explode( ":", $kw['id'] );
										$kw['id'] = count( $sid_array ) > 1 ?  end( $sid_array ) : $kw['id'];
										if ( $kww == $kw['id'] ){
											$field .= ' SELECTED';
										}
									}
									$field .= ">"._getOutputString( $kw['text'], array( '"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;' ) ) ."</option>\n";
						    	$field .= ( $i+1 == $n ) ? "</optgroup>" : "";
							}
					}

				}else{
					for ( $i=0, $n=sizeof( $values ); $i<$n; $i++ ){
							$field .= '<option value="' . _getOutputString( $values[$i]['id'] ) . '"';

							foreach ( $default as $key => $kw ){
								$sid_array			= explode( ":", $values[$i]['id'] );
								$values[$i]['id'] = count( $sid_array ) > 1 ?  end( $sid_array ) : $values[$i]['id'];
								if ( $kw == $values[$i]['id'] ){
									$field .= ' SELECTED';
								}
							}
							$field .= '>' . _getOutputString( $values[$i]['text'], array( '"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;' ) ) . '</option>'."\n";
					}
				}
			} else {
				if( $this->setArrayDepth($values) > 1){
					$previous = "";
					for ( $i=0, $n=sizeof( $values ); $i<$n; $i++ ){
							foreach ( $values[$i] as $key => $kw ) {
									if( $previous !=  $key ){
										$field .= ( $i != 0 ) ? "</optgroup>\n" : "";
										$field .= "<optgroup label=\"". $key ."\">\n";
										$previous = $key;
									}

									$field .= "\t<option value=\"". _getOutputString( $kw['id'] ) ."\"";
									if ( $default == $kw['id'] ){
										$field .= ' SELECTED';
									}
									$field .= ">"._getOutputString( $kw['text'], array( '"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;' ) ) ."</option>\n";
						    	$field .= ( $i+1 == $n ) ? "</optgroup>" : "";
							}
					}

				}else{
						for ( $i=0, $n=sizeof( $values ); $i<$n; $i++ ){
							$field .= '<option value="' . _getOutputString( $values[$i]['id'] ) . '"';

							$sid_array	= explode( ":", $values[$i]['id'] );
							$values[$i]['id'] = count( $sid_array ) > 1 ?  end( $sid_array ) : $values[$i]['id'];
							if ( $default == $values[$i]['id'] ){
								$field .= ' SELECTED';
							}

							$field .= '>' . _getOutputString( $values[$i]['text'], array( '"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;' ) ) . '</option>'."\n";
						}
				}
			}

		$field .= '</select>'."\n";
		$field .= $required ? TEXT_FIELD_REQUIRED : "";

		return $field;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::PullDown Menu with onchange - Select
 *  ------------------------------------------------------------------------- */
	public function  setPullDownMenuOnChange( $name, $values, $default = '', $parameters = '', $required = false ){
		$field = "\n\t".'<select name="' . _getOutputString( $name ) . '"';

		if ( _getCheckNullorNot( $parameters ) ){ $field .= ' ' . $parameters; }

		$field .= ' onchange="this.form.submit();">'."\n";
		$field .= "\t".'<option value="">'.CONFIG_PLEASE_SELECT_ONE.'</option>'."\n";
		if ( empty( $default ) && isset( $GLOBALS[$name] ) ) $default = stripslashes( $GLOBALS[$name] );

		for ( $i=0, $n=sizeof( $values ); $i<$n; $i++ ){
			$field .= "\t".'<option value="' . _getOutputString( $values[$i]['id'] ) . '"';
			if ( $default == $values[$i]['id'] ){ $field .= ' SELECTED'; }

			$field .= '>' . _getOutputString( $values[$i]['text'], array( '"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;' ) ) . '</option>'."\n";
		}
		$field .= "\t".'</select>'."\n";
		$field .= $required ? TEXT_FIELD_REQUIRED : "";

		return $field;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Select menu with onchange JS
 *  ------------------------------------------------------------------------- */
	public function  setPullDownMenuOnChangeJS( $name, $values, $default = '', $parameters = '', $required = false ){
		$field = "\n<script language=\"JavaScript\" type=\"text/JavaScript\">
					<!--
					function JumpMenu( targ,selObj,restore ){ //v3.0
					  eval( targ+\".location='\"+selObj.options[selObj.selectedIndex].value+\"'\" );
					  if ( restore ) selObj.selectedIndex=0;
					}
					//-->
					</script>";
		$field .= "\n\t".'<select name="' . _getOutputString( $name ) . '"';

		if ( _getCheckNullorNot( $parameters ) ){ $field .= ' ' . $parameters; }

		$field .= ' onchange="JumpMenut( \'parent\',this,0 );">'."\n";
		$field .= "\t".'<option value="">'.CONFIG_PLEASE_SELECT_ONE.'</option>'."\n";
		if ( empty( $default ) && isset( $GLOBALS[$name] ) ) $default = stripslashes( $GLOBALS[$name] );

		for ( $i=0, $n=sizeof( $values ); $i<$n; $i++ ){
			$field .= "\t".'<option value="' . _getOutputString( $values[$i]['id'] ) . '"';
			if ( $default == $values[$i]['id'] ){ $field .= ' SELECTED'; }

			$field .= '>' . _getOutputString( $values[$i]['text'], array( '"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;' ) ) . '</option>'."\n";
		}
		$field .= "\t".'</select>'."\n";
		$field .= $required ? TEXT_FIELD_REQUIRED : "";

		return $field;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::Getting prper link with http://
 * http://www.yourdomain.com OR subdoamin.yourdomain.com
 *  ------------------------------------------------------------------------- */
	public function setProperLink( $url ){
		$pharse_url = explode( ".", $url );

		if ( $pharse_url[0] == 'www' && sizeof( $pharse_url )== 3 ){
			$link		= "http://". $url ."";

		} elseif ( $pharse_url[0] == 'http://www' ){
		       $link	= $url;

	    } elseif ( $pharse_url[0] != 'www' ){
	    	$second_pharse_url = explode( "/", $pharse_url[0] );

			if( sizeof( $pharse_url ) == 3 ){
		        $link = "http://". $url ."";

		    } else {
		    	if( end( $second_pharse_url ) != 'www' && $second_pharse_url[0] == 'http:' ){
		    		$link = $url;

		    	} else {
		    		$link = "http://www.". $url ."";
		    	}
		    }
		}

		return $link;
	}

 /** -------------------------------------------------------------------------
 * [00/00/2011]::@param string $pInfo
 *  ------------------------------------------------------------------------- */
	public function getValidation( $val, $max=2, $type="string" ){
		if( !empty( $val ) ){
			$val = trim( $val );
			if( strlen( $val ) <= ( int )$max ){
				if( $type === "email" ){
					$this->error_flag = ( bool )preg_match( "/^[a-z0-9][a-z0-9_\.-]{0,}[a-z0-9]@[a-z0-9][a-z0-9_\.-]{0,}[a-z0-9][\.][a-z0-9]{2,4}$/ix", $val );

				} elseif( $type === "int" ){
					$this->error_flag = ( bool )is_int( $val );

				} else {
					$this->error_flag = ( bool )preg_match( "/^( [a-zA-Z0-9] )+$/i", $val );
				}
			}

		}else{
			$this->error_flag = false;
		}

 		return $this;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To display publsiching icon
 *  ------------------------------------------------------------------------- */
	public function setPublishIcon( $nodeValue, $id, $value, $sstring = "", $cstring = "", $pstring  = "" ){
		switch( strtoupper( $value ) ){
			case '1':
				$icon =  "<a href=\"".$this->basicURL."/indexx.php?node={$nodeValue}{$sstring}{$cstring}&amp;id={$id}{$pstring}&amp;st=0&amp;m=update"."\" title=\"Click to deactivate\" onfocus=\"this.blur()\"><img src='".$this->basicURL."/design/images/admin/icon-16-allow.png' width='16px' height='16px' alt='yes'></a>\n";
			break;
			case '0':
				$icon =  "<a href=\"".$this->basicURL."/indexx.php?node={$nodeValue}{$sstring}{$cstring}&amp;id={$id}{$pstring}&amp;st=1&amp;m=update"."\" title=\"Click to activate\" onfocus=\"this.blur()\"><img src='".$this->basicURL."/design/images/admin/icon-16-deny.png' width='16px' height='16px' alt='no'></a>\n";
			break;
		}

		return $icon;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To dispaly menu bar in a page
 *  ------------------------------------------------------------------------- */
	public function setToolbar( $title, $xtraclass="", $btns ){

		$toolbarframe = "<div class=\"toolbar_box\">
										<div class=\"t\">
												<div class=\"t\">
														<div class=\"t\"></div>
												</div>
										</div>
										<div class=\"m\">
												<div class=\"toolbar\">
															{$title}
												</div>
												<div class=\"header {$xtraclass}\"> {$btns}</div>
												<div class=\"clr\"></div>
										</div>
										<div class=\"b\">
												<div class=\"b\">
														<div class=\"b\"></div>
												</div>
										</div>
										</div>";

		return $toolbarframe;
	}

/** -------------------------------------------------------------------------
 * [03/01/2014]::Getting page number block
 *  ------------------------------------------------------------------------- */
	public function setPagelistblock( $nodeValue, $total_page, $noPage, $sxtra="", $cxtra="", $total_list="", $opt=false ){

		if(($opt === true) && _getCheckNullorNot($total_list)){
			$actualTotalpage = ceil( $total_list/CONFIG_HOW_MANY_ARTICLES_PER_PAGE );
		}

		if( $total_page != 0){
			$_pagelist		= "<div class=\"page_numbers_area\">";
			$total_block	= ceil( $total_page/CONFIG_HOW_MANY_PAGES_PER_BLOCK );
			$block 			= ceil( $noPage/CONFIG_HOW_MANY_PAGES_PER_BLOCK );
			$first_page 	= ( $block-1 )*CONFIG_HOW_MANY_PAGES_PER_BLOCK;
			$last_page 	= $block*CONFIG_HOW_MANY_PAGES_PER_BLOCK;

			$_pagelist .=  ($opt === true) ? " <a href=\"".$this->basicURL."/indexx.php?node={$nodeValue}{$sxtra}{$cxtra}&amp;p=all\" class=\"prev\" onfocus=\"this.blur()\">ALL</a> " : "";
			if( $total_block <= $block ){$last_page = $total_page;}

			//Link to preview page block
			if( $block > 1 ){
				$my_page	 = $first_page;
				$_pagelist .= " <a href=\"".$this->basicURL."/indexx.php?node={$nodeValue}{$sxtra}{$cxtra}&amp;p={$my_page}\" class=\"prev\" onfocus=\"this.blur()\">previews</a> ";
			}

			//Link to the page directly
			for( $direct_page = $first_page+1; $direct_page <= $last_page; $direct_page++ ){
				$_pagelist .= ( $noPage == $direct_page ) ? " <strong class=\"page_numbers current \">{$direct_page}</strong>\n " : " <a href=\"".$this->basicURL."/indexx.php?node={$nodeValue}{$sxtra}{$cxtra}&amp;p={$direct_page}\" class=\"page_numbers\" onfocus=\"this.blur()\">{$direct_page}</a> \n";
			}

			//Link to next page block
			if( $block < $total_block ){
				$my_page	 = $last_page+1;
				$_pagelist .= " <a href=\"".$this->basicURL."/indexx.php?node={$nodeValue}{$sxtra}{$cxtra}&amp;p={$my_page}\" class=\"next\" onfocus=\"this.blur()\">next</a> \n";
			}

			$_pagelist .= ( ($opt === true) && ($actualTotalpage > 1) && ($direct_page-1 != $actualTotalpage) ) ? " <a href=\"".$this->basicURL."/indexx.php?node={$nodeValue}{$sxtra}{$cxtra}\" class=\"prev\" onfocus=\"this.blur()\">PAGES</a> " : "";
			$_pagelist .= "</div>\n";
		} else{
			$_pagelist = "\n\t<!--zero page-->";
		}

		return $_pagelist;
	}

/** -------------------------------------------------------------------------
 * [00/00/2011]::To dispaly menu bar in a page
 *  ------------------------------------------------------------------------- */
	function getCurrentItems( $tbname, $max = 1, $type = "articles", $svalue = "", $cvalue="", $eclass = "" ){

 		if( $type === "pages" ){
			$count = 1;
			$inside   = "<div class=\"currenttitle\"><strong>Recent ".$this->dbConnect->getCategoriesName( $cvalue, 'text' )."</strong></div>\n<ul class=\"currentitems\">\n";
			$list_query = $this->dbConnect->getQuery( "SELECT * FROM {$tbname} WHERE publish = '1' AND status = '1' ORDER BY created_date DESC LIMIT 0, {$max}" );
			while ( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
					$newTitle	= _mb_strcut( $list_values['title'], 0, 90 ) . "&nbsp;&nbsp;[". _getShortFormatDate($list_values['created_date']) ."]";
					$pURL		= $this->basicURL."/indexx.php?node=page{$sxtra}{$cxtra}&amp;id={$list_values['id']}{$pxtra}&amp;m=view";
					$inside     .= "<li>\n<a href=\"{$pURL}\" class=\"item{$count}\" title=\"{$list_values['title']}\" target=\"_self\" onfocus=\"this.blur()\">{$newTitle}</a>\n</li>\n";

					$count++;
			}

			$inside  .= "</ul>\n<a href=\"".$this->basicURL."/indexx.php?node=page&sid={$svalue}&cid={$cvalue}&m=view\" target=\"_self\" class=\"wbtn\" onfocus=\"this.blur()\">POST</a>\n";

		}elseif( $type === "articles" ){
			$count	 = 1;
			$cValues = _getCheckNullorNot( $cvalue ) ? " AND b.categoriesid = '{$cvalue}'" : "";
			$inside   = "<div class=\"currenttitle\"><strong>Recent ".$this->dbConnect->getSectionsName( $svalue, 'text' )."</strong></div>\n<ul class=\"currentitems\">\n";
			$list_query = $this->dbConnect->getQuery( "SELECT b.id, b.title, b.title_alias, b.embed_code, b.ordering, b.created_date, b.created_by, b.created_by_alias, o.external FROM {$tbname} b, opensef  o WHERE b.id = o.tid AND o.tbname = '{$tbname}' AND o.publish = '1' AND b.publish = '1' AND b.status = '1' AND b.notice = 0 AND b.sectionid = '{$svalue}'". $cValues ." ORDER BY b.created_date DESC LIMIT 0, {$max}" );
			while ( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
					$newTitle	= _mb_strcut( $list_values['title'], 0, 90 );
					$pURL		= $this->basicURL."/indexx.php?node=article{$sxtra}{$cxtra}&amp;id={$list_values['id']}{$pxtra}&amp;m=view";
					$inside     .= "<li><a href=\"{$pURL}\" class=\"item{$count}\" title=\"{$list_values['title']}\" target=\"_self\" onfocus=\"this.blur()\">{$newTitle}</a></li>\n";

					$count++;
			}

			$inside  .= "</ul>\n<a href=\"".$this->basicURL."/indexx.php?node=article&sid={$svalue}&cid={$cvalue}&m=view\" target=\"_self\" class=\"wbtn\" onfocus=\"this.blur()\">POST</a>\n";

		}elseif( $type === "photos" ){
			$actual_cvalue = !empty( $cvalue ) ? " AND b.categoriesid = '{$cvalue}' " : "";
			$maxCols	= 3;
			$recIdx		= 0;
			$list_query = $this->dbConnect->getQuery( "SELECT b.id, b.title, b.fulltxt, b.filename, b.urls, b.embed_code, b.views, b.votes,o.external FROM {$tbname} b, opensef o WHERE b.id = o.tid AND o.tbname = 'pictures' AND o.publish = '1' AND b.access_level >= '1' AND b.group_level >= '1' AND b.publish = '1' AND b.status = '1' AND b.notice = 0 AND b.sectionid = '{$svalue}'{$actual_cvalue}ORDER BY b.created_date DESC LIMIT 0, {$max}" );
			while( $list_values = $this->dbConnect->getFetch_Array( $list_query ) ){
				$recIdx++;
				$maxWidth = 90;
				$file_array = explode( ";", $list_values['filename'] );

				for( $i=0; $i < count( $file_array ); $i++ ){
					$src[$i]					= $_SERVER['DOCUMENT_ROOT'] ."/". CONFIG_FILES_UPLOAD_ROOT.$file_array[$i];
					$image_size[$i] 		= getimagesize( $src[$i] );
					$percent_resizing	= round( ( $maxWidth / $image_size[$i][0] ) * 100 );
					$new_height  		= round( ( $percent_resizing / 100 )  * $image_size[$i][1] );

					if( $new_height < 70 ){
						$image_name[$recIdx] = $file_array[$i];
						$i = 7;
					}
				}

				if( empty( $image_name[$recIdx] ) ){
					$image_name[$recIdx]	= $file_array[0];
					$maxWidth 					= 40;
				}

				$image_location = $this->basicURL.$list_values['external'];

				if( $recIdx % $maxCols == 1 ){
					$_boardlist .= "\n\t<tr>";
				}

				$_boardlist .= "\n\t<td align=\"center\" height=\"80\"><a href=\"". $image_location."\" title=\"".stripslashes( $list_values['title'] )."\" onfocus=\"this.blur()\">".getDisplayImg( $image_name[$recIdx], $maxWidth, stripslashes( $list_values['title'] ), false, " class=\"frontpiclist\"" )."</a></td>";
				//Close row if needed

				if( $recIdx % $maxCols == 0 ){
					$_boardlist .= "\n\t</tr>";
				}
			}

			//Close last row if needed
			if( $recIdx % $maxCols != 0 ){
				$_boardlist .= "\n\t</tr>";
			}

			$inside  = "\n\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\" class=\"front_photo_group\">\n\t<tbody>".$_boardlist."\n\t</tbody>\n\t</table>";

		}elseif( $type === "videos" ){
			$list_query = "SELECT b.title, b.embed_code_s, b.ordering, b.host, b.casting_date, b.created_date, b.created_by, o.external FROM {$tbname} b, opensef  o WHERE b.id = o.tid AND o.tbname = '{$tbname}' AND o.publish = '1' AND b.publish = '1' AND b.status = '1' {$list_tail} ORDER BY b.created_date DESC LIMIT 0, {$max}";
			$insideObject = $this->dbConnect->getObject( $list_query );
			$inside  ="<div class=\"img_box shadow_img see_big\">
							<div style=\"cursor:pointer\">
								<div class=\"description\">
									<span class=\"title\"><a href=\"{$insideObject->external}\" title=\"{$insideObject->title}\" target=\"_self\" onfocus=\"this.blur()\">".$insideObject->title."</a></span>
								</div>
								<div class=\"author\">
									<span class=\"date\">".$insideObject->casting_date."</span><span class=\"nickname\">{$insideObject->host}</span>
								</div>".$insideObject->embed_code_s."
							</div>
						</div>";
		}

 		return $inside;
	}
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!