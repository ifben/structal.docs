<?php

require 'lib/Klein.php';

respond( 'GET',      '/changes',                 'changes' );
respond( 'GET',      '/[:resource]',             'constructor' );
respond( 'POST',     '/[:resource]',             'constructor' );
respond( 'PUT',      '/[:resource]/[:id]',       'constructor' );
respond( 'DELETE',   '/[:resource]/[:id]',       'constructor' );
respond( 'GET',      '/[:resource]/[:action]',   'constructor' );
respond( 'POST',     '/[:resource]/[:action]',   'constructor' );
respond( 'GET',      '/',                        'index' );

function constructor($request,$response) {
  require 'lib/Mullet.php';
  $model = 'mdl/'.$request->resource.".php";
  if (file_exists($model)) include $model;
  $action = strtolower($request->method());
  if (isset($request->action))
    $action = $request->action;
  $mapper = ucwords($request->resource);
  if (class_exists($mapper))
    $obj = new $mapper;
  header('HTTP/1.1 200 OK');
  header('Content-Type: application/json');
  if (isset($obj) && method_exists($obj,$action))
    echo json_encode($obj->$action($request,$response))."\n";
  else
    echo json_encode(array(
      'error'=>'internal error',
      'code'=>500
    ));
}

function index($request,$response) {
  require 'lib/Mustache.php';
  $m = new Mustache;
  session_start();
  $params = array();
  if (isset($_SESSION['current_user']))
    $params['username'] = $_SESSION['current_user'];
  echo $m->render(file_get_contents('tpl/index.html'),$params);
}

dispatch();