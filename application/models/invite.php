<?PHP

class Invite extends DataMapper {

	var $has_one = array('local_user');

	public function __construct( $id = NULL ){
		parent::__construct( $id );
	}

	public function check_code( $code_to_check ) {
		// Returns true if valid invite code, false otherwise
		$query = $this->db->get_where( 'invites', array( 'code' => $code_to_check ) );
		$code_info = $query->row();

		if ( !empty($code_info) ) {
			if ( ($code_info->uses < $code_info->allowed_uses) || ($code_info->allowed_uses == 0) ) {
				return true;
			}
		}

		return false;

		// $code_info
		/* stdClass Object (
		    [id] => 1
		    [code] => lol
		    [created_by] => 5
		    [redeemed_by] => 0
		    [allowed_uses] => 0
		    [uses] => 0
		) */
	}

	public function use_code( $code ) {
		
		$this->db->set('uses', 'uses+1', FALSE);
		$this->db->where('code', $code);
		if ( $this->check_code( $code ) && $this->db->update('invites') ) {
			return true;
		} else {
			return false;
		}
	}
	
	public function get_codes( $user_id ) {
		$query = $this->db->get_where( 'invites', array( 'created_by' => $user_id ) );
		return $query->result();
	}

	public function generate_codes( $quantity = 1 ) {
		for ($i=0; $i<$quantity; $i++) {
			$codes[] = random_string( 'md5' );
		}
		return $codes;
	}

	public function give_codes( $user_id, $codes = 1 ) {

		if (!is_array($codes)) {
			$codes = $this->generate_codes( $codes );
		}

		foreach ($codes as $i => $code) {
			$invites[$i]['created_by'] = $user_id;
			$invites[$i]['code'] = $code;
		}

		return $this->db->insert_batch( 'invites', $invites );
	

	}


}