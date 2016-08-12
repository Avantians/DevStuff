<?php
/** -------------------------------------------------------------------------
 * This program is Open Source; you can redistribute it
 * and/or modify it under the terms of the GNU General Public License
 * This program is coded in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY
 * @package  CMS
 * @author    Kenwoo - iweb@kenwoo.ca
 * @license    http://creativecommons.org/licenses/by/4.0/ Creative Commons
 *
 * @actual package Minify
 * @original author Stephen Clay <steve@mrclay.org>
 * @original author http://code.google.com/u/1stvamp/ (Issue 64 patch)
 * 
 * [v02-02/25/2015]:: Set flag, not allow to direct access
 *  ------------------------------------------------------------------------- */
defined( "_VALID_MOS" ) or die( "Your system is not working properly." );

class getCSSOptimize{
 
        public function __construct($template, $css, $options= null){
								global $base_url;
				
								$this->basicURL	= !_getCheckNullorNot( $baseURL ) ? $base_url : $baseURL;
								$css_array = explode( '|', $css );							
								if(sizeof($css_array)){
										for ( $i = 0; $i < sizeof( $css_array ); $i++ ){
											$this->_css .= (is_file(CONFIG_DCOCUMENT_ROOT ."/design/". $template ."/css/".$css_array[$i].".css") ? file_get_contents(CONFIG_DCOCUMENT_ROOT ."/design/". $template ."/css/".$css_array[$i].".css") : '');
										}
								}
								$this->_template	= $template;										
								$this->_options = $options;					
        }

        /**
         * Minify a CSS string
         * @param string $css
         * @return string
         */
        public function minifyCSS (){
								//regex for double quoted strings
                $s1 = '"(?>(?:\\\\.)?[^\\\\"]*+)+?(?:"|(?=$))';
								//regex for single quoted string
                $s2 = "'(?>(?:\\\\.)?[^\\\\']*+)+?(?:'|(?=$))";
                $u  = '(?<=url)\([^)]*+\)';

								$this->_css = str_replace( "../", $this->basicURL."/design/". $this->_template ."/", $this->_css );
                // Remove all comments
                $this->_css = preg_replace("#(?>/?[^/\"'(]*+(?:$s1|$s2|$u|[\"'(])?)*?\K(?>/\*(?:\*?[^*]*+)*?\*/|//[^\r\n]*+|$)#s", '', $this->_css);

                // remove ws around , ; : { } in CSS Declarations and media queries
                $this->_css = preg_replace(
                        "#(?>(?:[{};]|^)[^{}@;]*+{|(?:(?<![,;:{}])\s++(?![,;:{}]))?[^\s{};\"'(]*+(?:$s1|$s2|$u|[\"'({};])?)+?\K"
                        . "(?:\s++(?=[,;:{}])|(?<=[,;:{}])\s++|\K$)#s", '',
                        $this->_css
                );

                //remove ws around , + > ~ { } in selectors
                $this->_css = preg_replace(
                        "#(?>(?:(?<![,+>~{}])\s++(?![,+>~{}]))?[^\s{\"'(]*+(?:{[^{}]++}|{|$s1|$s2|$u|[\"'(])?)*?\K"
                        . "(?:\s++(?=[,+>~{}])|(?<=[,+>~{};])\s++|$\K)#s", '', $this->_css
                );

                //remove last ; in block
                $this->_css = preg_replace("#(?>(?:;(?!}))?[^;\"'(]*+(?:$s1|$s2|$u|[\"'(])?)*?(?:\K;(?=})|$\K)#s", '', $this->_css);

                // remove ws inside urls
                $this->_css = preg_replace("#(?>\(?[^\"'(]*+(?:$s1|$s2|[\"'])?)*?(?:(?<=\burl)\(\K\s++|\G"
                        . "(?(?=[\"'])['\"][^'\"]++['\"]|[^\s]++)\K\s++(?=\))|$\K)#s", '', $this->_css);

                // minimize hex colors
                $this->_css = preg_replace("/(?>\#?[^\#\"'(]*+(?:$s1|$s2|$u|[\"'(])?)*?(?:(?<!=)\#\K"
                        . "([a-f\d])\g{1}([a-f\d])\g{2}([a-f\d])\g{3}|$\K)/is", '$1$2$3', $this->_css);

                // reduce remaining ws to single space
                $this->_css = preg_replace("#(?>[^\s'\"(]*+(?:$s1|$s2|$u|[\"'(]|\s(?!\s))?)*?\K(?:\s\s++|$)#s", ' ', $this->_css);
								
								header ("content-type: text/css; charset: UTF-8");
								header ("cache-control: must-revalidate");
								header( "Content-Encoding: gzip" );								
                echo trim($this->_css);
								exit();
        }
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!