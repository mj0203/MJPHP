<?php namespace Application\Librarys\Commons;
class Logger {
	private function __construct(){}
	
	static private $logFile = null;
	static private $logPath = null;
	static private $resource = null;
	static private $unique_id = '';
	static private $method = null;
	static private $uri = '';
	static private $referer = '';
	
	static function getFile(){
		return self::$logFile;
	}
	static private function openFile(){
		if( is_null(self::$resource) ){
			$logs = env('LOGS');
			if(!isset($logs['path']) || empty($log['path'])){
				self::$logPath = ROOT_PATH . DIRECTORY_SEPARATOR . 'logs';
			}
			if(!is_dir(self::$logPath) && !mkdir(self::$logPath, 0755, true)){
				throw new \Exception('创建日志目录失败');
			}
			self::$logFile = self::$logPath . DIRECTORY_SEPARATOR . (isset($logs['prefix']) ? $logs['prefix'] : '') . (isset($logs['split']) ? date($logs['split']) : 'log') . '.log';

			self::$resource = fopen(self::$logFile, 'a');
			if(!self::$resource){
				throw new \Exception('打开文件资源失败');
			}
			self::$unique_id = getmypid().'_'.uniqid();
			
			if(isset($_SERVER['REQUEST_METHOD']))
			    self::$method = $_SERVER['REQUEST_METHOD'];
			if(isset($_SERVER['REQUEST_URI']))
			    self::$uri = $_SERVER['REQUEST_URI'];
			if(isset($_SERVER['HTTP_REFERER']))
				self::$referer = $_SERVER['HTTP_REFERER'];
		}
		return self::$resource;
	}
	static function debug($message, $data=array()){
		return self::log('DEBUG', $message, $data);
	}
	static function info($message, $data=array()){
		return self::log('INFO', $message, $data);
	}
	static function warning($message, $data=array()){
		return self::log('warning', $message, $data);
	}
	static function error($message, $data=array()){
		self::log('ERROR', $message, $data);
	}
	static private function log($level, $message, $data=array()){
	    self::openFile();
	    $format = "Level:{$level} ";
	    $format .= '"'. self::$unique_id . '" ';
	    $format .= '"'. date('Y-m-d H:i:s').' '.microtime(true) .'" ';
	    $format .= '"' .$message .'" ';
		$format .= '"'. self::$method .' '. self::$uri .'" ';
		$format .= '"'. self::$referer . '" ';
		$format .= '"'. (is_array($data) ? json_encode($data) : $data) .'"';
		fwrite(self::$resource, $format."\n");
		return true;
	}
}