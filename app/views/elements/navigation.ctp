<?php
	$url = implode($this->params['url'], '/');
	$thelinks = array(
		'Home' => array('location' => '/', 'class' => 'hidden-subnav-trigger', 'id' => 'trigger-news-posts'),
		'About &amp; Contact' => '/about/',
		'Workshops' => array('location' => '/workshops/', 'class' => 'hidden-subnav-trigger', 'id' => 'trigger-workshops'),
		'Road Scholars' => array('location' => '/road_scholars', 'class' => 'hidden-subnav-trigger', 'id' => 'trigger-road-scholars'),
		'Resources/Libraries' => array('location' => '/libraries/', 'class' => 'hidden-subnav-trigger', 'id' => 'trigger-libraries'),
		'' => ''
	);
	
	$loggedout = array(
		'Log In' => '/users/login/',
		'Register' => '/users/register/'
	);
	
	$loggedin = array(
		'My Account' => array('location' => '/users/', 'class' => 'hidden-subnav-trigger', 'id' => 'trigger-users'),
	);
	
	$hasorg = array(
		'My Organization' => array('location' => '/groups/', 'class' => 'hidden-subnav-trigger', 'id' => 'trigger-groups'),
	);
	
	function NavigationProcessLinks($html, $links, $params)
	{
		// "echo + new line"
		function enl($text)
		{
			echo $text, "\n";
		}
		
		function WriteHere($html, $name, $href, $linkid = null, $linkclass = null)
		{
			$opt = array('class' => 'here' . (!empty($linkclass) ? " $linkclass" : ''), 'escape' => false);
			if ($linkid)
				$opt['id'] = $linkid;
			enl($html->link($name, $href, $opt));
			enl('</li>');
		}
		
		foreach ($links as $name => $where)
		{
			$linkclass = '';
			$href = '';
			$linkid = null;
			
			if (is_array($where))
			{
				$href = $where['location'];
				if (!empty($where['class']))
					$linkclass = $where['class'];
				if (!empty($where['id']))
					$linkid = $where['id'];
			}
			else
				$href = $where;
			
			$location = Router::parse($href);
			
			enl('<li>');
			
			// no name specified means there should be a space here
			if (empty($name))
			{
				enl('<div class="spacer"></div>');
				enl('</li>');
				continue;
			}
			
			// special case for libraries
			if (($params['controller'] == 'videos' || $params['controller'] == 'libraries' || $params['controller'] == 'cart_items' || $params['controller'] == 'publications') && $location['controller'] == 'libraries')
			{
				WriteHere($html, $name, $href, $linkid, $linkclass);
				continue;
			}
			// special case for search
			if (($params['controller'] == 'search') && $location['controller'] == 'news_posts')
			{
				WriteHere($html, $name, $href, $linkid, $linkclass);
				continue;
			}
			// special case for links
			if (($params['controller'] == 'pages' && $params['action'] == 'display' && !empty($params['pass']) && $params['pass'][0] == 'links') && $location['controller'] == 'news_posts')
			{
				WriteHere($html, $name, $href, $linkid, $linkclass);
				continue;
			}
			// check if we're at $location
			// start with the controller
			if ($location['controller'] == $params['controller'])
			{
				// distinguish the pages controller
				if ($location['controller'] == 'pages' && $location['action'] == 'display')
				{
					// i wrote this at one point and now i have no idea what it means
					// i think it's the pages controller version of action
					// anyway i guess it means that we are here
					if ($location['pass'] == $params['pass'])
					{
						WriteHere($html, $name, $href, $linkid, $linkclass);
						continue;
					}
				}
				else
				{
					// look at the controller action
					if ($location['action'] == 'index' && $params['action'] != 'index')
					{
						WriteHere($html, $name, $href, $linkid, $linkclass);
						continue;
					}
					else if ($location['action'] == $params['action'])
					{
						WriteHere($html, $name, $href, $linkid, $linkclass);
						continue;
					}
				}
			}
			
			// if all else fails then we're not there
			$opt = array('escape' => false, 'class' => $linkclass);
			if ($linkid)
				$opt['id'] = $linkid;
			enl($html->link($name, $href, $opt));
			enl('</li>');
		}
	}
?>

<div id="nav">
	<ul>
		<?php
			$links = array();
			if ($session->check('User.id'))
			{
				// add the admin tab if the user is an admin
				if ($session->read('User.admin'))
					$loggedin['Admin'] = array('location' => '/admin/', 'class' => 'hidden-subnav-trigger', 'id' => 'trigger-admin');
				
				// add the organization tab if the user is logged in
				// check if the user is part of a group first
				if (!$session->read('GroupMember.id'))
				{
					$keys = array_keys($hasorg);
					unset($hasorg[$keys[0]]['class']);
				}
				$loggedin = array_merge($hasorg, $loggedin);
				$links = array_merge($thelinks, $loggedin);
			}
			else
				$links = array_merge($thelinks, $loggedout);
			
			NavigationProcessLinks($html, $links, $this->params);
		?>
	</ul>
</div> <!-- nav -->