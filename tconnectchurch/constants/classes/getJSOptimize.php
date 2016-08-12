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

class getJSOptimize{

        public function __construct($template, $css, $options= null){
								global $base_url;

								$this->basicURL	= !_getCheckNullorNot( $baseURL ) ? $base_url : $baseURL;
								$css_array = explode( '|', $css );
								if(sizeof($css_array)){
										for ( $i = 0; $i < sizeof( $css_array ); $i++ ){
											$this->_js .= (is_file(CONFIG_DCOCUMENT_ROOT ."/design/". $template ."/js/".$css_array[$i].".js") ? file_get_contents(CONFIG_DCOCUMENT_ROOT ."/design/". $template ."/js/".$css_array[$i].".js") : '');
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
        public function minifyJS (){
                //regex for double quoted strings
                $s1 = '"(?>(?:\\\\.)?[^\\\\"]*+)+?"';

                //regex for single quoted string
                $s2 = "'(?>(?:\\\\.)?[^\\\\']*+)+?'";

                //regex for block comments
                $b = '/\*(?>[^/\*]++|//|\*(?!/)|(?<!\*)/)*+\*/';

                //regex for line comments
                $c = '//[^\r\n]*+';

                //We have to do some manipulating with regexp literals; Their pattern is a little 'irregular' but
                //they need to be escaped
                //
                //characters that can precede a regexp literal
                $x1 = '[(.<>%,=:[!&|?+\-~*{;\r\n^]';
                //keywords that can precede a regex literal
                $x2 = '\breturn|\bthrow|\btypeof|\bcase|\bdelete|\bdo|\belse|\bin|\binstanceof|\bnew|\bvoid';
                //actual regexp literal
                $x3 = '/(?![/*])(?>(?(?=\\\\)\\\\.|\[(?>(?:\\\\.)?[^\]\r\n]*+)+?\])?[^\\\\/\r\n\[]*+)+?/';
                //ambiguous characters
                $x4 = '[)}]';
                //methods and properties
                $x5 = 'compile|exec|test|toString|constructor|global|ignoreCase|lastIndex|multiline|source';

                //regex for complete regexp literal
                $x = "(?:(?<={$x1}|$x2)(?<!\+\+|--){$x3}"
                        . "|(?<={$x4}){$x3}(?=\.(?:{$x5})))";

                //control characters excluding \r, \n and space
                $ws = '\x00-\x09\x0B\x0C\x0E-\x1F\x7F';

                //Remove spaces before regexp literals
                $this->_js = preg_replace(
                        "#(?>\s*+[^'\"/$ws ]*+(?>$s1|$s2|$b|$c|$x|/)?)*?\K"
                        . "(?>(?<=$x1|$x2)[$ws ]++($x3)|(?<=$x4)[$ws ]++($x3)(?=\.(?:$x5))|$)#si", '$1$2', $this->_js
                );

								//Remove comments
                $this->_js = preg_replace(
                        '#(?>(?:<(?!!))?[^<]*+(?:<(?:script|style)\b[^>]*+>(?><?[^<]*+)*?<\/(?:script|style)>|<!--\[(?><?[^<]*+)*?'
                        . '<!\s*\[(?>-?[^-]*+)*?--!?>|<!DOCTYPE[^>]++>)?)*?\K(?:<!--(?>-?[^-]*+)*?--!?>|[^<]*+\K$)#i', '', $this->_js);

                //replace line comments with line feed
                $this->_js = preg_replace("#(?>[^'\"/]*+(?>{$s1}|{$s2}|{$x}|{$b}|/(?![*/]))?)*?\K(?>{$c}|$)#si", "\n", $this->_js);

                //replace block comments with single space
                $this->_js = preg_replace("#(?>[^'\"/]*+(?>{$s1}|{$s2}|{$x}|/(?![*/]))?)*?\K(?>{$b}|$)#si", ' ', $this->_js);

                //convert carriage returns to line feeds
                $this->_js = preg_replace("#(?>[^'\"/\\r]*+(?>$s1|$s2|$x|/)?)*?\K(?>\\r\\n?|$)#si", "\n", $this->_js);

                //convert all other control characters to space
                $this->_js = preg_replace("#(?>[^'\"/$ws]*+(?>$s1|$s2|$x|/)?)*?\K(?>[$ws]++|$)#si", ' ', $this->_js);

                //replace runs of whitespace with single space or linefeed
                $this->_js = preg_replace("#(?>[^'\"/\\n ]*+(?>{$s1}|{$s2}|{$x}|[ \\n](?![ \\n])|/)?)*?\K(?:[ ]++(?=\\n)|\\n\K\s++|[ ]\K[ ]++|$)#si", '', $this->_js);

                //if regex literal ends line (without modifiers) insert semicolon
                $this->_js = preg_replace("#(?>[/]?[^'\"/]*+(?>$s1|$s2|$x(?!\\n))?)*?(?:$x\K\\n(?![!\#%&`*./,:;<=>?@\^|~}\])\"'])|\K$)#si", ';', $this->_js);

                //clean up
                $this->_js = preg_replace('#.+\K;$#s', '', $this->_js);


                //regex for removing spaces
                //remove space except when a space is preceded and followed by a non-ASCII character or by an ASCII letter or digit,
                //or by one of these characters \ $ _  ...ie., all ASCII characters except those listed.
                $c = '["\'!\#%&`()*./,:;<=>?@\[\]\^{}|~+\-]';
                $sp = "(?<=$c) | (?=$c)";

                //Non-ASCII characters
                $na = '[^\x00-\x7F]';

                //spaces to keep
                $k1 = "(?<=[\$_a-z0-9\\\\]|$na) (?=[\$_a-z0-9\\\\]|$na)|(?<=\+) (?=\+)|(?<=-) (?=-)";

                //regex for removing linefeeds
                //remove linefeeds except if it precedes a non-ASCII character or an ASCII letter or digit or one of these
                //characters: \ $ _ [ ( { + - and if it follows a non-ASCII character or an ASCII letter or digit or one of these
                //characters: \ $ _ ] ) } + - " ' ...ie., all ASCII characters except those listed respectively
                $ln = '(?<=[!\#%&`*./,:;<=>?@\^|~{\[(])\n|\n(?=[!\#%&`*./,:;<=>?@\^|~}\])"\'])';

                //line feeds to keep
                $k2 = "(?<=[\$_a-z0-9\\\\\])}+\-\"']|$na)\n(?=[\$_a-z0-9\\\\\[({+\-]|$na)";

                //remove unnecessary linefeeds and spaces
                $this->_js = preg_replace(
                        "#(?>[^'\"/\\n ]*+(?>$s1|$s2|$x|/|$k1|$k2)?)*?\K(?>$sp|$ln|$)#si", '', $this->_js
                );

								header ("content-type: text/javascript; charset: UTF-8");
								header ("cache-control: must-revalidate");
								header( "Content-Encoding: gzip" );
                echo trim($this->_js);
								exit();
        }
}
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!