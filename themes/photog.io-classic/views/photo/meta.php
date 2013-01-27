<div class="entry-meta">

	<?PHP if ( !empty( $photo->user_icon_url ) ) { ?>
	<a class="user-icon" href="<?PHP echo $photo->user_url; ?>" title="View all photos by <?PHP echo $photo->user_username; ?>" rel="author">
		<img class="icon" src="<?PHP echo html_escape($photo->user_icon_url); ?>" alt="" width="50" height="50" />
	</a>
	<?PHP } ?>
	<time class="entry-date" datetime="<?php echo date( DATE_W3C, mysql_to_unix( $photo->time ) ); ?>" title="<?php echo date( 'M j, Y @ h:ia', mysql_to_unix( $photo->time ) ); ?>"><?php echo time2str($photo->time); ?></time>
	<span class="byline"> by <span class="author vcard">
	<a class="url fn n" href="<?PHP echo $photo->user_url; ?>" title="View all photos by <?PHP echo $photo->user_username; ?>" rel="author"><?PHP echo $photo->user_username; ?>
	</a></span>
	</span>

</div>