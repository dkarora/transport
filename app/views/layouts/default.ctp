<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
 
<?php echo $facebook->html(); ?>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $title_for_layout; ?></title>
	<?php echo $html->meta('icon') . "\n"; ?>
	<?php echo $html->meta('description', 'The Baystate Roads Program provides technology transfer assistance to all communities in the Commonwealth of Massachusetts. The program is a cooperative effort of the Federal Highway Administration, Massachusetts Executive Office of Transportation, and the University of Massachusetts at Amherst.'); ?>
	<?php echo $html->meta('keywords', 'transportation, Massachusetts, Commonwealth of Massachusetts, Baystate Roads, Bay State Roads, technology, workshops, snow plowing, road safety, highways, department of transportation, driving, local technical assistance program, ltap, moving together conference'); ?>
	<?php echo $html->css(array('style', 'subnav-hidden')) . "\n"; ?>
	<!--[if GTE IE 6]>
	<?php echo $html->css('subnav-hidden-ie') . "\n"; ?>
	<![endif]-->
	<!--[if LT IE 8]>
	<?php echo $html->css('skipto-links-ie') . "\n"; ?>
	<![endif]-->
	<?php echo $javascript->link(array('http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js', 'nav-hover')); ?>
	<?php echo $scripts_for_layout . "\n"; ?>
	<?php echo $html->css('print', null, array('media' => 'print')) . "\n"; ?>
	<link rel="alternate" type="application/rss+xml" title="Baystate Roads - News Feed" href="<?php echo Router::url('/news_posts/index.rss', true); ?>" />
	
	<?php
		if (!empty($fb_ogTags))
			echo $facebook->ogTags($fb_ogTags);
	?>
	
	<?php if (!Configure::read('debug')) : ?>
	<!-- google analytics code -->
	<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-9329046-1']);
		_gaq.push(['_trackPageview']);
		
		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
	</script>
	<?php endif; ?>
</head>
 
