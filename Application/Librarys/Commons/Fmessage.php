<?php
namespace Application\Librarys\Commons;

class Fmessage {
	const SUCCESS = 0;
	const SYSTEM_ERROR = 10000;
	const CORE_CLASS_NOT_FOUND = 10001;
	const CORE_ACTION_NOT_FOUND = 10002;
	const CORE_CLASS_IS_EMPTY = 10003;
	const CORE_CLASS_FILE_NOT_EXIST = 10004;
	const PARAM_ISEMPTY = 10005;
	const PARAM_LOSE = 10006;
	const DB_CONNECTION_IS_NOT_EXIST = 10007;
	const DB_CONFIG_LOSE = 10008;
	const DB_PARAM_EMPTY = 10009;
	const DB_PARAM_ERROR = 10010;
	const DB_TABLE_NAME_EMPTY = 10011;
	const DB_PARAMS_FILTERED_EMPTY = 10012;
	const LAYOUT_HEADER_NOT_FOUND = 10013;
	const LAYOUT_FOOTER_NOT_FOUND = 10014;
	
	static private function maps(){
		return array(
			self::CORE_CLASS_NOT_FOUND => 'Class not found', 
			self::CORE_ACTION_NOT_FOUND => 'Action not found', 
			self::CORE_CLASS_IS_EMPTY => 'Class name is empty!', 
			self::CORE_CLASS_FILE_NOT_EXIST => 'load class file not exist!', 
			self::SYSTEM_ERROR => '系统错误', 
			self::SUCCESS => 'success', 
			self::PARAM_ISEMPTY => '参数为空', 
			self::PARAM_LOSE => '缺少参数', 
			self::DB_CONNECTION_IS_NOT_EXIST => 'DB链接节点不存在', 
			self::DB_CONFIG_LOSE => 'DB配置丟失!', 
			self::DB_PARAM_EMPTY => 'DB参数为空', 
			self::DB_PARAM_ERROR => 'DB参数错误', 
			self::DB_TABLE_NAME_EMPTY => 'DB表名为空', 
			self::DB_PARAMS_FILTERED_EMPTY => 'DB参数过滤为空', 
			self::LAYOUT_HEADER_NOT_FOUND => 'layout header not exist!', 
			self::LAYOUT_FOOTER_NOT_FOUND => 'layout footer not exist!'
		);
	}
	
	static public function getMessage($code = null){
		$maps = self::maps();
		return isset($maps[$code]) ? $maps[$code] : '';
	}
}