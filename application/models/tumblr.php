<?PHP

class Tumblr extends CI_Model {
	
	var $base_api_url = 'http://api.tumblr.com/v2/';

	public function __construct( $id = NULL ){
		parent::__construct( $id );
        $this->load->helper('url');
        $this->load->spark('oauth/0.3.1');        
        $this->provider = 'tumblr';

        $this->consumer = $this->get_consumer();
	}
	
	public function request( $method = 'GET', $url = 'user/info', $params = array() ) {
		// A wrapper for OAuth_Request::forge that automatically takes care of the tokens and signing

        $request_url = $this->base_api_url . $url;

        $this->load->model('access_token');
        $token_arr = $this->access_token->get( $this->provider );
      	
        /*
        $token_arr
        array(2) {
          ["access_token"]=>
          string(50) "z8QaCfObw3Blpurdb9z1xZbV4zRMVjpjjcytF8cJQnVS6Yxrb1"
          ["access_secret"]=>
          string(50) "bVdTKKGcIrHjgvPO4XXkhnY4UzgG260Ft91DDCwy5ESqLlmf2c"
        } */

        $provider = $this->oauth->provider( $this->provider );

        if ( !empty( $token_arr['access_token'] ) && !empty( $token_arr['access_secret'] ) ) {
            $acces_token = OAuth_Token::forge('request', array(
                'access_token'  => $token_arr['access_token'],
                'secret' => $token_arr['access_secret'],
            ));
        
        $params['oauth_consumer_key'] = $this->consumer->key;
        $params['oauth_token'] = $acces_token->access_token;

        } else {
            $acces_token = false;
        }

        $request = OAuth_Request::forge( 'resource', $method, $request_url, $params);


        if ( $acces_token ) {
            // Sign the request using the consumer and token
            $request->sign( $provider->signature, $this->consumer, $acces_token );
        }

        try {
            $response = json_decode( $request->execute() );
        } catch ( Exception $e ) {
            return false;
        }

        return $response;
	}


	public function get_consumer() {
        // Returns a consumer object with the keys from config.php

        $oauth_config = $this->config->item( $this->provider, 'oauth' );

        $consumer = $this->oauth->consumer(array(
            'key' => $oauth_config['key'],
            'secret' => $oauth_config['secret'],
        ));

        return $consumer;
	}

    public function get_user_info() {
        return $this->request( 'GET', 'user/info' );
    }

    public function post_photo( $photo ) {

        $user_info = $this->get_user_info();
        $blog_name = $user_info->response->user->blogs[0]->name;
        $hostname = "$blog_name.tumblr.com";
        $params = array(
            'type' => 'photo',
            'caption' => $photo['caption'],
        );

        if ( $this->config->item( 'caption_link', 'syndication' ) ) {
            $params['caption'] .= '<p><a href="'.$photo['link'].'">via photog.io</a></p>';
        }

        if ( $this->config->item( 'image_link', 'syndication' ) ) {
            $params['link'] = $photo['link'];
        }


        if ( $this->config->item( 'source_link', 'syndication' ) ) {
            $params['source_url'] = $photo['link'];
        }

        if ( $this->config->item( 'upload_via_post', 'syndication' ) ) {
            $params['data'] = $photo['data'];
        } else {
            $params['source'] = $photo['source'];
        }

        return $this->request( 'POST', "blog/$hostname/post", $params );
    }

    public function isAuthorized() {
        
        if ( !empty( $this->get_user_info()->meta ) && $this->get_user_info()->meta->status === 200 ) {
            return true;
        } else {
            $this->load->model('access_token');
            $this->access_token->delete( $this->provider );
            return false;
        }
    }

}