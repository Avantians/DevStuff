<?php
/** -------------------------------------------------------------------------
* @package  ElasticActs CMS
* @author   Kenwoo - iweb@kenwoo.ca
* @license  http://creativecommons.org/licenses/by/4.0/ Creative Commons
*
* Codign Style Guide http://www.php-fig.org/psr/psr-2/
* Setting flag for parent file
* ------------------------------------------------------------------------ */
define( '_VALID_TAGS', 1 );

require_once __DIR__ . '/vendor/autoload.php';

use ElasticActs\App\http\getHttps;
use ElasticActs\App\http\getRoute;
use ElasticActs\Constants\libraries\getFrontAccess;
use ElasticActs\Constants\libraries\getHandleElements;

getHttps::getHttps();

use ElasticActs\App\initials\getInitial;

$initials = new getInitial();
$initials->setSessionHandling( SESSION_SAVE_PATH, CONFIG_COOKIE_DOMAIN );

$elements = new getHandleElements();
$frontAccess = new getFrontAccess();
// IP protected access
if ( CONFIG_PROTECT_IP_FRONT == true && ! $frontAccess->getAccessbyIP() ) {
    $elements::redirectURL( CONFIG_SITE_URL );
}
// Checking an end user access point
$frontAccess->getISmobile();

getRoute::getRoute();

$initials->flushBuff();
