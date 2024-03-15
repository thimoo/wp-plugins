<?php
add_action('wp_ajax_nopriv_process_quiz', 'process_quiz_form');

/**
 * Function to process the quiz form submission.
 * 
 * This function handles the submission of the quiz form. It verifies the security nonce to prevent CSRF attacks,
 * retrieves the user's answers, compares them with the correct answers stored in the database, calculates the score,
 * and returns the results in JSON format.
 */
function process_quiz_form()
{
    global $wpdb;

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['quiz_nonce'])) {
        // Verify the security nonce to prevent CSRF attacks
        if (wp_verify_nonce($_POST['quiz_nonce'], 'quiz_form_nonce')) {
            // Extract the user's answers submitted through the form
            $user_answers = isset($_POST['user_answers']) ? $_POST['user_answers'] : array();

            $quiz_id = isset($_POST['quiz_id']) ? intval($_POST['quiz_id']) : 0;
            // Retrieve the correct answers from the database
            $questions = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT id, correct_option FROM {$wpdb->prefix}quiz_questions WHERE quiz_id = %d",
                    $quiz_id
                )
            );

            $score = 0;
            $total_questions = count($questions);

            // Compare the user's answers with the correct answers
            foreach ($questions as $question) {
                $question_id = $question->id;
                $correct_option = $question->correct_option;

                if (isset($user_answers[$question_id]) && $user_answers[$question_id] == $correct_option) {
                    $score++;
                }
            }

            // Prepare the results to be returned
            $results = array(
                'score' => $score,
                'total_questions' => $total_questions,
                'message_title' => ($score === $total_questions) ? __("Congratulations!", "quiz_plugin_js") : __("Oops!", "quiz_plugin_js"),
                'message_subtitle' => ($score === $total_questions) ? __("You answered all questions correctly!", "quiz_plugin_js") : __("You were almost there!", "quiz_plugin_js")
            );

            // Return the results in JSON format
            wp_send_json($results);
        } else {
            // Nonce verification failed, possibly a CSRF attack
            wp_send_json_error(__("Security error", "quiz_plugin_js"));
        }
    }
}
add_action('init', 'process_quiz_form');

/**
 * Handles AJAX request to execute a shortcode.
 * 
 * Checks if the 'template_id' parameter is set in the AJAX request.
 * Constructs the shortcode with the provided template ID.
 * Executes the shortcode and echoes the result.
 * 
 */
function handle_shortcode_ajax_request() {
    // Check if the 'template_id' parameter is set in the AJAX request
    if (isset($_POST['template_id'])) {
        // Construct the shortcode with the provided template ID
        $shortcode = '[elementor-template id="' . $_POST['template_id'] . '"]';
        // Execute the shortcode and echo the result
        echo do_shortcode($shortcode);
    }
    exit;
}

add_action('wp_ajax_handle_shortcode_ajax_request', 'handle_shortcode_ajax_request');
add_action('wp_ajax_nopriv_handle_shortcode_ajax_request', 'handle_shortcode_ajax_request');

/**
 * Shortcode to display the quiz.
 * 
 * This shortcode generates and displays the quiz form. It retrieves questions from the database,
 * displays them one by one along with options, and provides functionality to submit the quiz
 * via AJAX. It also handles validation to ensure that an option is selected for each question.
 *
 * @param array $atts Shortcode attributes.
 * @return string Quiz form HTML.
 */
