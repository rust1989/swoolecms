<?php
namespace Jacky;
class Log{
	protected static  $_instance='';
	private static $config=[];
	private static $logs=[];
	public function __construct(){
	
	}
	public static function getInstance(){
		if(self::$_instance==''){
			self::$_instance=new self();
			self::$config=config('app.log');
		}
		return self::$_instance;
	}
	public  function write($type,...$logs){
		    $type=strtoupper($type);
		    $msgs="{$type} \t ".date("Y-m-d H:i:s")." \t ".join("\t",$logs);
		
		    if(!in_array($type,self::$config['level']))return false;
		    echo $msgs.$type;
		    if(self::$config['echo']){
		    	echo $msg,PHP_EOL;
		    }
		    self::$logs[$type][]=$msgs;
	}
	public function save(){
		   if(empty(self::$logs))return false;
		   var_dump(self::$logs);
		   foreach (self::$logs as $type=>$logs){
			   $dir=LOG_PATH.date("Ymd").DIRECTORY_SEPARATOR;
			   !is_dir($dir)&&mkdir($dir,0777);
			   $filename=$dir.date("h").'.'.$type.'.log';
			   $content='';
			   foreach ($logs as $log){
			   	  $content.=$log.PHP_EOL;
			   }
			   \swoole_async_writefile($filename,$content,null,FILE_APPEND);
		   }
		   self::$logs=[];
		   return true;
	}
}