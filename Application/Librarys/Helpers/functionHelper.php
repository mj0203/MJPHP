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
if(!function_exists('Lib_init_token')){
    function Lib_init_token($token=''){
        $token = trim($token) ? $token : strtoupper(md5(uniqid() . time()));
        setcookie('token', $token, time()+86400*365, '/');
        return $token;
    }
}