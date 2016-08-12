<?php
namespace ElasticActs\Constants\libraries;

use ElasticActs\App\config\definedConfig;
/**
* Get Front Access method
*/
class getFrontAccess
{
    use definedConfig;
    protected $endUserIP;

    /**
    * Getting User IP ||  This function was from CodeIgniter
    * @link     http://codeigniter.com
    */
    public function __construct()
    {
        $cip = ( isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'] != "") ? $_SERVER['HTTP_CLIENT_IP'] : false;
        $rip = ( isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] != "") ? $_SERVER['REMOTE_ADDR'] : false;
        $fip = ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != "") ? $_SERVER['HTTP_X_FORWARDED_FOR'] : false;

        if ( $cip && $rip ) {
            $userIP = $cip;
        } elseif ( $rip ) {
            $userIP = $rip;
        } elseif ( $cip ) {
            $userIP = $cip;
        } elseif ( $fip ) {
            $userIP = $fip;
        }

        if ( strstr($userIP, ',') ) {
            $x = explode(',', $userIP);
            $userIP = end($x);
        }

        if ( ! preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $userIP) ) {
            $userIP = '0.0.0.0';
        }

        unset($cip);
        unset($rip);
        unset($fip);

        $this->endUserIP = $userIP;
    }

    /**
    * Check IP address to allow access
    * @return boolean
    */
    public function getAccessbyIP()
    {
        $kw = 0;
        $ip_count = 0;
        while( $kw < count(self::allowedIPs()) ) {
            if ( preg_match( "/". self::allowedIPs()[$kw]."/", $this->endUserIP ) ) {
                $ip_count++;
            }
            $kw++;
        }

        if ( $ip_count <= 0 ) {
            return false;
        } else {
            return true;
        }
    }

    /**
    * Getting tht browser is for mobile
    * @return boolean
    */
    public function getISmobile(){
        static $is_mobile;

        if ( isset($is_mobile) ) {
            return $is_mobile;
        }

        if ( empty($_SERVER['HTTP_USER_AGENT']) ) {
            $is_mobile = false;
        } elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false // many mobile devices ( all iPhone, iPad, etc. )
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false ) {

            $is_mobile = true;
        } else {
            $is_mobile = false;
        }

        return $is_mobile;
    }
}
