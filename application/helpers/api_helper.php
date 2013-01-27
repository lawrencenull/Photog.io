<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('output_json')) {
    function output_json( $data ) {

    	$ci =& get_instance();
		if ( $ci->input->get('format', true) == 'json' ) {
			// $ci->output->set_header('application/json');
			// $ci->output->set_content_type('application/json');
			header("Content-type: application/json");
			echo json_encode( $data );
			die();			
		}
        
    }
}

if ( ! function_exists('get_json_url')) {
	function get_json_url( $user_url ) {
		$ci =& get_instance();
		$ci->load->library('simple_html_dom');

		if ( stripos( $user_url, 'http://' ) !== 0 ) {
			$user_url = 'http://' . $user_url;
		}

		$html = @file_get_html( $user_url );
		if ( ! $html ) {
			return false;
		}
		$elements = $html->find('link[type="application/json"]');

		if ( count($elements) === 1 && $elements[0]->rel == "alternate" ) {
			$json_url = $elements[0]->href;
			return $json_url;
		} else {
			return false;
		}
	}
}