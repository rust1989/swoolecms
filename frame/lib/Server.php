<?php
namespace Jacky;
use Jacky\Task;
class Server{
	private $serv;
	private static $instance;
	public $name;
	private $config=[];
	private $workerId;
	public static  function getInstance(){
		if(is_null(self::$instance)){
			self::$instance=new self();
		}
		return self::$instance;
	}
	public function setConfig($config){
		   $this->config=$config;
		   $this->name=$config['name'];
	}
	public function run(){
		   $swooleServer=isset($this->config['server'])&&$this->config['server']=='websocket'?'swoole_websocket_server':'swoole_http_server';
		   
		   $this->serv=new $swooleServer($this->config['ip'],$this->config['port']);
		   $this->serv->set($this->config['set']);
		   
		   $this->serv->on('Start',array($this,'onStart'));
		   $this->serv->on('WorkerStart',array($this,'onWorkerStart'));
		   $this->serv->on('WorkerStop',array($this,'onWorkerStop'));
		   $this->serv->on('WorkerError',array($this,'onWorkerError'));
		   
		   $this->serv->on('ManagerStart',array($this,'onManagerStart'));
		   $this->serv->on('ManagerStop',array($this,'onManagerStop'));
		   echo $swooleServer;
		   if($this->config['server']=='websocket'){
			   	$this->serv->on('Open',array($this,'onOpen'));
			   	$this->serv->on('Message',array($this,'onMessage'));
			   	$this->serv->on('Close',array($this,'onClose'));
		   }
		   if(isset($this->config['set']['task_worker_num'])&&$this->config['set']['task_worker_num']){
			   	$this->serv->on('Task',array($this,'onTask'));
			   	$this->serv->on('Finish',array($this,'onFinish'));
		   }
		   
		   $this->serv->on('Request',array($this,'onRequest'));
		   $this->serv->start();
	}
	public function onManagerStart($server){
		   $this->set_process_title($this->name.'-manager');
		   logs("INFO",$this->name,'管理进程开始');
	}
	public function onManagerStop($server){
		   logs("INFO",$this->name,'管理进程结束');
	}
	public function onRequest($request,$response){
		  if($this->config['set']['enable_static_handler']&&$request->server['request_uri']=='/favicon.ico'){
		  	return;
		  }
		  App::getInstance()->http($this->serv,$request,$response);
	}
	public function onStart($serv){
		  date_default_timezone_set("Asia/Shanghai");
		  logs('INFO','启动成功',"{$this->config['ip']}:{$this->config['port']}");
		  $this->set_process_title($this->name."-master");
		  
		  Hook::getInstance()->listen('start',$serv);
	}
	public function onWorkerStart($serv,$worker_id){
		date_default_timezone_set("Asia/Shanghai");
		$this->workerId=$worker_id;
		if(function_exists('apc_clear_cache')){
			apc_clear_cache();
		}
		if(function_exists('opcache_reset')){
			opcache_reset();
		}
		$this->reload_config();
		if($worker_id>$this->serv->setting['worker_num']){
			\swoole_set_process_name("{$this->name}-worker");
		}else{
			\swoole_set_process_name("{$this->name}-tasker");
		}
		\swoole_timer_tick(3000,function($timer){
			 Log::getInstance()->save();
		});
	}
    public function onWorderError($server,$worker_id,$worker_pid,$exit_code){
    	   logs("ERROR",$this->name,"进程异常","WorkerID".$worker_id,"WorkerPID".$worker_pid,"ExitCode".$exit_code);
    }
	public function onWorkerStop($serv,$worker_id){
		  logs("ERROR",$this->name,"进程结束","WorkerID".$worker_id);;
	}
	public function onOpen($serv,$request){
	      logs('DEBUG',"FD:".$request->fd,"握手成功");
	      Hook::getInstance()->listen('open',$serv,$request->fd);
	}
	public function onMessage($server,$frame){
		echo "FD:{$frame->fd}","Opcode:{$frame->opcode}","Finish:{$frame->finish}","Data:{$frame->data}\r\n";
		App::getInstance()->websocket($server,$frame);
	}
	public function onClose($serv,$fd,$from_id){
		echo "Close-Client:".$fd.";FromID:".$from_id;
		logs('DEBUG',"Close-Client:".$fd.";FromID:".$from_id);
		Hook::getInstance()->listen('close',$serv,$fd);
	}
	public function onTask($serv, $task_id, $from_id, $data){
		echo "Task-Client:".$task_id.";FromID:".$from_id;
	    return Task::getInstance()->setServer($serv)->dispatch($task_id,$from_id,$data);
	}
	public function onFinish($serv,$task_id,$data){
		echo "AsyncTask Finish:Connect.PID=" . posix_getpid() . PHP_EOL;
		 Task::getInstance()->setServer($serv)->finish($task_id,$data);
	}
	public function set_process_title($title){
		   if(PHP_OS=='Darwin')return ;
		   if(function_exists('cli_set_process_title')){
		   	  @cli_set_process_title($title);
		   }else{
		   	  @swoole_set_process_name($title);
		   }
		   
	}
	public function get_server_port($fd){
		   return $this->serv->connnection_info($fd)['server_port'];
	}
	public function reload_config(){
		$this->config=config('app');
		$this->name=$this->config['name'];
	}
}