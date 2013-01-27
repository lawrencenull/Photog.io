<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('get_local_username_by_url')) {

	function get_local_username_by_url( $user_url ) {

		if ( stripos( $user_url, '.' ) === FALSE && stripos( $user_url, '/' ) === FALSE ) {
			// Not a URL at all, must be a local username (joe)
			return $user_url;
		}

		$user_url = strip_protocol( $user_url );
		$base_url = strip_protocol( base_url() );

		$username = substr( $user_url, strlen( $base_url ) );
		$username = trim_slashes( $username );
		// var_dump( $username ); die();
		return $username;
	}

}

if ( ! function_exists('theme_url')) {

	function theme_url( $path = null ) {
		$ci=& get_instance();
		$theme_slug = $ci->theme_slug;
		return base_url("themes/$theme_slug/$path");
	}

}

if ( ! function_exists('strip_protocol')) {

	function strip_protocol( $url ) {
		preg_match( '#https?://(.+)#', $url, $matches );

		if ( !empty( $matches[1] ) ) {
			$url = $matches[1];
		}
		return $url;
	}

}



