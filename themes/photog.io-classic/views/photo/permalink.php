<script>
$(document).ready(function() {


	var caption = document.querySelector('.caption');
	if ( caption ) {
		caption.addEventListener('blur', function(){
			// var caption = this.innerHTML.replace('&nbsp;', ' ');
			// caption = caption.replace('&amp;', '&');
			var caption = this.innerHTML;
			caption_container = this;
			var send_data = { caption: caption };
			console.log( send_data );
			edit_photo( send_data, function(data){
				if ( data.caption ) {
					caption_container.innerHTML = data.caption;
				} else {
					alert('There was an error saving your caption. Please try again!');
					document.location = '';
				}
			} );

		});
	}

	$(function(){

  $(".caption").live("keyup keydown mouseup", function(e){
	if (e.which == 13) {
		e.preventDefault();
		this.blur();
		return false;
	}
  });

});

	
});
</script>

<?PHP // $this->load->view('photo/single'); ?>
<?PHP $this->load->view('photo/meta'); ?>
<?PHP
$options_nav = '';
if ( $this->ion_auth->logged_in() ) {
	$options_nav[] = array( 'class' => '', 'href'=>'#', 'icon'=>'heart icon-white', 'text'=>'Like' );
}
// $options_nav[] = array( 'class' => '', 'href'=>'#', 'icon'=>'pencil icon-white', 'text'=>'Edit' );

if ( $photo->editable ) {
	$options_nav[] = array( 'class' => '', 'href'=>$photo->delete_url, 'icon'=>'trash icon-white', 'text'=>'Delete' );
}

// $options_nav[] = array( 'class' => 'lightbox', 'href'=>$photo->img_url, 'icon'=>'zoom-in icon-white', 'text'=>'Zoom' );

?>
<div class="photo">
	<!-- <a class="lightbox" href="<?PHP echo $photo->img_url; ?>"> -->
		<img class="image" src="<?PHP echo $photo->img_url; ?>" alt="" />
	<!-- </a> -->

	<?PHP echo nav( $options_nav, 'options' ); ?>

	<?PHP if ( ! empty( $photo->caption ) || $photo->editable ) { ?>
		<div class="caption" <?PHP if ( $photo->editable ) { echo 'contenteditable="true"'; } ?>>
		<?PHP if ( ! empty( $photo->caption ) ) { echo strip_tags($photo->caption); } ?>
		</div>
	<?PHP } ?>



</div>