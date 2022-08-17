<?php

use GuzzleHttp\Client as HttpClient;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

define('TEST_MODE', 'test' === getenv('ENV'));

session_start();

$me = null;

$access_token = null;

$db = new PDO(
    sprintf(
        'mysql:dbname=%s;host=%s',
        DB_NAME,
        DB_HOST
    ),
    DB_USER,
    DB_PASSWORD,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$client = new HttpClient(['base_uri' => 'https://public-api.wordpress.com/']);

function db_fetch_row($sql, array $params)
{
    global $db;

    try {
        $statement = $db->prepare($sql);

        if ($statement->execute($params)) {
            $result = $statement->fetchAll();

            foreach ($result as $row) {
                return $row;
            }
        }
    } catch (Exception $e) {
        echo $e->getMessage() . PHP_EOL;
        exit;
    }
}

function db_insert($table, array $data)
{
    global $db;

    $placeholders = [];

    foreach ($data as $column) {
        $placeholders[] = '?';
    }

    $sql = sprintf(
        'INSERT INTO %s (%s) VALUES (%s)',
        $table,
        implode(', ', array_keys($data)),
        implode(', ', $placeholders)
    );

    $db
        ->prepare($sql)
        ->execute(array_values($data));
}

function db_update($table, array $data, $where_column, $where_value)
{
    global $db;

    $assignments = [];

    $placeholder_values = [];

    foreach ($data as $column_name => $value) {
        $assignments[] = $column_name . ' = ?';
        $placeholder_values[] = $value;
    }

    $sql = sprintf(
        'UPDATE %s SET %s WHERE %s = ?',
        $table,
        implode(', ', $assignments),
        $where_column
    );

    $placeholder_values[] = $where_value;

    try {
        $db
            ->prepare($sql)
            ->execute($placeholder_values);
    } catch (Exception $e) {
        echo $e->getMessage();
        exit;
    }
}

function set_access_token($new_access_token)
{
    global $access_token, $me;

    $me = null;

    $access_token = $new_access_token;
}

function get_access_token()
{
    global $access_token;
    return $access_token;
}

function async_request($url, $is_post = false, $version = 'v1', $timeout = null)
{
	global $client;

	try {
		$promise = $client->requestAsync(
			($is_post ? 'POST': 'GET'),
			sprintf('/rest/%s/%s', $version, ltrim($url, '/')),
			[
				'timeout' => $timeout ?: 10,
				'headers' => [
					'Authorization' => 'Bearer ' . get_access_token()
				]
			]
		);
	} catch (Exception $e) {

	}

	return $promise;
}

function request($url, $is_post = false, $version = 'v1')
{
    $url = '/' . ltrim($url, '/');

    $curl = curl_init('https://public-api.wordpress.com/rest/' . $version . $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . get_access_token()));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    if ($is_post) {
        curl_setopt($curl, CURLOPT_POST, 1);
    }

    $raw_response = curl_exec($curl);
    $decoded_response = json_decode($raw_response, true);
    return $decoded_response;
}

function get_me()
{
    global $me;

    if (!isset($me) || !$me) {
        $me = request('/me');
    }

    return $me;
}

function get_post_params()
{
    if ('POST' !== $_SERVER['REQUEST_METHOD']) {
        return [];
    }

    return json_decode(file_get_contents('php://input'), true);
}

function sign_link()
{
    $me   = get_me();
    $args = func_get_args();

    $args[] = $me['ID'];
    $args[] = get_access_token();

    return hash_hmac('sha256', implode(':', $args), HMAC_KEY);
}

function filter_images($content)
{
    $dom    = new Zend\Dom\Query($content);
    $images = $dom->execute('img');

    $me = get_me();

    foreach ($images as $image) {
        $src = $image->getAttribute('src');

        if (0 === strpos($src, 'data:')) {
            continue;
        }

        $content = str_replace(
            str_replace('&', '&#038;', $src),
            BASE_URI . '/image.php?url=' . urlencode($src) . '&user=' . $me['ID'] . '&key=' . sign_link('image', $src),
            $content
        );
    }

    return $content;
}

function check_auth($keyName = null)
{
	// Authenticate with HMAC alone
	if ($keyName && isset($_GET['key']) && isset($_GET['user'])) {
		$db_user = db_fetch_row('SELECT * FROM users WHERE ID = ?', [$_GET['user']]);

		set_access_token($db_user['access_token']);

		if (!verify_link($keyName, $_GET['id'])) {
			render_error('Could not verify link.');
		}

		return;
	}

    if (!isset($_COOKIE['wpcom_auth']) || !$_COOKIE['wpcom_auth']) {
        request_auth();
    }

    list($user_id, $auth_key) = explode(':', $_COOKIE['wpcom_auth']);

    $db_user = db_fetch_row('SELECT * FROM users WHERE ID = ?', [$user_id]);

    if (!$db_user || $auth_key !== hash_hmac('sha256', $db_user['access_token'], HMAC_KEY)) {
        request_auth();
    }

    set_access_token($db_user['access_token']);
}

function request_auth()
{
    $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
    header('Location: ' . BASE_URI . '/connect.php');
    exit;
}

function verify_link()
{
    $signature = call_user_func_array('sign_link', func_get_args());

    return isset($_GET['key']) && $_GET['key'] === $signature;
}

function render_error($message)
{
    require __DIR__ . '/www/includes/error.php';
    exit;
}

