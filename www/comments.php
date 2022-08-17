<?php

use Zend\Escaper\Escaper;

require_once __DIR__ . '/../bootstrap.php';

check_auth('comments');

$params = get_post_params();

$post = request(
    sprintf(
        '/sites/%d/posts/%d',
        $_GET['site'],
        $_GET['id']
    )
);

$site = request(
    sprintf(
        '/sites/%d',
        $_GET['site']
    )
);
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

<?php require __DIR__ . '/includes/comments.php';?>

<script type="text/javascript" src="/main.js"></script>

<script type="text/javascript">
    handleClickOn('add-like', function () {
        xhr('POST', {action: 'add'});
    });

    handleClickOn('remove-like', function () {
        xhr('POST', {action: 'remove'});
    });
</script>

<?php require __DIR__ . '/includes/footer.php';?>
