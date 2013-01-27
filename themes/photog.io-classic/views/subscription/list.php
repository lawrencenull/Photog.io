<ul>
	<?PHP foreach ($subscriptions as $i => $subscription) { ?>
		<li><a href="<?PHP echo $subscription->subscribee_url; ?>"><?PHP echo $subscription->name; ?></a></li>
	<?PHP } ?>
</ul>