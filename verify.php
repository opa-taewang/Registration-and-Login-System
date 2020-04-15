<?php 
	require_once 'core/init.php';

		if($salt = Input::get('salt'))
		{
			$verification = new User();
			$verification->verify($salt);
		}else
		{
			Redirect::to(404);
		}
	