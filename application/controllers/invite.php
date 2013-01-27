<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invite extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('site_option');
		$this->data['title'] = 'Invites';
	}


	public function request() {
		$this->data['title'] = 'Request an invite';

		$email = $this->input->post('email');

		if ( !empty( $email ) ) {
			$this->add_to_mailchimp_list( $email );
			$this->session->set_flashdata('success', "<strong>Success!</strong><p>Please check your email to confirm your spot on the waiting list and we'll let you know as soon as we're ready for you.</p>");
			$current_url = $this->uri->uri_string();
			redirect();
			// redirect($current_url);
		}

		$this->load->view('header', $this->data);
        $this->load->view('invite/request', $this->data);
        $this->load->view('footer');

	}

	public function add_to_mailchimp_list( $email, $first_name = '', $last_name = '' ) {
		
		$api_key = $this->config->item( 'mailchimp_api_key' );
		$waiting_list_id = $this->config->item( 'mailchimp_waiting_list_id' );

		// https://github.com/waynhall/CodeIgniter-Library-for-MailChimp-API-v1.3/
		$this->load->library('MCAPI', array('apikey'=>$api_key), 'mail_chimp');

		$merge_vars = array(
			'FNAME' => $first_name,
			'LNAME' => $last_name,
		);

		// http://apidocs.mailchimp.com/api/1.3/listsubscribe.func.php
		return $this->mail_chimp->listSubscribe( $waiting_list_id, $email, $merge_vars );

	}



}