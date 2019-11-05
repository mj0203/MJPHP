<?php
namespace Application\Controllers\Demo;
use Core\BaseController;
use Application\Models\UserModel;
class DemoController extends BaseController {
	
	function indexAction(){
		echo '作者: ' . config('app.author.name');echo "<br>";
		echo '版本: ' . config('app.version') . "<br>";
		echo '<pre>';
		print_r(config('app.websites'));echo "<br>";
		
		echo 'APP_ENV: ' . env('APP_ENV') . '<br>';
		
	    $this->view('demo.index');
	}
	function getUserInfoAction(){
	    return $this->success(UserModel::get());
	}
}