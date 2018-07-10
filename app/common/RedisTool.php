<?php
namespace app\common;
class RedisTool{
	protected static  $_instance='';
	private function __construct(){
		    $host=config('redis.host');
		    $port=config('redis.port');
		    $pwd=config('redis.passwd');
		    $db=config('redis.db');
		    try {
		    	$redis=new \Redis();
		    	$redis->pconnect($host,$port);
		    	if($pwd){
		    		$redis->auth($pwd);
		    	}
		    	$redis->select($db);
		    	logs('INFO',"REDIS","已连接",$host.":".$port);
		    } catch (Exception $e) {
		    	$redis='';
		    	logs('INFO',"REDIS:".$e->getMessage());
		    }
		    self::$_instance=$redis;
	}
	public static function getInstance(){
		if(self::$_instance==''){
			new self();
		}
		return self::$_instance;
	}
	public function __call($method,$args=null){
	       self::$_instance->$method(...$args); 	
	}
}