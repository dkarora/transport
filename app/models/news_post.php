<?php
	class NewsPost extends AppModel
	{
		var $escapeFields = array('title', 'preview', 'content');
		
		var $name = 'NewsPost';
		
		var $belongsTo = array(
			'Author' => array(
				'className' => 'User',
				'foreignKey' => 'author_id'
			)
		);
		
		var $order = 'date_posted DESC';
		
		var $validate = array(
			'preview' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Preview text must be entered.',
					'last' => true
				)
			),
			
			'content' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'The content text must not be empty.',
					'last' => true
				)
			),
			
			'author_id' => array(
				'exists' => array(
					'rule' => 'authorExists',
					'message' => 'The author with this ID must exist.',
					'last' => true
				),
				
				'is_admin' => array(
					'rule' => 'authorIsAdmin',
					'message' => 'The author must be an administrator to post to the news.',
					'last' => true
				)
			),
			
			'title' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'The title field must not be empty.',
					'last' => true
				)
			)
		);
		
		function authorExists($check)
		{
			$author = $this->Author->find('first', array('recursive' => -1, 'conditions' => array('Author.id' => $check['author_id']), 'fields' => array('Author.id')));
			if (!empty($author))
				return true;
			
			return false;
		}
		
		function authorIsAdmin($check)
		{
			$author = $this->Author->find('first', array('recursive' => -1, 'conditions' => array('Author.id' => $check['author_id']), 'fields' => array('Author.admin')));
			if ($author['Author']['admin'])
				return true;
			
			return false;
		}
		
		function afterFind($results, $primary)
		{
			return $this->_htmlEscape($results, $this->escapeFields);
		}
	}
?>