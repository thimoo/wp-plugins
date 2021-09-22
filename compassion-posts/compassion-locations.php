<?php


class CompassionLocations
{
    public function __construct()
    {
        add_action('init', array($this, 'init'), 0);
        add_filter('cmb2_admin_init', array($this, 'location_settings'));
    }

    /**
     * Called by init action.
     */
    public function init()
    {
        $this->register_post_type_locations();
        $this->register_taxonomy_location_category();
        $this->register_taxonomy_location_group();
    }

    public function register_post_type_locations() {
        $labels = array(
            'name'                  => _x( 'Standorte', 'Post Type General Name', 'compassion-posts' ),
            'singular_name'         => _x( 'Standort', 'Post Type Singular Name', 'compassion-posts' ),
            'menu_name'             => __( 'Standorte', 'compassion-posts' ),
            'name_admin_bar'        => __( 'Standorte', 'compassion-posts' ),
            'archives'              => __( 'Standorte', 'compassion-posts' ),
            'parent_item_colon'     => __( 'Parent:', 'compassion-posts' ),
            'all_items'             => __( 'Alle Standorte', 'compassion-posts' ),
            'add_new_item'          => __( 'Neuen Standort eintragen', 'compassion-posts' ),
            'add_new'               => __( 'Neu', 'compassion-posts' ),
            'new_item'              => __( 'Neuer Standort', 'compassion-posts' ),
            'edit_item'             => __( 'Standort bearbeiten', 'compassion-posts' ),
            'update_item'           => __( 'Standort aktualisieren', 'compassion-posts' ),
            'view_item'             => __( 'Standort ansehen', 'compassion-posts' ),
            'search_items'          => __( 'Standort suchen', 'compassion-posts' ),
            'not_found'             => __( 'Nicht gefunden', 'compassion-posts' ),
            'not_found_in_trash'    => __( 'Nicht im Papierkorb gefunden', 'compassion-posts' ),
            'featured_image'        => __( 'Featured Image', 'compassion-posts' ),
            'set_featured_image'    => __( 'Set featured image', 'compassion-posts' ),
            'remove_featured_image' => __( 'Remove featured image', 'compassion-posts' ),
            'use_featured_image'    => __( 'Use as featured image', 'compassion-posts' ),
            'insert_into_item'      => __( 'Insert into item', 'compassion-posts' ),
            'uploaded_to_this_item' => __( 'Uploaded to this item', 'compassion-posts' ),
            'items_list'            => __( 'Items list', 'compassion-posts' ),
            'items_list_navigation' => __( 'Items list navigation', 'compassion-posts' ),
            'filter_items_list'     => __( 'Filter items list', 'compassion-posts' ),
        );
        $args = array(
            'label'                 => __( 'Standorte', 'compassion-posts' ),
            'description'           => __( '', 'compassion-posts' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor','revisions' ),
            'taxonomies'            => array(  ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_rest'          => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
        );
        register_post_type( 'location', $args );
    }

    /**
     * Register custom taxonomy 'location-category'
     */
    public function register_taxonomy_location_category() {
        register_taxonomy(
            'location-category',
            'location',
            array(
                'label' => __( 'Standort-Kategorie', 'compassion-posts' ),
                'rewrite' => array( 'slug' => 'location-category' ),
                'query_var'         => true,
                'hierarchical' => true,
            )
        );
    }

    /**
     * Register custom taxonomy 'location-group'
     */
    public function register_taxonomy_location_group() {
        register_taxonomy(
            'location-group',
            'location',
            array(
                'label' => __( 'Standort-Gruppe', 'compassion-posts' ),
                'rewrite' => array( 'slug' => 'location-group' ),
                'query_var'         => true,
                'hierarchical' => true,
            )
        );
    }

    /**
     * Called by cmb2_admin_init action.
     */
    public function location_settings( ) {
        // Start with an underscore to hide fields from custom fields list
        $prefix = '_cmb_';

        $cmb = new_cmb2_box( array(
            'id'            => $prefix . 'location_metabox',
            'title'         => __( 'Zusätzliche Angaben', 'compassion-posts' ),
            'object_types'  => array( 'location' ),
        ) );

        $cmb->add_field( array(
            'name'       => __( 'Text der Verlinkung', 'compassion-posts' ),
            'id'         => $prefix . 'link_text',
            'type'       => 'text'
        ) );

        $cmb->add_field( array(
            'name'      => __( 'Pixel von oben', 'compassion-posts' ),
            'id'        => $prefix . 'longitude',
            'desc'			=>	__('Bei Zentren handelt es sich um die Anzahl der Pixel von oben', 'compassion-posts'),
            'type'      => 'text'
        ) );

        $cmb->add_field( array(
            'name'       => __( 'Pixel von links', 'compassion-posts' ),
            'id'         => $prefix . 'latitude',
            'desc'			=>	__('Bei Zentren handelt es sich um die Anzahl der Pixel von links', 'compassion-posts'),
            'type'       => 'text'
        ) );

        $cmb->add_field( array(
            'name'      => __( 'Längengrad', 'compassion-posts' ),
            'id'        => $prefix . 'google_longitude',
            'type'      => 'text'
        ) );

        $cmb->add_field( array(
            'name'       => __( 'Breitengrad', 'compassion-posts' ),
            'id'         => $prefix . 'google_latitude',
            'type'       => 'text'
        ) );

        $cmb->add_field( array(
            'name'       => __( 'Foto', 'compassion-posts' ),
            'id'         => $prefix . 'country_photo',
            'type'       => 'file'
        ) );

        $cmb->add_field( array(
            'name'      => __( 'Titel', 'compassion-posts' ),
            'id'        => $prefix . 'country_child_title',
            'desc'		=> __('z.B. für Philippinen: in den Philippinen', 'compassion-posts'),
            'type'      => 'text'
        ) );

        $cmb->add_field( array(
            'name'      => __( 'Über das Land', 'compassion-posts' ),
            'id'        => $prefix . 'country_info',
            'type'      => 'wysiwyg'
        ) );

        $cmb->add_field( array(
            'name'      => __( 'Ländercode', 'compassion-posts' ),
            'id'        => $prefix . 'country_code',
            'type'      => 'text'
        ) );

        $cmb->add_field( array(
            'name'      => __( 'Externe URL', 'compassion-posts' ),
            'id'        => $prefix . 'country_url',
            'type'      => 'text'
        ) );

        $cmb->add_field( array(
            'name'      => __( 'about', 'compassion-posts' ),
            'id'        => $prefix . 'country_about',
            'type'      => 'wysiwyg'
        ) );
        $args = array(
                'type'=>'string',
                'single'=>true,
                'show_in_rest'=>true
        );
        register_post_meta('location', 'country_about', $args);
    }
}
