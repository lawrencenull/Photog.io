<p>Enter the URL of the user you'd like to follow below.</p>

<?PHP echo form_open(); ?>

<?PHP echo form_label('URL', 'url'); ?>
<?PHP echo form_input( 'url', set_value('url', $user_url), 'autofocus' ); ?>

<button class="btn btn-primary" type="submit" name="submit">Follow</button>

<?PHP form_close(); ?>