<?php
	class OauthAccessToken extends AppModel
	{
		var $name = 'OauthAccessToken';
		
		var $validate = array(
			'consumer' => array(
				'oneTokenPerConsumer' => array(
					'rule' => 'isUnique',
					'message' => 'Only one token per consumer allowed.'
				)
			)
		);
		
		function tokenExists($consumer)
		{
			return ($this->find('count', array('conditions' => array('consumer' => $consumer))) > 0);
		}
		
		function deleteToken($consumer)
		{
			$tok = $this->getToken($consumer);
			$id = $tok[$this->alias]['id'];
			if ($this->delete($id))
				return $id;
			return false;
		}
		
		function setToken($consumer, $token)
		{
			// if twitter doesn't exist then saving is all we need to do
			if (!$this->tokenExists($consumer))
				return $this->save(array('consumer' => $consumer, 'key' => $token->key, 'secret' => $token->secret));
			else
			{
				// get the token, then update it
				$oldToken = $this->getToken($consumer);
				$oldToken[$this->name]['key'] = $token->key;
				$oldToken[$this->name]['secret'] = $token->secret;
				
				return $this->save($oldToken);
			}
		}
		
		function getToken($consumer)
		{
			return $this->find('first', array('conditions' => array('consumer' => $consumer)));
		}
	}
?>