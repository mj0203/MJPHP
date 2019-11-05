<?php
/**
 *	@desc	程序开始前初始化代码
 *	@author	MJ
 */
namespace Core;

class Init {
	private static $count = 0;
	private function __construct(){}
	
	static public function start(){
		if(!self::$count){
			$instance = new self();
			$ref = new \ReflectionClass($instance);
			if(!empty($ref->getMethods())){
				foreach($ref->getMethods() as $v){
					$classMethodName = $v->name;
					if($classMethodName && strpos($classMethodName, '__') !== false && $classMethodName != __FUNCTION__){
						$instance->$classMethodName();
					}
				}
			}
		}
		self::$count++;
	}
	static public function __todo1(){
		//todo1
	}
	static public function __todo2(){
		//todo2
	}
}