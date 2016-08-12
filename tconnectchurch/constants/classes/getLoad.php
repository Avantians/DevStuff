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

class getLoad {

    public static $basicURL;
    public static $error_flag;
    public static $error_message;
    public static $returnValue;
    public static $dbConnect;
    public static $dphp;
    public static $module;
    public static $MetaInfo;
    public static $minifyHtml;

    public function __construct( $baseURL ){
        global $Bon_db, $base_url;

        self::$dbConnect                = $Bon_db;
        self::$basicURL                 = !_getCheckNullorNot( $baseURL ) ? $base_url : $baseURL;
        self::$error_flag                   = false;
        self::$error_message    = array();
        self::$returnValue          = array();
        self::$dphp                             = new getDirectCode();
        self::$module                       = new getApps("404");
        self::$MetaInfo                 = new getMetaInfo( self::$basicURL );
        self::$minifyHtml                       = new getHTMLOptimize();

        if ( !preg_match( "/$server_host/i", $_SERVER['SERVER_NAME'] ) ){
            self::$error_message    = PLEASE_WRITE_ARTICLE_PROPERLY;
            self::$error_flag                   = true;
        }

        if ( getenv( $_SERVER['REQUEST_METHOD'] ) == "GET" ){
            self::$error_message    = PLEASE_WRITE_ARTICLE_PROPERLY_2;
            self::$error_flag                   = true;
        }
    }

/** -------------------------------------------------------------------------
 * [00/00/2011]:: To
 *  ----------------------------------------------------------------------- */
    public function setLoading( $lodaing_path ){
        global $gvalue;

        if ( _getCheckNullorNot( $lodaing_path ) && self::$error_flag === false ){
            //To check the existing session if there is one and update session live time
            if ( isset( $_SESSION ) && $_SESSION['session_user_id'] && $_SESSION['guest'] == 1 ){
                _getCheckingSession();
            }

            if ( $lodaing_path === "/" ){
                    //To display the front page
                    $FrontContents = self::$dbConnect->getFrontPage( "publish = '1' AND status = '1'" );
                    if ( _getCheckNullorNot( $FrontContents['fulltxt'] ) ){
                        $FrontContents['pid'] = $FrontContents['id'];

                        self::$returnValue['design']        = $this->setTemplate( $FrontContents );
                        self::$returnValue['modules']   = self::$module->setApps( $FrontContents );
                        self::$returnValue['metainfo']  = self::$MetaInfo->setMetInfos( $FrontContents );
                        self::$returnValue['contents']  = self::$minifyHtml->minifyHTML(self::$dphp->getDirectPHPS( $FrontContents['fulltxt'], false, $FrontContents['id'] ));

                        self::$dbConnect->getQuery( "UPDATE pages SET views = views + 1 WHERE access_level >= '".getAllElements::setAccesslevel("ulevel")."' AND group_level >= '".getAllElements::setAccesslevel("gid")."' AND publish = '1' AND status = '1' AND id = '{$FrontContents['id']}'" );
                    }
                    else {
                        self::$returnValue['contents']  = "<br/> <strong>Hello there,</strong><br/> This is the front page.<div class=\"divider\"> </div>";
                        self::$returnValue['metainfo']  = self::$MetaInfo->setMetInfos( $all_ids );
                    }
            }
            else {
                        //To assign proper action to do under the page
                        //preg_replace( "/\/$/", "", $lodaing_path ) is to remove " /  " from the End of URL
                        $_unset                         = true;
                        $basic_action           = array( "post","edit","delete","reply","register","activation","login","logout" );
                        $lodaing_path           = preg_replace( "/\/$/", "", $lodaing_path );
                        $lodaing_path           = preg_replace( "/\&$/", "", $lodaing_path );
                        $pageInfo_array = explode( "/", $lodaing_path );

                        if ( in_array( end( $pageInfo_array ), $basic_action ) ){
                            $do                     = end( $pageInfo_array );
                            $_alias             = rtrim( str_replace( $do, "", $lodaing_path ), "/" );
                            $_unset         = false;
                        }
                        elseif ( $pageInfo_array[1] === "download" ){
                            $do                 = $pageInfo_array[1] ;
                            $_falias        = rtrim( str_replace( "/download/", "", $lodaing_path ), "/" );
                            $_unset     = false;
                        }
                        elseif ($pageInfo_array[1] === "xml") {
                            $do                 = $pageInfo_array[1] ;
                            $_falias        = rtrim(str_replace("/xml/", "", $lodaing_path), "/");
                            $_unset     = false;
                        }
                        elseif ($pageInfo_array[1] === "search") {
                            $do                 = $pageInfo_array[1] ;
                            $_falias        = rtrim(str_replace("/search/", "", $lodaing_path), "/");
                            $_unset     = false;
                        }
                        elseif ( $pageInfo_array[1] === "minifycss" ){
                            $do                 = $pageInfo_array[1] ;
                            $_falias        = rtrim( str_replace( "/minifycss/", "", $lodaing_path ), "/" );
                            $_unset     = false;
                        }
                        elseif ( $pageInfo_array[1] === "minifyjs" ){
                            $do                 = $pageInfo_array[1] ;
                            $_falias        = rtrim( str_replace( "/minifyjs/", "", $lodaing_path ), "/" );
                            $_unset     = false;
                        }
                        else {
                            $do             = $lodaing_path;
                            $_alias     = $lodaing_path;
                        }

                        $ci_array = explode( "&",$_alias );
                        if ( count( $ci_array ) > 1 ){
                             if ( is_numeric( end( $ci_array ) ) ){
                                    $_alias                 = str_replace( "&".end( $ci_array ), '', $_alias );
                                    $page_array = array( "page_no" => end( $ci_array ) );
                                    $xno                        = "&".end( $ci_array );
                             }
                             else {
                                    $wm_array = explode( "&_",$_alias );
                                    if ( count( $wm_array ) > 1 ){
                                        $_alias     = str_replace( "&_".end( $wm_array ), '', $_alias );
                                        $nowm_array = explode( "&", end( $wm_array ) );
                                         for( $n =0; count( $nowm_array ) > $n; $n++ ){
                                            $value_array                                                = explode( "=", $nowm_array[$n] );
                                            $addition_array[strtolower($value_array[0])]    = _getSanitize( $value_array[1] );
                                         }
                                    }
                                    else {
                                        unset( $do );
                                    }
                             }
                        }

                        $menu_array = explode( "/", $_alias );
                        $article_id = end( $menu_array );
                        $check_sef  = self::$dbConnect->getTotalNumber( "opensef", "publish = '1' AND external LIKE '%{$article_id}%'" );
                        if ( is_numeric( $article_id ) && ( $check_sef > 0 ) ){
                            $_alias     = rtrim( str_replace( $article_id, "", $_alias ), "/" );
                        }
                        elseif ( is_numeric( $article_id ) && ( $check_sef == 0 ) ){
                            $article_id ="-+-";
                            $_alias = $_alias;
                        }
                        else {
                            $article_id ="-+-";
                            $_alias = $_alias;
                        }

                        if ( self::$dbConnect->getTotalNumber( "menu", "publish = '1' AND status = '1' AND group_level >= '".getAllElements::setAccesslevel("gid")."' AND access_level >= '".getAllElements::setAccesslevel("ulevel")."' AND alias = '{$_alias}'" ) > 0 ){
                            if ( $check_sef > 0 ){
                                $pidqry = "SELECT pid FROM menu WHERE publish = '1' AND status = '1' AND group_level >= '".getAllElements::setAccesslevel("gid")."' AND access_level >= '".getAllElements::setAccesslevel("ulevel")."' AND alias = '{$_alias}'";
                                $pidObject = self::$dbConnect->getObject( $pidqry );

                                $ids_query  = self::$dbConnect->getQuery( "SELECT * FROM opensef WHERE publish = '1' AND pid = '{$pidObject->pid}' AND external LIKE '%{$article_id}%'" );
                                $all_ids    = self::$dbConnect->getFetch_Array( $ids_query );
                            }
                            else {
                                $ids_query  = self::$dbConnect->getQuery( "SELECT * FROM menu WHERE publish = '1' AND status = '1' AND group_level >= '".getAllElements::setAccesslevel("gid")."' AND access_level >= '".getAllElements::setAccesslevel("ulevel")."' AND alias = '{$_alias}'" );
                                $all_ids    = self::$dbConnect->getFetch_Array( $ids_query );
                                $notToDo    = array( "edit","delete","reply","register","activation" );

                                if ( in_array( end( $pageInfo_array ), $notToDo ) ){
                                    _getRedirect( $_alias );
                                }
                            }
                        }
                        else {
                            if ( $_unset ){
                                unset( $do );
                            }
                        }

                        $pcqry = "SELECT categoriesid FROM pages WHERE publish = '1' AND status = '1' AND group_level >= '".getAllElements::setAccesslevel("gid")."' AND access_level >= '".getAllElements::setAccesslevel("ulevel")."' AND id = '{$all_ids['pid']}'";
                        $pcObject = self::$dbConnect->getObject( $pcqry );

                        if ( _getCheckNullorNot( $pcObject->categoriesid ) && $pcObject->categoriesid !=0 ){
                            $catqry = self::$dbConnect->getQuery( "SELECT * FROM categories WHERE publish = '1' AND status = '1' AND id = '{$pcObject->categoriesid}'" );
                            $cates  = self::$dbConnect->getFetch_Array( $catqry );
                            $all_ids    = array_merge( $all_ids, $cates );
                        }

                        if ( count( $ci_array ) > 1 ){
                            if ( count( $all_ids ) >= 1 && count( $page_array ) >= 1 ){
                                $all_ids    = array_merge( $all_ids, $page_array );
                                #$gvalue    = array_merge( $gvalue, $page_array );
                            }
                            if ( count( $addition_array ) >= 1 ){
                                $all_ids    = array_merge( $all_ids, $addition_array );
                                $gvalue = array_merge( $gvalue, $addition_array );
                            }
                        }

                        //To set redirect URL from previews page
                        if ( isset( $_SESSION['reffer'] ) && _getCheckNullorNot( $_SESSION['reffer'] ) && $_SESSION['guest'] == 1 ){
                            $reffer =  $_SESSION['reffer'];
                            unset( $_SESSION['reffer'] );
                        }
                        else {
                            $reffer =  $_alias.$xno;
                        }

                        if ( is_string( $do ) && _getCheckNullorNot( $do ) ){
                                self::$returnValue['metainfo']  = self::$MetaInfo->setMetInfos( $all_ids );
                                self::$returnValue['design']        = $this->setTemplate( $all_ids );
                                self::$returnValue['modules']   = self::$module->setApps( $all_ids );

                                $KenuLogin          = new getLogin( self::$basicURL );
                                $KenuArticles   = new getArticles( self::$basicURL );
                                switch ( $do ){
                                    //To display posting Area
                                    case edit:
                                    case post:
                                        if ( $gvalue['press'] === "doing" && _getCheckNullorNot( $gvalue['title'] ) && _getCheckNullorNot( $gvalue['article'] ) ){
                                                if ( md5( $gvalue['captcha'] ).'a4xn' === $gvalue['verifiedid'] ){
                                                    setcookie( 'tntcon','' );
                                                    $gvalue['error_flag']                   = false;
                                                    self::$returnValue['contents']  = $KenuArticles->postProcess( $gvalue );
                                                }
                                                else {
                                                    echo "<script type=\"text/javascript\"> alert( 'Please Verificate Number!' ); window.history.go( -1 ); </script>\n";
                                                }
                                        }
                                        else {
                                            $pcontents = self::$dbConnect->getPageContent( $all_ids['pid'] );
                                            if ( $pcontents['sectionid'] != 0 ){
                                                self::$returnValue['contents']  = $KenuArticles->postForm( $all_ids, $reffer, $do );
                                            }
                                            else {
                                                _getRedirect( $reffer );
                                            }
                                        }
                                    break;

                                    case delete:
                                        $all_ids                                                                        = array_merge( $gvalue, $all_ids );
                                        self::$returnValue['contents']  = $KenuArticles->deleteProcess( $all_ids, $reffer, $do );
                                    break;

                                    case reply:
                                        self::$returnValue['contents'] = $do;
                                    break;

                                    case move:
                                        self::$returnValue['contents'] = $do;
                                    break;

                                    //To display authentication Area
                                    case register:
                                        $KenuRegister = new getRegister( self::$basicURL );
                                        if ( $gvalue['press'] === "doing" && _getCheckNullorNot( $gvalue['first_name'] ) && _getCheckNullorNot( $gvalue['last_name'] ) && _getCheckNullorNot( $gvalue['username'] ) && _getCheckNullorNot( $gvalue['password'] ) ){
                                            self::$returnValue['modules']   = self::$module->setApps( "all" );
                                            self::$returnValue['contents'] = $KenuRegister->setRegister( $gvalue );
                                        }
                                        else {
                                            $gvalue['option']                                           = "register";
                                            $gvalue['returnurl']                                    = self::$basicURL;
                                            self::$returnValue['modules']   = self::$module->setApps( "all" );
                                            self::$returnValue['contents']  = $KenuRegister->setHTML( $gvalue['option'] );
                                        }
                                    break;

                                    case minifyjs:
                                    //http://address.com/minifycss?tp=powua_general&fn=powua-kw-01
                                        $compressCSS = new getJSOptimize( $gvalue['tp'], $gvalue['fn']);
                                        $compressCSS->minifyJS();
                                    break;

                                    case minifycss:
                                    //http://address.com/minifycss?tp=powua_general&fn=powua-kw-01
                                        $compressCSS = new getCSSOptimize( $gvalue['tp'], $gvalue['fn']);
                                        $compressCSS->minifyCSS();
                                    break;

                                    case download:
                                        $kDownload  = new getDownload( self::$basicURL );
                                        $kDownload->setProcess( $_falias );
                                    break;

                                    case xml:
                                        $kxml   = new getXml( self::$basicURL );
                                        $kxml->setProcess( $_falias );
                                    break;

                                    case search:
                                        $searchq                                                        = new getSearch( self::$basicURL );
                                        self::$returnValue['modules']   = self::$module->setApps( "all" );
                                        self::$returnValue['contents']  = $searchq->setProcess( $gvalue['qkw'] );
                                    break;

                                    case activation:
                                        $KenuRegister                                           = new getRegister( self::$basicURL );
                                        self::$returnValue['modules']   = self::$module->setApps( "all" );
                                        self::$returnValue['contents']  = $KenuRegister->setActivation( $gvalue );
                                    break;

                                    case myaccount:
                                        //$KenuRegister                                             = new getRegister( self::$basicURL );
                                        //self::$returnValue['modules'] = self::$module->setApps( "all" );
                                    break;

                                    case login:
                                        if ( $gvalue['press'] === "doing" && _getCheckNullorNot( $gvalue['username'] ) && _getCheckNullorNot( $gvalue['password'] ) ){
                                            self::$returnValue['modules']   = self::$module->setApps( "all" );
                                            self::$returnValue['contents'] = $KenuLogin->setLogin( $gvalue );
                                        }
                                        else {
                                            $gvalue['option']                                   = "login";
                                            self::$returnValue['modules']   = self::$module->setApps( "all" );
                                            self::$returnValue['contents']  = $KenuLogin->setHTML( $gvalue['option'], $reffer );
                                        }
                                    break;

                                    case logout:
                                        self::$returnValue['modules']   = self::$module->setApps( "all" );
                                        self::$returnValue['contents'] = $KenuLogin->setLogout();
                                    break;

                                    //To display Page or Article
                                    default:
                                        $KenuContents                                       = new getContent( self::$basicURL );
                                        self::$returnValue['contents']  = $KenuContents->getContents( $all_ids );
                                    break;
                                }
                        }
                        else {
                            // Set a 400 (bad request) response code and exit.
                            http_response_code(400);
                            $comment_value = !_getCheckNullorNot( $all_ids['pid'] ) ? "SYSTEM-UI" : $all_ids['pid'];
                            self::$returnValue['modules']    = self::$module->setApps( "404" );
                            self::$returnValue['metainfo']   = self::$MetaInfo->setMetInfos( "404" );
                            self::$returnValue['contents']   = "\n<!--// Bof Contents //-->\n<section class=\"content\">\n<div class=\"container\">\n<div id=\"effect\">";
                            self::$returnValue['contents']  .= "\n".NOT_FIND_PAGES ."<a href=\"mailto:". CONFIG_SITE_EMAIL ."?subject=I am looking for a page in ". CONFIG_SITE_NAME .".\" title=\"Email to the site admin\">".  CONFIG_SITE_EMAIL . "</a>.<br />- ". $comment_value;
                            self::$returnValue['contents'] .= "\n</div>\n</div>\n</section>\n<!--// Eof Contents //-->\n";
                        }
                }
        }
        else {
            // Set a 400 (bad request) response code and exit.
            http_response_code(400);
            self::$returnValue['modules']   = self::$module->setApps( "404" );
            self::$returnValue['metainfo']  = self::$MetaInfo->setMetInfos( "404" );
            self::$returnValue['contents']  = "<!--// Bof Error //-->\n<section class=\"content\">\n<div class=\"container\">\n".getAllElements::setMessage(self::$error_messag)."\n</div>\n</section>\n<!--// Eof Error //-->\n";
        }
        unset( $all_ids );
        unset( $gvalue );

        return self::$returnValue;
    }

/** -------------------------------------------------------------------------
 * [00/00/2011]:: To
 *  ------------------------------------------------------------------------- */
    public function setTemplate( $pid ){
        $menuObject = self::$dbConnect->getAllContents( "menu", "pid = '".$pid['pid']."' AND publish = '1' AND status = '1'" );
        if ( self::$dbConnect->getTotalNumber( "menu_templates", "menuid = '".$menuObject['id']."' AND publish = '1' AND status = '1'" ) > 0 ){
            $menuTemplateObject = self::$dbConnect->getAllContents( "menu_templates", "menuid = '".$menuObject['id']."' AND publish = '1' AND status = '1'" );
            $setTemplate['template']    = $menuTemplateObject['template'];
        }
        else {
            $setTemplate['template'] = CONFIG_FRONT_TEMPLATE;
        }

        return $setTemplate;
    }
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!
