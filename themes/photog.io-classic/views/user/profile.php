<p>Everything you'd want to know about <?PHP echo $user->username; ?></p>

<?PHP $this->load->view('subscription/follow_button', $this->data); ?>

<p><a href="../"><?PHP echo $user->photo_count; ?> photos</a></p>
<p>Following <?PHP echo $user->subscription_count; ?> people</p>


<img class="user-icon" src="<?PHP echo html_escape($user->user_icon_url); ?>" alt="" />