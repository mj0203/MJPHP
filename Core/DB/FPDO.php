<?php

namespace Core\DB;
use Application\Librarys\Commons\Fexception;
use Application\Librarys\Commons\Fmessage;

class FPDO {
	private static $connections = [];
	private $connection = null;
	public $pdo = null;
	
	private function __construct(){}
	/**
	 * 获取单例实例
	 *
	 * @author xu.sun
	 * @param string $connection        	
	 * @return mixed
	 */
	static function getInstance($connection = 'master'){
		if(!isset(self::$connections[$connection])){
			$instance = new self();
			$instance->connect($connection);
			self::$connections[$connection] = $instance;
		}
		return self::$connections[$connection];
	}
	private function connect($connection){
		$dbConfig = env('database');
		if(empty($dbConfig))
			throw new Fexception(Fmessage::PARAM_ISEMPTY);
		if(!isset($dbConfig[$connection]))
			throw new Fexception(Fmessage::DB_CONNECTION_IS_NOT_EXIST);
		if(!isset($dbConfig[$connection]['host']) || !isset($dbConfig[$connection]['port']) || !isset($dbConfig[$connection]['dbname']) || !isset($dbConfig[$connection]['username']) || !isset($dbConfig[$connection]['password'])){
			throw new Fexception(Fmessage::DB_CONFIG_LOSE);
		}
		
		$dbConfig[$connection]['dsn'] = "mysql:host={$dbConfig[$connection]['host']};port={$dbConfig[$connection]['port']};dbname={$dbConfig[$connection]['dbname']}";
		$this->pdo = new \PDO($dbConfig[$connection]['dsn'], $dbConfig[$connection]['username'], $dbConfig[$connection]['password']);
		$this->pdo->exec("SET NAMES utf8");
		$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
		
		$this->connection = $connection;
		return true;
	}
	/**
	 * 开启事务
	 *
	 * @author xu.sun
	 * @return boolean
	 */
	function begin(){
		return $this->pdo->beginTransaction();
	}
	/**
	 * 提交事务
	 *
	 * @author xu.sun
	 * @return boolean
	 */
	function commit(){
		return $this->pdo->commit();
	}
	/**
	 * 回滚事务
	 *
	 * @author xu.sun
	 * @return boolean
	 */
	function rollback(){
		return $this->pdo->rollBack();
	}
}