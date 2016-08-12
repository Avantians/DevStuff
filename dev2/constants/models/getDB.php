<?php
namespace ElasticActs\Constants\models;

use ElasticActs\Constants\models\getConnect;
use PDO;
/**
 * Class for Database connection.
 */
class getDB extends getConnect
{
    public static $stmt;
    private static $connect;

    public function __construct()
    {
        getDB::$connect = getConnect::getOpen();
    }

    public static function getQuery( $db_query )
    {
        //it takes away the threat of SQL Injection
        getDB::$stmt = getDB::$connect->prepare( $db_query );
    }

    /**
     * Bind the inputs with the placeholders
     * $param placehoder like :name
     * $valu actual value
     * $type data type
     */
    public static function getBind( $param, $value )
    {
        if ( is_int($value) ) {
            $type = PDO::PARAM_INT;
        } elseif ( is_bool($value) ) {
            $type = PDO::PARAM_BOOL;
        } elseif ( is_null($value) ) {
            $type = PDO::PARAM_NULL;
        } else {
            $type = PDO::PARAM_STR;
        }

        getDB::$stmt->bindValue( ':'.$param, $value, $type );
    }

    public static function getExecute()
    {
        getDB::$stmt->execute();
    }

    //returns an array of the result set rows
    public static function getExcuteFetchAll( $value = null )
    {
        getDB::getExecute();
        $type = $value === 'OBJ' ? PDO::FETCH_OBJ : PDO::FETCH_ASSOC;

        $result = getDB::$stmt->fetchAll( $type );
        getDB::$stmt->closeCursor();

        return $result;
    }

    //returns a single record from the database
    public static function getExcuteFetch(  $value = null )
    {
        getDB::getExecute();
        $type = $value === 'OBJ' ? PDO::FETCH_OBJ : PDO::FETCH_ASSOC;

        $result = getDB::$stmt->fetch( $type );
        getDB::$stmt->closeCursor();

        return $result;
    }

    /**
     *  Returns the value of one single field/column
     *
     *  @param  string $query
     *  @param  array  $params
     *  @return string
     */
    public static function getSingle()
    {
        getDB::getExecute();
        $result = getDB::$stmt->fetchColumn();
        // Frees up the connection to the server so that other SQL statements may be issued
        getDB::$stmt->closeCursor();

        return $result;
    }

    // returns the number of effected rows from the previous delete, update or insert statement
    public static function getRowCount()
    {
        return getDB::$stmt->rowCount();
    }

    // returns the last inserted Id as a string
    public static function getLastInsertId()
    {
        return getDB::$stmt->lastInsertId();
    }

    // Truncate table
    public static function truncateTB( $table )
    {
        exec( "TRUNCATE TABLE $table" );
    }
}
