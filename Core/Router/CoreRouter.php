<?php

namespace Core\Router;

class CoreRouter {
	static public function parse(){
		$params = array();
		$isRewrite = env('REWRITE', false);
		$reqUri = explode('?', self::realRoute());
		if($isRewrite){
			$params = array_filter(explode('/', $reqUri[0]));
			$m = current($params);
			if($m && is_dir(CONTROLLER_PATH . DIRECTORY_SEPARATOR . ucfirst($m))){
				array_shift($params);
			}else{
				$m = '';
			}
			$c = current($params) ?: \Core_Bootstrap::getController();
			array_shift($params);
			$a = current($params) ?: \Core_Bootstrap::getAction();
			array_shift($params);
			
			\Core_Bootstrap::setUriParams($params);
		}else{
			$m = isset($_REQUEST['m']) ? $_REQUEST['m'] : \Core_Bootstrap::getModule();
			$c = isset($_REQUEST['c']) ? $_REQUEST['c'] : \Core_Bootstrap::getController();
			$a = isset($_REQUEST['a']) ? $_REQUEST['a'] : \Core_Bootstrap::getAction();
		}
		\Core_Bootstrap::setModule($m);
		\Core_Bootstrap::setController($c);
		\Core_Bootstrap::setAction($a);
		
		return true;
	}
	/**
	 * 短路由检测
	 * 
	 * @return string
	 */
	static private function realRoute(){
		$realUriPath = '';
		$reqURI = explode('?', $_SERVER['REQUEST_URI']);
		$uriPath = array_shift($reqURI);
		$routes = config('route');
		if(isset($routes[$uriPath]) && $routes[$uriPath]){
			$realUriPath = $routes[$uriPath];
			if($reqURI){
				$realUriPath .= '?' . array_shift($reqURI);
			}
			$_SERVER['REAL_REQUEST_URI'] = $realUriPath;
		}
		return $realUriPath ?: $_SERVER['REQUEST_URI'];
	}
}