<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Site_options extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('site_option');
		$this->data['title'] = 'Options';
		require_auth('admin');
	}

	public function index(){
		if ( $_POST ) {
			$this->save();
		}

		$this->data['options'] = $this->site_option->get_options();

		$this->load->view('header', $this->data);
        $this->load->view('option/form', $this->data);
        $this->load->view('footer');
	}

	public function save() {
		if ( $_POST ) {
			$options = $this->input->post('options', true);
			$i = 0;
			foreach ($options as $key => $value) {
				$options_numerical[$i]['key'] = $key;
				$options_numerical[$i]['value'] = $value;
				$i++;
			}

			$this->site_option->update_options( $options_numerical );
        	$current_url = $this->uri->uri_string();
			redirect($current_url);
		}
	}

}