<?php
namespace Jacky;
class Config{
	protected static  $_instance='';
	private static $config=[];
	public function __construct(){
	
	}
	public static function getInstance(){
		if(self::$_instance==''){
			self::$_instance=new self();
		}
		return self::$_instance;
	}
	public function get($keys,$default=null){
		   $keys=array_filter(explode('.',$keys));
		   if(empty($keys))return null;
		   
		   $file=array_shift($keys);
		   if(empty(self::$config[$file])){
		   	   if(!is_file(CONFIG_PATH.$file.'.php')){
		   	   	     return null;
		   	   }
		   	   self::$config[$file]=include CONFIG_PATH.$file.'.php';
		   }
		   $config=self::$config[$file];
		   while ($keys){
		   	     $key=array_shift($keys);
		   	     if(!isset($config[$key])){
		   	     	 $config=$default;
		   	     	 break;
		   	     }
		   	     $config=$config[$key];  
		   }
		   return $config;
	}
}