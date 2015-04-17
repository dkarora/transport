<?php $this->set('subnavcontent', $this->element('roadscholarssubnav')); ?>

<h2>List of Road Scholars</h2>

<?php if (!$session->check('User.id')) : ?>
<p><strong>Please note: These records reflect workshops taken past February 1st, 2011. To request submission of workshops before that date, <?php echo $html->link('log in', '/users/login/' . base64_encode('/' . $this->params['url']['url'])); ?> or <?php echo $html->link('register', '/users/register'); ?> and request legacy record integration.</strong></p>
<?php endif; ?>

<?php echo $this->element('scholarstable', array('scholars' => $scholars)); debug ($scholars); ?>