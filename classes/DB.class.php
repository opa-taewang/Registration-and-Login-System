<?php 
	//Database class
	class DB
	{
		private static $_instance = null;
		private $_pdo,
				$_query,
				$_error = false,
				$_results,
				$_count = 0;

		// Instantiate connection
		private function __construct()
		{
			try {
				$this->_pdo = new PDO('mysql:host=' . Config::get('mysql/dbHost') . ';dbname=' . Config::get('mysql/dbName'),Config::get('mysql/dbUser'),Config::get('mysql/dbPass'));
				$this->_pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
			} catch (PDOException $e) {
				die($e->getMessage());
			}
		}

		//Make sure connection is set
		public static function getInstance()
		{
			if(!isset(self::$_instance)){
				self::$_instance = new DB;
			}
			return self::$_instance;
		}

		//Querying Database
		public function query($sql, $params = array())
		{
			$this->_error = false;
			if ($this->_query = $this->_pdo->prepare($sql))
			{
				$x = 1;
				if (count($params))
				{
					foreach ($params as $param)
					{
						$this->_query->bindValue($x, $param);
						$x++;
					}
				}

				if ($this->_query->execute())
				{
					$this->_results = $this->_query->fetchAll();
					$this->_count = $this->_query->rowCount();
				}else
				{
					$this->_error = true;
				}
			}
			return $this;	
		}

		//Performing action on database
		public function action($action, $table, $where = array())
		{
			if(count($where) === 3)
			{
				$operators = array('=','<', '>', '<=', '>=');

				$field		= $where[0];
				$operator 	= $where[1];
				$value 		= $where[2];

				if(in_array($operator, $operators))
				{
					$sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";
					if(!$this->query($sql, array($value))->error())
					{
						return $this;
					}
				}
			} 
			return false;
		}

		//Selecting from database
		public function get($table, $where)
		{
			return $this->action('SELECT *', $table, $where);
		}

		//Deleting from database
		public function delete($table, $where)
		{
			return $this->action('DELETE', $table, $where);
		}

		//Insert into database
		public function insert($table, $fields = array())
		{
			if (count($fields))
			{
				$keys = array_keys($fields);
				$values = '';
				$x = 1;
				// To give unknown to each database parameter
				foreach ($fields as $field)
				{
					$values .= '?';
					//To add comma after each parameters
					if ($x < count($fields)) {
						$values .= ',';
					}
					$x++;
				}

				$sql= "INSERT INTO `{$table}` (`". implode("`, `" , $keys) ."`) VALUES ({$values}) ";
				if (!$this->query($sql, $fields)->error()) {
					return true;
				}
			}
			return false;
		}

		//To update database
		public function update($table, $fields = array(), $where = array())
		{

			if(count($where) === 3)
			{
				$operators = array('=','<', '>', '<=', '>=');

				$field		= $where[0];
				$operator 	= $where[1];
				$value 	= $where[2];

				if(in_array($operator, $operators))
				{	
					$set = '';
					$x = 1;

					// To set column name and each parameter
					foreach ($fields as $key => $values)
					{
						$set .= $key . "=" . '?';
						//To add comma after each parameters
						if ($x < count($fields))
						{
							$set .= ', ';
						}
						$x++;
					}
					$sql = "UPDATE {$table} SET {$set} WHERE {$field} {$operator} {$value}";
					if(!$this->query($sql, $fields)->error())
					{
						return true;
					}
					return false;
				}
				return false;
			}
			echo "where more than 3";
			return false;
		}

		// Returning Error
		public function error()
		{
			return $this->_error;
		}

		//Counting
		public function count()
		{
			return $this->_count;
		}

		// Results
		public function result()
		{
			return $this->_results;
		}

		// Return result as an array
		public function first()
		{
			return $this->result()[0];
		}

	}