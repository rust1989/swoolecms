<?php
namespace app\modules\user;
use app\task\Notice;
use Jacky\WsController;
use app\model\User;
class info extends WsController{
	 public function get(){
	 	$content="Uid:{$this->param['uid']};Name:test;say:{$this->param['msg']}";
	 	$user=new User();
	 	$ret=$user->getByUsername('admin');
	 	var_dump($ret);
	 	$this->task->delivery(Notice::class,'ToAll',[$this->fd,$content]);
	 }
}