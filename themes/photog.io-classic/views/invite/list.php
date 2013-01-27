<ul>
	<?PHP foreach ( $invites as $invite ) { ?>
		<li><pre><a href="<?PHP echo base_url('register/'.$invite->code); ?>"><?PHP echo $invite->code; ?></a></pre></li>
	<?PHP } ?>
</ul>