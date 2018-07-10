<?php
return [
     'name'=>'server',
     'namespace'=>'app',
     'path'=>realpath(__DIR__.'/../app/'),
     'gzip'=>0,
     //server
     'ip'=>'127.0.0.1',
     'port'=>'9501',
     'server'=>'websocket',
     'set'=>[
        'worker_num' => 4,
        'task_worker_num' =>4,
        'daemonize'=>0,
        'enable_static_handler' => TRUE,
        'document_root' => realpath (__DIR__.'/../static/')
     ],
     'log'=>[
          'echo'=>0,
          'path'=>LOG_PATH,
          'level'=>['EMERGENCY','ALERT','CRITICAL','ERROR','WARNING','NOTICE','INFO','DEBUG','SQL'], 
     ]     
];