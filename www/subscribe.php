<?php

use Zend\Escaper\Escaper;

require_once __DIR__ . '/../bootstrap.php';

check_auth('subscribe');

$params = get_post_params();

request(
    sprintf(
        '/sites/%d/posts/%d/subscribers/new',
        $_GET['site'],
        $_GET['id']
    ),
	true,
	'v1.1'
);

if (isset($params['action']) && 'unsubscribe' === $params['action']) {
    request(
        sprintf(
            '/sites/%d/posts/%d/subscribers/mine/delete',
            $_GET['site'],
            $_GET['id']
        ),
		true,
		'v1.1'
    );
}

$site = request(
    sprintf(
        '/sites/%d',
        $_GET['site']
    )
);

$post = request(
    sprintf(
        '/sites/%d/posts/%d',
        $_GET['site'],
        $_GET['id']
    )
);

$subscribers = request(
	sprintf(
		'/sites/%d/posts/%d/subscribers/mine',
		$_GET['site'],
		$_GET['id']
	),
	false,
	'v1.1'
);

$subscribed = $subscribers['i_subscribe'];
?>

<?php
$escaper = new Escaper;
?>

<?php require __DIR__ . '/includes/header.php';?>

<h1>
    <?php echo $escaper->escapeHtml(html_entity_decode($post['title']));?>
    <small>
        <?php echo $escaper->escapeHtml($site['name']) ?> -
        <?php echo $escaper->escapeHtml($post['author']['first_name'] . ' ' . $post['author']['last_name']);?>
        (@<?php echo $escaper->escapeHtml($post['author']['login']);?>)
    </small>
</h1>

<?php if (!$subscribed):?>
<div class="primary-call-to-action">
    <button type="button" id="subscribe">Subscribe to This Post</button>
</div>
<?php else:?>
<div class="alert">
    You subscribed to this post.
    <button type="button" id="unsubscribe">Unsubscribe</button>
</div>
<?php endif;?>

<script type="text/javascript" src="/main.js"></script>

<script type="text/javascript">
    handleClickOn('subscribe', function () {
        xhr('POST', {action: 'subscribe'});
    });

    handleClickOn('unsubscribe', function () {
        xhr('POST', {action: 'unsubscribe'});
    });
</script>

<?php require __DIR__ . '/includes/footer.php';?>
