<?php namespace Core\DB;
use Application\Librarys\Commons\Fexception;
use Application\Librarys\Commons\Fmessage;
use Application\Librarys\Commons\Logger;

class FDB {
	private $model = null;
	private $connection = 'master';
	
	private $table = '';
	private $fields = '';
	private $fillable = [];
	private $timestamps = true;
	
	private $bindParams = [];
	
	private $where = '';
	private $sets = '';
	private $insertFields = '';
	private $insertVals = '';
	
	private $orderBy = '';
	private $groupBy = '';
	
	private $page = null;
	private $limit = null;
	
	private static $enableSqlLog = false;
	private static $sqlLogs = [];
	/**
	 * @author	xu.sun
	 * @desc		开启sql log
	 */
	public static function enableSqlLog(){
		self::$enableSqlLog = true;
	}
	/**
	 * @author	xu.sun
	 * @desc		获取sql log
	 */
	public static function getSqlLog(){
		return self::$sqlLogs;
	}
	private function setSqlLog($sql){
		$data = ['connection' => $this->connection, 'sql' => $sql, 'bindParams' => $this->bindParams];
		if(self::$enableSqlLog==true){
			array_push(self::$sqlLogs, $data);
		}
		/* 记录日志 */
		$messageByMethodName = __METHOD__;
		$debugLinks = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		$preObj = $debugLinks[0+1] ?? [];
		if($preObj){
			$messageByMethodName = ($preObj['class'] ?? "") . ($preObj['type'] ?? ".") . ($preObj['function'] ?? ""); 
		}
		Logger::debug($messageByMethodName . ' sqlLog ', $data);
		
		return true;
	}
	/**
	 * @author	xu.sun
	 * @desc		初始化配置
	 * @param unknown $calledModelClass
	 */
	public function initConfig($calledModelClass){
		$this->model = $calledModelClass;
		#table必须设置
		if(!isset($calledModelClass::$table) || !$calledModelClass::$table){
			throw new Fexception(Fmessage::FDB_TABLE_NAME_EMPTY);
		}
		$this->table = $calledModelClass::$table;
		if(isset($this->model::$connection)){
			$this->connection = $this->model::$connection;
		}
		#可过滤字段列表
		if(isset($this->model::$fillable)){
			$this->fillable = (array) $this->model::$fillable;
		}
		if(isset($this->model::$timestamps)){
			$this->timestamps = $this->model::$timestamps;
		}
	}
	public function table($tableName){
		$this->table = $tableName;
		return $this;
	}
	public function connection($connectionName="master"){
		$this->connection = $connectionName;
		return $this;
	}
	/**
	 * @author	xu.sun
	 * @desc	查询字段
	 * @param string $fields
	 * @return \Core\DB\FDB
	 */
	public function select(){
		$args = func_get_args();
		if(empty($args)){
			throw new Fexception(Fmessage::FDB_PARAM_EMPTY);
		}
			
		$fields = [];
		foreach ($args as $f){
			if(is_array($f)){
				$fields = array_merge($fields, $f);
			}else{
				array_push($fields, $f);
			}
		}
		
		//$fields = '`' .implode('`,`', $fields) .'`';
		$fields = implode(',', $fields);
		$this->fields = $this->fields ? $this->fields .','. $fields : $fields;
		return $this;
	}
	public function groupBy($fields){
		if(is_array($fields)){
			$this->groupBy = implode(',', $fields);
		}else{
			$this->groupBy = $fields;
		}
		return $this;
	}
	public function orderBy($fields, $sort='DESC'){
		$orderBy = [];
		if(is_array($fields)){
			$orderBy = array_merge($orderBy, $fields);
		}else{
			$orderBy = [$fields => $sort];
		}
		foreach ($orderBy as $f => $s){
			$this->orderBy .= $this->orderBy ? ',' .$f .' '. $s : $f .' '. $s;
		}
		return $this;
	}
	public function limit($start, $size=''){
		$this->limit = $size ? "$start, $size" : $start;
		return $this;
	}
	/**
	 * @author	xu.sun
	 * @desc	sql条件
	 */
	public function where($p1, $p2='', $p3=''){
		$args_number = func_num_args();
		if(empty($p1)){
			throw new Fexception(Fmessage::FDB_PARAM_EMPTY);
		}
		if( $args_number==1 && is_array($p1) ){
			foreach ($p1 as $k => $v){
				if($this->where){
					$this->where .= ' AND ' . $k . ' = ?';
				}else{
					$this->where = $k . ' = ?';
				}
				array_push($this->bindParams, $v);
			}
		}elseif( $args_number==2 && is_string($p1) && is_array($p2) ){
			if($this->where){
				$this->where .= ' AND ' . $p1;
			}else{
				$this->where = $p1;
			}
			if(!empty($p2)){
				$this->bindParams = array_merge($this->bindParams, $p2);
			}
		}elseif($args_number==3 && is_string($p1) && is_string($p2)){
			if($this->where){
				$this->where .= ' AND ' . $p1 .' '. $p2 . ' ? ';
			}else{
				$this->where = $p1 .' '. $p2 . ' ? ';
			}
			array_push($this->bindParams, $p3);
		}else{
			throw new Fexception(Fmessage::FDB_PARAM_ERROR);
		}
		return $this;
	}
	public function whereIn($field, array $val){
		$beforeParams = '';
		foreach ($val as $v){
			$beforeParams .= '?,';
			array_push($this->bindParams, $v);
		}
		$beforeParams = rtrim($beforeParams, ',');
		$this->where .= ($this->where ? " AND " : '') . "$field IN ($beforeParams)";
		return $this;
	}
	/**
	 * @author	xu.sun
	 * @desc	插入数据
	 * @alias	create save
	 * @param array $data	可单条 可多条
	 * @throws AtsException
	 * @return number
	 */
	public function insert($data){
		if(empty($data)){
			throw new Fexception(Fmessage::FDB_PARAM_EMPTY);
		}
			
		$first = current($data);
		if(!is_array($first)){
			$data = [ $data ];
		}
		$cdate = date('Y-m-d H:i:s');
		foreach ($data as $val){
			#自动管理时间
			if($this->timestamps){
				if(!isset($val['created_at'])){
					$val['created_at'] = $cdate;
				}
				if(!isset($val['updated_at'])){
					$val['updated_at'] = $cdate;
				}
			}
			#过滤
			if($this->fillable){
				$val = array_intersect_key($val, array_flip((array) $this->fillable));
				if(empty($val)){
					throw new Fexception(Fmessage::FDB_PARAMS_FILTERED_EMPTY);
				}
			}
			if(!$this->insertFields){
				$this->insertFields = '(`' . implode('`,`', array_keys($val)) . '`)';
			}
				
			$this->insertVals .= '(';
			$this->insertVals .= str_pad('', count($val)*2, '?,', STR_PAD_RIGHT);
			$this->insertVals = rtrim($this->insertVals, ',') . '),';
			$this->bindParams = array_merge($this->bindParams, array_values($val));
		}
		$this->insertVals = rtrim($this->insertVals, ',');
		$sql = 'INSERT INTO ' . $this->table . $this->insertFields . ' VALUES' . $this->insertVals;
		
		$this->setSqlLog($sql);
		
		$fpdo = FPDO::getInstance($this->connection);
		$std = $fpdo->pdo->prepare($sql);
		$std->execute($this->bindParams);
		return $fpdo->pdo->lastInsertId();
	}
	/**
	 * @author	xu.sun
	 * @desc	编辑数据
	 * @alias	update
	 * @param array $sets
	 * @param array $bindParams
	 */
	public function modify($sets, $bindParams=array()){
		if(empty($sets)){
			throw new Fexception(Fmessage::FDB_PARAM_EMPTY);
		}
		if(is_array($sets)){
			$bindParams = array_values($sets);
			foreach ($sets as $k => $v){
				if($this->sets){
					$this->sets .= ', ' . $k . ' = ?';
				}else{
					$this->sets .= $k . ' = ?';
				}
			}
		}else{
			$this->sets .= $sets;
		}
		$this->bindParams = array_merge($bindParams, $this->bindParams);

		$sql = 'UPDATE ' . $this->table . ' SET ' . $this->sets . ' WHERE ' . $this->getWhere();
		
		$this->setSqlLog($sql);
		
		$fpdo = FPDO::getInstance($this->connection);
		$std = $fpdo->pdo->prepare($sql);
		$std->execute($this->bindParams);
		return $std->rowCount();
	}
	/**
	 * @author	xu.sun
	 * @desc	获取一条数据
	 * @alias	getOne
	 * @param string $fields
	 * @return mixed
	 */
	public function first(){
		$fields = $this->getFields();
		$sql = 'SELECT ' . $fields . ' FROM ' . $this->table . ' WHERE ' . $this->getWhere();
		if($this->groupBy){
			$sql .= ' GROUP BY ' . $this->groupBy;
		}
		if($this->orderBy){
			$sql .= ' ORDER BY ' .$this->orderBy;
		}
		$sql .= ' LIMIT 1';
		
		$this->setSqlLog($sql);
		
		$fpdo = FPDO::getInstance($this->connection);
		$std = $fpdo->pdo->prepare($sql);
		$std->execute($this->bindParams);
		return $std->fetch() ? : [];
	}
	/**
	 * @author	xu.sun
	 * @desc	统计数量
	 * @param string $fields
	 * @return mixed
	 */
	public function count(){
		$sql = 'SELECT COUNT(1) count FROM ' . $this->table . ' WHERE ' . $this->getWhere();
		if($this->groupBy){
			$sql .= ' GROUP BY ' . $this->groupBy;
		}
		
		$this->setSqlLog($sql);
		
		$fpdo = FPDO::getInstance($this->connection);
		$std = $fpdo->pdo->prepare($sql);
		$std->execute($this->bindParams);
		$res = $std->fetch();
		return isset($res['count']) ? $res['count'] : 0;
	}
	/**
	 * @author	xu.sun
	 * @desc	获取所有数据
	 * @param string $fields
	 * @return array
	 */
	public function getAll(){
		$fields = $this->getFields();
		$sql = 'SELECT ' . $fields . ' FROM ' . $this->table . ' WHERE ' . $this->getWhere();
		if($this->groupBy){
			$sql .= ' GROUP BY ' . $this->groupBy;
		}
		if($this->orderBy){
			$sql .= ' ORDER BY ' .$this->orderBy;
		}
		if($this->limit){
			$sql .= ' LIMIT ' .$this->limit;
		}
		
		$this->setSqlLog($sql);
		
		$fpdo = FPDO::getInstance($this->connection);
		$std = $fpdo->pdo->prepare($sql);
		$std->execute($this->bindParams);
		return $std->fetchAll();
	}
	/**
	 * @author	xu.sun
	 * @desc	执行原始sql语句
	 * @param string $sql	sql
	 * @param array $bindParams	参数
	 */
	public function query($sql, $bindParams=array()){
		$sql = trim($sql);
		$this->bindParams = $bindParams;
		
		$this->setSqlLog($sql);
		
		$fpdo = FPDO::getInstance($this->connection);
		$std = $fpdo->pdo->prepare($sql);
		$std->execute($this->bindParams);
		
		if(in_array(strtoupper(substr($sql, 0, 6)), ['INSERT','UPDATE','DELETE'])){
			$res = $std->rowCount();
		}else{
			$res = $std->fetchAll();
		}
		return $res;
	}
	private function getFields(){
		return $this->fields ? : '*';
	}
	private function getWhere(){
		return $this->where ? : ' 1=1 ';
	}
	/* alias */
	public function create($data){
		return $this->insert($data);
	}
	public function save($data){
		return $this->insert($data);
	}
	public function getOne(){
		return $this->first();
	}
	public function get(){
		return $this->first();
	}
	public function gets(){
		return $this->getAll();
	}
	public function update($sets, $bindParams=array()){
		return $this->modify($sets, $bindParams);
	}
	#开启事务
	public function begin(){
		$this->setSqlLog("begin");
		return FPDO::getInstance($this->connection)->pdo->beginTransaction();
	}
	#提交事务
	public function commit(){
		$this->setSqlLog("commit");
		return FPDO::getInstance($this->connection)->pdo->commit();
	}
	#回滚事务
	public function rollback(){
		$this->setSqlLog("rollback");
		return FPDO::getInstance($this->connection)->pdo->rollBack();
	}
}