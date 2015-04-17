<?php
	class NewsPostsController extends AppController
	{
		var $name = 'NewsPosts';
		var $components = array('SessionPlus', 'ErrorListFormatter', 'RequestHandler', 'OauthConsumer', 'Bitly', 'Link');
		var $uses = array('NewsPost', 'Newsletter', 'TechNote', 'Workshop', 'Announcement', 'OauthAccessToken');
		var $helpers = array('Javascript', 'TimeFormatter', 'BbCode', 'Link');
		var $paginate = array(
			'NewsPost' => array(
				'recursive' => 2,
				'limit' => 5,
				'fields' => array('NewsPost.id', 'NewsPost.title', 'NewsPost.preview', 'NewsPost.author_id', 'NewsPost.date_posted')
			)
		);
		
		function beforeFilter()
		{
			parent::beforeFilter();
			
			$adminActions = array('create');
			$this->SessionPlus->denyNonAdminsFrom($adminActions);
			
			$this->pageTitle = 'Baystate Roads &rsaquo; ';
		}
		
		function index()
		{
			$this->useOpenGraph = true;
			
			// check for rss request
			// this will use /app/views/news_posts/rss/index.ctp as the view
			if ($this->RequestHandler->isRss())
			{
				$this->pageTitle .= 'News Posts (RSS)';
				
				$posts = $this->NewsPost->find('all');
				$this->set('posts', $posts);
			}
			else
			{
				$this->pageTitle .= 'Home';
				
				$posts = $this->paginate('NewsPost');
				
				// get the most recently added workshops
				$conditions = array('Workshop.unlisted' => 0, 'Workshop.date >=' => date('Y-m-d H:i:s', strtotime('-7 days')));
				$workshops = $this->Workshop->find('all', array('recursive' => 0, 'order' => 'Workshop.date DESC', 'limit' => 6, 'conditions' => $conditions));
				$workshops = Set::sort($workshops, '{n}.Workshop.date', 'ASC');
				
				// get the most recent announcement
				$announcement = $this->Announcement->mostRecent();
				
				$this->set('announcement', $announcement);
				$this->set('newsPosts', $posts);
				$this->set('recentWorkshops', $workshops);
			}
		}
		
		function newsletters($mode = null)
		{
			$this->pageTitle .= 'Newsletters';
			
			if ($mode == 'all_years')
			{
				$this->set('all_years', true);
				$this->set('years', $this->Newsletter->recordedYears());
			}
			else
			{
				$this->set('most_recent', $this->Newsletter->mostRecent());
				$this->set('years', $this->Newsletter->recordedYears(10));
				
				if (!empty($this->params['named']['year']))
				{
					$this->pageTitle .= ' &rsaquo; ' . $this->params['named']['year'];
					$this->set('year_newsletters', $this->Newsletter->findYear($this->params['named']['year']));
				}
			}
		}
		
		function tech_notes($mode = null)
		{
			$this->pageTitle .= 'Tech Notes';
			$this->set('show_all', false);
			$tn = array();
			
			if ($mode == 'all')
			{
				$this->set('show_all', true);
				$tn = $this->TechNote->findAll();
			}
			else
			{
				$tn = $this->TechNote->mostRecent();
			}
			
			$this->set('tn', $tn);
		}
		
		function _notFound()
		{
			$this->cakeError('error404');
		}
		
		function view($id = null, $slug = null)
		{
			$this->useOpenGraph = true;
			$this->openGraphTags['og:type'] = 'article';
			
			// no id? do a 404
			if (empty($id))
				$this->_notFound();
			
			// get the post
			$post = $this->NewsPost->find('first', array('conditions' => array('NewsPost.id' => $id), 'recursive' => 2));
			
			// bad id? 404
			if (empty($post))
				$this->_notFound();
			
			// no slug? redirect
			if (empty($slug))
				$this->redirect($link->viewNewsPost($post));
			
			// set the page vars
			$this->pageTitle .= $post['NewsPost']['title'];
			$this->set('post', $post);
			$neighbors = $this->NewsPost->find('neighbors', array('field' => 'NewsPost.id', 'value' => $id, 'recursive' => -1, 'fields' => 'NewsPost.id, NewsPost.title'));
			$this->set('neighbors', $neighbors);
		}
		
		function create()
		{
			if (!empty($this->data))
			{
				$this->data['NewsPost']['author_id'] = $this->Session->read('User.id');
				$preview_was_set = true;
				
				// if a preview wasn't specified, then generate it from
				// the first few sentences.
				/*if (empty($this->data['NewsPost']['preview']))
				{
					$preview_was_set = false;
					$words = explode(' ', $this->data['NewsPost']['content']);
					$limit = 500;
					$preview = array_shift($words);
					$w = array_shift($words);
					
					// subtract 3 from limit to fit the ellipsis
					$limit -= 3;
					
					while (sizeof($words) > 0 && strlen($preview . ' ' . $w) <= $limit)
					{
						$preview .= ' ' . $w;
						$w = array_shift($words);
					}
					
					$preview .= '...';
					
					$this->data['NewsPost']['preview'] = $preview;
				}*/
				
				if ($this->NewsPost->save($this->data))
				{
					// log it
					$format = '%s created news post "%s" (%d)';
					$message = sprintf($format,
						$this->SessionPlus->loggableUserName(),
						$this->data['NewsPost']['title'], $this->NewsPost->id
					);
					$this->logAction($this->NewsPost, 'create', $message);
					
					$this->SessionPlus->flashSuccess('New post created.');
					$this->ErrorListFormatter->deleteOldData();
					
					// tweet it too
					if (!empty($this->data['NewsPost']['tweet']) && $this->data['NewsPost']['tweet'] == 1)
					{
						$this->data['NewsPost']['id'] = $this->NewsPost->id;
						
						// make a bit.ly url
						$url = $this->Bitly->shorten(Router::url($this->Link->viewNewsPost($this->data), true));
						
						// create the tweet
						$status = 'New Post: ' . $this->data['NewsPost']['title'];
						if (strlen($status) >= 140 - (strlen($url) + 1))
							$status = substr($status, 0, 140 - (strlen($url) + 4)) . '...';
						$status .= ' ' . $url;
						
						$access = $this->OauthAccessToken->getToken('Twitter');
						$this->OauthConsumer->post('Twitter', $access['OauthAccessToken']['key'], $access['OauthAccessToken']['secret'], 'http://api.twitter.com/1/statuses/update.json', array('status' => $status));
					}
				}
				else
				{
					/*if (!$preview_was_set)
						$this->data['NewsPost']['preview'] = '';*/
					$this->SessionPlus->flashError('Post was not created. Please fix the errors below.');
					//$this->ErrorListFormatter->format($this->NewsPost->validationErrors);
					$this->set('old_data', $this->data);
					$this->ErrorListFormatter->setOldData($this->data);
				}
				
				$this->redirect($this->referer());
			}
		}
		
		function links()
		{
			$this->pageTitle .= 'Links';
		}
	}
?>