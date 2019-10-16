<?php
namespace Application\Models;

use Core\BaseModel;
class UserModel extends BaseModel {
	static public $timestamps = false;
	static public $table = 'Users';
	static public $fillable = array('nickname');
	
	static function get(){
	    return [
	        'nickname' => 'MJ'
	    ];
	}
}