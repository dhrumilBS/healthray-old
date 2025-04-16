<?php

/**
 * Plugin Name: ABHA Card Create
 * Version: 1.1
 * Author: Dhrumil 
 * Text Domain: abha-card-create  
 */



function create_abha_card_table()
{
    global $wpdb;
    $abha_PHR_card = $wpdb->prefix . "abha_phr_card";
    $abha_cards = $wpdb->prefix . "abha_cards";
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $abha_PHR_card (
    id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        aadhaar_number VARCHAR(12) NOT NULL,
        abha_number VARCHAR(14) NOT NULL,
        transaction_id VARCHAR(255) NOT NULL,
        mobile_number VARCHAR(15) NOT NULL,
        first_name VARCHAR(255) NOT NULL,
        middle_name VARCHAR(255) DEFAULT NULL,
        last_name VARCHAR(255) NOT NULL,
        gender ENUM('Male', 'Female', 'Other') NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY (aadhaar_number),
        UNIQUE KEY (abha_number)
    ) $charset_collate;";
    $sql2 = "CREATE TABLE IF NOT EXISTS $abha_cards  (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        aadhaar_number VARCHAR(12) NOT NULL,
        number VARCHAR(15) NOT NULL,
        image VARCHAR(255) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    dbDelta($sql2);
}
register_activation_hook(__FILE__, 'create_abha_card_table');
