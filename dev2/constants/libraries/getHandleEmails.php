<?php
namespace ElasticActs\Constants\libraries;

use ElasticActs\App\config\definedConfig;

/**
 * Class handle emails
 */
class getHandleEmails
{
    use definedConfig;

    public static $actualEmail;
    private static $domainMatching;
    private static $nameMatching;

    /**
     * [__construct description]
     */
    public function __construct()
    {
        self::$domainMatching = "/^(([A-Za-z0-9!#$%&'*+\/=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/";
        self::$nameMatching   = "/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/";
    }

    /**
     * Email Address Validation funtion
     * @param  string  $email
     * @param  boolean $publicEmail
     * @param  boolean $dnsrrEmail
     * @return boolean
     */
    public static function checkEmail( $email, $publicEmail = false, $dnsrrEmail = false )
    {
        if ( $publicEmail == true ) {
            return self::isPublicEmail($email);
        }

        // Email invalid because wrong number of characters
        // in one section, or wrong number of @ symbols
        if( !preg_match("/^[^@]{1,64}@[^@]{1,255}$/", $email) ) {
            return false;
        }

        // Split email into sections to make life easier
        $email_array = explode("@", $email);
        $local_array = explode(".", $email_array[0]);
        for ( $i = 0; $i < sizeof( $local_array ); $i++ ) {
            if ( !preg_match( self::$domainMatching, $local_array[$i] ) ) {
                return false;
            }
        }

        // Check if domain is IP.
        // If not, it should be valid domain name
        if ( !preg_match("/^\[?[0-9\.]+\]?$/", $email_array[1]) ) {
            $domain_array = explode( ".", $email_array[1] );

            //Not enough parts to domain
            if ( sizeof($domain_array) < 2 ) {
                return false;
            }

            for ( $i = 0; $i < sizeof($domain_array); $i++ ) {
                if ( !preg_match(self::$nameMatching, $domain_array[$i]) ) {
                    return false;
                }
            }
        }

        if ( $dnsrrEmail == true ) {
            return self::checkDNSRR( $email );
        }

        return true;
    }

    /**
     * Checking email Domain, not to allow public email address
     * @param  string  $email
     * @return boolean
     */
    public static function isPublicEmail( $email )
    {
        $emailDomain = explode('@', $email);
        if ( in_array($emailDomain[1], self::freeEmails()) ) {
            return true;
        } else {
            $emailDomain_name = explode( '.', $emailDomain[1] );
            if ( in_array( $emailDomain_name[0], self::freeEmails() ) ) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Convert to special characters
     * @param  string $email
     * @return string
     */
    public static function convertToActualEmail( $email )
    {
        self::$actualEmail = str_replace("&Dagger;", ".", trim($email));
        self::$actualEmail = str_replace("&#64;", "@", trim(self::$actualEmail));

        return self::$actualEmail;
    }

    /**
     * Checking email with checkdnsrr function
     * @param  string $email
     * @return boolean
     */
    protected static function checkDNSRR( $email )
    {
        list($userName, $hostName) = explode("@", self::convertToActualEmail($email));
        if( !empty($hostName) ) {
            if ( checkdnsrr($hostName , "MX") ) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }
}
