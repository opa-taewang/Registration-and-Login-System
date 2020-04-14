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
			'cookie_expiry' => '604800'
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