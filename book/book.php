<?php
/**
 * @package Book
 */
/*
    Plugin Name: Book
    Plugin URI: http://www.kenwoo.ca/
    Version: 1.0.0
    Author: Kenwoo
    Author URI: http://www.kenwoo.ca/
    Description: Book creates custom post type with an additional meta called "Publisher.
    License: GPLv2 or later
*/
namespace plugins\book;

define( 'PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once ( PLUGIN_DIR . "bookContainer.php" );
require_once ( PLUGIN_DIR . "metaBoxes.php" );
require_once ( PLUGIN_DIR . "bookShortCode.php" );

use plugins\book\bookContainer;

if ( is_admin()){
    add_action( 'init', array( new bookContainer, 'createBook' ) );
} else {
    add_shortcode('book', array( new bookShortCode, 'booksShortCode') );
}
