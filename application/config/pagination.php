<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['num_links'] = 20;
$config['use_page_numbers'] = true;
$config['per_page'] = 8;


// HTML stuff below... (the easy part)
$config['full_tag_open'] = '<nav class="pagination">';
$config['full_tag_close'] = '</nav>';

$config['first_link'] = false;
$config['last_link'] = false;

$config['next_link'] = 'Next';
$config['next_tag_open'] = '<span class="next">';
$config['next_tag_close'] = '</span>';

$config['prev_link'] = 'Prev';
$config['prev_tag_open'] = '<span class="prev">';
$config['prev_tag_close'] = '</span>';

$config['display_pages'] = FALSE;

$config['num_tag_open'] = '<span class="number">';
$config['num_tag_close'] = '</span>';

$config['cur_tag_open'] = '<span class="current">';
$config['cur_tag_close'] = '</span>';