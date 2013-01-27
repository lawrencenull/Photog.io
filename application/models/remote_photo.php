<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Remote_photo extends DataMapper {
	
	var $has_one = array('remote_user');

	public function __construct( $id = NULL ){
		parent::__construct( $id );
	}

	public function cache_remote_photo( $remote_photo_id ) {
		$query = $this->db->get_where( 'remote_photos', array( 'id' => $remote_photo_id ) );
		$result = $result = $query->result();
		$remote_url = $result[0]->img_url;
		
		$image_bin = file_get_contents( $remote_url );

		$hash = md5( $remote_photo_id . $remote_url . microtime() . $this->config->item( 'encryption_key' ) );
		$new_path = './photos/';

		preg_match( '#.([a-z]+)$#i', $remote_url, $matches );
		if ( ! empty( $matches[1] ) ) {
			$extension = $matches[1];
		} else {
			$extension = '';
		}

		if ( $extension == 'jpeg' ) {
			$extension = 'jpg';
		}

		$filename = "$hash.$extension";

		file_put_contents( $new_path . $filename, $image_bin );

		$remote_photo = array(
			'filename' => $filename,
		);

		$this->db->where('id', $remote_photo_id);
		$this->db->update( 'remote_photos', $remote_photo );
		return $filename;
	}		

	public function get_remote_photos( $where = array() ) {
		
		$rem_photos = new Remote_photo();

		if ( !empty($where['where']) ) {
			$rem_photos->where( $where['where'] );
		}

		if ( !empty($where['or_where']) ) {
			$rem_photos->or_where( $where['or_where'] );
		}

		if ( !empty($where['where_in']) ) {
			$key = array_keys( $where['where_in'] );
			$key = $key[0];
			$rem_photos->where_in( $key, $where['where_in'][$key] );
		}

		$rem_photos->include_related('remote_user')->get();
		$remote_photos = $rem_photos->all;

		foreach ( $remote_photos as $i => $photo ) {
			$photos[$i] = $photo;
			$username = trim_slashes( $photo->remote_user_user_url );
			$find = array(
				'#^http(s?)://(www?)#',
			);
			$username = preg_replace( '#^https?://(www.)?#', '', $username );

			$photos[$i]->permalink = $photo->remote_permalink;
			$photos[$i]->editable = null;
			$photos[$i]->user_url = $photo->remote_user_user_url;
			$photos[$i]->user_username = $username;
			$photos[$i]->user_icon_url = $photo->remote_user_icon_url;
		}

		return $photos;

	}

	/*public function get_subscription_photos( $user_id = null ) {
		// The "Dashboard"


		if ( !$user_id ) {
			$current_user = $this->data['current_user'];
			$user_id = $current_user->id;
		}

		$this->load->model('local_subscription');
		$this->load->model('remote_subscription');
		$local_subscriptions = $this->local_subscription->get_local_subscriptions( $user_id );
		$remote_subscriptions = $this->remote_subscription->get_remote_subscriptions( $user_id );
		
		$local_subscription_ids = $this->local_subscription->get_local_user_ids_for_subscriptions($user_id);
		$remote_subscription_ids = $this->remote_subscription->get_remote_user_ids_for_subscriptions($user_id);
		
		$local_photos = $this->get_local_user_photos( $local_subscription_ids );
		$remote_photos = $this->get_remote_user_photos( $remote_subscription_ids );

		$photos = array_merge( (array) $local_photos, (array) $remote_photos );
		usort( $photos, function($a, $b) {
			$a = strtotime( $a->time );
			$b = strtotime( $b->time );
			if ( $a == $b ) {
				return 0;
			}

		   return ($a > $b) ? -1 : 1;
		});
		return $photos;
	}*/

	public function get_remote_user_photos( $remote_user_ids = array() ) {
		$where_in = array(
			'remote_user_id'=> $remote_user_ids,
		);

		return $this->get_remote_photos( array( 'where_in' => $where_in ) );
	}

	public function parse_feed( $xml_str ) {
		$xml =  simplexml_load_string( $xml_str );
		$xml->registerXPathNamespace('a', 'http://www.w3.org/2005/Atom');
		

		$self = $xml->xpath( '/a:feed/a:link[@rel="self"]/@href' );
		$self = (string) $self[0];

		
		/*array (
		    'caption' => '',
		    'img_url' => 'http://photos.joe.im/photos/1878c9ab07e1ec042d28aecc3383dbb0.png',
		    'time' => '',
		    'remote_permalink' => 'http://photos.joe.im/joe/289/',
		    'remote_id' => 'http://photos.joe.im/joe/289/',
		  )*/

		$remote_user = $this->remote_user->get_by_feed( $self );

		foreach ( $xml->xpath( '/a:feed/a:entry' ) as $entry ) {

			$photo['caption'] = (string) $entry->summary;
			$photo['img_url'] = $entry->content[0]->attributes();
			$photo['img_url'] = (string) $photo['img_url']['src'];
			$photo['time'] = (string) $entry->updated;
			$photo['remote_permalink'] = $entry->link[0]->attributes();
			$photo['remote_permalink'] = (string) $photo['remote_permalink']['href'];
			$photo['remote_id'] = (string) $entry->id;
			$photo['remote_user_id'] = $remote_user->id;

			$photos[] = $photo;
		}

		log_message( 'debug', 'PuSH: remote_photo->parse_feed(): ' . var_export( $photos, TRUE ) );

		return $photos;
	}

	public function insert_batch( $photos ){


		$this->db->insert_batch( 'remote_photos', $photos );
	}

	public function insert( $photo ){
		$this->db->insert('remote_photos', $photo);
		return $this->db->insert_id();
	}

}