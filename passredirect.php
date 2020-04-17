<?php
	require_once 'core/init.php';

	if (!Input::get('hash'))
	{
	    Redirect::to(404);
	}else
	{
		$user = new User;
		if ($user->passRedirect(Input::get('hash')))
		{
			Redirect::to('passwordreset.php');
		}else
		{
			Redirect::to('beginpasswordreset.php');
		}
	}