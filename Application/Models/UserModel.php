<?php
namespace Application\Models;

use Core\BaseModel;
class UserModel extends BaseModel {
	public static $timestamps = false;
	public static $table = 'Users';
	public static $fillable = array('nickname');
	
	static function get(){
		return ['nickname' => 'MJ'];
	}
}