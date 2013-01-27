<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('require_auth')) {
    function require_auth( $role = null ) {

        $ci =& get_instance();

        if ( $role == 'admin' && ! $ci->ion_auth->is_admin() ) {
            $ci->session->set_flashdata('message', 'Please log in to continue.');

            $current_url = '/' . $ci->uri->uri_string();
            $ci->session->set_userdata(array('continue_url'=>$current_url));

            redirect('login');
            die();
        }

        if ( ! $ci->ion_auth->logged_in() ) {
            $ci->session->set_flashdata('message', 'Please log in to continue.');

            $current_url = '/' . $ci->uri->uri_string();
            $ci->session->set_userdata(array('continue_url'=>$current_url));

            redirect('login');
            die();
        }

    }
}