<body<?php if(isset($bodyClass)): echo " class='$bodyClass'"; endif; ?>>
	<script type="text/javascript">
	//<![CDATA[
		$(function() {
			// set target for external links to maintain
			// xhtml 1.0 strict compliance
			$("a[rel='external']").attr('target', '_blank');
		});
	//]]>
	</script>
	
	<div id="skip-to-links">
		<?php
			echo $html->link('Skip to main content', '#content');
			echo $html->link('Skip to navigation', '#nav');
		?>
	</div> <!-- skip-to-links -->
	
	<div id="topbar">		
		<div id="topbar-search">
			<?php
				$select = array(
					'everything' => 'Entire Site',
					'newsletters' => 'Newsletters',
					'tech_notes' => 'Tech Notes',
					'workshops' => 'Workshops'
				);
				
				echo $form->create('Search', array('url' => '/search/'));
				echo $form->input('query', array('label' => array('text' => 'Query', 'class' => 'offscreen')));
				echo $html->div('', $form->label('area', 'Search area', array('class' => 'offscreen')) . $form->select('area', $select, null, array(), false));
				echo $form->end('Search');
			?>
		</div>
		
		<div id="topbar-links">
			<?php echo $html->link('UMass Amherst', 'http://www.umass.edu/', array('rel' => 'external')); ?> |
			<?php echo $html->link('UMass Transportation Center', 'http://www.ecs.umass.edu/umtc/', array('rel' => 'external')); ?>
		</div>
	</div> <!-- topbar -->
	
	<div id="wrap">
		<div id="header">
			<?php //echo $html->image('beta-badge.png', array('title' => 'Still working on it!', 'alt' => 'beta!', 'id' => 'nifty-badge')); ?>
			<h1>
				<?php
					echo $html->link(
						'Baystate Roads Program',
						'/',
						array('id' => 'headerlink', 'escape' => false)
					);
				?>
			</h1>
			
			<div id="social">
				<p class="tagline">Follow Us!</p>
				<?php
					echo $html->link($html->image('facebook-icon.png', array('alt' => 'BSR on Facebook', 'title' => 'Like us on Facebook!')), 'http://www.facebook.com/pages/Amherst-MA/Baystate-Roads/111717658883225', array('escape' => false, 'id' => 'facebook', 'rel' => 'external'));
					echo $html->link($html->image('twitter-icon.png', array('alt' => 'BSR on Twitter', 'title' => 'Follow @baystateroads on Twitter!')), 'http://twitter.com/BaystateRoads/', array('escape' => false, 'id' => 'twitter', 'rel' => 'external'));
					echo $html->link($html->image('youtube-icon.png', array('alt' => 'BSR on YouTube', 'title' => 'Watch our videos on YouTube!')), 'http://www.youtube.com/user/BaystateRoadsLTAP', array('escape' => false, 'id' => 'youtube', 'rel' => 'external'));
					echo $html->link($html->image('tumblr-icon.png', array('alt' => 'BSR on Tumblr', 'title' => 'Read our latest Newsletters on Tumblr')), 'http://baystateroads.tumblr.com/', array('escape' => false, 'id' => 'tumblr', 'rel' => 'external'));
						echo $html->link($html->image('wordpress-icon.png', array('alt' => 'BSR on wordpress', 'title' => 'Check out the Baystate Roads Blog')), 'http://baystateroadsblog.wordpress.com/', array('escape' => false, 'id' => 'wordpress', 'rel' => 'external'));
						echo $html->link($html->image('flickr.png', array('alt' => 'BSR on flickr', 'title' => 'See Baystate Roads Photos on Flickr')), 'http://www.flickr.com/photos/baystateroads/', array('escape' => false, 'id' => 'flickr', 'rel' => 'external'));
					echo $html->link($html->image('rss-icon.png', array('alt' => 'BSR RSS Feed', 'title' => 'BSR News Posts Feed!')), '/news_posts/index.rss', array('escape' => false, 'id' => 'rss', 'rel' => 'external'));
				?>
			</div>
			
			<?php if ($session->check('User.id')) : ?>
				<div id="loggedinas">Logged in as <?php echo $session->read('User.username'); ?> (<?php echo $html->link('Log out', '/users/logout/'); ?>)</div>
			<?php endif; ?>
			
			<?php echo $this->element('navigation'); ?>
		</div> <!-- header -->
		
		<?php echo $this->element('subnav-hidden'); ?>
		<?php if(isset($subnavcontent)): echo $subnavcontent; endif; ?>
		<!-- subnav -->
		
		<div id="content">
			<!--[if LTE IE 6]>
			<br class="clearfix" style="line-height: 0;" />
			<![endif]-->
			
			<?php
				if ($session->check('Message.flash'))
					$session->flash();
				$session->flash('valErrors');
				echo $content_for_layout;
			?>
		</div> <!-- content -->
		
		<div id="footer">
			<div class="clearfix" style="line-height: 0;"></div>
			<dl>
				<dt><?php echo $html->link('Home', '/'); ?></dt>
					<dd><?php echo $html->link('About/Contact', '/pages/about'); ?></dd>
					<dd><?php echo $html->link('Tech Notes', '/news_posts/tech_notes/'); ?></dd>
					<dd><?php echo $html->link('Search', '/search'); ?></dd>
			</dl>
			
			<dl>
				<dt>Workshops</dt>
					<dd><?php echo $html->link('Browse Workshops', '/workshops/index/'); ?></dd>
					<dd><?php echo $html->link('Search Workshops', '/workshops/search/'); ?></dd>
			</dl>
			
			<dl>
				<dt>Road Scholars</dt>
					<dd><?php echo $html->link('FAQ', '/road_scholars/'); ?></dd>
					<dd><?php echo $html->link('List of Road Scholars', '/road_scholars/scholars'); ?></dd>
					<dd><?php echo $html->link('List of Master Road Scholars', '/road_scholars/masterscholars'); ?></dd>
					<dd><?php echo $html->link('Check Your Progress', '/road_scholars/checkprogress'); ?></dd>
			</dl>
			
			<dl>
				<dt><?php echo $html->link('Libraries', '/libraries/'); ?></dt>
					<dd><?php echo $html->link('Video Library', '/videos/'); ?></dd>
					<dd><?php echo $html->link('Publications Library', '/publications/'); ?></dd>
					<dd><?php echo $html->link('Cart', '/cart_items/'); ?></dd>
			</dl>
			
			<dl>
				<dt>My Account</dt>
					<?php if ($session->check('User.id')) : ?>
						<dd><?php echo $html->link('Dashboard', '/users/'); ?></dd>
						<dd><?php echo $html->link('Edit Account Details', '/users/edit/'); ?></dd>
						<dd><?php echo $html->link('Log out', '/users/logout/'); ?></dd>
					<?php else : ?>
						<dd><?php echo $html->link('Log in', '/users/login/'); ?></dd>
						<dd><?php echo $html->link('Register', '/users/register/'); ?></dd>
						<dd><?php echo $html->link('Forgot Username/Password', '/users/forgot/'); ?></dd>
					<?php endif; ?>
			</dl>
			
			<div style="clear: both; height: 1em"></div>
			
			<div id="footer-logos">
				<?php
					echo $html->link($html->image('footer-fhwa.png', array('alt' => 'United States Federal Highway Administration Logo', 'title' => 'United States Federal Highway Administration')), 'http://www.fhwa.dot.gov/', array('escape' => false, 'rel' => 'external'));
					echo $html->link($html->image('footer-massdot.png', array('alt' => 'Massachusetts Department of Transportation Logo', 'title' => 'Massachusetts Department of Transportation')), 'http://www.massdot.state.ma.us/', array('escape' => false, 'rel' => 'external'));
					echo $html->link($html->image('footer-umtc.png', array('alt' => 'University of Massachusetts Transportation Center Logo', 'title' => 'University of Massachusetts Transportation Center (UMTC)')), 'http://www.ecs.umass.edu/umtc/', array('escape' => false, 'rel' => 'external'));
					echo $html->link($html->image('footer-ltap.png', array('alt' => 'Local &amp; Tribal Technical Assistance Program Logo', 'title' => 'Local and Tribal Technical Assistance Program (LTAP)')), 'http://www.ltap.org/', array('escape' => false, 'rel' => 'external'));
				?>
			</div> <!-- footer-logos -->
			
			<div id="copyright">&copy; 2009-<?php echo date('Y'); ?> Baystate Roads Program. All Rights Reserved. <span id="app-version">v<?php echo Configure::read('App.version_number'); ?>.</span></div>
			<?php echo $this->element('wisdom'); ?>
		</div> <!-- footer -->
	</div> <!-- wrap -->
	<?php echo $facebook->init(); ?>
</body>
</html>