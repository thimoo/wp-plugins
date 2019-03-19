<?php


class CompassionDownloads
{
    public function __construct()
    {
        add_action('init', array($this, 'init'), 0);
        add_filter('cmb2_admin_init', array($this, 'download_settings'));
    }

    /**
     * Called by init action.
     */
    public function init()
    {
        $this->register_post_type_download();
        $this->register_taxonomy_download_cat();
    }

    public function register_post_type_download() {
        $labels = array(
            'name'                  => _x( 'Downloads', 'Post Type General Name', 'compassion-posts' ),
            'singular_name'         => _x( 'Download', 'Post Type Singular Name', 'compassion-posts' ),
            'menu_name'             => __( 'Downloads', 'compassion-posts' ),
            'name_admin_bar'        => __( 'Downloads', 'compassion-posts' ),
            'archives'              => __( 'Downloads', 'compassion-posts' ),
            'parent_item_colon'     => __( 'Parent:', 'compassion-posts' ),
            'all_items'             => __( 'Alle Downloads', 'compassion-posts' ),
            'add_new_item'          => __( 'Neuen Download eintragen', 'compassion-posts' ),
            'add_new'               => __( 'Neu', 'compassion-posts' ),
            'new_item'              => __( 'Neuen Download', 'compassion-posts' ),
            'edit_item'             => __( 'Download bearbeiten', 'compassion-posts' ),
            'update_item'           => __( 'Download aktualisieren', 'compassion-posts' ),
            'view_item'             => __( 'Download ansehen', 'compassion-posts' ),
            'search_items'          => __( 'Download suchen', 'compassion-posts' ),
            'not_found'             => __( 'Nicht gefunden', 'compassion-posts' ),
            'not_found_in_trash'    => __( 'Nicht im Papierkorb gefunden', 'compassion-posts' ),
        );
        $args = array(
            'label'                 => __( 'Downloads', 'compassion-posts' ),
            'description'           => __( '', 'compassion-posts' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'thumbnail' ),
            'taxonomies'            => array( ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'show_in_rest'          => true,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
            'rewrite'								=> array( 'slug' => 'downloads' )
        );
        register_post_type( 'download', $args );
    }

    public function register_taxonomy_download_cat() {
        register_taxonomy(
            'download-cat',
            'download',
            array(
                'label'         => __( 'Kategorien', 'compassion-posts' ),
                'rewrite'       => array( 'slug' => 'download-cat' ),
                'query_var'     => true,
                'hierarchical'  => true,
            )
        );
    }

    /**
     * Called by cmb2_admin_init action.
     */
    public function download_settings( ) {
        // Start with an underscore to hide fields from custom fields list
        $prefix = '_download_';

        $cmb = new_cmb2_box( array(
            'id'            => $prefix . 'settings',
            'title'         => __( 'Zusätzliche Angaben', 'compassion-posts' ),
            'object_types'  => array( 'download' ),
        ) );

        $cmb->add_field( array(
            'name'       => __( 'Bestellen', 'compassion-posts' ),
            'desc'       => __( 'Download für Bestellungen freigeben', 'compassion-posts' ),
            'id'         => $prefix . 'order',
            'type'       => 'checkbox'
        ) );

        $cmb->add_field( array(
            'name'       => __( 'Button-Text', 'compassion-posts' ),
            'id'         => $prefix . 'button_text',
            'type'       => 'text'
        ) );

        $cmb->add_field( array(
            'name'       => __( 'Button-Link', 'compassion-posts' ),
            'id'         => $prefix . 'button_link',
            'type'       => 'text'
        ) );
    }
}
