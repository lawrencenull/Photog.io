<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('nav')) {
    function nav($nav_array, $ul_class, $echo = FALSE) {
        
        $ci =& get_instance();
        $current_url = '/' . $ci->uri->uri_string();

        if ( !empty( $nav_array ) ) {
            $nav = '<ul class="'. $ul_class .'">';
            foreach ( $nav_array as $i => $item ) {
                if ( !empty( $item['li_class'] ) ) {
                    $li_class_arr[] = $item['li_class'];
                } else {
                    $li_class_arr = array();
                }
                if ( $current_url == $item['href'] ) {
                    $li_class_arr[] = 'active';
                }

                if ( strpos($item['href'], '/') === 0 ) {
                    $item['href'] = site_url( $item['href'] );
                }

                if ( empty($item['class']) ) {
                    $item['class'] = '';
                }
                $li_class = implode(' ', $li_class_arr);
                $nav .= '<li class="'. $li_class .'">';
                $nav .= '<a href="'. $item['href'] .'" class="'. $item['class'] .'">';
                if ( !empty( $item['icon'] ) ) {
                    $nav .= '<i class="icon-'. $item['icon'] .'"></i>';
                }
                $nav .= $item['text'];
                $nav .= '</a>';
                $nav .= '</li>';
            }
            $nav .= '</ul>';
        } else {
            $nav = '';
        }


    	if ($echo) {
    		echo $nav;
    	} else {
        	return $nav;
    	}
    }   
}

if ( ! function_exists('title')) {
    function title() {

        $ci =& get_instance();

        if ( $ci->data['title'] ) {
            $title = $ci->data['title'] . ' - ' . $ci->config->item( 'site_title' );
        } else {
            $title = $ci->config->item( 'site_title' );
        }

        return $title;
    }

}

if ( ! function_exists('body_class')) {
    function body_class() {

        $ci =& get_instance();
        if ( !empty( $ci->data['body_classes'] ) ) {
            
            return ' class="' . implode( ' ', $ci->data['body_classes'] ) . '"';

        } else {
            return '';
        }

    }
}

/*if ( ! function_exists('get_image_url')) {
    function get_image_url( $photo,  ) {


    }
}*/

