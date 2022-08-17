<?php

use Zend\Escaper\Escaper;

require_once __DIR__ . '/../bootstrap.php';

check_auth('like');

$params = get_post_params();

if (isset($params['action']) && 'remove' === $params['action']) {
    request(
        sprintf(
            '/sites/%d/posts/%d/likes/mine/delete',
            $_GET['site'],
            $_GET['id']
        ),
        true
    );
} else {
    request(
        sprintf(
            '/sites/%d/posts/%d/likes/new',
            $_GET['site'],
            $_GET['id']
        ),
        true
    );
}

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

$likes = request(
    sprintf(
        '/sites/%d/posts/%d/likes/',
        $_GET['site'],
        $_GET['id']
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

<?php if (!$likes['i_like']):?>
<div class="primary-call-to-action">
    <button type="button" id="add-like">Like This Post</button>
</div>
<?php else:?>
<div class="alert">
    You liked this post.
    <button type="button" id="remove-like">Remove</button>
</div>
<?php endif;?>

<?php if (count($likes['likes'])):?>

<?php if (1 === count($likes['likes'])):?>
<h2>1 Like</h2>
<?php else:?>
<h2><?php echo $escaper->escapeHtml(count($likes['likes']));?> Likes</h2>
<?php endif;?>

<ul class="avatar-list list-group">
    <?php foreach ($likes['likes'] as $like):?>
    <li>
        <img src="<?php echo $escaper->escapeHtmlAttr($like['avatar_URL']);?>" alt="" />
    </li>
    <?php endforeach;?>
</ul>

<?php endif;?>

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
