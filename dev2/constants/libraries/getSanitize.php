<?php
namespace ElasticActs\Constants\libraries;

/**
 * Sanitizing functions
 */
class getSanitize extends getSanitizeFCNs
{
    public $output;

    /**
     * Sanitization
     * @param  string $input [description]
     * @param  string $type  [description]
     * @return string        [description]
     */
    public function getSanitizing( $input, $type = "string" )
    {
        if ( is_array( $input ) ) {
            foreach ( $input as $var => $val ) {
              $output[$var] = $this->getSanitizing( $val );
            }
            unset( $input );
        } else {
            if ( $type === 'astxt' ){
                $input = getSanitizeFCNs::getHtml2txt( $input );
            } elseif ( $type === "simple" ) {
                $input = str_replace( "<p>&nbsp;</p>", "", trim( $input ) );
                $input = getSanitizeFCNs::getKeepHtml( $input );
            } elseif ( $type === "nojs" ) {
                $input = getSanitizeFCNs::getKeepHtml( $input );
                $input = getSanitizeFCNs::getCleanUpJS( $input );
            } elseif ( $type === "uri" ) {
                $input = getSanitizeFCNs::getCleanUpURL( $input );
            } else {
                $input = getSanitizeFCNs::getBasicCleanUp( $input );
            }
            $this->output = trim( $input );
        }
        return $this->output;
    }
}
