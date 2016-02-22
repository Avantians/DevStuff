<?php
/*
    Description: Container for Book with an additional meta called "Publisher.
    License: GPLv2 or later
*/
namespace plugins\book;

use plugins\book\metaBoxes;
use plugins\book\bookShortCode;

class bookContainer
{
    /**
    * Called when plugin is initialised
    */
    public function __construct()
    {
        add_action( 'add_meta_boxes',   array( new metaBoxes, 'customMetaBoxes' ) );
        add_action( 'save_post',        array( new metaBoxes, 'saveMetaBoxes' ) );
    }

   /**
    * Register a Custom Post Type
    */
    public function createBook()
    {
        register_post_type( 'books', array(
            'labels' => array(
                'name'               => _x('Books', 'post type general name'),
                'singular_name'      => _x('Book', 'post type singular name'),
                'add_new'            => __('Add New'),
                'add_new_item'       => __('Add New Book'),
                'edit'               => __('Edit'),
                'edit_item'          => __('Edit Book'),
                'new_item'           => __('New Book'),
                'view'               => __('View'),
                'view_item'          => __('View Book'),
                'search_items'       => __('Search Book'),
                'not_found'          => __('No Book found'),
                'not_found_in_trash' => __('No Book found in Trash'),
                'parent'             => __('Parent Book')
            ),

            // Frontend
            'has_archive'        => false,
            'public'             => true,
            'publicly_queryable' => true,

            // Admin
            'hierarchical'    => false,
            'capability_type' => 'post',
            'menu_icon'       => 'dashicons-book',
            'menu_position'   => 10,
            'query_var'       => true,
            'show_in_menu'    => true,
            'show_ui'         => true,
            'supports'        => array(
                                        'title',
                                        'publisher',
                                        'editor')
        ) );
    }
}
