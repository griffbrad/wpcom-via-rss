<?php

require_once __DIR__ . '/../config.php';

$url = sprintf(
    'https://public-api.wordpress.com/oauth2/authorize?client_id=%s&redirect_uri=%s&response_type=code&scope=global',
    CLIENT_ID,
    REDIRECT_URI
);

header("Location: {$url}");
exit;

