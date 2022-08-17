<?php

use Zend\Escaper\Escaper;

require_once __DIR__ . '/../bootstrap.php';

check_auth('video');

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

foreach ($post['metadata'] as $metadata) {
	if ('xpost_origin' === $metadata['key']) {
		list($blog_id, $post_id) = explode(':', $metadata['value']);
		$cross_post_site = (int) $blog_id;
		$cross_post_id   = (int) $post_id;

		$post = request(
			sprintf(
				'/sites/%d/posts/%d',
				$cross_post_site,
				$cross_post_id
			)
		);
	}
}

$dom     = new Zend\Dom\Query($post['content']);
$iframes = $dom->execute('iframe');
$videos  = [];

foreach ($iframes as $iframe) {
	$url = $iframe->getAttribute('src');

	if (!preg_match('/^https:\/\/video.wordpress.com\/embed\//', $url)) {
		continue;
	}

	$url_segments = parse_url($url);
	$guid         = preg_replace('/^\/embed\//', '', $url_segments['path']);

	$video_response = request(
		sprintf('/videos/%s', $guid)
	);

	$videos[] = $video_response['file_url_base']['https'] . $video_response['files']['dvd']['mp4'];
}
?>

<?php
$escaper = new Escaper;
?>

<?php require __DIR__ . '/includes/header.php';?>

<h1>
    Videos embedded in <?php echo $escaper->escapeHtml(html_entity_decode($post['title']));?>
    <small>
        <?php echo $escaper->escapeHtml($site['name']) ?> -
        <?php echo $escaper->escapeHtml($post['author']['first_name'] . ' ' . $post['author']['last_name']);?>
        (@<?php echo $escaper->escapeHtml($post['author']['login']);?>)
    </small>
</h1>

<?php if (!count($videos)): ?>
No videos found.
<?php else: ?>

<?php foreach ($videos as $video): ?>
<video controls style="width: 100%">
	<source src="<?php echo $escaper->escapeHtmlAttr($video);?>" type="video/mp4">
</video>
<?php endforeach;?>

<?php endif;?>

<?php require __DIR__ . '/includes/footer.php';?>
