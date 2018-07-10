<?php
namespace Jacky;
class Request{
	protected static  $_instance='';
	public function __construct(){
	
	}
	public static function getInstance(){
		if(self::$_instance==''){
			self::$_instance=new self();
		}
		return self::$_instance;
	}
	public function set($request){
		  $this->server=$request->server;
		  $this->header=$request->header;
		  $this->tmpfiles=$request->tmpfiles;
		  $this->request=$request->request;
		  $this->cookie=$request->cookie;
		  $this->get=$request->get;
		  $this->files=$request->files;
		  $this->post=$request->post;
		  $this->rawContent=$request->rawContent();
		  $this->getData=$request->getData();
	}
	public function __get($name){
		   return $this->$name;
	}
}