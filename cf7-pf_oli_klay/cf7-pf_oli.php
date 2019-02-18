<?php
/*
  Plugin Name: Postfinance paiement
  Plugin URI: http://compassion.ch/
  Description:
  Author: Olivier Requet / J. Kläy
  Version: 0.2
  Author URI: http://compassion.ch/
 */
defined('ABSPATH') || die();

global $donation_db_version;
$donation_db_version = '1.25';

const DONATION_TABLE_NAME = 'donation_to_odoo';

function donation_db_install() {

    global $wpdb;
    global $donation_db_version;

    $table_name = $wpdb->prefix . DONATION_TABLE_NAME;

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
             id int(1) NOT NULL AUTO_INCREMENT,
             time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
             ip_address tinytext NOT NULL,
             email tinytext NOT NULL,
             orderid tinytext NULL,
             campaign_slug tinytext NULL,
             last_name tinytext NOT NULL,
             first_name tinytext NOT NULL,
             street tinytext NOT NULL,
             zipcode tinytext NOT NULL,
             city tinytext NOT NULL,
             country tinytext NOT NULL,
             language varchar(5) NOT NULL,
             amount decimal(10,2) NULL,
             currency varchar(5) NOT NULL,
             fund tinytext NULL,
             child_id tinytext NULL,
             partner_ref tinytext NULL,
             transaction_id tinytext NOT NULL,
             session_id tinytext NOT NULL,
             pf_pm tinytext NULL,
             pf_payid varchar(16) NULL,
             pf_brand tinytext NULL,
             pf_raw text NULL,
             odoo_status tinytext NOT NULL,
             odoo_complete_time datetime NULL,
             odoo_invoice_id int(1) NULL,
             PRIMARY KEY (id)
            ) $charset_collate; ";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);

    add_option('donation_db_version', $donation_db_version);

    if (version_compare($donation_db_version, '1.24') < 0) {
        $sql = "CREATE TABLE $table_name (
                 id int(1) NOT NULL AUTO_INCREMENT,
                 time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
                 ip_address tinytext NOT NULL,
                 email tinytext NOT NULL,
                 orderid tinytext NULL,
                 campaign_slug tinytext NULL,
                 last_name tinytext NOT NULL,
                 first_name tinytext NOT NULL,
                 street tinytext NOT NULL,
                 zipcode tinytext NOT NULL,
                 city tinytext NOT NULL,
                 country tinytext NOT NULL,
                 language varchar(5) NOT NULL,
                 amount decimal(10,2) NULL,
                 currency varchar(5) NOT NULL,
                 fund tinytext NULL,
                 child_id tinytext NULL,
                 partner_ref tinytext NULL,
                 transaction_id tinytext NOT NULL,
                 session_id tinytext NOT NULL,
                 pf_pm tinytext NULL,
                 pf_payid varchar(16) NULL,
                 pf_brand tinytext NULL,
                 pf_raw text NULL,
                 odoo_status tinytext NOT NULL,
                 odoo_complete_time datetime NULL,
                 odoo_invoice_id int(1) NULL,
                 PRIMARY KEY (id)
                ) $charset_collate; ";
        dbDelta($sql);
        error_log('update to version 1.25');

        update_option('donation_db_version', '1.25');
    }
}

add_action('plugins_loaded', 'donate_load_textdomain');

function donate_load_textdomain() {
    load_plugin_textdomain('donation-form', false, dirname(plugin_basename(__FILE__)) . '/lang/');
}

