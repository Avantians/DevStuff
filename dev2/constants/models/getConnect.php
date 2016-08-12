<?php
namespace ElasticActs\Constants\models;

use ElasticActs\App\config\database;
use PDO;

/**
 * Class for Database connection.
 */
abstract class getConnect
{
    use database;

    private $DB = null;
    private $DB_HOST;
    private $DB_USERNAME;
    private $DB_PASSWORD;
    private $DB_DATABASE_NAME;

    private function getSetup()
    {
        $dbParams = (object)[];
        $dbParams->db = self::dbParams();

        //Assign the host name if passed in
        if ( strlen( trim( $dbParams->db['DB_SERVER'] ) ) > 0 ) {
            $this->setHost( $dbParams->db['DB_SERVER'] );
        }
        //Assign the user name if passed in
        if ( strlen( trim( $dbParams->db['DB_USERNAME'] ) ) > 0 ) {
            $this->setUsername( $dbParams->db['DB_USERNAME'] );
        }
        //Assign the password if passed in
        if ( strlen( trim( $dbParams->db['DB_PASSWORD'] ) ) > 0 ) {
            $this->setPassword( $dbParams->db['DB_PASSWORD'] );
        }
        //Assign the database name if passed in
        if ( strlen( trim( self::dbParams()['DB_NAME'] ) ) > 0 ) {
            $this->setDatabaseName( $dbParams->db['DB_NAME'] );
        }
    }

    /**
    * initialization HOST
    */
    private function setHost( $host )
    {
        $this->DB_HOST = $host;
    }

    /**
    * initialization USERNAME
    */
    private function setUsername( $user )
    {
        $this->DB_USERNAME = $user;
    }

    /**
    * initialization PASSWORD
    */
    private function setPassword( $password )
    {
        $this->DB_PASSWORD = $password;
    }

    /**
    * initialization DATABASE Name
    */
    private function setDatabaseName( $name )
    {
        $this->DB_DATABASE_NAME = $name;
    }

    /**
    * open Connection to Database
    */
    protected function getOpen()
    {
        getConnect::getSetup();

        //Check if the connection is not already set
        if ( isset( $this->DB ) && ! is_null( $this->DB ) ) {
            return;
        }

        //Make sure that the host, the username, the password, and the database name are set
        if ( ( !isset( $this->DB_HOST ) ) || ( strlen( $this->DB_HOST ) == 0 )
              || ( !isset( $this->DB_USERNAME ) ) || ( strlen( $this->DB_USERNAME ) == 0 )
              || ( !isset( $this->DB_PASSWORD ) ) || ( strlen( $this->DB_PASSWORD ) == 0 )
              || ( !isset( $this->DB_DATABASE_NAME ) ) || ( strlen( $this->DB_DATABASE_NAME ) == 0 )
           ) {
            throw new Exception( 'DATABASE VARIABLES HAVE NOT BEEN SET' );
        }

        try {
            $this->DB = new PDO( 'mysql:host='.$this->DB_HOST.';dbname='.$this->DB_DATABASE_NAME.'',
                                  $this->DB_USERNAME,
                                  $this->DB_PASSWORD,
                                  array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8 COLLATE utf8_general_ci") );
            $this->DB->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            // Force MySQL to use the UTF-8 character set. Also set the collation, if a certain one has been set;
            // otherwise, MySQL defaults to 'utf8_general_ci' for UTF-8.
            // $this->DB->exec('SET NAMES utf8 COLLATE utf8_general_ci');
        } catch(PDOException $e) {
            header('HTTP/1.1 503 Service Unavailable'. $e->getMessage());
        }

        return $this->DB;
    }

    /**
    * close Database connection
    */
    public function getClose()
    {
        if ( isset( $this->DB ) && ! is_null( $this->DB ) ) {
            $this->DB = null;
        }

        return $this->DB;
    }
}
