<div id="subnav-hidden-container">
	<?php
		if ($session->read('User.admin'))
			echo $this->element('adminsubnav', array('attr' => array('container_id' => 'subnav-hidden-admin', 'container_class' => 'subnav-hidden')));
	?>
	<?php echo $this->element('news_posts_subnav', array('attr' => array('container_id' => 'subnav-hidden-news-posts', 'container_class' => 'subnav-hidden'))); ?>
	<?php echo $this->element('roadscholarssubnav', array('attr' => array('container_id' => 'subnav-hidden-road-scholars', 'container_class' => 'subnav-hidden'))); ?>
	<?php
		if ($session->read('User.id'))
		{
			echo $this->element('userssubnav', array('attr' => array('container_id' => 'subnav-hidden-users', 'container_class' => 'subnav-hidden')));
			if ($session->read('GroupMember.id'))
				echo $this->element('orgsubnav', array('isGroupAdmin' => (bool)$session->read('GroupMember.permissions'), 'attr' => array('container_id' => 'subnav-hidden-groups', 'container_class' => 'subnav-hidden')));
		}
	?>
	<?php echo $this->element('workshopsubnav', array('attr' => array('container_id' => 'subnav-hidden-workshops', 'container_class' => 'subnav-hidden'))); ?>
	<?php echo $this->element('libraries-subnav', array('attr' => array('container_id' => 'subnav-hidden-libraries', 'container_class' => 'subnav-hidden'))); ?>
</div>