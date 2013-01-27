<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Photo extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('local_photo');
		$this->load->model('remote_photo');
	}

	public function home() {
		if ( $this->ion_auth->logged_in() ) {
			$this->view_subscription_photos();
		} else {
			$this->index();
		}
	}

	public function index() {
		/*$this->load->library('pubsubhubbub');
		var_dump( $this->pubsubhubbub->subscribe( 'http://photos.joe.im/joe/feed/' ) ); die();*/

		if ( $this->config->item( 'index' ) ) {
			$this->view_by_username( $this->config->item( 'index' )  );
		} else {
			
			// BEGIN pagination
			$this->load->config('pagination', true);
			$pagination_config = $this->config->item('pagination');
			$this->load->library('pagination');

			$pagination_config['total_rows'] = count( $this->local_photo->get_local_photos() );
			$pagination_config['first_url'] = '/';
			$pagination_config['base_url'] = site_url('page');
			$pagination_config['uri_segment'] = 2;

			$pagination_config['current_page'] = $this->uri->segment( $pagination_config['uri_segment'], 1 );
			$pagination_config['offset'] = ( $pagination_config['current_page'] - 1 ) * $pagination_config['per_page'];

			$this->data['photos'] = $this->local_photo->get_local_photos( null, $pagination_config['per_page'], $pagination_config['offset'] );
			
			$this->pagination->initialize( $pagination_config ); 
			$this->data['pagination'] = $this->pagination->create_links();
			// END pagination
			
			$this->load->view('header', $this->data);
	        $this->load->view('photo/list', $this->data);
	        $this->load->view('footer');
		}

	}

	public function view_single_photo( $id ){
		$current_url = '/' . $this->uri->uri_string(); // 
		$this->session->set_userdata(array('continue_url'=>$current_url));

		$this->data['body_classes'][] = 'single-photo';

		$where = array( 'local_photos.id' => $id );

		$results = $this->local_photo->get_local_photos( array('where' => $where) );

		if ( empty( $results[0] ) ) {
			do_404();
			return;
		}
		$this->data['photo'] = $results[0];
		
		$this->load->view('header', $this->data);
	    $this->load->view('photo/permalink', $this->data);
	    $this->load->view('footer');

	}

	public function save_data_uri( $new_path, $data_uri ){

		preg_match( '#data:image/([a-z]+);#', $data_uri, $matches );

		// data:image/jpeg;

		$extension = $matches[1];

		if ( $extension == 'jpeg' ) {
			$extension = 'jpg';
		}

		$data_uri = substr($data_uri,strpos($data_uri,",")+1);
		$decoded_data = base64_decode( $data_uri );
		
		$filename = md5( microtime() ) . ".$extension";

		file_put_contents( "$new_path/$filename", $decoded_data );

		return $filename;
	}

	public function upload(){
		require_auth();

		if ($_POST) {

			if ( $this->input->get('ajax', true) ) {
				$data_uri = $this->input->post('image', false);
				
				$new_path = './photos/';
				$new_filename = $this->save_data_uri( $new_path, $data_uri );

				$photo = array(
					'caption' => '',
					'user_id' => $this->data['current_user']->id,
					'filename' => $new_filename,
					'time' => date('Y-m-d H:i:s'),
				);

				$publish_results = $this->local_photo->publish( $photo );
				$response['photo']['permalink'] = $publish_results['permalink'];
				// $response = $publish_results;
				$this->output->set_output( json_encode( $response ) );
				return;
				
			}

			$config['upload_path'] = './photos/';
			$config['allowed_types'] = 'gif|jpg|png';
			// $config['max_size']	= '100';
			// $config['max_width']  = '1024';
			// $config['max_height']  = '768';
			$config['encrypt_name']  = TRUE;

			$this->load->library('upload', $config);


			if ( ! $this->upload->do_upload('file') ) {
				// $this->data['error'] = $this->upload->display_errors();

				$this->session->set_flashdata( 'error', $this->ion_auth->errors() );

			} else {
				// http://ellislab.com/codeigniter/user-guide/libraries/file_uploading.html
				/* $this->upload->data()
				Array (
				    [file_name] => 3c4b6d52d036901d4a2c852e311617ce.gif
				    [file_type] => image/gif
				    [file_path] => /Users/joe/Dropbox/www/local/shmitstagram/photos/
				    [full_path] => /Users/joe/Dropbox/www/local/shmitstagram/photos/3c4b6d52d036901d4a2c852e311617ce.gif
				    [raw_name] => 3c4b6d52d036901d4a2c852e311617ce
				    [orig_name] => Dandelion.gif
				    [client_name] => Dandelion.gif
				    [file_ext] => .gif
				    [file_size] => 2.66
				    [is_image] => 1
				    [image_width] => 48
				    [image_height] => 48
				    [image_type] => gif
				    [image_size_str] => width="48" height="48"
				) */
				
				$upload_data = $this->upload->data();

				$user = $this->ion_auth->user()->row();
				$photo['user_id'] = $user->id;
				$photo['caption'] = $this->input->post('caption', true);
				$photo['filename'] = $upload_data['file_name'];
				// $photo['orig_name'] = $upload_data['orig_name'];
				$photo['time'] = date('Y-m-d H:i:s');

				$this->local_photo->publish( $photo );

				redirect();
			}

		}

		// Show upload form
		$this->data['title'] = 'Upload';
		$this->load->view('header', $this->data);
        $this->load->view('photo/upload', $this->data);
        $this->load->view('footer');
	}

	public function snap(  ) {
		require_auth();

		$this->load->view('header', $this->data);
        $this->load->view('photo/snap', $this->data);
        $this->load->view('footer');
	}	

	public function view_by_username( $username = array() ) {

		if ( is_string( $username ) ) {
			// $atom_feed_url = $username . '/feed/?format=' . 'atom' . '&limit='. 10 .'&offset=' . 0;
			$atom_feed_url = "$username/feed/";
			$this->data['head'] .=  link_tag( $atom_feed_url, 'alternate', 'application/atom+xml' );
			$this->data['head'] .=  "\n";

			$canonical_url = "$username/";
			$this->data['head'] .=  link_tag( $canonical_url, 'canonical' );
			$this->data['head'] .=  "\n";
		}

		if ( empty($username) ) {
			redirect( $this->data['current_user']->username );
		}

		if ( !is_array($username) ) {

			if ( strpos($username, ',') ) {
				$usernames = explode(',', $username);
				$usernames = array_map('trim', $usernames);
			} else {
				$usernames[] = $username;
			}

		}

		$this->data['body_classes'][] = 'user-photos';

		$this->load->model('user');
		
		$user_id = $this->user->get_user_id( $usernames );
		
		$where['where_in'] = array( 'user_id' => $user_id );


		// BEGIN pagination
		$this->load->config('pagination', true);
		$pagination_config = $this->config->item('pagination');
		$this->load->library('pagination');

		$pagination_config['total_rows'] = count( $this->local_photo->get_local_photos( $where ) );
		$pagination_config['first_url'] = '/' . $this->uri->segment( 1 );
		$pagination_config['base_url'] = site_url( $this->uri->segment( 1 ) .'/page');
		$pagination_config['uri_segment'] = 3;
		$pagination_config['current_page'] = $this->uri->segment( $pagination_config['uri_segment'], 1 );

		$pagination_config['offset'] = ( $pagination_config['current_page'] - 1 ) * $pagination_config['per_page'];

		$this->data['photos'] = $this->local_photo->get_local_photos( $where, $pagination_config['per_page'], $pagination_config['offset'] );

		$this->pagination->initialize( $pagination_config ); 
		$this->data['pagination'] = $this->pagination->create_links();
		// END pagination

		$this->data['user'] = $this->user->get_user_by_id( $user_id[0] );
		$this->data['user']->user_icon_url = $this->user->get_icon_url( $user_id[0], 100 );

		// $this->data['photos'] = $this->local_photo->get_local_photos( $where );
		// $this->data['photos'] is empty array if the user has no photos, FALSE if user doesn't exist.
		// $query->num_rows()

		$user_id = $user_id[0];
		if ( ! empty( $this->data['current_user'] ) ) {
			$current_user_id = $this->data['current_user']->id;
		} else {
			$current_user_id = false;
		}

		$this->load->model('local_subscription');
		$this->data['show_button'] = $this->local_subscription->show_button( $user_id );
		if ( ! empty( $this->data['photos'] ) ) {
			$this->load->view('header', $this->data);
			$this->load->view('user/photos_header', $this->data);
	        $this->load->view('photo/list', $this->data);
		} else {
			$this->data['post_content'] = '<p>Sorry, no photos here!</p>';
			$this->load->view('header', $this->data);
			$this->load->view('user/photos_header', $this->data);
		}

        $this->load->view('footer');

	}

	public function view_subscription_photos() {
		// The "dashboard"
		require_auth();
		
		// BEGIN pagination
		$this->load->config('pagination', true);
		$pagination_config = $this->config->item('pagination');
		$this->load->library('pagination');

		$pagination_config['total_rows'] = count( $this->local_photo->get_local_photos() );
		$pagination_config['first_url'] = '/';
		$pagination_config['base_url'] = site_url('page');
		$pagination_config['uri_segment'] = 2;

		$pagination_config['current_page'] = $this->uri->segment( $pagination_config['uri_segment'], 1 );
		$pagination_config['offset'] = ( $pagination_config['current_page'] - 1 ) * $pagination_config['per_page'];

		$this->data['photos'] = $this->local_photo->get_local_photos( null, $pagination_config['per_page'], $pagination_config['offset'] );
		
		$this->pagination->initialize( $pagination_config ); 
		$this->data['pagination'] = $this->pagination->create_links();
		// END pagination

		$this->data['photos'] = $this->local_photo->get_subscription_photos( null, $pagination_config['per_page'], $pagination_config['offset'] );		

		if ( ! empty( $this->data['photos'] ) ) {
			$this->load->view('header', $this->data);
	        $this->load->view('photo/list', $this->data);
	        $this->load->view('footer');
		} else {
			$this->load->view('header', $this->data);

			$subscription_count = $this->user->count_subscriptions( $this->data['current_user']->id );
			if ( $subscription_count ) {
				$this->data['post_content'] .= '<p>None of the cool people you follow have posted anything!</p>';
				$this->data['post_content'] .= '<p>Try following more people!</p>';
			} else {
				$this->data['post_content'] .= "<p>You're not following anyone!</p>";
			}

			$this->load->view('footer', $this->data);
		}


	}

	public function edit( $photo_id = 0 ){

		// $photo_id = $this->input->post('photo_id', true);
		$caption = $this->input->post('caption', true);
		$caption = trim( $caption );

		if ( ! empty( $photo_id ) && isset( $caption ) ) {
			$this->load->model('Local_photo');
			$result = $this->Local_photo->update_caption( $photo_id, $caption );
			
			if ( $result == true) {
				$response['caption'] = $caption;
			} else {
				$response = false;
			}

			$this->output->set_output( json_encode( $response ) );
			// die();
			// redirect();

		} else {
			$response = 'Error!';
			$this->output->set_output( json_encode( $response ) );
		}
	}	

	public function delete( $photo_id ){
		require_auth();

		$photo = $this->local_photo->get_photo_by_id( $photo_id );
		$photo = $photo[0];

		if ( $this->ion_auth->is_admin() || $photo->user_id == $this->data['current_user']->id ) {
			$this->data['photo'] = $photo;

			if ( $this->input->post('confirm_deletion', true) ) {
				if ( unlink( $_SERVER['DOCUMENT_ROOT'] . '/photos/' . $photo->filename ) && $this->db->delete('local_photos', array('id' => $photo_id)) ) {
					$this->session->set_flashdata( 'success', 'Your photo has been successfully deleted!' );
				} else {
					$this->session->set_flashdata( 'error', "I'm sorry, unfortunately there was a problem deleting your photo. Please try again. :(" );
				}
				redirect();
			} elseif ( $this->input->post('no', true) ) {
				redirect( $this->session->userdata( 'continue_url' ) );
			}
			

			$this->data['body_classes'][] = 'delete-photo';

			$this->data['title'] = 'Are you sure you want to delete this photo?';
			$this->load->view('header', $this->data);
			$this->load->view('photo/confirm_deletion', $this->data);
	        $this->load->view('footer');
		} else {
			// Photo NOT owned by currently logged-in user or an admin!
			redirect();
			die();
		}

	}

	function resize( $size_str, $original_filename ) {
		if ( preg_match('#([0-9]+)x([0-9]+)#', $size_str, $matches) ) {
			$width = $matches[1];
			$height = $matches[2];
		} else {
			$width = $size_str;
			$height = $size_str;
		}

		if ( ! is_file( "photos/$original_filename" ) ) {
			die('404 should go here but I\'m too lazy right now');
		}
		$this->load->library('image_lib');

		$config['image_library'] = 'gd2';
		$config['source_image']	= "photos/$original_filename";
		$config['maintain_ratio'] = TRUE;
		$config['width']	= $width;
		$config['height']	= $height;

		$config['dynamic_output'] = TRUE;
		$this->image_lib->initialize( $config );
		$this->image_lib->resize();

		$config['dynamic_output'] = FALSE;
		$config['new_image'] = "photos/$size_str/$original_filename";
		$this->image_lib->clear();
		$this->image_lib->initialize( $config );
		$this->image_lib->resize();


	}

}