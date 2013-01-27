<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Error extends MY_Controller {

	public function __construct() {
		parent::__construct();
		// $this->load->model('site_option');
		$this->data['title'] = 'Error';
	}


	public function error_404() {
		$this->data['title'] = 'Error';
		$this->data['pre_content'] = 'Sorry, the page you are looking for is not here.';
		$this->load->view('header', $this->data);
        $this->load->view('footer');
	}




}