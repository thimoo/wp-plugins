<?php
/**
 * Callback function to display the content of the administration page for managing quiz questions.
 * It renders a form to add or edit quiz questions and displays existing questions in a table.
 */

function quiz_questions_admin_page_callback() {
    // Get the base URL of WordPress
    $base_url = home_url();

    // Get the quiz ID from the URL
    $quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;

    // Get the question ID from the URL
    $question_id = isset($_GET['question_id']) ? intval($_GET['question_id']) : 0;

    // Build the URL of the PHP script to retrieve question details
    $url_to_script = admin_url('admin-ajax.php?action=get_question_details&question_id=' . $question_id);

    // If a question ID is passed, retrieve the quiz details to modify
    $quiz_details = array();
    if ($question_id > 0) {
        $quiz_details = get_question_details_by_id($question_id);
    }
    ?>
    <div class="wrap">
        <h1><?php _e('Quiz Management', 'quiz_plugin_js'); ?></h1>
        
        <!-- Form to add or edit a question -->
        <h2><?php echo ($question_id > 0) ? __('Edit', 'quiz_plugin_js') : __('Add', 'quiz_plugin_js'); ?> <?php _e('a question', 'quiz_plugin_js'); ?></h2>
        <form id="add-edit-question-form" method="post" action="">
            <input type="text" 
                name="quiz_question" 
                id="quiz_question" 
                placeholder="<?php _e('Enter the question', 'quiz_plugin_js'); ?>" 
                value="<?php echo isset($quiz_details['question']) ? esc_attr($quiz_details['question']) : ''; ?>" required>
            <input type="text" 
                name="quiz_option_1" 
                id="quiz_option_1" 
                placeholder="<?php _e('Option 1', 'quiz_plugin_js'); ?>" 
                value="<?php echo isset($quiz_details['option_1']) ? esc_attr($quiz_details['option_1']) : ''; ?>" required>
            <input type="text" 
                name="quiz_option_2" 
                id="quiz_option_2" 
                placeholder="<?php _e('Option 2', 'quiz_plugin_js'); ?>" 
                value="<?php echo isset($quiz_details['option_2']) ? esc_attr($quiz_details['option_2']) : ''; ?>" required>
            <input type="text" 
                name="quiz_option_3" 
                id="quiz_option_3" 
                placeholder="<?php _e('Option 3', 'quiz_plugin_js'); ?>" 
                value="<?php echo isset($quiz_details['option_3']) ? esc_attr($quiz_details['option_3']) : ''; ?>" required>
            <label for="correct_option"><?php _e('Correct option:', 'quiz_plugin_js'); ?></label>
            <select name="correct_option" id="correct_option">
                <option value="1" <?php echo (isset($quiz_details['correct_option']) && $quiz_details['correct_option'] == 1) ? 'selected' : ''; ?>><?php _e('Option 1', 'quiz_plugin_js'); ?></option>
                <option value="2" <?php echo (isset($quiz_details['correct_option']) && $quiz_details['correct_option'] == 2) ? 'selected' : ''; ?>><?php _e('Option 2', 'quiz_plugin_js'); ?></option>
                <option value="3" <?php echo (isset($quiz_details['correct_option']) && $quiz_details['correct_option'] == 3) ? 'selected' : ''; ?>><?php _e('Option 3', 'quiz_plugin_js'); ?></option>
            </select>
            <input type="hidden" name="question_id" id="question_id" value="<?php echo $question_id; ?>">
            <input type="hidden" name="quiz_id" id="quiz_id" value="<?php echo $quiz_id; ?>">
            <input type="submit" 
                    name="add_edit_quiz_question" 
                    id="add_edit_quiz_question" 
                    class="button button-primary"
                    style="vertical-align: bottom !important;"
                    value="<?php echo ($question_id > 0) ? __('Edit Quiz', 'quiz_plugin_js') : __('Add Question', 'quiz_plugin_js'); ?>">
        </form>
        
        <!-- Display existing questions -->
        <h2><?php _e('Existing Questions', 'quiz_plugin_js'); ?></h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th scope="col"><?php _e('Question', 'quiz_plugin_js'); ?></th>
                    <th scope="col"><?php _e('Answers', 'quiz_plugin_js'); ?></th>
                    <th scope="col"><?php _e('Correct Answer', 'quiz_plugin_js'); ?></th>
                    <th scope="col"><?php _e('Actions', 'quiz_plugin_js'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                global $wpdb;
                $questions = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}quiz_questions WHERE quiz_id = %d", $quiz_id));

                // Use foreach() to iterate through the query results and display questions
                foreach ($questions as $question) {
                    // Escape data before displaying
                    $escaped_question = esc_html($question->question);
                    $question_id = intval($question->id);
                    $escaped_option_1 = esc_html($question->option_1);
                    $escaped_option_2 = esc_html($question->option_2);
                    $escaped_option_3 = esc_html($question->option_3);
                    $correct_option = intval($question->correct_option);

                    // Display the text of the correct answer
                    $correct_option_text = get_correct_option_text($question_id, $correct_option);

                    // Format the row using printf()
                    printf(
                        '<tr>
                            <td>%s</td>
                            <td>1. %s <br/> 2. %s <br/> 3. %s</td>
                            <td>%s</td>
                            <td>
                                <a href="%s" class="button button-primary">%s</a>
                                <a href="%s" class="button button-primary">%s</a>
                            </td>
                        </tr>',
                        $escaped_question,
                        $escaped_option_1,
                        $escaped_option_2,
                        $escaped_option_3,
                        $correct_option_text,
                        admin_url('admin.php?page=manage-quiz-questions&quiz_id=' . $quiz_id . '&question_id=' . $question_id),
                        __("Edit", "quiz_plugin_js"),
                        admin_url('admin.php?page=manage-quiz-questions&quiz_id=' . $quiz_id . '&delete_question=' . $question_id),
                        __("Delete", "quiz_plugin_js")
                    );
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}

/**
 * Function to retrieve the text of the correct answer based on the question ID.
 *
 * @param int $question_id The ID of the question.
 * @param int $correct_option The ID of the correct option.
 * @return string The text of the correct answer.
 */
function get_correct_option_text($question_id, $correct_option) {
    global $wpdb;
    
    // Select the text of the correct option from the database based on the question ID
    $query = $wpdb->prepare("SELECT option_%d FROM {$wpdb->prefix}quiz_questions WHERE id = %d", $correct_option, $question_id);
    $correct_option_text = $wpdb->get_var($query);
    
    return $correct_option_text;
}

/**
 * Function to handle the modification of a quiz question.
 * 
 * This function processes the form submission for adding/editing a quiz question.
 * It retrieves the question details from the POST data, sanitizes them, and updates or inserts the question into the database accordingly.
 * After processing, it redirects the user back to the quiz administration page.
 */
function edit_quiz_question()
{
    if (isset($_POST['add_edit_quiz_question'])) {
        $quiz_id = isset($_POST['quiz_id']) ? intval($_POST['quiz_id']) : 0;
        $question_id = isset($_POST['question_id']) ? intval($_POST['question_id']) : 0;
        $question = sanitize_text_field($_POST['quiz_question']);
        $option_1 = sanitize_text_field($_POST['quiz_option_1']);
        $option_2 = sanitize_text_field($_POST['quiz_option_2']);
        $option_3 = sanitize_text_field($_POST['quiz_option_3']);
        $correct_option = intval($_POST['correct_option']);

        global $wpdb;
        if ($question_id > 0) {
            // Update the existing question
            $wpdb->update(
                "{$wpdb->prefix}quiz_questions",
                array(
                    'question' => $question,
                    'option_1' => $option_1,
                    'option_2' => $option_2,
                    'option_3' => $option_3,
                    'correct_option' => $correct_option
                ),
                array('id' => $question_id),
                array('%s', '%s', '%s', '%s', '%d'),
                array('%d')
            );
        } else {
            // Add a new question
            $wpdb->insert(
                "{$wpdb->prefix}quiz_questions",
                array(
                    'quiz_id' => $quiz_id,
                    'question' => $question,
                    'option_1' => $option_1,
                    'option_2' => $option_2,
                    'option_3' => $option_3,
                    'correct_option' => $correct_option
                ),
                array('%s', '%s', '%s', '%s', '%s', '%d')
            );
        }

        // Redirect after modification
        wp_redirect(admin_url('admin.php?page=manage-quiz-questions&quiz_id=' . $quiz_id));
        exit;
    }
}
add_action('admin_init', 'edit_quiz_question');

/**
 * Function to retrieve details of a question by its ID.
 * 
 * @param int $question_id The ID of the question to retrieve details for.
 * @return array An array containing details of the question, or an empty array if no details are found.
 */
function get_question_details_by_id($question_id) {
    if ($question_id > 0) {
        global $wpdb;

        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}quiz_questions WHERE id = %d", $question_id);
        $question = $wpdb->get_row($query);

        if ($question) {
            $question_details = array(
                'question' => $question->question,
                'option_1' => $question->option_1,
                'option_2' => $question->option_2,
                'option_3' => $question->option_3,
                'correct_option' => $question->correct_option
            );

            return $question_details;
        }
    }

    return array(); // Return an empty array if no question details are found
}

/**
 * Function to handle the deletion of a quiz question.
 * 
 * This function processes the deletion of a quiz question based on the question ID passed through the query parameter.
 * It deletes the question from the database and redirects the user back to the quiz administration page.
 */
function delete_question() {
    if (isset($_GET['delete_question'])) {
        $quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;
        $question_id = intval($_GET['delete_question']);
        
        // Delete the question from the database
        global $wpdb;
        $result = $wpdb->delete("{$wpdb->prefix}quiz_questions", array('id' => $question_id), array('%d'));
        
        if ($result !== false) {
            // Redirect to the administration page after deletion
            wp_redirect(admin_url('admin.php?page=manage-quiz-questions&quiz_id=' . $quiz_id));
            exit;
        }
    }
}
add_action('admin_init', 'delete_question');