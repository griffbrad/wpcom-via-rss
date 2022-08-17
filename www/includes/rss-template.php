<?php
function escape($input)
{
	echo htmlspecialchars($input);
}
?>
<?xml version="1.0" encoding="UTF-8"?><rss version="2.0"
        xmlns:content="http://purl.org/rss/1.0/modules/content/"
        xmlns:wfw="http://wellformedweb.org/CommentAPI/"
        xmlns:dc="http://purl.org/dc/elements/1.1/"
        xmlns:atom="http://www.w3.org/2005/Atom"
        xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
        xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
		xmlns:georss="http://www.georss.org/georss" xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#"
>

<channel>
        <title><?php escape($feed_title);?></title>
        <link>https://wordpress.com/</link>
        <description>a8c Reader</description>
        <language>en-US</language>
        <sy:updatePeriod>hourly</sy:updatePeriod>
        <sy:updateFrequency>2</sy:updateFrequency>

		<?php foreach ($posts as $entry):?>


		<?php
		ob_start();
		?>
		<span style="display: none;"><?php echo strip_tags($entry['site_name']);?> &mdash; </span>
		<?php
        $content = trim(ob_get_clean()) . $entry['content'];
		?>

        <?php
        $renderButton = function ($text, $url) {
            $out = '<a href="' . $url . '" class="button">';
            $out .= $text;
            $out .= '</a>';

            return $out;
        };
        ?>


		<?php
		$me = get_me();

		ob_start();
        ?>
		<p class="entry-button-wrapper" style="text-align: center">
			<?php
			$url = sprintf(
				BASE_URI . '/post.php?id=%d&site=%d',
				$entry['ID'],
				$entry['site_ID']
			);

			if (SIGN_ALL_LINKS) {
				$url .= '&user=' . $me['ID'] . '&key=' . sign_link('post', $entry['ID']);
			}

            echo $renderButton('Comments', $url);
            ?>

			&nbsp;
			<span class="kindle-hidden">
			<span style="opacity: 0;">&middot;</span>
			<span style="opacity: 0;">&middot;</span>
			<span style="opacity: 0;">&middot;</span>
			<span style="opacity: 0;">&middot;</span>
			</span>
			&nbsp;

			<?php
            $url = sprintf(
				BASE_URI . '/post.php?id=%d&site=%d&initial-action=add-like',
				$entry['ID'],
				$entry['site_ID']
			);

			if (SIGN_ALL_LINKS) {
				$url .= '&user=' . $me['ID'] . '&key=' . sign_link('post', $entry['ID']);
			}

            echo $renderButton('Like', $url);
            ?>

			&nbsp;
			<span class="kindle-hidden">
			<span style="opacity: 0;">&middot;</span>
			<span style="opacity: 0;">&middot;</span>
			<span style="opacity: 0;">&middot;</span>
			<span style="opacity: 0;">&middot;</span>
			</span>
			&nbsp;

			<?php
            $url = sprintf(
				BASE_URI . '/post.php?id=%d&site=%d&initial-action=subscribe',
				$entry['ID'],
				$entry['site_ID']
			);

			if (SIGN_ALL_LINKS) {
				$url .= '&user=' . $me['ID'] . '&key=' . sign_link('post', $entry['ID']);
			}

            echo $renderButton('Subscribe', $url);
            ?>
        </p>
        <style type="text/css">
            img {
                max-width: 85% !important;
            }
        </style>
		<?php
        $content .= trim(ob_get_clean());
		?>

		<item>
			<title>
				<?php escape($entry['title']);?>
			</title>
			<link><?php escape($entry['URL']);?></link>
			<author>
				<?php escape($entry['site_name']);?> -
				<?php escape($entry['author']['first_name'] . ' ' . $entry['author']['last_name']);?>
				(@<?php escape($entry['author']['login']);?>)
			</author>
			<pubDate><?php escape(date('r', strtotime($entry['date'])));?></pubDate>
			<guid isPermaLink="false"><?php escape($entry['guid']);?></guid>
            <description><![CDATA[<?php echo str_replace(PHP_EOL, ' ', filter_images($content));?>]]></description>
		</item>
		<?php endforeach;?>
</channel>
</rss>
