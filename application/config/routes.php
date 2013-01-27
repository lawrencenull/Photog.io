<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/


$route['default_controller'] = 'photo/home';
$route['page/(:num)'] = 'photo/home';

$route['pubsubhubbub/callback'] = 'pubsubhubbub_controller/callback';

$route['me/authorize/(:any)'] = 'auth/oauth/$1';
$route['tumblr_dash'] = 'tumblr_controller/get_dash';

$route['users'] = 'users';
$route['login'] = 'users/login';
$route['logout'] = 'users/logout';
$route['register'] = 'users/register';

$route['invite/request'] = 'invite/request';
$route['register/(:any)'] = 'users/register/$1';

$route['me'] = 'photo/view_by_username';
$route['me/settings'] = 'users/edit';
$route['me/invites'] = 'users/invites';
$route['me/following'] = 'subscription';
$route['me/followers'] = 'subscription/show_followers';

$route['(:any)/feed'] = 'feed/user_photos/$1';
$route['(:any)/about'] = 'users/profile/$1';

$route['follow'] = 'subscription/follow';
$route['follow/(:any)'] = 'subscription/follow/$1';

$route['unfollow'] = 'subscription/unfollow';
$route['unfollow/(:any)'] = 'subscription/unfollow/$1';

$route['dashboard'] = 'photo/view_subscription_photos';

$route['admin/options'] = 'site_options';

$route['upload'] = 'photo/upload';
$route['snap'] = 'photo/snap';

$route['(:any)/(:num)/delete'] = 'photo/delete/$2';
$route['(:any)/(:num)/edit'] = 'photo/edit/$2';
$route['(:any)/(:num)'] = 'photo/view_single_photo/$2';

$route['photos/(:any)/(:any)'] = 'photo/resize/$1/$2';

$route['(:any)'] = 'photo/view_by_username/$1';
$route['(:any)/page/(:num)'] = 'photo/view_by_username/$1';


$route['404_override'] = 'error/error_404';

/* End of file routes.php */
/* Location: ./application/config/routes.php */