function quiz_shortcode($atts) {
    global $wpdb;

    // Shortcode attributes
    $atts = shortcode_atts(array(
        'quiz_id' => 0, // Default quiz ID is 0
        'success_elementor_template_id' => '', // Success Elementor template ID
        'fail_elementor_template_id' => '' // Fail Elementor template ID
    ), $atts);

    // Retrieve questions from the database based on the provided quiz ID
    $quiz_id = intval($atts['quiz_id']);
    $questions = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}quiz_questions WHERE quiz_id = %d", $quiz_id));

    ob_start();

    if ($questions) {
        // Display the quiz form
        echo "<form id='quiz-form'>";
        echo "<div class='quiz-container'>";
        echo "<input type='hidden' value='" . $quiz_id . "' name='quiz_id' />";
        foreach ($questions as $index => $question) {
            echo "<div class='quiz-question' style='display: " . ($index == 0 ? 'block' : 'none') . ";'>";
            echo "<h3 style='color:#005eb8;'>". ($index + 1) . '/' . count($questions) ." - {$question->question}</h3>";
            echo "<ul style='list-style-type: none;'>";

            // Decode the JSON string containing options
            $options = json_decode($question->options);

            // Generate radio buttons for each option
            foreach ($options as $option_index => $option) {
                echo "<li><input type='radio' name='user_answers[{$question->id}]' value='" . ($option_index + 1) . "'> {$option}</li>";
            }

            echo "</ul>";
            if ($index == count($questions) - 1) { // Display the "Submit Quiz" button after the second-last question
                echo "<button id='submit-button' type='button' class='button button-blue button-small '>". __("Submit Quiz", "quiz_plugin_js") ."</button>";
                
            } else {
                // Add a button to proceed to the next question (except for the last question)
                echo "<button id='next-question' type='button' class='button button-blue button-small' onclick='nextQuestion()'>". __("Next Question", "quiz_plugin_js") ."</button>";
            }
            echo "</div>";
        }
        echo "</div>";
        echo "</form>";
        echo "<div id='quiz-result' style='display: none;'></div>";
        echo "<div id='quiz-message'></div>";

    } else {
        echo "<p>". __("No questions found.", "quiz_plugin_js") ."</p>";
    }

    ?>
    <script type="text/javascript">
        function nextQuestion() {
            // Retrieve the currently displayed question
            var currentQuestion = jQuery('.quiz-question:visible');

            // Check if an option is selected for the current question
            var selectedOption = currentQuestion.find('input[type="radio"]:checked').val();
            
            if (selectedOption === undefined) {
                // No option selected for this question, display an error message
                alert('<?php echo __("Please select an answer for the current question before proceeding to the next one.", "quiz_plugin_js"); ?>');
                return; // Stop execution of the function if no option is selected
            }

            // Hide the current question
            currentQuestion.hide();

            // Display the next question
            currentQuestion.next('.quiz-question').show();
        }
        
        jQuery(document).ready(function($) {
            $('#quiz-form').on('click', '#submit-button', function(e) {
                e.preventDefault();
                
                // Retrieve the AJAX endpoint URL
                var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';

                // Create a FormData object to send form data
                var formData = new FormData($('#quiz-form')[0]);

                // Add the action and security nonce
                formData.append('action', 'process_quiz_form');
                formData.append('quiz_nonce', '<?php echo wp_create_nonce('quiz_form_nonce'); ?>');

                /**
                 * AJAX request to process quiz form submission.
                 * 
                 * Sends form data to the WordPress AJAX endpoint for processing.
                 * If successful, hides the quiz form and displays the result or
                 * an Elementor template based on the quiz outcome.
                 * 
                 * @param {FormData} formData - The form data to be submitted.
                 */
                $.ajax({
                    type: 'POST',
                    url: ajaxurl, // WordPress AJAX endpoint URL
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(answers) {
                        // Hide the quiz form
                        $('#quiz-form, #title_quiz,#sep_quiz').hide();

                        // Display Elementor template based on the quiz result
                        if (answers.score === answers.total_questions) {
                            var templateId = <?php echo json_encode($atts['success_elementor_template_id']); ?>; 
                        }else {
                            var templateId = <?php echo json_encode($atts['fail_elementor_template_id']); ?>;
                        }
                        if (templateId) {
                            executeShortcode(answers.quiz_id, templateId);
                        }else {
                            // Create HTML elements
                            var titleElement = $('<h3></h3>').text(answers.message_title).css('color', '#005eb8');
                            var subtitleElement = $('<p></p>').text(answers.message_subtitle);

                            // Display the result in the result div
                            $('#quiz-result').empty().append(titleElement, subtitleElement).show();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.answersText);
                    }
                });

                /**
                 * JavaScript function to handle AJAX request and execute shortcode.
                 * 
                 * @param {number} answers - The ID of the quiz.
                 * @param {number} templateId - The ID of the Elementor template.
                 */
                function executeShortcode(answers, templateId) {
                    $.ajax({
                        type: 'POST',
                        url: ajaxurl, // WordPress AJAX endpoint URL
                        data: {
                            action: 'handle_shortcode_ajax_request',
                            template_id: templateId
                        },
                        success: function(answers) {
                            $('#quiz-message').html(answers);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error executing shortcode:', error);
                        }
                    });
                }
            });
        });
    </script>
    <?php

    return ob_get_clean();
}
add_action("wp_ajax_frontend_action_without_file" , "quiz_shortcode");
add_action("wp_ajax_nopriv_frontend_action_without_file" , "quiz_shortcode");
add_shortcode('quiz', 'quiz_shortcode');