<?php
namespace app\hook;
use app\common\RedisTool;
class FD{
	protected static  $_instance='';
	public static function getInstance(){
		if(self::$_instance==''){
			self::$_instance=new self();
		}
		return self::$_instance;
	}
	public function start($server){
		  RedisTool::getInstance()->del("FD");
		  logs("INFO","Hook","重置FD表");
	}
	public function open($server,$fd){
		  RedisTool::getInstance()->sadd("FD",$fd);
		  logs("INFO","Hook","写入redis集合","FD:{$fd}");
	}
	public function close($server,$fd){
		   RedisTool::getInstance()->srem("FD",$fd);
		   logs("INFO","Hook","移除redis集合","FD:{$fd}");
	}
	public function __call($method,$args){
		   $this->$method(...$args);
	}
}