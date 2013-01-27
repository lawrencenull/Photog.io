<?PHP echo form_open(); ?>


	<label for="real_name">Real name</label>
	<input type="text" name="real_name" value="<?PHP echo set_value('real_name', 'Bob') ?>" id="real_name" />

	<label for="favorite_color">Real name</label>
	<input type="text" name="favorite_color" value="<?PHP echo set_value('favorite_color', 'Green') ?>" id="favorite_color" />

<button class="btn btn-primary" type="submit" name="submit">Save</button>

<?PHP echo form_close(); ?>