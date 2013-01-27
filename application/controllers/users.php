<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends MY_Controller {

    public function __construct(){
        parent::__construct();
        $this->data['title'] = 'User';
    }

	public function index() {
		// print_r( $this->ion_auth->user()->row() );
	}

	public function login() {
        $this->data['title'] = 'Login';
        // if this request is a form submission
        // var_dump($_POST);
            // die();
        if ($_POST) {

	        // get form values and xss filter the input
            $identity = $this->input->post('identity', true);
            $password = $this->input->post('password', true);

            // if user is logged in successfully
            if( $this->ion_auth->login( $identity, $password) ) {
                $username = $this->ion_auth->user()->row()->username;
                $welcome_messages = array(
                    "Welcome back to the party, $username!",
                    "Long time no see!",
                    "We missed you, $username!",
                    "What's new, $username?",
                    "How've you been, $username?",
                    "Lookin' good today, $username!",
                );
                $random_welcome_message = $welcome_messages[ array_rand( $welcome_messages ) ];
                // send on to protected area ('user' controller)
                $this->session->set_flashdata( 'message', $random_welcome_message );
                redirect( $this->session->userdata('continue_url') );
            } else {
                // incorrect creds
                // load up error
                $this->data['error'] = "Incorrect Credentials";
                
                // load form view again, with error
                $this->load->view('header', $this->data);
                $this->load->view('user/login', $this->data);
                $this->load->view('footer');
            }
	    } else {
            // show form view
            $this->load->view('header', $this->data);
            $this->load->view('user/login', $this->data);
            $this->load->view('footer');

	    }
	}

    public function logout() {

        if ( ! $this->ion_auth->logged_in() ) {
            redirect();
        }

        $username = $this->data['current_user']->username;
        // log current user out and send back to public root
        $this->ion_auth->logout();
        $goodbye_messages = array(
            'Hurry back!',
            'Have fun out there!',
            "Don't be gone too long!",
            "Hope to see you again soon!",
            "Seeya next time!",
            "Bye, $username!",
        );

        $random_goodbye_message = $goodbye_messages[ array_rand( $goodbye_messages ) ];
        $this->session->set_flashdata( 'message', $random_goodbye_message );
        redirect('/');
    }

    public function register( $invite_code = '' ) {

        if( $this->input->post('invite-code', true) ) {
            redirect( 'register/' . $this->input->post('invite-code', true) );
        }

        $this->data['title'] = 'Register';
        $this->data['body_classes'][] = 'register';

        $this->load->model('invite');
        $code_valid = $this->invite->check_code( $invite_code );
        if ( $this->config->item( 'invite_only' ) && !$code_valid ) {
            $this->data['body_classes'][] = 'ask for code';
            $this->data['title'] = '';
            // $this->session->set_flashdata('error', 'Please enter a valid invite code to continue.');
            $this->data['error'] = 'Please enter a valid invite code to continue.';
            $this->load->view('header', $this->data);
            $this->load->view('user/enter_invite_code');
            $this->load->view('footer', $this->data);
            return;
        }

        if ($_POST) {

            $metadata = array(
                'invite_code' => $invite_code,
            );

            $username = $this->input->post('username', true);
            $password1 = $this->input->post('password1', true);
            $password2 = $this->input->post('password2', true);
            $email = $this->input->post('email', true);

            $password = ( $password1 === $password2 ) ? $password1 : false;

            // $group = array();

            if ( $this->invite->use_code( $invite_code ) && $new_id = $this->ion_auth->register( $username, $password, $email, $metadata ) ) {
                
                $this->invite->give_codes( $new_id, $this->config->item( 'new_user_invites' ) );
            
                $this->ion_auth->login( $email, $password);
                $this->session->set_flashdata( 'message', 'Welcome, you have been automatically signed in!' );
                redirect();
            } else {
                $this->session->set_flashdata( 'message', $this->ion_auth->errors() );
                redirect( $this->uri->uri_string() );
            }
            
        } else {
            // show registration form view
            $this->load->view('header', $this->data);
            $this->load->view('user/register', $this->data);
            $this->load->view('footer');
        }
    }

    public function edit( $user_id = 0 ) {
        require_auth();

        if ( ! $user_id ) {
            $user_id = $this->data['current_user']->id;
        }

        if ( $_POST ) {

            // debug( $options = $this->input->post('profile', true) );
            // die();

        }

        // debug( $this->data['current_user'] );

        $this->data['profile']['real_name'] = 'Joe Anzalone';
        $this->data['title'] = 'All about ' . $this->ion_auth->user($user_id)->row()->username;
        ;
        $this->data['pre_content'] = '<p>Fill out information about yourself below.</p>';



        $this->load->view('header', $this->data);
        $this->load->view('user/edit', $this->data);
        $this->load->view('footer');
    }

    public function follow( $user ) {
        if ( $_POST ) {

        }
    }    

    public function invites() {
        require_auth();
        $this->data['title'] = 'Invites';
        $this->data['pre_content'] = '<p>Below is a list of invites available to you.</p>';


        $this->load->model('invite');
        $this->data['invites'] = $this->invite->get_codes( $this->data['current_user']->id );
        
        foreach ( $this->data['invites'] as $i => $invite ) {
            if ( ! $this->invite->check_code( $invite->code ) ) {
                unset( $this->data['invites'][$i] );
            }
        }

        $this->load->view('header', $this->data);
        $this->load->view('invite/list', $this->data);
        $this->load->view('footer');

    }

    public function profile( $username ) {
        $this->data['title'] = "$username";
        $this->data['body_classes'][] = "profile";

        if ( $username == 'me' ) {
            redirect( $this->data['current_user']->username . '/about' );
        }

        $this->load->model('user');

        $this->load->model('local_subscription');


        $this->data['user'] = $this->user->get_user_by_username( $username );
        $this->data['user']->photo_count = $this->user->count_photos( $this->data['user']->id );
        $this->data['user']->subscription_count = $this->user->count_subscriptions( $this->data['user']->id );
        $this->data['user']->user_icon_url = $this->user->get_icon_url( $this->data['user']->id, 560 );

        $this->data['show_button'] = $this->local_subscription->show_button( $this->data['user']->id );
        
        $this->load->view('header', $this->data);
        $this->load->view('user/profile', $this->data);
        $this->load->view('footer');
    }

}