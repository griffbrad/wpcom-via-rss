<?php

define( 'CLIENT_ID', $_ENV['CLIENT_ID'] ?? 0 );

define( 'CLIENT_SECRET', $_ENV['CLIENT_SECRET'] ?? 'xxxxxx' );

define( 'BASE_URI', $_ENV['BASE_URI'] ?? 'https://example.org' );

define( 'REDIRECT_URI', $_ENV['REDIRECT_URI'] ?? ( BASE_URI . '/auth.php' )  );

define( 'DB_HOST', $_ENV['DB_HOST'] ?? 'localhost' );

define( 'DB_USER', $_ENV['DB_USER'] ?? 'example' );

define( 'DB_NAME', $_ENV['DB_NAME'] ?? 'example' );

define( 'DB_PASSWORD', $_ENV['DB_PASSWORD'] ?? 'xxxxxx' );

define( 'HMAC_KEY', $_ENV['HMAC_KEY'] ?? 'xxxxxx' );

define( 'SIGN_ALL_LINKS', ! ! $_ENV['REDIRECT_URI'] );

