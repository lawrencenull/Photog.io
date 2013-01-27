<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tumblr_controller extends MY_Controller {

	public function __construct(){
		parent::__construct();

		$this->load->library('session');
    	$this->load->model('Tumblr');
	}

    public function get_dash( $consumer = null, $token = null ) {
        var_dump( $this->Tumblr->isAuthorized() );
    	$dash = $this->Tumblr->request( 'GET', 'user/dashboard' );

    	var_dump( $dash );
	
	}

    public function new_post( ) {
      
        if ( empty( $user ) && $this->session->userdata('tumblr_user')) {
            $user = $this->session->userdata('tumblr_user');
        }

        $img_data = file_get_contents('http://joeanzalone.com/wordpress/wp-content/uploads/2012/04/goompa_stomping-300x225.jpg');

        // $hostname = $user['name'] . '.tumblr.com';
        $hostname = $user->blogs[1]->name . '.tumblr.com';
        $params = array(
            'type' => 'photo',
            'caption' => 'Testing out the <a href="http://www.tumblr.com/docs/en/api/v2#posting">Tumblr API.</a> Please ignore! :)',
            'data' => $img_data,
            'link' => 'http://placekitten.com',
            'body' => 'lol',
            'source_url' => 'http://joe.im',
        );

    	$response = $this->Tumblr->request( 'POST', "blog/$hostname/post", $params );
    	return $response;
    
    }

}