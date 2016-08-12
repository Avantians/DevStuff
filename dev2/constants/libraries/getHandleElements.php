<?php
namespace ElasticActs\Constants\libraries;

/**
 * Class handle other Elements
 */
class getHandleElements
{
    // public function __construct()
    // {

    // }

    /**
     * nl2br() prior PHP 4.2.0 did not convert linefeeds on all OSs
     * ( it only converted \n )
     */
    // public function convertLinefeeds( $from, $to, $string )
    // {
    //     if ( ( PHP_VERSION < "4.0.5" ) && is_array( $from ) ){
    //         return preg_replace( '/( ' . implode( '|', $from ) . ' )/', $to, $string );
    //     } else {
    //         return str_replace( $from, $to, $string );
    //     }
    // }

    /**
     * Checking the value is null or not
     */
    public static function checkNullValue( $value )
    {
        if ( is_array( $value ) ){
            if ( sizeof( $value ) > 0 ){
                return true;
            } else {
                return false;
            }
        } else {
            if ( ( is_string( $value ) || is_int( $value ) ) && ( $value != '' ) && ( $value != 'NULL' ) && ( strlen( trim( $value ) ) > 0 ) ){
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Redirect to another page or site
     * @param string $url
     */
    public static function redirectURL( $url )
    {
        if ( ( CONFIG_ENABLE_SSL == true ) && ( getenv( 'HTTPS' ) == 'on' ) ){          // We are loading an SSL page
            if ( substr( $url, 0, strlen( CONFIG_SITE_URL ) ) == CONFIG_SITE_URL ){     // NONSSL url
                $url = CONFIG_SITE_URL_SSL . substr( $url, strlen( CONFIG_SITE_URL ) ); // Change it to SSL
            }
        }

        if ( headers_sent() ){
            echo "<script>document.location.href='". $url ."';</script>";
        } else {
            @ob_end_clean();    //Clear output buffer
            header( 'HTTP/1.1 301 Moved Permanently' );
            header( "Location: ". $url );
        }
    }

    /**
     * To cut string by certain length
     */
    public static function mbStrCut( $str, $start = 0, $length )
    {
        // Strip HTML and PHP tags from a string
        $string = trim( strip_tags( $str ) );
        // Replace <p> with <br> tag
        $string = str_replace( array( "<p>", "</p>" ), array( "<br>", "<br>" ),  $string );
        // Remove first <br> tag
        $string = preg_replace('/^<br\s?\>/', '', $string);

       if ( mb_strlen( $string,'UTF-8' ) > $length ){
            // Checking Korean with special
            $languageCheck = "/^[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}가-힣0-9\s]+$/";
            if ( preg_match($languageCheck, $string) ) {
                $length = 25;
            }

           /* mb_substr  PHP 4.0 이상, iconv_substr PHP 5.0 이상 */
           $cutted_str  = mb_substr( $string, $start, $length,'UTF-8') . "..";
        } else {
            $cutted_str = $string;
        }

        return $cutted_str;
    }

    /**
     * Required Design files
     */
    public static function allViews( $design_directory )
    {
        $directory_array    = array();
        if( $dir = @dir( $design_directory ) ){
            while( $file = $dir->read() ){
                if ( !is_dir( $design_directory . $file ) && ( !preg_match("/^[_]/", $file) ) && ( $file != "index.php" && $file != "." && $file != ".." ) ){
                        $directory_array[] = array( 'id' => $file, 'text' => strtoupper( $file ) );
                }
            }
            sort( $directory_array );
            $dir->close();
        }

        return $directory_array;
    }

    /**
     * $eStrings is array
     */
    public static function errorOutput( $eStrings )
    {
        if ( count( $eStrings ) > 0 ){
            foreach ( $eStrings as $key => $kw ){
                $va          = strtolower( $key );
                $output[$va] = _getSanitize( $kw );
            }
            unset( $eStrings );

            $i = 1;
            foreach ( $output as $keys => $val ){
                $extra_tail   = $i == count( $output ) ? '<br/>' : '';
                $errorOutput .= '<strong>'. strtoupper( $keys ) .':</strong>'. $val . $extra_tail;
                $i++;
            }

            $errorOutput = "<div>". $errorOutput ."</div>";
        }

        return $errorOutput;
    }
}
