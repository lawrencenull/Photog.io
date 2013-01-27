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
$options_html = nav( $options_nav, 'options' );
?>
<div class="photo">
	<img class="image" src="<?PHP echo base_url( 'photos/560x1200/' . $photo->filename ); ?>" alt="" />
	<details>
		<summary>Photo details</summary>
		<?PHP $this->load->view('photo/meta'); ?>

		<?PHP if ( !empty( $options_html ) || !empty( $photo->caption ) ) { ?>
			<div class="bottom">
				<div class="caption"><?PHP echo strip_tags( $photo->caption ); ?></div>
				<?PHP echo $options_html; ?>
			</div>
		<?PHP } ?>
		
		<a class="permalink" href="<?PHP echo $photo->permalink; ?>">Permalink</a>

	</details>
</div>