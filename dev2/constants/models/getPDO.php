<?php
namespace ElasticActs\Constants\models;

use ElasticActs\App\config\database;
use PDO;

/**
 * Class for Database connection.
 */
abstract class getPDO
{
    use database;

    private static $db = null;
    private static $db_HOST;
    private static $db_USERNAME;
    private static $db_PASSWORD;
    private static $db_DATABASE_NAME;

    private static function getPDO()
    {
        $dbParams = (object)[];
        $dbParams->db = self::dbParams();

        //Assign the host name if passed in
        if ( strlen( trim( $dbParams->db['DB_SERVER'] ) ) > 0 ) {
            getPDO::setHost( $dbParams->db['DB_SERVER'] );
        }
        //Assign the user name if passed in
        if ( strlen( trim( $dbParams->db['DB_USERNAME'] ) ) > 0 ) {
            getPDO::setUsername( $dbParams->db['DB_USERNAME'] );
        }
        //Assign the password if passed in
        if ( strlen( trim( $dbParams->db['DB_PASSWORD'] ) ) > 0 ) {
            getPDO::setPassword( $dbParams->db['DB_PASSWORD'] );
        }
        //Assign the database name if passed in
        if ( strlen( trim( self::dbParams()['DB_NAME'] ) ) > 0 ) {
            getPDO::setDatabaseName( $dbParams->db['DB_NAME'] );
        }
    }

    /**
    * initialization HOST
    */
    private static function setHost( $host )
    {
        getPDO::$db_HOST = $host;
    }

    /**
    * initialization USERNAME
    */
    private static function setUsername( $user )
    {
        getPDO::$db_USERNAME = $user;
    }

    /**
    * initialization PASSWORD
    */
    private static function setPassword( $password )
    {
        getPDO::$db_PASSWORD = $password;
    }

    /**
    * initialization DATABASE Name
    */
    private static function setDatabaseName( $name )
    {
        getPDO::$db_DATABASE_NAME = $name;
    }

    /**
    * open Connection to Database
    */
    protected static function getOpen()
    {
        getPDO::getPDO();

        //Check if the connection is not already set
        if ( isset( getPDO::$db ) && ! is_null( getPDO::$db ) ) {
            return;
        }

        //Make sure that the host, the username, the password, and the database name are set
        if ( ( !isset( getPDO::$db_HOST ) ) || ( strlen( getPDO::$db_HOST ) == 0 )
              || ( !isset( getPDO::$db_USERNAME ) ) || ( strlen( getPDO::$db_USERNAME ) == 0 )
              || ( !isset( getPDO::$db_PASSWORD ) ) || ( strlen( getPDO::$db_PASSWORD ) == 0 )
              || ( !isset( getPDO::$db_DATABASE_NAME ) ) || ( strlen( getPDO::$db_DATABASE_NAME ) == 0 )
           ) {
            throw new Exception( 'DATABASE VARIABLES HAVE NOT BEEN SET' );
        }

        try {
            getPDO::$db = new PDO( 'mysql:host='.getPDO::$db_HOST.';dbname='.getPDO::$db_DATABASE_NAME.'',
                                    getPDO::$db_USERNAME,
                                    getPDO::$db_PASSWORD,
                                    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8 COLLATE utf8_general_ci") );

            // We can now log any exceptions on Fatal error.
            getPDO::$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            // Disable emulation of prepared statements, use REAL prepared statements instead.
            getPDO::$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            // Force MySQL to use the UTF-8 character set. Also set the collation, if a certain one has been set;
            // otherwise, MySQL defaults to 'utf8_general_ci' for UTF-8.
            // getPDO::$db->exec('SET NAMES utf8 COLLATE utf8_general_ci');
        } catch(PDOException $e) {
            header('HTTP/1.1 503 Service Unavailable'. $e->getMessage());
        }

        return getPDO::$db;
    }

    /**
    * close Database connection
    */
    public static function getClose()
    {
        if ( isset( getPDO::$db ) && ! is_null( getPDO::$db ) ) {
            getPDO::$db = null;
        }
    }
}
