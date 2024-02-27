<?php
/*
Plugin Name: Quiz Plugin
Description: Plugin de quiz.
Version: 1.0
Author: Jeremie Lang for Compassion Suisse
Text Domain: quiz_plugin_js
*/

/**
 * Function to activate the quiz plugin.
 * It creates the necessary database tables for storing quizzes and quiz questions upon activation.
 */
function quiz_activate() {
    global $wpdb;

    // Get the charset collation
    $charset_collate = $wpdb->get_charset_collate();
    
    // Define the table names
    $quiz_table_name = $wpdb->prefix . 'quizzes';
    $question_table_name = $wpdb->prefix . 'quiz_questions';

    // Check if the tables already exist
    if ($wpdb->get_var("SHOW TABLES LIKE '$quiz_table_name'") != $quiz_table_name &&
        $wpdb->get_var("SHOW TABLES LIKE '$question_table_name'") != $question_table_name) {

        // SQL query to create the quizzes table
        $sql_quiz_table = "CREATE TABLE $quiz_table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            language_code varchar(10) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // SQL query to create the questions table
        $sql_question_table = "CREATE TABLE $question_table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            quiz_id mediumint(9) NOT NULL,
            question text NOT NULL,
            options text NOT NULL, /* Changed column type to text */
            correct_option tinyint(1) NOT NULL,
            PRIMARY KEY  (id),
            FOREIGN KEY (quiz_id) REFERENCES $quiz_table_name(id)
        ) $charset_collate;";

        // Include WordPress upgrade functions
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // Execute the SQL queries to create the tables
        dbDelta($sql_quiz_table);
        dbDelta($sql_question_table);
    }
}
// Register the activation hook for the quiz plugin
register_activation_hook(__FILE__, 'quiz_activate');



/**
 * Function to enqueue the plugin styles.
 * It retrieves the URL of the active theme's style.css file and enqueues it.
 * Additionally, it enqueues a custom CSS file for the plugin.
 */
function enqueue_plugin_styles() {
    // Get the URL of the custom CSS file for the plugin
    $plugin_css_url = plugins_url('/assets/css/quiz-plugin-styles.css', __FILE__);

    // Enqueue the custom CSS file for the plugin
    wp_enqueue_style('quiz-plugin-styles', $plugin_css_url);

    // Get the URL of the active theme's style.css file
    $theme_css_url = get_stylesheet_directory_uri() . '/style.css';

    // Enqueue the CSS file of the active theme
    wp_enqueue_style('plugin-theme-styles', $theme_css_url);
}
// Add action hook to enqueue plugin styles on WordPress frontend
add_action('wp_enqueue_scripts', 'enqueue_plugin_styles');
add_action('admin_enqueue_scripts', 'enqueue_plugin_styles');



/**
 * Function to add an administration page for managing quizzes.
 * It adds a submenu page under the main Quiz menu in the admin dashboard.
 */
function add_quiz_admin_page() {
    // Add the main Quiz menu page
    add_menu_page(
        __('Manage Quizzes', 'quiz_plugin_js'), // Page title
        __('Quizzes', 'quiz_plugin_js'), // Menu title
        'edit_pages', // Capability required to access the page (changed from 'manage_options')
        'quiz-admin', // Menu slug
        'quiz_quizzes_admin_page_callback', // Callback function to display the page content
        'dashicons-welcome-learn-more', // Icon for the menu item (optional)
        30 // Menu position (optional)
    );


    // Check if a quiz ID is set
    $quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;
    if ($quiz_id > 0) {
        // Add a submenu page for managing quiz questions only if a quiz ID is set
        add_submenu_page(
            'quiz-admin', // Parent menu slug
            __('Manage Quiz Questions', 'quiz_plugin_js'), // Page title
            __('Quiz Questions', 'quiz_plugin_js'), // Menu title
            'edit_pages', // Capability required to access the page (changed from 'edit_pages')
            'manage-quiz-questions', // Menu slug
            'quiz_questions_admin_page_callback' // Callback function to display the page content
        );
    }
}
add_action('admin_menu', 'add_quiz_admin_page');



// Include the quizzes administration page file
require_once plugin_dir_path(__FILE__) . 'admin/quiz-quizzes-admin.php';
require_once plugin_dir_path(__FILE__) . 'admin/quiz-questions-admin.php';
require_once plugin_dir_path(__FILE__) . 'quiz-shortcode.php';
