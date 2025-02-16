<?php

namespace Grocy\Services;

use \Grocy\Services\ApplicationService;

class PDOWrap
{
	private $instance = null;
	public function __construct(){
		$pars = func_get_args();
		$this->instance = is_object($obj = 'PDO') ? $obj : new \PDO($pars[0], $pars[1], $pars[2], array(\PDO::ATTR_PERSISTENT => true, \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
		return $this;
	}

	public function __call($name, $pars) {
		$result = null;

		$fp = fopen('/usr/local/var/www/grocy/sql.log', 'a');
		fwrite($fp, "PDO::".$name." called with arguments:- ".implode( ", ", $pars)."\n");
		$time_start = microtime(true);
		if(in_array($name, array("exec","query")))
		{
			// if($name != 'exec') {

				$pos = strpos(implode( ", ", $pars), 'product_group_id');
				if($pos !== false) {
					$wantedKeys = array('function', 'line', 'file', 'class');
					$i = 0;
					$debug = debug_backtrace();
					while(array_key_exists($i, $debug)) {
						$a = array_intersect_key(debug_backtrace()[$i], array_flip($wantedKeys));
						print_r($a);
						$i++;
					}
					die();
				}

				// fwrite($fp, array_values($pars)[0] . "\n");
				$result = call_user_func_array([$this->instance,$name],$pars);
			// }
		} else {
			$result = call_user_func_array([$this->instance,$name],$pars);
		}
		#fwrite($fp, "Total execution time in seconds: " . round((microtime(true) - $time_start),6) . "\n");
		#fclose($fp);
		return $result;
	}
}

class DatabaseService
{
	private static $instance = null;

	public static function getInstance()
	{
		if (self::$instance == null)
		{
			self::$instance = new self();
		}

		return self::$instance;
	}

	#private static function __construct()
	#{
	#	parent::__construct();
	#}


	private function GetDbFilePath()
	{
		if (GROCY_MODE === 'demo' || GROCY_MODE === 'prerelease')
		{
			return GROCY_DATAPATH . '/grocy_' . GROCY_CULTURE . '.db';
		}

		return GROCY_DATAPATH . '/grocy.db';
	}

	#private $DbConnectionRaw;
    private static $DbConnectionRaw = null;
	/**
	 * @return \PDO
	 */
	public function GetDbConnectionRaw()
	{
		if (self::$DbConnectionRaw == null)
		#if ($this->DbConnectionRaw == null)
		{
			#$fp = fopen('/www/data/sql.log', 'a');
			#fwrite($fp, "+++Creating new PDO object\n");
			#$time_start = microtime(true);
			$pdo = new PDOWrap("mysql:host=127.0.0.1;dbname=grocy", 'grocy', 'grocy');
			$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			self::$DbConnectionRaw = $pdo;
			#$this->DbConnectionRaw = $pdo;
			#fwrite($fp, "+++Total execution time in seconds: " . round((microtime(true) - $time_start),6) . "\n");
			#fwrite($fp, "+++object created\n");
			#fclose($fp);
		}

		return self::$DbConnectionRaw;
		#return $this->DbConnectionRaw;
	}

	#private $DbConnection;
	private static $DbConnection = null;
	/**
	 * @return \LessQL\Database
	 */
	public function GetDbConnection()
	{
		if (self::$DbConnection == null)
		#if ($this->DbConnection == null)
		{
			#$fp = fopen('/www/data/sql.log', 'a');
			#fwrite($fp, "---creating new LessQL::Database object\n");
			#$time_start = microtime(true);
			self::$DbConnection = new \LessQL\Database($this->GetDbConnectionRaw());
			#$this->DbConnection = new \LessQL\Database($this->GetDbConnectionRaw());
			#fwrite($fp, "---Total execution time in seconds: " . round((microtime(true) - $time_start),6) . "\n");
			#fwrite($fp, "---object created\n");
			#fclose($fp);
		}

		return self::$DbConnection;
		#return $this->DbConnection;
	}

	/**
	 * @return boolean
	 */
	public function ExecuteDbStatement(string $sql)
	{
		$pdo = $this->GetDbConnectionRaw();
		if ($pdo->exec($sql) === false)
		{
			throw new Exception($pdo->errorInfo());
		}

		return true;
	}

	/**
	 * @return boolean|\PDOStatement
	 */
	public function ExecuteDbQuery(string $sql)
	{
		$pdo = $this->GetDbConnectionRaw();
		return $pdo->query($sql);
	}

	public function GetDbChangedTime()
	{
		return date('Y-m-d H:i:s', filemtime($this->GetDbFilePath()));
	}

	public function SetDbChangedTime($dateTime)
	{
		// touch($this->GetDbFilePath(), strtotime($dateTime));
	}
}