<?php
namespace Application\Librarys\Commons;

class Fmessage {

	const NOLOGIN							= 80000;
	const SYSTEM_ERROR						= 80001;
	const SAVE_ERROR						= 80002;
	const CORE_CLASS_NOT_FOUND				= 90000;
	const CORE_ACTION_NOT_FOUND				= 90001;
	const CORE_CLASS_IS_EMPTY				= 90002;
	const CORE_CLASS_FILE_NOT_EXIST			= 90003;
	const SUCCESS							= 10000;
	const PARAM_ISEMPTY						= 10001;
	const PARAM_LOSE						= 10002;
	const USER_OR_PASSWORD_ERROR			= 10003;
	const USER_PHONE_IS_EMPTY				= 10004;
	const VCODE_IS_EMPTY					= 10005;
	const USER_LOGIN_ERROR					= 10006;
	const VERIFY_CODE_ERROR					= 10007;
	const USER_NOT_FOUND					= 10008;
	const USER_MODIFY_FAIL					= 10009;
	const VCODE_TIMES_ERROR					= 10010;
	const NEW_INSTANCE_STATIC_ERROR			= 10011;
	const DB_CONNECTION_IS_NOT_EXIST		= 10012;
	const DB_CONFIG_LOSE					= 10013;
	const FDB_PARAM_EMPTY					= 10014;
	const FDB_PARAM_ERROR					= 10015;
	const FDB_TABLE_NAME_EMPTY				= 10016;
	const FDB_PARAMS_FILTERED_EMPTY			= 10017;
	
	
	const ARTICLE_TITLE_LENGTH_TOO_LONG		= 30001;
	const ARTICLE_AUTH_ERROR				= 30002;
	const ARTICLE_SAVE_ERROR				= 30003;
	const ARTICLE_NOT_FOUND					= 30004;
	const ARTICLE_COMMENT_CONTENT_EMPTY		= 30005;
	const ARTICLE_COMMENT_TOOLONG			= 30006;
	const ARTICLE_ID_NOT_FOUND				= 30007;
	const ARTICLE_WANT_ERROR				= 30008;
	
	const UPLOAD_IMAGE_SAVE_ERROR			= 40001;
	const UPLOAD_IMAGE_GET_RESOURCE_NOT_EXIST = 40002;
	const UPLOAD_IMAGE_RESOURCE_EMPTY		= 40003;
	
	
	
	static private function maps(){
		$core = array(
				self::CORE_CLASS_NOT_FOUND	=> 'Class not found',
				self::CORE_ACTION_NOT_FOUND	=> 'Action not found',
				self::CORE_CLASS_IS_EMPTY	=> 'Class name is empty!',
				self::CORE_CLASS_FILE_NOT_EXIST	=> 'load class file not exist!'
		);
		$arr = array(
				self::NOLOGIN				=> '请登录',
				self::SYSTEM_ERROR			=> '系统错误',
				self::SAVE_ERROR			=> '保存失败',
				self::SUCCESS				=> 'success',
				self::PARAM_ISEMPTY			=> '参数为空',
				self::PARAM_LOSE			=> '缺少参数',
				self::USER_OR_PASSWORD_ERROR=> '用户名或密码输入错误',
				self::USER_PHONE_IS_EMPTY	=> '手机号为空',
				self::VCODE_IS_EMPTY		=> '验证码为空',
				self::USER_LOGIN_ERROR		=> '登录失败',
				self::VERIFY_CODE_ERROR		=> '验证码错误',
				self::USER_NOT_FOUND		=> '用户未找到',
				self::USER_MODIFY_FAIL		=> '用户编辑错误',
				self::VCODE_TIMES_ERROR		=> '每分钟可获取一次验证码，请稍后重试',
				self::NEW_INSTANCE_STATIC_ERROR		=> '实例化静态类错误',
				self::DB_CONNECTION_IS_NOT_EXIST	=> 'DB链接节点不存在',
				self::DB_CONFIG_LOSE				=> 'DB配置丟失!',
				self::FDB_PARAM_EMPTY				=> 'FDB参数为空',
				self::FDB_PARAM_ERROR				=> 'FDB参数错误',
				self::FDB_TABLE_NAME_EMPTY			=> 'FDB表名为空',
				self::FDB_PARAMS_FILTERED_EMPTY		=> 'FDB参数过滤为空'
		);
		
		$article = array(
					30001		=> '文章标题太长,最多50个字符',
					30002		=> '文章没权限操作',
					30003		=> '文章保存失败',
					30004		=> '文章不存在',
					30005		=> '评论内容不能为空!',
					30006		=> '评论内容不能超过50个字!',
					30007		=> '文章ID不存在',
					30008		=> '文章喜欢失败'
		);
		
		$upload = array(
					40001		=> '图片上传保存失败',
					40002		=> '获取图片资源不存在',
					40003		=> '图片资源为空'
		);
		
		return $core + $arr + $article + $upload;
	}
	static public function getMessage($code=null){
		if(!empty($code)){
			if(isset(self::maps()[$code]))
				return self::maps()[$code];
		}
		return '';
	}
}