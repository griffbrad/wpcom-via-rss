<?php
$comments = request(
    sprintf(
        '/sites/%d/posts/%d/replies/?hierarchical=1',
        $_GET['site'],
        $_GET['id']
    )
);

$comments = $comments['comments'];
krsort($comments);
?>

<?php if (!count($comments)):?>

<div class="alert">
    No comments.
</div>

<?php else:?>

<?php if (1 === count($comments)):?>
<h2>1 Comment</h2>
<?php else:?>
<h2><?php echo $escaper->escapeHtml(count($comments));?> Comments</h2>
<?php endif;?>

<?php
$allComments = $comments;

$renderComments = function ($comments, $isChild = false) use (&$renderComments, $allComments, $escaper) {
    ?>
    <ul class="comments list-group <?php echo ($isChild ? 'child-comments' : 'root-comments');?>">
        <?php foreach ($comments as $comment):?>
        <li>
            <span class="avatar">
                <img src="<?php echo $escaper->escapeHtmlAttr($comment['author']['avatar_URL']);?>" />
            </span>
            <span class="author">
                <?php echo $escaper->escapeHtml($comment['author']['name']);?>
            </span>
            <span class="date">
                <?php echo $escaper->escapeHtml(date('M j, Y g:iA', strtotime($comment['date'])));?>
            </span>
            <?php echo $comment['content'];?>

            <?php
            $childComments = [];

            foreach ($allComments as $potentialChildComment) {
                if ($potentialChildComment['parent'] && $comment['ID'] === $potentialChildComment['parent']['ID']) {
                    $childComments[] = $potentialChildComment;
                }
            }

            if (count($childComments)) {
                $renderComments($childComments, true);
            }
            ?>
        </li>
        <?php endforeach;?>
    </ul>
    <?php
};

$topLevelComments = [];

foreach ($comments as $comment) {
    if (!$comment['parent']) {
        $topLevelComments[] = $comment;
    }
}

$renderComments($topLevelComments);
?>

<?php endif;?>

