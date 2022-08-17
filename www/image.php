<?php

require_once __DIR__ . '/../bootstrap.php';

$user = db_fetch_row('SELECT * FROM users WHERE ID = ?', [$_GET['user']]);

if ($user) {
    set_access_token($user['access_token']);
}

if (!$user || !isset($_GET['url']) || !verify_link('image', $_GET['url'])) {
    render_error('Error: Could not verify URL signature.');
}

$httpContext = stream_context_create(
	[
		'http' => [
			'method' => 'GET',
			'header' => 'Authorization: Bearer ' . get_access_token()
		]
	]
);

$buffer   = file_get_contents($_GET['url'], false, $httpContext);
$fileInfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $fileInfo->buffer($buffer);

header('Content-Type: ' . $mimeType);
echo $buffer;
exit;

