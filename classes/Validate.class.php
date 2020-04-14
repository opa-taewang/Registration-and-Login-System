<?php
	/**
	 * 
	 */
	class Validate
	{
		private $_passed = false,
				$_errors = array(),
				$_db = null;

		public $_loginItems = array(
			'username' => array('required' => true),
			'password' => array('required' => true)
		);

		public $_items = array(
			//Validate name
			'name' => array(
				'required' => true,
				'min' => 2,
				'max' => 50,

			),
			//Validate username
			'username' => array(
				'required' => true,
				'min' => 3,
				'max' => 20,
				'type' => 'username',
				'unique' => 'user'
			),
			//Validate email
			'email' => array(
				'required' => true,
				'min' => 2,
				'max' => 50,
				'type' => 'email',
				'unique' => 'user'
			),
			//Validate password
			'password' => array(
				'required' => true,
				'min' => 6,
			),
			//Validate confirm password
			'cpassword' => array(
				'required' => true,
				'matches' => 'password'
			)
		);
		
		public function __construct()
		{
			$this->_db = DB::getInstance();
		}

		public function check($source, $items)
		{
			foreach ($items as $item => $rules) {
				foreach ($rules as $rule => $rule_value)
				{	
					$value = trim($source[$item]);
					$item = escape($item);
					if($rule === "required" && empty($value))
					{
						$this->addError("{$item} is required");
					}
					else if(!empty($value))
					{
						switch ($rule)
						{
							case 'min':
								if (strlen($value) < $rule_value) {
									$this->addError("{$item} must be minimum of {$rule_value} characters");
								}
							break;

							case 'max':
								if (strlen($value) > $rule_value) {
									$this->addError("{$item} must be maximum of {$rule_value} characters");
								}
							break;

							case 'matches':
								if ($value !== $source[$rule_value]) {
									$this->addError("{$rule_value} and {$item} does not match");
								}
							break;

							case 'type':
								switch ($rule_value) {
									case 'email':
										if (!filter_var($value, FILTER_VALIDATE_EMAIL))
										{
  											$this->addError("Please enter a valid {$item}");
										}
									break;

									case 'username':
										if (!preg_match('/^[a-zA-Z0-9]+(?:_[a-zA-Z0-9]+)?$/', $value))
										{
  											$this->addError("Spaces and characters are not accepted in {$item}");
										}
									break;
									
								}
								
							break;

							case 'unique':
								$check = $this->_db->get('user', array($item, '=', $value));
								if ($check->count()) {
									$this->addError("{$item} has been taken");
								}
							break;
						}
					}
				}								
			}
			if (empty($this->_errors))
			{
				$this->_passed = true;
			}
			return $this;
		}

		private function addError($error)
		{
			$this->_errors[] = $error;
			return $this;
		}

		public function errors()
		{
			return $this->_errors;
		}

		public function passed()
		{
			return $this->_passed;
		}
	}