<?PHP

class Local_photo extends DataMapper {

	var $has_one = array('user');

	public function __construct( $id = NULL ){
		parent::__construct( $id );
	}
	
	public function get_local_photos( $where = array(), $limit = null, $offset = null ) {

		$lp = new Local_photo();

		if ( !empty($where['where']) ) {
			$lp->where( $where['where'] );
		}

		if ( !empty($where['or_where']) ) {
			$lp->or_where( $where['or_where'] );
		}

		if ( !empty($where['where_in']) ) {
			$key = array_keys( $where['where_in'] );
			$key = $key[0];
			$lp->where_in( $key, $where['where_in'][$key] );
		}

		$lp->order_by('time', 'desc');
		$lp->include_related('user')->get( $limit, $offset ); // http://stackoverflow.com/a/5852004
		
		$photos = $lp->all;
		$this->load->model('user');
		$this->load->library('ion_auth');

		$ci =& get_instance();
		$current_user = $ci->ion_auth->user()->row();

		if ( $ci->ion_auth->logged_in() ) {
			$current_user_id = $current_user->id;
		} else {
			$current_user_id = null;
		}
		$is_admin = $ci->ion_auth->is_admin();

		foreach ($photos as $k => &$photo) {
			$photo->img_url = base_url('/photos/' . $photo->filename);
			$photo->permalink = site_url('/' . $photo->user_username . '/' . $photo->id);
			$photo->delete_url = site_url('/' . $photo->user_username . '/' . $photo->id . '/' . 'delete');
			$photo->user_url = site_url( '/' . $photo->user_username );
			$photo->user_icon_url = $this->user->get_icon_url( $photo->user_id );

			if ( $photo->user_id == $current_user_id || $is_admin ) {
				$photo->editable = true;
			} else {
				$photo->editable = false;
			}
		}

		return $photos;
	}

	public function get_photo_by_id( $photo_id ) {

		// $this->db->select( array('local_photos.id', 'caption', 'user_id', 'filename', 'time', 'username') );
		// $this->db->from('local_photos');

		$lp = new Local_photo();

		$lp->where( array('id' => $photo_id) );

		$lp->include_related('user')->get(); // http://stackoverflow.com/a/5852004
		
		$photos = $lp->all;
		
		foreach ($photos as $k => &$photo) {
			$photo->img_url = base_url('/photos/' . $photo->filename);
			$photo->permalink = site_url('/' . $photo->user_username . '/' . $photo->id);
			$photo->delete_url = site_url('/' . $photo->user_username . '/' . $photo->id . '/' . 'delete');
			$photo->user_url = site_url( '/' . $photo->user_username );
		}

		return $photos;
	}

	public function get_subscription_photos( $user_id = null, $limit = 9999, $offset = 0 ) {
		// The "Dashboard"
		require_auth();
		
		$ci =& get_instance();
		$current_user = $ci->ion_auth->user()->row();
		$current_user_id = $current_user->id;
		$is_admin = $ci->ion_auth->is_admin();
		if ( ! $user_id ) {
			$user_id = $current_user_id;
		}


		$loc_sub = new Local_subscription();
		$loc_sub->where( 'subscriber_id', $user_id )->get();
		foreach ( $loc_sub->all as $key => $sub ) {
			$local_subscriptions[] = $sub->subscribee_id;
		}
		// Automatically add the current user so they can see their own posts!
		$local_subscriptions[] = $user_id;

		$rem_sub = new Remote_subscription();
		$rem_sub->where( 'subscriber_id', $user_id )->get();
		foreach ( $rem_sub->all as $key => $sub ) {
			$remote_subscriptions[] = $sub->remote_user_id;
		}

		if ( empty ( $rem_sub->all ) ) {
			$remote_subscriptions[] = 0;
		}

		$where_in['remote_user_id'] = $remote_subscriptions;
		$where_in['user_id'] = $remote_subscriptions;

		$remote_subscriptions_str = implode( ',', $remote_subscriptions );
		$local_subscriptions_str = implode( ',', $local_subscriptions );

		$sql = "SELECT local_photos.id, NULL AS user_icon_url, username AS user_username, caption, user_id, filename, time, NULL AS remote_user_id, NULL AS img_url, NULL AS remote_permalink, NULL AS user_url FROM local_photos JOIN users ON users.id = local_photos.user_id WHERE user_id IN ($local_subscriptions_str) UNION SELECT remote_photos.id, icon_url AS user_icon_url, NULL AS username, caption, NULL AS user_id, filename, time, remote_user_id, img_url, remote_permalink, user_url FROM remote_photos JOIN remote_users ON remote_users.id = remote_photos.remote_user_id WHERE remote_user_id IN ($remote_subscriptions_str) ORDER BY time DESC LIMIT $limit OFFSET $offset";

		$query = $this->db->query( $sql );
		$result = $query->result();

		if ( empty( $result ) ) {
			return false;
		}

		foreach ( $result as $photo ) {
			if ( empty( $photo->user_username ) ) {
				$photo->user_username = preg_replace( '#https?://(www.)?#', '', $photo->user_url );
				$photo->user_username = trim_slashes($photo->user_username);
			}

			if ( empty( $photo->img_url ) ) {
				$photo->img_url = base_url('/photos/' . $photo->filename);
			}

			if ( empty( $photo->filename ) && ! empty( $photo->remote_permalink ) ) {

				$ci->load->model('remote_photo');
				$photo->filename = $ci->remote_photo->cache_remote_photo( $photo->id );
			}

			if ( empty( $photo->permalink ) ) {
				if ( ! empty( $photo->remote_permalink ) ) {
					$photo->permalink = $photo->remote_permalink;
				} else {
					$photo->permalink = site_url('/' . $photo->user_username . '/' . $photo->id);
				}
			}

			$photo->delete_url = site_url('/' . $photo->user_username . '/' . $photo->id . '/' . 'delete');

			if ( empty( $photo->user_url ) ) {
				$photo->user_url = site_url( '/' . $photo->user_username );
			}
			if ( empty( $photo->user_icon_url ) ) {
				$photo->user_icon_url = $this->user->get_icon_url( $photo->user_id );
			}

			if ( ( $photo->user_id == $current_user_id || $is_admin ) && empty( $photo->remote_user_id ) ) {
				$photo->editable = true;
			} else {
				$photo->editable = false;
			}

			$photos[] = $photo;
		}

		return $photos;
	}

