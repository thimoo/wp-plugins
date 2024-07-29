<?php

/*
 * Plugin Name: Compassion Connector for Odoo
 * Description: Envoie les parrainages et les dons à Odoo via XMLRPC
 * Version:     14.0.1.0.0
 * Author:      Compassion Suisse
 */
defined('ABSPATH') || die();


require_once 'vendor/autoload.php';

use \Ripcord\Ripcord as Ripcord;
use \Ripcord\Client\Transport\Stream as Ripcord_Transport_Stream;

class CompassionOdooConnector {

    private $odoo_host = ODOO_HOST;
    private $odoo_db = ODOO_DB;
    private $odoo_user = ODOO_USER;
    private $odoo_password = ODOO_PASSWORD;
    private $uid;
    private $models;
    
    public function __construct() {
        $common = ripcord::client($this->odoo_host . '/xmlrpc/2/common');
        $transport = new ripcord_Transport_Stream(array(
            'timeout' => 5 // in seconds.
        ));
        $this->uid = $common->authenticate($this->odoo_db, $this->odoo_user, $this->odoo_password, array());
        $this->models = ripcord::client("$this->odoo_host/xmlrpc/2/object", null, $transport);
    }

    /**
     * Send the raw information about a donation to Odoo.
     */
    public function send_donation_info($donnation_infos) {
        return $this->call_method('account.move', 'process_wp_confirmed_donation', array($donnation_infos));
    }

     /**
     * Generic function to call any method on Odoo
     * @param $model   string: the name of odoo model
     * @param $method  string: the name of the method
     * @param $params  array:  parameters to the odoo function
     * @return mixed   the result of the method
     */
    public function call_method($model, $method, $params) {
        return $this->models->execute_kw($this->odoo_db, $this->uid, $this->odoo_password, $model, $method, $params);
    }

}
