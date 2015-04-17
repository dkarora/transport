<?php
	class User extends AppModel
	{
		var $name = 'User';
		var $order = array('last_name' => 'asc');
		// to generate a summary of a user's assets (related models), set this to 'string' or 'array'.
		// this makes *a lot* of queries right now, so use sparingly!
		var $asset_summary_format = false;
		
		var $validate = array(
			'human' => array(
				'blank' => array(
					'rule' => 'blank',
					'message' => 'Are you a spam bot?',
					'last' => true,
					'on' => 'create'
				)
			),
			
			'username' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter a username.',
					'last' => true
				),
				
				'alphanumeric' => array(
					'rule' => 'alphaNumeric',
					'message' => 'Username must consist of only letters and numbers.',
					'last' => true
				),
				
				'unavailable' => array(
					'rule' => 'isUnique',
					'message' => 'That username is unavailable.'
				)
			),
			
			'password' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter a password.'
				),
				
				'passwordlength' => array(
					'rule' => array('minLength', 6),
					'message' => 'Passwords must be at least 6 characters in length.',
					'last' => true
				)
			),
			
			'password_repeat' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please verify the password.',
					'last' => true
				),
				
				'matchpassword' => array(
					'rule' => array('matchFields', 'password'),
					'message' => 'Password fields must match.'
				)
			),
			
			'first_name' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter a first name.'
				)
			),
			
			'last_name' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter a last name.',
					'last' => true
				)
			),
			
			'email' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter an email address.',
					'last' => true
				),
				
				'isemail' => array(
					'rule' => 'email',
					'message' => 'Please enter a valid email address.'
				)
			),
			
			'address_line1' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter the first line of the address.',
					'last' => true
				)
			),
			
			'city' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter the city of place of work.',
					'last' => true
				)
			),
			
			'state' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter the state.',
					'last' => true
				),
				
				'isstate' => array(
					'rule' => 'validState',
					'message' => 'Please enter a valid state abbreviation.',
					'last' => true
				)
			),
			
			'zip' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter a ZIP code.',
					'last' => true
				),
				
				'iszip' => array(
					'rule' => array('postal', null, 'us'),
					'message' => 'Please enter a valid United States ZIP code.',
					'last' => true
				)
			),
			
			'phone' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter a phone number.',
					'last' => true
				),
				
				'phone' => array(
					'rule' => array('phone', null, 'us'),
					'message' => 'Please enter a valid United States phone number in the format XXX XXX XXXX.',
					'last' => true
				)
			),
			
			'fax' => array(
				'phone' => array(
					'allowEmpty' => true,
					'rule' => array('phone', null, 'us'),
					'message' => 'Please enter a valid Unites States phone number in the format XXX XXX XXXX.',
					'last' => true
				)
			),
			
			// for password changes
			'current_password' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter your current password.',
					'last' => true
				),
				
				'is_current' => array(
					'rule' => 'matchesCurrentPassword',
					'message' => 'Current password is incorrect. Please try again.',
					'last' => true
				)
			),
			
			'new_password' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter the new password.',
					'last' => true
				),
				
				'confirm_match' => array(
					'rule' => 'newPasswordsMatch',
					'message' => 'The new passwords do not match. Please try again.',
					'last' => true
				)
			),
			
			'new_password_confirm' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please confirm the new password.',
					'last' => true
				)
			),
			
			'affiliation' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter the affiliation.',
					'last' => true
				)
			),
		);
		
		var $hasOne = array(
			'GroupInvite' => array(
				'className' => 'GroupInvite',
				'foreignKey' => 'user_id',
				'dependent' => true
			),
			
			'GroupMember' => array(
				'className' => 'GroupMember',
				'foreignKey' => 'user_id',
				'dependent' => true
			)
		);
		
		function _handleAssetArray(&$assets, $count, $singular_asset, $pluralizer = 's')
		{
			if ($count == 0)
				return '';
			
			$asset = $count . ' ' . $singular_asset;
			if ($count > 1)
				$asset .= $pluralizer;
			$assets []= $asset;
			
			return $asset;
		}
		
		function afterFind($results)
		{
			parent::afterFind($results);
			
			// allocating asset_summary-related vars here will prevent models from being allocated repeatedly.
			if ($this->asset_summary_format !== false)
			{
				$Attendee =& ClassRegistry::init('Attendee');
				$CartItem =& ClassRegistry::init('CartItem');
				$IntegrationRequest =& ClassRegistry::init('IntegrationRequest');
				$NewsPost =& ClassRegistry::init('NewsPost');
				$PublicationCheckout =& ClassRegistry::init('PublicationCheckout');
				$PublicationRequest =& ClassRegistry::init('PublicationRequest');
				$VideoCheckout =& ClassRegistry::init('VideoCheckout');
				$VideoRequest =& ClassRegistry::init('VideoRequest');
			}
			
			foreach ($results as $key => $value)
			{
				// this should prevent returning strings like a a, a. for full_name
				if (!is_array($value))
					continue;
				
				$fullName = '';
				$data = null;
				$publicName = '';
				$asset_summary = '';
				
				// let's assume that if we find the first name we find any other required fields
				// (primary)
				if (!empty($value[$this->alias]))
					$data = $value[$this->alias];
				// (non-primary)
				else
					$data = $value;
				
				if (!empty($data['first_name']))
				{
					$fullName = $data['first_name'] . ' ';
					$publicName = $data['first_name'] . ' ';
					
					if (!empty($data['middle_initial']))
						$fullName .= $data['middle_initial'] . '. ';
					
					$fullName .= $data['last_name'];
					$publicName .= substr($data['last_name'], 0, 1) . '.';
					
					if (!empty($data['suffix']))
						$fullName .= ', ' . $data['suffix'];
					
					// generate asset summary for delete_users
					if ($this->asset_summary_format !== false)
					{
						$assets = array();
						
						// check attendee records first.
						$Attendee->recursive = -1;
						$count_attendance_records = $Attendee->find('count', array('conditions' => array($Attendee->alias.'.user_id' => $data['id'])));
						$this->_handleAssetArray($assets, $count_attendance_records, 'Attendance Record');
						
						// payment records, with payment options removed
						$Attendee->PaymentRecord->unbindModel(array('belongsTo' => array('PaymentOption')));
						$count_payment_records = $Attendee->PaymentRecord->find('count', array('conditions' => array('Attendee.user_id' => $data['id'])));
						$this->_handleAssetArray($assets, $count_payment_records, 'Payment Record');
						
						// cart items
						$CartItem->recursive = -1;
						$count_cart_items = $CartItem->find('count', array('conditions' => array($CartItem->alias.'.user_id' => $data['id'])));
						$this->_handleAssetArray($assets, $count_cart_items, 'Cart Item');
						
						// group invites
						$this->GroupInvite->recursive = -1;
						$count_group_invites = $this->GroupInvite->find('count', array('conditions' => array($this->GroupInvite->alias.'.user_id' => $data['id'])));
						$this->_handleAssetArray($assets, $count_group_invites, 'Group Invite');
						
						// group member (records)
						$this->GroupMember->recursive = -1;
						$count_group_members = $this->GroupMember->find('count', array('conditions' => array($this->GroupMember->alias.'.user_id' => $data['id'])));
						$this->_handleAssetArray($assets, $count_group_members, 'Group Member Record');
						
						// integration requests
						$IntegrationRequest->recursive = -1;
						$count_integration_requests = $IntegrationRequest->find('count', array('conditions' => array($IntegrationRequest->alias.'.user_id' => $data['id'])));
						$this->_handleAssetArray($assets, $count_integration_requests, 'Integration Request');
						
						// news posts
						$NewsPost->recursive = -1;
						$count_news_posts = $NewsPost->find('count', array('conditions' => array($NewsPost->alias.'.author_id' => $data['id'])));
						$this->_handleAssetArray($assets, $count_news_posts, 'News Post');
						
						// publication checkouts
						$PublicationCheckout->recursive = -1;
						$count_publication_checkouts = $PublicationCheckout->find('count', array('conditions' => array($PublicationCheckout->alias.'.user_id' => $data['id'])));
						$this->_handleAssetArray($assets, $count_publication_checkouts, 'Publication Checkout');
						
						// publication requests
						$PublicationRequest->recursive = -1;
						$count_publication_requests = $PublicationRequest->find('count', array('conditions' => array($PublicationRequest->alias.'.user_id' => $data['id'])));
						$this->_handleAssetArray($assets, $count_publication_checkouts, 'Publication Request');
						
						// video checkouts
						$VideoCheckout->recursive = -1;
						$count_Video_checkouts = $VideoCheckout->find('count', array('conditions' => array($VideoCheckout->alias.'.user_id' => $data['id'])));
						$this->_handleAssetArray($assets, $count_Video_checkouts, 'Video Checkout');
						
						// video requests
						$VideoRequest->recursive = -1;
						$count_Video_requests = $VideoRequest->find('count', array('conditions' => array($VideoRequest->alias.'.user_id' => $data['id'])));
						$this->_handleAssetArray($assets, $count_Video_checkouts, 'Video Request');
						
						if ($this->asset_summary_format == 'string')
							$asset_summary = implode(', ', $assets);
						
						// coalesce into human-readable string or empty array if necessary
						if (empty($asset_summary))
						{
							if ($this->asset_summary_format == 'string')
								$asset_summary = 'No Assets';
							if ($this->asset_summary_format == 'array')
								$asset_summary = array();
						}
					} // and after all that, it would have been easier (and probably more efficient) to write a view in sql. oh well.
					
					// (primary)
					if (isset($value[$this->alias]['first_name']))
					{
						$results[$key][$this->alias]['full_name'] = $fullName;
						$results[$key][$this->alias]['public_name'] = $publicName;
						$results[$key][$this->alias]['asset_summary'] = $asset_summary;
					}
					// (non-primary)
					else
					{
						$results['full_name'] = $fullName;
						$results['public_name'] = $publicName;
						$results['asset_summary'] = $asset_summary;
					}
				}
			}
			
			return $results;
		}
		
		function newPasswordsMatch($check)
		{
			return ($this->data[$this->name]['new_password'] == $this->data[$this->name]['new_password_confirm']);
		}
		
		function matchesCurrentPassword($check)
		{
			$hash = sha1(Configure::read('Security.salt') . $check['current_password']);
			$pwd = $this->field('password', array('User.id' => $this->data['User']['id']));
			
			if ($pwd == $hash)
				return true;
			
			return false;
		}
		
		// determines if the user specified by $admin_id
		// can administer the user specified by $user_id's account.
		function canAdminister($admin_id, $user_id)
		{
			// check the ids before we move onto the happy cases
			if (empty($admin_id) || empty($user_id))
				return false;
			
			// all users can administer themselves
			if ($admin_id === $user_id)
				return true;
			
			$admin = $this->find('first', array('conditions' => $this->alias.".id = $admin_id", 'recursive' => 0));
			$user = $this->find('first', array('conditions' => $this->alias.".id = $user_id", 'recursive' => 0));
			
			// if either user doesn't actually exist, then nothing makes sense and your whole world view is shattered
			if (empty($admin) || empty($user))
				return false;
			
			// if the $admin_id is a sitewide admin then it doesn't matter who $user_id is
			if ($admin['User']['admin'])
				return true;
			
			// this could be a problem if there's more than two user classes
			if ($admin['GroupMember']['permissions'] >= $user['GroupMember']['permissions'] && $admin['GroupMember']['group_id'] == $user['GroupMember']['group_id'])
				return true;
			return false;
		}
		
		function getStates()
		{
			return array_merge(array('--' => '--'), $this->getValidStates());
		}
		
		function getValidStates()
		{
			return array(
				'AL'=>"Alabama",
				'AK'=>"Alaska", 
				'AZ'=>"Arizona", 
				'AR'=>"Arkansas", 
				'CA'=>"California", 
				'CO'=>"Colorado", 
				'CT'=>"Connecticut", 
				'DE'=>"Delaware", 
				'DC'=>"District Of Columbia", 
				'FL'=>"Florida", 
				'GA'=>"Georgia", 
				'HI'=>"Hawaii", 
				'ID'=>"Idaho", 
				'IL'=>"Illinois", 
				'IN'=>"Indiana", 
				'IA'=>"Iowa", 
				'KS'=>"Kansas", 
				'KY'=>"Kentucky", 
				'LA'=>"Louisiana", 
				'ME'=>"Maine", 
				'MD'=>"Maryland", 
				'MA'=>"Massachusetts", 
				'MI'=>"Michigan", 
				'MN'=>"Minnesota", 
				'MS'=>"Mississippi", 
				'MO'=>"Missouri", 
				'MT'=>"Montana",
				'NE'=>"Nebraska",
				'NV'=>"Nevada",
				'NH'=>"New Hampshire",
				'NJ'=>"New Jersey",
				'NM'=>"New Mexico",
				'NY'=>"New York",
				'NC'=>"North Carolina",
				'ND'=>"North Dakota",
				'OH'=>"Ohio", 
				'OK'=>"Oklahoma", 
				'OR'=>"Oregon", 
				'PA'=>"Pennsylvania", 
				'RI'=>"Rhode Island", 
				'SC'=>"South Carolina", 
				'SD'=>"South Dakota",
				'TN'=>"Tennessee", 
				'TX'=>"Texas", 
				'UT'=>"Utah", 
				'VT'=>"Vermont", 
				'VA'=>"Virginia", 
				'WA'=>"Washington", 
				'WV'=>"West Virginia", 
				'WI'=>"Wisconsin", 
				'WY'=>"Wyoming");
		}
		
		function validState($check)
		{
			return in_array(strtoupper($check['state']), array_keys($this->getValidStates()));
		}
		
		function matchFields($field = array(), $compare_field = null)
		{
			foreach( $field as $key => $value )
			{
				$v1 = $value;
				$v2 = $this->data[$this->name][ $compare_field ];
				
				if($v1 !== $v2)
					return FALSE;
				else
					continue;
			}
			return TRUE; 
		}
		
		function beforeSave()
		{
			// this will be false if updating user info (name, email, etc)
			// and true if registering or updating password
			if (isset($this->data['User']['password']))
				$this->data['User']['password'] = sha1(Configure::read('Security.salt') . $this->data['User']['password']);
			return true;
		}
		
		function getPendingRegistrations($fields = array())
		{
			return $this->find('all', array('conditions' => array('User.active' => 0), 'recursive' => -1, 'fields' => $fields));
		}
		
		function getAdmins($recursive = -1)
		{
			return $this->find('all', array('conditions' => array('User.admin' => 1), 'recursive' => $recursive));
		}
		
		function resetAffiliation($user_id, $affiliation = 'Private')
		{
			$this->create();
			$this->id = $user_id;
			$this->set('affiliation', $affiliation);
			return $this->save();
		}
		
		// deletes the user model and all its assets.
		function deleteWithAssets($id = null)
		{
			// add all the models with dependencies for cascade delete
			$this->bindModel(array(
				'hasMany' => array(
					// PaymentRecord is already included under attendee as a dependent row
					'Attendee' => array('className' => 'Attendee', 'dependent' => true, 'foreignKey' => 'user_id'),
					'CartItem' => array('className' => 'CartItem', 'dependent' => true, 'foreignKey' => 'user_id'),
					'IntegrationRequest' => array('className' => 'IntegrationRequest', 'dependent' => true, 'foreignKey' => 'user_id'),
					'NewsPost' => array('className' => 'NewsPost', 'dependent' => true, 'foreignKey' => 'author_id'),
					'PublicationCheckout' => array('className' => 'PublicationCheckout', 'dependent' => true, 'foreignKey' => 'user_id'),
					'PublicationRequest' => array('className' => 'PublicationRequest', 'dependent' => true, 'foreignKey' => 'user_id'),
					'VideoCheckout' => array('className' => 'VideoCheckout', 'dependent' => true, 'foreignKey' => 'user_id'),
					'VideoRequest' => array('className' => 'VideoRequest', 'dependent' => true, 'foreignKey' => 'user_id'),
				),
			), false);
			
			$result = $this->delete($id);
			$this->resetAssociations();
			
			return $result;
		}
	}
?>