function guidv4()
{
    if (function_exists('com_create_guid') === true)
        return trim(com_create_guid(), '{}');

    $data = openssl_random_pseudo_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

register_activation_hook(__FILE__, 'donation_db_install');

class Compassion_Donation_Form {

    public static $alreadyEnqueued = false;

    private $step;

    // Possible values for the odoo_status db field.
    const SUBMITTED_TO_PF = 'submit_to_pf';
    const RECEIVED_FROM_PF = 'received_from_pf';
    const INVOICED = 'invoiced';
    const VERIFICATION_ERROR = 'verification_error';


    public function __construct() {
        add_action('init', [$this, '__init']);
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueueScripts' ) );

    }

    public function enqueueScripts() {
        if ( ! self::$alreadyEnqueued ) {
            wp_enqueue_script( 'wp-color-picker' );
            wp_enqueue_style( 'wp-color-picker' );
        }
        self::$alreadyEnqueued = true;
    }

    public function __init() {

        if (!isset($_SESSION)) {
            session_start();
        }
        @$_SESSION['count_runs']=0;
//         error_log($_SESSION['campaign_slug']);

        add_shortcode('donation-form', array($this, 'shortcode'));

        add_shortcode('donation-confirmation', array($this, 'shortcode_confirmation'));


        // load styles
        wp_enqueue_style('donation-form', plugin_dir_url(__FILE__) . '/assets/stylesheets/screen.css', array(), null);

        //load scripts
        wp_enqueue_script('validation-js', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js', array('jquery'));
    }



    /**
     * Process form data
     *
     * Save data to session, destroy session or send data
     *
     * @param $data
     */
    private function process_form_data($data) {
//        $session_data = $_SESSION['donation-form'];

        switch ($this->step) {
            case 2;
                $this->send_data($data);
//                session_destroy();
                break;
        }
    }

    /**
     * Generate shortcode
     *
     * Process form data and load next template
     *
     * @return string
     */
    public function shortcode($atts, $content) {

        $this->step = (isset($_GET['step'])) ? intval($_GET['step']) : 1;
        $atts = shortcode_atts(
                array(
            'form' => '',
                ), $atts);
        /**
         * process form data
         */
        $this->process_form_data($_POST);

        /**
         * load template
         */
        ob_start();
//        $session_data = $_SESSION['donation-form'];

//      include("templates/frontend/header.php");
        if ('donation' == $atts['form']) {
            include("templates/frontend/step-$this->step.php");
        } elseif ('csp' == $atts['form']){
            include("templates/csp/step-$this->step.php");
        } else {
            include("templates/cadeau/step-$this->step.php");
        }

        $content = ob_get_contents();
        ob_end_clean();

        /**
         * return shortcode
         */
        return $content;
    }

    private function cleanfordb($value) {

        return trim(filter_var($value));

    }

    /**
     * Send form data
     *
     * When user completed form do whatever you want with the data
     *
     * @param $data
     */
    private function send_data($data) {
//        print_r($data);

        global $wpdb;

        $session_data = $data;
        $my_current_lang = apply_filters('wpml_current_language', NULL);
        if ($my_current_lang == 'fr') {
            $lang = 'fr_FR';
        } elseif ($my_current_lang == 'de') {
            $lang = 'de_DE';
        } elseif ($my_current_lang == 'it') {
            $lang = 'it_IT';
        } else {
            $lang = 'de_DE';
        }

        if($_SESSION['count_runs']==0) {
            $transaction = guidv4();
            $_SESSION['transaction'] = $transaction;
        }

        $from_csp = substr($session_data['fonds'], 0, strlen('csp_mensuel')) == 'csp_mensuel';
        $final_amount = ($from_csp ? floatval(substr($session_data['fonds'], -2)) : $session_data['wert']);

        // Form data to send to postfinance (ogone)
        $base_address = 'https://' . $_SERVER['HTTP_HOST'] . '/' . $my_current_lang;
        $form = array(
            'PSPID' => 'compassion_yp',
            'ORDERID' => trim($session_data['refenfant'] . '_' . $session_data['fonds']),
            'AMOUNT' => $final_amount * 100,
            'CURRENCY' => 'CHF',
            'LANGUAGE' => $lang,
            'CN' => $session_data['first_name'] . ' ' . $session_data['last_name'],
            'EMAIL' => $session_data['email'],
            'COMPLUS' => $_SESSION['transaction'],
            'PARAMPLUS' => 'campaign_slug='.$_SESSION['campaign_slug'],
            'ACCEPTURL' =>  $base_address .'/confirmation-don',
            'DECLINEURL' => $base_address .'/annulation-don',
            'EXCEPTIONURL' => $base_address .'/annulation-don',
            'CANCELURL' => $base_address .'/annulation-don',        );

        if($_SESSION['count_runs']==0) {
            $_SESSION['count_runs']++;

            $wpdb->insert(
                    $wpdb->prefix . DONATION_TABLE_NAME,
                    array(
                        'time' => date('Y-m-d H:i:s'),
                        'ip_address' => $_SERVER['REMOTE_ADDR'],
                        'email' => $this->cleanfordb($data['email']),
                        'orderid' => $this->cleanfordb($session_data['refenfant'] . '_' . $session_data['fonds']),
                        'utm_source' => $this->cleanfordb($_SESSION['utm_source']),
                        'utm_medium' => $this->cleanfordb($_SESSION['utm_medium']),
                        'utm_campaign' => $this->cleanfordb($_SESSION['utm_campaign']),
                        'first_name' => $this->cleanfordb($data['first_name']),
                        'last_name' => $this->cleanfordb($data['last_name']),
                        'street' => $this->cleanfordb($data['street']),
                        'zipcode' => $this->cleanfordb($data['zipcode']),
                        'city' => $this->cleanfordb($data['city']),
                        'country' => $this->cleanfordb($data['country']),
                        'language' => $lang,
                        'amount' => $final_amount,
                        'currency' => 'CHF',
                        'fund' => $this->cleanfordb($session_data['fonds']),
                        'child_id' => $this->cleanfordb($session_data['refenfant']),
                        'partner_ref' => $this->cleanfordb($session_data['partner_ref']),
                        'transaction_id' => $transaction,
                        'session_id' => session_id(),
                        'odoo_status' => self::SUBMITTED_TO_PF,
                    )
                );
        }

        //generate hash string
        $arrayToHash = array();
        foreach ($form as $key => $value) {
            if ($value != '') {
                $arrayToHash[] = strtoupper($key) . '=' . $value . POSTFINANCE_SHA_IN;
            }
        }
        asort($arrayToHash);
        $stringToHash = implode('', $arrayToHash);
        $hashedString = sha1($stringToHash);

        ?>
        <html>
            <head><title>Redirecting to Postfinance...</title></head>
            <body>
                <form action="<?=POSTFINANCE_URL?>/orderstandard_utf8.asp" method="post" name="pf_for_contact_form">
                    <input type="hidden" name="PSPID" value="<?php echo $form['PSPID']; ?>">
                    <input type="hidden" name="ORDERID" value="<?php echo $form['ORDERID']; ?>">
                    <input type="hidden" name="PARAMPLUS" value="<?php echo $form['PARAMPLUS']; ?>">
                    <input type="hidden" name="AMOUNT" value="<?php echo $form['AMOUNT']; ?>">
                    <input type="hidden" name="CURRENCY" value="<?php echo $form['CURRENCY']; ?>">
                    <input type="hidden" name="LANGUAGE" value="<?php echo $form['LANGUAGE']; ?>">
                    <input type="hidden" name="CN" value="<?php echo $form['CN']; ?>">
                    <input type="hidden" name="EMAIL" value="<?php echo $form['EMAIL']; ?>">
                    <input type="hidden" name="COMPLUS" value="<?php echo $form['COMPLUS']; ?>">
                    <input type="hidden" name="ACCEPTURL" value="<?php echo $form['ACCEPTURL']; ?>">
                    <input type="hidden" name="DECLINEURL" value="<?php echo $form['DECLINEURL']; ?>">
                    <input type="hidden" name="EXCEPTIONURL" value="<?php echo $form['EXCEPTIONURL']; ?>">
                    <input type="hidden" name="CANCELURL" value="<?php echo $form['CANCELURL']; ?>">
                    <input type="hidden" name="SHASIGN" value="<?php echo $hashedString; ?>">
                </form>
                <script type="text/javascript">
                    document.pf_for_contact_form.submit();
                </script>
            </body>
        </html>
        <?php
    }

    /**
     * Generate shortcode for dealing with a PostFinance transaction.
     *
     * @return string
     */
    public function shortcode_confirmation($atts, $content) {

        $atts = shortcode_atts(array(), $atts);

        ob_start();

        if ($_SERVER['HTTP_HOST'] == TEST_SERVER) {

//        $debug = false;
            $debug = true;
            if ($debug) {
                print_r($_GET);
            }
        } else {

            $debug = false;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . DONATION_TABLE_NAME;

        if (isset($_GET['COMPLUS']) AND strlen($_GET['COMPLUS']) == 36) {

            error_log('Process to export to Odoo is running now... ');

            $complus = filter_var($_GET['COMPLUS'], FILTER_SANITIZE_STRING);


            $time_transaction = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT time FROM " . $table_name . " "
                    . "WHERE transaction_id='%s' "
                    . "AND odoo_status = '" . self::SUBMITTED_FROM_PF . "'"
                    . "LIMIT 1", $complus));

            // return from Postfinance should occur within 5 minutes, otherwise we ignore the db update.
            if ($debug) {
                $additional_time = 1500000;
            } else {
                $additional_time = 72000;
            }
            if (time() < (strtotime($time_transaction) + $additional_time )) {

                if ($debug) {
                    echo 'continue donation process...';
                }
                error_log('Update transaction with parameters received from Postfinance');

                $wpdb->update($table_name, array(
                    'pf_pm' => $_GET['PM'],
                    'pf_payid' => $_GET['PAYID'],
                    'pf_brand' => $_GET['BRAND'],
                    'pf_raw' => json_encode($_GET),
                    'ip_address' => $_GET['IP'],
                    'odoo_status' => self::RECEIVED_FROM_PF
                ), array('transaction_id' => $complus,
                        'odoo_complete_time' => NULL)
                );

                unset($_SESSION['transaction']);

//        } else {
//            wp_die('Request timeout. ');
            }
        }


        if(isset($_GET) OR isset($_POST)) {

            error_log('Looking for payments to export...');

            $results = $wpdb->get_results("SELECT * FROM " . $table_name . " WHERE odoo_status = '" . self::RECEIVED_FROM_PF . "'");

            if ($debug) {
                print_r($results);
            }

            try {
                if (sizeof($results) >= 1) {

                    $odoo = new CompassionOdooConnector($debug);

                    foreach ($results as $result) {

                        unset($search_partner);
                        unset($partner);
                        $partner_id = '';

                        try {

                            if ($result->email != '' AND strlen(trim($result->email))>=5 AND $result->child_id != '' AND strlen($result->child_id)>=8) {

                                $search = $odoo->searchContractByPartnerEmailChildCode($result->email, $result->child_id);
                                $partner_id = $search[0]['partner_id'][0];
                            }

                            if (empty($partner_id)) {
                                error_log('Partner ID not found with email and child_id');
                                throw new Exception('foo');
                            }

                        } catch (Exception $e) {

                            if ($result->last_name != '' AND strlen(trim($result->last_name))>=3 AND $result->child_id != '' AND strlen($result->child_id)>=8) {

                                $search = $odoo->searchContractByPartnerLastNameChildCode($result->last_name, $result->child_id);
                                if (!empty($search)) {
                                    $partner_id = $search[0]['partner_id'][0];

                                    if($debug) {
                                        echo ' ###'.$partner_id.'### ';
                                    }
                                }
                            }

                            if(empty($partner_id)) {

                                error_log('Searching partner with email, last_name, first_name, ...');
                                $search = $odoo->searchPartnerByEmailNameCity($result->email, $result->last_name, $result->first_name, $result->city);
                                if($debug) {
                                    print_r($search);
                                }
                                if (!empty($search)) {
                                    $partner_id = $search[0];
                                    if($debug) {
                                        echo ' ##'.$partner_id.'## ';
                                    }
                                }
                            }

                            if(empty($partner_id)) {

                                error_log('Let\'s create a partner');
                                $partner_id = $odoo->createPartner(
                                    $result->last_name, $result->first_name, $result->street, $result->zipcode, $result->city, $result->email, $result->country, $result->language);
                            }
                        }

                        if (!empty($partner_id)) {

//                            $invoice_id = $odoo->createInvoiceWithObjects(
//                                    $partner_id, date('Y-m-d H:i:s'), 'survie', '12.34', 'CHF', 'fund', $child_code, 'CreditCard', '1234567890', 'Mastercard');

                            $invoice_id = $odoo->createInvoiceWithObjects(
                                $partner_id, $result->orderid, $result->amount, $result->fund, $result->child_id,
                                $result->pf_pm, $result->pf_payid, $result->pf_brand, $result->utm_source,
                                $result->utm_medium, $result->utm_campaign);

                            if($debug) {
                                print_r($invoice_id);
                            }
                            if (!empty($invoice_id)) {

                                $wpdb->update($table_name, array(
                                    'odoo_status' => self::INVOICED,
                                    'odoo_invoice_id' => $invoice_id,
                                    'odoo_complete_time' => date('Y-m-d H:i:s'),
                                ), array('transaction_id' => $result->transaction_id)
                                );
                            }
                        }
                    }
                }
            } catch (Exception $ex) {

                echo 'Error occured. Please try again. ';
                if ($debug) {
                    print_r($ex);
                }
            }
        }

        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}

new Compassion_Donation_Form();