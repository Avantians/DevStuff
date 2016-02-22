<?php
/*
    Description: Custome Meta Box.
    License: GPLv2 or later
*/
namespace plugins\book;

class metaBoxes
{
    protected $publisher;
    protected $metaBox;
    /**
    * Called when plugin is initialised
    */
    public function __construct()
    {
        $this->publisher = '';
        $this->metaBox   = array();
    }

    /**
    * Custom Meta Box on Custom Post Type
    */
    public function customMetaBoxes()
    {
        add_meta_box( 'publisher',
                      'Publisher',
                      array( $this, 'outputMetaBox' ),
                      'books',
                      'normal',
                      'high' );
    }

    /**
    * Output a Publisher meta box
    *
    * @param WP_Post $post WordPress Post object
    */
    public function outputMetaBox( $post )
    {
        $this->publisher = get_post_meta( $post->ID, '_publisher', true );

        // Add a nonce field to be available checkign it later.
        wp_nonce_field( 'save_publisher', 'publisher_nonce' );

        // Output label and field
        $this->metaBox[] = '<label for="publisher">' . __( 'Publisher', 'book' ) . '</label> ';
        $this->metaBox[] = '<input type="text" name="publisher" id="publisher" value="' . esc_attr( $this->publisher ) . '" />';

        echo implode("\n", $this->metaBox);
    }

    /**
    * Saves the meta box field data
    *
    * @param int $post_id Post ID
    */
    public function saveMetaBoxes( $post_id )
    {
        // Check if our nonce is set.
        if ( ! isset( $_POST['publisher_nonce'] ) ){
            return $post_id;
        }

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $_POST['publisher_nonce'], 'save_publisher' ) ){
            return $post_id;
        }

        // Check this is the Custom Post Type
        if ( 'books' != $_POST['post_type'] ){
            return $post_id;
        }

        // Check the logged in user has permission to edit this post
        if ( ! current_user_can( 'edit_post', $post_id ) ){
            return $post_id;
        }

        if ( isset( $_POST[ 'publisher' ] ) ){
            // After sanitize text, good to save meta data
            $this->publisher = sanitize_text_field( $_POST['publisher'] );
            update_post_meta( $post_id, '_publisher', $this->publisher );
        } else {
            return $post_id;
        }
    }
}
