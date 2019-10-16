<?php
namespace Core;

use Application\Librarys\Commons\Fmessage;
use Application\Librarys\Commons\Fexception;
class BaseController {
	protected $layout_header = 'Layout.header';
	protected $layout_footer = 'Layout.footer';
	
	private $view_data = array();
	
	public function __construct(){
	
	}
	/**
	 * 渲染view
	 *
	 * @param string $path        	
	 * @param array $data        	
	 */
	public function view($path = '', array $data = array()){
		$path = trim($path);
		if($path){
			$view_file = VIEW_PATH . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $path) . '.phtml';
			if(is_file($view_file)){
				$data = array_merge($data, $this->view_data);
				if(!empty($data)){
					foreach($data as $k => $v){
						$$k = $v;
					}
					unset($v);
				}
				// 头布局
				if($this->layout_header && !isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
					$header_file = VIEW_PATH . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $this->layout_header) . '.phtml';
					if(is_file($header_file)){
						include_once $header_file;
					}else{
						throw new Fexception(Fmessage::LAYOUT_HEADER_NOT_FOUND);
					}
				}
				
				// App_Main模板
				include_once $view_file;
				
				// 尾布局
				if($this->layout_footer && !isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
					$footer_file = VIEW_PATH . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $this->layout_footer) . '.phtml';
					if(is_file($footer_file))
						include_once $footer_file;
					else
						throw new Fexception(Fmessage::LAYOUT_FOOTER_NOT_FOUND);
				}
			}
		}
	}
	public function setViewData($key, $val){
		$this->view_data[$key] = $val;
	}
	/**
	 * 获取参数
	 *
	 * @param string $key        	
	 * @param string $val        	
	 * @return string
	 */
	public function getParam($key = '', $val = ''){
		if(func_num_args() == 0)
			$val = $_REQUEST;
		else 
			if($key && !empty($_REQUEST))
				$val = isset($_REQUEST[$key]) ? $_REQUEST[$key] : $val;
		
		return $val;
	}
	
	protected function success($data = ''){
		if(empty($data))
			$data = array();
		
		$value['code'] = Fmessage::SUCCESS;
		$value['message'] = Fmessage::getMessage(Fmessage::SUCCESS);
		$value['status'] = 'OK';
		$value['data'] = $data;
		$this->responseJson($value);
	}
	protected function error($code = Fmessage::SYSTEM_ERROR, $message = '', $data = array()){
		$message = $message ?: (Fmessage::getMessage($code) ?: $message);
		$this->responseJson(['code' => $code, 'status' => 'ERROR', 'message' => $message, 'data' => $data]);
	}
	private function responseJson($value){
		header('Content-type: application/json');
		if(is_array($value))
			$value = json_encode($value);
		
		die("{$value}");
	}
}