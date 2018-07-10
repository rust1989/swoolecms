<?php
namespace Jacky;
class App{
	protected static  $_instance='';
	//映射表 
	private static $map = [];
	public function __construct(){
	
	}
	public static function getInstance(){
		if(self::$_instance==''){
			self::$_instance=new self();
		}
		return self::$_instance;
	}
	public function websocket($server,$frame){
		$router=Router::getInstance()->websocket($frame->data);
		$namespace=config('app.namespace');
		$module=$router['m'];
		$controller=$router['c'];
		$action=$router['a'];
		$param=$router['p'];
		$classname="\\{$namespace}\\modules\\{$module}\\{$controller}";
		
		if(!isset(self::$map[$classname])){
			try {
				$class=new $classname;
				self::$map[$classname]=$class;
			} catch (\Exception $e) {
				echo $e->getMessage(),PHP_EOL;
				return;
			}
		}
		if(get_parent_class($classname)!="Jacky\WsController"){
			echo "[{$classname}]必须继承Jacky\WsController".PHP_EOL;
			return ;
		}
		try {
			self::$map[$classname]->server=$server;
			self::$map[$classname]->fd=$frame->fd;
			self::$map[$classname]->param=$param;
			self::$map[$classname]->task=Task::getInstance()->setServer($server);
			self::$map[$classname]->$action();
		} catch (\Exception $e) {
			 echo $e->getMessage(),PHP_EOL;
			 return;
		}
	}
	public function http($server,$request,$response){
		if($request->server['request_uri']=='/favicon.ico')return ;
		$req=Request::getInstance();
		$req->set($request);
		
		$router=Router::getInstance()->http($req->server['request_uri']);
		
		$namespace=config('app.namespace');
		$module=$router['m'];
		$controller=$router['c'];
		$action=$router['a'];
		$param=$router['p'];
		$classname="\\{$namespace}\\modules\\{$module}\\{$controller}";
		
		if(!isset(self::$map[$classname])){
			 $class=new $classname;
			 self::$map[$classname]=$class;
		}
		if(get_parent_class($classname)!="Jacky\Controller"){
			   $response->header("Content-Type","text/html;charset=utf8");
			   $response->status(503);
			   $response->end("503 Service Unavaliable");
			   echo "[{$classname}]必须继承Jacky\Controller".PHP_EOL;
			   return ;
		}
		try {
		    if(!empty(ob_get_contents()))ob_get_clean();
		    ob_start();
		    self::$map[$classname]->server=$server;
		    self::$map[$classname]->response=$response;
		    self::$map[$classname]->request=$request;
		    self::$map[$classname]->router=$router;
		    self::$map[$classname]->task=Task::getInstance()->setServer($server);
		    self::$map[$classname]->$action($param);
		    //$content=ob_get_contents();
		   //ob_end_clean();
		   // $response->end($content);
		} catch (Exception $e) {
			$response->header('Content-type',"text/html;charset=utf-8;"); 
			$response->status(404);
			$response->end('404 NOT FOUND'); 
			return ;
		}
	}
}