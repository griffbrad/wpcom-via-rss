<?php

use GuzzleHttp\Promise;
use Zend\Escaper\Escaper;

require_once __DIR__ . '/../bootstrap.php';

check_auth('post');

$params = get_post_params();
$action = null;

if (isset($params['action'])) {
	$action = $params['action'];
} elseif (isset($_GET['initial-action']) && $_GET['initial-action']) {
	$action = $_GET['initial-action'];
}

$i_liked        = null;
$i_subscribed   = null;
$action_promise = null;
$extra_like     = null;

if (null !== $action) {
	switch ($action) {
		case 'remove-like':
			$action_promise = async_request(
				sprintf(
					'/sites/%d/posts/%d/likes/mine/delete',
					$_GET['site'],
					$_GET['id']
				),
				true
			);

			$action_promise->wait();

			$i_liked = false;
			break;
		case 'add-like':
			$action_promise = async_request(
				sprintf(
					'/sites/%d/posts/%d/likes/new',
					$_GET['site'],
					$_GET['id']
				),
				true,
				'v1',
				.1
			);

			$extra_like = get_me();

			$i_liked = true;
			break;
		case 'subscribe':
			$action_promise = async_request(
				sprintf(
					'/sites/%d/posts/%d/subscribers/new',
					$_GET['site'],
					$_GET['id']
				),
				true,
				'v1.1'
			);

			$i_subscribed = true;
			break;
		case 'unsubscribe':
			$action_promise = request(
				sprintf(
					'/sites/%d/posts/%d/subscribers/mine/delete',
					$_GET['site'],
					$_GET['id']
				),
				true,
				'v1.1'
			);

			$action_promise->wait();

			$i_subscribed = false;
			break;
	}
}

function decode_unwrapped_promise($unwrapped_promise)
{
	$body  = $unwrapped_promise->getBody()->getContents();
	return json_decode($body, true);
}

$promises = [
	'site'        => async_request(sprintf('/sites/%d', $_GET['site'])),
	'post'        => async_request(sprintf('/sites/%d/posts/%d', $_GET['site'], $_GET['id'])),
	'likes'       => async_request(sprintf('/sites/%d/posts/%d/likes/', $_GET['site'], $_GET['id'])),
	'subscribers' => async_request(sprintf(
			'/sites/%d/posts/%d/subscribers/mine',
			$_GET['site'],
			$_GET['id']
		),
		false,
		'v1.1'
	)
];

$results = Promise\unwrap($promises);

$site        = decode_unwrapped_promise($results['site']);
$post        = decode_unwrapped_promise($results['post']);
$subscribers = decode_unwrapped_promise($results['subscribers']);
$likes 		 = decode_unwrapped_promise($results['likes']);

if ($extra_like) {
	$likes['likes'][] = $extra_like;
}

if (null === $i_subscribed) {
	$i_subscribed = $subscribers['i_subscribe'];
}

if (null === $i_liked) {
	$i_liked = $likes['i_like'];
}
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

<div class="button-wrapper">
	<?php if (!$i_liked):?>
	<button class="primary" type="button" id="add-like">Like This Post</button>
	<?php else:?>
	<button type="button" id="remove-like">Remove Like</button>
	<?php endif;?>

	<?php if (!$i_subscribed):?>
	<button type="button" id="subscribe">Subscribe to This Post</button>
	<?php else:?>
	<button type="button" id="unsubscribe">Unsubscribe</button>
	<?php endif;?>
</div>

<?php if (count($likes['likes'])):?>

<?php if (1 === count($likes['likes'])):?>
<h2>1 Like</h2>
<?php else:?>
<h2><?php echo $escaper->escapeHtml(count($likes['likes']));?> Likes</h2>
<?php endif;?>

<ul class="avatar-list list-group">
    <?php foreach ($likes['likes'] as $like):?>
    <li>
        <img src="<?php echo $escaper->escapeHtmlAttr($like['avatar_URL']);?>" alt="" />
    </li>
    <?php endforeach;?>
</ul>
<?php endif;?>

<?php require __DIR__ . '/includes/comments.php';?>

<script type="text/javascript" src="/main.js"></script>

<script type="text/javascript">
    handleClickOn('add-like', function () {
        xhr('POST', {action: 'add-like'});
    });

    handleClickOn('remove-like', function () {
        xhr('POST', {action: 'remove-like'});
    });

	handleClickOn('subscribe', function () {
        xhr('POST', {action: 'subscribe'});
    });

    handleClickOn('unsubscribe', function () {
        xhr('POST', {action: 'unsubscribe'});
    });
</script>

<?php require __DIR__ . '/includes/footer.php';?>
