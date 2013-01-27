<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('do_404')) {
    function do_404() {
    	header("HTTP/1.1 404 Not Found");
    	include( APPPATH . 'controllers/error.php' );
    	$error = new Error();
    	$error->error_404();
    }
}