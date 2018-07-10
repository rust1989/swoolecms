<?php
namespace Jacky;
class Controller{
	/**
     * @var \Piz\Router
     */
    protected $router;
    /**
     * @var swoole_http_server->request
     */
    protected $request;
    /**
     * @var swoole_http_server->response
     */
    protected $response;
    /**
     * @var swoole_server
     */
    protected $server;
    /**
     * @var swoole_server->task
     */
    protected $task;
    
	final public function json($array=array(),$callback=null){
		  $this->gzip();
		  $this->response->header("Content-Type",'application/json');
		  $json=json_encode($array);
		  $json=is_null($callback)?$json:"{$callback}({$json})";
		  $this->response->end($json);
	}
	final public function display($params=array(),$return=false){
		  if(!is_array($params)){
		  	echo "数据类型必须为数组";
		  }
		  extract($params);
		  $this->gzip();
		  $path=config('app.path').'/tpl/'.$this->router['m'].'/'.$this->router['c'].'/'.$this->router['a'].'.php';
		  echo $path;
		  if(!file_exists($path)){
		  	  $this->response->status(404);
		  	  $this->response->end("棋牌不存在:"+$path);
		  }
		  if(!empty(ob_get_contents()))ob_end_clean();
		  ob_start();
		  include $path;
		  $content=ob_get_contents();
		  ob_end_clean();
		  
		  $this->response->end($content);
	}
	final public function gzip($level=null){
		  if(empty($level)){
		  	  $level=config('app.gzip',0);
		  }
		  $level>0&&$this->response->gzip($level);
	}
    public function __set($name,$object){
    	echo "set:{$name}\r\n";
        $this->$name = $object;
    }

    public function __get($name){
        return $this->$name;
    }
}