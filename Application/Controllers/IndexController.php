<?php

namespace Application\Controllers;
use Core\BaseController;

class IndexController extends BaseController {
	public function indexAction(){
		
		return $this->view('index');
	}
}