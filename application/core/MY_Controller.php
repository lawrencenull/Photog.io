<?PHP

class MY_Controller extends CI_Controller {
	function __construct() {
		parent::__construct();
		
		// http://philsturgeon.co.uk/blog/2009/08/UTF-8-support-for-CodeIgniter
		$this->output->set_header('Content-Type: text/html; charset=utf-8');

		$this->data['title'] = '';
		$this->data['head'] = '';
		$this->data['pre_content'] = '';
		$this->data['post_content'] = '';
		$this->data['body_classes'] = array();

		// $this->theme_slug = 'default';
		$this->theme_slug = $this->config->item( 'theme' );
		$this->load->set_theme( $this->theme_slug );

		$user = $this->ion_auth->user()->row();

		if ( $this->ion_auth->logged_in() ) {
			$user_url = base_url( $user->username );
			$this->data['welcome'] = 'Welcome, <a href="' . $user_url . '">' . $user->username . '!</a> :D';
			$this->data['current_user'] = $user;
		} else {
			$this->data['welcome'] = "Welcome, stranger! :)";
		}

		if ( ! $this->ion_auth->logged_in() ) {
			$nav['main'][] = array( 'href'=>'/login', 'icon'=>'off icon-white', 'text'=>'Log in' );
			$nav['main'][] = array( 'href'=>'/register', 'icon'=>'user icon-white', 'text'=>'Register' );
			$nav['secondary'] = '';
		} else {
			$nav['main'][] = array( 'href'=>'/upload', 'icon'=>'picture icon-white', 'text'=>'Upload Photo' );
			$nav['main'][] = array( 'href'=>'/snap', 'icon'=>'camera icon-white', 'text'=>'Take Photo', 'li_class' => 'snap' );
			// $nav['main'][] = array( 'href'=>'/logout', 'icon'=>'off icon-white', 'text'=>'Log out' );
			$nav['secondary'][] = array( 'href'=>'/logout', 'icon'=>'off icon-white', 'text'=>'Log out' );
		}

		$this->data['nav']['main'] = nav( $nav['main'], 'nav' );
		$this->data['nav']['secondary'] = nav( $nav['secondary'], 'nav' );
	}

}