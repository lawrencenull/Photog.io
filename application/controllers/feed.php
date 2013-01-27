<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Feed extends MY_Controller {

	public $default_format = 'atom';

	public function __construct( ) {
		parent::__construct();

		if ( $this->input->get('format', TRUE) ) {
			$this->format = $this->input->get('format', TRUE);
		} else {
			$this->format = $this->default_format;
		}

		if ( $this->input->get('limit', TRUE) ) {
			$this->limit = $this->input->get('limit', TRUE);
		} else {
			$this->limit = 10;
		}

		if ( $this->input->get('offset', TRUE) ) {
			$this->offset = $this->input->get('offset', TRUE);
		} else {
			$this->offset = 0;
		}



		$content_type = $this->get_content_type( $this->format );
		$this->output->set_header("Content-Type: $content_type");

	}

	public function get_content_type( $format ) {
		$types = array(
			'atom' => 'application/atom+xml',
			'rss'  => 'application/rss+xml',
			'json' => 'application/json',
		);

		return $types[ $format ];
	}

	public function get_mime_type( $extension ) {
		$extension = strtolower( $extension );

		$types = array(
			'jpg'  => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'png'  => 'image/png',
			'gif'  => 'image/gif',
		);

		return $types[ $extension ];
	}



	public function user_photos( $username ) {
		$this->load->helper('xml');

		$this->load->model( 'user' );
		$user_id = $this->user->get_user_id( $username );

		$where['where'] = array(
			'user_id' => $user_id,
		);

		$this->load->model( 'local_photo' );
		$photos = $this->local_photo->get_local_photos( $where, $this->limit, $this->offset );

		if ( $this->format == $this->default_format ) {
			// $this->data['feed']['self'] = site_url( $username . '/feed/?limit='. $this->limit .'&offset=' . $this->offset );
			$this->data['feed']['self'] = site_url( $username . '/feed' );
		} else {
			// $this->data['feed']['self'] = site_url( $username . '/feed/?format=' . $this->format . '&limit='. $this->limit .'&offset=' . $this->offset );
			$this->data['feed']['self'] = site_url( $username . '/feed/?format=' . $this->format );
		}

		$this->data['feed']['html_version'] = site_url( $username );
		$this->data['feed']['id'] = $this->data['feed']['self'];

		if ( !empty( $photos ) ) {
			$this->data['feed']['updated'] = date( 'Y-m-d\TH:i:sP', strtotime( $photos[0]->time ) );
		} else {
			$this->data['feed']['updated'] = date( 'Y-m-d\TH:i:sP' );
		}

		// $this->data['feed']['updated'] = '2013-01-24T03:00:00-05:00';
		$site_name = $this->config->item( 'site_title' );
		$this->data['feed']['title'] = "$username's $site_name Stream";
		$this->data['feed']['subtitle'] = "A feed of photos from $username's $site_name stream";

		$this->config->load('pubsubhubbub', TRUE);
		$this->data['feed']['hubs'] = $this->config->item( 'hubs', 'pubsubhubbub' );

		$this->data['feed']['entries'] = array();
		foreach ($photos as $i => $photo) {

			$extension = pathinfo( $photo->filename, PATHINFO_EXTENSION );

			$this->data['feed']['entries'][] = array(
				'title' => $photo->caption,
				'permalink' => $photo->permalink,
				'mime_type' => $this->get_mime_type( $extension ),
				'img_url' => $photo->img_url,
				'id' => $photo->permalink,
				'updated' => date( 'Y-m-d\TH:i:sP', strtotime( $photo->time ) ),
				'caption' => $photo->caption,
				'author' => array( 'name' => $photo->user_username ),
			);
		}

		$this->load->view("feeds/{$this->format}/user_photos", $this->data);
	}

}