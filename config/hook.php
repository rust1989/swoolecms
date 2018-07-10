<?php
return [
      'start'=>[
              [app\hook\FD::class,'start']
      		],
      'open'=>[
             [app\hook\FD::class,'open']
      		],
      'close'=>[
             [app\hook\FD::class,'close']
      		]
];