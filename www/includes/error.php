<?php
use Zend\Escaper\Escaper;
$escaper = new Escaper;
?>

<?php require __DIR__ . '/header.php';?>

<div class="error">
    <?php echo $escaper->escapeHtml($message);?>
</div>

<?php require __DIR__ . '/footer.php';?>
