<?php
function escape($input)
{
    echo htmlspecialchars($input);
}
function site_url($site)
{
    echo preg_replace( '/\/feed$/', '', preg_replace( '/^https?:\/\//', '', $site['URL'] ) );
}
?>
<?xml version="1.0" encoding="UTF-8"?><opml version="1.0">
<head>
    <title>WPCOM subscriptions for <?php escape($_SERVER['PHP_AUTH_USER']) ?></title>
</head>
<body>
<?php foreach($followed_sites as $site) { ?>
    <outline type="rss" title="<?php site_url( $site ); ?>" xmlUrl="<?php echo BASE_URI, '/rss.php?blog=', $site['blog_ID']; ?>" />
<?php } ?>
</body>
</opml>
