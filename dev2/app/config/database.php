<?php
namespace ElasticActs\App\config;

/**
 * Class to return values
 */
trait database
{
    /**
     * Getting allowed file extension
     * @return array
     */
    protected static function dbParams()
    {
        return [ 'DB_SERVER'   => 'localhost',   // This is normally set to localhost
                 'DB_USERNAME' => 'root',        // MySQL Username
                 'DB_PASSWORD' => 'root',        // MySQL Password
                 'DB_NAME'     => 'mosaicon_tcc' // MySQL Database name
                ];

    }
}
