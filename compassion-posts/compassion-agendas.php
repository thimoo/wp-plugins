<?php


class CompassionAgendas
{
    public function __construct()
    {
        add_action('init', array($this, 'init'), 0);
        add_action('daily_expiration_check', array($this, 'cron_expire_old_agenda_events'));
        add_filter('cmb2_admin_init', array($this, 'agenda_settings'));

        register_activation_hook(__FILE__, array($this, 'activation'));
        register_deactivation_hook(__FILE__, array($this, 'deactivation'));
    }

    /**
     * Called by init action.
     */
    public function init()
    {
        $this->register_post_type_agenda();
        $this->register_post_status_expired();
        $this->register_taxonomy_agenda_cat();
    }

    /**
     * Called on plugin activation.
     */
    public function activation() {
        $this->schedule_crons();
    }

    /**
     * Called on plugin deactivation.
     */
    public function deactivation() {
        $this->unschedule_crons();
    }

    public function schedule_crons() {
        if (! wp_next_scheduled ( 'daily_expiration_check' )) {
            wp_schedule_event(time(), 'daily', 'daily_expiration_check');
        }
    }

    public function unschedule_crons() {
        wp_clear_scheduled_hook('daily_expiration_check');
    }

    public function register_post_status_expired() {
        $args = array(
            'label'                     => __('Expired', 'compassion-posts'),
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true
        );
        register_post_status('expired', $args);
    }

    public function register_post_type_agenda() {

        $labels = array(
            'name'                  => _x( 'Agendas', 'Post Type General Name', 'compassion-posts' ),
            'singular_name'         => _x( 'Agenda', 'Post Type Singular Name', 'compassion-posts' ),
            'menu_name'             => __( 'Agendas', 'compassion-posts' ),
            'name_admin_bar'        => __( 'Agendas', 'compassion-posts' ),
            'archives'              => __( 'Agendas', 'compassion-posts' ),
            'parent_item_colon'     => __( 'Parent:', 'compassion-posts' ),
            'all_items'             => __( 'Tous les agendas', 'compassion-posts' ),
            'add_new_item'          => __( 'Ajouter une nouvelle date', 'compassion-posts' ),
            'add_new'               => __( 'Nouveau', 'compassion-posts' ),
            'new_item'              => __( 'Nouvelle date', 'compassion-posts' ),
            'edit_item'             => __( 'Modifier la date', 'compassion-posts' ),
            'update_item'           => __( 'Mettre à jour la date', 'compassion-posts' ),
            'view_item'             => __( 'Voir la date', 'compassion-posts' ),
            'search_items'          => __( 'Cherche une date', 'compassion-posts' ),
            'not_found'             => __( 'Nicht gefunden', 'compassion-posts' ),
            'not_found_in_trash'    => __( 'Nicht im Papierkorb gefunden', 'compassion-posts' ),
        );
        $args = array(
            'label'                 => __( 'Agendas', 'compassion-posts' ),
            'description'           => __( '', 'compassion-posts' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'thumbnail','excerpt' ),
            'taxonomies'            => array( ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'rewrite'				=> array( 'slug' => 'agenda' )
        );
        register_post_type( 'agendas', $args );

    }

    public function register_taxonomy_agenda_cat() {
        register_taxonomy(
            'categorie-cat',
            'agendas',
            array(
                'label'         => __( 'categorie', 'compassion-posts' ),
                'rewrite'       => array( 'slug' => 'categorie-cat' ),
                'query_var'     => true,
                'hierarchical'  => true,
            )
        );
    }

    public function cron_expire_old_agenda_events() {
        $current_time = current_time( 'timestamp' );

        $args = array(
            'post_type'=> 'agendas',
            'numberposts'   => -1,
        );
        $agenda_posts = get_posts($args);
        foreach($agenda_posts as $post) {
            $start_date = get_post_meta( $post->ID, '_agenda_date_agenda', true );

            if( ! empty( $start_date ) ) {
                $end_date = get_post_meta( $post->ID, '_agenda_date_agenda_fin', true );
                if(empty($end_date)) {
                    $expires = $start_date;
                } else {
                    $expires = $end_date;
                }

                // Get the current time and the post's expiration date
                $current_time = current_time( 'timestamp' );
                $expiration   = strtotime( $expires, current_time( 'timestamp' ) );

                // Expire only the day after
                $expiration += 86400;

                // Determine if current time is greater than the expiration date
                if( $current_time >= $expiration ) {
                    $post->post_status = 'expired';
                    wp_update_post($post);
                }
            }
        }
    }

    /**
     * Called by cmb2_admin_init action.
     */
    public function agenda_settings( ) {
        // Start with an underscore to hide fields from custom fields list
        $prefix = '_agenda_';

        $cmb = new_cmb2_box( array(
            'id'            => $prefix . 'settings',
            'title'         => __( 'date', 'compassion-posts' ),
            'object_types'  => array( 'agendas' ),
        ) );
        $cmb->add_field( array(
            'name'      => __( 'Datum hinzufügen', 'compassion-posts' ),
            'id'        => $prefix . 'date_agenda',
            'type'      => 'text_date',
        ) );

        $cmb->add_field( array(
            'name'      => __( 'Enddatum', 'compassion-posts' ),
            'id'        => $prefix . 'date_agenda_fin',
            'type'      => 'text_date',
        ) );
    }
}
