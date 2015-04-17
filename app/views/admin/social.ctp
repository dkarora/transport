<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>

<h2>Twitter</h2>
<?php if (!empty($twitterUsername)) : ?>
	<div>Twitter integration is <strong>enabled</strong>. New content can be tweeted to account <?php echo $html->link($twitterUsername, 'http://twitter.com/' . $twitterUsername); ?>.</div>
	<div><?php echo $html->link('Disable Twitter integration', '/admin/twitter_deauth/'); ?></div>
<?php else : ?>
	<div>Twitter integration is <strong>disabled</strong>.</div>
	<div><?php echo $html->link('Integrate Twitter account', '/admin/twitter_auth/'); ?></div>
<?php endif; ?>

<h2>Facebook</h2>
<p>Working on it!</p>