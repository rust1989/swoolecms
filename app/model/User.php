<?php
namespace app\model;
use Jacky\Model;
class User extends Model{
	  public $table_name='user';
	  public function getByUsername($username){
	  	   return $this->get_one("`username`='{$username}'");
	  }
}