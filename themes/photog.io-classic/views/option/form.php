<?PHP echo form_open(); ?>

<?PHP foreach ($options as $i => $option) { ?>
	<label for="options[<?PHP echo html_escape($option['key']); ?>]"><?PHP echo html_escape($option['label']); ?>
	<p><?PHP echo html_escape($option['description']); ?></p>
	</label><input type="text" name="options[<?PHP echo html_escape($option['key']); ?>]" value="<?PHP echo html_escape($option['value']); ?>" placeholder="<?PHP echo html_escape($option['label']); ?>" id="options[<?PHP echo html_escape($option['key']); ?>]" />
<?PHP } ?>

<button class="btn btn-primary" type="submit" name="submit">Save</button>

<?PHP form_close(); ?>