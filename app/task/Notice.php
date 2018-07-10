<?php
namespace app\task;
class Notice{
	  public function ToAll($fd,$data){
	  	   $fds=[];
	  	   foreach ($this->server->connections as $clientfd){
	  	   	    if($fd!=$clientfd&&$this->server->exist($clientfd)){
	  	   	    	  $this->server->push($clientfd,$data);
	  	   	    	  $fds[]=$clientfd;
	  	   	    }
	  	   }
	  	   return "已向".join(',', $fds)."发送内容:".$data;
	  }
}