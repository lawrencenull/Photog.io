<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends MY_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->spark( 'oauth/0.3.1' );
        require_auth();
    }

    public function oauth( $provider ) {
        $this->load->helper( 'url' );

        // Create an consumer from the settings
        $oauth_config = $this->config->item( $provider, 'oauth' );
        $consumer = $this->oauth->consumer(array(
            'key' => $oauth_config['key'],
            'secret' => $oauth_config['secret'],
        ));

        // Load the provider
        $provider = $this->oauth->provider( $provider );

        // Create the URL to return the user to
        $callback = site_url( 'me/authorize/'.$provider->name );

        if ( ! $this->input->get_post( 'oauth_token' ) ) {
            // Add the callback URL to the consumer
            $consumer->callback( $callback );

            // Get a request token for the consumer
            $token = $provider->request_token( $consumer );

            // Store the token
            $this->session->set_userdata( 'oauth_access_token', array( 'access_token'=>$token->access_token, 'access_secret'=> $token->secret) );

            // Get the URL to the twitter login page
            $url = $provider->authorize( $token, array(
                'oauth_callback' => $callback,
            ) );

            // Send the user off to login
            redirect( $url );
        } else {
            if ( $this->session->userdata('oauth_access_token') ) {
                // Get the token from storage
                $token_arr = $this->session->userdata('oauth_access_token');
            }

            if ( ! empty( $token_arr ) AND $token_arr['access_token'] !== $this->input->get_post('oauth_token')) {   
                // Delete the token, it is not valid
                $this->session->unset_userdata('oauth_access_token');

                // Send the user back to the beginning
                exit('invalid token after coming back to site');
            }

            // Get the verifier
            $verifier = $this->input->get_post('oauth_verifier');

            $token = OAuth_Token::forge('request', array(
                'access_token'  => $token_arr['access_token'],
                'secret' => $token_arr['access_secret'],
            ));
            // Store the verifier in the token
            $token->verifier( $verifier );

            // Exchange the request token for an access token
            $token = $provider->access_token( $consumer, $token );
            $this->load->model('access_token');
            $this->access_token->save( $provider->name, array( 'token'=>$token->access_token, 'secret'=>$token->secret, ) );

            // We got the token, let's get some user data
            // $user = $provider->get_user_info($consumer, $token);
            $user = $this->get_user_info( $consumer, $token );
            $this->session->set_userdata( 'tumblr_user', $user );

            $this->session->set_flashdata( 'success', "Cool! Photos will now be automatically posted to $provider->name!" );
            redirect();

            // Here you should use this information to A) look for a user B) help a new user sign up with existing data.
            // If you store it all in a cookie and redirect to a registration page this is crazy-simple.
            // echo "<pre>Tokens: ";
            // var_dump($token).PHP_EOL.PHP_EOL;

            // echo "User Info: ";
            // var_dump($user);

        }
    }

     public function get_user_info( $consumer, $token ) {
        $this->load->spark('oauth/0.3.1');

        $request = OAuth_Request::forge('resource', 'GET', 'http://api.tumblr.com/v2/user/info', array(
            'oauth_consumer_key' => $consumer->key,
            'oauth_token' => $token->access_token,
        ));
        $provider = $this->oauth->provider( 'tumblr' );

        // Sign the request using the consumer and token
        $request->sign( $provider->signature, $consumer, $token );

        $response = json_decode( $request->execute() );

        return $response->response->user;
    }



}