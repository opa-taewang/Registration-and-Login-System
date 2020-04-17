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
				
			} else
			{
				$subject = "Account Verification";
                $message = '
                        <h3>Thanks for signing up!<h3>
                        <p>Your account has been created, you can login with the following credentials after you have activated your account by pressing the url below.<p>
 
                        <p>------------------------</p>
                        <p>Username: '.Input::get('username').'</p> 
                        <p>Password: '.Input::get('password').'</p>
                        <p>------------------------</p>
 
                        <p>Please click this link to activate your account:
                        https://localhost/verify.php?salt='.$fields['salt'].'<p>
                        ';
				Email::send(Input::get('email'), $subject, $message);
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
						if($this->data()->role > 0)
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
						else
						{
							echo "Please verify your account";;
						}
					} else
					{
						echo "Incorrect username or password";
					}
				}
			}
			return false;
		}

		//Verify user account
		public function verify($salt)
		{
			$verify = $this->_db->get('user', array('salt', '=', $salt));
			if ($verify->first()->role == 0)
			{
				if($this->_db->update('user', array('role' => 1), array('salt', '=', $salt)))
				{
					echo "Verification successful";
				}
			}else
			{
				Redirect::to('login.php');
			}
		}

		// Begin password reset
		public function beginPasswordReset($email)
		{
			//$user = $this->find($email);
			$user = $this->_db->get('user', array('email', '=', $email));
			if($user->count())
			{
				$username = $user->first()->username;
				$hash = Hash::unique();
				$fields = array(
					'user_id' => $user->first()->id,
					'hash' => $hash
				);
				if($this->_db->insert('password_reset', $fields)){
					// Send email
					$subject = "Password Reset";
					$message = '
	                    <h3>Dear '. $username .' ,<h3>
	                         
	                   	<p>Click the link below to reset your password:
	                   	https://localhost/passredirect.php?hash='.$hash.'</p>
	                    ';
					Email::send($email, $subject, $message);
					return true;
				}
			}
			return false;
		}

		// Redirect password reset
		public function passRedirect($hash)
		{
			$getId = $this->_db->get('password_reset', array('hash', '=', $hash));
			if($getId->count())
			{
				$this->_db->get('user', array('id', '=', $getId->first()->user_id));
				Cookie::put($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));
				return true;
			}
			return false;
		}

		public function findHash($hash)
		{
			$find = $this->_db->get('password_reset', array('hash', '=', $hash));
			if($find->count())
			{
				return $this->_data = $find->first();
				return true;
			}else
			{
				Cookie::delete('hash');
			}
			return false;
		}

		// Password reset
		public function passwordReset($hash, $fields = array())
		{
			if($this->findHash($hash))
			{
				$update = $this->_db->update('user', $fields, array('id', '=', $this->data()->user_id));
				if($update)
				{
					if ($this->_db->delete('password_reset', array('hash', '=', $hash))) 
					{
					Cookie::delete('hash');
					$this->_db->delete('session', array('user_id', '=', $this->data()->user_id));
					return true;
					}	
				}
			}
			return false;
		}

		// Check if data exists for cookie
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