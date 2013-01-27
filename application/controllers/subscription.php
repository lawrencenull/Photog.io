<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Subscription extends MY_Controller {

	public function __construct(){
		parent::__construct();
		// $this->load->model('subscription');
		$this->data['title'] = 'Subscriptions';
		// $this->load->model('local_subscription');
		// $this->load->model('remote_subscription');
		require_auth();
	}

	public function index(){
		if ( $_POST ) {
			$this->save();
		}

		$current_user = $this->data['current_user'];
		$this->load->model('local_subscription');
		$this->data['subscriptions'] = $this->local_subscription->get_subscriptions( $current_user->id );

		if ( ! $this->data['subscriptions'] ) {
			$this->data['pre_content'] = 'No subscriptions found!';
		}

		$this->load->view('header', $this->data);
        $this->load->view('subscription/list', $this->data);
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

	public function follow( $user_url = '' ) {

		if ( $_POST ) {
			$user_url = $this->input->post('url', true);
			$current_user = $this->data['current_user'];
			$this->load->model('local_subscription');
			$result = $this->local_subscription->add_subscription( $current_user->id, $user_url );

			$this->session->set_flashdata( $result['message_type'], $result['message_body'] );

			// This should actually redirect them back to where they were, not necessrily 'follow'
			redirect();
		}

		$this->data['user_url'] = $user_url;
		$this->load->view('header', $this->data);
        $this->load->view('subscription/follow', $this->data);
        $this->load->view('footer');
	}

	public function unfollow( $user_url = '' ) {

		if ( $_POST ) {
			$user_url = $this->input->post('url', true);
			$current_user = $this->data['current_user'];
			$this->load->model('local_subscription');
			$result = $this->local_subscription->remove_subscription( $current_user->id, $user_url );

			$this->session->set_flashdata( $result['message_type'], $result['message_body'] );

			// This should actually redirect them back to where they were, not necessrily 'follow'
			redirect();
		}

		/*$this->data['user_url'] = $user_url;
		$this->load->view('header', $this->data);
        $this->load->view('subscription/follow', $this->data);
        $this->load->view('footer');*/
	}



	public function show_followers() {
		$this->data['title'] = 'Followers';

		$id = 0;

		$this->load->model('local_subscription');
		$this->local_subscription->get_followers( $id );
	
		$this->load->view('header', $this->data);
        $this->load->view('user/followers', $this->data);
        $this->load->view('footer');		

	}


	public function remove( $id ) {

	}
}