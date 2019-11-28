<?php

/*
 * Plugin Name: Compassion Letters
 * Version:     0.0.1
 * Author:      giftGRUEN GmbH
*/
defined('ABSPATH') || die();

define('COMPASSION_LETTERS_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
define('COMPASSION_LETTERS_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));

// Constant used to store files processed from information sent by the user.
$plugin_name = dirname(plugin_basename(__FILE__));
$uploads_dir = wp_upload_dir();
define('COMPASSION_LETTERS_FILES_DIR_PATH', trailingslashit($uploads_dir['basedir']) . $plugin_name);
define('COMPASSION_LETTERS_FILES_DIR_URL',  trailingslashit($uploads_dir['baseurl']) . $plugin_name);

add_action('plugins_loaded', 'wan_load_textdomain');
function wan_load_textdomain() {
    load_plugin_textdomain( 'compassion-letters', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
}

require_once 'vendor/autoload.php';
require_once 'PDFGenerator.php';

use \WPHelper as WP;
//use \CompassionOdooConnector as Odoo;

class CompassionLetters
{
    private $pdf_folder;
    private $thumb_folder;
    private $template_folder;

    public function __construct()
    {
        add_action('init', [$this, '__init']);

        $this->pdf_folder = trailingslashit(COMPASSION_LETTERS_FILES_DIR_PATH) . 'pdf/';
        $this->pdf_folder_url = trailingslashit(COMPASSION_LETTERS_FILES_DIR_URL) . 'pdf/';
        $this->thumb_folder = trailingslashit(COMPASSION_LETTERS_FILES_DIR_PATH) . 'thumb/';
        $this->thumb_folder_url = trailingslashit(COMPASSION_LETTERS_FILES_DIR_URL) . 'thumb/';
        $this->uploads_folder = trailingslashit(COMPASSION_LETTERS_FILES_DIR_PATH) . 'uploads';
        $this->uploads_folder_url = trailingslashit(COMPASSION_LETTERS_FILES_DIR_URL) . 'uploads/';
        $this->template_folder = trailingslashit(COMPASSION_LETTERS_PLUGIN_DIR_PATH) . 'templates/';

        register_activation_hook(__FILE__, array($this, 'create_folder_structure'));
        // register cronjob
        register_activation_hook(__FILE__, array($this, 'register_cleanup_cronjob'));
        add_action('compassion-letters-cleanup-event', array($this, 'cleanup_action'));
        register_activation_hook(__FILE__, array($this, 'activation'));
    }

    /**
     * Called on plugin activation.
     */
    public function activation() {
        $this->check_dependencies();
    }

    public function check_dependencies() {
        if(!class_exists('CompassionOdooConnector')) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            wp_die( sprintf(__( 'Please install and activate: %s.', 'compassion' ), 'compassion-odoo'), 'Plugin dependency check', array( 'back_link' => true ) );
        }
    }

    public function __init()
    {
        add_shortcode('compassion-letters', array($this, 'shortcode'));
        add_shortcode('compassion-christmas', array($this, 'shortcodech'));


        // register ajax actions
        add_action( 'wp_ajax_compassion_letters_preview', array($this, 'ajax_action_preview') );
        add_action( 'wp_ajax_nopriv_compassion_letters_preview', array($this, 'ajax_action_preview') );

        add_action( 'wp_ajax_compassion_letters_send', array($this, 'ajax_action_send') );
        add_action( 'wp_ajax_nopriv_compassion_letters_send', array($this, 'ajax_action_send') );

        // load styles
        wp_enqueue_style('compassion-letters', plugin_dir_url(__FILE__) . '/assets/stylesheets/screen.css', array(), null);

        //load scripts
//        wp_enqueue_script('validation-js', plugin_dir_url(__FILE__) . 'bower_components/jquery-validation/dist/jquery.validate.min.js', array('jquery'));
        wp_enqueue_script('validation-js', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js', array('jquery'));

        wp_register_script( 'compassion-letters-js', plugin_dir_url(__FILE__) . 'assets/js/main.js' );
        wp_localize_script( 'compassion-letters-js', 'wp_data', [
            'admin_ajax' => admin_url( 'admin-ajax.php' ),
            'lang' => apply_filters( 'wpml_current_language', 'fr' )
        ] );
        wp_enqueue_script( 'compassion-letters-js');

    }

    public function create_folder_structure() {
        $targets = array($this->pdf_folder, $this->thumb_folder, $this->uploads_folder);
        foreach($targets as $target) {
            wp_mkdir_p($target);
        }
    }

    public function register_cleanup_cronjob() {
        if (! wp_next_scheduled ( 'compassion-letters-cleanup-event' )) {
            wp_schedule_event(time(), 'hourly', 'compassion-letters-cleanup-event');
        }
    }

    public function cleanup_action() {
        // uploads
        if ($handle = opendir($this->uploads_folder)) {
            while (false !== ($file = readdir($handle))) {
                if ('.' === $file) continue;
                if ('..' === $file) continue;

                unlink(trailingslashit($this->uploads_folder) . $file);
            }
            closedir($handle);
        }
    }

    function get_pdf_name() {
        if(!isset($_SESSION['letterid'])) {
            $_SESSION['letterid'] = session_id() . time();
        }
        return md5($_SESSION['letterid']);
    }

    function clean_pdf_files($pdf_path) {
        unlink($this->pdf_folder . $pdf_path);
        unlink($this->thumb_folder . basename($pdf_path, '.pdf') . '-0.jpg');
        unlink($this->thumb_folder . basename($pdf_path, '.pdf') . '-1.jpg');
    }

    /**
     * Upload images to plugin directory
     *
     * @return bool|null|string
     */
    private function handle_image_upload() {
        if(empty($_FILES) || isset($_FILES['image']) && $_FILES['image']['name'] == '') return null;

        $file = WP\Common::getFile('image', $this->uploads_folder);

        $this->correct_image_orientation($file);
        $this->maybe_resize_image($file);

        return $file;
    }

    /**
     * Resize the image if bigger that a target size.
     */
    private function maybe_resize_image($image_path) {
        $image = new Imagick($image_path);
        $imageLength = $image->getImageLength();
        $maxImageLength = 0.5 * 1024 * 1024.0;
        if($imageLength <= $maxImageLength) {
            return;
        }
        $scalingRatio = $maxImageLength / $imageLength;
        $new_width = round($image->getImageWidth() * $scalingRatio);
        $new_height = round($image->getImageHeight() * $scalingRatio);
        $image->resizeImage($new_width, $new_height, Imagick::FILTER_CUBIC, 0.5);
        $image->writeImage($image_path);
    }

    private function correct_image_orientation($image_path) {
        if(function_exists('exif_read_data')) {
            $exif = exif_read_data($image_path);
            if($exif && isset($exif['Orientation'])) {
                $orientation = $exif['Orientation'];
                if($orientation != 1) {
                    $img = imagecreatefromjpeg($image_path);
                    $deg = 0;
                    $flip = 0;
                    switch ($orientation) {
                        case 2:
                            $flip = IMG_FLIP_HORIZONTAL;
                            break;
                        case 4:
                            $flip = IMG_FLIP_HORIZONTAL;
                        case 3:
                            $deg = 180;
                            break;
                        case 5:
                            $flip = IMG_FLIP_HORIZONTAL;
                        case 6:
                            $deg = 270;
                            break;
                        case 7:
                            $flip = IMG_FLIP_HORIZONTAL;
                        case 8:
                            $deg = 90;
                            break;
                    }
                    if($deg) {
                        $img = imagerotate($img, $deg, 0);
                    }
                    if($flip) {
                        imageflip($img, $flip);
                    }
                    imagejpeg($img, $image_path, 95);
                }
            }
        } else {
            error_log('The function exif_read_data does not exist !');
        }
    }

    /**
     * Generate a preview image
     *
     * @return array
     */
    public function ajax_action_preview() {
        $form_data = $_POST;

        /**
         * upload images
         */
        $form_data['image'] = $this->handle_image_upload();

        /**
         * check if pdf exists already
         */
        if( isset($form_data['pdf_path']) && !empty($form_data['pdf_path']) && file_exists($this->pdf_folder . '/' . $form_data['pdf_path']) ) {
            $thumb_filename = basename($form_data['pdf_path'], ".pdf");
            $pdf_path = $form_data['pdf_path'];

            $thumbnails = PDFGenerator::preview($this->pdf_folder . '/' . $form_data['pdf_path'], $this->thumb_folder, $thumb_filename);

        } else {
            $pdf_path = PDFGenerator::generate($form_data, $this->pdf_folder, $this->get_pdf_name());
            $thumbnails = PDFGenerator::preview($this->pdf_folder . $pdf_path, $this->thumb_folder, basename($pdf_path, ".pdf"));
        }

        /*
         * Count number of preview asked (to avoid caching)
         */
        if( !isset($_SESSION['preview_count']) ) {
            $_SESSION['preview_count'] = 1;
        } else {
            $_SESSION['preview_count'] += 1;
        }
        foreach ($thumbnails as &$thumb) {
            $thumb .= '?v=' . $_SESSION['preview_count'];
        }
        echo json_encode([
            'thumbnails' => $thumbnails,
            'thumbnailURL' => $this->thumb_folder_url,
            'pdf' => $pdf_path,
        ]);

        wp_die();
    }

    /**
     * Load email template
     *
     * @param $template
     * @param $data
     * @return string
     */
    public function get_email_template1($template, $data) {

        $my_current_lang = apply_filters( 'wpml_current_language', NULL );
        ob_start();
        $form_data = $data;

        if ( $my_current_lang == "fr" ) {
            include($this->template_folder . 'email/' . $template);
        } elseif ( $my_current_lang == "de" ) {
            include('templates/email_de/' . $template);
        } elseif ( $my_current_lang == "it" ) {
            include('templates/email_it/' . $template);
        }
        $content = ob_get_contents();
        ob_end_clean();
        return $content;

    }

    /**
     * Send PDF to compassion and user
     */
    public function ajax_action_send() {

        $form_data = $_POST;

        $form_data['image'] = $this->handle_image_upload();

        // Don't use the same filename for the next letter.
        unset($_SESSION['letterid']);

        if( isset($form_data['pdf_path']) && !empty($form_data['pdf_path']) && file_exists($this->pdf_folder . '/' . $form_data['pdf_path']) ) {
            $pdf_path = $form_data['pdf_path'];
        } else {
            $pdf_path = PDFGenerator::generate($form_data, $this->pdf_folder, $this->get_pdf_name());
        }
        $file_to_attach = $this->pdf_folder_url . $pdf_path;
        if ($form_data['image'] !== NULL) {
            $image_url = $this->uploads_folder_url . basename($form_data['image']);
        } else {
            $image_url = false;
        }

        if ($this->_sentToOdoo(
            $form_data['referenznummer'], $form_data['patenkind'], $form_data['message'],
            $form_data['template'], $file_to_attach, $image_url,
            $form_data['name'], $form_data['email']
            )
        ) {
            /**
             * send email to user
             */
            $email = new PHPMailer();
            $email->isSMTP();                                      // Set mailer to use SMTP
            $email->Host = 'smtp.sendgrid.net';  // Specify main and backup SMTP servers
            $email->SMTPAuth = true;                               // Enable SMTP authentication
            $email->Username = 'apikey';                 // SMTP username
            $email->Password = SENDGRID_API_KEY;                           // SMTP password
            $email->Port = 587;
            $email->CharSet = 'UTF-8';
            $email->From = 'info@compassion.ch';
            $email->FromName = __('Compassion Schweiz', 'compassion-letters');
            $email->Subject = __('Der Brief an Ihr Patenkind', 'compassion-letters');
            $email->Body = $this->get_email_template1('user-email.php', $form_data);
            $email->isHTML(true);
            $email->AddAddress($form_data['email']);
            // $email->AddBCC('ecino@compassion.ch', 'Compassion Suisse');
            $file_to_attach = $this->pdf_folder . $pdf_path;
            $email->AddAttachment($file_to_attach, $form_data['patenkind'].'.pdf');
            // Disable Sendgrid Unsubscribe
            $email->addCustomHeader('X-SMTPAPI', '{"filters": {"subscriptiontrack" : {"settings" : {"enable" : 0}}}}');
            $email->Send();

            $this->clean_pdf_files($pdf_path);

            wp_send_json_success([message => 'Letter imported']);
        }

        wp_send_json_error([message => 'Import error']);
    }

    /**
     * Generate shortcode
     *
     * Process form data and load next template
     *
     * @return string
     */
    public function shortcode()
    {

        ob_start();

        include('templates/frontend/form.php');

        $content = ob_get_contents();

        ob_end_clean();

        return $content;
    }


     public function shortcodech()
    {

        ob_start();

        include('templates/frontend/form_christmas.php');

        $content = ob_get_contents();

        ob_end_clean();

        return $content;
    }


    /**
     * Functions for XMLRPC sending to Odoo
     */
    private function _sentToOdoo($sponsor_number, $child_number, $letter_text, $template_name, $file_url, $attachment_url, $name, $email)
    {
        $letter_text = str_replace("\\", "", $letter_text);
        $ext = substr($attachment_url, -3, 3);
        if ($ext == "jpeg") {
            $ext = "jpg";
        }

        // Call method in Odoo to insert new web letter
        $odoo = new CompassionOdooConnector();
        $utm_source = false;
        $utm_medium = false;
        $utm_campaign = false;
        if(isset($_SESSION) AND isset($_SESSION['utm_source'])) {
            $utm_source = $_SESSION['utm_source'];
        }
        if(isset($_SESSION) AND isset($_SESSION['utm_medium'])) {
            $utm_medium = $_SESSION['utm_medium'];
        }
        if(isset($_SESSION) AND isset($_SESSION['utm_campaign'])) {
            $utm_campaign = $_SESSION['utm_campaign'];
        }
        $res = $odoo->call_method(
            'import.letters.history', 'import_web_letter',
            array($child_number, $sponsor_number, $name, $email, $letter_text, $template_name, $file_url, $attachment_url, $ext, $utm_source, $utm_medium, $utm_campaign));
        if ($res and $res->faultString) {
            return false;
        }
        return $res;
    }
}

new CompassionLetters();
