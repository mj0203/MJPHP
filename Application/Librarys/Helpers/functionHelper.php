<?php
/**
 * @desc		configs配置文件读取
 */
if(!function_exists('config')){
	function config($key, $default=''){
		global $function_config_configs;
		$keyArr = array_filter(explode('.', $key));
		$configFileName = array_shift($keyArr);
		if(!isset($function_config_configs[$configFileName])){
			$function_config_configs[$configFileName] = require_once CONFIG_PATH . DIRECTORY_SEPARATOR . $configFileName . '.php';
		}
		$result = '';
		if(!empty($function_config_configs[$configFileName])){
			$result = $function_config_configs[$configFileName];
			foreach ($keyArr as $key){
				$result = isset($result[$key]) ? $result[$key] : '';
			}
		}
		return $result;
	}
}
/**
 * @desc	配置文件读取
 * @example	env('config_key', 'default')
 */
if(!function_exists('env')){
	function env($key, $default=null){
		$tmp_config = '';
		global $function_env_configs;
		
		if(empty($function_env_configs)){
			$env_file = ROOT_PATH . DIRECTORY_SEPARATOR . '.env';
			if(is_file($env_file))
				$function_env_configs = json_decode(file_get_contents($env_file), true);
		}
		$keys = array_filter(explode('.', $key));
		if(!empty($keys) && !empty($function_env_configs)){
			$tmp_config = $function_env_configs;
			foreach ($keys as $k){
				if(isset($tmp_config[$k])){
					$tmp_config = $tmp_config[$k];
				}else{
					$tmp_config = '';
					break;
				}
			}
		}
		return $tmp_config ? $tmp_config : ($default ? $default : '');
	}
}
/**
 * @desc	load class common
 * @example	Lib_common_load('className', $p1, $p2, $p3)		//实例化最多允许传递三个参数
 */
if ( ! function_exists('Lib_common_load'))
{
	function Lib_common_load($class){
		if(!trim($class)) return false;

		$libs_common_path = COMMON_PATH . DIRECTORY_SEPARATOR;
		$file_name = $class . '.class.php';

		if(is_file($libs_common_path . $file_name)){
			include_once $libs_common_path . $file_name;
				
			if(class_exists($class)){
				if(func_num_args()==1){
					return new $class();
				}elseif(func_num_args()==2){
					return new $class( func_get_arg(1) );
				}elseif(func_num_args()==3){
					return new $class( func_get_arg(1), func_get_arg(2) );
				}elseif(func_num_args()==4){
					return new $class( func_get_arg(1), func_get_arg(2), func_get_arg(3) );
				}
			}else{
				throw new Exception($class . ' class not exists!');
			}
		}
		throw new Exception($file_name . ' file not exists!');
	}
}
if(!function_exists('Lib_init_token')){
    function Lib_init_token($token=''){
        $token = trim($token) ? $token : strtoupper(md5(uniqid() . time()));
        setcookie('token', $token, time()+86400*365, '/');
        return $token;
    }
}