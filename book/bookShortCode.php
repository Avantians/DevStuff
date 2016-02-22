<?php
/*
    Description: Shortcode for book.
    License: GPLv2 or later
*/
namespace plugins\book;

class bookShortCode
{
    protected $content;
    protected $options;
    protected $post_meta;
    /**
    * Called when plugin is initialised
    */
    public function __construct()
    {
        $this->content   = '';
        $this->post_meta = '';
        $this->options   = array();
    }

    public function booksShortCode( $atts )
    {
        extract( shortcode_atts( array(
            'id' => null,
        ), $atts ) );

        // Define query parameters based on attributes
        $this->options = array(
            'id' => $id
        );

        $post = get_post($id);

        if ( $post->post_status === "publish" ){
            $this->post_meta = get_post_meta( $post->ID, '_publisher', TRUE );
            $this->content   = $post->post_title . " published by " . $this->post_meta;;

            return $this->content;
        }
    }
}
