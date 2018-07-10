<?php
use Jacky\Config;
use Jacky\Log;
function config($name,$default=null){
	   return Config::getInstance()->get($name,$default);
}
function logs($type,...$logs){
	  Log::getInstance()->write($type,...$logs);
}