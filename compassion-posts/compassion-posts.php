<?php
/**
 * Plugin Name: Compassion Posts
 * Plugin URI: https://github.com/CompassionCH/wp-plugins/tree/master/compassion-posts
 * Description: Create the different types of post for CompassionCH.
 * Version:     0.0.1
 * Author:      Christopher Meier <dev@c-meier.ch>
 * Text Domain: compassion-posts
 * Domain Path: /languages
*/

define('COMPASSION_POSTS_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
require_once(COMPASSION_POSTS_PLUGIN_DIR_PATH . 'compassion-agendas.php');
require_once(COMPASSION_POSTS_PLUGIN_DIR_PATH . 'compassion-downloads.php');
require_once(COMPASSION_POSTS_PLUGIN_DIR_PATH . 'compassion-locations.php');
require_once(COMPASSION_POSTS_PLUGIN_DIR_PATH . 'compassion-child.php');

class CompassionPosts
{
    public $agendas_post;
    public $downloads_post;
    public $locations_post;
    public $children_post;

    public function __construct()
    {
        $this->agendas_post = new CompassionAgendas();
        $this->downloads_post = new CompassionDownloads();
        $this->locations_post = new CompassionLocations();
        $this->children_post = new CompassionChildren();
        add_action('plugins_loaded', array($this, 'loaded'));
    }

    /**
     * Called by plugins_loaded action
     */
    public function loaded()
    {
        $this->load_text_domain();
    }

    public function load_text_domain() {
        load_plugin_textdomain( 'compassion-posts', false, basename( dirname( __FILE__ ) ) . '/languages/' );
    }
}

new CompassionPosts();
