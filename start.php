<?php
use Jacky\Server;
require './frame/base.php';
Server::getInstance()->setConfig(config('app'));
Server::getInstance()->run();