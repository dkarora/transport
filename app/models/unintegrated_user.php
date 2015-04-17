<?php
	class UnintegratedUser extends AppModel
	{
		var $name = 'UnintegratedUser';
		var $order = 'UnintegratedUser.last_name';
		
		function beforeSave()
		{
			// since this is a view we can't (shouldn't) save to it
			return false;
		}
		
		function beforeDelete()
		{
			// same here
			return false;
		}
	}
?>