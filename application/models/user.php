<?PHP

class User extends DataMapper {

	var $has_many = array('local_photo', 'user', 'remote_user', 'invite');
	
	public function __construct( $id = NULL ){
		parent::__construct( $id );
	}
	

	public function count_photos( $user_id ) {

		if ( is_object( $user_id ) ) {
			$user_id = $user_id->id;
		}

		$this->db->where( 'user_id', $user_id );
		$this->db->from('local_photos');
		return $this->db->count_all_results();
	}

	public function count_subscriptions( $user_id ) {

		$this->db->where( 'subscriber_id', $user_id );
		$this->db->from('local_subscriptions');
		$local_count = $this->db->count_all_results();

		$this->db->where( 'subscriber_id', $user_id );
		$this->db->from('remote_subscriptions');
		$remote_count = $this->db->count_all_results();

		return $local_count + $remote_count;

	}

	public function get_user_id( $username = '' ) {
		$this->db->select( 'id' );
		$this->db->from('users');

		if ( is_array($username) ) {
			$this->db->where_in( 'username', $username );

		} else {
			$this->db->where( array( 'username' => $username ) );
		}

		$query = $this->db->get();
		$results = $query->result();
		if ( ! $results ) {
			return false;
		}

		if ( is_array( $username ) ) {
			foreach ($results as $i => $result) {
				$ids[] = $result->id;
			}

			return $ids;
		} else {
			return $results[0]->id;
		}
		

	}

	public function get_user_by_username( $username ) {
		$id = $this->get_user_id( $username );
		return $this->get_user_by_id( $id );
	}

	public function get_username_by_id( $id ) {
		$user = $this->get_user_by_id( $id );
		return $user->username;
	}

	public function get_user_by_id( $id ) {
		if ( ! $id ) {
			return false;
		}
		$this->db->select( array('id', 'username', 'email') );
		$this->db->from('users');
		$this->db->where( array( 'id' => $id ) );

		$query = $this->db->get();
		$results = $query->result();

		foreach ($results as $i => &$user) {
			$user->photos_url = site_url( $user->username );
		}

		$user = $results[0];
		// var_dump( $user );
		return $user;
	}

	public function get_email_by_id( $id ) {
		$user = $this->get_user_by_id( $id );
		if ( !empty( $user->email ) ) {
			return $user->email;
		} else {
			return false;
		}
	}

    public function get_icon_url( $user, $size = 50 ) {
        // $user should be either a user ID, user object, user name, or an email address

        $email = $this->get_email_by_id( $user );
        $hash = md5( strtolower( trim( $email ) ) );
        $default = 'identicon';

        $base_url = 'https://www.gravatar.com/avatar/';
        $url = $base_url . $hash . '.jpg?d=' . $default .'&s=' . $size;
        return $url;
    }


}