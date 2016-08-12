<?php
namespace ElasticActs\App\http;

use ElasticActs\Constants\libraries\getAccessPoint;
use ElasticActs\Constants\models\getStmt;

/**
* Get Front Access method
*/
class getRoute extends getAccessPoint
{
    private static $loading = [];

    public static function getRoute()
    {
        // To check the existing session if there is one and update session live time
        // if ( isset( $_SESSION ) && $_SESSION['session_user_id'] && $_SESSION['guest'] == 1 ){
        //     _getCheckingSession();
        // }
        getAccessPoint::getAccessPoint();
        getRoute::$loading = getAccessPoint::accessPoint();

        new getStmt();
        getStmt::getStatment( getRoute::$loading );

        $do     = getRoute::$loading['do'];
        $uri    = getRoute::$loading['uri'];
        $ino    = getRoute::$loading['ino'];
        $pno    = getRoute::$loading['pno'];
        $action = getRoute::$loading['action'];
        $method = getRoute::$loading['method'];
        $gvalue = getRoute::$loading['gvalue'];

    }
}
