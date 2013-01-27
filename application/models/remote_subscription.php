<?PHP

class Remote_subscription extends DataMapper {

	public function __construct( $id = NULL ){
		parent::__construct( $id );
	}
	
	public function get_remote_subscriptions( $user_id ) {
		$this->db->select( '*' );
		$this->db->from('remote_subscriptions');
		$this->db->join('remote_users', 'remote_users.id = remote_subscriptions.remote_user_id');
		$query = $this->db->where( array( 'subscriber_id' => $user_id ) );
		$query = $this->db->get();
		$subscriptions = $query->result();
		foreach ($subscriptions as $i => &$subscription) {
			
			$name = trim_slashes( $subscription->user_url );

			$name = preg_replace('/^(http|ftp|news)s?:\/+/i', '', $name);

			$subscription->name = $name;
			$subscription->subscribee_url = $subscription->user_url;
		}
		return $subscriptions;
	}

	public function get_remote_user_ids_for_subscriptions( $user_id ) {
		foreach( $this->get_remote_subscriptions( $user_id ) as $i => $subscription ){
			$ids[] = $subscription->remote_user_id;
		}
		return $ids;
	}

	public function add_remote_subscription( $subscriber_id, $user_url ) {
		$ci =& get_instance();
		$ci->load->model('remote_user');

		$ci->load->library('pubsubhubbub');

		if ( $feed_url = $ci->pubsubhubbub->get_feed_url( $user_url ) ) {
			$where = array( 'feed_url' => $feed_url );
			$remote_users = $ci->remote_user->get_remote_users( $where );
		} else {
			// Error getting feed URL
			$result['message_type'] = 'error';
			$result['message_body'] = "Error contacting remote server";
			return $result;
		}

		if ( ! $remote_users ) {
			$remote_user = array(
				'user_url' => $user_url,
				'feed_url' => $feed_url,
			);
			$remote_user_id = $ci->remote_user->add_remote_user( $remote_user );

			$push_subscribe_success = $ci->pubsubhubbub->subscribe( $feed_url );
			var_dump( $push_subscribe_success ); die();

			$xml = file_get_contents( $feed_url );
			$ci->load->model('remote_photo');
			$photos = $ci->remote_photo->parse_feed( $xml );
			$ci->remote_photo->insert_batch( $photos );

		} else {
			$remote_user_id = $remote_users[0]->id;
		}

		$subscription = array( 
			'subscriber_id' => $subscriber_id,
			'remote_user_id' => $remote_user_id,
		);
		$this->db->insert( 'remote_subscriptions', $subscription );
	}


}