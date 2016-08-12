<?php
namespace ElasticActs\App\http;

use ElasticActs\App\http\getContentType;

/**
* Get Front Access method
*/
class getHttps
{
    /**
    * Get
    */
    public static function getHttps()
    {
        $path = preg_replace('/\W\w+\s*(\W*)$/', '$1', realpath(dirname(__FILE__)));
        // Requrie once globals & constatns files
        if ( file_exists( $path . '/config/config.php' ) ) {
            require_once $path . '/config/config.php';
        } else {
            throw new Exception ('There is no main file.');
        }

        new getContentType( $fileName, $extension );
    }
}
