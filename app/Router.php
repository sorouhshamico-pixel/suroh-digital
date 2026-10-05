<?php
namespace App;
final class Router {
    private array $routes=[];
    public function get(string $path, callable|array $handler):void{$this->add('GET',$path,$handler);} public function post(string $path, callable|array $handler):void{$this->add('POST',$path,$handler);}
    private function add(string $method,string $path,callable|array $handler):void { $names=[]; $regex=preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',function($m)use(&$names){$names[]=$m[1];return '([^/]+)';},$path); $this->routes[$method][]=[$path,'#^'.$regex.'$#',$names,$handler]; }
    public function dispatch(string $method,string $path):void { $method=$method==='HEAD'?'GET':$method; foreach($this->routes[$method]??[] as [$_,$regex,$names,$handler]){ if(!preg_match($regex,$path,$m))continue; array_shift($m); $params=[]; foreach($names as $i=>$name)$params[$name]=urldecode($m[$i]??''); if(is_array($handler)){[$class,$action]=$handler;(new $class())->{$action}(...array_values($params));}else{$handler(...array_values($params));} return;} http_response_code(404); $title='الصفحة غير موجودة'; $description='الصفحة المطلوبة غير متاحة.'; $contentView=view_path('pages/404.php'); require view_path('layouts/app.php'); }
}