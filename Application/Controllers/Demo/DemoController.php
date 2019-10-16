<?php
namespace Application\Controllers\Demo;
use Core\BaseController;
use Application\Models\UserModel;
class DemoController extends BaseController {
	
	function indexAction(){
	    $this->view('demo.index');
	}
	function getUserInfoAction(){
	    return $this->success(UserModel::get());
	}
}