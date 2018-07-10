<?php
namespace Jacky;
class Loader{
	//类名映射
	protected static $map=[];
	//命名空间
	protected static $namespaces=[];
	public static  function register(){
		spl_autoload_register("Jacky\Loader::autoload",true,true);
		self::addNamespace('Jacky',__DIR__.'/');
	}
	//注册命名空间
	public static function addNamespace($name,$path){
		   self::$namespaces[$name]=rtrim($path,'/').DIRECTORY_SEPARATOR;
	}
	//类名映射
	public static function addMap($class,$map=''){
		    self::$map[$class]=$map;
	}
	public static function autoload($class){
		   if($file=self::find($class)){
		   	include $file;
		   	return true;
		   }
	}
	//查找文件并保存映射
	public static function find($class){
		   if(isset(self::$map[$class])){
		   	  return self::$map[$class];
		   }
		   
		   $classes=array_filter(explode("\\",$class));
		   $namespace=array_shift($classes);
		   $file=join(DIRECTORY_SEPARATOR,$classes).'.php';
		   if(isset(self::$namespaces[$namespace])){
		   	    $dir=self::$namespaces[$namespace];
		   	    if(is_file($path=$dir.$file)){
		   	    	self::$map[$class]=$path;
		   	    	return $path;
		   	    }
		   }
		   
		   return false;
	}
}