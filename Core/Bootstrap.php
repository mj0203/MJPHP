<?php
/**
 * @author	MJ
 * @desc		启动文件
 */
use Core\Router;
use Core\Init;
use Application\Librarys\Commons\Fexception;
use Application\Librarys\Commons\Fmessage;
use Application\Librarys\Commons\Logger;

define('CORE_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'Core');
define('CONFIG_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'Configs');
define('APP_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'Application');
define('CONTROLLER_PATH', APP_PATH . DIRECTORY_SEPARATOR . 'Controllers');
define('MODEL_PATH', APP_PATH . DIRECTORY_SEPARATOR . 'Models');
define('VIEW_PATH', APP_PATH . DIRECTORY_SEPARATOR . 'Views');
define('LIB_PATH', APP_PATH . DIRECTORY_SEPARATOR . 'Librarys');
define('HELP_PATH', LIB_PATH . DIRECTORY_SEPARATOR . 'Helpers');
define('COMMON_PATH', LIB_PATH . DIRECTORY_SEPARATOR . 'Commons');
define('PLUGIN_PATH', LIB_PATH . DIRECTORY_SEPARATOR . 'Plugins');

define('PUBLIC_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'public');

spl_autoload_register(array('Core_Bootstrap', 'loadClassLoader'), true, true);

class Core_Bootstrap {
	
	private static $Module = '';
	private static $Controller = 'index';
	private static $Action = 'index';
	private static $UiDebug = true;
	
	private static $UriParams = [];
	
	static public function setUriParams($params){
		self::$UriParams = $params;
	}
	static public function getUriParams(){
		return self::$UriParams;
	}
	
	static public function getModule(){
		return self::$Module;
	}
	static public function getController(){
		return self::$Controller;
	}
	static public function getAction(){
		return self::$Action;
	}
	static public function setModule($module){
		self::$Module = $module;
	}
	static public function setController($controller){
		self::$Controller = $controller;
	}
	static public function setAction($action){
		self::$Action = $action;
	}
	
	static public function init(){
		Router::parse();
		$module = ucfirst(self::getModule());
		$controller = ucfirst(self::getController()) . 'Controller';
		$action = self::getAction() . 'Action';
		$namespace = 'Application\\Controllers\\' . ($module ? ($module . '\\' . $controller) : $controller);
		
		Init::start();
		
		if(class_exists($namespace)){
			$class_obj = new $namespace();
			if(method_exists($class_obj, $action)){
				call_user_func_array(array($class_obj, $action), self::getUriParams());
			}else{
				throw new Fexception(Fmessage::CORE_ACTION_NOT_FOUND);
			}
		}else{
			throw new Fexception(Fmessage::CORE_CLASS_NOT_FOUND);
		}
	}
	
	static private function loadHelps(){
		if(is_dir(HELP_PATH)){
			foreach(glob(HELP_PATH . DIRECTORY_SEPARATOR . '*Helper.php') as $file){
				if(is_file($file))
					include_once $file;
			}
		}
	}
	static public function run(){
		self::loadHelps();
		
		$contentLength = isset($_SERVER['CONTENT_LENGTH']) ? $_SERVER['CONTENT_LENGTH'] : 0;
		Logger::debug("BootstrapRun", ['POST' => $contentLength > 10000 ? 'TooMaxContentLength! {' . $contentLength . '}' : $_POST]);
		
		env('APP_ENV', 'product') == 'product' ? ini_set('display_errors', 'off') : ini_set('display_errors', 'on');
		
		self::init();
		
		$php_sapi_name = php_sapi_name();
		if($php_sapi_name == 'fpm-fcgi'){
			if(session_status() != PHP_SESSION_ACTIVE)
				session_start();
			// 生成唯一token
			if(!array_key_exists('token', $_COOKIE))
				Lib_init_token();
		}
		
		return true;
	}
	
	static public function loadClassLoader($class_name){
		$class_name = trim($class_name);
		if(!$class_name){
			throw new Fexception(Fmessage::CORE_CLASS_IS_EMPTY);
		}
		
		$file = ROOT_PATH . DIRECTORY_SEPARATOR . str_replace(' ', DIRECTORY_SEPARATOR, ucwords(str_replace('\\', ' ', $class_name))) . '.php';
		
		if(is_file($file)){
			include_once $file;
		}else{
			if(self::$UiDebug === true){
				die("<p><span style='font-weight:bold;'>file: </span><span style='font-weight:bold;color:#DDD'>$file</span> is not found!</p>");
			}else{
				throw new Fexception(Fmessage::CORE_CLASS_FILE_NOT_EXIST);
			}
		}
	}
}

$file = __DIR__ . '/../vendor/autoload.php';
if(is_file($file))
	require_once $file;