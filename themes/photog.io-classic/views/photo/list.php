<ul class="photos">
	<?PHP foreach ($photos as $i => $photo) { ?>

	<li>
		<?PHP $this->load->view( 'photo/single', array('photo' => $photo) ); ?>
	</li>


	<?PHP } ?>
</ul>

<?PHP if ( ! empty( $pagination ) ) { echo $pagination; } ?>