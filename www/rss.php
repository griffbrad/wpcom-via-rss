<?php

require_once __DIR__ . '/../bootstrap.php';

if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="a8c RSS"');
    header('HTTP/1.0 401 Unauthorized');
    $message = 'Please provide a username and password via HTTP Basic auth.';
    require __DIR__ . '/includes/error.php';
    exit;
} else {
    $user = db_fetch_row('SELECT * FROM users WHERE username = ?', [$_SERVER['PHP_AUTH_USER']]);

    if (
        !$user ||
        !isset($_SERVER['PHP_AUTH_PW']) ||
        !password_verify($_SERVER['PHP_AUTH_PW'], $user['rss_password_hash'])
    ) {
        send_unauthorized_response();
    }

    set_access_token($user['access_token']);
}

function send_unauthorized_response()
{
    header('HTTP/1.0 401 Unauthorized');
    $message = 'Incorrect username or password.';
    require __DIR__ . '/includes/error.php';
    exit;
}

function get_followed_sites()
{
	$response = request('/read/following/mine');

    $out = [];

    foreach ($response['subscriptions'] as $site) {
        $out[] = $site['blog_ID'];
    }

    return $out;
}

$me = get_me();

$followed_sites = get_followed_sites();

if (isset($_GET['team']) && 'a8c' === $_GET['team']) {
    $path       = 'a8c';
    $feed_title = 'a8c';
} else {
    $path       = 'following';
    $feed_title = 'WordPress.com';
}

$response = request('/read/' . $path . '?number=20');
$posts    = [];

foreach ($response['posts'] as $post) {
	if ('Auto Draft' === $post['title']) {
		continue;
	}

    $cross_post_site = false;
    $cross_post_id   = null;

    foreach ($post['metadata'] as $metadata) {
        if ('xpost_origin' === $metadata['key']) {
            list($blog_id, $post_id) = explode(':', $metadata['value']);
            $cross_post_site = $blog_id;
            $cross_post_id   = $post_id;
        }
    }

    if ($cross_post_site) {
        if (in_array($cross_post_site, $followed_sites)) {
            continue;
        } else {
            $post_response = request(
                sprintf(
                    '/sites/%d/posts/%d',
                    $cross_post_site,
                    $cross_post_id
                )
            );

			if ($post_response && isset($post_response['content'])) {
				$post['ID']       = $cross_post_id;
				$post['site_ID']  = $cross_post_site;
				$post['content'] .= $post_response['content'];
			}
        }
    }

    $posts[] = $post;
}

header('Content-Type: application/rss+xml');
require __DIR__ . '/includes/rss-template.php';
exit;
