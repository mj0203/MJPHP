<?php
namespace Application\Librarys\Commons;

class Fexception extends \Exception {
	public function __construct($code, array $data=[]){
		$this->code = $code;
		$this->message = Fmessage::getMessage($code);
		
		$info = [
				'code'			=> $this->code,
				'message'		=> $this->message,
				'file'			=> $this->getFile() . ':' . $this->getLine()
		];
		
		$data ? $info['data'] = $data : '';
		
		Logger::error('Fexception ' .$this->message, $info);
	}
}