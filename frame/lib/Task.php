<?php 
namespace Jacky;
final class Task{
	protected static  $_instance='';
	private  $server;
	public function __construct(){
	
	}
	
	final public static function getInstance(){
		if(self::$_instance==''){
			self::$_instance=new self();
		}
		return self::$_instance;
	}
	final public function setServer($server){
		   self::$_instance->server=$server;
		   return self::$_instance;
	}
	final public function delivery($class,$func,$params=[]){
		   $taskid=$this->server->task([$class,$func,$params]);
		   return $taskid;
	}
	final public function dispatch($taskid,$workerid,$data){
		   $ret=null;
		   if(empty($data)){
		   	  echo "任务内容不合法";
		   	  return false;
		   }
		   list($classname,$func,$params)=$data;
		   try {
		   	  $class=(new $classname);
		   	  $class->server=$this->server;
		   	  return $class->$func(...$params);
		   } catch (Exception $e) { 
		   	  return $e->getMessage();
		   }
	}
	final public function finish($taskid,$data){
		   echo "TaskId:{$taskid}\t{$data}",PHP_EOL;
	}
	public function __get($name){
		   return $this->$name;
	}
	
}