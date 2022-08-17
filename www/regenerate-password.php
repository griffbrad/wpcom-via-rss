<?php

require_once __DIR__ . '/../bootstrap.php';

check_auth();

if (!verify_link('password')) {
    render_error('Could not generate RSS password.');
}

$me = get_me();

$rss_password = bin2hex(random_bytes(16));

db_update(
    'users',
    [
        'rss_password_hash' => password_hash($rss_password, PASSWORD_DEFAULT)
    ],
    'ID',
    $me['ID']
);

$_SESSION['plaintext_rss_password'] = $rss_password;

header('Location: /start.php');
exit;

