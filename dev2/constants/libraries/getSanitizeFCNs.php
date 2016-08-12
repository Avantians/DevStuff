<?php
namespace ElasticActs\Constants\libraries;

/**
 * Sanitizing functions
 */
abstract class getSanitizeFCNs
{
    const InChar  = array( '@&( – );@i', '/\®/', '/\©/', '/\™/', '/\@/', '/\"/', '/\=/', '/\?/', '/\!/' );
    const OutChar = array( '-', '&#174;', '&#169;', '&trade;', '&#64;', '&quot;', '&#61;', '&#63;', '&#33;' );

    /**
     * Convert some special characters
     */
    protected function getConvertHTML( $string, $type )
    {
        // $in[] = '@\s[\s]+@i'; $out[] = " ";
        return preg_replace( InChar, OutChar, trim( $string ) );
    }

    /**
     * Strip multi-line comments including CDATA
     */
    protected function getCleanUpComments( $string )
    {
        return preg_replace( '@<![\s\S]*?--[ \t\n\r]*>@i', '', $string );
    }

    /**
     * Strip out Javascript
     */
    protected function getCleanUpJS( $string )
    {
        return preg_replace( '@<script[^>]*?>.*?</script>@si', '', $string );
    }

    /**
     * Strip Multi-line comments
     */
    protected function getCleanUpURL( $string )
    {
        return preg_replace( '/[^A-Za-z0-9\\#\\&\\=\\/\\-\\_\\.]/', '', $string );
    }

    /**
     * Strip Multi-line comments
     */
    protected function getCleanUpHTML( $string )
    {
        return preg_replace( '@<[\/\!]*?[^<>]*?>@si', '', $string );
    }

    /**
     * Strip style tags properly
     */
    protected function getCleanUpStyle( $string )
    {
        return preg_replace( '@<style[^>]*?>.*?</style>@siU', '', $string );
    }

    /**
     * Convert speical characters to HTML Tags
     * to keep HTML Tag
     */
    protected function getKeepHtml( $string )
    {
        $string = str_replace( array( "&lt;", "&gt;" ), array( "<", ">" ), htmlentities( $string, ENT_NOQUOTES, "UTF-8" ) );

        if ( strpos( $string, '&amp;#' ) !== false ){
            $string = preg_replace( '/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string );
        }
        $string = preg_replace( '/&amp;/', "&", $string );

        return $string;
    }

    /**
     * Convert Hthml to Text
     * Remove Multi lin tags
     */
    protected function getHtml2txt( $string )
    {
        $string = $this->getCleanUpStyle( $string );    // Strip style
        $string = $this->getCleanUpComments( $string ); // Strip multi-line comments including CDATA
        $string = $this->getCleanUpJS( $string );       // Strip out javascript
        $text = $this->getCleanUpHTML( $string );       // Strip out HTML tags

        return $text;
    }

    /**
    * http://svn.bitflux.ch/repos/public/popoon/trunk/classes/externalinput.php
    * Author: Christian Stocker <christian.stocker@liip.ch>
    */
    protected function getBasicCleanUp( $string )
    {
        if ( get_magic_quotes_gpc() ){
            $string = stripslashes( $string );
        }

        $string = str_replace( array( '&amp;', '&lt;', '&gt;' ), array( '&amp;amp;', '&amp;lt;', '&amp;gt;' ), $string );
        // Fix &entitiy\n;
        $string = preg_replace( '#(&\#*\w+)[\x00-\x20]+;#u', "$1;", $string );
        $string = preg_replace( '#(&\#x*)([0-9A-F]+);*#iu', "$1$2;", $string );
        $string = html_entity_decode( $string, ENT_COMPAT, "UTF-8" );

        // Remove any attribute starting with "on" or xmlns
        $string = preg_replace( '#( <[^>]+[\x00-\x20\"\'\/] )( on|xmlns )[^>]*>#iUu', "$1>", $string );

        // Remove javascript: and vbscript: protocol
        $string = preg_replace( '#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2nojavascript...', $string );
        $string = preg_replace( '#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2novbscript...', $string );
        $string = preg_replace( '#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/]*-moz-binding[\x00-\x20]*:#Uu', '$1=$2nomozbinding...', $string );
        $string = preg_replace( '#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/]*data[\x00-\x20]*:#Uu', '$1=$2nodata...', $string );
        // Remove any style attributes, IE allows too much stupid things in them, eg.
        // <span style="width: expression( alert( 'Ping!' ) );"></span>
        // and in general you really don't want style declarations in your UGC
        $string = preg_replace( '#(<[^>]+[\x00-\x20\"\'\/])style[^>]*>#iUu', "$1>", $string );
        // Remove namespaced elements ( we do not need them... )
        $string = preg_replace( '#</*\w+:\w[^>]*>#i', "", $string );
        // Remove really unwanted tags
        do {
            $oldstring = $string;
            $string = preg_replace( '#</*(applet|meta|xml|blink|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>#i', "", $string );
        } while ( $oldstring != $string );

        return $string;
    }
}
