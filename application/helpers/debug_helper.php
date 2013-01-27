<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('debug')) {
    function debug( $data ) {

        echo '<pre>';
        print_r($data);
        echo '</pre>';
        
    }
}