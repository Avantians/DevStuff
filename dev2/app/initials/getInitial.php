<?php
namespace ElasticActs\App\initials;

use ElasticActs\Constants\models\getPDO;
/**
* Initialization of error log and Timezone
*/
class getInitial extends getInitialBase
{
    //private static $getDB_CONNECT;
    public function __construct()
    {
        getInitialBase::setInitialBase();
    }

	// Handle errors
	public function catchErrors( $type, $location )
	{
		// Setting the level of error reporting
        // Show all types but notices
		ini_set( 'display_errors', 1 );

		if ( $type == 'file' ) {
			ini_set( 'log_errors', true );
			ini_set( 'error_log', $location . 'dev_errors.txt' );
		}
		error_reporting( E_ALL ^ E_NOTICE );
	}

	//  SetUp Timezone with default_timezone
	public function setTimeZone( $zone )
	{
		// Setting  Default Timezone
		if ( date_default_timezone_get() !== $zone ) {
			if ( function_exists( 'date_default_timezone_set' ) ) {
				date_default_timezone_set( $zone );
			}
		}
	}

    // Handling _SESSION
    public function setSessionHandling( $sessionPath, $cookieDomain )
    {
        // PHPSESSID를 자동으로 넘기지 않음
        @ini_set( 'session.use_trans_sid', 0 );
        // 링크에 PHPSESSID가 따라다니는것을 무력화함
        @ini_set( 'url_rewriter.tags', '' );
        // 세션 캐쉬 보관시간 (분)
        @ini_set( 'session.cache_expire', 180 );
        // session data의 garbage collection 존재 기간을 지정 (초)
        @ini_set( 'session.gc_maxlifetime', 10800 );
        // session.gc_probability는 session.gc_divisor와 연계하여 gc(쓰레기 수거) 루틴의 시작 확률을 관리합니다.
        // 기본값은 1입니다. 자세한 내용은 session.gc_divisor를 참고
        @ini_set( 'session.gc_probability', 1 );
        // session.gc_divisor는 session.gc_probability와 결합하여
        // 각 세션 초기화 시에 gc(쓰레기 수거) 프로세스를 시작할 확률을 정의합니다.
        // 확률은 gc_probability/gc_divisor를 사용하여 계산합니다.
        // 즉, 1/100은 각 요청시에 GC 프로세스를 시작할 확률이 1%입니다.
        // session.gc_divisor의 기본값은 100입니다.
        @ini_set( 'session.gc_divisor', 100 );
        // To protect session cookie
        @ini_set( 'session.cookie_httponly', 1);
        @ini_set( 'session.cookie_secure', 1);

        session_save_path( $sessionPath );
        session_cache_limiter( 'no-cache, must-revalidate' );
        // 세션쿠키가 적용되는 위치 (특별한 경우가 없다면 일반적으로 홈디렉토리 루트경로인 / 를 설정합니다.)
        // session_set_cookie_params($expire, $path, $domain, $secure, httponly);
        session_set_cookie_params( 0, "/", $cookieDomain, isset($_SERVER["HTTPS"]), true );

        // but not all user allow cookies
        @ini_set( 'session.use_only_cookies', 'false' );
        // delete session/cookies when browser is closed
        @ini_set( 'session.cookie_lifetime', 0 );
        // warn but don't work with bug
        @ini_set( 'session.bug_compat_42', 'false' );
        @ini_set( 'session.bug_compat_warn', 'true' );
        // 세션이활성화될도메인
        @ini_set( 'session.cookie_domain', $cookieDomain );
        @session_start();
    }

    // Flush the output buffer and turn off output buffering
    public function flushBuff()
    {
        /**
        *  This will delete all of files under the /static/filemanager/thumbs/
        *  which are temporarily generated from UPLOAD/IMAGE FOLDER
        */
        if ( date('d') == 1 || date('d') == 15 || date('d') == 30 ){
            $folders = CONFIG_DCOCUMENT_ROOT . '/static/filemanager/thumbs/';
        }

        unset( $_SESSION['mg'] );
        // clearstatcache() function
        // to clear the information that PHP caches about a file
        clearstatcache();
        if ( ob_get_length() ){
            @ob_end_flush();
            @flush();
        }
        exit();
    }
}
