<div class="photo">
	<img class="image" src="<?PHP echo $photo->img_url; ?>" alt="" />
</div>
<?PHP echo form_open(); ?>
<button class="btn" type="submit" name="no" value="true">No</button>
<button class="btn btn-primary" type="submit" name="confirm_deletion" value="true">Yes</button>
<?PHP echo form_close(); ?>