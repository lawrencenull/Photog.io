<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Local_subscription extends DataMapper {

	public function __construct( $id = NULL ){
		parent::__construct( $id );
	}

	public function get_subscriptions( $user_id = null ) {

		if ( !$user_id ) {
			$current_user = $this->data['current_user'];
			$user_id = $current_user->id;
		}

		$local = $this->get_local_subscriptions( $user_id );

		$ci =& get_instance();
		$this->load->model('remote_subscription');
		$remote = $ci->remote_subscription->get_remote_subscriptions( $user_id );

		foreach ($local as $i => &$local_subscription) {
			$local_subscription->type = 'local';
		}

		foreach ($remote as $i => &$remote_subscription) {
			$remote_subscription->type = 'remote';
		}

		$subscriptions = array_merge( (array)$local, (array)$remote) ;

		return $subscriptions;
	}

	public function get_followers( $id ) {
		return $id;
		$sql = "SELECT local_photos.id, username AS user_username, caption, user_id, filename, time, NULL AS remote_user_id, NULL AS img_url, NULL AS remote_permalink, NULL AS user_url FROM local_photos JOIN users ON users.id = local_photos.user_id WHERE user_id IN ($local_subscriptions_str) UNION SELECT remote_photos.id, NULL AS username, caption, NULL AS user_id, NULL AS filename, time, remote_user_id, img_url, remote_permalink, user_url FROM remote_photos JOIN remote_users ON remote_users.id = remote_photos.remote_user_id WHERE remote_user_id IN ($remote_subscriptions_str) ORDER BY time DESC LIMIT $limit OFFSET $offset";

		$query = $this->db->query( $sql );
		$result = $query->result();

		foreach ( $result as $photo ) {
			if ( empty( $photo->user_username ) ) {
				$photo->user_username = preg_replace( '#https?://(www.)?#', '', $photo->user_url );
				$photo->user_username = trim_slashes($photo->user_username);
			}

			$photo->user_id = 0;

			if ( empty( $photo->img_url ) ) {
				$photo->img_url = base_url('/photos/' . $photo->filename);
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

	}
	
	public function get_local_subscriptions( $user_id ) {

		$this->db->select( array('users.id', 'username', 'email') );
		$this->db->from('local_subscriptions');

		$this->db->where( 'subscriber_id', $user_id  );
		$this->db->join('users', 'users.id = local_subscriptions.subscribee_id');

		$query = $this->db->get();
		$subscriptions = $query->result();

		foreach ($subscriptions as $i => &$subscription) {
			$subscription->name = $subscription->username;
			$subscription->subscribee_url = base_url($subscription->username);
		}
		
		return $subscriptions;

	}

	public function get_local_user_ids_for_subscriptions( $user_id ) {
		foreach( $subscriptions = $this->get_local_subscriptions( $user_id ) as $i => $subscription ){
			$ids[] = $subscription->id;
		}
		return $ids;
	}


	public function is_local_user_url( $user_url ) {

		$user_url = strip_protocol( $user_url );
		$base_url = strip_protocol( base_url() );

		if ( stripos( $user_url, $base_url ) === 0 ) {
			return true;
		} else {
			return false;
		}

	}

	public function is_local( $user_url ) {

		
		if ( $this->is_local_user_url( $user_url ) ) {
			// Local URL (http://photog.io/joe)
			return true;
			// $this->load->helper('url');
			// $subscribee_username = get_local_username_by_url( $user_url );
		
		} elseif ( stripos( $user_url, '.' ) === FALSE && stripos( $user_url, '/' ) === FALSE ) {
			// Not a URL at all, must be a local username (joe)
			return true;
			// $subscribee_username = $user_url;
		}

		return false;

	}	

	public function add_subscription( $subscriber_id, $user_url ) {
		// Decide if user_url is local or remote,
		// pass to appropriate method

		if ( $this->is_local( $user_url ) ) {
			$subscribee_username = get_local_username_by_url( $user_url );
			return $this->add_local_subscription( $subscriber_id, $subscribee_username );
		} else {
			$this->load->model('remote_subscription');
			$ci =& get_instance();
			return $ci->remote_subscription->add_remote_subscription( $subscriber_id, $user_url );
		}
		
	}

	public function is_subscribed_local( $user_id, $subscribee_id ) {
		if ( $user_id === $subscribee_id ) {
			return true;
		}

		$where = array(
			'subscriber_id' => $user_id,
			'subscribee_id' => $subscribee_id,
		);

		$this->db->where( $where );
		$this->db->from('local_subscriptions');

		return (bool) $this->db->count_all_results();

	}

	public function show_button( $subscribee_id ) {

   		$ci =& get_instance();
		$current_user = $ci->ion_auth->user()->row();
		if ( $current_user ) {
            $current_user_id = $current_user->id;
        } else {
            $current_user_id = 0;
        }
        
		if ( $current_user_id && ( $subscribee_id !== $current_user_id ) ) {
			if ( $this->is_subscribed_local( $current_user_id, $subscribee_id ) ) {
				$show_button['unfollow'] = true;
				$show_button['follow'] = false;
			} else {
				$show_button['follow'] = true;
				$show_button['unfollow'] = false;
			}
		} else {
			$show_button['follow'] = false;
			$show_button['unfollow'] = false;
		}
		return $show_button;
	}

	public function remove_local_subscription( $subscriber_id, $subscribee_username ) {
		$ci =& get_instance();
		$ci->load->model('user');
		$subscribee_id = $ci->user->get_user_id( $subscribee_username );

		$where = array(
			'subscriber_id' => $subscriber_id, 
			'subscribee_id' => $subscribee_id, 
		);

		if ( $this->db->delete( 'local_subscriptions', $where ) ) {
			$result['message_type'] = 'success';
			$result['message_body'] = "Successfully unfollowed!";
		} else {
			$result['message_type'] = 'error';
			$result['message_body'] = "An error occured!";
		}
		return $result;
	}

	public function remove_subscription( $subscriber_id, $user_url ) {
		// Decide if user_url is local or remote,
		// pass to appropriate method

		if ( $this->is_local( $user_url ) ) {
			$subscribee_username = get_local_username_by_url( $user_url );
			return $this->remove_local_subscription( $subscriber_id, $subscribee_username );
		} else {
			$this->load->model('remote_subscription');
			$ci =& get_instance();
			return $ci->remote_subscription->remove_remote_subscription( $subscriber_id, $user_url );
		}
		
	}

	public function add_local_subscription( $subscriber_id, $subscribee_username ) {
		
		$this->load->model('user');
		$ci =& get_instance();
		$subscribee_id = $ci->user->get_user_id( $subscribee_username );
		if ( ! $subscribee_id ) {
			$result['message_type'] = 'error';
			$result['message_body'] = "That user doesn't exist!";
			return $result;
		}
		if ( $this->is_subscribed_local( $subscriber_id, $subscribee_id ) ) {
			$result['message_type'] = 'error';
			$result['message_body'] = "You're already following them";
			return $result;
		}		
		
		$subscription = array(
			'subscriber_id' => $subscriber_id,
			'subscribee_id' => $subscribee_id,
		);

		if ( $this->db->insert( 'local_subscriptions', $subscription ) ) {
			$result['message_type'] = 'success';
			$result['message_body'] = "Successfully added subscription";
		} else {
			$result['message_type'] = 'error';
			$result['message_body'] = "There was an error adding your subscription";
		}

		return $result;
	}

}