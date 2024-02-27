<?php
/**
 * Callback function to display the content of the administration page for managing quizzes.
 * It renders a form to add or edit quizzes and displays existing quizzes in a table.
 */
function quiz_quizzes_admin_page_callback() {
    // Get the base URL of WordPress
    $base_url = home_url();

    // Get the quiz ID from the URL
    $quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;

    // Build the URL of the PHP script to retrieve quiz details
    $url_to_script = admin_url('admin-ajax.php?action=get_quiz_details&quiz_id=' . $quiz_id);

    // If a quiz ID is passed, retrieve the quiz details to modify
    $quiz_details = array();
    if ($quiz_id > 0) {
        // Retrieve quiz details by ID
        $quiz_details = get_quiz_details_by_id($quiz_id);
    }
    ?>
    <div class="wrap">
        <h1><?php _e('Quizzes Management', 'quiz_plugin_js'); ?></h1>
        
        <!-- Form to add or edit a quiz -->
        <h2><?php echo ($quiz_id > 0) ? __('Edit', 'quiz_plugin_js') : __('Add', 'quiz_plugin_js'); ?> <?php _e('a quiz', 'quiz_plugin_js'); ?></h2>
        <form id="add-edit-quiz-form" method="post" action="">
            <input type="text" 
                name="quiz_title"
                id="quiz_title" 
                placeholder="<?php _e('Enter the quiz title', 'quiz_plugin_js'); ?>" 
                value="<?php echo isset($quiz_details['title']) ? esc_attr($quiz_details['title']) : ''; ?>" required>
            <select name="quiz_language" id="quiz_language">
                <?php
                // Get active languages in WordPress
                $languages = get_available_languages();
                foreach ($languages as $code => $name) {
                    echo '<option value="' . esc_attr($code) . '">' . esc_html($name) . '</option>';
                }
                ?>
            </select>
            <input type="hidden" name="quiz_id" id="quiz_id" value="<?php echo $quiz_id; ?>">
            <input type="submit" 
                    name="add_edit_quiz" 
                    id="add_edit_quiz" 
                    class="button button-primary quiz-button" 
                    value="<?php echo ($quiz_id > 0) ? __('Edit Quiz', 'quiz_plugin_js') : __('Add Quiz', 'quiz_plugin_js'); ?>">
        </form>

        
        <!-- Display existing quizzes -->
        <h2><?php _e('Existing Quizzes', 'quiz_plugin_js'); ?></h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th scope="col"><?php _e('Title', 'quiz_plugin_js'); ?></th>
                    <th scope="col"><?php _e('Language', 'quiz_plugin_js'); ?></th>
                    <th scope="col"><?php _e('Number of questions', 'quiz_plugin_js'); ?></th>
                    <th scope="col"><?php _e('Actions', 'quiz_plugin_js'); ?></th>
                    <th scope="col"><?php _e('Shortcode', 'quiz_plugin_js'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                global $wpdb;
                $quizzes = $wpdb->get_results("SELECT q.*, COUNT(qq.id) AS question_count FROM {$wpdb->prefix}quizzes q LEFT JOIN {$wpdb->prefix}quiz_questions qq ON q.id = qq.quiz_id GROUP BY q.id");

                // Use foreach() to iterate through the query results and display quizzes
                foreach ($quizzes as $quiz) {
                    // Escape data before displaying
                    $escaped_title = sanitize_text_field($quiz->title);
                    $escaped_title = stripslashes($escaped_title);
                    $language_code = $quiz->language_code;
                    $language_name = isset($languages[$language_code]) ? $languages[$language_code] : ''; // Get language name
                    $question_count = intval($quiz->question_count);
                    $quiz_id = intval($quiz->id);

                    // Format the row using printf()
                    printf(
                        '<tr>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%d</td>
                            <td>
                                <a href="%s" class="button button-primary">%s</a>
                                <a href="%s" class="button">%s</a>
                                <a href="%s" class="button">%s</a>
                            </td>
                            <td>[quiz quiz_id="%s" success_elementor_template_id="<b>ID HERE</b>" fail_elementor_template_id="<b>ID HERE</b>"]</td>
                        </tr>',
                        $escaped_title,
                        $language_name,
                        $question_count,
                        admin_url('admin.php?page=manage-quiz-questions&quiz_id=' . $quiz_id),
                        __('Manage Questions', 'quiz_plugin_js'),
                        admin_url('admin.php?page=quiz-admin&quiz_id=' . $quiz_id),
                        __('Edit Quiz', 'quiz_plugin_js'),
                        admin_url('admin.php?page=quiz-admin&delete_quiz=' . $quiz_id),
                        __('Delete Quiz', 'quiz_plugin_js'),
                        $quiz_id
                    );
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}

/**
 * Function to retrieve quiz details by ID.
 *
 * @param int $quiz_id The ID of the quiz.
 * @return array|null Quiz details if found, null otherwise.
 */
function get_quiz_details_by_id($quiz_id) {
    global $wpdb;
    
    // Prepare and execute SQL query to retrieve quiz details
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}quizzes WHERE id = %d", $quiz_id);
    $quiz = $wpdb->get_row($query, ARRAY_A);
    
    return $quiz;
}

/**
 * Function to handle the modification of a quiz.
 * 
 * This function processes the form submission for adding/editing a quiz.
 * It retrieves the quiz details from the POST data, sanitizes them, and updates or inserts the quiz into the database accordingly.
 * After processing, it redirects the user back to the quiz administration page.
 */
function edit_quiz() {
    if (isset($_POST['add_edit_quiz'])) {
        $quiz_id = isset($_POST['quiz_id']) ? intval($_POST['quiz_id']) : 0;
        $quiz_title = sanitize_text_field($_POST['quiz_title']); // Corrected field name
        $quiz_title = stripslashes($quiz_title);
        $quiz_language = sanitize_text_field($_POST['quiz_language']); // Corrected field name

        global $wpdb;
        if ($quiz_id > 0) {
            // Update the existing quiz
            $wpdb->update(
                "{$wpdb->prefix}quizzes",
                array(
                    'title' => $quiz_title, // Corrected field name
                    'language_code' => $quiz_language // Corrected field name
                ),
                array('id' => $quiz_id),
                array('%s', '%s'),
                array('%d')
            );
        } else {
            // Add a new quiz
            $wpdb->insert(
                "{$wpdb->prefix}quizzes",
                array(
                    'title' => $quiz_title, // Corrected field name
                    'language_code' => $quiz_language // Corrected field name
                ),
                array('%s', '%s')
            );
        }

        // Redirect after modification
        wp_redirect(admin_url('admin.php?page=quiz-admin'));
        exit;
    }
}
add_action('admin_init', 'edit_quiz');


/**
 * Function to handle the deletion of a quiz.
 * 
 * This function processes the deletion of a quiz based on the quiz ID passed through the query parameter.
 * It deletes the quiz from the database and redirects the user back to the manage quizzes page.
 */
function delete_quiz() {
    if (isset($_GET['delete_quiz'])) {
        $quiz_id = intval($_GET['delete_quiz']);
        
        // Delete the quiz from the database
        global $wpdb;
        $wpdb->delete("{$wpdb->prefix}quizzes", array('id' => $quiz_id), array('%d'));
        $wpdb->delete("{$wpdb->prefix}quiz_questions", array('quiz_id' => $quiz_id), array('%d'));
        
        // Redirect to the manage quizzes page after deletion
        wp_redirect(admin_url('admin.php?page=quiz-admin'));
        exit;
    }
}
add_action('admin_init', 'delete_quiz');