<?PHP echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
 
<feed xmlns="http://www.w3.org/2005/Atom">
 
        <title><?PHP echo $feed['title']; ?></title>
        <subtitle><?PHP echo $feed['subtitle']; ?></subtitle>
        <link href="<?PHP echo xml_convert( $feed['self'] ); ?>" rel="self" />
        <link href="<?PHP echo $feed['html_version']; ?>" />

        <?PHP foreach( $feed['hubs'] as $hub ) { ?><link rel="hub" href="<?PHP echo xml_convert( $hub ); ?>" />
        <?PHP } ?>

        <id><?PHP echo xml_convert( $feed['id'] ); ?></id>
        <updated><?PHP echo $feed['updated']; ?></updated>

        <?PHP foreach( $feed['entries'] as $i => $entry ) { ?>

        <entry>
                <title><?PHP echo xml_convert( $entry['title'] ); ?></title>
                <link rel="alternate" type="text/html" href="<?PHP echo $entry['permalink']; ?>" />
                <content type="<?PHP echo xml_convert( $entry['mime_type'] ); ?>" src="<?PHP echo xml_convert( $entry['img_url'] ); ?>" />
                <id><?PHP echo xml_convert( $entry['id'] ); ?></id>
                <updated><?PHP echo xml_convert( $entry['updated'] ); ?></updated>
                <summary><?PHP echo xml_convert( $entry['caption'] ); ?></summary>
                <author>
                      <name><?PHP echo $entry['author']['name']; ?></name>
                </author>
        </entry>

        <?PHP } ?>

</feed>