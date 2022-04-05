<?php

/**
 * ServerConnect for WHMCS
 *
 * An alternative for WHMCS Connect that actually works in Google Chrome.
 * 
 * Same functionality (filtering, sorting by groups, server logos).
 * 
 * Left click the server to open it in the same tab, right click to open in a new tab. 
 *
 * @package    WHMCS
 * @author     Lee Mahoney <lee@leemahoney.dev>
 * @copyright  Copyright (c) Lee Mahoney 2022
 * @license    MIT License
 * @version    0.0.1
 * @link       https://leemahoney.dev
 */

# Prevent direct loading of the script
if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}


/**
 * serverconnect_config
 *
 * @return void
 */
function serverconnect_config() {

    return [
        'name'          => 'ServerConnect for WHMCS',
        'description'   => 'An alternative for WHMCS Connect that actually works in Google Chrome.',
        'version'       => '0.0.1',
        'author'        => 'Lee Mahoney'
    ];

}

/**
 * serverconnect_activate
 *
 * @return void
 */
function serverconnect_activate() {

    return [
        'status' => 'success'
    ];

}

/**
 * serverconnect_output
 *
 * @return void
 */
function serverconnect_output() {
    include 'main.php';
}