<?PHP

class Site_option extends DataMapper {

	public function __construct( $id = NULL ){
		parent::__construct( $id );
	}

	public function get_options() {
		
		$query = $this->db->get('site_options');
		
		// $this->db->order_by('time', 'desc');
		// No order or groups (yet) but maybe later?

		$options = $query->result_array();

		foreach ($options as $key => &$option) {
			if ( ! $option['label'] ) {
				$option['label'] = ucwords( $option['key'] );
			}
		}

		return $options;

	}

	public function get_option_values() {

		$query = $this->db->get('site_options');
	
		foreach ($query->result_array() as $i => $option) {
			$options[ $option['key'] ] = $option['value'];
		}

		return $options;

	}

	public function update_options($options){
		return $this->db->update_batch('site_options', $options, 'key');
	}
}