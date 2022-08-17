<?php

require_once __DIR__ . '/../bootstrap.php';

use Zend\Escaper\Escaper;

check_auth();

$plaintext_rss_password = null;

if (isset($_SESSION['plaintext_rss_password'])) {
    $plaintext_rss_password = $_SESSION['plaintext_rss_password'];
    unset($_SESSION['plaintext_rss_password']);
}

$me = get_me();

$db_user = db_fetch_row('SELECT * FROM users WHERE ID = ?', [$me['ID']]);

$teams   = request('/read/teams', false, 'v1.2');
$is_a11n = false;

foreach ($teams['teams'] as $team) {
    if ('a8c' === $team['slug']) {
        $is_a11n = true;
    }
}

$escaper = new Escaper();
?>

<?php require __DIR__ . '/includes/header.php';?>
<?php require __DIR__  . '/includes/default-h1.php';?>

<table>
    <tbody>
        <tr>
            <th scope="row">RSS Feeds</th>
            <td>
                <h2>Followed Sites</h2>
                <a href="<?php echo BASE_URI;?>/rss.php"><?php echo BASE_URI;?>/rss.php</a>
                <?php if ($is_a11n):?>
                <br />
                <br />
                <h2>a8c</h2>
                <a href="<?php echo BASE_URI;?>/rss.php?team=a8c"><?php echo BASE_URI;?>/rss.php?team=a8c</a>
                <?php endif;?>
            </td>
        </tr>
        <tr>
            <th scope="row">Username</th>
            <td><?php echo $escaper->escapeHtml($db_user['username']);?></td>
        </tr>
        <tr>
            <th scope="row">Password</th>
            <td>
                <?php if ($plaintext_rss_password):?>
                <?php echo $escaper->escapeHtml($plaintext_rss_password);?>
                <div>
                    <strong>Important:</strong> Save this password now.  You will not be able to retrieve it again later.
                </div>
                <?php else:?>
                <span class="note">RSS password cannot be displayed after initial setup.</span>
                <br />
                <br />
                <a class="button secondary" href="/regenerate-password.php?key=<?php echo sign_link('password');?>">Generate New Password</a>
                <?php endif;?>
            </td>
        </tr>
    </tbody>
</table>

<?php require __DIR__  . '/includes/help.php';?>
<?php require __DIR__ . '/includes/footer.php';?>
