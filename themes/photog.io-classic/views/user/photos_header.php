<header class="user">
	<img class="user-icon" width="100" height="100" src="<?PHP echo html_escape($user->user_icon_url); ?>" alt="" />
	<h2 class="username"><?PHP echo $user->username; ?></h2>
	<?PHP $this->load->view('subscription/follow_button'); ?>
</header>