<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pubsubhubbub_controller extends MY_Controller {


	public function __construct(){
		parent::__construct();
		$this->load->library('pubsubhubbub');
	}


	public function callback() {
		$this->log();

		log_message( 'debug', 'PUSH: Pubsubhubbub_controller->callback()' );

		if ( $this->input->get( 'hub_challenge' ) ) {
			$this->output->set_output( $this->input->get( 'hub_challenge' ) );
			log_message( 'debug', 'PUSH: Pubsubhubbub_controller->callback() - Answered challenge' );
			return;
		}

		$headers = $this->input->request_headers();
		$body = file_get_contents('php://input');
		preg_match( '#sha1=([a-fA-F0-9]{40})#i', $headers['X-Hub-Signature'], $matches );
		if ( ! empty( $matches[1] ) ) {
			log_message( 'debug', 'PUSH: Pubsubhubbub_controller->callback() - $matches[1] is NOT empty' );
			$hmac = hash_hmac( 'sha1', $body, $this->config->item( 'secret' ) );

			if ( $matches[1] === $hmac ) {
				// HMAC matches, deal with updates (probably in a model such as remote_photo)
				log_message( 'debug', 'PUSH: Pubsubhubbub_controller->callback() - $matches[1] equals $hmac' );
				$this->load->model('remote_photo');
				$photos = $this->remote_photo->parse_feed( $body );
				return $this->remote_photo->insert_batch( $photos );
			}
		}

	}

	public function log() {
		
		$headers = $this->input->request_headers();
		$get = $this->input->get();
		$post = $this->input->post();
		$body = file_get_contents('php://input');

		$this->load->config( 'pubsubhubbub' );
		$hmac = hash_hmac( 'sha1', $body, $this->config->item( 'secret' ) );

		$string = '';

		$string .= date('M j @ h:i:sa');
		$string .= "\n\n";


		$string .= "Headers:\n";
		$string .= var_export( $headers, TRUE );
		$string .= "\n\n";

		$string .= "Get:\n";
		$string .= var_export( $get, TRUE );
		$string .= "\n\n";


		$string .= "Post:\n";
		$string .= var_export( $post, TRUE );
		$string .= "\n\n";

		$string .= "Body:\n";
		$string .= $body;
		$string .= "\n\n";

		$string .= "HMAC:\n";
		$string .= $hmac;
		$string .= "\n\n";

		$string .= "secret:\n";
		$string .= $this->config->item( 'secret' );
		$string .= "\n\n";




		$string .= '----------';
		$string .= "\n\n";

		file_put_contents( 'log.txt', $string, FILE_APPEND );
	}

}