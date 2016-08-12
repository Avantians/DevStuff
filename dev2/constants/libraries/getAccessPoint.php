<?php
namespace ElasticActs\Constants\libraries;

use ElasticActs\Constants\libraries\getSanitize;

/**
* Get Front Access method
*/
class getAccessPoint
{
    private static $uriValue;
    private static $gvalue = array();
    private static $sanitizingObj;

    const basicAction = array( 'post', 'update', 'edit', 'delete', 'reply', 'register', 'activation', 'forgotpw', 'login', 'logout' );
    const specialAction = array( 'download', 'search', 'api', 'xml', 'json', 'minifyhtml', 'minifycss', 'minifyjs'  );
    const xlist = array( "username", "password" );

    public static function getAccessPoint()
    {
        getAccessPoint::$sanitizingObj = new getSanitize();
        // Parse needed information from PATH_INFO or REQUEST_URI
        if ( ! empty($_SERVER['PATH_INFO']) && isset($_SERVER['PATH_INFO']) ) {
            getAccessPoint::$uriValue = getenv('PATH_INFO');
        } else {
            if ( ! empty($_SERVER['REQUEST_URI']) && isset($_SERVER['REQUEST_URI']) ) {
                if ( strpos(getenv('REQUEST_URI'), '?') > 0 ) {
                    getAccessPoint::$uriValue = strstr(getenv('REQUEST_URI'), '?', true);
                } else {
                    getAccessPoint::$uriValue = getenv('REQUEST_URI');
                }
            } else {
                getAccessPoint::$uriValue = getenv('REDIRECT_CI_PATH');
            }
        }
    }

    /**
    * Get URI or CI path in array with actions
    * @return array
    */
    public static function accessPoint()
    {
        if ( getAccessPoint::$uriValue == '/' ) {
            $justURI = ['uri' => '/'];
        } else {
            $justURI = getAccessPoint::getJustURI(getAccessPoint::$uriValue);
        }

        return ['uri'    => getAccessPoint::$sanitizingObj->getSanitizing($justURI['uri'], 'uri'),
                'ino'    => $justURI['item_no'],
                'pno'    => $justURI['page_no'],
                'do'     => $justURI['basic_act'],
                'action' => $justURI['special_act'],
                'method' => getenv( 'REQUEST_METHOD' ),
                'gvalue' => getAccessPoint::setGvalue()
                ];

    }

    /**
     * Do not allow to use global $_POST and $_GET
     * Sanitizing values and convert to gvalue
     * @return array
     */
    private static function setGvalue()
    {
        if ( isset($_POST) && !empty($_POST) ) {
            foreach ( $_POST as $key => $kw ) {
                getAccessPoint::$gvalue[strtolower($key)] = in_array(strtolower($key), getAccessPoint::xlist) ? getAccessPoint::$sanitizingObj->getSanitizing($kw) : getAccessPoint::$sanitizingObj->getSanitizing($kw, "simple");
            }
            unset( $_POST );
        }

        if ( isset($_GET) &&  !empty($_GET) ) {
            foreach ( $_GET as $key => $kw ) {
                getAccessPoint::$gvalue[strtolower($key)] = getAccessPoint::$sanitizingObj->getSanitizing($kw, 'uri');
            }
            unset($_GET);
        }

        return getAccessPoint::$gvalue;
    }

    /**
     * @param  string $uValue
     * @return array
     */
    private static function getJustURI( $uValue )
    {
        $_requestUri = strtr($uValue, "\\", "/");
        // Remove  "/" from the front
        $_requestUri = preg_replace('/^\/+/', '', $_requestUri);
        // Remove  "/" from the tail
        $_requestUri = preg_replace('/\/$/', '', $_requestUri);

        // Get Page number and remove it from URI
        $uri_array = explode('&', $_requestUri);
        if ( count($uri_array) > 1 ) {
            $onlyURI['page_no'] = end($uri_array);
        }
        if ( !empty($onlyURI['page_no']) ) {
            $_requestUri = str_replace('&'.end($uri_array), '', $_requestUri);
        }

        // Make URI into array
        $uriArray   = explode("/", $_requestUri);
        $allActions = array_merge(getAccessPoint::basicAction, getAccessPoint::specialAction);

        // Chekcing any actions been contained
        if ( array_intersect($uriArray, $allActions) ) {
            // Checking special action in requestUri.
            // If so, set specia action from the first of array and remvoe it from requestUri
            if  ( in_array(reset($uriArray), getAccessPoint::specialAction) ){
                $onlyURI['special_act'] = reset($uriArray);
                $onlyURI['uri'] = preg_replace('/^\/+/', '', str_replace(getAccessPoint::specialAction, '', $_requestUri));
            }

            // Checking basic action in requestUri.
            // If so, set basic action from the end of array and remvoe it from requestUri
            if ( in_array(end($uriArray), getAccessPoint::basicAction) ) {
                $onlyURI['basic_act'] = end($uriArray);
                // If special action been set above, than remove it for requestUri
                if ( ! empty($onlyURI['basic_act']) ) {
                    $_requestUri = preg_replace('/^\/+/', '', str_replace($onlyURI['special_act'], '', $_requestUri));
                }
                $onlyURI['uri'] = preg_replace('/\/$/', '', str_replace(getAccessPoint::basicAction, '', $_requestUri));
            }
        } else {
            $onlyURI['uri'] = $_requestUri;
        }

        // Get Item number and remove it from URI
        $page_array = explode("/", $onlyURI['uri']);
        if ( is_numeric(end($page_array)) ) {
            $onlyURI['item_no'] = end($page_array);
            $onlyURI['uri'] = str_replace('/'.end($page_array), '', $onlyURI['uri']);
        }

        return $onlyURI;
    }
}
