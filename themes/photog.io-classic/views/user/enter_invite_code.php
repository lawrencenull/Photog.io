<?PHP echo form_open(); ?>

<p><?PHP echo $this->config->item( 'site_title' ); ?> is not yet open for public registration.</p>
<p>If you have an invite code, enter it below.</p>
<p>Otherwise feel free to <a href="<?PHP echo site_url('invite/request'); ?>">request an invite.</a></p>
<button class="btn btn-primary pull-right" type="submit" name="submit">Continue</button>
<span class="input-wrapper"><label for="invite-code">Invite Code</label><input type="text" name="invite-code" value="" id="invite-code" autofocus /></span>

<?PHP echo form_close(); ?>