<?php
	require_once 'core/init.php';
	
	if (Session::exists('success'))
	{
		echo Session::flash('success');
	}

	echo Session::get(Config::get('session/session_name'));
	 	