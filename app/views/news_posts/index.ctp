<?php $this->set('subnavcontent', $this->element('news_posts_subnav')); ?>
<?php $javascript->link(array('jquery.flow.1.1.min'), false); ?>
<?php $this->set('bodyClass', 'front-page'); ?>

<?php
	// don't render info, gallery on pages > 1
	$isFrontPage = !$paginator->hasPrev();
?>

<?php if ($isFrontPage) : ?>
	<script type="text/javascript">
		//<![CDATA[
			$(function() {
				$('#gallery-wrapper').show();
				
				$("div#controller").jFlow({
					slides: "#slides",
					width: "515px",
					height: "150px"
				});
				
				setTimeout("$('#gallery2 #slides').fadeIn();", 500);
			});
		//]]>
	</script>

	<div id="gallery-wrapper" style="display: none;">
	<div id="gallery2">
		<div id="controller" class="hidden">
			<span class="jFlowControl">No 0 </span>
			<span class="jFlowControl">No 1 </span>
			<span class="jFlowControl">No 2 </span>
		</div>
		
		<div id="prevNext">
			<?php echo $html->image('prev.png', array('alt' => 'Previous', 'class' => 'jFlowPrev')); ?>
			<?php echo $html->image('next.png', array('alt' => 'Next', 'class' => 'jFlowNext')); ?>
		</div>
		
		<div id="slides">
			<div>
				<?php echo $html->image('1.jpg'); ?>
				<p>The Baystate Roads Program - LTAP</p>
			</div>
			
			<div>
				<?php echo $html->image('0.jpg'); ?>
				<p>Sharing The Best In Transportation Technology</p>
			</div>
			
			<div>
				<?php echo $html->image('2.jpg'); ?>
				<p>Located at the University of Massachusetts Amherst<p>
			</div>
		</div>
	</div> <!-- gallery -->
	</div> <!-- gallery-wrapper -->
<?php endif; ?>

<div id="newspage-content">
	<?php if ($isFrontPage) : ?>
	<div id="newspage-info">
		<!-- content for the front page goes here -->
		<h2>Welcome to Baystateroads.org</h2>

		<p style="text-indent: 2em;">
			Welcome to the Baystate Roads Program.
			Established in 1986, the Baystate Roads Program is a cooperative effort of the Federal Highway Administration, Massachusetts Department of Transportation, and the University of Massachusetts Amherst.
			The program provides technology transfer assistance to all communities in the Commonwealth of Massachusetts under the direction of Program Manager, Christopher J. Ahmadjian.</p>
</div><!-- info blurb -->


            
<?php endif; ?>
<!-- can insert video iframe or photos here -->

			<?php echo $html->image('MT14.jpg', array('alt' => 'image of bicyclists, train and bus', 'title' => 'MT14.jpg')); ?>		

     
	<div id="newspage-post-list">
		<h2 id="newsbar-header">News</h2>
		<?php foreach ($newsPosts as $index => $post) : ?>
			
			<div class="news-post-preview">
				<h3 class="newsbar-title">
					<?php echo $html->link($post['NewsPost']['title'], $link->viewNewsPost($post), array('escape' => false)); ?>
				</h3>
				
				<p class="newsbar-timestamp">
					<?php echo $html->image('timestamp-icon.png', array('alt' => 'Posted on')); ?>
					<span><?php echo $timeFormatter->commonDate($post['NewsPost']['date_posted']), ' (', $timeFormatter->ago($post['NewsPost']['date_posted']), ')'; ?></span>
				</p>
				
				<p class="newsbar-author">
					<?php echo $html->image('author-icon.png', array('alt' => 'Authored by')); ?>
					<span><?php echo $post['Author']['first_name'], ' ', $post['Author']['last_name']; ?></span>
				</p>
				
				<div class="newsbar-preview">
					<?php echo $bbCode->bb2html($post['NewsPost']['preview']); ?>
				</div>
				<p class="newsbar-readmore"><?php echo $html->link('Read More &raquo;', $link->viewNewsPost($post), array('class' => 'button-link', 'escape' => false)); ?></p>
			</div>
			
		<?php endforeach; ?>
	</div> <!-- post list -->
</div>

<div id="newspage-sidebar">
	<div id="newspage-sidebar-content">
		
		<div id="newspage-managers-corner" class="sidebar-box">
			<h2>Manager's Corner</h2>
			<?php echo $html->image('staff_chris.jpg', array('alt' => 'Christopher J. Ahmadjian', 'title' => 'Christopher J. Ahmadjian')); ?>
			<p><?php echo $announcement; ?></p>
			<p id="newspage-managers-corner-signature">&mdash; Christopher J. Ahmadjian</p>
			<p class="clearfix"></p>
		</div> <!-- manager's corner -->
		
		
		
			<div id="newspage-managers-corner" class="sidebar-box">
			<h2>Upcoming Events</h2>
            <?php echo $html->image('MT14small.jpg', array('alt' => 'photo of people biking, walking and a bus', 'title' => 'MT14small')); ?>
            <h6>2014 Moving Together Conference</h6>

            <big>October 30 &bull; Boston, MA</big>
			<p class="newsbar-readmore"><?php echo $html->link('More Information/Register &raquo;', 'http://www.movingtogetherma.org/', array('class' => 'button-link', 'escape' => false)); ?></p>
			<p class="clearfix"></p>
		</div> <!-- special events corner -->


	
		<div id="newspage-recently-added-workshops" class="sidebar-box">
			<h2>New Workshops</h2>
			
			<?php
				foreach ($recentWorkshops as $ws)
				{
					echo $html->tag('div', null, array('class' => 'newspage-recent-workshop'));
					echo $html->tag('h3', $html->link($ws['Detail']['name'], $link->viewWorkshop($ws)));
					// clock icon, timestamp, relative time
					echo $html->tag('p', sprintf('%s %s (%s)', $html->image('timestamp-icon.png', array('alt' => 'Timestamp')), $timeFormatter->commonDate($ws['Workshop']['date']), $timeFormatter->ago($ws['Workshop']['date'])), array('class' => 'newsbar-iconed-text timestamp'));
					// author icon, instructor name
					echo $html->tag('p', sprintf('%s %s', $html->image('author-icon.png', array('alt' => 'Instructor')), $ws['Workshop']['instructor']), array('class' => 'newsbar-iconed-text author'));
					echo $html->tag('p', $html->image('map-pin.png', array('alt' => 'Location')) . $ws['Workshop']['location'], array('class' => 'newsbar-iconed-text location'));
					echo $html->tag('p', $ws['Workshop']['city'], array('class' => 'newsbar-iconed-text city'));
					echo $html->tag('/div', null);
				}
			?>
			
			<p class="all"><?php echo $html->link('View all', '/workshops/'); ?></p>
		</div> <!-- recently added workshops -->
	</div> <!-- sidebar-content -->
</div> <!-- sidebar -->

<p class="clearfix"></p>

<?php echo $this->element('page-numbers', array('id' => 'newspage-page-listing')); ?>

<p class="clearfix"></p>