<?php
namespace ElasticActs\Constants\libraries;

/**
 * Class handle Images
 */
class getHandleSessionAndCookies
{
    private static $cookieDomain;

    public function __construct( $domain )
    {
        static::$cookieDomain = $domain;
    }

    /**
     * Checking Session
     */
    public function checkSession( $base_url = null )
    {

            $session_life_admin = CONFIG_LIFETIME_ADMIN; //30mins in second
            $logintime          = $_SESSION['session_time'];
            $session_id         = $_SESSION['session_id'];
            $session_life       = time() - $logintime;

            if ( $Bon_db->getTotalNumber( "members_session", "session_id != ''" ) > 0 ){
                $t_time     = time() - $session_life_admin;
                $dquery = "DELETE FROM members_session WHERE time < '". $t_time ."'";
                $Bon_db->getQuery( $dquery );
            }

            if ( CONFIG_CHECK_IP_STATUS_FOR_SESSION === "true" ){
                if ( $_ipno != $_SESSION['ipno'] ){
                    $extre = $_SESSION['section'] === "b" ? " AND section = 'b'" : "" ;

                    $query = "DELETE FROM members_session WHERE session_id = '". $session_id ."'". $extre. " AND userid = '".( int )$_SESSION['session_user_id'] ."'";
                    $Bon_db->getQuery( $query );
                    session_destroy();
                    clearstatcache();

                    echo "<script type=\"text/javascript\"> alert( 'Your IP address has been changed. Please login again.' ); window.location.reload( '". $base_url ."' ); </script>\n";
                    exit();
                }
            }

            $members    = $Bon_db->getMemberInfo( ( int )$_SESSION['session_user_id'], "members_email = '". $_SESSION['session_username']  ."'" );

            if ( $session_life < $session_life_admin ){
                #Calculation of past date
                $past = date( 'Y-m-d H:i:s', time() - 7 * 60 * 60 * 24 );
                if ( $session_id == md5( $members['id'] . $members['members_email'] . $members['members_type'] . $logintime ) ){

                    if ( $Bon_db->getTotalNumber( "members_session", "session_id = '". $session_id ."'" ) == 0 ){
                        session_start();
                        unset($_SESSION);
                        session_destroy();

                        echo "<script type=\"text/javascript\">parent.location=\"". $base_url ."\"; </script>\n";
                    }
                    else {
                        $current_time   = time();
                        $current_session_id  = md5( $members['id'] . $members['members_email'] . $members['members_type'] . $current_time );
                        $query = "UPDATE members_session SET time = ". $current_time . ", session_id = '". $current_session_id ."' WHERE session_id = '". $session_id ."'";
                    }

                    if( $Bon_db->getQuery( $query ) ){
                        $_SESSION['session_id']         = $current_session_id;
                        $_SESSION['session_time']   = $current_time;
                    }
                    else{
                        $query = "DELETE FROM members_session WHERE session_id = '". $session_id ."'";
                        $Bon_db->getQuery( $query );
                        unset($_SESSION);
                        session_destroy();
                        echo "<script type=\"text/javascript\"> alert( 'Please try to login again.' ); window.location.reload( '". $base_url ."' ); </script>\n";
                        exit();
                    }
                }
                else {
                    $query = "DELETE FROM members_session WHERE session_id = '". $session_id ."'";
                    $Bon_db->getQuery( $query );
                    unset($_SESSION);
                    session_destroy();
                    echo "<script type=\"text/javascript\"> alert( 'Your seesion ID was not assigned.' ); window.location.reload( '". $base_url ."' ); </script>\n";
                    exit();
                }
            }
            else {
                $query = "DELETE FROM members_session WHERE session_id = '". $session_id ."' AND userid = '".( int )$_SESSION['session_user_id'] ."'";
                $Bon_db->getQuery( $query );
                unset($_SESSION);
                session_destroy();
                clearstatcache();

                echo "<script type=\"text/javascript\"> alert( 'Your seesion has expired. Please reconnect.' ); window.location.reload( '". $base_url ."' ); </script>\n";
                exit();
            }
    }

    /**
     * Create Cookie
     */
    public function set_cookie( $cookieName, $value, $expire )
    {
        setcookie( md5( $cookieName ), base64_encode( $value ), time() + $expire, '/', static::$cookieDomain );
    }

    /**
     * Delete Cookie
     */
    public function unset_cookie( $cookieName, $expire )
    {
        setcookie( md5( $cookieName ), "", time() - $expire, '/', static::$cookieDomain );
    }

    /**
     * get Cookie
     */
    public function get_cookie( $cookieName )
    {
        return base64_decode( $_COOKIE[md5( $cookieName )] );
    }

}
