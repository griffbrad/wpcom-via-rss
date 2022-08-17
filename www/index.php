<?php
require_once __DIR__ . '/../bootstrap.php';
?>

<?php require __DIR__  . '/includes/header.php';?>
<?php require __DIR__  . '/includes/default-h1.php';?>

<p style="text-align: center;">
    <a class="button primary" href="/start.php">Get Started</a>
</p>

<h3>How does this work?</h3>
<p>
    We connect to your WordPress.com account to get an access token.  Using that token, we
    request posts from the sites you follow and add them to a password-protected RSS feed.
    Your RSS feed will automatically use the same API for images, comments, likes and subscribing
    to individual posts.
</p>

<h3>How do I subscribe to a password-protected RSS feed?</h3>
<p>
    Many RSS services have integrated support for password-protected feeds.  In some cases,
    you may need to embed your credentials in the URL:
</p>
<pre>
    https://[username]:[password]@<?php echo preg_replace('/^https?:\/\//', '', BASE_URI);?>/rss.php
</pre>
<p>
    <strong>Note:</strong> Your RSS feed password is not the same as your WordPress.com
    password.  You'll be provided a random RSS password when you connect your account.
</p>

<h3>Why?</h3>
<p>
    If you use RSS a lot for following other sites, it may be helpful to integrate your
    WordPress.com reading into that same workflow.  Also, having the ability to track
    read/unread status can help you confidently keep up with a large number of blogs.
</p>

<?php require __DIR__  . '/includes/help.php';?>
<?php require __DIR__  . '/includes/footer.php';?>
