<?PHP

class Remote_user extends DataMapper {

	var $has_many = array('remote_photo');

	public function __construct( $id = NULL ){
		parent::__construct( $id );
	}

	public function get_remote_users( $where = array() ) {

		$this->db->select( '*' );
		$this->db->from('remote_users');

		if ( $where ) {
			$this->db->where( $where );
		}
		// $this->db->join('users', 'users.id = local_subscriptions.subscribee_id');

		$query = $this->db->get();
		$result = $query->result();

		return $result;
	}

	public function get_by_feed( $feed_url ) {
		$this->db->select( '*' );
		$this->db->from('remote_users');
		$this->db->where( array( 'feed_url' => $feed_url ) );

		$query = $this->db->get();
		$result = $query->result();

		$remote_user = $result[0];

		return $remote_user;
	}


	public function add_remote_user( $remote_user ) {
			
		/*$remote_user = array(
			'user_url' => $user_url,
			'feed_url' => $feed_url,
		);*/

		$this->db->insert( 'remote_users', $remote_user );
		return $this->db->insert_id();
	}

}