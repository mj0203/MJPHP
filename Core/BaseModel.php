<?php
namespace Core;
use Core\DB\FDB;

class BaseModel {
	public static $timestamps = true;
	
	public function __call($name, $arguments){
		return self::start($name, $arguments);
	}
	
	static public function __callstatic($name, $arguments){
		return self::start($name, $arguments);
	}
	static private function start($name = null, $arguments = null){
		$instance = new FDB();
		$instance->initConfig(static::class);
		
		return call_user_func_array(array($instance, $name), $arguments);
	}
}