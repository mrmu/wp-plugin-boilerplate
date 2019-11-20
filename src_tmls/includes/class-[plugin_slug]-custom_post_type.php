<?php
/**
 * Register all custom post types for the plugin
 *
 * @link       [AUTHOR_URI]
 * @since      1.0.0
 *
 * @package    [plugin_slug_classname]
 * @subpackage [plugin_slug_classname]/includes
 */

/**
 * Register all custom post types for the plugin.
 *
 * Maintain a list of all custom post types that are registered throughout
 * the plugin, and register them with the WordPress API.
 *
 * @package    [plugin_slug_classname]
 * @subpackage [plugin_slug_classname]/includes
 * @author     [AUTHOR_NAME] <[AUTHOR_EMAIL]>
 */
class [plugin_slug_classname]_Custom_Post_Type {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

    }
    
    public function reg() {

		// if ( post_type_exists( "cpt_slug" ) )
        //     return;

        // $plugin_name = $this->plugin_name;

        // // Custom Taxonomy

		// $tax_singular  = __( 'taxonomy name', $plugin_name );
		// $tax_plural = __( 'taxonomy names', $plugin_name );
        // $rewrite   = array(
        //     'slug'         => 'taxonomy-name',
        //     'with_front'   => false,
        //     'hierarchical' => false
        // );
        // $public    = true;
        // $admin_capability = 'manage_categories';

        // register_taxonomy(
        //     "taxonomy_name",
        //     array( 'cpt_slug' ),
        //     array(
        //         'hierarchical' 			=> true,
        //         'label' 				=> $tax_singular,
        //         'labels' => array(
        //             'name'              => $tax_singular,
        //             'singular_name'     => $tax_singular,
        //             'menu_name'         => ucwords( $tax_singular ),
        //             'search_items'      => sprintf( __( 'Search %s', $plugin_name ), $tax_plural ),
        //             'all_items'         => sprintf( __( 'All %s', $plugin_name ), $tax_plural ),
        //             'parent_item'       => sprintf( __( 'Parent %s', $plugin_name ), $tax_singular ),
        //             'parent_item_colon' => sprintf( __( 'Parent %s:', $plugin_name ), $tax_singular ),
        //             'edit_item'         => sprintf( __( 'Edit %s', $plugin_name ), $tax_singular ),
        //             'update_item'       => sprintf( __( 'Update %s', $plugin_name ), $tax_singular ),
        //             'add_new_item'      => sprintf( __( 'Add New %s', $plugin_name ), $tax_singular ),
        //             'new_item_name'     => sprintf( __( 'New %s Name', $plugin_name ),  $tax_singular )
        //         ),
        //         'show_ui' 				=> true,
        //         'public' 	     		=> $public,
        //         'capabilities'			=> array(
        //             'manage_terms' 		=> $admin_capability,
        //             'edit_terms' 		=> $admin_capability,
        //             'delete_terms' 		=> $admin_capability,
        //             'assign_terms' 		=> $admin_capability,
        //         ),
        //         'rewrite' 				=> $rewrite,
        //     )
        // );

        // // Custom Post types

		// $singular  = __( 'CPT', $plugin_name );
        // $plural = __( 'CPTs', $plugin_name );
        // $menu_name = __( 'CPT Menu', $plugin_name );

        // $has_archive = false;
        // $rewrite     = array(
        //     'slug'       => 'cpt-slug',
        //     'with_front' => false,
        //     'feeds'      => true,
        //     'pages'      => false
        // );

        // register_post_type(
        //     "cpt_slug",
        //     array(
        //         'labels' => array(
        //             'name' 					=> $singular,
        //             'singular_name' 		=> $singular,
        //             'menu_name'             => sprintf( __( '%s', $plugin_name ), $menu_name ),
        //             'all_items'             => sprintf( __( 'All %s', $plugin_name ), $plural ),
        //             'add_new' 				=> __( 'Add New', $plugin_name ),
        //             'add_new_item' 			=> sprintf( __( 'Add %s', $plugin_name ), $singular ),
        //             'edit' 					=> __( 'Edit', $plugin_name ),
        //             'edit_item' 			=> sprintf( __( 'Edit %s', $plugin_name ), $singular ),
        //             'new_item' 				=> sprintf( __( 'New %s', $plugin_name ), $singular ),
        //             'view' 					=> sprintf( __( 'View %s', $plugin_name ), $singular ),
        //             'view_item' 			=> sprintf( __( 'View %s', $plugin_name ), $singular ),
        //             'search_items' 			=> sprintf( __( 'Search %s', $plugin_name ), $plural ),
        //             'not_found' 			=> sprintf( __( 'No %s found', $plugin_name ), $singular ),
        //             'not_found_in_trash' 	=> sprintf( __( 'No %s found in trash', $plugin_name ), $singular ),
        //             'parent' 				=> sprintf( __( 'Parent %s', $plugin_name ), $singular ),
        //             'featured_image'        => __( 'Event Cover', $plugin_name ),
        //             'set_featured_image'    => __( 'Set event cover', $plugin_name ),
        //             'remove_featured_image' => __( 'Remove event cover', $plugin_name ),
        //             'use_featured_image'    => __( 'Use as event cover', $plugin_name ),
        //         ),
        //         'description' => sprintf( __( 'This is where you can create and manage %s.', $plugin_name ), $singular ),
        //         'public' 				=> true,
        //         'show_ui' 				=> true,
        //         'capability_type' 		=> 'post',
        //         'map_meta_cap'          => true,
        //         'publicly_queryable' 	=> true,
        //         'exclude_from_search' 	=> false,
        //         'hierarchical' 			=> false,
        //         'rewrite' 				=> $rewrite,
        //         'query_var' 			=> true,
        //         'supports' 				=> array( 'title', 'editor', 'custom-fields' , 'thumbnail'),
        //         'has_archive' 			=> $has_archive,
        //         'show_in_nav_menus' 	=> false,
        //         'menu_icon' => 'dashicons-calendar'
        //     )
        // );


		// // Custom Post status

		// register_post_status( 'expired', array(
		// 	'label'                     => _x( 'Expired', 'post status', $plugin_name ),
		// 	'public'                    => true,
		// 	'exclude_from_search'       => true,
		// 	'show_in_admin_all_list'    => true,
		// 	'show_in_admin_status_list' => true,
		// 	'label_count'               => _n_noop( 'Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>', $plugin_name )
		// ) );

		// register_post_status( 'preview', array(
		// 	'public'                    => true,
		// 	'exclude_from_search'       => true,
		// 	'show_in_admin_all_list'    => true,
		// 	'show_in_admin_status_list' => true,
		// 	'label_count'               => _n_noop( 'Preview <span class="count">(%s)</span>', 'Preview <span class="count">(%s)</span>', $plugin_name )
        // ) );

    }
}
