<?php
	session_start();

	$GLOBALS['config'] = array(
		// For database connection 
		'mysql' => array(
			'dbHost' => '127.0.0.1',
			'dbUser' => 'root',
			'dbPass' => '',
			'dbName' => 'chat'
		),
		// For cookies
		'remember' => array(
			'cookie_name' => 'hash',
			'cookie_expiry' => 604800
		),
		// For session
		'session' => array(
			'session_name' => 'user',
			'token_name' => 'token'
		)
	);

	spl_autoload_register('myAutoLoader');

	function myAutoLoader($className){
		$path = 'classes/';
		$ext  = '.class.php';
		$fullPath = $path . $className . $ext;

		if(!file_exists($fullPath)){
			return false;
		}

		require_once $fullPath;
	}

	require_once 'functions/sanitize.php';

	if (Cookie::exists(Config::get('remember/cookie_name')) && !Session::exists(Config::get('session/session_name')))
	{
		$hash = Cookie::get(Config::get('remember/cookie_name'));
		$hashCheck = DB::getInstance()->get('session', array('hash', '=', $hash));

		if ($hashCheck->count())
		{
			$user = new User($hashCheck->first()->user_id);
			$login = $user->login();
			if ($login) {
				$message = "<p>You are welcome back<b>" . escape($user->data()->username) . "<b><p>";
				Session::flash('welcome', $message);
            	Redirect::to('index.php');
			}
		}
	}