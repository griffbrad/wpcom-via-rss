# wpcom-via-rss
Retrieve your WP.com Reader subscriptions as an RSS feed.

## Setup

You will need to create [a WordPress.com app](https://developer.wordpress.com/apps/) and obtain a OAuth Client ID and Client Secret.

### Docker

#### Development

1. Copy `.env.docker.example` to `.env` and edit it, setting the `BASE_URI` you intend to host at (must match the Redirect URL you provided when setting up your app in WordPress.com; you can use http://localhost:8966/ if you're just testing locally) as well as the `CLIENT_ID` and `CLIENT_SECRET` you obtained.
2. Run `docker-compose up`
3. Visit http://localhost:8966/

#### Production

1. Populate your MySQL DB with the `schema.sql` file.
1. Build your image using e.g. `docker build -t wpcom-via-rss .`
2. Run your image using e.g. `docker run --name wpcom-via-rss -p 8966:80 -e BASE_URI=https://example.com -e DB_HOST=dbserver -e ... wpcom-via-rss` - see `config.docker.php` for the full list of (`-e`) environment variables you might like to set along with their default values.
3. Put a HTTPS reverse proxy in front of it.

### Manual

**Note: this application only supports PHP 7, and not PHP 8.**

1. Install dependencies with `composer install`
2. Fill out the values in `config.example.php` and rename it to `config.php`.
3. Make `www` your document root.
4. Populate your MySQL DB with the `schema.sql` file.
