<?php
	/**
	 * 
	 */
	class User
	{
		private $_db,
				$_data = array(),
				$_sessionName,
				$_cookieName,
				$_isLoggedIn; 

		public function __construct($user = '')
		{
			$this->_db 				= DB::getInstance();
			$this->_sessionName 	= Config::get('session/session_name');
			$this->_cookieName 		= Config::get('remember/cookie_name');

			if (!$user)
			{
				if (Session::exists($this->_sessionName)) {
					$user = Session::get($this->_sessionName);
					if ($this->find($user)) {
						$this->_isLoggedIn = true;
					}
					else {
						// Process logout
					}
				}
			} else
			{
				$this->find($user);
			}

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
			$field = filter_var($user, FILTER_VALIDATE_EMAIL) ? 'email' : is_numeric($user) ? 'id' : 'username';
			$data = $this->_db->get('user', array($field, '=', $user));
			if($data->count())
			{
				return $this->_data = $data->first();
			}
			
		}

		public function login($username = '', $password = '', $remember = false)
		{	
			if (!$username && !$password && $this->exists()) {
				Session::put($this->_sessionName, $this->data()->username);
				return true;
			}else
			{
				$user = $this->find($username);
				// Check if username/ email exists
				if ($user) 
				{
					// Decrypt password
					$pass = Hash::make($password, $this->data()->salt);
					// Check is the new hash password matches to the one in the database
					if ($pass === $this->data()->password)
					{
						if ($remember)
						{
							$hash = Hash::unique();
							$hashCheck = $this->_db->get('session', array('user_id', '=', $this->data()->id));
							if (!$hashCheck->count())
							{
								$this->_db->insert('session', array(
									'user_id' => $this->data()->id,
									'hash' => $hash
								));
							}else
							{
								$hash = $hashCheck->first()->hash;
							}

							Cookie::put($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));
						}
						Session::put($this->_sessionName, $this->data()->username);
						return true;
					}
				}
			}
			return false;
		}

		public function exists()
		{
			return (!empty($this->_data)) ? true : false;
		}
		public function logout()
		{
			$this->_db->delete('session', array('user_id', '=', $this->data()->id));
			Session::delete($this->_sessionName);
			Cookie::delete($this->_cookieName);
		}

		public function data()
		{
			return $this->_data;
		}

		public function isLoggedIn()
		{
			return $this->_isLoggedIn;
		}


	}