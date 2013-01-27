<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Pubsubhubbub {

	public function __construct( $config ) {
		$this->callback = site_url( 'pubsubhubbub/callback' );

		$this->hubs = $config['hubs'];
		$this->secret = $config['secret'];

	}

    public function update_hub( $updated_urls, $hubs = array() ) {

		if ( empty( $updated_urls ) ) {
			return false;
		}

		if ( ! is_array( $updated_urls ) ) {
		    $updated_urls = array( $updated_urls );
		}

		if ( ! is_array( $hubs ) ) {
		    $hubs = array( $hubs );
		}

		if ( empty( $hubs ) ) {
		    // $hubs = $this->config->item( 'hubs', 'pubsubhubbub' );
		    $hubs = $this->hubs;
		}

		$params = "hub.mode=publish";
		foreach ( $updated_urls as $url ) {
		    $params .= "&hub.url=" . urlencode( $url );
		}

		$curl_opt = array(
		    CURLOPT_POST => TRUE,
		    CURLOPT_POSTFIELDS => $params,
		    CURLOPT_USERAGENT => 'Photog.io',
		    CURLOPT_FOLLOWLOCATION => true,
	    );

	    foreach ($hubs as $hub) {
			$curl_opt[CURLOPT_URL] = $hub;

			$ch = curl_init();
			curl_setopt_array($ch, $curl_opt);
			curl_exec($ch);

			$curl_info = curl_getinfo($ch);
			curl_close($ch);

			if ($curl_info['http_code'] !== 204) {
			    return false;
			}
		}

		return true;
    }

    public function get_feed_url( $user_url ) {
		$ci =& get_instance();
		$ci->load->library('simple_html_dom');

		if ( stripos( $user_url, 'http://' ) !== 0 ) {
			$user_url = 'http://' . $user_url;
		}

		$html = @file_get_html( $user_url );
		if ( ! $html ) {
			return false;
		}

		$elements = $html->find('link[type="application/atom+xml"]');

		if ( count($elements) === 1 && $elements[0]->rel == "alternate" ) {
			$feed_url = $elements[0]->href;
			return $feed_url;
		} else {
			return false;
		}
    }

    public function subscribe( $feed ) {
    	return $this->change_subscription( 'subscribe', $feed );
    }

    public function unsubscribe( $feed ) {
    	return $this->change_subscription( 'unsubscribe', $feed );
    }

    public function get_hubs( $feed ) {
		// Get hub(s) from feed
		$xml = simplexml_load_file( $feed );
		$xml->registerXPathNamespace('a', 'http://www.w3.org/2005/Atom');
		
		foreach ($xml->xpath('/a:feed/a:link[@rel="hub"]/@href') as $link) {
			$hubs[] = (string) $link;
		}

		return $hubs;
    }

   public function calculate_hmac( $body, $key ) {
	// $this->config->item( 'encryption_key', 'pubsubhubbub' )
   	return hash_hmac( 'sha1', $body, $key );

   }

    public function change_subscription( $mode, $feed, $hubs = array() ) {

    	if ( ! is_array( $hubs ) ) {
    		$hubs = array( $hubs );
    	}

    	if ( empty( $hubs ) ) {
    		$hubs = $this->get_hubs( $feed );
    	}

    	$secret = $this->secret;

    	$params = array(
    		'hub.mode' => $mode,
			'hub.verify' => 'sync',
			'hub.callback' => $this->callback,
			'hub.topic' => $feed,
			'hub.secret' => $secret,
    	);

    	$curl_opt = array(
    		CURLOPT_POST => true,
    		CURLOPT_POSTFIELDS => $params,
    		CURLOPT_RETURNTRANSFER => true,
    		CURLOPT_FOLLOWLOCATION => true,
		);

    	foreach ( $hubs as $hub ) {
	    	$ch = curl_init( $hub );
	    	curl_setopt_array( $ch, $curl_opt );
	    	curl_exec( $ch );
	    	$curl_info = curl_getinfo($ch);
			curl_close($ch);

			if ( $curl_info['http_code'] !== 204 ) {
				echo 'Error changing subscription!';
				echo "\n";
				echo 'HTTP status code: ';
				echo $curl_info['http_code'];
				die();
			    return false;
			} else {
				$http_codes[] = $curl_info['http_code'];
			}

    	}
    	return $http_codes;
    }

}

/* End of file Pubsubhubbub.php */