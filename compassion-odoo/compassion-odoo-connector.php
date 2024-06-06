<?php

/*
 * Plugin Name: Compassion Connector to Odoo V10 only
 * Description: ATTENTION : ne fonctionne que avec Odoo en <strong>V10 </strong> Les 2 connecteurs Odoo ne peuvent pas être actifs en même temps et doivent être activés <strong>après</strong> "Compassion Letters"
 * Version:     10.0.1
 * Author:      Compassion Suisse | J. Kläy
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
     * Functions for XMLRPC sending to Odoo
     */
    public function getPartnerById($partner_id) {
        // Call method in Odoo to insert new web letter
        $res = $this->models->execute_kw($this->odoo_db, $this->uid, $this->odoo_password, 'res.partner', 'search_read', array(
            array(
                array('id', '=', $partner_id),
            )
                )
        );
        error_log(serialize($res));

        if ($res and $res->faultString) {
            return false;
        }
        return $res;
    }

    public function reserveChild($local_id) {
        global $wpdb;
        // get hold in odoo
        $wished_hold_type = 'No Money Hold';
        $child_id = $this->models->execute_kw($this->odoo_db, $this->uid, $this->odoo_password,
            'compassion.child', 'search_read', array(array(
                array('local_id', '=', $local_id)),
                array('fields'=>array('id'))))[0]['id'];

        $hold_ids = $this->getHoldByChildId($child_id);

        if (!is_null($hold_ids)) {
            foreach ($hold_ids as $index => $hold) {
                $hold_id = $hold['id'];

                // setting expiration date in one week
                // (8 days to have a little margin for the cron that removes expired childs)
                $date = new DateTime('8 days');
                $expiration_date = $date->format('Y-m-d H:i:s');

                // writing this in odoo
                $this->models->execute_kw($this->odoo_db, $this->uid, $this->odoo_password,
                    'compassion.hold', 'write', array(array($hold_id), array(
                        'type' => $wished_hold_type,
                        'expiration_date' => $expiration_date,
                    )));
                $final_hold_type = $this->getHoldByChildId($child_id)[$index]['type'];

                // reserve child in wordpress
                if ($wished_hold_type == $final_hold_type) { // test if changed in odoo
                    $posts_id = $this->getPostsIdByLocalId($local_id);
                    foreach ($posts_id as $post_id) {
                        $querystr = "UPDATE $wpdb->postmeta 
                      SET meta_value = 'true' 
                      WHERE post_id = $post_id AND meta_key = '_child_reserved'";
                        $res = $wpdb->query($querystr);
                        if (!$res) {
                            error_log('error setting child reservation metadata to true');
                        }

                        $expiration_date = new DateTime('7 days');
                        $expiration_date = $expiration_date->format('Y-m-d');

                        $querystr = "UPDATE $wpdb->postmeta 
                      SET meta_value = '$expiration_date' 
                      WHERE post_id = $post_id AND meta_key = '_child_reserved_expiration'";
                        $res = $wpdb->query($querystr);
                        if (!$res) {
                            error_log('error setting child reservation expiration date');
                        }
                    }
                } else {
                    error_log('error changing child hold type in odoo');
                }
            }
        } else {
            error_log('error getting hold id from odoo');
        }
    }

    public function getHoldByChildId($child_id) {
        return $this->models->execute_kw($this->odoo_db, $this->uid, $this->odoo_password,
            'compassion.hold', 'search_read',
            array(array(array('child_id', '=', $child_id))),
            array('fields'=>array('hold_id', 'type')));
    }

    public function getPostsIdByLocalId($local_id) {
        global $wpdb;
        $querystr = "SELECT post_id FROM compassion_postmeta WHERE meta_value = '$local_id'";
        $res = $wpdb->get_results($querystr);

        $a = array();
        foreach ($res as $row) {
            array_push($a, $row->post_id);
        }
        return $a;
    }

    public function searchPartnerByPartnerRefCity($partner_ref, $city) {

        // Call method in Odoo to insert new web letter
        $res = $this->models->execute_kw($this->odoo_db, $this->uid, $this->odoo_password, 'res.partner', 'search_read', array(
            array(
                array('ref', '=', $partner_ref),
                array('city', 'ilike', $city),
            )
                )
        );
        error_log(serialize($res));

        if ($res and $res->faultString) {
            return false;
        }
        return $res;
    }

    public function searchContractByPartnerRefChildCode($partner_ref, $child_code) {
        
        $search_contracts = $this->models->execute_kw($this->odoo_db, $this->uid, $this->odoo_password, 'recurring.contract', 'search_count', array(
            array(  
                    array('partner_id', '=', trim($partner_ref)),
                    array('child_code', '=', trim(strtoupper($child_code))),
            )));
        
        if ($search_contracts > 0) {
            $search_contract = $this->models->execute_kw($this->odoo_db, $this->uid, $this->odoo_password, 'recurring.contract', 'search_read', array(
                array( //filter
                    array('partner_id', '=', trim($partner_ref)),
                    array('child_code', '=', trim(strtoupper($child_code))),
                ),
                array('name', 'partner_codega', 'partner_id', 'child_code', 'reference'), //fields
                0, //offset
                1, //limit
                'create_date DESC ' //order
                ));
            return $search_contract;
        }
        return false;
    }

    public function searchContractByPartnerLastNameChildCode($last_name, $child_code) {
        
        $search_contracts = $this->models->execute_kw($this->odoo_db, $this->uid, $this->odoo_password, 'recurring.contract', 'search_count', array(
            array(  
                    array('partner_id.lastname', 'ilike', trim($last_name)),
                    array('child_code', '=', trim(strtoupper($child_code))),
            )));
        
        if ($search_contracts > 0) {
            $search_contract = $this->models->execute_kw($this->odoo_db, $this->uid, $this->odoo_password, 'recurring.contract', 'search_read', array(
                array( //filter
                    array('partner_id.lastname', 'ilike', trim($last_name)),
                    array('child_code', '=', trim(strtoupper($child_code))),
                ),
                array('name', 'partner_codega', 'partner_id', 'child_code', 'reference'), //fields
                0, //offset
                1, //limit
                'create_date DESC ' //order
                ));
            return $search_contract;
        }
        return false;
    }

    public function searchContractByPartnerEmailChildCode($email, $child_code) {
        
        $search_contracts = $this->models->execute_kw($this->odoo_db, $this->uid, $this->odoo_password, 'recurring.contract', 'search_count', array(
            array(  
                    array('partner_id.email', 'ilike', trim($email)),
                    array('child_code', '=', trim(strtoupper($child_code))),
            )));
        
        if ($search_contracts > 0) {
            $search_contract = $this->models->execute_kw($this->odoo_db, $this->uid, $this->odoo_password, 'recurring.contract', 'search_read', array(
                array( //filter
                    array('partner_id.email', 'ilike', trim($email)),
                    array('child_code', '=', trim(strtoupper($child_code))),
                ),
                array('name', 'partner_codega', 'partner_id', 'child_code', 'reference'), //fields
                0, //offset
                1, //limit
                'create_date DESC ' //order
                ));
            return $search_contract;
        }
        return false;
    }

    
    public function searchPartnerByEmailNameCity($email, $last_name, $first_name, $city) {

        $search_partner = $this->models->execute_kw($this->odoo_db, $this->uid, $this->odoo_password, 'res.partner', 'search_count', array(
            array(
                array('email', 'ilike', $email),
                array('lastname', 'ilike', $last_name),
                array('firstname', 'ilike', $first_name),
                array('city', 'ilike', $city),
                '|', array('active', '=', true), array('active', '=', false)
            )
                )
        );
        
        if ($search_partner == 1) {

            return $this->models->execute_kw($this->odoo_db, $this->uid, $this->odoo_password, 'res.partner', 'search', array(
                        array(
                            array('email', 'ilike', $email),
                            array('lastname', 'ilike', $last_name),
                            array('firstname', 'ilike', $first_name),
                            array('city', 'ilike', $city),
                            '|', array('active', '=', true), array('active', '=', false)
                        )
                            )
            );
        } else {

            $search_partner = $this->models->execute_kw($this->odoo_db, $this->uid, $this->odoo_password, 'res.partner', 'search_count', array(
                array(
                    array('email', 'ilike', $email),
                    array('lastname', 'ilike', $last_name),
                    array('firstname', 'ilike', $first_name),
                    '|', array('active', '=', true), array('active', '=', false)
                )
                    )
            );

            if ($search_partner == 1) {
                return $this->models->execute_kw($this->odoo_db, $this->uid, $this->odoo_password, 'res.partner', 'search', array(
                            array(
                                array('email', 'ilike', $email),
                                array('lastname', 'ilike', $last_name),
                                array('firstname', 'ilike', $first_name),
                                '|', array('active', '=', true), array('active', '=', false)
                            )
                                )
                );
            } else {

                $search_partner = $this->models->execute_kw($this->odoo_db, $this->uid, $this->odoo_password, 'res.partner', 'search_count', array(
                    array(
                        array('email', 'ilike', $email),
                        array('lastname', 'ilike', $last_name),
                        '|', array('active', '=', true), array('active', '=', false)
                    )
                        )
                );

                if ($search_partner == 1) {
                    return $this->models->execute_kw($this->odoo_db, $this->uid, $this->odoo_password, 'res.partner', 'search', array(
                                array(
                                    array('email', 'ilike', $email),
                                    array('lastname', 'ilike', $last_name),
                                    '|', array('active', '=', true), array('active', '=', false)
                                )
                                    )
                    );
                } else {

                    $search_partner = $this->models->execute_kw($this->odoo_db, $this->uid, $this->odoo_password, 'res.partner', 'search_count', array(
                        array(
                            array('email', 'ilike', $email),
                            '|', array('active', '=', true), array('active', '=', false)
                        )
                            )
                    );

                    if ($search_partner == 1) {
                        return $this->models->execute_kw($this->odoo_db, $this->uid, $this->odoo_password, 'res.partner', 'search', array(
                                    array(
                                        array('email', 'ilike', $email),
                                        '|', array('active', '=', true), array('active', '=', false)
                                    )
                                        )
                        );
                    }
                    return false;
                }
                return false;
            }
            return false;
        }
        return false;
    }

    public function createPartner($last_name, $first_name, $street, $zipcode, $city, $email, $country, $language) {

        $odoo_countries = array();
        $odoo_countries['Schweiz'] = 44;
        $odoo_countries['Suisse'] = 44;
        $odoo_countries['Deutschland'] = 58;
        $odoo_countries['Allemagne'] = 58;
        $odoo_countries['Österreich'] = 13;
        $odoo_countries['Autriche'] = 13;
        $odoo_countries['Frankreich'] = 76;
        $odoo_countries['France'] = 76;
        $odoo_countries['Italien'] = 110;
        $odoo_countries['Italie'] = 110;


        $new_partner = $this->models->execute_kw($this->odoo_db, $this->uid, $this->odoo_password, 'res.partner', 'create', array(
            array(
                'customer' => true,
                'lastname' => stripslashes(ucfirst(trim($last_name))),
                'firstname' => stripslashes(ucfirst(trim($first_name))),
                'street' => stripslashes(ucfirst(trim($street))),
                'zip' => stripslashes(trim($zipcode)),
                'city' => stripslashes(ucfirst(trim($city))),
                'country_id' => $odoo_countries[$country],
                'lang' => str_replace('it_CH', 'it_IT', str_replace('_FR', '_CH', str_replace('de_CH', 'de_DE', trim($language)))),
                'email' => stripslashes(trim($email)),
                'state' => 'pending',
            )
        ));

        return $new_partner;
    }

    public function createInvoiceWithObjects($partner_id, $origin, $amount, $fund, $child_id, $pf_pm, $pf_payid, $pf_brand, $utm_source, $utm_medium, $utm_campaign) {

        $payment_mode = trim( ($pf_pm!=$pf_brand ? $pf_pm.' '.$pf_brand : $pf_brand) );
        return $this->models->execute_kw($this->odoo_db, $this->uid, $this->odoo_password, 'account.move', 'create_from_wordpress', array(
            $partner_id, $origin, $amount, $fund, $child_id, $pf_payid, $payment_mode, $utm_source, $utm_medium, $utm_campaign
        ));
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
