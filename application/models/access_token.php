<?PHP

class access_token extends CI_Model {
	
	public function __construct( $id = NULL ){
		parent::__construct( $id );

		if ( $this->ion_auth->logged_in() ) {
			$ci =& get_instance();
			$current_user = $ci->ion_auth->user()->row();
			$this->current_user_id = $current_user->id;
		} else {
			$this->current_user_id = 0;
		}

	}
	

	public function save( $provider, $token_arr = array() ) {

		$token_arr['user_id'] = $this->current_user_id;
		$token_arr['provider'] = $provider;

		if ( $this->get( $provider ) ) {
			// Access token DOES exist for given provider and user
			$where = array(
				'user_id' => $this->current_user_id,
				'provider' => $provider,
			);

			$this->db->where( $where );
			return $this->db->update( 'oauth_access_tokens', $token_arr );
		} else {
			// Access token does NOT exist for given provider and user
			return $this->db->insert( 'oauth_access_tokens', $token_arr );
		}

	}

	public function get( $provider ) {

		$params = array(
			'provider' => $provider,
			'user_id' => $this->current_user_id,
		);

		$query = $this->db->get_where( 'oauth_access_tokens', $params );
		if ( $token = $query->row() ) {
			$token_arr['access_token'] = $token->token;
			$token_arr['access_secret'] = $token->secret;
			return $token_arr;
		} else {
			return false;
		}

	}

	public function delete( $provider ) {

		$params = array(
			'provider' => $provider,
			'user_id' => $this->current_user_id,
		);

		return $this->db->delete( 'oauth_access_tokens', $params );
		
	}

}