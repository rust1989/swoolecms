<?php
namespace Jacky;

class Router{
	protected static  $_instance='';
	protected static $config=[];
	public function __construct(){
		
	}
	public static function getInstance(){
		   if(self::$_instance==''){
		   	    self::$_instance=new self();
		   	    self::$config=Config::getInstance()->get('router');
		   }
		   return self::$_instance;
	}
	public function websocket($data){
		   $data=json_decode($data,true);
		   if(empty($data)){
		   	return ['m'=>'','c'=>'','a'=>'','p'=>''];
		   }
		   $path=empty($data['cmd'])?'':trim($data['cmd'],'/');
		   if(empty($path)){
		   	return ['m'=>'','c'=>'','a'=>'','p'=>''];
		   }
		   if(!empty(self::$config['rule'])&&isset(self::$config['rule'][$path])){
		   	  $path=self::$config['rule'][$path];
		   }
		   $params=explode('/',$path);
		   $module=array_shift($params);
		   $controller=array_shift($params);
		   $action=array_shift($params);
		   unset($data['cmd']);
		   return  ['m'=>$module,'c'=>$controller,'a'=>$action,'p'=>$data];
	}
	//http 请求
    public function http($url){
    	   $params=[];
    	  
    	   $module=self::$config['m'];
    	   $controller=self::$config['c'];
    	   $action=self::$config['a'];
    	   if(empty($url)){
    	   	  return ['m'=>$module,'c'=>$controller,'a'=>$action,'p'=>$params];
    	   }
    	   
    	   $path=trim($url,'/');
    	   if(!empty(self::$config['ext'])){
    	   	   $path=rtrim($path,self::$config['ext']);
    	   }
    	   
    	   if(!empty(self::$config['rule'])){
    	   	  foreach (self::$config['url'] as $key=>$value){
    	   	  	   if(substr($path,0,strlen($key))==$key){
    	   	  	   	    $path=str_replace($value,$value,$path);
    	   	  	   	    break;
    	   	  	   }
    	   	  }
    	   }
    	   
    	   $paths=explode('/',$path);
    	   !empty($paths[0])&&$module=$paths[0];
    	   isset($paths[1])&&$controller=$paths[1];
    	   isset($paths[2])&&$action=$paths[2];
    	   
    	   if(count($paths)>=3){
    	   	   $params=array_slice($paths,3);
    	   }else{
    	   	   $params=array_slice($paths,2);
    	   }
    	   return ['m'=>$module,'c'=>$controller,'a'=>$action,'p'=>$params];
    }
}