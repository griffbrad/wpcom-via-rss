<?php

require_once __DIR__ . '/../bootstrap.php';

$curl = curl_init( 'https://public-api.wordpress.com/oauth2/token' );
curl_setopt( $curl, CURLOPT_POST, true );

curl_setopt( $curl, CURLOPT_POSTFIELDS, array(
    'client_id' => CLIENT_ID,
    'redirect_uri' => REDIRECT_URI,
    'client_secret' => CLIENT_SECRET,
    'code' => $_GET['code'],
    'grant_type' => 'authorization_code'
) );

curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1);

$auth   = curl_exec( $curl );
$secret = json_decode($auth, true);

if (isset($secret['error']) && $secret['error']) {
    header('Location: /connect.php');
    exit;
}

set_access_token($secret['access_token']);

$me = get_me();

$db_user = db_fetch_row('SELECT * FROM users WHERE ID = ?', [$me['ID']]);

if (!$db_user) {
    $rss_password = bin2hex(random_bytes(16));

    db_insert(
        'users',
        [
            'ID' => $me['ID'],
            'access_token' => $secret['access_token'],
            'username' => $me['username'],
            'rss_password_hash' => password_hash($rss_password, PASSWORD_DEFAULT)
        ]
    );

    $_SESSION['plaintext_rss_password'] = $rss_password;
} else {
    db_update(
        'users',
        ['access_token' => $secret['access_token'], 'username' => $me['username']],
        'ID',
        $me['ID']
    );
}

setcookie(
    'wpcom_auth',
    $me['ID'] . ':' . hash_hmac('sha256', $secret['access_token'], HMAC_KEY),
    strtotime('+90 days')
);

if (isset($_SESSION['redirect'])) {
    header('Location: ' . $_SESSION['redirect']);
    exit;
} else {
    header('Location: /start.php');
    exit;
}

