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

define('CF7PF_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
define('CF7PF_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));

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

        $this->register_shortcode_donation_form();
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
            case 'redirect';
                $this->send_data($data);
//                session_destroy();
                break;
        }
    }

    public function register_shortcode_donation_form() {
        add_shortcode('donation-form', array($this, 'shortcode'));
        if(function_exists('shortcode_ui_register_for_shortcode')) {
            $args = array(
                'label' => __('Donation form', 'donation-form'),
                'listItemImage' => 'dashicons-heart',
                'attrs' => array(
                    array(
                        'label' => __('Type of form', 'donation-form'),
                        'attr' => 'form',
                        'description' => __('The type of donation form to display.', 'donation-form'),
                        'type' => 'select',
                        'options' => array(
                            array('value' => 'donation', 'label' => __('General funds', 'donation-form')),
                            array('value' => 'csp', 'label' => __('CSP', 'donation-form')),
                            array('value' => 'cadeau', 'label' => __('Gift to a child', 'donation-form')),
                            array('value' => 'single', 'label' => __('Specific fund', 'donation-form')),
                        ),
                    ),
                    array(
                        'label' => __('Reason for the donation', 'donation-form'),
                        'attr' => 'motif',
                        'description' => sprintf(__('Only useful when the form type is : "%s". ' .
                                            'The first part is the displayed reason. ' .
                                            'The second part (separated by a "|") is the reason used by Odoo (corresponds to the internal reference of a product).', 'donation-form'), __('Specific fund', 'donation-form')),
                        'type' => 'text',
                        'meta' => array(
                            'placeholder' => __( 'Toilettes for all|toilette', 'donation-form'),
                            'data-test' => 1,
                        ),
                    ),
                ),
            );
            shortcode_ui_register_for_shortcode('donation-form', $args);
        }
    }

    /**
     * Generate the donation-form shortcode.
     *
     * This shortcode adds a donation form to the calling page, It also process the form after its submitted.
     *
     * There MUST BE at most one donation form by page. Otherwise undefined behaviour occurs.
     *
     * Several type of donation are supported, the user can choose between them by setting the form attribute:
     *  - 'donation' displays a form to donate to one of several fund.
     *  - 'csp' displays a form to donate (once or monthly) for the welfare of unborn child and their mother.
     *  - 'single' displays a form to donate to a specified found (set in the motif attribute).
     *      - The motif attribute is separated by '|'. The first part is the displayed reason for the donation, the
     *        second part is parsed by odoo to categorize the donation.
     *  - 'cadeau' and any other values display a form to offer a gift to a specific child.
     *
     * Once a donation has been submitted, there is a redirection to Postfinance for the payment.
     *
     * Exemple:
     *
     *      [donation-form form=single motif="Toillette pour tous|toilette"]
     *
     * @return string
     */
    public function shortcode($atts, $content) {

        $this->step = (isset($_GET['step'])) ? $_GET['step'] : 'form';
        $atts = shortcode_atts(array(
                'form' => '',
                'motif' => '',
            ), $atts);

        /**
         * process form data
         */
        $this->process_form_data($_POST);

        /**
         * load template
         */
        ob_start();

        switch ($atts['form']) {
            case 'donation':
                $donation_inputs_template = plugin_dir_path(__FILE__) . 'templates/frontend/inputs.php';
                $bank_transfer_comment = __('Vielen Dank, dass du nicht vergisst, den Spendenzweck zu erwähnen.','donation-form' );
                break;
            case 'csp':
                $donation_inputs_template = plugin_dir_path(__FILE__) . 'templates/csp/inputs.php';
                $bank_transfer_comment = __('Bitte gib an, ob du regelmässig oder einmalig für das Kinder-Überlebensprogramm spenden möchtest. Spendenzweck (monatlich oder einmalig): Überlebensprogramm', 'donation-form');
                $bank_transfer_reason = '<tspan x="0" dy="0">' . __('Überlebensprogramm', 'donation-form') . ' :</tspan>' .
                                        '<tspan x="0" dy="1.4em"> ☐ ' . __('monatliche Spende', 'donation-form') . '</tspan>' .
                                        '<tspan x="0" dy="1.4em"> ☐ ' . __('einmalige Spende', 'donation-form') . '</tspan>';
                break;
            case 'single':
                $donation_inputs_template = plugin_dir_path(__FILE__) . 'templates/single/inputs.php';
                if ($atts['motif']) {
                    $parts = explode('|', $atts['motif']);
                    $fonds = $parts[1];
                    $bank_transfer_reason = $parts[0];
                    $bank_transfer_comment = __('Vielen Dank, dass du nicht vergisst, den Spendenzweck zu erwähnen.','donation-form' );                }
                break;
            case 'cadeau':
            default:
                $bank_transfer_comment = __('Vielen Dank, dass du nicht vergisst, den Spendenzweck zu erwähnen.','donation-form' );
                $donation_inputs_template = plugin_dir_path(__FILE__) . 'templates/cadeau/inputs.php';
                break;
        }
        include("templates/donation-$this->step.php");

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
            $lang = 'fr_CH';
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
        $session_data['fonds'] = $from_csp ? 'csp' : $session_data['fonds'];
        $session_data['choix_don_unique_mensuel'] = $from_csp ? 'monthly' : $session_data['choix_don_unique_mensuel'];

        // Form data to send to postfinance (ogone)
        $base_address = 'https://' . $_SERVER['HTTP_HOST'] . '/' . $my_current_lang;
        $form = array(
            'PSPID' => 'compassion_yp',
//             'ORDERID' => trim($session_data['refenfant'].'_' .$session_data['fonds']),
            'ORDERID' => trim($session_data['refenfant'] .' '.  $session_data['choix_don_unique_mensuel'].' '.$session_data['fonds']),
            'AMOUNT' => $final_amount * 100,
            'CURRENCY' => 'CHF',
            'LANGUAGE' => $lang,
            'CN' => $session_data['first_name'] . ' ' . $session_data['last_name'],
            'EMAIL' => $session_data['email'],
            'COMPLUS' => $_SESSION['transaction'],
            'PAYMENT_REFERENCE' => $_SESSION['choix_don_unique_mensuel'],
            'PARAMPLUS' => 'campaign_slug='.$_SESSION['campaign_slug'],
            'ACCEPTURL' =>  $base_address . '/confirmation-don',
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
                        'orderid' => $this->cleanfordb($session_data['refenfant'] .' '.  $session_data['choix_don_unique_mensuel'].' '.  $session_data['fonds']),
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

        //redirect thank you page for Christmas only
       if($this->cleanfordb($session_data['fonds'])=='noel'){$form['ACCEPTURL'] = $base_address . '/confirmation-don-noel';}

        $sha_sign = self::compute_pf_sha_sign($form)

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
                    <input type="hidden" name="SHASIGN" value="<?php echo $sha_sign; ?>">
                </form>
                <script type="text/javascript">
                    document.pf_for_contact_form.submit();
                </script>
            </body>
        </html>
        <?php
    }

    private static function compute_pf_sha_sign($form) {
        $arrayToHash = array();
        foreach ($form as $key => $value) {
            if ($value != '') {
                $arrayToHash[] = strtoupper($key) . '=' . $value . POSTFINANCE_SHA_IN;
            }
        }
        asort($arrayToHash);
        $stringToHash = implode('', $arrayToHash);
        $hashedString = sha1($stringToHash);

        return $hashedString;
    }

    /**
     * The sorted names of the parameters that can be sent by PostFinance after a successful payment.
     * https://e-payment-postfinance.v-psp.com/~/media/kdb/integration%20guides/sha-out_params.ashx
     */
    const PF_CORRECT_PARAMETERS = array(
        'AAVADDRESS',      'AAVCHECK',              'AAVMAIL',             'AAVNAME',
        'AAVPHONE',        'AAVZIP',                'ACCEPTANCE',          'ALIAS',
        'AMOUNT',          'BIC',                   'BIN',                 'BRAND',
        'CARDNO',          'CCCTY',                 'CN',                  'COLLECTOR_BIC',
        'COLLECTOR_IBAN',  'COMPLUS',               'CREATION_STATUS',     'CREDITDEBIT',
        'CURRENCY',        'CVCCHECK',              'DCC_COMMPERCENTAGE',  'DCC_CONVAMOUNT',
        'DCC_CONVCCY',     'DCC_EXCHRATE',          'DCC_EXCHRATESOURCE',  'DCC_EXCHRATETS',
        'DCC_INDICATOR',   'DCC_MARGINPERCENTAGE',  'DCC_VALIDHOURS',      'DEVICEID',
        'DIGESTCARDNO',    'ECI',                   'ED',                  'EMAIL',
        'ENCCARDNO',       'FXAMOUNT',              'FXCURRENCY',          'IP',
        'IPCTY',           'MANDATEID',             'MOBILEMODE',          'NBREMAILUSAGE',
        'NBRIPUSAGE',      'NBRIPUSAGE_ALLTX',      'NBRUSAGE',            'NCERROR',
        'ORDERID',         'PAYID',                 'PAYIDSUB',            'PAYMENT_REFERENCE',
        'PM',              'SCO_CATEGORY',          'SCORING',             'SEQUENCETYPE',
        'SIGNDATE',        'STATUS',                'SUBBRAND',            'SUBSCRIPTION_ID',
        'TICKET',          'TRXDATE',               'VC'
    );

    public static function is_verified_pf_sha_sign($form) {
        $upperized = array();
        foreach ($form as $key => $value) {
            $upperized[strtoupper($key)] = $value;
        }

        foreach (self::PF_CORRECT_PARAMETERS as $key) {
            $value = $upperized[$key] ?? '';
            if ($value != '') {
                $arrayToHash[] = $key . '=' . $value . POSTFINANCE_SHA_OUT;
            }
        }

        $stringToHash = implode('', $arrayToHash);
        $hashedString = sha1($stringToHash);

        return strtoupper($hashedString) == strtoupper($form['SHASIGN']);
    }

    /**
     * Generate shortcode for dealing with a PostFinance transaction.
     *
     * @return string
     */
    public function shortcode_confirmation($atts, $content) {

        $atts = shortcode_atts(array(), $atts);

        ob_start();

        global $wpdb;
        $table_name = $wpdb->prefix . DONATION_TABLE_NAME;

        // Donation info has been received.
        if (isset($_GET['COMPLUS']) AND strlen($_GET['COMPLUS']) == 36) {

            $complus = filter_var($_GET['COMPLUS'], FILTER_SANITIZE_STRING);

            // Check if donation infos truly comes from PostFinance
            if(Compassion_Donation_Form::is_verified_pf_sha_sign($_GET)) {
                error_log('Update transaction with parameters received from Postfinance');

                $status = self::RECEIVED_FROM_PF;
            } else {
                // Two possibilities:
                //   - An attempt at hacking with a falsified message.
                //   - The POSTFINANCE_SHA_OUT constant does not match the real one.
                // In case of the second possibility, keep the donation in the db but with a verification_error status.

                error_log('Received falsified donation info. Please check that the POSTFINANCE_SHA_OUT constant is '
                    . 'correct.');

                $status = self::VERIFICATION_ERROR;
            }

            $wpdb->update($table_name, array(
                'pf_pm' => $_GET['PM'],
                'pf_payid' => $_GET['PAYID'],
                'pf_brand' => $_GET['BRAND'],
                'pf_raw' => json_encode($_GET),
                'ip_address' => $_GET['IP'],
                'odoo_status' => $status
            ), array('transaction_id' => $complus,
                    'odoo_complete_time' => NULL)
            );

            unset($_SESSION['transaction']);
        }


        if(isset($_GET) OR isset($_POST)) {

            error_log('Looking for donation(s) to export...');

            $results = $wpdb->get_results("SELECT * FROM " . $table_name . " WHERE odoo_status = '" . self::RECEIVED_FROM_PF . "'");
            if (sizeof($results) >= 1) {

                $odoo = new CompassionOdooConnector();

                foreach ($results as $result) {

                    unset($invoice_id);

                    try {
                        $invoice_id = $odoo->send_donation_info($result);
                        if (!empty($invoice_id)) {
                            $wpdb->update($table_name, array(
                                    'odoo_status' => self::INVOICED,
                                    'odoo_invoice_id' => $invoice_id,
                                    'odoo_complete_time' => date('Y-m-d H:i:s'),
                                ), array('transaction_id' => $result->transaction_id)
                            );
                        } else {
                            error_log("Donation '$result->transaction_id' did not receive an invoice_id.");
                        }
                    } catch (Exception $e) {
                        error_log("Error while exporting donation '$result->transaction_id' to odoo.");
                    }
                }
            }
        }

        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}

new Compassion_Donation_Form();
