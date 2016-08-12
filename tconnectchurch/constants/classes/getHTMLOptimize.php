<?php
/** -------------------------------------------------------------------------
 * This program is Open Source; you can redistribute it
 * and/or modify it under the terms of the GNU General Public License
 * This program is coded in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY
 * @package  CMS
 * @author      Kenwoo - iweb@kenwoo.ca
 * @license    http://creativecommons.org/licenses/by/4.0/ Creative Commons
 *
 * This  class was modified from the original class Minify_HTML that was
 * written by Stephen Clay <steve@mrclay.org>
 * @original author Samuel Marshall
 *
 * [v02-02/25/2015]:: Set flag, not allow to direct access
 *  ------------------------------------------------------------------------- */
defined( "_VALID_MOS" ) or die( "Your system is not working properly." );

class getHTMLOptimize{

        public function __construct($options = array()){
                //$this->_html = file_get_contents($html);

                if (isset($options['xhtml'])){
                        $this->_isXhtml = (bool) $options['xhtml'];
                }

                if (isset($options['html5'])){
                        $this->_isHtml5 = (bool) $options['html5'];
                }
        }

				public function minifyHTML( $html ){

                if ($this->_isXhtml === null){
                        $this->_isXhtml = (preg_match('#^\s*+<!DOCTYPE[^X>]++XHTML#i', $html));
                }

                if ($this->_isHtml5 === null){
                        $this->_isHtml5 = (preg_match('#^\s*+<!DOCTYPE html>#i', $html));
                }

                //Remove comments (not containing IE conditional comments)
                $html = preg_replace(
                        '#(?>(?:<(?!!))?[^<]*+(?:<(?:script|style)\b[^>]*+>(?><?[^<]*+)*?<\/(?:script|style)>|<!--\[(?><?[^<]*+)*?'
                        . '<!\s*\[(?>-?[^-]*+)*?--!?>|<!DOCTYPE[^>]++>)?)*?\K(?:<!--(?>-?[^-]*+)*?--!?>|[^<]*+\K$)#i', '', $html);

                //Reduce runs of whitespace outside all elements to one
                $html = preg_replace(
                        '#(?>[^<]*+(?:<script\b[^>]*+>(?><?[^<]*+)*?</script>|<(?>[^>\'"]*+(?:"[^"]*+"|\'[^\']*+\')?)*?>)?)*?\K'
                        . '(?:[\t\f ]++(?=[\r\n]\s*+<)|(?>\r?\n|\r)\K\s++(?=<)|[\t\f]++(?=[ ]\s*+<)|[\t\f]\K\s*+(?=<)|[ ]\K\s*+(?=<)|$)#', '',
                        $html
                );

                //Replace line feed with space (legacy)
                $html = preg_replace(
                        '#(?>[^<]*+(?:<script\b[^>]*+>(?><?[^<]*+)*?</script>|<(?>[^>\'"]*+(?:"[^"]*+"|\'[^\']*+\')?)*?>)?)*?\K'
                        . '(?:[\r\n\t\f]++(?=<)|$)#', ' ', $html
                );

                // remove ws around block elements preserving space around inline elements
                //block/undisplayed elements
                $b  = 'address|article|aside|audio|body|blockquote|canvas|dd|div|dl'
                        . '|fieldset|figcaption|figure|footer|form|h[1-6]|head|header|hgroup|html|noscript|ol|output|p'
                        . '|pre|section|style|table|title|tfoot|ul|video';

                //self closing block/undisplayed elements
                $b2 = 'base|meta|link|hr';

                //inline elements
                $i = 'b|big|i|small|tt'
                        . '|abbr|acronym|cite|code|dfn|em|kbd|strong|samp|var'
                        . '|a|bdo|br|map|object|q|script|span|sub|sup'
                        . '|button|label|select|textarea';

                //self closing inline elements
                $i2 = 'img|input';

                $html = preg_replace(
                        "#(?>\s*+(?:<(?:(?>$i)\b[^>]*+>|(?:/(?>$i)\b>|(?>$i2)\b[^>]*+>)\s*+)|<[^>]*+>)|[^<]++)*?\K"
                        . "(?:\s++(?=<(?>$b|$b2)\b)|(?:</(?>$b)\b>|<(?>$b2)\b[^>]*+>)\K\s++(?!<(?>$i|$i2)\b)|$)#i", '', $html
                );

                //Replace runs of whitespace inside elements with single space escaping pre, textarea, scripts and style elements
                //elements to escape
                $e           = 'pre|script|style|textarea';

                //Regex for escape elements
                $p  = "<pre\b[^>]*+>(?><?[^<]*+)*?</pre>";
                $sc = "<script\b[^>]*+>(?><?[^<]*+)*?</script>";
                $st = "<style\b[^>]*+>(?><?[^<]*+)*?</style>";
                $t  = "<textarea\b[^>]*+>(?><?[^<]*+)*?</textarea>";

                $html = preg_replace(
                        "#(?>[^<]*+(?:$p|$sc|$st|$t|<[^>]++>[^<]*+))*?(?:(?:<(?!$e)[^>]*+>)?(?>\s?[^\s<]*+)*?\K\s{2,}|\K$)#i", ' ', $html
                );

                //Remove additional ws around attributes
                $html = preg_replace(
                        '#(?><?[^<]*+)*?(?:<[a-z0-9]++\K\s++|\G[^\>=]++=(?(?=\s*+["\'])\s*+["\'][^"\']*+["\']|[^\s]++)\K\s++|$\K)#i', ' ',
                        $html
                );

                //remove redundant attributes
                $html = preg_replace(
                        '#(?:(?=[^<>]++>)|(?><?[^<]*+)*?<(?:(?:script|style|link)|/html>))(?>[ ]?[^ >]*+)*?\K'
                        . '(?: (?:type|language)=["\']?(?:(?:text|application)/(?:javascript|css)|javascript)["\']?|[^<]*+\K$)#i', '', $html
                );

                //Remove quotes from selected attributes
                if ($this->_isHtml5){
                        $ns1 = '"[^"\'`=<>\s]*+(?:[\'`=<>\s]|(?<=\\\\)")(?>(?:(?<=\\\\)")?[^"]*+)*?(?<!\\\\)"';
                        $ns2 = "'[^'\"`=<>\s]*+(?:[\"`=<>\s]|(?<=\\\\)')(?>(?:(?<=\\\\)')?[^']*+)*?(?<!\\\\)'";

                        $html = preg_replace(
                                "#(?:(?=[^>]*+>)|<[a-z0-9]++ )"
                                . "(?>[=]?[^=>]*+(?:=(?:$ns1|$ns2)|>(?><?[^<]*+)*?(?:<[a-z0-9]++ |$))?)*?"
                                . "(?:=\K([\"'])([^\"'`=<>\s]++)\g{1}[ ]?|\K$)#i", '$2 ', $html
                        );
                }

                //Remove last whitespace in open tag
                $html = preg_replace(
                        '#(?><?[^<]*+)*?(?:<[a-z0-9]++(?>\s*+[^\s>]++)*?\K(?:\s++(?=>)|(?<=["\'])\s++(?=/>))|$\K)#i', '', $html
                );

                return trim($html);
        }

}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!