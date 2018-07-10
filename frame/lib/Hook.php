<?php
namespace Jacky;
class Hook{
	protected static  $_instance='';
	protected static  $config=[];
	public function __construct(){
	
	}
	public static function getInstance(){
		if(self::$_instance==''){
			self::$_instance=new self();
			self::$config=config('hook');
		}
		return self::$_instance;
	}
	public function listen($hook,...$args){
		   $hooks=isset(self::$config[$hook])?self::$config[$hook]:[];
		   while ($hooks){
		   	     list($class,$func)=array_shift($hooks);
		   	     try {
		   	     	$class::getInstance()->$func(...$args);
		   	     } catch (Exception $e) {
		   	     	 logs('ERROR',$e->getMessage());
		   	     }
		   	     
		   }
	}
}