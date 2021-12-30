<?php

route::get('/',['Main','home']);

route::match(['post','get'],'/users', ['Main','users']);

route::static('/assets/[*]', 'assets');

route::get('/token',function(){
  var_dumps(
    $_SERVER['REQUEST_METHOD']
  );
});

