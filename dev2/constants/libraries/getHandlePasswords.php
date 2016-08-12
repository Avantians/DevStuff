<?php
namespace ElasticActs\Constants\libraries;

use ElasticActs\Constants\libraries\getHandleElements;
/**
 * Class handle passwords
 */
class getHandlePasswords
{
    private static $pwSault;
    private static $checkingMatching;
    private static $makePW;
    private static $encryptedPW;
    private static $elements;

    public function __construct()
    {
        self::$pwSault          = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        self::$checkingMatching = '#.*^(?=.{7,12})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$#';
        self::$makePW           = '';
        self::$encryptedPW      = '';
        self::$elements         = new getHandleElements();
    }

    // Validates a plain text password with an encrpyted password
    private static function validatePassword( $plain, $encrypted )
    {
        if ( self::$elements->checkNullValue( $plain ) && self::$elements->checkNullValue( $encrypted ) ){
            // split apart the hash / salt
            $stack = explode( ':', $encrypted );
            if( sizeof( $stack ) != 2 ){
                return false;
            }
            if ( md5( $stack[1] . $plain ) == $stack[0] ){
                return true;
            }
        }

        return false;
    }

    // Checking password - 7 to 12  /  at least one CAPS / one letter / one number
    private static function checkPassword( $password )
    {
        if ( !preg_match( self::$checkingMatching, $password ) ){
            return false;
        } else {
            return true;
        }
    }

    // Encrypting password
    private static function encryptPassword( $plain )
    {
        for ( $i=0; $i<10; $i++ ){
            self::$encryptedPW .= self::randNum();
        }

        $salt = substr( md5( self::$encryptedPW ), 0, 2 );
        self::$encryptedPW = md5( $salt . $plain ) . ':' . $salt;

        return self::$encryptedPW;
    }

    // Making 12 length of password
    public static function makePassword( $length = 12 )
    {
        mt_srand( 10000000*( double )microtime() );
        for ( $i = 0; $i < $length; $i++ ){
            self::$makePW .= self::$pwSault[mt_rand( 0,61 )];
        }

        return self::$makePW;
    }

    // Generate random numbers
    public static function randNum( $min = null, $max = null )
    {
        static $seeded;

        if ( !isset( $seeded ) ){
            mt_srand( ( double )microtime()*1000000 );
            $seeded = true;
        }

        if ( isset( $min ) && isset( $max ) ){
            if ( $min >= $max ){
                return $min;
            } else {
                return mt_rand( $min, $max );
            }
        } else {
            return mt_rand();
        }
    }
}