	public function get_local_user_photos( $local_user_ids = array() ) {
		$where_in = array(
			'user_id'=> $local_user_ids,
		);

		return $this->get_local_photos( array( 'where_in' => $where_in ) );
	}

	public function insert( $photo ){
		$this->db->insert('local_photos', $photo);
		return $this->db->insert_id();
	}

	public function publish( $photo ){
		/*	$photo
		array(4) {
		  ["caption"]=>
		  string(0) ""
		  ["user_id"]=>
		  string(2) "19"
		  ["filename"]=>
		  string(36) "aa26c405977a65aef3deeb6d30594bb8.jpg"
		  ["time"]=>
		  string(19) "2013-01-14 21:10:30"
		} */
		
		$photo_id = $this->insert( $photo );
		$user_id = $photo['user_id'];
		$user_slug = $this->user->get_username_by_id( $user_id );
		$permalink = site_url( "$user_slug/$photo_id" );

		$publish_results = array(
			'permalink' => $permalink,
			'id' => $photo_id,
			'user_id' => $user_id,
		);

		// Update PubSubHubBub Hub
		// Hopefully we won't need the additional default feed parameters:
		$user_atom_feed = site_url( "$user_slug/feed" );
		$ci =& get_instance();
		$ci->load->library('pubsubhubbub');
		$publish_results['syndicated']['pubsubhubbub'] = $ci->pubsubhubbub->update_hub( $user_atom_feed );
		// END PubSubHubBub

		$img_data = file_get_contents( './photos/' . $photo['filename'] );
		$tumblr_post = array(
			'caption' => '',
			'data' => $img_data,
			'link' => $permalink,
			'source' => base_url( 'photos/' . $photo['filename'] ),
		);
		
		$ci =& get_instance();
    	$ci->load->model('Tumblr');
		if ( $ci->Tumblr->isAuthorized() && $ci->Tumblr->post_photo( $tumblr_post ) ) {
			$publish_results['syndicated'][] = 'tumblr';
		}

		return $publish_results;
	}

	public function update_caption( $photo_id, $caption ){

		if ( empty( $photo_id ) ) {
			return false;
		}

		$ci =& get_instance();
		$current_user = $ci->ion_auth->user()->row();

		if ( $ci->ion_auth->logged_in() ) {
			$current_user_id = $current_user->id;
		} else {
			$current_user_id = null;
		}
		$is_admin = $ci->ion_auth->is_admin();
		
		$lp = new Local_photo();
		$lp->where( 'id', $photo_id );
		$lp->include_related('user')->get();
		$photo = $lp->all[0];

		if ( $is_admin || $photo->user_id == $current_user_id ) {
			$result = $lp->where( 'id', $photo_id )->update( 'caption', $caption );
			return $result;
		} else {
			return false;
		}
	}

}