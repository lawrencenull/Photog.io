<?PHP if ( $show_button['follow'] ) { ?>

<?PHP echo form_open( 'follow', array('class'=>'follow follow-unfollow') ); ?>

<?PHP echo form_hidden( 'url', set_value('url', $user->username) ); ?>

<button class="btn btn-primary" type="submit" name="submit">Follow</button>

<?PHP echo form_close(); ?>

<?PHP } elseif( $show_button['unfollow'] ) { ?>

	<?PHP echo form_open( 'unfollow', array('class'=>'unfollow follow-unfollow') ); ?>

	<?PHP echo form_hidden( 'url', set_value('url', $user->username) ); ?>

	<button class="btn btn-danger" type="submit" name="submit">Unfollow</button>

	<?PHP echo form_close(); ?>

<?PHP } ?>