<?php
	/**
	 * 
	 */
	class User
	{
		private $_db,
				$_data = array(),
				$_sessionName; 

		public function __construct()
		{
			$this->_db = DB::getInstance();
			$this->_sessionName = Config::get('session/session_name');

		}

		public function create($fields = array())
		{
			if (!$this->_db->insert('user', $fields))
			{
				throw new Exception("There was a problem creating an account");
				
			}
		}

		public function find($user = '')
		{	
			$field = filter_var($user, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
			$data = $this->_db->get('user', array($field, '=', $user));
			if($data->count())
			{
				return $this->_data = $data->first();
			}
			
		}

		public function login($username = '', $password = '')
		{	
			$user = $this->find($username);
			// Check if username/ email exists
			if ($user) {
				// Decrypt password
				$pass = Hash::make($password, $this->data()->salt);
				// Check is the new hash password matches to the one in the database
				if ($pass === $this->data()->password)
				{
					Session::put($this->_sessionName, $this->data()->id);
				return true;
				}
			}
			return false;
		}

		private function data()
		{
			return $this->_data;
		}


	}